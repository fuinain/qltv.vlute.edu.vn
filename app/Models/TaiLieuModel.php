<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class TaiLieuModel extends Model
{
    protected $table = 'tai_lieu';
    protected $primaryKey = 'id_tai_lieu';
    public $timestamps = false;

    protected $fillable = [
        'ma_tai_lieu',
        'ten_tai_lieu',
    ];

    protected static function listTaiLieu(){
        return DB::table('tai_lieu')
            ->select('id_tai_lieu', 'ten_tai_lieu')
            ->get();
    }
}
