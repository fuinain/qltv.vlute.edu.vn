<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ChuyenNganhModel;

class ChuyenNganhController extends Controller
{
    public function index(Request $request)
    {
        $query = ChuyenNganhModel::getDanhSachChuyenNganhWithTenDonVi();
        return response()->json([
            'status' => 200,
            'data' => $query->paginate(perPage: 10)
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'ma_chuyen_nganh' => 'required|string',
            'ten_chuyen_nganh' => 'required|string',
            'id_don_vi' => 'required',
        ]);

        $chuyennganh = ChuyenNganhModel::create($validated);

        return response()->json([
            'status' => 200,
            'message' => 'Thêm thành công',
            'data' => $chuyennganh
        ]);
    }

    public function update(Request $request, $id)
    {
        $chuyennganh = ChuyenNganhModel::findOrFail($id);
        $chuyennganh->update($request->all());
        return response()->json(['status' => 200, 'message' => 'Cập nhật thành công']);
    }

    public function destroy($id)
    {
        ChuyenNganhModel::destroy($id);
        return response()->json(['status' => 200, 'message' => 'Xóa thành công']);
    }

    public function listByDonViSelectOption($id_don_vi)
    {
        $rows = ChuyenNganhModel::getListByDonVi($id_don_vi);

        return response()->json([
            'status' => 200,
            'data'   => $rows,
        ]);
    }

    
}
