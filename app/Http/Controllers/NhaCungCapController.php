<?php

namespace App\Http\Controllers;

use App\Models\NhaCungCapModel;
use Illuminate\Http\Request;

class NhaCungCapController extends Controller
{
    public function index(Request $request)
    {
        $query = NhaCungCapModel::query();
        return response()->json([
            'status' => 200,
            'data' => $query->paginate(perPage: 10)
        ]);
    }

    public function store(Request $request)
    {
        $rules = [
            'ma_nha_cung_cap' => 'required|string',
            'ten_nha_cung_cap' => 'required|string',
            'dia_chi' => 'string',
            'dien_thoai' => 'string',
            'email' => 'string',
            'lien_he' => 'string',
            'stk' => 'string',
            'ngan_hang' => 'string',
        ];

        $messages = [
            'ma_nha_cung_cap.required' => 'Vui lòng nhập mã ncc.',
            'ma_nha_cung_cap.string' => 'Mã ncc phải là chuỗi ký tự.',
            'ten_nha_cung_cap.required' => 'Vui lòng nhập tên ncc.',
            'ten_nha_cung_cap.string' => 'Tên ncc phải là chuỗi ký tự.',
        ];

        $validated = $request->validate($rules, $messages);

        $data = NhaCungCapModel::create($validated);
        return response()->json(['status' => 200, 'message' => 'Thêm thành công', 'data' => $data]);
    }

    public function update(Request $request, $id)
    {
        $model = NhaCungCapModel::findOrFail($id);
        $model->update($request->all());
        return response()->json(['status' => 200, 'message' => 'Cập nhật thành công']);
    }

    public function destroy($id)
    {
        NhaCungCapModel::destroy($id);
        return response()->json(['status' => 200, 'message' => 'Xóa thành công']);
    }

    public function listNCCSelectOption()
    {
        $data = NhaCungCapModel::listNCC();
        return response()->json([
            'status' => 200,
            'data' => $data
        ]);
    }
}
