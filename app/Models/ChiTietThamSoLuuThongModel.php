<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ChiTietThamSoLuuThongModel extends Model
{
    protected $table = 'chi_tiet_tham_so_lt';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'id_doi_tuong_ban_doc',
        'id_diem_luu_thong',
        'id_kho_an_pham',
        'muon',
        'giu',
        'gia_han',
    ];

    public static function taoNeuChuaCo($idDoiTuong, $idDiem)
    {
        // Danh sách kho đang còn tồn tại trong hệ thống tại thời điểm hiện tại
        $idKhoList = DB::table('chi_tiet_diem_luu_thong')
            ->where('id_diem_luu_thong', $idDiem)
            ->pluck('id_kho_an_pham')
            ->toArray(); // Chuyển thành mảng để dễ xử lý

        foreach ($idKhoList as $idKho) {
            $exists = self::where('id_doi_tuong_ban_doc', $idDoiTuong)
                ->where('id_diem_luu_thong', $idDiem)
                ->where('id_kho_an_pham', $idKho)
                ->exists();

            if (!$exists) {
                self::create([
                    'id_doi_tuong_ban_doc' => $idDoiTuong,
                    'id_diem_luu_thong' => $idDiem,
                    'id_kho_an_pham' => $idKho,
                    'muon' => 0,
                    'giu' => 0,
                    'gia_han' => 0,
                ]);
            }
        }

        self::where('id_doi_tuong_ban_doc', $idDoiTuong)
            ->where('id_diem_luu_thong', $idDiem)
            ->whereNotIn('id_kho_an_pham', $idKhoList)
            ->delete();
    }


    public static function layTheoDoiTuongVaDiem($idDoiTuong, $idDiem)
    {
        return DB::table('chi_tiet_tham_so_lt as ct')
            ->join('kho_an_pham as kho', 'ct.id_kho_an_pham', '=', 'kho.id_kho_an_pham')
            ->where('ct.id_doi_tuong_ban_doc', $idDoiTuong)
            ->where('ct.id_diem_luu_thong', $idDiem)
            ->select(
                'ct.id',
                'ct.id_kho_an_pham',
                'kho.ten_kho',
                'ct.muon',
                'ct.giu',
                'ct.gia_han'
            )
            ->get();
    }

    public static function capNhatNhieu(array $danhSach)
    {
        DB::transaction(function () use ($danhSach) {
            foreach ($danhSach as $item) {
                self::where('id', $item['id'])
                    ->update([
                        'muon' => $item['muon'] ?? 0,
                        'giu' => $item['giu'] ?? 0,
                        'gia_han' => $item['gia_han'] ?? 0,
                    ]);
            }
        });
    }
}
