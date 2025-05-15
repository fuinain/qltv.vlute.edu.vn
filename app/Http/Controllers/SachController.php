<?php

namespace App\Http\Controllers;

use App\Models\BienMucBieuGhiModel;
use App\Models\BienMucTruongChaModel;
use App\Models\BienMucTruongConModel;
use App\Models\DonNhanModel;
use App\Models\SachModel;
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

}
