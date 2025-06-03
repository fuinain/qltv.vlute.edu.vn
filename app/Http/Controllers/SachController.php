<?php

namespace App\Http\Controllers;

use App\Models\BienMucBieuGhiModel;
use App\Models\BienMucTruongChaModel;
use App\Models\BienMucTruongConModel;
use App\Models\DonNhanModel;
use App\Models\SachModel;
use App\Models\DKCBModel;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\RichText\RichText;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Throwable;

class SachController extends Controller
{
    public function index(int $id_don_nhan, Request $request)
    {
        $perPage = $request->get('per_page', 10);
        $search = $request->get('search', '');
        
        $query = SachModel::where('id_don_nhan', $id_don_nhan);
        
        // Thêm điều kiện tìm kiếm nếu có
        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('nhan_de', 'like', '%' . $search . '%')
                  ->orWhere('tac_gia', 'like', '%' . $search . '%')
                  ->orWhere('nha_xuat_ban', 'like', '%' . $search . '%')
                  ->orWhere('noi_xuat_ban', 'like', '%' . $search . '%')
                  ->orWhere('nam_xuat_ban', 'like', '%' . $search . '%');
            });
        }
        
        $data = $query->orderBy('ngay_tao', 'desc')->paginate($perPage);

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
        try {
            DB::transaction(function () use ($sach, $validated, $id) {
                // 1. Cập nhật sách
                $sach->update($validated);

                // 2. Cập nhật biên mục tương ứng
                $bieuGhi = BienMucBieuGhiModel::where('id_sach', $id)->first();

                if ($bieuGhi) {
                    $id_bieu_ghi = $bieuGhi->id_bien_muc_bieu_ghi;

                    $this->updateMarcField($id_bieu_ghi, '245', 'a', $validated['nhan_de']);
                    $this->updateMarcField($id_bieu_ghi, '100', 'a', $validated['tac_gia'] ?? null);
                    $this->updateMarcField($id_bieu_ghi, '245', 'c', $validated['tac_gia'] ?? null);
                    $this->updateMarcField($id_bieu_ghi, '260', 'a', $validated['noi_xuat_ban'] ?? null);
                    $this->updateMarcField($id_bieu_ghi, '260', 'b', $validated['nha_xuat_ban'] ?? null);
                    $this->updateMarcField($id_bieu_ghi, '260', 'c', $validated['nam_xuat_ban'] ?? null);

                } else {
                    Log::warning("Không tìm thấy biểu ghi cho sách ID: {$id} khi cập nhật.");
                }
            });

            return response()->json(['status' => 200, 'message' => 'Cập nhật thành công']);

        } catch (\Throwable $e) {
            Log::error("Lỗi cập nhật sách hoặc biên mục (ID: {$id}): " . $e->getMessage());
            return response()->json(['status' => 500, 'message' => 'Cập nhật thất bại. Có lỗi xảy ra.'], 500);
        }
    }

    public function destroy(int $id)
    {
        try {
            DB::transaction(function () use ($id) {
                $bieuGhi = BienMucBieuGhiModel::where('id_sach', $id)->first();
                if ($bieuGhi) {
                    $parents = BienMucTruongChaModel::where('id_bien_muc_bieu_ghi', $bieuGhi->id_bien_muc_bieu_ghi)->get();
                    foreach ($parents as $parent) {
                        $parent->children()->delete();
                        $parent->delete();
                    }
                    $bieuGhi->delete();
                }
                SachModel::destroy($id);
            });
            return response()->json(['status' => 200, 'message' => 'Xoá thành công']);
        } catch (\Exception $e) {
            Log::error("Lỗi xóa sách (ID: {$id}): " . $e->getMessage());
            return response()->json(['status' => 500, 'message' => 'Xoá thất bại'], 500);
        }
    }

    private function updateMarcField(int $id_bieu_ghi, string $ma_truong, string $ma_truong_con, ?string $noi_dung): void
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
                        'noi_dung' => $noi_dung ?? '', // Đảm bảo không lưu null vào db nếu cột không cho phép
                    ]
                );
            } else {
                Log::warning("Không tìm thấy trường cha {$ma_truong} cho biểu ghi ID: {$id_bieu_ghi} khi cập nhật trường con {$ma_truong_con}.");
            }
        } catch (\Exception $e) {
            Log::error("Lỗi cập nhật MARC field {$ma_truong}{$ma_truong_con} cho biểu ghi {$id_bieu_ghi}: " . $e->getMessage());
        }
    }

    public function exportExcelDonNhan(int $id_don_nhan)
    {
        try {
            $donNhan = DonNhanModel::getDataDonNhanForExport($id_don_nhan);
            $sachList = $donNhan->sach;
            $tongSoTen = $sachList->count();
            $tongSoBan = $sachList->sum('so_luong');
            $tongTriGia = $sachList->sum(function ($sach) {
                $gia = is_numeric($sach->gia) ? $sach->gia : 0;
                $so_luong = is_numeric($sach->so_luong) ? $sach->so_luong : 0;
                return $gia * $so_luong;
            });

            $templatePath = public_path('template_excel/template_excel_in_don_nhan.xlsx');
            if (!file_exists($templatePath)) {
                return response()->json(['status' => 500, 'message' => 'Lỗi server: Không tìm thấy file mẫu.'], 500);
            }
            $spreadsheet = IOFactory::load($templatePath);
            $sheet = $spreadsheet->getActiveSheet();

            $sheet->setCellValue('A5', setValueInCell('DANH MỤC SÁCH ĐƠN NHẬN SỐ: ', $donNhan->id_don_nhan));
            $sheet->setCellValue('B6', $donNhan->id_don_nhan);
            $sheet->setCellValue('B7', $donNhan->ten_don_nhan);
            $sheet->setCellValue('B8', $donNhan->nhaCungCap->ten_nha_cung_cap ?? '');
            $sheet->setCellValue('C6', setValueInCell('Ngày nhận: ', Carbon::parse($donNhan->ngay_nhan)->format('d-m-Y')));
            $sheet->setCellValue('B9', setValueInCell('Tổng số nhãn: ', $tongSoTen));
            $sheet->setCellValue('D9', setValueInCell('Tổng số quyển: ', $tongSoBan));
            $sheet->setCellValue('I9', $tongTriGia);
            $sheet->getStyle('I9')->getNumberFormat()->setFormatCode('#,##0');
            $sheet->setCellValue('H6', setValueInCell('Trạng thái: ',$donNhan->trangThaiDon->trang_thai ?? ''));
            $sheet->setCellValue('H7', setValueInCell('Nguồn nhận: ',$donNhan->nguonNhan->ten_nguon ?? ''));
            $sheet->setCellValue('H8', setValueInCell('Ghi chú: ',$donNhan->ghi_chu ?? ''));

            $startRow = 11;
            $stt = 1;
            $currentRow = $startRow - 1;
            $templateStyle = [];
            for ($col = 'A'; $col <= 'O'; $col++) {
                $templateStyle[$col] = $sheet->getStyle($col . $startRow);
            }
            $templateRowHeight = $sheet->getRowDimension($startRow)->getRowHeight();

            $borderStyle = [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['argb' => 'FF000000'],
                    ],
                ],
            ];
            if ($sachList->count() > 0) {
                foreach ($sachList as $sach) {
                    $currentRow = $startRow + $stt - 1;

                    if ($stt > 1) {
                        $sheet->insertNewRowBefore($currentRow, 1);
                        for ($col = 'A'; $col <= 'O'; $col++) {
                            $sheet->duplicateStyle($templateStyle[$col], $col . $currentRow);
                        }
                        $sheet->getRowDimension($currentRow)->setRowHeight($templateRowHeight);
                    }

                    $phanLoai = '';
                    $tenTaiLieu = '';
                    $bieuGhi = $sach->bienMucBieuGhi;
                    if ($bieuGhi) {
                        $tenTaiLieu = $bieuGhi->taiLieu->ten_tai_lieu ?? '';
                        $truong082 = $bieuGhi->truongCha
                            ->where('ma_truong', '082')
                            ->where('ct1', '1')
                            ->where('ct2', '4')
                            ->first();
                        if ($truong082) {
                            $children = $truong082->children;
                            $con_a = $children->where('ma_truong_con', 'a')->first();
                            $con_b = $children->where('ma_truong_con', 'b')->first();
                            $phanLoaiArr = [];
                            if ($con_a && !empty($con_a->noi_dung)) $phanLoaiArr[] = $con_a->noi_dung;
                            if ($con_b && !empty($con_b->noi_dung)) $phanLoaiArr[] = $con_b->noi_dung;
                            $phanLoai = implode('/', $phanLoaiArr);
                        }
                    }

                    // Điền dữ liệu sách (giữ nguyên)
                    $sheet->setCellValue('A' . $currentRow, $stt);
                    $sheet->setCellValue('B' . $currentRow, $sach->id_sach);
                    $sheet->setCellValue('C' . $currentRow, $sach->nhan_de);
                    $sheet->mergeCells('C' . $currentRow . ':E' . $currentRow);
                    $sheet->getStyle('C' . $currentRow)->getAlignment()->setVertical(Alignment::VERTICAL_TOP)->setWrapText(true);
                    $sheet->setCellValue('F' . $currentRow, $sach->tac_gia);
                    $sheet->mergeCells('F' . $currentRow . ':G' . $currentRow);
                    $sheet->getStyle('F' . $currentRow)->getAlignment()->setVertical(Alignment::VERTICAL_TOP)->setWrapText(true);
                    $sheet->setCellValue('H' . $currentRow, $phanLoai);
                    $sheet->setCellValue('I' . $currentRow, $sach->nam_xuat_ban);
                    $sheet->setCellValue('J' . $currentRow, $sach->nha_xuat_ban);
                    $sheet->setCellValue('K' . $currentRow, $sach->noi_xuat_ban);
                    $sheet->setCellValue('L' . $currentRow, $tenTaiLieu);
                    $sheet->setCellValue('M' . $currentRow, $sach->gia);
                    $sheet->getStyle('M' . $currentRow)->getNumberFormat()->setFormatCode('#,##0');
                    $sheet->setCellValue('N' . $currentRow, $sach->so_luong);
                    $sheet->setCellValue('O' . $currentRow, $sach->thanh_tien); // Hoặc $sach->gia * $sach->so_luong
                    $sheet->getStyle('O' . $currentRow)->getNumberFormat()->setFormatCode('#,##0');
                    $sheet->getStyle('A' . $currentRow . ':O' . $currentRow)->applyFromArray($borderStyle);

                    $stt++;
                }
            } else {
                $sheet->removeRow($startRow, 1);
            }

            // --- Thêm dòng Tổng cộng và Chữ ký (Giữ nguyên phần logic điền dữ liệu) ---
            $totalRow = $currentRow + 1;
            $dateRow = $currentRow + 3;
            $titleRow = $currentRow + 4;
            $nameRow = $currentRow + 5;

            $sheet->mergeCells('A' . $totalRow . ':B' . $totalRow)->setCellValue('A' . $totalRow, 'Tổng cộng');
            $sheet->getStyle('A' . $totalRow)->getFont()->setBold(true);
            $sheet->getStyle('A' . $totalRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
            $sheet->mergeCells('C' . $totalRow . ':M' . $totalRow)->setCellValue('C' . $totalRow, $tongSoTen);
            $sheet->getStyle('C' . $totalRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT)->setVertical(Alignment::VERTICAL_CENTER);
            $sheet->getStyle('C' . $totalRow)->getFont()->setBold(true);
            $sheet->setCellValue('N' . $totalRow, $tongSoBan);
            $sheet->getStyle('N' . $totalRow)->getFont()->setBold(true);
            $sheet->getStyle('N' . $totalRow)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
            $sheet->setCellValue('O' . $totalRow, $tongTriGia);
            $sheet->getStyle('O' . $totalRow)->getNumberFormat()->setFormatCode('#,##0');
            $sheet->getStyle('O' . $totalRow)->getFont()->setBold(true);
            $sheet->getStyle('O' . $totalRow)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
            $sheet->getStyle('A' . $totalRow . ':O' . $totalRow)->applyFromArray($borderStyle);

            //Dòng Ngày tháng năm (giữ nguyên)
            $now = Carbon::now('Asia/Ho_Chi_Minh'); // Lấy giờ VN
            $chuoiNgayThang = "Vĩnh Long, ngày " . $now->day . " tháng " . $now->month . " năm " . $now->year;
            $sheet->mergeCells('I' . $dateRow . ':O' . $dateRow)->setCellValue('I' . $dateRow, $chuoiNgayThang);
            $sheet->getStyle('I' . $dateRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT)->setVertical(Alignment::VERTICAL_CENTER);

            //Dòng Người lập báo cáo (giữ nguyên)
            $sheet->mergeCells('I' . $titleRow . ':O' . $titleRow)->setCellValue('I' . $titleRow, 'Người lập báo cáo');
            $sheet->getStyle('I' . $titleRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT)->setVertical(Alignment::VERTICAL_TOP);
            $sheet->getStyle('I' . $titleRow)->getFont()->setBold(true);
            $sheet->getRowDimension($titleRow)->setRowHeight(60);

            //Dòng Tên người tạo (giữ nguyên)
            $sheet->mergeCells('I' . $nameRow . ':O' . $nameRow)->setCellValue('I' . $nameRow, $donNhan->nguoi_tao ?? '');
            $sheet->getStyle('I' . $nameRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT)->setVertical(Alignment::VERTICAL_CENTER);

            $writer = new Xlsx($spreadsheet);
            $fileName = 'Don_Nhan_' . $donNhan->id_don_nhan . '_' . date('dmY') . '.xlsx';
            $tempFilePath = tempnam(sys_get_temp_dir(), 'excel_don_nhan_');
            $writer->save($tempFilePath);
            return response()->download($tempFilePath, $fileName, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ])->deleteFileAfterSend(true);

        } catch (ModelNotFoundException $e) {
            return response()->json(['status' => 404, 'message' => 'Không tìm thấy đơn nhận.'], 404);
        } catch (Throwable $e) {
            return response()->json(['status' => 500, 'message' => 'Có lỗi xảy ra trong quá trình xuất file Excel.'], 500);
        }
    }

    public function exportExcelThongKeTaiLieu(int $id_don_nhan)
    {
        try {
            $exportData = DonNhanModel::getDataThongKeTaiLieuForExport($id_don_nhan);

            if (!$exportData) {
                return response()->json(['status' => 404, 'message' => 'Không tìm thấy đơn nhận với ID cung cấp.'], 404);
            }

            $donNhan = $exportData->donNhan;
            $thongKeData = $exportData->thongKeData;
            $tongSoTenChung = $exportData->tongSoTenChung;
            $tongSoBanChung = $exportData->tongSoBanChung;
            $tongTriGiaChung = $exportData->tongTriGiaChung;

            $templatePath = public_path('template_excel/template_excel_thong_ke_tai_lieu.xlsx');
            if (!file_exists($templatePath)) {
                return response()->json(['status' => 500, 'message' => 'Lỗi server: Không tìm thấy file mẫu thống kê.'], 500);
            }
            $spreadsheet = IOFactory::load($templatePath);
            $sheet = $spreadsheet->getActiveSheet();

            // --- Điền Header ---
            $sheet->setCellValue('A5', setValueInCell('THỐNG KÊ TÀI LIỆU ĐƠN NHẬN SỐ: ', $donNhan->id_don_nhan));
            $sheet->setCellValue('B6', $donNhan->id_don_nhan);
            $sheet->setCellValue('B7', $donNhan->ten_don_nhan);
            $sheet->setCellValue('B8', optional($donNhan->nhaCungCap)->ten_nha_cung_cap ?? '');
            $sheet->setCellValue('C6', setValueInCell('Ngày nhận: ', Carbon::parse($donNhan->ngay_nhan)->format('d-m-Y')));
            $sheet->setCellValue('H6', setValueInCell('Trạng thái: ', optional($donNhan->trangThaiDon)->trang_thai ?? ''));
            $sheet->setCellValue('H7', setValueInCell('Nguồn nhận: ', optional($donNhan->nguonNhan)->ten_nguon ?? ''));
            $sheet->setCellValue('H8', setValueInCell('Ghi chú: ', $donNhan->ghi_chu ?? ''));
            $sheet->setCellValue('B9', setValueInCell('Tổng số loại TL: ', count($thongKeData)));
            $sheet->setCellValue('D9', setValueInCell('Tổng số bản: ', $tongSoBanChung));
            $sheet->setCellValue('I9', $tongTriGiaChung);
            $sheet->getStyle('I9')->getNumberFormat()->setFormatCode('#,##0');

            // --- Chuẩn bị Style và Vị trí bắt đầu ---
            $startRow = 11;
            $currentRow = $startRow - 1;
            $templateStyle = [];

            for ($col = 'A'; $col <= 'O'; $col++) {
                $columnLetter = $col;
                $templateStyle[$columnLetter] = $sheet->getStyle($columnLetter . $startRow);
            }
            $templateRowHeight = $sheet->getRowDimension($startRow)->getRowHeight();

            $borderStyle = [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['argb' => 'FF000000'],
                    ],
                ],
            ];

            // --- Điền Dữ liệu Thống kê vào các dòng ---
            if (count($thongKeData) > 0) {
                $stt = 1;
                foreach ($thongKeData as $item) {
                    $currentRow = $startRow + $stt - 1;

                    if ($stt > 1) {
                        $sheet->insertNewRowBefore($currentRow, 1);
                        for ($col = 'A'; $col <= 'O'; $col++) {
                            $columnLetter = $col;
                            if (isset($templateStyle[$columnLetter])) {
                                $sheet->duplicateStyle($templateStyle[$columnLetter], $columnLetter . $currentRow);
                            }
                        }
                        if ($templateRowHeight > 0) {
                            $sheet->getRowDimension($currentRow)->setRowHeight($templateRowHeight);
                        }
                    }

                    // Điền dữ liệu
                    $sheet->setCellValue('A' . $currentRow, $stt);
                    $sheet->mergeCells('B' . $currentRow . ':E' . $currentRow);
                    $sheet->setCellValue('B' . $currentRow, $item['ten_tai_lieu']);
                    $sheet->getStyle('B' . $currentRow)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER)->setHorizontal(Alignment::HORIZONTAL_LEFT)->setWrapText(true);
                    $sheet->mergeCells('F' . $currentRow . ':I' . $currentRow);
                    $sheet->setCellValue('F' . $currentRow, $item['so_ten']);
                    $sheet->getStyle('F' . $currentRow)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER)->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $sheet->mergeCells('J' . $currentRow . ':M' . $currentRow);
                    $sheet->setCellValue('J' . $currentRow, $item['so_ban']);
                    $sheet->getStyle('J' . $currentRow)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER)->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $sheet->mergeCells('N' . $currentRow . ':O' . $currentRow);
                    $sheet->setCellValue('N' . $currentRow, $item['tri_gia']);
                    $sheet->getStyle('N' . $currentRow)->getNumberFormat()->setFormatCode('#,##0');
                    $sheet->getStyle('N' . $currentRow)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER)->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                    $sheet->getStyle('A' . $currentRow . ':O' . $currentRow)->applyFromArray($borderStyle);

                    $stt++;
                }
            } else {
                $sheet->removeRow($startRow, 1);
                $currentRow = $startRow - 1;
            }

            // --- Thêm dòng Tổng cộng Chung và Chữ ký ---
            $totalRow = $currentRow + 1;
            $dateRow = $totalRow + 3;
            $titleRow = $dateRow + 1;
            $nameSignatureRow = $titleRow + 4;

            // Dòng tổng cộng chung
            $sheet->mergeCells('A' . $totalRow . ':E' . $totalRow)->setCellValue('A' . $totalRow, 'Tổng cộng');
            $sheet->getStyle('A' . $totalRow)->getFont()->setBold(true);
            $sheet->getStyle('A' . $totalRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
            $sheet->mergeCells('F' . $totalRow . ':I' . $totalRow)->setCellValue('F' . $totalRow, $tongSoTenChung);
            $sheet->getStyle('F' . $totalRow)->getFont()->setBold(true);
            $sheet->getStyle('F' . $totalRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
            $sheet->mergeCells('J' . $totalRow . ':M' . $totalRow)->setCellValue('J' . $totalRow, $tongSoBanChung);
            $sheet->getStyle('J' . $totalRow)->getFont()->setBold(true);
            $sheet->getStyle('J' . $totalRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
            $sheet->mergeCells('N' . $totalRow . ':O' . $totalRow)->setCellValue('N' . $totalRow, $tongTriGiaChung);
            $sheet->getStyle('N' . $totalRow)->getNumberFormat()->setFormatCode('#,##0');
            $sheet->getStyle('N' . $totalRow)->getFont()->setBold(true);
            $sheet->getStyle('N' . $totalRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT)->setVertical(Alignment::VERTICAL_CENTER);
            $sheet->getStyle('A' . $totalRow . ':O' . $totalRow)->applyFromArray($borderStyle);

            // Phần Chữ ký (Merge từ J đến O)
            $now = Carbon::now('Asia/Ho_Chi_Minh');
            $currentLocation = config('app.location_for_report', 'Vĩnh Long');
            $chuoiNgayThang = $currentLocation . ", ngày " . $now->day . " tháng " . $now->month . " năm " . $now->year;
            $sheet->mergeCells('J' . $dateRow . ':O' . $dateRow)->setCellValue('J' . $dateRow, $chuoiNgayThang);
            $sheet->getStyle('J' . $dateRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT)->setVertical(Alignment::VERTICAL_CENTER);
            $sheet->getStyle('J' . $dateRow)->getFont()->setItalic(true);

            $sheet->mergeCells('J' . $titleRow . ':O' . $titleRow)->setCellValue('J' . $titleRow, 'Người lập báo cáo');
            $sheet->getStyle('J' . $titleRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT)->setVertical(Alignment::VERTICAL_TOP);
            $sheet->getStyle('J' . $titleRow)->getFont()->setBold(true);
            if ($sheet->getRowDimension($titleRow)->getRowHeight() < 20) {
                $sheet->getRowDimension($titleRow)->setRowHeight(20);
            }

            $sheet->mergeCells('J' . $nameSignatureRow . ':O' . $nameSignatureRow)->setCellValue('J' . $nameSignatureRow, $donNhan->nguoi_tao ?? '');
            $sheet->getStyle('J' . $nameSignatureRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT)->setVertical(Alignment::VERTICAL_CENTER);
            $sheet->getStyle('J' . $nameSignatureRow)->getFont()->setBold(true);

            // 4. Chuẩn bị và Xuất file Excel
            $writer = new Xlsx($spreadsheet);
            $fileName = 'Thong_Ke_Loai_TL_' . $donNhan->id_don_nhan . '_' . Carbon::now()->format('dmY') . '.xlsx';
            $tempFilePath = tempnam(sys_get_temp_dir(), 'excel_thongke_tl_');
            $writer->save($tempFilePath);

            return response()->download($tempFilePath, $fileName, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ])->deleteFileAfterSend(true);

        } catch (ModelNotFoundException $e) {
            return response()->json(['status' => 404, 'message' => 'Không tìm thấy đơn nhận với ID cung cấp.'], 404);
        } catch (Throwable $e) {
            return response()->json(['status' => 500, 'message' => 'Có lỗi không xác định xảy ra trong quá trình xuất file. Liên hệ quản trị viên.'], 500);
        }
    }

    /**
     * Lấy thông tin chi tiết sách
     * 
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        try {
            $sach = SachModel::findOrFail($id);
            
            return response()->json([
                'status' => 200,
                'data' => $sach
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => 404,
                'message' => 'Không tìm thấy sách'
            ], 404);
        } catch (\Exception $e) {
            Log::error('Lỗi khi lấy chi tiết sách: ' . $e->getMessage());
            
            return response()->json([
                'status' => 500,
                'message' => 'Đã xảy ra lỗi khi lấy chi tiết sách'
            ], 500);
        }
    }

    /**
     * Tìm số DKCB khả dụng
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function timDKCB(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'id_kho_an_pham' => 'required|exists:kho_an_pham,id_kho_an_pham',
            'so_bat_dau' => 'required|integer|min:1',
            'so_luong' => 'required|integer|min:1|max:100',
        ], [
            'id_kho_an_pham.required' => 'Vui lòng chọn kho ấn phẩm',
            'id_kho_an_pham.exists' => 'Kho ấn phẩm không tồn tại',
            'so_bat_dau.required' => 'Vui lòng nhập số bắt đầu',
            'so_bat_dau.integer' => 'Số bắt đầu phải là số nguyên',
            'so_bat_dau.min' => 'Số bắt đầu phải lớn hơn 0',
            'so_luong.required' => 'Vui lòng nhập số lượng',
            'so_luong.integer' => 'Số lượng phải là số nguyên',
            'so_luong.min' => 'Số lượng phải lớn hơn 0',
            'so_luong.max' => 'Số lượng tối đa là 100 nhãn mỗi lần',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $validator->errors()
            ], 422);
        }
        
        try {
            $id_kho_an_pham = $request->id_kho_an_pham;
            $so_bat_dau = $request->so_bat_dau;
            $so_luong = $request->so_luong;
            
            // Sử dụng phương thức timDKCBKhaDung từ DKCBModel
            $dkcbList = \App\Models\DKCBModel::timDKCBKhaDung($id_kho_an_pham, $so_bat_dau, $so_luong);
            
            if ($dkcbList->isEmpty()) {
                return response()->json([
                    'status' => 404,
                    'message' => 'Không tìm thấy số DKCB khả dụng. Vui lòng thử lại với kho hoặc số bắt đầu khác.'
                ]);
            }
            
            return response()->json([
                'status' => 200,
                'data' => $dkcbList,
                'message' => 'Tìm thấy ' . $dkcbList->count() . ' số DKCB khả dụng'
            ]);
        } catch (\Exception $e) {
            Log::error('Lỗi khi tìm DKCB: ' . $e->getMessage());
            
            return response()->json([
                'status' => 500,
                'message' => 'Đã xảy ra lỗi khi tìm số DKCB'
            ], 500);
        }
    }
    
    /**
     * Gán số DKCB cho sách
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function ganDKCB(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'id_sach' => 'required|exists:sach,id_sach',
            'ma_dkcb' => 'required|string',
            'so_luong' => 'required|integer|min:1|max:100',
            'auto_assign' => 'boolean',
        ], [
            'id_sach.required' => 'Thiếu thông tin sách',
            'id_sach.exists' => 'Sách không tồn tại',
            'ma_dkcb.required' => 'Vui lòng nhập mã DKCB',
            'so_luong.required' => 'Vui lòng nhập số lượng',
            'so_luong.integer' => 'Số lượng phải là số nguyên',
            'so_luong.min' => 'Số lượng phải lớn hơn 0',
            'so_luong.max' => 'Số lượng tối đa là 100 nhãn mỗi lần',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $validator->errors()
            ], 422);
        }
        
        try {
            $id_sach = $request->id_sach;
            $ma_dkcb = $request->ma_dkcb;
            $so_luong = $request->so_luong;
            $auto_assign = $request->auto_assign ?? true;
            
            // Kiểm tra sách có tồn tại không
            $sach = SachModel::find($id_sach);
            if (!$sach) {
                return response()->json([
                    'status' => 404,
                    'message' => 'Không tìm thấy sách'
                ], 404);
            }
            
            // Lấy số lượng DKCB đã gán
            $daGan = \App\Models\DKCBModel::where('id_sach', $id_sach)->count();
            $conLai = $sach->so_luong - $daGan;
            
            // Kiểm tra số lượng DKCB cần gán có phù hợp với số lượng sách còn lại không
            if ($so_luong > $conLai) {
                return response()->json([
                    'status' => 400,
                    'message' => 'Số lượng DKCB cần gán (' . $so_luong . ') vượt quá số lượng còn lại (' . $conLai . ')'
                ], 400);
            }
            
            if ($auto_assign) {
                // Sử dụng phương thức ganDKCBChoSachTheoMa từ DKCBModel để gán tự động
                $result = \App\Models\DKCBModel::ganDKCBChoSachTheoMa($id_sach, $ma_dkcb, $so_luong);
            } else {
                // Gán một mã DKCB duy nhất (không liên tiếp)
                $result = \App\Models\DKCBModel::ganMotDKCBChoSach($id_sach, $ma_dkcb);
            }
            
            return response()->json($result, $result['status'] == 200 ? 200 : 400);
        } catch (\Exception $e) {
            Log::error('Lỗi khi gán DKCB: ' . $e->getMessage());
            
            return response()->json([
                'status' => 500,
                'message' => 'Đã xảy ra lỗi khi gán số DKCB: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getDKCBBySach($id_sach)
    {
        try {
            // Kiểm tra sách có tồn tại không
            $sach = SachModel::find($id_sach);
            if (!$sach) {
                return response()->json([
                    'status' => 404,
                    'message' => 'Không tìm thấy sách'
                ], 404);
            }
            
            // Lấy danh sách DKCB đã gán cho sách
            $danhSachDKCB = \App\Models\DKCBModel::where('id_sach', $id_sach)
                ->orderBy('ma_dkcb', 'asc')
                ->get();
            
            return response()->json([
                'status' => 200,
                'data' => $danhSachDKCB
            ]);
        } catch (\Exception $e) {
            Log::error('Lỗi khi lấy danh sách DKCB của sách: ' . $e->getMessage());
            
            return response()->json([
                'status' => 500,
                'message' => 'Đã xảy ra lỗi khi lấy danh sách DKCB'
            ], 500);
        }
    }

    /**
     * Xóa mã DKCB khỏi sách
     * 
     * @param  int  $id_dkcb
     * @return \Illuminate\Http\JsonResponse
     */
    public function xoaDKCB($id_dkcb)
    {
        try {
            // Tìm DKCB theo ID
            $dkcb = \App\Models\DKCBModel::find($id_dkcb);
            
            if (!$dkcb) {
                return response()->json([
                    'status' => 404,
                    'message' => 'Không tìm thấy mã DKCB'
                ], 404);
            }
            
            // Lưu lại thông tin mã DKCB để trả về
            $maDKCB = $dkcb->ma_dkcb;
            
            // Xóa liên kết với sách (không xóa mã DKCB khỏi hệ thống)
            $dkcb->id_sach = null;
            $dkcb->save();
            
            return response()->json([
                'status' => 200,
                'message' => 'Đã xóa mã DKCB ' . $maDKCB . ' khỏi sách',
                'data' => $dkcb
            ]);
        } catch (\Exception $e) {
            Log::error('Lỗi khi xóa DKCB: ' . $e->getMessage());
            
            return response()->json([
                'status' => 500,
                'message' => 'Đã xảy ra lỗi khi xóa mã DKCB'
            ], 500);
        }
    }

    public function danhSachSachTheoDonNhan(Request $request)
    {
        try {
            $validated = $this->validate($request, [
                'don_nhan_bat_dau' => 'required|integer|min:1',
                'don_nhan_ket_thuc' => 'required|integer|min:1',
            ], [
                'don_nhan_bat_dau.required' => 'Vui lòng nhập đơn nhận bắt đầu',
                'don_nhan_ket_thuc.required' => 'Vui lòng nhập đơn nhận kết thúc',
                'don_nhan_bat_dau.integer' => 'Đơn nhận bắt đầu phải là số nguyên',
                'don_nhan_ket_thuc.integer' => 'Đơn nhận kết thúc phải là số nguyên',
            ]);

            // Đảm bảo don_nhan_bat_dau <= don_nhan_ket_thuc
            if ($validated['don_nhan_bat_dau'] > $validated['don_nhan_ket_thuc']) {
                return response()->json([
                    'status' => 400,
                    'message' => 'Đơn nhận bắt đầu phải nhỏ hơn hoặc bằng đơn nhận kết thúc'
                ], 400);
            }

            // Lấy danh sách đơn nhận trong khoảng
            $donNhanIds = range($validated['don_nhan_bat_dau'], $validated['don_nhan_ket_thuc']);
            
            // Kiểm tra xem các đơn nhận có tồn tại không
            $cacDonNhanTonTai = DonNhanModel::whereIn('id_don_nhan', $donNhanIds)->pluck('id_don_nhan')->toArray();
            
            if (empty($cacDonNhanTonTai)) {
                return response()->json([
                    'status' => 404,
                    'message' => 'Không tìm thấy đơn nhận nào trong khoảng này'
                ], 404);
            }

            // Lấy danh sách sách từ các đơn nhận
            $danhSachSach = SachModel::whereIn('id_don_nhan', $cacDonNhanTonTai)
                ->with(['bienMucBieuGhi.truongCha.children'])
                ->get();

            $ketQua = [];

            foreach ($danhSachSach as $sach) {
                $phanLoai = ['', ''];
                $dkcbList = [];

                // Lấy DKCB của sách
                $danhSachDKCB = DB::table('dkcb')
                    ->where('id_sach', $sach->id_sach)
                    ->where('trang_thai', 1)
                    ->get();

                foreach ($danhSachDKCB as $dkcb) {
                    $dkcbList[] = $dkcb->ma_dkcb;
                }

                // Tìm trường phân loại 082
                if ($sach->bienMucBieuGhi) {
                    $truong082 = $sach->bienMucBieuGhi->truongCha
                        ->where('ma_truong', '082')
                        ->first();

                    if ($truong082) {
                        // Tìm các trường con a và b
                        $con_a = $truong082->children->where('ma_truong_con', 'a')->first();
                        $con_b = $truong082->children->where('ma_truong_con', 'b')->first();

                        if ($con_a && !empty($con_a->noi_dung)) {
                            $phanLoai[0] = $con_a->noi_dung;
                        }
                        if ($con_b && !empty($con_b->noi_dung)) {
                            $phanLoai[1] = $con_b->noi_dung;
                        }
                    }
                }

                // Thêm vào kết quả
                $ketQua[] = [
                    'id_don_nhan' => $sach->id_don_nhan,
                    'id_sach' => $sach->id_sach,
                    'nhan_de' => $sach->nhan_de,
                    'tac_gia' => $sach->tac_gia,
                    'nam_xuat_ban' => $sach->nam_xuat_ban,
                    'phan_loai_1' => $phanLoai[0],
                    'phan_loai_2' => $phanLoai[1],
                    'so_luong' => $sach->so_luong,
                    'dkcb_list' => $dkcbList
                ];
            }

            return response()->json([
                'status' => 200,
                'message' => 'Lấy dữ liệu thành công',
                'data' => $ketQua,
                'don_nhan_ids' => $cacDonNhanTonTai
            ]);
        } catch (\Exception $e) {
            Log::error('Lỗi khi lấy danh sách sách theo đơn nhận: ' . $e->getMessage());
            return response()->json([
                'status' => 500,
                'message' => 'Đã xảy ra lỗi: ' . $e->getMessage()
            ], 500);
        }
    }

    public function taoNhanPhanLoai(Request $request)
    {
        try {
            $validated = $this->validate($request, [
                'id_sach' => 'required|array',
                'id_sach.*' => 'integer|exists:sach,id_sach',
            ], [
                'id_sach.required' => 'Vui lòng chọn ít nhất một sách',
                'id_sach.array' => 'Danh sách id sách không hợp lệ',
            ]);

            $danhSachSach = SachModel::whereIn('id_sach', $validated['id_sach'])
                ->with(['bienMucBieuGhi.truongCha.children'])
                ->get();

            $ketQua = [];

            foreach ($danhSachSach as $sach) {
                $phanLoai = ['', ''];

                // Tìm trường phân loại 082
                if ($sach->bienMucBieuGhi) {
                    $truong082 = $sach->bienMucBieuGhi->truongCha
                        ->where('ma_truong', '082')
                        ->first();

                    if ($truong082) {
                        // Tìm các trường con a và b
                        $con_a = $truong082->children->where('ma_truong_con', 'a')->first();
                        $con_b = $truong082->children->where('ma_truong_con', 'b')->first();

                        if ($con_a && !empty($con_a->noi_dung)) {
                            $phanLoai[0] = $con_a->noi_dung;
                        }
                        if ($con_b && !empty($con_b->noi_dung)) {
                            $phanLoai[1] = $con_b->noi_dung;
                        }
                    }
                }

                // Nếu có phân loại, thêm số lượng nhãn tương ứng với số lượng sách
                if (!empty($phanLoai[0]) || !empty($phanLoai[1])) {
                    for ($i = 0; $i < $sach->so_luong; $i++) {
                        $ketQua[] = [
                            'id_sach' => $sach->id_sach,
                            'phan_loai_1' => $phanLoai[0],
                            'phan_loai_2' => $phanLoai[1]
                        ];
                    }
                }
            }

            return response()->json([
                'status' => 200,
                'message' => 'Tạo nhãn phân loại thành công',
                'data' => $ketQua
            ]);
        } catch (\Exception $e) {
            Log::error('Lỗi khi tạo nhãn phân loại: ' . $e->getMessage());
            return response()->json([
                'status' => 500,
                'message' => 'Đã xảy ra lỗi: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Import sách từ file Excel
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function importExcel(Request $request)
    {
        // Validate request
        $request->validate([
            'excel_file' => 'required|file|mimes:xlsx,xls',
            'id_don_nhan' => 'required|integer|exists:don_nhan,id_don_nhan',
        ], [
            'excel_file.required' => 'Vui lòng chọn file Excel để import',
            'excel_file.file' => 'File không hợp lệ',
            'excel_file.mimes' => 'File phải có định dạng Excel (.xlsx, .xls)',
            'id_don_nhan.required' => 'Thiếu thông tin đơn nhận',
            'id_don_nhan.exists' => 'Đơn nhận không tồn tại',
        ]);

        try {
            // Lấy file từ request
            $file = $request->file('excel_file');
            $id_don_nhan = $request->id_don_nhan;

            // Đọc file Excel
            $spreadsheet = IOFactory::load($file->getPathname());
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();

            // Bỏ qua dòng tiêu đề
            $dataRows = array_slice($rows, 1);

            // Biến đếm số sách đã import thành công
            $successCount = 0;
            $errorCount = 0;
            $errors = [];

            // Lấy thông tin người tạo từ đơn nhận
            $donNhan = DonNhanModel::find($id_don_nhan);
            $nguoiTao = $donNhan->nguoi_tao ?? '';

            // Xử lý từng dòng dữ liệu
            $rowIndex = 0;
            while ($rowIndex < count($dataRows)) {
                $row = $dataRows[$rowIndex];
                
                // Bỏ qua dòng trống
                if (empty($row[1])) {
                    $rowIndex++;
                    continue;
                }

                try {
                    DB::beginTransaction();

                    // Xử lý thông tin xuất bản (cột D)
                    $xuatBanInfo = $row[3] ?? '';
                    $noiXuatBan = '';
                    $nhaXuatBan = '';
                    $namXuatBan = '';

                    if (!empty($xuatBanInfo)) {
                        // Tách theo dấu ":"
                        $parts = explode(':', $xuatBanInfo);
                        if (count($parts) > 1) {
                            $noiXuatBan = trim($parts[0]);
                            
                            // Tách phần còn lại theo dấu ","
                            $remainingParts = explode(',', $parts[1]);
                            if (count($remainingParts) > 1) {
                                $nhaXuatBan = trim($remainingParts[0]);
                                $namXuatBan = trim($remainingParts[1]);
                            } else {
                                $nhaXuatBan = trim($parts[1]);
                            }
                        } else {
                            // Nếu không có dấu ":" thì thử tách theo dấu ","
                            $commaParts = explode(',', $xuatBanInfo);
                            if (count($commaParts) > 1) {
                                $nhaXuatBan = trim($commaParts[0]);
                                $namXuatBan = trim($commaParts[1]);
                            } else {
                                $nhaXuatBan = $xuatBanInfo;
                            }
                        }
                    }

                    // Xử lý phân loại (cột E)
                    $phanLoaiInfo = $row[4] ?? '';
                    $phanLoai1 = '';
                    $phanLoai2 = '';

                    if (!empty($phanLoaiInfo)) {
                        $phanLoaiParts = explode('/', $phanLoaiInfo);
                        $phanLoai1 = trim($phanLoaiParts[0] ?? '');
                        
                        if (count($phanLoaiParts) > 1) {
                            // Nếu có nhiều hơn 2 phần (trường hợp 2: "660.6/ Ch125/ T.3")
                            if (count($phanLoaiParts) > 2) {
                                // Lấy phần đầu tiên cho 082 a
                                // Ghép các phần còn lại cho 082 b
                                $phanLoai2 = trim(implode('/ ', array_slice($phanLoaiParts, 1)));
                            } else {
                                // Trường hợp 1: "621.4/ Ph500"
                                $phanLoai2 = trim($phanLoaiParts[1]);
                            }
                        }
                    }

                    // Xử lý giá và số lượng
                    $gia = !empty($row[5]) ? floatval($row[5]) : 0;
                    $soLuong = !empty($row[6]) ? intval($row[6]) : 0;
                    $thanhTien = $gia * $soLuong;

                    // Kiểm tra xem đầu sách đã tồn tại chưa (dựa trên nhan đề và tác giả)
                    $sachDaTonTai = SachModel::where('id_don_nhan', $id_don_nhan)
                        ->where('nhan_de', $row[1])
                        ->where('tac_gia', $row[2])
                        ->first();

                    if ($sachDaTonTai) {
                        // Cập nhật thông tin sách đã tồn tại
                        $sachDaTonTai->nam_xuat_ban = $namXuatBan;
                        $sachDaTonTai->nha_xuat_ban = $nhaXuatBan;
                        $sachDaTonTai->noi_xuat_ban = $noiXuatBan;
                        $sachDaTonTai->gia = $gia;
                        $sachDaTonTai->so_luong = $soLuong;
                        $sachDaTonTai->thanh_tien = $thanhTien;
                        $sachDaTonTai->save();

                        // Sử dụng sách đã tồn tại
                        $sach = $sachDaTonTai;

                        // Cập nhật biểu ghi biên mục nếu có
                        $bmg = BienMucBieuGhiModel::where('id_sach', $sach->id_sach)->first();
                        if ($bmg) {
                            // Cập nhật các trường cha và con
                            $truong082 = BienMucTruongChaModel::where('id_bien_muc_bieu_ghi', $bmg->id_bien_muc_bieu_ghi)
                                ->where('ma_truong', '082')
                                ->first();
                            
                            if ($truong082 && !empty($phanLoai1)) {
                                BienMucTruongConModel::where('id_bien_muc_truong_cha', $truong082->id_bien_muc_truong_cha)
                                    ->where('ma_truong_con', 'a')
                                    ->update(['noi_dung' => $phanLoai1]);
                            }
                            
                            if ($truong082 && !empty($phanLoai2)) {
                                BienMucTruongConModel::where('id_bien_muc_truong_cha', $truong082->id_bien_muc_truong_cha)
                                    ->where('ma_truong_con', 'b')
                                    ->update(['noi_dung' => $phanLoai2]);
                            }
                            
                            // Cập nhật thông tin xuất bản
                            $truong260 = BienMucTruongChaModel::where('id_bien_muc_bieu_ghi', $bmg->id_bien_muc_bieu_ghi)
                                ->where('ma_truong', '260')
                                ->first();
                                
                            if ($truong260) {
                                if (!empty($noiXuatBan)) {
                                    BienMucTruongConModel::where('id_bien_muc_truong_cha', $truong260->id_bien_muc_truong_cha)
                                        ->where('ma_truong_con', 'a')
                                        ->update(['noi_dung' => $noiXuatBan]);
                                }
                                
                                if (!empty($nhaXuatBan)) {
                                    BienMucTruongConModel::where('id_bien_muc_truong_cha', $truong260->id_bien_muc_truong_cha)
                                        ->where('ma_truong_con', 'b')
                                        ->update(['noi_dung' => $nhaXuatBan]);
                                }
                                
                                if (!empty($namXuatBan)) {
                                    BienMucTruongConModel::where('id_bien_muc_truong_cha', $truong260->id_bien_muc_truong_cha)
                                        ->where('ma_truong_con', 'c')
                                        ->update(['noi_dung' => $namXuatBan]);
                                }
                            }
                        } else {
                            // Nếu không có biểu ghi biên mục, tạo mới
                            $bmg = BienMucBieuGhiModel::create([
                                'id_sach' => $sach->id_sach,
                            ]);

                            // Tạo các trường cha và con mặc định
                            $allParents = [
                                // Các trường dựa trên dữ liệu nhập
                                [
                                    'ma_truong' => '245', 'ct1' => '0', 'ct2' => '#',
                                    'children' => [
                                        ['ma_truong_con' => 'a', 'noi_dung' => $row[1] ?? ''], // nhan_de
                                        ['ma_truong_con' => 'c', 'noi_dung' => $row[2] ?? ''], // tac_gia
                                        ['ma_truong_con' => 'b', 'noi_dung' => ''],
                                        ['ma_truong_con' => 'n', 'noi_dung' => ''],
                                        ['ma_truong_con' => 'p', 'noi_dung' => ''],
                                    ],
                                ],
                                [
                                    'ma_truong' => '260', 'ct1' => '#', 'ct2' => '#',
                                    'children' => [
                                        ['ma_truong_con' => 'a', 'noi_dung' => $noiXuatBan],
                                        ['ma_truong_con' => 'b', 'noi_dung' => $nhaXuatBan],
                                        ['ma_truong_con' => 'c', 'noi_dung' => $namXuatBan],
                                    ],
                                ],
                                [
                                    'ma_truong' => '082', 'ct1' => '1', 'ct2' => '4',
                                    'children' => [
                                        ['ma_truong_con' => '2', 'noi_dung' => ''],
                                        ['ma_truong_con' => 'a', 'noi_dung' => $phanLoai1],
                                        ['ma_truong_con' => 'b', 'noi_dung' => $phanLoai2],
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
                                        ['ma_truong_con' => 'a', 'noi_dung' => $row[2] ?? ''], // tac_gia
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
                        }
                        
                        // Xóa các mã DKCB cũ đã gán cho sách này
                        DKCBModel::where('id_sach', $sach->id_sach)
                            ->update(['id_sach' => null]);
                    } else {
                        // Tạo bản ghi sách mới
                        $sach = SachModel::create([
                            'id_don_nhan' => $id_don_nhan,
                            'nhan_de' => $row[1] ?? '',
                            'tac_gia' => $row[2] ?? '',
                            'nam_xuat_ban' => $namXuatBan,
                            'nha_xuat_ban' => $nhaXuatBan,
                            'noi_xuat_ban' => $noiXuatBan,
                            'gia' => $gia,
                            'so_luong' => $soLuong,
                            'thanh_tien' => $thanhTien,
                        ]);

                        // Tạo biểu ghi biên mục
                        $bmg = BienMucBieuGhiModel::create([
                            'id_sach' => $sach->id_sach,
                        ]);

                        // Tạo các trường cha và con mặc định
                        $allParents = [
                            // Các trường dựa trên dữ liệu nhập
                            [
                                'ma_truong' => '245', 'ct1' => '0', 'ct2' => '#',
                                'children' => [
                                    ['ma_truong_con' => 'a', 'noi_dung' => $row[1] ?? ''], // nhan_de
                                    ['ma_truong_con' => 'c', 'noi_dung' => $row[2] ?? ''], // tac_gia
                                    ['ma_truong_con' => 'b', 'noi_dung' => ''],
                                    ['ma_truong_con' => 'n', 'noi_dung' => ''],
                                    ['ma_truong_con' => 'p', 'noi_dung' => ''],
                                ],
                            ],
                            [
                                'ma_truong' => '260', 'ct1' => '#', 'ct2' => '#',
                                'children' => [
                                    ['ma_truong_con' => 'a', 'noi_dung' => $noiXuatBan],
                                    ['ma_truong_con' => 'b', 'noi_dung' => $nhaXuatBan],
                                    ['ma_truong_con' => 'c', 'noi_dung' => $namXuatBan],
                                ],
                            ],
                            [
                                'ma_truong' => '082', 'ct1' => '1', 'ct2' => '4',
                                'children' => [
                                    ['ma_truong_con' => '2', 'noi_dung' => ''],
                                    ['ma_truong_con' => 'a', 'noi_dung' => $phanLoai1],
                                    ['ma_truong_con' => 'b', 'noi_dung' => $phanLoai2],
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
                                    ['ma_truong_con' => 'a', 'noi_dung' => $row[2] ?? ''], // tac_gia
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
                    }

                    // Xử lý DKCB (cột H)
                    $dkcbList = [];
                    
                    // Thêm mã DKCB từ dòng hiện tại
                    if (!empty($row[7])) {
                        $dkcbList[] = trim($row[7]);
                    }
                    
                    // Nếu số lượng > 1, tìm các mã DKCB từ các dòng tiếp theo
                    if ($soLuong > 1) {
                        $nextRowIndex = $rowIndex + 1;
                        $dkcbCount = 1; // Đã có 1 mã từ dòng hiện tại
                        
                        // Kiểm tra các dòng tiếp theo để lấy các mã DKCB
                        while ($dkcbCount < $soLuong && $nextRowIndex < count($dataRows)) {
                            $nextRow = $dataRows[$nextRowIndex];
                            
                            // Nếu dòng tiếp theo có các cột thông tin sách trống
                            // nhưng có mã DKCB, thì đó là mã DKCB của sách hiện tại
                            if (empty($nextRow[1]) && empty($nextRow[2]) && empty($nextRow[3]) && 
                                empty($nextRow[4]) && empty($nextRow[5]) && empty($nextRow[6]) && 
                                !empty($nextRow[7])) {
                                
                                $dkcbList[] = trim($nextRow[7]);
                                $dkcbCount++;
                                $nextRowIndex++;
                            } else {
                                // Nếu dòng tiếp theo có thông tin sách, 
                                // thì đó là một sách mới, không phải mã DKCB của sách hiện tại
                                break;
                            }
                        }
                        
                        // Cập nhật rowIndex để bỏ qua các dòng đã xử lý
                        $rowIndex = $nextRowIndex - 1;
                    }
                    
                    // Gán các mã DKCB cho sách
                    foreach ($dkcbList as $maDKCB) {
                        if (empty($maDKCB)) continue;
                        
                        $dkcb = DKCBModel::where('ma_dkcb', $maDKCB)->first();
                        
                        if ($dkcb) {
                            // Gán DKCB cho sách (luôn ghi đè)
                            $dkcb->id_sach = $sach->id_sach;
                            $dkcb->save();
                        } else {
                            Log::warning("Không tìm thấy mã DKCB {$maDKCB} trong hệ thống");
                        }
                    }

                    DB::commit();
                    $successCount++;
                } catch (\Exception $e) {
                    DB::rollBack();
                    $errorCount++;
                    $errors[] = "Lỗi ở dòng " . ($rowIndex + 2) . ": " . $e->getMessage();
                    Log::error("Lỗi import Excel dòng " . ($rowIndex + 2) . ": " . $e->getMessage());
                }
                
                $rowIndex++;
            }

            // Tạo thông báo kết quả
            $message = "Import thành công {$successCount} sách";
            if ($errorCount > 0) {
                $message .= ", {$errorCount} sách bị lỗi";
            }

            return response()->json([
                'status' => 200,
                'message' => $message,
                'success_count' => $successCount,
                'error_count' => $errorCount,
                'errors' => $errors
            ]);

        } catch (\Exception $e) {
            Log::error("Lỗi import Excel: " . $e->getMessage());
            return response()->json([
                'status' => 500,
                'message' => 'Có lỗi xảy ra khi import: ' . $e->getMessage()
            ], 500);
        }
    }
}
