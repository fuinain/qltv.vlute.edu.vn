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

    public static function listDoiTuongBanDoc()
    {
        return DB::table('doi_tuong_ban_doc')
            ->select(
                'doi_tuong_ban_doc.id_doi_tuong_ban_doc',
                'doi_tuong_ban_doc.ten_doi_tuong_ban_doc',
            )
            ->get();
    }

}
