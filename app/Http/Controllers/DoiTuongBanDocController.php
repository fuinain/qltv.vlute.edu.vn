<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DoiTuongBanDocModel;
class DoiTuongBanDocController extends Controller
{
    public function index(Request $request)
    {
        $query = DoiTuongBanDocModel::query();
        return response()->json([
            'status' => 200,
            'data' => $query->paginate(perPage: 10)
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'ma_doi_tuong_ban_doc' => 'required|string',
            'ten_doi_tuong_ban_doc' => 'required|string',
        ]);
        $data = DoiTuongBanDocModel::create($validated);
        return response()->json(['status' => 200, 'message' => 'Thêm thành công', 'data' => $data]);
    }

    public function update(Request $request, $id)
    {
        $model = DoiTuongBanDocModel::findOrFail($id);
        $model->update($request->all());
        return response()->json(['status' => 200, 'message' => 'Cập nhật thành công']);
    }

    public function destroy($id)
    {
        DoiTuongBanDocModel::destroy($id);
        return response()->json(['status' => 200, 'message' => 'Xóa thành công']);
    }

    public function listDoiTuongBanDocSelectOption(){
        $data = DoiTuongBanDocModel::listDoiTuongBanDoc();
        return response()->json([
            'status' => 200,
            'data' => $data
        ]);
    }
}
