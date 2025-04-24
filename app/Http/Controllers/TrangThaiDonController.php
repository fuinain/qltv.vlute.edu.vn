<?php

namespace App\Http\Controllers;

use App\Models\TrangThaiDonModel;
use Illuminate\Http\Request;

class TrangThaiDonController extends Controller
{
    public function index(Request $request)
    {
        $query = TrangThaiDonModel::query();
        return response()->json([
            'status' => 200,
            'data' => $query->paginate(perPage: 10)
        ]);
    }

    public function store(Request $request)
    {
        $rules = [
            'trang_thai' => 'required|string',
        ];

        $messages = [
            'trang_thai.required' => 'Vui lòng nhập tên trạng thái.',
            'trang_thai.string' => 'Tên trạng thái phải là chuỗi ký tự.',
        ];

        $validated = $request->validate($rules, $messages);

        $data = TrangThaiDonModel::create($validated);
        return response()->json(['status' => 200, 'message' => 'Thêm thành công', 'data' => $data]);
    }

    public function update(Request $request, $id)
    {
        $model = TrangThaiDonModel::findOrFail($id);
        $model->update($request->all());
        return response()->json(['status' => 200, 'message' => 'Cập nhật thành công']);
    }

    public function destroy($id)
    {
        TrangThaiDonModel::destroy($id);
        return response()->json(['status' => 200, 'message' => 'Xóa thành công']);
    }

    public function listTTDonSelectOption()
    {
        $data = TrangThaiDonModel::listTTDon();
        return response()->json([
            'status' => 200,
            'data' => $data
        ]);
    }
}
