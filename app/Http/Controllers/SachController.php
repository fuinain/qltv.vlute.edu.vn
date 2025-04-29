<?php

namespace App\Http\Controllers;

use App\Models\BienMucBieuGhiModel;
use App\Models\BienMucTruongChaModel;
use App\Models\BienMucTruongConModel;
use App\Models\DonNhanModel;
use App\Models\SachModel;
use Carbon\Carbon;
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
            $sheet->setCellValue('H9', setValueInCell('Tổng số tiền: ',$tongTriGia ?? ''));
            $sheet->getStyle('H9')->getNumberFormat()->setFormatCode('#,##0');
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

                    // Lấy thông tin biên mục (giữ nguyên logic)
                    $phanLoai = '';
                    $tenTaiLieu = '';
                    $bieuGhi = $sach->bienMucBieuGhi;
                    // ... (logic lấy phân loại và tên tài liệu giữ nguyên) ...
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
            $fileName = 'Don_Nhan_' . $donNhan->id_don_nhan . '_' . date('YmdHis') . '.xlsx';
            $tempFilePath = tempnam(sys_get_temp_dir(), 'excel_don_nhan_');
            $writer->save($tempFilePath);
            return response()->download($tempFilePath, $fileName, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ])->deleteFileAfterSend(true);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::error("Export Excel Error - Đơn nhận không tồn tại: ID " . $id_don_nhan . " - " . $e->getMessage());
            return response()->json(['status' => 404, 'message' => 'Không tìm thấy đơn nhận.'], 404);
        } catch (\Throwable $e) {
            Log::error("Lỗi export Excel Đơn nhận ID {$id_don_nhan}: " . $e->getMessage() . "\n" . $e->getTraceAsString());
            // Quan trọng: Trả về lỗi dạng JSON để frontend có thể bắt được nếu có thể
            return response()->json(['status' => 500, 'message' => 'Có lỗi xảy ra trong quá trình xuất file Excel.'], 500);
        }
    }

    public function exportExcelThongKeTaiLieu(int $id_don_nhan)
    {
        try {
            // 1. Gọi hàm từ Model để lấy dữ liệu đã chuẩn bị và thống kê
            $exportData = DonNhanModel::getDataThongKeTaiLieuForExport($id_don_nhan);

            // Kiểm tra nếu model trả về null (không tìm thấy đơn nhận)
            if (!$exportData) {
                Log::error("Export Thống kê TL Error - Đơn nhận ID {$id_don_nhan} không tồn tại.");
                return response()->json(['status' => 404, 'message' => 'Không tìm thấy đơn nhận với ID cung cấp.'], 404);
            }

            // 2. Lấy dữ liệu từ kết quả trả về của Model
            $donNhan = $exportData->donNhan;
            $thongKeData = $exportData->thongKeData;
            $tongSoTenChung = $exportData->tongSoTenChung;
            $tongSoBanChung = $exportData->tongSoBanChung;
            $tongTriGiaChung = $exportData->tongTriGiaChung;

            // 3. Load template và chuẩn bị PhpSpreadsheet
            $templatePath = public_path('template_excel/template_excel_thong_ke_tai_lieu.xlsx');
            if (!file_exists($templatePath)) {
                Log::error("Template file not found: " . $templatePath);
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
            $sheet->setCellValue('H9', setValueInCell('Tổng trị giá: ', $tongTriGiaChung));
            $sheet->getStyle('H9')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

            // --- Chuẩn bị Style và Vị trí bắt đầu ---
            $startRow = 11;
            $currentRow = $startRow - 1;
            $templateStyle = [];

            // --- SỬA LỖI TẠI ĐÂY ---
            // Sao chép style từ dòng mẫu (dòng $startRow) - Bỏ kiểm tra styleExists()
            for ($col = 'A'; $col <= 'O'; $col++) {
                $columnLetter = $col;
                // Lấy style trực tiếp, getStyle() sẽ xử lý trả về style mặc định nếu cần
                $templateStyle[$columnLetter] = $sheet->getStyle($columnLetter . $startRow);
            }
            // --- KẾT THÚC SỬA LỖI ---

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
                    $sheet->getStyle('N' . $currentRow)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
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
            $dateRow = $totalRow + 2;
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
            $sheet->getStyle('N' . $totalRow)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
            $sheet->getStyle('N' . $totalRow)->getFont()->setBold(true);
            $sheet->getStyle('N' . $totalRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT)->setVertical(Alignment::VERTICAL_CENTER);
            $sheet->getStyle('A' . $totalRow . ':O' . $totalRow)->applyFromArray($borderStyle);

            // Phần Chữ ký (Merge từ J đến O)
            $now = Carbon::now('Asia/Ho_Chi_Minh');
            $currentLocation = config('app.location_for_report', 'Vĩnh Long');
            $chuoiNgayThang = $currentLocation . ", ngày " . $now->day . " tháng " . $now->month . " năm " . $now->year;
            $sheet->mergeCells('J' . $dateRow . ':O' . $dateRow)->setCellValue('J' . $dateRow, $chuoiNgayThang);
            $sheet->getStyle('J' . $dateRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
            $sheet->getStyle('J' . $dateRow)->getFont()->setItalic(true);

            $sheet->mergeCells('J' . $titleRow . ':O' . $titleRow)->setCellValue('J' . $titleRow, 'Người lập báo cáo');
            $sheet->getStyle('J' . $titleRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_TOP);
            $sheet->getStyle('J' . $titleRow)->getFont()->setBold(true);
            if ($sheet->getRowDimension($titleRow)->getRowHeight() < 20) {
                $sheet->getRowDimension($titleRow)->setRowHeight(20);
            }

            $sheet->mergeCells('J' . $nameSignatureRow . ':O' . $nameSignatureRow)->setCellValue('J' . $nameSignatureRow, $donNhan->nguoi_tao ?? '');
            $sheet->getStyle('J' . $nameSignatureRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
            $sheet->getStyle('J' . $nameSignatureRow)->getFont()->setBold(true);

            // 4. Chuẩn bị và Xuất file Excel
            $writer = new Xlsx($spreadsheet);
            $fileName = 'ThongKe_LoaiTaiLieu_DN' . $donNhan->id_don_nhan . '_' . Carbon::now()->format('Ymd_His') . '.xlsx';
            $tempFilePath = tempnam(sys_get_temp_dir(), 'excel_thongke_tl_');
            $writer->save($tempFilePath);

            return response()->download($tempFilePath, $fileName, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ])->deleteFileAfterSend(true);

        } catch (ModelNotFoundException $e) {
            Log::error("Export Thống kê TL Error - Đơn nhận ID {$id_don_nhan} không tồn tại: " . $e->getMessage());
            return response()->json(['status' => 404, 'message' => 'Không tìm thấy đơn nhận với ID cung cấp.'], 404);
        } catch (\PhpOffice\PhpSpreadsheet\Exception $e) {
            Log::error("Lỗi PhpSpreadsheet khi export Thống kê Tài liệu ID {$id_don_nhan}: " . $e->getMessage() . "\n" . $e->getTraceAsString());
            return response()->json(['status' => 500, 'message' => 'Có lỗi xảy ra khi tạo file Excel. Vui lòng thử lại.'], 500);
        } catch (\Throwable $e) {
            Log::error("Lỗi không xác định export Excel Thống kê Tài liệu ID {$id_don_nhan}: " . $e->getMessage() . "\n" . $e->getTraceAsString());
            return response()->json(['status' => 500, 'message' => 'Có lỗi không xác định xảy ra trong quá trình xuất file. Liên hệ quản trị viên.'], 500);
        }
    }

}
