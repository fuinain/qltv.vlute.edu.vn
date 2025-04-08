<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DonViModel;

class DonViController extends Controller
{
    // GET /api/don-vi
    public function index(Request $request)
    {
        $query = DonViModel::query();
        return response()->json([
            'status' => 200,
            'data' => $query->paginate(perPage: 10)
        ]);
    }

    // POST /api/don-vi
    public function store(Request $request)
    {
        $validated = $request->validate([
            'ma_don_vi' => 'required|string',
            'ten_don_vi' => 'required|string',
        ]);

        $donvi = DonViModel::create($validated);
        return response()->json(['status' => 200, 'message' => 'Thêm thành công', 'data' => $donvi]);
    }

    // PUT /api/don-vi/{id}
    public function update(Request $request, $id)
    {
        $donvi = DonViModel::findOrFail($id);
        $donvi->update($request->all());
        return response()->json(['status' => 200, 'message' => 'Cập nhật thành công']);
    }

    // DELETE /api/don-vi/{id}
    public function destroy($id)
    {
        DonViModel::destroy($id);
        return response()->json(['status' => 200, 'message' => 'Xóa thành công']);
    }

    public function listDonViSelectOption(){
        $query = DonViModel::getListDonViSelectOption();
        return response()->json([
            'status' => 200,
            'data' => $query
        ]);
    }
}
