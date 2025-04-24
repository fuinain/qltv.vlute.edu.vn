<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class TrangThaiDonModel extends Model
{
    protected $table = 'trang_thai_don';
    protected $primaryKey = 'id_trang_thai_don';
    public $timestamps = false;

    protected $fillable = [
        'trang_thai',
    ];
    protected static function listTTDon(){
        return DB::table('trang_thai_don')
            ->select('id_trang_thai_don', 'trang_thai')
            ->get();
    }
}
