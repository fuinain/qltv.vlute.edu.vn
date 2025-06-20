<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class GiangVienModel extends Model
{
    protected $table = 'giang_vien';
    protected $primaryKey = 'id_giang_vien';
    public $timestamps = false;

    protected $fillable = [
        'ho_ten',
        'email',
        'id_don_vi',
    ];

}
