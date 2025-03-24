<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaiKhoanModel extends Model
{
    use HasFactory;

    protected $table = 'tai_khoan';
    protected $primaryKey = 'id_tai_khoan';
    public $timestamps = false;

    protected $fillable = [
        'ten_dang_nhap',
        'ten_nguoi_dung',
        'quyen',
        'mat_khau',
    ];
}