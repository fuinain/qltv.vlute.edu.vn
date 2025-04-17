<?php

namespace App\Http\Controllers;

use App\Models\KhoAnPhamModel;
use Illuminate\Http\Request;

class KhoAnPhamController extends Controller
{
    public function index(Request $request)
    {
        $query = KhoAnPhamModel::query();
        return response()->json([
            'status' => 200,
            'data' => $query->paginate(perPage: 10)
        ]);
    }

    public function store(Request $request)
    {
        $rules = [
            'ma_kho' => 'required|string',
            'ten_kho' => 'required|string',
        ];

        $messages = [
            'ma_kho.required' => 'Vui lòng nhập mã kho.',
            'ma_kho.string' => 'Mã kho phải là chuỗi ký tự.',
            'ten_kho.required' => 'Vui lòng nhập tên kho.',
            'ten_kho.string' => 'Tên kho phải là chuỗi ký tự.',
        ];

        $validated = $request->validate($rules, $messages);

        $data = KhoAnPhamModel::create($validated);
        return response()->json([
            'status' => 200,
            'message' => 'Thêm thành công',
            'data' => $data
        ]);
    }

    public function update(Request $request, $id)
    {
        $model = KhoAnPhamModel::findOrFail($id);
        $model->update($request->all());
        return response()->json(['status' => 200, 'message' => 'Cập nhật thành công']);
    }

    public function destroy($id)
    {
        KhoAnPhamModel::destroy($id);
        return response()->json(['status' => 200, 'message' => 'Xóa thành công']);
    }

    public function listDanhMucKhoAnPhamSelectOption(){
        $data = KhoAnPhamModel::listDanhMucKho();
        return response()->json([
            'status' => 200,
            'data' => $data
        ]);
    }
}
