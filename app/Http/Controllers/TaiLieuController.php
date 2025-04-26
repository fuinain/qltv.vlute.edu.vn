<?php

namespace App\Http\Controllers;

use App\Models\TaiLieuModel;
use Illuminate\Http\Request;

class TaiLieuController extends Controller
{
    public function index(Request $request)
    {
        $query = TaiLieuModel::query();
        return response()->json([
            'status' => 200,
            'data' => $query->paginate(perPage: 10)
        ]);
    }

    public function store(Request $request)
    {
        $rules = [
            'ma_tai_lieu' => 'required|string',
            'ten_tai_lieu' => 'required|string',
        ];

        $messages = [
            'ma_tai_lieu.required' => 'Vui lòng nhập mã tài liệu.',
            'ma_tai_lieu.string' => 'Mã tài liệu phải là chuỗi ký tự.',
            'ten_tai_lieu.required' => 'Vui lòng nhập tên tài liệu.',
            'ten_tai_lieu.string' => 'Tên tài liệu phải là chuỗi ký tự.',
        ];

        $validated = $request->validate($rules, $messages);

        $data = TaiLieuModel::create($validated);
        return response()->json([
            'status' => 200,
            'message' => 'Thêm thành công',
            'data' => $data
        ]);
    }

    public function update(Request $request, $id)
    {
        $model = TaiLieuModel::findOrFail($id);
        $model->update($request->all());
        return response()->json(['status' => 200, 'message' => 'Cập nhật thành công']);
    }

    public function destroy($id)
    {
        TaiLieuModel::destroy($id);
        return response()->json(['status' => 200, 'message' => 'Xóa thành công']);
    }

    public function listTaiLieuSelectOption()
    {
        $data = TaiLieuModel::listTaiLieu();
        return response()->json([
            'status' => 200,
            'data' => $data
        ]);
    }
}
