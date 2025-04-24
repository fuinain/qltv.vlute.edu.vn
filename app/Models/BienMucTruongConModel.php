<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BienMucTruongConModel extends Model
{
    protected $table = 'bien_muc_truong_con';
    protected $primaryKey = 'id_bien_muc_truong_con';
    public $timestamps = false;
    protected $fillable = [
        'id_bien_muc_truong_cha',
        'ma_truong_con',
        'noi_dung',
    ];
}
