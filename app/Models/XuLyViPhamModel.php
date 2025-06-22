<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class XuLyViPhamModel extends Model
{
    protected $table = 'xu_ly_vi_pham';
    protected $primaryKey = 'id_xu_ly_vi_pham';
    public $timestamps = false;

    protected $fillable = [
        'id_ban_doc',
        'id_phat_ban_doc',
        'ngay_phat',
        'so_tien',
        'hinh_thuc_phat',
        'ngay_het_han_phat',
        'lan_phat',
        'ghi_chu',
        'id_dkcb'
    ];
}





