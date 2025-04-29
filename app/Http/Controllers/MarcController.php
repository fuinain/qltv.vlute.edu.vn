<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
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
//    public function updateChild(Request $request, int $id): JsonResponse
//    {
//        $rules = [
//            'ma_truong_con' => 'required|string|size:1|regex:/^[0-9a-z]$/i',
//            'noi_dung'      => 'nullable|string',
//        ];
//        $messages = [
//            'ma_truong_con.required' => 'Mã trường con không được trống.',
//        ];
//        $data = $request->validate($rules, $messages);
//
//        $child = BienMucTruongConModel::findOrFail($id);
//
//        $dup = BienMucTruongConModel::where('id_bien_muc_truong_cha',$child->id_bien_muc_truong_cha)
//            ->where('ma_truong_con',$data['ma_truong_con'])
//            ->where('id_bien_muc_truong_con','!=',$id)
//            ->exists();
//        if ($dup) {
//            return response()->json([
//                'status'=>422,
//                'errors'=>['ma_truong_con'=>['Trùng mã con']],
//            ],422);
//        }
//
//        try {
//            $child->update($data);
//            return response()->json(['status'=>200,'message'=>'Cập nhật child thành công']);
//        } catch (\Exception $e) {
//            Log::error('Lỗi updateChild: '.$e->getMessage());
//            return response()->json(['status'=>500,'message'=>'Cập nhật thất bại'],500);
//        }
//    }

    public function updateChild(Request $request, int $id): JsonResponse
    {
        $rules = [
            'ma_truong_con' => 'nullable|string|max:1|regex:/^[0-9a-z]?$/i',
            'noi_dung'      => 'nullable|string',
        ];
        $messages = [
            'ma_truong_con.size' => 'Mã trường con chỉ được 1 ký tự.',
            'ma_truong_con.regex' => 'Mã trường con chỉ chứa chữ cái hoặc số.',
        ];
        $data = $request->validate($rules, $messages);
        $data['ma_truong_con'] = $data['ma_truong_con'] ?? '';
        $data['noi_dung'] = $data['noi_dung'] ?? '';

        $child = BienMucTruongConModel::with('parent.bieuGhi.sach')->findOrFail($id); // Tải sẵn các quan hệ cần thiết

        // Kiểm tra trùng lặp (kể cả rỗng)
        if (isset($data['ma_truong_con'])) { // Chỉ kiểm tra nếu mã trường con được cung cấp
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
        }


        try {
            DB::transaction(function() use ($child, $data) {
                $child->update($data);
                $parent = $child->parent;
                $sach = $parent?->bieuGhi?->sach; // Sử dụng optional chaining

                if ($parent && $sach) {
                    $updatedSachData = [];
                    $ma_truong_cha = $parent->ma_truong;
                    $ma_truong_con = $child->ma_truong_con; // Lấy mã con đã được cập nhật
                    $noi_dung = $child->noi_dung;

                    switch ($ma_truong_cha) {
                        case '100':
                            if ($ma_truong_con === 'a') {
                                $updatedSachData['tac_gia'] = $noi_dung;
                                // Cũng cần cập nhật 245$c nếu 100$a thay đổi
                                $this->updateRelatedMarcField($parent->bieuGhi->id_bien_muc_bieu_ghi, '245', 'c', $noi_dung);
                            }
                            break;
                        case '245':
                            if ($ma_truong_con === 'a') {
                                $updatedSachData['nhan_de'] = $noi_dung;
                            } elseif ($ma_truong_con === 'c') {
                                $updatedSachData['tac_gia'] = $noi_dung;
                                // Cũng cần cập nhật 100$a nếu 245$c thay đổi
                                $this->updateRelatedMarcField($parent->bieuGhi->id_bien_muc_bieu_ghi, '100', 'a', $noi_dung);
                            }
                            break;
                        case '260':
                            if ($ma_truong_con === 'a') {
                                $updatedSachData['noi_xuat_ban'] = $noi_dung;
                            } elseif ($ma_truong_con === 'b') {
                                $updatedSachData['nha_xuat_ban'] = $noi_dung;
                            } elseif ($ma_truong_con === 'c') {
                                $updatedSachData['nam_xuat_ban'] = $noi_dung;
                            }
                            break;
                    }

                    if (!empty($updatedSachData)) {
                        $sach->update($updatedSachData);
                    }
                } else {
                    Log::warning("Không tìm thấy Sách hoặc Trường cha liên kết với trường con ID: {$child->id_bien_muc_truong_con} khi cập nhật.");
                }
            });

            return response()->json(['status'=>200,'message'=>'Cập nhật thành công']);

        } catch (\Exception $e) {
            Log::error('Lỗi updateChild hoặc đồng bộ ngược về Sách (ID con: {$id}): '.$e->getMessage());
            return response()->json(['status'=>500,'message'=>'Cập nhật thất bại'],500);
        }
    }

    /* =======================================================
     * 8. Xoá CON
     * =======================================================
     */
    public function destroyChild(int $id): JsonResponse
    {
        // Khi xóa trường con, cũng cần kiểm tra xem có cần cập nhật sách không
        $child = BienMucTruongConModel::with('parent.bieuGhi.sach')->find($id);

        if (!$child) {
            return response()->json(['status'=>404, 'message'=>'Không tìm thấy trường con'], 404);
        }

        $parent = $child->parent;
        $sach = $parent?->bieuGhi?->sach;
        $ma_truong_cha = $parent?->ma_truong;
        $ma_truong_con = $child->ma_truong_con;

        try {
            DB::transaction(function() use ($child, $sach, $ma_truong_cha, $ma_truong_con, $parent) {
                // 1. Xóa trường con
                $child->delete();

                // 2. Cập nhật sách nếu trường con bị xóa thuộc nhóm ánh xạ
                if ($sach && $ma_truong_cha) {
                    $updatedSachData = [];
                    switch ($ma_truong_cha) {
                        case '100':
                            if ($ma_truong_con === 'a') {
                                $updatedSachData['tac_gia'] = null; // Hoặc '' tùy vào logic
                                // Kiểm tra xem 245$c còn tồn tại không, nếu không thì không cần cập nhật
                            }
                            break;
                        case '245':
                            if ($ma_truong_con === 'a') {
                                $updatedSachData['nhan_de'] = null; // Hoặc ''
                            } elseif ($ma_truong_con === 'c') {
                                // Nếu xóa 245$c, chỉ cập nhật tac_gia = null nếu 100$a cũng không tồn tại hoặc rỗng
                                $otherTacGia = $parent?->bieuGhi?->parents()
                                    ->where('ma_truong', '100')
                                    ->first()?->children()
                                    ->where('ma_truong_con', 'a')
                                    ->value('noi_dung');
                                if (empty($otherTacGia)) {
                                    $updatedSachData['tac_gia'] = null;
                                }
                            }
                            break;
                        case '260':
                            if ($ma_truong_con === 'a') {
                                $updatedSachData['noi_xuat_ban'] = null;
                            } elseif ($ma_truong_con === 'b') {
                                $updatedSachData['nha_xuat_ban'] = null;
                            } elseif ($ma_truong_con === 'c') {
                                $updatedSachData['nam_xuat_ban'] = null;
                            }
                            break;
                    }

                    if (!empty($updatedSachData)) {
                        $sach->update($updatedSachData);
                    }
                }
            });

            return response()->json(['status'=>200,'message'=>'Đã xoá trường con']);
        } catch (\Exception $e) {
            Log::error('Lỗi xoá child hoặc cập nhật sách (ID con: {$id}): '.$e->getMessage());
            return response()->json(['status'=>500,'message'=>'Xoá thất bại'],500);
        }
    }

    private function updateRelatedMarcField(int $id_bieu_ghi, string $ma_truong, string $ma_truong_con, ?string $noi_dung): void
    {
        try {
            $parent = BienMucTruongChaModel::firstWhere([
                'id_bien_muc_bieu_ghi' => $id_bieu_ghi,
                'ma_truong' => $ma_truong,
            ]);

            if ($parent) {
                BienMucTruongConModel::updateOrCreate(
                    [
                        'id_bien_muc_truong_cha' => $parent->id_bien_muc_truong_cha,
                        'ma_truong_con' => $ma_truong_con,
                    ],
                    [
                        'noi_dung' => $noi_dung ?? '',
                    ]
                );
            } else {
                Log::warning("Không tìm thấy trường cha liên quan {$ma_truong} cho biểu ghi ID: {$id_bieu_ghi} khi cập nhật trường con {$ma_truong_con}.");
            }
        } catch (\Exception $e) {
            Log::error("Lỗi cập nhật MARC field liên quan {$ma_truong}{$ma_truong_con} cho biểu ghi {$id_bieu_ghi}: " . $e->getMessage());
        }
    }
}
