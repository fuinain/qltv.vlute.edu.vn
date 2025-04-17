<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DoiTuongBanDocModel;
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
}
