<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ChuyenNganhModel;

class ChuyenNganhController extends Controller
{
    // GET /api/chuyen-nganh
    public function index(Request $request)
    {
        $query = ChuyenNganhModel::getDanhSachChuyenNganhWithTenDonVi();
        return response()->json([
            'status' => 200,
            'data' => $query->paginate(perPage: 10)
        ]);
    }

    // POST /api/chuyen-nganh
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

    // PUT /api/chuyen-nganh/{id}
    public function update(Request $request, $id)
    {
        $chuyennganh = ChuyenNganhModel::findOrFail($id);
        $chuyennganh->update($request->all());
        return response()->json(['status' => 200, 'message' => 'Cập nhật thành công']);
    }

    // DELETE /api/chuyen-nganh/{id}
    public function destroy($id)
    {
        ChuyenNganhModel::destroy($id);
        return response()->json(['status' => 200, 'message' => 'Xóa thành công']);
    }
}
