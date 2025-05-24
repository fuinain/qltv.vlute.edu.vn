<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BaiVietModel extends Model
{
    protected $table = 'bai_viet';
    protected $primaryKey = 'id_bai_viet';
    public $timestamps = false;
    protected $fillable = [
        'ten_bai_viet',
        'tom_tat',
        'ngay_cap_nhat',
        'ngay_tao',
        'id_chu_de_bai_viet',
        'noi_dung',
        'noi_dung_navbar',
    ];
}




