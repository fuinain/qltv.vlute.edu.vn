<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ChucVuModel;
class ChucVuController extends Controller
{
// GET /api/chucvu
public function index(Request $request)
{
    $query = ChucVuModel::query();   
    return response()->json([
        'status' => 200,
        'data' => $query->paginate(perPage: 10)
    ]);
}

// POST /api/chucvu
public function store(Request $request)
{
    $chucvu = ChucVuModel::create($request->all());
    return response()->json(['status' => 200, 'message' => 'Thêm thành công', 'data' => $chucvu]);
}

// PUT /api/chucvu/{id}
public function update(Request $request, $id)
{
    $chucvu = ChucVuModel::findOrFail($id);
    $chucvu->update($request->all());
    return response()->json(['status' => 200, 'message' => 'Cập nhật thành công']);
}

// DELETE /api/chucvu/{id}
public function destroy($id)
{
    ChucVuModel::destroy($id);
    return response()->json(['status' => 200, 'message' => 'Xóa thành công']);
}
}