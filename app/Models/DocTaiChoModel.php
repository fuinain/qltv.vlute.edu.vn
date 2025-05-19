<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocTaiChoModel extends Model
{
    protected $table = 'doc_tai_cho';
    protected $primaryKey = 'id_doc_tai_cho';
    public $timestamps = false;

    protected $fillable = [
        'id_ban_doc',
        'id_dkcb',
        'gio_muon',
        'gio_tra',
        'qua_han',
    ];
}
