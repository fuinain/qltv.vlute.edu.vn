<?php

namespace App\Http\Controllers;

use App\Models\DonNhanModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class DonNhanController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 10);
        $data = DonNhanModel::getListDonNhan($perPage);

        return response()->json([
            'status' => 200,
            'data' => $data
        ]);
    }

    public function store(Request $request)
    {
        $rules = [
            'ten_don_nhan' => 'required|string|max:255',
            'id_loai_nhap' => 'nullable|integer',
            'id_nguon_nhan' => 'nullable|integer',
            'id_trang_thai_don' => 'nullable|integer',
            'ngay_nhan' => 'nullable|date',
            'id_nha_cung_cap' => 'nullable|integer',
            'so_chung_tu' => 'nullable',
            'ghi_chu' => 'nullable',
        ];

        $messages = [
            'ten_don_nhan.required' => 'Tên đơn nhận không được để trống.',
        ];

        $validated = $request->validate($rules, $messages);
        $validated['nguoi_tao'] = Session::get('HoTen', 'System');
        try {
            $data = DonNhanModel::create($validated);
            return response()->json(['status' => 200, 'message' => 'Thêm thành công', 'data' => $data]);
        } catch (\Exception $e) {
            // Xử lý lỗi cơ bản nếu có sự cố khi tạo
            Log::error("Lỗi khi tạo Đơn nhận: " . $e->getMessage());
            return response()->json(['status' => 500, 'message' => 'Thêm thất bại, có lỗi xảy ra.'], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $model = DonNhanModel::findOrFail($id);
        $model->update($request->all());
        return response()->json(['status' => 200, 'message' => 'Cập nhật thành công']);
    }

    public function destroy($id)
    {
        DonNhanModel::destroy($id);
        return response()->json(['status' => 200, 'message' => 'Xóa thành công']);
    }
}
