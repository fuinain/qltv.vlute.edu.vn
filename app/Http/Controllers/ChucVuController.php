<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ChucVuModel;

class ChucVuController extends Controller
{
    // GET /api/chuc-vu
    public function index(Request $request)
    {
        $query = ChucVuModel::query();
        return response()->json([
            'status' => 200,
            'data' => $query->paginate(perPage: 10)
        ]);
    }

    // POST /api/chuc-vu
    public function store(Request $request)
    {
        $validated = $request->validate([
            'ma_chuc_vu' => 'required|string',
            'ten_chuc_vu' => 'required|string',
        ]);
        $chucvu = ChucVuModel::create($validated);
        return response()->json(['status' => 200, 'message' => 'Thêm thành công', 'data' => $chucvu]);
    }

    // PUT /api/chuc-vu/{id}
    public function update(Request $request, $id)
    {
        $chucvu = ChucVuModel::findOrFail($id);
        $chucvu->update($request->all());
        return response()->json(['status' => 200, 'message' => 'Cập nhật thành công']);
    }

    // DELETE /api/chuc-vu/{id}
    public function destroy($id)
    {
        ChucVuModel::destroy($id);
        return response()->json(['status' => 200, 'message' => 'Xóa thành công']);
    }
}
