<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class LopHocModel extends Model
{
    protected $table = 'lop_hoc';
    protected $primaryKey = 'id_lop_hoc';
    public $timestamps = false;

    protected $fillable = [
        'ma_lop',
        'han_su_dung',
        'ten_lop',
        'id_don_vi',
        'id_doi_tuong_ban_doc',
        'khoa_hoc',
        'nien_khoa',
    ];

    public static function getLopHoc($perPage = 10)
    {
        return DB::table('lop_hoc')
            ->leftJoin('don_vi', 'lop_hoc.id_don_vi', '=', 'don_vi.id_don_vi')
            ->leftJoin('doi_tuong_ban_doc', 'lop_hoc.id_doi_tuong_ban_doc', '=', 'doi_tuong_ban_doc.id_doi_tuong_ban_doc')
            ->select(
                'lop_hoc.id_lop_hoc',
                'lop_hoc.ma_lop',
                'lop_hoc.ten_lop',
                'lop_hoc.han_su_dung',
                'lop_hoc.khoa_hoc',
                'lop_hoc.nien_khoa',
                'lop_hoc.ngay_cap_nhat',
                'lop_hoc.ngay_tao',
                'don_vi.ten_don_vi',
                'doi_tuong_ban_doc.ten_doi_tuong_ban_doc'
            )
            ->paginate($perPage);
    }
}
