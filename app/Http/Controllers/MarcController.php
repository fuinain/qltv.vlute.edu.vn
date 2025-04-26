<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Models\BienMucBieuGhiModel;
use App\Models\BienMucTruongChaModel;
use App\Models\BienMucTruongConModel;

class MarcController extends Controller
{
    /* =======================================================
     * 1. Lấy danh sách cha–con của sách
     * =======================================================
     */
    public function index(int $id_sach): JsonResponse
    {
        $parents = BienMucTruongChaModel::listBySach($id_sach);
        return response()->json(['status' => 200, 'data' => $parents]);
    }

    /* =======================================================
     * 2. Thêm trường CHA
     * =======================================================
     */
    public function storeParent(Request $request): JsonResponse
    {
        /* ---------- Validate ---------- */
        $rules = [
            'id_sach'   => 'required|integer',
            'ma_truong' => 'required|string|max:10',
            'nhan'      => 'nullable|string|max:10',
            'ct1'       => 'nullable|string|max:10',
            'ct2'       => 'nullable|string|max:10',
        ];
        $messages = [
            'id_sach.required'   => 'Thiếu ID sách.',
            'ma_truong.required' => 'Nhãn không được để trống.',
        ];
        $data = $request->validate($rules, $messages);

        /* ---------- Check dup ---------- */
        $bieuGhi = BienMucBieuGhiModel::where('id_sach', $data['id_sach'])->firstOrFail();
        $dup = BienMucTruongChaModel::where('id_bien_muc_bieu_ghi', $bieuGhi->id_bien_muc_bieu_ghi)
            ->where('ma_truong', $data['ma_truong'])
            ->exists();
        if ($dup) {
            return response()->json([
                'status'=>422,
                'errors'=>['ma_truong'=>['Nhãn đã tồn tại']],
            ],422);
        }

        try {
            $parent = BienMucTruongChaModel::create([
                'id_bien_muc_bieu_ghi' => $bieuGhi->id_bien_muc_bieu_ghi,
                'ma_truong'            => $data['ma_truong'],
                'nhan'                 => $data['nhan'] ?? null,
                'ct1'                  => $data['ct1']  ?? null,
                'ct2'                  => $data['ct2']  ?? null,
            ]);

            return response()->json([
                'status'=>200,
                'message'=>'Đã thêm trường cha',
                'data'=>$parent,
            ]);
        } catch (\Exception $e) {
            Log::error('Lỗi tạo parent: '.$e->getMessage());
            return response()->json([
                'status'=>500,
                'message'=>'Thêm trường cha thất bại.',
            ],500);
        }
    }

    /* =======================================================
     * 3. Cập nhật trường CHA
     * =======================================================
     */
    public function updateParent(Request $request, int $id): JsonResponse
    {
        $rules = [
            'ma_truong' => 'required|string|max:10',
            'nhan'      => 'nullable|string|max:10',
            'ct1'       => 'nullable|string|max:10',
            'ct2'       => 'nullable|string|max:10',
        ];
        $messages = [
            'ma_truong.required' => 'Nhãn không được để trống.',
        ];
        $data = $request->validate($rules, $messages);

        $parent = BienMucTruongChaModel::findOrFail($id);

        /* dup check */
        $dup = BienMucTruongChaModel::where('id_bien_muc_bieu_ghi',$parent->id_bien_muc_bieu_ghi)
            ->where('ma_truong', $data['ma_truong'])
            ->where('id_bien_muc_truong_cha','!=',$id)
            ->exists();
        if ($dup) {
            return response()->json([
                'status'=>422,
                'errors'=>['ma_truong'=>['Nhãn đã tồn tại']],
            ],422);
        }

        try {
            $parent->update($data);
            return response()->json(['status'=>200,'message'=>'Cập nhật parent thành công']);
        } catch (\Exception $e) {
            Log::error('Lỗi cập nhật parent: '.$e->getMessage());
            return response()->json(['status'=>500,'message'=>'Cập nhật thất bại'],500);
        }
    }

    /* =======================================================
     * 4. Xoá CHA (kèm CON)
     * =======================================================
     */
    public function destroyParent(int $id): JsonResponse
    {
        try {
            $p = BienMucTruongChaModel::with('children')->findOrFail($id);
            $p->children()->delete();
            $p->delete();
            return response()->json(['status'=>200,'message'=>'Đã xoá trường cha']);
        } catch (\Exception $e) {
            Log::error('Lỗi xoá parent: '.$e->getMessage());
            return response()->json(['status'=>500,'message'=>'Xoá thất bại'],500);
        }
    }

    /* =======================================================
     * 5. Thêm CHA trống sau idx
     * =======================================================
     */
    public function addParentAfter(Request $request): JsonResponse
    {
        $data = $request->validate([
            'id_sach' => 'required|integer',
            'index'   => 'required|integer|min:0',
        ]);

        try {
            $bieuGhi = BienMucBieuGhiModel::where('id_sach', $data['id_sach'])
                ->firstOrFail();

            $new = BienMucTruongChaModel::create([
                'id_bien_muc_bieu_ghi' => $bieuGhi->id_bien_muc_bieu_ghi,
                'ma_truong'            => '',
            ]);

            return response()->json([
                'status'=>200,
                'message'=>'Đã thêm parent trống',
                'data'=>$new,
            ]);
        } catch (\Exception $e) {
            Log::error('Lỗi addParentAfter: '.$e->getMessage());
            return response()->json(['status'=>500,'message'=>'Thêm thất bại'],500);
        }
    }

    /* =======================================================
     * 6. Thêm CON
     * =======================================================
     */
    public function storeChild(Request $request): JsonResponse
    {
        $rules = [
            'parent_id'     => 'required|integer|exists:bien_muc_truong_cha,id_bien_muc_truong_cha',
            'ma_truong_con' => 'nullable|string|size:1|regex:/^[0-9a-z]$/i',
            'noi_dung'      => 'nullable|string',
        ];
        $messages = [
            'parent_id.required' => 'Thiếu parent_id.',
        ];
        $data = $request->validate($rules, $messages);

        /* dup check (kể cả rỗng) */
        $dup = BienMucTruongConModel::where('id_bien_muc_truong_cha',$data['parent_id'])
            ->where('ma_truong_con',$data['ma_truong_con'] ?? '')
            ->exists();
        if ($dup) {
            return response()->json([
                'status'=>422,
                'errors'=>['ma_truong_con'=>['Trùng mã con']],
            ],422);
        }

        try {
            $child = BienMucTruongConModel::create([
                'id_bien_muc_truong_cha' => $data['parent_id'],
                'ma_truong_con'          => $data['ma_truong_con'] ?? '',
                'noi_dung'               => $data['noi_dung']      ?? '',
            ]);

            return response()->json([
                'status'=>200,
                'message'=>'Đã thêm trường con',
                'data'=>$child,
            ]);
        } catch (\Exception $e) {
            Log::error('Lỗi storeChild: '.$e->getMessage());
            return response()->json(['status'=>500,'message'=>'Thêm thất bại'],500);
        }
    }

    /* =======================================================
     * 7. Cập nhật CON
     * =======================================================
     */
    public function updateChild(Request $request, int $id): JsonResponse
    {
        $rules = [
            'ma_truong_con' => 'required|string|size:1|regex:/^[0-9a-z]$/i',
            'noi_dung'      => 'nullable|string',
        ];
        $messages = [
            'ma_truong_con.required' => 'Mã trường con không được trống.',
        ];
        $data = $request->validate($rules, $messages);

        $child = BienMucTruongConModel::findOrFail($id);

        $dup = BienMucTruongConModel::where('id_bien_muc_truong_cha',$child->id_bien_muc_truong_cha)
            ->where('ma_truong_con',$data['ma_truong_con'])
            ->where('id_bien_muc_truong_con','!=',$id)
            ->exists();
        if ($dup) {
            return response()->json([
                'status'=>422,
                'errors'=>['ma_truong_con'=>['Trùng mã con']],
            ],422);
        }

        try {
            $child->update($data);
            return response()->json(['status'=>200,'message'=>'Cập nhật child thành công']);
        } catch (\Exception $e) {
            Log::error('Lỗi updateChild: '.$e->getMessage());
            return response()->json(['status'=>500,'message'=>'Cập nhật thất bại'],500);
        }
    }

    /* =======================================================
     * 8. Xoá CON
     * =======================================================
     */
    public function destroyChild(int $id): JsonResponse
    {
        try {
            BienMucTruongConModel::findOrFail($id)->delete();
            return response()->json(['status'=>200,'message'=>'Đã xoá trường con']);
        } catch (\Exception $e) {
            Log::error('Lỗi xoá child: '.$e->getMessage());
            return response()->json(['status'=>500,'message'=>'Xoá thất bại'],500);
        }
    }
}
