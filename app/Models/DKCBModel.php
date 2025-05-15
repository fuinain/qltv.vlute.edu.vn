<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DKCBModel extends Model
{
    protected $table = 'dkcb';
    protected $primaryKey = 'id_dkcb';
    public $timestamps = false;

    protected $fillable = [
        'ma_dkcb',
        'id_kho_an_pham',
        'so_thu_tu',
        'id_sach',
        'trang_thai',
        'bar_code',
    ];

    public static function soNhanTheoKho()
    {
        return self::selectRaw('id_kho_an_pham, COUNT(*) as so_nhan')
            ->where('trang_thai', 1)
            ->groupBy('id_kho_an_pham')
            ->pluck('so_nhan', 'id_kho_an_pham')
            ->toArray();
    }
    
    public static function taoNhanDKCB($id_kho_an_pham, $so_bat_dau, $so_luong)
    {
        // Lấy thông tin kho
        $kho = KhoAnPhamModel::find($id_kho_an_pham);
        
        if (!$kho) {
            return [
                'status' => 404,
                'message' => 'Không tìm thấy thông tin kho'
            ];
        }
        
        // Mảng chứa mã DKCB đã tạo
        $dkcbArr = [];
        
        // Tạo các nhãn DKCB
        for ($i = 0; $i < $so_luong; $i++) {
            $so_thu_tu = $so_bat_dau + $i;
            $ma_dkcb = $kho->ma_kho . "." . str_pad($so_thu_tu, 6, '0', STR_PAD_LEFT);
            
            // Tạo mã DKCB mới
            $dkcb = self::create([
                'ma_dkcb' => $ma_dkcb,
                'id_kho_an_pham' => $id_kho_an_pham,
                'so_thu_tu' => $so_thu_tu,
                'trang_thai' => 1,
                'bar_code' => $ma_dkcb
            ]);
            
            $dkcbArr[] = [
                'id' => $dkcb->id_dkcb,
                'ma_dkcb' => $ma_dkcb,
                'so_thu_tu' => $so_thu_tu
            ];
        }
        
        return [
            'status' => 200,
            'message' => 'Tạo ' . $so_luong . ' nhãn DKCB thành công',
            'data' => $dkcbArr
        ];
    }
    
    public static function layNhanTheoBatDau($id_kho_an_pham, $so_bat_dau, $so_luong)
    {
        $nhan = self::where('id_kho_an_pham', $id_kho_an_pham)
            ->where('so_thu_tu', '>=', $so_bat_dau)
            ->orderBy('so_thu_tu', 'asc')
            ->limit($so_luong)
            ->get();
            
        return $nhan;
    }

    public static function timDKCBKhaDung($id_kho_an_pham, $so_bat_dau, $so_luong)
    {
        return self::where('id_kho_an_pham', $id_kho_an_pham)
            ->where('so_thu_tu', '>=', $so_bat_dau)
            ->whereNull('id_sach')
            ->where('trang_thai', 1)
            ->orderBy('so_thu_tu', 'asc')
            ->limit($so_luong)
            ->get();
    }

   
    public static function ganDKCBChoSach($id_sach, $id_kho_an_pham, $so_bat_dau, $so_luong)
    {
        // Lấy danh sách DKCB khả dụng
        $danhSachDKCB = self::timDKCBKhaDung($id_kho_an_pham, $so_bat_dau, $so_luong);
        
        if ($danhSachDKCB->count() < $so_luong) {
            return [
                'status' => 400,
                'message' => 'Không đủ số DKCB để gán. Vui lòng tạo thêm số DKCB.'
            ];
        }
        
        // Gán DKCB cho sách
        $danhSachDaGan = [];
        foreach ($danhSachDKCB as $dkcb) {
            $dkcb->id_sach = $id_sach;
            $dkcb->save();
            
            $danhSachDaGan[] = [
                'id' => $dkcb->id_dkcb,
                'ma_dkcb' => $dkcb->ma_dkcb,
                'so_thu_tu' => $dkcb->so_thu_tu
            ];
            
            if (count($danhSachDaGan) >= $so_luong) {
                break;
            }
        }
        
        return [
            'status' => 200,
            'message' => 'Đã gán ' . count($danhSachDaGan) . ' số DKCB cho sách',
            'data' => $danhSachDaGan
        ];
    }

    /**
     * Gán số DKCB cho sách dựa trên mã DKCB
     * 
     * @param  int  $id_sach
     * @param  string  $ma_dkcb
     * @param  int  $so_luong
     * @return array
     */
    public static function ganDKCBChoSachTheoMa($id_sach, $ma_dkcb, $so_luong)
    {
        // Chuẩn hóa mã DKCB đầu vào
        $ma_dkcb = trim($ma_dkcb);
        
        // Phân tích mã DKCB để lấy mã kho và số thứ tự
        if (preg_match('/^([A-Z]+)\.0*(\d+)$/', $ma_dkcb, $matches) || 
            preg_match('/^([A-Z]+)\.(\d+)$/', $ma_dkcb, $matches)) {
            $ma_kho = $matches[1];
            $so_thu_tu = (int)$matches[2];
        } else {
            return [
                'status' => 400,
                'message' => 'Mã DKCB không đúng định dạng. Vui lòng nhập theo định dạng MaKho.SoThuTu (ví dụ: KM.000001 hoặc KM.1)'
            ];
        }
        
        // Tìm kho ấn phẩm theo mã kho
        $kho = KhoAnPhamModel::where('ma_kho', $ma_kho)->first();
        if (!$kho) {
            return [
                'status' => 404,
                'message' => 'Không tìm thấy kho ấn phẩm với mã "' . $ma_kho . '"'
            ];
        }
        
        // Tìm danh sách DKCB liên tiếp
        $danhSachDKCB = self::where('id_kho_an_pham', $kho->id_kho_an_pham)
            ->whereBetween('so_thu_tu', [$so_thu_tu, $so_thu_tu + $so_luong - 1])
            ->whereNull('id_sach')
            ->where('trang_thai', 1)
            ->orderBy('so_thu_tu', 'asc')
            ->get();
        
        if ($danhSachDKCB->count() < $so_luong) {
            return [
                'status' => 400,
                'message' => 'Không đủ số DKCB liên tiếp để gán. Cần ' . $so_luong . ' số nhưng chỉ tìm thấy ' . $danhSachDKCB->count() . ' số. Vui lòng kiểm tra lại mã DKCB bắt đầu hoặc tạo thêm số DKCB.'
            ];
        }
        
        // Gán DKCB cho sách
        $danhSachDaGan = [];
        foreach ($danhSachDKCB as $dkcb) {
            $dkcb->id_sach = $id_sach;
            $dkcb->save();
            
            $danhSachDaGan[] = [
                'id' => $dkcb->id_dkcb,
                'ma_dkcb' => $dkcb->ma_dkcb,
                'so_thu_tu' => $dkcb->so_thu_tu
            ];
            
            if (count($danhSachDaGan) >= $so_luong) {
                break;
            }
        }
        
        return [
            'status' => 200,
            'message' => 'Đã gán ' . count($danhSachDaGan) . ' số DKCB cho sách',
            'data' => $danhSachDaGan
        ];
    }

    /**
     * Gán một mã DKCB duy nhất cho sách (không liên tiếp)
     * 
     * @param  int  $id_sach
     * @param  string  $ma_dkcb
     * @return array
     */
    public static function ganMotDKCBChoSach($id_sach, $ma_dkcb)
    {
        // Chuẩn hóa mã DKCB đầu vào
        $ma_dkcb = trim($ma_dkcb);
        
        // Kiểm tra mã DKCB đã tồn tại chưa
        $dkcb = self::where('ma_dkcb', $ma_dkcb)->first();
        
        if (!$dkcb) {
            return [
                'status' => 404,
                'message' => 'Không tìm thấy mã DKCB: ' . $ma_dkcb
            ];
        }
        
        // Kiểm tra mã DKCB đã được gán cho sách nào chưa
        if ($dkcb->id_sach !== null) {
            return [
                'status' => 400,
                'message' => 'Mã DKCB ' . $ma_dkcb . ' đã được gán cho sách khác'
            ];
        }
        
        // Gán DKCB cho sách
        $dkcb->id_sach = $id_sach;
        $dkcb->save();
        
        return [
            'status' => 200,
            'message' => 'Đã gán mã DKCB: ' . $ma_dkcb . ' cho sách',
            'data' => [
                [
                    'id' => $dkcb->id_dkcb,
                    'ma_dkcb' => $dkcb->ma_dkcb,
                    'so_thu_tu' => $dkcb->so_thu_tu
                ]
            ]
        ];
    }
}
