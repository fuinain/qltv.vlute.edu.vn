<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DoiTuongBanDocModel;
use App\Models\DocGiaModel;
use Illuminate\Support\Facades\DB;

class DoiTuongBanDocController extends Controller
{
    public function index(Request $request)
    {
        $query = DoiTuongBanDocModel::query();
        return response()->json([
            'status' => 200,
            'data' => $query->paginate(perPage: 10)
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'ma_doi_tuong_ban_doc' => 'required|string',
            'ten_doi_tuong_ban_doc' => 'required|string',
        ]);

        try {
            $data = DoiTuongBanDocModel::createWithThamSoLuuThong($validated);

            return response()->json([
                'status' => 200,
                'message' => 'Thêm thành công',
                'data' => $data,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Lỗi khi thêm dữ liệu',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'ma_doi_tuong_ban_doc' => 'required|string',
            'ten_doi_tuong_ban_doc' => 'required|string',
        ]);

        try {
            DoiTuongBanDocModel::updateWithThamSoLuuThong($id, $validated);

            return response()->json([
                'status' => 200,
                'message' => 'Cập nhật thành công',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Lỗi khi cập nhật',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    public function destroy($id)
    {
        try {
            DoiTuongBanDocModel::deleteWithRelated($id);
            return response()->json([
                'status' => 200,
                'message' => 'Xóa thành công',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Lỗi khi xóa',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    public function listDoiTuongBanDocSelectOption(){
        $data = DoiTuongBanDocModel::listDoiTuongBanDoc();
        return response()->json([
            'status' => 200,
            'data' => $data
        ]);
    }
    
    public function thongKeDoiTuongBanDoc()
    {
        try {
            $thongKe = DB::table('doi_tuong_ban_doc')
                ->select(
                    'doi_tuong_ban_doc.id_doi_tuong_ban_doc',
                    'doi_tuong_ban_doc.ma_so_quy_uoc',
                    'doi_tuong_ban_doc.ten_doi_tuong_ban_doc',
                    DB::raw('COUNT(doc_gia.id_doc_gia) as so_luong_ban_doc')
                )
                ->leftJoin('doc_gia', 'doi_tuong_ban_doc.ma_so_quy_uoc', '=', 'doc_gia.ma_so_quy_uoc')
                ->groupBy(
                    'doi_tuong_ban_doc.id_doi_tuong_ban_doc',
                    'doi_tuong_ban_doc.ma_so_quy_uoc',
                    'doi_tuong_ban_doc.ten_doi_tuong_ban_doc'
                )
                ->get();
            
            return response()->json([
                'status' => 200,
                'data' => $thongKe
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Lỗi khi thống kê dữ liệu',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    
    public function chiTietDocGiaTheoDoiTuong(Request $request, $ma_so_quy_uoc)
    {
        try {
            $perPage = $request->input('perPage', 10);
            
            $query = DocGiaModel::query()
                ->where('ma_so_quy_uoc', $ma_so_quy_uoc);
                
            if ($ma_so_quy_uoc == 99) {
                // Cán bộ giảng viên
                $query = $query->select(
                    'id_doc_gia',
                    'so_the',
                    'ho_ten',
                    'chuc_vu',
                    'ngay_cap_the',
                    DB::raw("(SELECT ten_don_vi FROM don_vi 
                             INNER JOIN chuyen_nganh ON don_vi.id_don_vi = chuyen_nganh.id_don_vi 
                             WHERE chuyen_nganh.id_chuyen_nganh = doc_gia.id_chuyen_nganh 
                             LIMIT 1) as don_vi")
                );
            } else {
                // Sinh viên và các đối tượng khác
                $query = $query->select(
                    'id_doc_gia',
                    'so_the',
                    'ho_ten',
                    'ten_lop',
                    'ngay_cap_the',
                    'han_the',
                    'nien_khoa',
                    DB::raw("(SELECT ten_don_vi FROM don_vi 
                             INNER JOIN chuyen_nganh ON don_vi.id_don_vi = chuyen_nganh.id_don_vi 
                             WHERE chuyen_nganh.id_chuyen_nganh = doc_gia.id_chuyen_nganh 
                             LIMIT 1) as khoa")
                );
            }
                
            return response()->json([
                'status' => 200,
                'ma_so_quy_uoc' => $ma_so_quy_uoc,
                'data' => $query->paginate($perPage)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Lỗi khi lấy chi tiết bạn đọc',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
