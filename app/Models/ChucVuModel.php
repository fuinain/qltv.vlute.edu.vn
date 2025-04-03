<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChucVuModel extends Model
{
    protected $table = 'chuc_vu';
    protected $primaryKey = 'id_chuc_vu';
    public $timestamps = false;

    protected $fillable = [
        'ma_chuc_vu',
        'ten_chuc_vu',
    ];
}