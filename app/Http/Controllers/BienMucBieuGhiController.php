<?php

namespace App\Http\Controllers;

use App\Models\BienMucBieuGhiModel;
use Illuminate\Http\Request;

class BienMucBieuGhiController extends Controller
{
    public function show(int $id_sach, Request $request)
    {
        $data = BienMucBieuGhiModel::where('id_sach', $id_sach)->first();

        return response()->json([
            'status' => 200,
            'data'   => $data,
        ]);
    }

    public function store(Request $request)
    {
        $rules = [
            'id_sach'             => 'nullable|integer',
            'id_tai_lieu'         => 'nullable|integer',
            'trang_thai_bieu_ghi' => 'nullable|string',
            'id_don_vi'           => 'nullable|integer',
            'id_chuyen_nganh'     => 'nullable|integer',
        ];

        $messages = [

        ];

        $validated = $request->validate($rules, $messages);

        try {
            BienMucBieuGhiModel::updateOrCreate(
                ['id_sach' => $validated['id_sach']],
                $validated
            );

            return response()->json([
                'status'  => 200,
                'message' => 'Lưu thông tin biên mục thành công',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 500,
                'message' => 'Lưu thất bại',
            ], 500);
        }
    }
}
