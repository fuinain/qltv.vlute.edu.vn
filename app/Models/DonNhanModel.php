<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
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

                DB::raw("CONCAT_WS(CHAR(10), loai_nhap.ten_loai_nhap, nguon_nhan.ten_nguon) AS nguon_nhan_hien_thi"),
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
}
