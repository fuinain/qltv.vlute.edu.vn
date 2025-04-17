<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ChiTietDiemLuuThongModel extends Model
{
    protected $table = 'chi_tiet_diem_luu_thong';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = ['id_diem_luu_thong', 'id_kho_an_pham'];

    public static function themNhieu($id_diem, $id_kho_an_pham)
    {
        $data = collect($id_kho_an_pham)->map(fn($id_kho_an_pham) => [
            'id_diem_luu_thong' => $id_diem,
            'id_kho_an_pham' => (int)$id_kho_an_pham,
        ])->toArray();

        DB::table('chi_tiet_diem_luu_thong')->insert($data);
    }

    public static function xoaTheoDiem($id_diem)
    {
        DB::table('chi_tiet_diem_luu_thong')->where('id_diem_luu_thong', $id_diem)->delete();
    }
}
