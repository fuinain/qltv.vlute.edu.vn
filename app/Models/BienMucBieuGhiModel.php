<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BienMucBieuGhiModel extends Model
{
    protected $table = 'bien_muc_bieu_ghi';
    protected $primaryKey = 'id_bien_muc_bieu_ghi';
    public $timestamps = false;
    protected $fillable = [
        'id_sach',
        'id_tai_lieu',
        'trang_thai_bieu_ghi',
        'id_chuyen_nganh',
        'id_don_vi',
    ];

    public function sach(): BelongsTo
    {
        return $this->belongsTo(SachModel::class, 'id_sach', 'id_sach');
    }

    public function taiLieu(): BelongsTo
    {
        return $this->belongsTo(TaiLieuModel::class, 'id_tai_lieu', 'id_tai_lieu');
    }


    public function truongCha(): HasMany
    {
        return $this->hasMany(BienMucTruongChaModel::class, 'id_bien_muc_bieu_ghi', 'id_bien_muc_bieu_ghi');
    }
}
