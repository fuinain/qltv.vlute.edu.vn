<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MuonSachModel extends Model
{
    protected $table = 'muon_sach';
    protected $primaryKey = 'id_muon_sach';
    public $timestamps = false;

    protected $fillable = [
        'id_ban_doc',
        'id_dkcb',
        'ngay_muon',
        'han_tra',
        'gia_han',  
    ];
}
