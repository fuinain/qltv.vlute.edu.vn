<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChuDeBaiVietModel extends Model
{
    protected $table = 'chu_de_bai_viet';
    protected $primaryKey = 'id_chu_de_bai_viet';
    public $timestamps = false;
    protected $fillable = [
        'ten_chu_de_bai_viet',
    ];
}
