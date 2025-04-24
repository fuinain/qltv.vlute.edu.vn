<?php

namespace App\Http\Controllers;

use App\Models\NguonNhanModel;
use Illuminate\Http\Request;

class NguonNhanController extends Controller
{
    public function index(Request $request)
    {
        $query = NguonNhanModel::query();
        return response()->json([
            'status' => 200,
            'data' => $query->paginate(perPage: 10)
        ]);
    }

    public function store(Request $request)
    {
        $rules = [
            'ma_nguon_nhan' => 'required|string',
            'ten_nguon' => 'required|string',
            'kinh_phi' => 'string',
        ];

        $messages = [
            'ma_nguon_nhan.required' => 'Vui lòng nhập mã nguồn nhận.',
            'ma_nguon_nhan.string' => 'Mã nguồn nhận phải là chuỗi ký tự.',
            'ten_nguon.required' => 'Vui lòng nhập tên nguồn nhận.',
            'ten_nguon.string' => 'Tên nguồn nhận phải là chuỗi ký tự.',
        ];

        $validated = $request->validate($rules, $messages);

        $data = NguonNhanModel::create($validated);
        return response()->json(['status' => 200, 'message' => 'Thêm thành công', 'data' => $data]);
    }

    public function update(Request $request, $id)
    {
        $model = NguonNhanModel::findOrFail($id);
        $model->update($request->all());
        return response()->json(['status' => 200, 'message' => 'Cập nhật thành công']);
    }

    public function destroy($id)
    {
        NguonNhanModel::destroy($id);
        return response()->json(['status' => 200, 'message' => 'Xóa thành công']);
    }

    public function listNguonNhanSelectOption()
    {
        $data = NguonNhanModel::listNguonNhan();
        return response()->json([
            'status' => 200,
            'data' => $data
        ]);
    }
}
