<?php

namespace App\Http\Controllers;

use App\Models\BienMucBieuGhiModel;
use App\Models\BienMucTruongChaModel;
use App\Models\BienMucTruongConModel;
use App\Models\DonNhanModel;
use App\Models\SachModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SachController extends Controller
{
    public function index(int $id_don_nhan, Request $request)
    {
        $perPage = $request->get('per_page', 10);
        $data = SachModel::getListByDonNhan($id_don_nhan, $perPage);

        return response()->json(['status' => 200, 'data' => $data]);
    }

    public function store(Request $request)
    {
        $validated = $this->validate($request, [
            'id_don_nhan' => 'required|integer',
            'nhan_de' => 'required',
            'tac_gia' => 'nullable',
            'nam_xuat_ban' => 'nullable|digits:4',
            'nha_xuat_ban' => 'nullable',
            'noi_xuat_ban' => 'nullable',
            'gia' => 'nullable|numeric|min:0',
            'so_luong' => 'nullable|integer|min:0',
        ], [
            'nhan_de.required' => 'Nhan đề không được để trống.',
            'nam_xuat_ban.digits' => 'Năm xuất bản phải gồm 4 chữ số.',
            'gia.numeric' => 'Giá tiền phải là số.',
            'so_luong.integer' => 'Số lượng phải là số.',
        ]);

        $validated['gia'] = $validated['gia'] ?? 0;
        $validated['so_luong'] = $validated['so_luong'] ?? 0;
        $validated['thanh_tien'] = $validated['gia'] * $validated['so_luong'];

        try {
            DB::transaction(function () use ($validated, &$sach) {
                $sach = SachModel::create($validated);
                $bmg = BienMucBieuGhiModel::create([
                    'id_sach' => $sach->id_sach,
                ]);

                $nguoiTao = optional(
                    DonNhanModel::find($validated['id_don_nhan'])
                )->nguoi_tao ?? '';

                $allParents = [
                    // Các trường dựa trên dữ liệu nhập
                    [
                        'ma_truong' => '245', 'ct1' => '0', 'ct2' => '#',
                        'children' => [
                            ['ma_truong_con' => 'a', 'noi_dung' => $validated['nhan_de']],
                            ['ma_truong_con' => 'c', 'noi_dung' => $validated['tac_gia'] ?? ''],
                            ['ma_truong_con' => 'b', 'noi_dung' => ''],
                            ['ma_truong_con' => 'n', 'noi_dung' => ''],
                            ['ma_truong_con' => 'p', 'noi_dung' => ''],
                        ],
                    ],
                    [
                        'ma_truong' => '260', 'ct1' => '#', 'ct2' => '#',
                        'children' => [
                            ['ma_truong_con' => 'a', 'noi_dung' => $validated['noi_xuat_ban'] ?? ''],
                            ['ma_truong_con' => 'b', 'noi_dung' => $validated['nha_xuat_ban'] ?? ''],
                            ['ma_truong_con' => 'c', 'noi_dung' => $validated['nam_xuat_ban'] ?? ''],
                        ],
                    ],
                    [
                        'ma_truong' => '904', 'ct1' => '#', 'ct2' => '#',
                        'children' => [
                            ['ma_truong_con' => 'i', 'noi_dung' => $nguoiTao],
                        ],
                    ],
                    [
                        'ma_truong' => '100', 'ct1' => '1', 'ct2' => '#',
                        'children' => [
                            ['ma_truong_con' => 'a', 'noi_dung' => $validated['tac_gia'] ?? ''],
                            ['ma_truong_con' => 'b', 'noi_dung' => ''],
                        ],
                    ],

                    // Các trường mặc định thêm vào (noi_dung rỗng)
                    [
                        'ma_truong' => '020', 'ct1' => '#', 'ct2' => '#',
                        'children' => [
                            ['ma_truong_con' => 'a', 'noi_dung' => ''],
                            ['ma_truong_con' => 'c', 'noi_dung' => ''],
                        ],
                    ],
                    [
                        'ma_truong' => '041', 'ct1' => '#', 'ct2' => '#',
                        'children' => [
                            ['ma_truong_con' => 'a', 'noi_dung' => ''],
                        ],
                    ],
                    [
                        'ma_truong' => '082', 'ct1' => '1', 'ct2' => '4',
                        'children' => [
                            ['ma_truong_con' => '2', 'noi_dung' => ''],
                            ['ma_truong_con' => 'a', 'noi_dung' => ''],
                            ['ma_truong_con' => 'b', 'noi_dung' => ''],
                        ],
                    ],
                    [
                        'ma_truong' => '300', 'ct1' => '#', 'ct2' => '#',
                        'children' => [
                            ['ma_truong_con' => 'a', 'noi_dung' => ''],
                            ['ma_truong_con' => 'c', 'noi_dung' => ''],
                            ['ma_truong_con' => 'e', 'noi_dung' => ''],
                        ],
                    ],
                    [
                        'ma_truong' => '520', 'ct1' => '#', 'ct2' => '#',
                        'children' => [
                            ['ma_truong_con' => 'a', 'noi_dung' => ''],
                        ],
                    ],
                    [
                        'ma_truong' => '530', 'ct1' => '#', 'ct2' => '#',
                        'children' => [
                            ['ma_truong_con' => 'a', 'noi_dung' => ''],
                        ],
                    ],
                    [
                        'ma_truong' => '650', 'ct1' => '#', 'ct2' => '#',
                        'children' => [
                            ['ma_truong_con' => 'a', 'noi_dung' => ''],
                        ],
                    ],
                    [
                        'ma_truong' => '653', 'ct1' => '#', 'ct2' => '#',
                        'children' => [
                            ['ma_truong_con' => 'a', 'noi_dung' => ''],
                        ],
                    ],
                    [
                        'ma_truong' => '700', 'ct1' => '0', 'ct2' => '#',
                        'children' => [
                            ['ma_truong_con' => 'a', 'noi_dung' => ''],
                        ],
                    ],
                ];


                foreach ($allParents as $p) {
                    $cha = BienMucTruongChaModel::create([
                        'id_bien_muc_bieu_ghi' => $bmg->id_bien_muc_bieu_ghi,
                        'ma_truong' => $p['ma_truong'],
                        'ct1' => $p['ct1'],
                        'ct2' => $p['ct2'],
                    ]);

                    foreach ($p['children'] as $c) {
                        BienMucTruongConModel::create([
                            'id_bien_muc_truong_cha' => $cha->id_bien_muc_truong_cha,
                            'ma_truong_con' => $c['ma_truong_con'],
                            'noi_dung' => $c['noi_dung'],
                        ]);
                    }
                }
            });

            return response()->json([
                'status' => 200,
                'message' => 'Thêm thành công',
                'data' => $sach,
            ]);

        } catch (\Throwable $e) {
            // Log::error($e);
            return response()->json([
                'status' => 500,
                'message' => 'Thêm thất bại',
            ], 500);
        }
    }

    public function update(Request $request, int $id)
    {
        $rules = [
            'nhan_de' => 'required',
            'tac_gia' => 'nullable|string|max:255',
            'nam_xuat_ban' => 'nullable|digits:4',
            'nha_xuat_ban' => 'nullable|string',
            'noi_xuat_ban' => 'nullable|string',
            'gia' => 'nullable|numeric|min:0',
            'so_luong' => 'nullable|integer|min:0',
        ];

        $messages = [
            'nhan_de.required' => 'Nhan đề không được để trống.',
            'nam_xuat_ban.digits' => 'Năm xuất bản phải là năm có 4 chữ số.',
            'gia.numeric' => 'Giá tiền phải là số.',
            'so_luong.integer' => 'Số lượng phải là số.',
        ];

        $validated = $request->validate($rules, $messages);
        $validated['gia'] = $validated['gia'] ?? 0;
        $validated['so_luong'] = $validated['so_luong'] ?? 0;
        $validated['thanh_tien'] = $validated['gia'] * $validated['so_luong'];

        $sach = SachModel::findOrFail($id);
        $sach->update($validated);

        return response()->json(['status' => 200, 'message' => 'Cập nhật thành công']);
    }

    public function destroy(int $id)
    {
        SachModel::destroy($id);
        return response()->json(['status' => 200, 'message' => 'Xoá thành công']);
    }
}
