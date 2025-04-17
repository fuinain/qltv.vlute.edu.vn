<?php

namespace App\Http\Controllers;

use App\Models\PhatBanDocModel;
use Illuminate\Http\Request;

class PhatBanDocController extends Controller
{
    public function index(Request $request)
    {
        $query = PhatBanDocModel::query();
        return response()->json([
            'status' => 200,
            'data' => $query->paginate(perPage: 10)
        ]);
    }

    public function store(Request $request)
    {
        $rules = [
            'ma_loai' => 'required|string',
            'ten_loai_phat' => 'required|string',
            'ghi_chu' => 'required|string',
        ];

        $messages = [
            'ma_loai.required' => 'Vui lòng nhập mã loại.',
            'ma_loai.string' => 'Mã loại phải là chuỗi ký tự.',
            'ten_loai_phat.required' => 'Vui lòng nhập tên loại phạt.',
            'ten_loai_phat.string' => 'Tên loại phạt phải là chuỗi ký tự.',
            'ghi_chu.required' => 'Vui lòng nhập ghi chú.',
            'ghi_chu.string' => 'Ghi chú phải là chuỗi ký tự.',
        ];

        $validated = $request->validate($rules, $messages);

        $data = PhatBanDocModel::create($validated);
        return response()->json([
            'status' => 200,
            'message' => 'Thêm thành công',
            'data' => $data
        ]);
    }

    public function update(Request $request, $id)
    {
        $model = PhatBanDocModel::findOrFail($id);
        $model->update($request->all());
        return response()->json(['status' => 200, 'message' => 'Cập nhật thành công']);
    }

    public function destroy($id)
    {
        PhatBanDocModel::destroy($id);
        return response()->json(['status' => 200, 'message' => 'Xóa thành công']);
    }
}
