<?php

namespace App\Http\Controllers;

use App\Models\DonViModel;
use App\Models\GiangVienModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\IOFactory;

class GiangVienController extends Controller
{
    public function index(Request $request)
    {
        $id_don_vi = $request->id_don_vi;
        $ds = GiangVienModel::query()
            ->leftJoin('don_vi','don_vi.id_don_vi','giang_vien.id_don_vi')
            ->when(!empty($id_don_vi), function ($q) use ($id_don_vi){
                $q->where('giang_vien.id_don_vi',$id_don_vi);
            })
            ->select('giang_vien.*','don_vi.ma_don_vi','don_vi.ten_don_vi')
            ->paginate(perPage: 10)
        ;

        return response()->json([
            'status' => 200,
            'data' => $ds
        ]);
    }

    public function store(Request $request)
    {
        $ho_ten = $request->ho_ten;
        $email = $request->email;
        $id_don_vi = $request->id_don_vi;

        $errors = [];

        if (empty($ho_ten))
            $errors[] = "Họ tên không để trống";

        if (empty($email))
            $errors[] = "Email không để trống";

        if (empty($id_don_vi))
            $errors[] = "Đơn vị không để trống";

        if($errors)
            return response()->json([
                'status' => 400,
                'message' => $errors
            ]);

        $insert = GiangVienModel::create([
            'ho_ten' => $ho_ten,
            'email' => $email,
            'id_don_vi' => $id_don_vi
        ]);

        return response()->json(['status' => 200, 'message' => 'Thêm dữ liệu thành công', 'data' => $insert]);

    }

    public function update(Request $request)
    {
        $id_giang_vien = $request->id_giang_vien;
        $ho_ten = $request->ho_ten;
        $email = $request->email;
        $id_don_vi = $request->id_don_vi;

        $errors = [];

        if (empty($ho_ten))
            $errors[] = "Họ tên không để trống";

        if (empty($email))
            $errors[] = "Email không để trống";

        if (empty($id_don_vi))
            $errors[] = "Đơn vị không để trống";

        if($errors)
            return response()->json([
                'status' => 400,
                'message' => $errors
            ]);

        $giang_vien = GiangVienModel::findOrFail($id_giang_vien);

        $giang_vien->update([
            'ho_ten' => $ho_ten,
            'email' => $email,
            'id_don_vi' => $id_don_vi
        ]);

        return response()->json(['status' => 200, 'message' => 'Cập nhật dữ liệu thành công']);
    }

    public function destroy(Request $request)
    {
        $id_giang_vien = $request->id_giang_vien;
        $giang_vien = GiangVienModel::findOrFail($id_giang_vien);

        $giang_vien->delete();

        return response()->json(['status' => 200, 'message' => 'Xóa dữ liệu thành công']);
    }

    public function importGiangVien(Request $r)
    {
        $file = $r->file('file');

        if (!$file) {
            return response()->json([
                'status' => 404,
                'message' => 'Vui lòng chọn file'
            ]);
        }

        // Đọc file Excel
        $spreadsheet = IOFactory::load($file->getPathname());
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray();

        $dsGiangVien = [];
        $errors = [];

        foreach ($rows as $index => $row) {
            if (array_filter($row, fn($cell) => trim($cell) !== '')) {
                if ($index <= 1) continue;

                $ho_ten = trim($row[0]);
                $email = trim($row[1]);
                $ten_don_vi = trim($row[2]);

                // Tìm id_sinh_vien từ bảng don_vi
                $don_vi = DonViModel::where('ten_don_vi', $ten_don_vi)->first();

                // nếu không tồn tại đơn vị thì bỏ qua
                if (!$don_vi) {
                    $dong = $index + 1;
                    $errors[] = "Dòng {$dong}: Không tìm thấy đơn vị '{$ten_don_vi}' trong CSDL";
                    continue;
                }

                // Chuẩn bị dữ liệu để insert (Miễn giam)
                DB::table('giang_vien')->updateOrInsert(
                    ['email' => $email], // điều kiện tìm
                    [
                        'ho_ten' => $ho_ten,
                        'id_don_vi' => $don_vi->id_don_vi
                    ] // giá trị cập nhật/thêm
                );
            }
        }

        // Thực hiện insert dữ liệu hợp lệ

        if (!empty($errors)) {
            Log::channel('daily')->error("Lỗi import giảng viên:", $errors);
            return response()->json([
                'status' => 409,
                'message' => 'Import không hoàn toàn',
                'data' => $errors
            ]);
        }

        return response()->json([
            'status' => 200,
            'message' => 'Import thành công'
        ]);

    }



}
