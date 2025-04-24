<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BienMucBieuGhiModel extends Model
{
    protected $table = 'bien_muc_bieu_ghi';
    protected $primaryKey = 'id_bien_muc_bieu_ghi';
    public $timestamps = false;
    protected $fillable = [
        'id_sach',
        'id_tai_lieu',
        'trang_thai_bieu_ghi',
        'id_chuyen_nganh',
        'id_don_vi',
    ];
}
