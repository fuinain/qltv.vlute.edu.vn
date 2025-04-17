<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ThamSoLuuThongModel extends Model
{
    protected $table = 'tham_so_luu_thong';
    protected $primaryKey = 'id_tham_so_luu_thong';
    public $timestamps = false;

    protected $fillable = [
        'id_chi_tiet_diem_luu_thong',
        'id_doi_tuong_ban_doc',
        'id_diem_luu_thong',
    ];

    public static function getChiTietTheoDoiTuongBanDoc($id_doi_tuong_ban_doc, $perPage = 10)
    {
        return DB::table('tham_so_luu_thong as tslt')
            ->join('diem_luu_thong as dlt', 'tslt.id_diem_luu_thong', '=', 'dlt.id_diem_luu_thong')
            ->select(
                'dlt.id_diem_luu_thong',
                'dlt.ma_loai',
                'dlt.ten_diem',
            )
            ->orderBy('tslt.id_diem_luu_thong')
            ->where('tslt.id_doi_tuong_ban_doc', $id_doi_tuong_ban_doc)
            ->paginate($perPage);
    }
}
