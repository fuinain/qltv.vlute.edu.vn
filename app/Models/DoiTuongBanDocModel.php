<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class DoiTuongBanDocModel extends Model
{
    protected $table = 'doi_tuong_ban_doc';
    protected $primaryKey = 'id_doi_tuong_ban_doc';
    public $timestamps = false;

    protected $fillable = [
        'ma_doi_tuong_ban_doc',
        'ten_doi_tuong_ban_doc',
    ];

    public static function createWithThamSoLuuThong(array $validated)
    {
        return DB::transaction(function () use ($validated) {
            // Tạo mới bạn đọc
            $doiTuong = self::create($validated);

            // Lấy danh sách id chi tiết điểm lưu thông
            $chiTietIds = ChiTietDiemLuuThongModel::pluck('id_diem_luu_thong');

            // Tạo các bản ghi tham số lưu thông
            foreach ($chiTietIds as $idChiTiet) {
                ThamSoLuuThongModel::create([
                    'id_diem_luu_thong' => $idChiTiet,
                    'id_doi_tuong_ban_doc' => $doiTuong->id_doi_tuong_ban_doc,
                ]);
            }

            return $doiTuong;
        });
    }

    public static function updateWithThamSoLuuThong($id, array $validated)
    {
        return DB::transaction(function () use ($id, $validated) {
            $doiTuong = self::findOrFail($id);
            $doiTuong->update($validated);

            // Lấy các id chi tiết điểm lưu thông
            $chiTietIds = ChiTietDiemLuuThongModel::pluck('id_diem_luu_thong');

            foreach ($chiTietIds as $idChiTiet) {
                // Kiểm tra nếu chưa có bản ghi thì mới tạo
                $exists = ThamSoLuuThongModel::where('id_doi_tuong_ban_doc', $doiTuong->id_doi_tuong_ban_doc)
                    ->where('id_diem_luu_thong', $idChiTiet)
                    ->exists();

                if (!$exists) {
                    ThamSoLuuThongModel::create([
                        'id_diem_luu_thong' => $idChiTiet,
                        'id_doi_tuong_ban_doc' => $doiTuong->id_doi_tuong_ban_doc,
                    ]);
                }
            }

            return $doiTuong;
        });
    }

    public static function deleteWithRelated($id)
    {
        return DB::transaction(function () use ($id) {
            // Xóa các bản ghi liên quan trong tham_so_luu_thong
            ThamSoLuuThongModel::where('id_doi_tuong_ban_doc', $id)->delete();

            // Xóa bản ghi chính
            return self::destroy($id);
        });
    }

    public static function listDoiTuongBanDoc()
    {
        return DB::table('doi_tuong_ban_doc')
            ->select(
                'id_doi_tuong_ban_doc',
                'ten_doi_tuong_ban_doc',
            )
            ->get();
    }

}
