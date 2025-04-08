<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class DonViModel extends Model
{
    protected $table = 'don_vi';
    protected $primaryKey = 'id_don_vi';
    public $timestamps = false;

    protected $fillable = [
        'ma_don_vi',
        'ten_don_vi',
    ];

    public static function getListDonViSelectOption()
    {
        return DB::table('don_vi')
            ->get();
    }
}
