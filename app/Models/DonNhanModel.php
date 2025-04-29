<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class DonNhanModel extends Model
{
    protected $table = 'don_nhan';
    protected $primaryKey = 'id_don_nhan';
    public $timestamps = false;

    protected $fillable = [
        'nguoi_tao',
        'ten_don_nhan',
        'id_nguon_nhan',
        'id_loai_nhap',
        'id_trang_thai_don',
        'ngay_nhan',
        'id_nha_cung_cap',
        'so_chung_tu',
        'ghi_chu',
    ];

    public static function getListDonNhan(int $perPage = 10)
    {
        return self::query()
            ->leftJoin('nguon_nhan', 'nguon_nhan.id_nguon_nhan', '=', 'don_nhan.id_nguon_nhan')
            ->leftJoin('loai_nhap', 'loai_nhap.id_loai_nhap', '=', 'don_nhan.id_loai_nhap')
            ->leftJoin('nha_cung_cap', 'nha_cung_cap.id_nha_cung_cap', '=', 'don_nhan.id_nha_cung_cap')
            ->leftJoin('trang_thai_don', 'trang_thai_don.id_trang_thai_don', '=', 'don_nhan.id_trang_thai_don')
            ->leftJoin('sach', 'sach.id_don_nhan', '=', 'don_nhan.id_don_nhan')
            ->select([
                'don_nhan.id_don_nhan',
                'don_nhan.ten_don_nhan',
                'don_nhan.ngay_nhan',
                'don_nhan.ngay_tao',
                'don_nhan.ngay_cap_nhat',
                'don_nhan.so_chung_tu',
                'don_nhan.ghi_chu',
                'don_nhan.id_nguon_nhan',
                'don_nhan.id_loai_nhap',
                'don_nhan.id_trang_thai_don',
                'don_nhan.id_nha_cung_cap',
                'nha_cung_cap.ten_nha_cung_cap',

                DB::raw("CONCAT_WS(' - ', loai_nhap.ten_loai_nhap, nguon_nhan.ten_nguon) AS nguon_nhan_hien_thi"),
                DB::raw('COUNT(sach.id_sach)                            AS so_ten'),
                DB::raw('COALESCE(SUM(sach.so_luong)           ,0)      AS so_ban'),
                DB::raw('COALESCE(SUM(sach.so_luong * sach.gia),0)      AS tri_gia'),
            ])
            ->groupBy('don_nhan.id_don_nhan')
            ->orderByDesc('don_nhan.id_don_nhan')
            ->paginate($perPage);
    }

    protected $appends = ['so_luong_tong_hop'];

    public function getSoLuongTongHopAttribute(): string
    {
        return "{$this->so_ten} / {$this->so_ban} / " . number_format($this->tri_gia, 0, ',', '.');
    }

    public function nhaCungCap(): BelongsTo
    {
        return $this->belongsTo(NhaCungCapModel::class, 'id_nha_cung_cap', 'id_nha_cung_cap');
    }

    public function trangThaiDon(): BelongsTo
    {
        return $this->belongsTo(TrangThaiDonModel::class, 'id_trang_thai_don', 'id_trang_thai_don');
    }

    public function nguonNhan(): BelongsTo
    {
        return $this->belongsTo(NguonNhanModel::class, 'id_nguon_nhan', 'id_nguon_nhan');
    }

    // Quan hệ một-nhiều với Sách
    public function sach(): HasMany
    {
        return $this->hasMany(SachModel::class, 'id_don_nhan', 'id_don_nhan');
    }

    public static function getDataDonNhanForExport(int $id_don_nhan): self
    {
        // Phiên bản dùng Eloquent with()
        return self::with([
            'nhaCungCap:id_nha_cung_cap,ten_nha_cung_cap',
            'trangThaiDon:id_trang_thai_don,trang_thai',
            'nguonNhan:id_nguon_nhan,ten_nguon',
            'sach' => function ($query) {
                $query->orderBy('ngay_tao');
            },
            'sach.bienMucBieuGhi' => function ($query) {
                $query->select('id_bien_muc_bieu_ghi', 'id_sach', 'id_tai_lieu');
            },
            'sach.bienMucBieuGhi.taiLieu:id_tai_lieu,ten_tai_lieu',
            'sach.bienMucBieuGhi.truongCha' => function ($query) {
                $query->where('ma_truong', '082')
                ->where('ct1', '1')->where('ct2', '4')
                ->select('id_bien_muc_truong_cha', 'id_bien_muc_bieu_ghi', 'ma_truong', 'ct1', 'ct2');
            },
            'sach.bienMucBieuGhi.truongCha.children' => function ($query) {
                $query->whereIn('ma_truong_con', ['a', 'b'])
                ->select('id_bien_muc_truong_con', 'id_bien_muc_truong_cha', 'ma_truong_con', 'noi_dung');
            }
        ])
            ->select([
                'id_don_nhan',
                'ten_don_nhan',
                'ngay_nhan',
                'id_nha_cung_cap',
                'id_trang_thai_don',
                'id_nguon_nhan',
                'ghi_chu',
                'nguoi_tao',
            ])
            ->findOrFail($id_don_nhan);
    }

    public static function getDataThongKeTaiLieuForExport(int $id_don_nhan): ?object
    {
        // 1. Lấy thông tin đơn nhận và các sách liên quan cùng biên mục, tài liệu
        $donNhan = self::with([
            'nhaCungCap:id_nha_cung_cap,ten_nha_cung_cap',
            'trangThaiDon:id_trang_thai_don,trang_thai',
            'nguonNhan:id_nguon_nhan,ten_nguon',
            'sach' => function ($query) {
                // Chỉ lấy các cột cần thiết cho tính toán và liên kết
                $query->select('id_sach', 'id_don_nhan', 'gia', 'so_luong', 'thanh_tien');
            },
            'sach.bienMucBieuGhi' => function ($query) {
                // Lấy id_tai_lieu để nhóm và liên kết
                $query->select('id_bien_muc_bieu_ghi', 'id_sach', 'id_tai_lieu');
            },
            'sach.bienMucBieuGhi.taiLieu:id_tai_lieu,ten_tai_lieu' // Lấy tên tài liệu để hiển thị
        ])
            ->select([ // Chỉ lấy các cột cần thiết của đơn nhận
                'id_don_nhan',
                'ten_don_nhan',
                'ngay_nhan',
                'id_nha_cung_cap',
                'id_trang_thai_don',
                'id_nguon_nhan',
                'ghi_chu',
                'nguoi_tao',
            ])
            ->find($id_don_nhan); // Dùng find thay vì findOrFail để có thể trả về null

        if (!$donNhan) {
            return null; // Trả về null nếu không tìm thấy đơn nhận
        }

        $sachList = $donNhan->sach; // Lấy danh sách sách từ đơn nhận đã load

        // 2. Nhóm sách theo id_tai_lieu và tính toán tổng hợp
        $thongKeData = [];
        $tongSoTenChung = 0;
        $tongSoBanChung = 0;
        $tongTriGiaChung = 0;

        // Nhóm sách theo Tên Tài Liệu để hiển thị
        $groupedByTaiLieu = $sachList->groupBy(function ($sach) {
            // Trả về tên tài liệu, hoặc 'Chưa phân loại' nếu không có
            return optional(optional($sach->bienMucBieuGhi)->taiLieu)->ten_tai_lieu ?? 'Chưa phân loại';
        });

        foreach ($groupedByTaiLieu as $tenTaiLieu => $sachsInGroup) {
            // Đảm bảo tính toán trên collection con
            $groupCollection = collect($sachsInGroup);
            $soTen = $groupCollection->count(); // Số lượng đầu sách (mỗi record SachModel là 1 tên)
            $soBan = $groupCollection->sum('so_luong');
            $triGia = $groupCollection->sum('thanh_tien'); // Giả sử thanh_tien đã được tính đúng

            $thongKeData[] = [
                'ten_tai_lieu' => $tenTaiLieu,
                'so_ten' => $soTen,
                'so_ban' => $soBan,
                'tri_gia' => $triGia,
            ];

            // Cộng dồn vào tổng chung
            $tongSoTenChung += $soTen;
            $tongSoBanChung += $soBan;
            $tongTriGiaChung += $triGia;
        }

        // 3. Trả về dữ liệu dưới dạng một object
        return (object) [
            'donNhan' => $donNhan,
            'thongKeData' => $thongKeData,
            'tongSoTenChung' => $tongSoTenChung,
            'tongSoBanChung' => $tongSoBanChung,
            'tongTriGiaChung' => $tongTriGiaChung,
        ];
    }
}
