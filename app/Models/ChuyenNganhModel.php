<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ChuyenNganhModel extends Model
{
    protected $table = 'chuyen_nganh';
    protected $primaryKey = 'id_chuyen_nganh';
    public $timestamps = false;

    protected $fillable = [
        'ma_chuyen_nganh',
        'ten_chuyen_nganh',
        'id_don_vi',
    ];

    public static function getDanhSachChuyenNganhWithTenDonVi()
    {
        return DB::table('chuyen_nganh as cn')
            ->join('don_vi as dv', 'cn.id_don_vi', '=', 'dv.id_don_vi')
            ->select('cn.id_chuyen_nganh', 'cn.ma_chuyen_nganh', 'cn.ten_chuyen_nganh', 'dv.id_don_vi', 'dv.ten_don_vi', 'cn.ngay_cap_nhat', 'cn.ngay_tao');
    }

    public static function getListByDonVi($id_don_vi)
    {
        return self::where('id_don_vi', $id_don_vi)
            ->select('id_chuyen_nganh', 'ten_chuyen_nganh')
            ->get();
    }

}
