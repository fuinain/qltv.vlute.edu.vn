<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HocKyModel extends Model
{
    protected $table = 'hoc_ky';
    protected $primaryKey = 'id_hoc_ky';
    public $timestamps = false;

    protected $fillable = [
        'ma_hoc_ky',
        'ten_hoc_ky',
        'nam_hoc',
        'loai_hoc_ky',
    ];
}