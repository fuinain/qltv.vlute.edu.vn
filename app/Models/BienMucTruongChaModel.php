<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BienMucTruongChaModel extends Model
{
    protected $table = 'bien_muc_truong_cha';
    protected $primaryKey = 'id_bien_muc_truong_cha';
    public $timestamps = false;
    protected $fillable = [
        'id_bien_muc_bieu_ghi',
        'ma_truong',
        'ct1',
        'ct2',
    ];
}
