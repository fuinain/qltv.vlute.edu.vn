<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class KhoAnPhamModel extends Model
{
    protected $table = 'kho_an_pham';
    protected $primaryKey = 'id_kho_an_pham';
    public $timestamps = false;

    protected $fillable = [
        'ma_kho',
        'ten_kho',
    ];

    protected static function listDanhMucKho(){
        return DB::table('kho_an_pham')
            ->select('id_kho_an_pham', 'ten_kho')
            ->get();
    }

    public function dkcb()
    {
        return $this->hasMany(DKCBModel::class, 'id_kho_an_pham', 'id_kho_an_pham');
    }
}
