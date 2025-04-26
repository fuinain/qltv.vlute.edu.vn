<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
}
