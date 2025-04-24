<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class NguonNhanModel extends Model
{
    protected $table = 'nguon_nhan';
    protected $primaryKey = 'id_nguon_nhan';
    public $timestamps = false;

    protected $fillable = [
        'ma_nguon_nhan',
        'ten_nguon',
        'kinh_phi',
    ];

    protected static function listNguonNhan(){
        return DB::table('nguon_nhan')
            ->select('id_nguon_nhan', 'ten_nguon')
            ->get();
    }
}
