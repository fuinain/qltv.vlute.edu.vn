<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class DiemLuuThongModel extends Model
{
    protected $table = 'diem_luu_thong';
    protected $primaryKey = 'id_diem_luu_thong';
    public $timestamps = false;

    protected $fillable = ['ma_loai', 'ten_diem'];

    public static function them($data)
    {
        return DB::transaction(function () use ($data) {
            $id = DB::table('diem_luu_thong')->insertGetId([
                'ma_loai' => $data['ma_loai'],
                'ten_diem' => $data['ten_diem'],
            ]);

            if (!empty($data['id_kho_an_pham'])) {
                ChiTietDiemLuuThongModel::themNhieu($id, $data['id_kho_an_pham']);
            }

            $doiTuongIds = DB::table('doi_tuong_ban_doc')->pluck('id_doi_tuong_ban_doc');

            foreach ($doiTuongIds as $idBanDoc) {
                ThamSoLuuThongModel::create([
                    'id_diem_luu_thong' => $id,
                    'id_doi_tuong_ban_doc' => $idBanDoc,
                ]);
            }

            return $id;
        });
    }


    public static function capNhat($id, $data)
    {
        return DB::transaction(function () use ($id, $data) {
            DB::table('diem_luu_thong')->where('id_diem_luu_thong', $id)->update([
                'ma_loai' => $data['ma_loai'],
                'ten_diem' => $data['ten_diem'],
            ]);

            ChiTietDiemLuuThongModel::xoaTheoDiem($id);

            if (!empty($data['id_kho_an_pham'])) {
                ChiTietDiemLuuThongModel::themNhieu($id, $data['id_kho_an_pham']);
            }
        });
    }

    public static function xoa($id)
    {
        return DB::transaction(function () use ($id) {
            ChiTietDiemLuuThongModel::xoaTheoDiem($id);
            DB::table('diem_luu_thong')->where('id_diem_luu_thong', $id)->delete();
        });
    }

    public static function layTatCa($perPage = 10)
    {
        $paginator = DB::table('diem_luu_thong as dlt')
            ->leftJoin('chi_tiet_diem_luu_thong as ct', 'dlt.id_diem_luu_thong', '=', 'ct.id_diem_luu_thong')
            ->leftJoin('kho_an_pham as kho', 'kho.id_kho_an_pham', '=', 'ct.id_kho_an_pham')
            ->select(
                'dlt.id_diem_luu_thong',
                'dlt.ma_loai',
                'dlt.ten_diem',
                DB::raw("GROUP_CONCAT(kho.ten_kho SEPARATOR ' / ') as ten_kho"),
                DB::raw('GROUP_CONCAT(kho.id_kho_an_pham) as id_kho_an_pham'),
                'dlt.ngay_cap_nhat',
                'dlt.ngay_tao',
            )
            ->groupBy('dlt.id_diem_luu_thong')
            ->paginate($perPage);

        // map lại từng item
        $paginator->getCollection()->transform(function ($item) {
            $item->id_kho_an_pham = $item->id_kho_an_pham ? explode(',', $item->id_kho_an_pham) : [];
            return $item;
        });

        return $paginator;
    }

}
