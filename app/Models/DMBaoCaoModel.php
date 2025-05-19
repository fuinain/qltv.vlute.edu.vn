<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DMBaoCaoModel extends Model
{
    protected $table = 'dm_bao_cao';
    protected $primaryKey = 'id_dm_bao_cao';
    public $timestamps = false;

    protected $fillable = [
        'ten_bao_cao',
    ];
}
