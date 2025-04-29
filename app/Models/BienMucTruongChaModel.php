<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BienMucTruongChaModel extends Model
{
    protected $table = 'bien_muc_truong_cha';
    protected $primaryKey = 'id_bien_muc_truong_cha';
    public $timestamps = false;
    protected $fillable = [
        'id_bien_muc_bieu_ghi',
        'ma_truong',
        'ct1',
        'ct2',
    ];

    public function children(): HasMany
    {
        return $this->hasMany(
            BienMucTruongConModel::class,
            'id_bien_muc_truong_cha',
            'id_bien_muc_truong_cha'
        );
    }

    public function bieuGhi(): BelongsTo
    {
        return $this->belongsTo(BienMucBieuGhiModel::class, 'id_bien_muc_bieu_ghi', 'id_bien_muc_bieu_ghi');
    }

    /** Lấy tất cả cha-con của một cuốn sách */
    public static function listBySach(int $id_sach)
    {
        $bieuGhi = BienMucBieuGhiModel::query()
            ->where('id_sach', $id_sach)
            ->firstOrFail();

        return self::with(['children' => function ($q) {
            /* Số ưu tiên, rồi tới chữ */
            $q->orderByRaw(
                "CASE WHEN ma_truong_con REGEXP '^[0-9]+$' THEN 0 ELSE 1 END"
            )->orderByRaw("CAST(ma_truong_con AS UNSIGNED), ma_truong_con");
        }])
            ->where('id_bien_muc_bieu_ghi', $bieuGhi->id_bien_muc_bieu_ghi)
            ->orderByRaw("CAST(ma_truong AS UNSIGNED), ma_truong")
            ->get();
    }
}
