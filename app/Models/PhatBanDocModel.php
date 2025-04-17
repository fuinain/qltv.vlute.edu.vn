<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PhatBanDocModel extends Model
{
    protected $table = 'phat_ban_doc';
    protected $primaryKey = 'id_phat_ban_doc';
    public $timestamps = false;

    protected $fillable = [
        'ma_loai',
        'ten_loai_phat',
        'ghi_chu',
    ];
}
