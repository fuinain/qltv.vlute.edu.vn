<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class LoaiNhapModel extends Model
{
    protected $table = 'loai_nhap';
    protected $primaryKey = 'id_loai_nhap';
    public $timestamps = false;

    protected $fillable = [
        'ten_loai_nhap',
    ];

    protected static function listLoaiNhap(){
        return DB::table('loai_nhap')
            ->select('id_loai_nhap', 'ten_loai_nhap')
            ->get();
    }
}
