<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CheckinBanDocModel extends Model
{
    protected $table = 'checkin_ban_doc';
    protected $primaryKey = 'id_checkin_ban_doc';
    public $timestamps = false;

    protected $fillable = [
        'id_ban_doc',
        'thoi_gian_den',
    ];
}
