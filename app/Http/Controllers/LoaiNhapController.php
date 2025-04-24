<?php

namespace App\Http\Controllers;

use App\Models\LoaiNhapModel;
use Illuminate\Http\Request;

class LoaiNhapController extends Controller
{
    public function index(Request $request)
    {
        $query = LoaiNhapModel::query();
        return response()->json([
            'status' => 200,
            'data' => $query->paginate(perPage: 10)
        ]);
    }

    public function store(Request $request)
    {
        $rules = [
            'ten_loai_nhap' => 'required|string',
        ];

        $messages = [
            'ten_loai_nhap.required' => 'Vui lòng nhập tên loại nhập.',
            'ten_loai_nhap.string' => 'Tên loại nhập phải là chuỗi ký tự.',
        ];

        $validated = $request->validate($rules, $messages);

        $data = LoaiNhapModel::create($validated);
        return response()->json(['status' => 200, 'message' => 'Thêm thành công', 'data' => $data]);
    }

    public function update(Request $request, $id)
    {
        $model = LoaiNhapModel::findOrFail($id);
        $model->update($request->all());
        return response()->json(['status' => 200, 'message' => 'Cập nhật thành công']);
    }

    public function destroy($id)
    {
        LoaiNhapModel::destroy($id);
        return response()->json(['status' => 200, 'message' => 'Xóa thành công']);
    }

    public function listLoaiNhapSelectOption()
    {
        $data = LoaiNhapModel::listLoaiNhap();
        return response()->json([
            'status' => 200,
            'data' => $data
        ]);
    }
}
