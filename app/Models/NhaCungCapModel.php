<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class NhaCungCapModel extends Model
{
    protected $table = 'nha_cung_cap';
    protected $primaryKey = 'id_nha_cung_cap';
    public $timestamps = false;

    protected $fillable = [
        'ma_nha_cung_cap',
        'ten_nha_cung_cap',
        'dia_chi',
        'dien_thoai',
        'email',
        'lien_he',
        'stk',
        'ngan_hang',
        'ngay_cap_nhat',
        'ngay_tao',
    ];

    protected static function listNCC(){
        return DB::table('nha_cung_cap')
            ->select('id_nha_cung_cap', 'ten_nha_cung_cap')
            ->get();
    }
}
