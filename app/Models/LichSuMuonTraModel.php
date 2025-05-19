<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LichSuMuonTraModel extends Model
{
    protected $table = 'lich_su_muon_tra';
    protected $primaryKey = 'id_lich_su';
    public $timestamps = false;

    protected $fillable = [
        'id_ban_doc',
        'id_dkcb',
        'ma_dkcb',
        'ten_sach',
        'ngay_muon',
        'han_tra',
        'ngay_tra',
        'tai_cho',
        'gia_han'
    ];
    
    // Quan hệ với bảng đọc giả
    public function docGia()
    {
        return $this->belongsTo(DocGiaModel::class, 'id_ban_doc', 'id_doc_gia');
    }
    
    // Quan hệ với bảng DKCB
    public function dkcb()
    {
        return $this->belongsTo(DKCBModel::class, 'id_dkcb', 'id_dkcb');
    }
} 