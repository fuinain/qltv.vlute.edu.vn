<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DonNhanModel;
use App\Models\SachModel;
use App\Models\DocGiaModel;
use App\Models\DKCBModel;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function getThongKeTongQuan()
    {
        // Đếm tổng số đơn nhận
        $tongDonNhan = DonNhanModel::count();
        
        // Đếm tổng số đầu sách
        $tongSach = SachModel::count();
        
        // Đếm tổng số độc giả
        $tongDocGia = DocGiaModel::count();
        
        // Thống kê DKCB
        $thongKeDKCB = DB::table('dkcb')
            ->select([
                DB::raw('COUNT(*) as tong_dkcb'),
                DB::raw('COUNT(CASE WHEN id_sach IS NOT NULL THEN 1 END) as dkcb_da_gan'),
                DB::raw('COUNT(CASE WHEN id_sach IS NULL THEN 1 END) as dkcb_chua_gan')
            ])
            ->first();
        
        return response()->json([
            'tong_don_nhan' => $tongDonNhan,
            'tong_sach' => $tongSach,
            'tong_doc_gia' => $tongDocGia,
            'thong_ke_dkcb' => $thongKeDKCB
        ]);
    }
}
