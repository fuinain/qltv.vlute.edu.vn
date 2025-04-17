<?php

namespace App\Http\Controllers;

use App\Models\DiemLuuThongModel;
use Illuminate\Http\Request;

class DiemLuuThongController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'ma_loai' => 'required|string',
            'ten_diem' => 'required|string',
            'id_kho_an_pham' => 'nullable|array',
        ]);

        try {
            DiemLuuThongModel::them($request->all());
            return response()->json(['status' => 200, 'message' => 'Thêm thành công']);
        } catch (\Exception $e) {
            return response()->json(['status' => 500, 'message' => 'Lỗi: ' . $e->getMessage()]);
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'ma_loai' => 'required|string',
            'ten_diem' => 'required|string',
            'id_kho_an_pham' => 'nullable|array',
        ]);

        try {
            DiemLuuThongModel::capNhat($id, $request->all());
            return response()->json(['status' => 200, 'message' => 'Cập nhật thành công']);
        } catch (\Exception $e) {
            return response()->json(['status' => 500, 'message' => 'Lỗi: ' . $e->getMessage()]);
        }
    }

    public function destroy($id)
    {
        try {
            DiemLuuThongModel::xoa($id);
            return response()->json(['status' => 200, 'message' => 'Xóa thành công']);
        } catch (\Exception $e) {
            return response()->json(['status' => 500, 'message' => 'Lỗi: ' . $e->getMessage()]);
        }
    }

    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 10);
        $data = DiemLuuThongModel::layTatCa($perPage);

        return response()->json([
            'status' => 200,
            'data' => $data
        ]);
    }

}
