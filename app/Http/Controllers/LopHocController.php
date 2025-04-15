<?php

namespace App\Http\Controllers;

use App\Models\LopHocModel;
use Illuminate\Http\Request;

class LopHocController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $data = LopHocModel::getLopHoc($perPage);

        return response()->json([
            'status' => 200,
            'data' => $data
        ]);
    }

    public function store(Request $request)
    {
        $rules = [
            'ma_lop' => 'required|string',
            'han_su_dung' => 'required|date',
            'ten_lop' => 'required|string',
            'id_don_vi' => 'required|integer',
            'id_doi_tuong_ban_doc' => 'required|integer',
            'khoa_hoc' => 'required|string',
            'nien_khoa' => 'required|string',
        ];

        $messages = [
            'ma_lop.required' => 'Vui lòng nhập mã lớp.',
            'ma_lop.string' => 'Mã lớp phải là chuỗi ký tự.',
            'han_su_dung.required' => 'Vui lòng chọn hạn sử dụng.',
            'ten_lop.required' => 'Vui lòng nhập tên lớp.',
            'ten_lop.string' => 'Tên lớp phải là chuỗi ký tự.',
            'id_don_vi.required' => 'Vui lòng chọn đơn vị.',
            'id_doi_tuong_ban_doc.required' => 'Vui lòng chọn đối tượng bạn đọc.',
            'khoa_hoc.required' => 'Vui lòng nhập khoá học.',
            'nien_khoa.required' => 'Vui lòng nhập niên khoá.',
        ];

        $validated = $request->validate($rules, $messages);

        $data = LopHocModel::create($validated);
        return response()->json([
            'status' => 200,
            'message' => 'Thêm thành công',
            'data' => $data
        ]);
    }

    public function show($id){
        $data = LopHocModel::findOrFail($id);
        return response()->json([
            'status' => 200,
            'data' => $data
        ]);
    }

    public function update(Request $request, $id)
    {
        $rules = [
            'ma_lop' => 'required|string',
            'han_su_dung' => 'required|date',
            'ten_lop' => 'required|string',
            'id_don_vi' => 'required|integer',
            'id_doi_tuong_ban_doc' => 'required|integer',
            'khoa_hoc' => 'required|string',
            'nien_khoa' => 'required|string',
        ];

        $messages = [
            'ma_lop.required' => 'Vui lòng nhập mã lớp.',
            'ten_lop.required' => 'Vui lòng nhập tên lớp.',
            'han_su_dung.required' => 'Vui lòng chọn hạn sử dụng.',
            'id_don_vi.required' => 'Vui lòng chọn đơn vị.',
            'id_doi_tuong_ban_doc.required' => 'Vui lòng chọn đối tượng bạn đọc.',
            'khoa_hoc.required' => 'Vui lòng nhập khoá học.',
            'nien_khoa.required' => 'Vui lòng nhập niên khoá.',
        ];

        $validated = $request->validate($rules, $messages);

        $lopHoc = LopHocModel::findOrFail($id);
        $lopHoc->update($validated);

        return response()->json([
            'status' => 200,
            'message' => 'Cập nhật thành công',
            'data' => $lopHoc
        ]);
    }

    public function destroy($id)
    {
        $lopHoc = LopHocModel::findOrFail($id);
        $lopHoc->delete();

        return response()->json([
            'status' => 200,
            'message' => 'Xoá thành công'
        ]);
    }


}
