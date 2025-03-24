<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\TaiKhoanModel;
class TaiKhoanController extends Controller
{
    public function index()
    {
        $accounts = TaiKhoanModel::all();
        return response()->json([
            'data' => $accounts
        ]);
    }

    public function store(Request $request) {
        $data = $request->validate([
            'ten_dang_nhap' => 'required|string|unique:tai_khoan',
            'ten_nguoi_dung' => 'required|string',
            'quyen' => 'required|string',
            'mat_khau' => 'required|string',
        ]);
        $data['mat_khau'] = bcrypt($data['mat_khau']);
        $account = TaiKhoanModel::create($data);
        return response()->json(['message' => 'Tạo tài khoản thành công!', 'data' => $account], 201);
    }
    public function update(Request $request, $id)
    {
        $account = TaiKhoanModel::find($id);
        if (!$account) {
            return response()->json([
                'message' => 'Không tìm thấy tài khoản'
            ], 404);
        }

        // Xác thực các trường được cập nhật (không cập nhật mật khẩu)
        $data = $request->validate([
            'ten_dang_nhap' => 'required|string|unique:tai_khoan,ten_dang_nhap,' . $id . ',id_tai_khoan',
            'ten_nguoi_dung' => 'required|string',
            'quyen' => 'required|string',
        ]);

        $account->update($data);
        return response()->json([
            'message' => 'Cập nhật tài khoản thành công!',
            'data' => $account
        ]);
    }
    public function destroy($id)
    {
        $account = TaiKhoanModel::find($id);

        if (!$account) {
            return response()->json([
                'message' => 'Không tìm thấy tài khoản'
            ], 404);
        }

        $account->delete();

        return response()->json([
            'message' => 'Xóa tài khoản thành công!'
        ]);
    }
}