<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocGiaModel extends Model
{
    
    protected $table = 'doc_gia';
    protected $primaryKey = 'id_doc_gia';
    public $timestamps = false;

    protected $fillable = [
        'ho_ten',
        'mssv',
        'ma_lop',
        'ten_lop',
        'so_the',
        'ngay_sinh',
        'ngay_cap_the',
        'han_the',
        'lan_cap_the',
        'ho_khau',
        'ghi_chu',
        'rut_han',
        'nien_khoa',
        'ma_so_quy_uoc',
        'id_chuyen_nganh',
        'email',
    ];
}
