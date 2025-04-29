<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class SachModel extends Model
{
    protected $table = 'sach';

    protected $primaryKey = 'id_sach';

    public $timestamps = false;

    protected $fillable = [
        'id_don_nhan',
        'nhan_de',
        'tac_gia',
        'nam_xuat_ban',
        'nha_xuat_ban',
        'noi_xuat_ban',
        'gia',
        'so_luong',
        'thanh_tien',
    ];

    public static function getListByDonNhan(int $id_don_nhan, int $perPage = 10)
    {
        return self::query()
            ->where('id_don_nhan', $id_don_nhan)
            ->orderBy('ngay_tao')
            ->paginate($perPage);
    }
    public function bienMucBieuGhi(): HasOne
    {
        return $this->hasOne(BienMucBieuGhiModel::class, 'id_sach', 'id_sach');
    }
    public function donNhan(): BelongsTo
    {
        return $this->belongsTo(DonNhanModel::class, 'id_don_nhan', 'id_don_nhan');
    }
}
