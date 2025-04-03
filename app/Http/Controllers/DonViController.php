<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\DonViModel;
class DonViController extends Controller
{
// GET /api/donvi
public function index(Request $request)
{
    $query = DonViModel::query();   
    return response()->json([
        'status' => 200,
        'data' => $query->paginate(perPage: 10)
    ]);
}

// POST /api/donvi
public function store(Request $request)
{
    $donvi = DonViModel::create($request->all());
    return response()->json(['status' => 200, 'message' => 'Thêm thành công', 'data' => $donvi]);
}

// PUT /api/donvi/{id}
public function update(Request $request, $id)
{
    $donvi = DonViModel::findOrFail($id);
    $donvi->update($request->all());
    return response()->json(['status' => 200, 'message' => 'Cập nhật thành công']);
}

// DELETE /api/donvi/{id}
public function destroy($id)
{
    DonViModel::destroy($id);
    return response()->json(['status' => 200, 'message' => 'Xóa thành công']);
}

}