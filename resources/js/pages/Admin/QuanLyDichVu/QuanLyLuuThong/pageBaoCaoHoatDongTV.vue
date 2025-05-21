<template>
    <ContentWrapper title="QUẢN LÝ BÁO CÁO HOẠT ĐỘNG THƯ VIỆN">
        <template #ContentPage>
            <div class="row">
                <div class="col-12">
                    <Card>
                        <template #ContentCardHeader>
                            <div class="d-flex justify-content-between">
                                <div>
                                    <button type="button" class="btn btn-primary mr-2" @click="baoCao" :disabled="isLoading">
                                        <i class="fas fa-file-alt" v-if="!isLoading"></i>
                                        <i class="fas fa-spinner fa-spin" v-else></i>&nbsp;
                                        Báo cáo
                                    </button>
                                </div>                              
                            </div>
                        </template>
                        <template #ContentCardBody>
                            <div class="row">
                                <div class="col-12">
                                    <div class="card card-primary card-outline">
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <Input v-model="formBaoCao.tu_ngay" label="Từ ngày" note="(*)" placeholder="Nhập từ ngày..." type="date" required />
                                                </div>
                                                <div class="col-md-4">
                                                    <Input v-model="formBaoCao.den_ngay" label="Đến ngày" note="(*)" placeholder="Nhập đến ngày..." type="date" required />
                                                </div>
                                                <div class="col-md-4">
                                                    <SelectOption 
                                                    v-model="formBaoCao.id_dm_bao_cao" 
                                                    :options="dsBaoCaoOptions" 
                                                    label="Báo cáo" note="(*)" 
                                                    placeholder="Chọn báo cáo..." required />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Hiển thị kết quả thống kê sách đang mượn -->
                            <div class="row mt-4" v-if="ketQuaThongKe.length > 0 && formBaoCao.id_dm_bao_cao == 2">
                                <div class="col-12">
                                    <Card title="Thống kê sách đang mượn">
                                        <template #ContentCardHeader>
                                            <div class="d-flex align-items-center">
                                                <button type="button" class="btn btn-success btn-sm" @click="xuatExcelSachDangMuon" :disabled="isLoading">
                                                    <i class="fas fa-file-excel"></i>&nbsp;
                                                    Xuất Excel
                                                </button>
                                            </div>
                                        </template>
                                        <template #ContentCardBody>
                                            <Table :columns="headersSachDangMuon" :data="ketQuaThongKe" :hideSearch="true">
                                                <!-- Slot cho cột STT -->
                                                <template v-slot:column-index="{ row, rowIndex }">
                                                    {{ rowIndex + 1 }}
                                                </template>
                                                
                                                <!-- Slot cho thông tin ấn phẩm -->
                                                <template v-slot:column-thong_tin_an_pham="{ row }">
                                                    <div>
                                                        <strong>Số ĐKCB:</strong> {{ row.ma_dkcb }}<br>
                                                        <strong>Nhan đề:</strong> {{ row.nhan_de }}
                                                    </div>
                                                </template>

                                                <!-- Slot cho thông tin độc giả -->
                                                <template v-slot:column-thong_tin_doc_gia="{ row }">
                                                    <div>
                                                        <strong>Số thẻ:</strong> {{ row.so_the }}<br>
                                                        <strong>Tên độc giả:</strong> {{ row.ho_ten }}<br>
                                                        <strong>Đơn vị:</strong> {{ row.don_vi_quan_ly || 'Chưa có' }}
                                                    </div>
                                                </template>

                                                <!-- Slot cho thông tin thời gian -->
                                                <template v-slot:column-thong_tin_thoi_gian="{ row }">
                                                    <div>
                                                        <strong>Ngày mượn:</strong> {{ formatDateTime(row.ngay_muon) }}<br>
                                                        <strong>Hạn trả:</strong> {{ formatDateTime(row.han_tra) }}<br>
                                                        <strong>Tại chỗ:</strong> {{ row.tai_cho ? 'X' : '' }}
                                                    </div>
                                                </template>
                                            </Table>
                                        </template>
                                    </Card>
                                </div>
                            </div>
                            
                            <!-- Hiển thị kết quả thống kê sách đã trả -->
                            <div class="row mt-4" v-if="ketQuaThongKe.length > 0 && formBaoCao.id_dm_bao_cao == 3">
                                <div class="col-12">
                                    <Card title="Thống kê sách đã trả">
                                        <template #ContentCardHeader>
                                            <div class="d-flex align-items-center">
                                                <button type="button" class="btn btn-success btn-sm" @click="xuatExcelSachDaTra" :disabled="isLoading">
                                                    <i class="fas fa-file-excel"></i>&nbsp;
                                                    Xuất Excel
                                                </button>
                                            </div>
                                        </template>
                                        <template #ContentCardBody>
                                            <Table :columns="headersSachDaTra" :data="ketQuaThongKe" :hideSearch="true">
                                                <!-- Slot cho cột STT -->
                                                <template v-slot:column-index="{ row, rowIndex }">
                                                    {{ rowIndex + 1 }}
                                                </template>
                                                
                                                <!-- Slot cho thông tin ấn phẩm -->
                                                <template v-slot:column-thong_tin_an_pham="{ row }">
                                                    <div>
                                                        <strong>Số ĐKCB:</strong> {{ row.ma_dkcb }}<br>
                                                        <strong>Nhan đề:</strong> {{ row.nhan_de }}
                                                    </div>
                                                </template>

                                                <!-- Slot cho thông tin độc giả -->
                                                <template v-slot:column-thong_tin_doc_gia="{ row }">
                                                    <div>
                                                        <strong>Số thẻ:</strong> {{ row.so_the }}<br>
                                                        <strong>Tên độc giả:</strong> {{ row.ho_ten }}<br>
                                                        <strong>Đơn vị:</strong> {{ row.don_vi_quan_ly || 'Chưa có' }}
                                                    </div>
                                                </template>

                                                <!-- Slot cho thông tin thời gian -->
                                                <template v-slot:column-thong_tin_thoi_gian="{ row }">
                                                    <div>
                                                        <strong>Ngày mượn:</strong> {{ formatDateTime(row.ngay_muon) }}<br>
                                                        <strong>Hạn trả:</strong> {{ formatDateTime(row.han_tra) }}<br>
                                                        <strong>Ngày trả:</strong> {{ formatDateTime(row.ngay_tra) }}<br>
                                                        <strong>Tại chỗ:</strong> {{ row.tai_cho ? 'X' : '' }}
                                                    </div>
                                                </template>
                                            </Table>
                                        </template>
                                    </Card>
                                </div>
                            </div>

                            <!-- Hiển thị kết quả thống kê sách quá hạn -->
                            <div class="row mt-4" v-if="ketQuaThongKe.length > 0 && formBaoCao.id_dm_bao_cao == 5">
                                <div class="col-12">
                                    <Card title="Thống kê sách đang mượn quá hạn">
                                        <template #ContentCardHeader>
                                            <div class="d-flex align-items-center">
                                                <button type="button" class="btn btn-success btn-sm" @click="xuatExcelSachQuaHan" :disabled="isLoading">
                                                    <i class="fas fa-file-excel"></i>&nbsp;
                                                    Xuất Excel
                                                </button>
                                            </div>
                                        </template>
                                        <template #ContentCardBody>
                                            <Table :columns="headersSachQuaHan" :data="ketQuaThongKe" :hideSearch="true">
                                                <!-- Slot cho cột STT -->
                                                <template v-slot:column-index="{ row, rowIndex }">
                                                    {{ rowIndex + 1 }}
                                                </template>
                                                
                                                <!-- Slot cho thông tin ấn phẩm -->
                                                <template v-slot:column-thong_tin_an_pham="{ row }">
                                                    <div>
                                                        <strong>Số ĐKCB:</strong> {{ row.ma_dkcb }}<br>
                                                        <strong>Nhan đề:</strong> {{ row.nhan_de }}
                                                    </div>
                                                </template>

                                                <!-- Slot cho thông tin độc giả -->
                                                <template v-slot:column-thong_tin_doc_gia="{ row }">
                                                    <div>
                                                        <strong>Số thẻ:</strong> {{ row.so_the }}<br>
                                                        <strong>Tên độc giả:</strong> {{ row.ho_ten }}<br>
                                                        <strong>Đơn vị:</strong> {{ row.don_vi_quan_ly || 'Chưa có' }}
                                                    </div>
                                                </template>

                                                <!-- Slot cho thông tin thời gian -->
                                                <template v-slot:column-thong_tin_thoi_gian="{ row }">
                                                    <div>
                                                        <strong>Ngày mượn:</strong> {{ formatDateTime(row.ngay_muon) }}<br>
                                                        <strong>Hạn trả:</strong> {{ formatDateTime(row.han_tra) }}<br>
                                                        <strong>Số ngày trễ:</strong> <span class="text-danger font-weight-bold">{{ row.so_ngay_tre }}</span><br>
                                                    </div>
                                                </template>
                                            </Table>
                                        </template>
                                    </Card>
                                </div>
                            </div>

                            <!-- Hiển thị kết quả thống kê bạn đọc đến thư viện -->
                            <div class="row mt-4" v-if="ketQuaThongKe.length > 0 && formBaoCao.id_dm_bao_cao == 4">
                                <div class="col-12">
                                    <Card title="Thống kê bạn đọc đến thư viện">
                                        <template #ContentCardHeader>
                                            <div class="d-flex align-items-center">
                                                <button type="button" class="btn btn-success btn-sm" @click="xuatExcelBanDocDenThuVien" :disabled="isLoading">
                                                    <i class="fas fa-file-excel"></i>&nbsp;
                                                    Xuất Excel
                                                </button>
                                            </div>
                                        </template>
                                        <template #ContentCardBody>
                                            <Table :columns="headersBanDocDenThuVien" :data="ketQuaThongKe" :hideSearch="true">
                                                <!-- Slot cho cột STT -->
                                                <template v-slot:column-index="{ row, rowIndex }">
                                                    {{ rowIndex + 1 }}
                                                </template>
                                                
                                                <!-- Slot cho thông tin độc giả -->
                                                <template v-slot:column-thong_tin_doc_gia="{ row }">
                                                    <div>
                                                        <strong>Số thẻ:</strong> {{ row.so_the }}<br>
                                                        <strong>Họ tên:</strong> {{ row.ho_ten }}<br>
                                                        <strong>Đơn vị:</strong> {{ row.don_vi_quan_ly || 'Chưa có' }}
                                                    </div>
                                                </template>
                                                
                                                <!-- Slot cho thông tin hoạt động -->
                                                <template v-slot:column-thong_tin_hoat_dong="{ row }">
                                                    <div>
                                                        <strong>Thời gian đến:</strong> {{ formatDateTime(row.thoi_gian_den) }}<br>
                                                        <strong>Số lượng mượn:</strong> {{ row.so_luong_muon }}<br>
                                                        <strong>Số lượng trả:</strong> {{ row.so_luong_tra }}
                                                    </div>
                                                </template>
                                            </Table>
                                        </template>
                                    </Card>
                                </div>
                            </div>

                            <!-- Hiển thị kết quả thống kê tình hình phục vụ bạn đọc -->
                            <div class="row mt-4" v-if="ketQuaThongKe.length > 0 && formBaoCao.id_dm_bao_cao == 1">
                                <div class="col-12">
                                    <Card title="Báo cáo tình hình phục vụ bạn đọc">
                                        <template #ContentCardHeader>
                                            <div class="d-flex align-items-center">
                                                <button type="button" class="btn btn-success btn-sm" @click="xuatExcelTinhHinhPhucVuBanDoc" :disabled="isLoading">
                                                    <i class="fas fa-file-excel"></i>&nbsp;
                                                    Xuất Excel
                                                </button>
                                            </div>
                                        </template>
                                        <template #ContentCardBody>
                                            <!-- Bảng thống kê tùy chỉnh để có thể merge cells -->
                                            <div class="table-responsive">
                                                <table class="table table-bordered table-striped">
                                                    <thead class="bg-primary text-white">
                                                        <tr>
                                                            <th rowspan="2" class="text-center align-middle" style="width: 50px;">STT</th>
                                                            <th rowspan="2" class="text-center align-middle">Ngày</th>
                                                            <th colspan="2" class="text-center align-middle">ĐG đến TV</th>
                                                            <th colspan="2" class="text-center align-middle">ĐG mượn sách</th>
                                                            <th colspan="4" class="text-center align-middle">Sách mượn</th>
                                                            <th colspan="2" class="text-center align-middle">Sách trả</th>
                                                            <th colspan="2" class="text-center align-middle">Sách quá hạn</th>
                                                            <th colspan="2" class="text-center align-middle">Xử lí phạt</th>
                                                            <th colspan="6" class="text-center align-middle">Số lần cấp thẻ</th>
                                                        </tr>
                                                        <tr>
                                                            <th class="text-center">SV</th>
                                                            <th class="text-center">CB-GV</th>
                                                            <th class="text-center">SV</th>
                                                            <th class="text-center">CB-GV</th>
                                                            <th colspan="2" class="text-center">Đọc tại chỗ</th>
                                                            <th colspan="2" class="text-center">Về nhà</th>
                                                            <th class="text-center">SV</th>
                                                            <th class="text-center">CB-GV</th>
                                                            <th class="text-center">SV</th>
                                                            <th class="text-center">CB-GV</th>
                                                            <th class="text-center">SV</th>
                                                            <th class="text-center">CB-GV</th>
                                                            <th colspan="2" class="text-center">1</th>
                                                            <th colspan="2" class="text-center">2</th>
                                                            <th colspan="2" class="text-center">3</th>
                                                        </tr>
                                                        <tr>
                                                            <th colspan="6" class="text-center"></th>
                                                            <th class="text-center">SV</th>
                                                            <th class="text-center">CB-GV</th>
                                                            <th class="text-center">SV</th>
                                                            <th class="text-center">CB-GV</th>
                                                            <th colspan="6" class="text-center"></th>
                                                            <th class="text-center">SV</th>
                                                            <th class="text-center">CB</th>
                                                            <th class="text-center">SV</th>
                                                            <th class="text-center">CB</th>
                                                            <th class="text-center">SV</th>
                                                            <th class="text-center">CB</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <!-- Dữ liệu từng ngày -->
                                                        <template v-for="(row, rowIndex) in ketQuaThongKe" :key="rowIndex">
                                                            <tr>
                                                                <td class="text-center">{{ rowIndex + 1 }}</td>
                                                                <td class="text-center">{{ row.ngay }}</td>
                                                                <td class="text-center">{{ row.dg_den_tv_sv }}</td>
                                                                <td class="text-center">{{ row.dg_den_tv_cbgv }}</td>
                                                                <td class="text-center">{{ row.dg_muon_sach_sv }}</td>
                                                                <td class="text-center">{{ row.dg_muon_sach_cbgv }}</td>
                                                                <td class="text-center">{{ row.sach_doc_tai_cho_sv }}</td>
                                                                <td class="text-center">{{ row.sach_doc_tai_cho_cbgv }}</td>
                                                                <td class="text-center">{{ row.sach_muon_ve_nha_sv }}</td>
                                                                <td class="text-center">{{ row.sach_muon_ve_nha_cbgv }}</td>
                                                                <td class="text-center">{{ row.sach_tra_sv }}</td>
                                                                <td class="text-center">{{ row.sach_tra_cbgv }}</td>
                                                                <td class="text-center">{{ row.sach_qua_han_sv }}</td>
                                                                <td class="text-center">{{ row.sach_qua_han_cbgv }}</td>
                                                                <td class="text-center">{{ row.xu_ly_vi_pham_sv }}</td>
                                                                <td class="text-center">{{ row.xu_ly_vi_pham_cbgv }}</td>
                                                                <td class="text-center">{{ row.cap_the_1_sv }}</td>
                                                                <td class="text-center">{{ row.cap_the_1_cbgv }}</td>
                                                                <td class="text-center">{{ row.cap_the_2_sv }}</td>
                                                                <td class="text-center">{{ row.cap_the_2_cbgv }}</td>
                                                                <td class="text-center">{{ row.cap_the_3_sv }}</td>
                                                                <td class="text-center">{{ row.cap_the_3_cbgv }}</td>
                                                            </tr>
                                                        </template>
                                                        
                                                        <!-- Dòng tổng cộng -->
                                                        <tr class="bg-light font-weight-bold">
                                                            <td class="text-center">TC</td>
                                                            <td class="text-center">{{ ketQuaThongKe.length }}</td>
                                                            <td class="text-center">{{ tongCongThongKe?.dg_den_tv_sv || 0 }}</td>
                                                            <td class="text-center">{{ tongCongThongKe?.dg_den_tv_cbgv || 0 }}</td>
                                                            <td class="text-center">{{ tongCongThongKe?.dg_muon_sach_sv || 0 }}</td>
                                                            <td class="text-center">{{ tongCongThongKe?.dg_muon_sach_cbgv || 0 }}</td>
                                                            <td class="text-center">{{ tongCongThongKe?.sach_doc_tai_cho_sv || 0 }}</td>
                                                            <td class="text-center">{{ tongCongThongKe?.sach_doc_tai_cho_cbgv || 0 }}</td>
                                                            <td class="text-center">{{ tongCongThongKe?.sach_muon_ve_nha_sv || 0 }}</td>
                                                            <td class="text-center">{{ tongCongThongKe?.sach_muon_ve_nha_cbgv || 0 }}</td>
                                                            <td class="text-center">{{ tongCongThongKe?.sach_tra_sv || 0 }}</td>
                                                            <td class="text-center">{{ tongCongThongKe?.sach_tra_cbgv || 0 }}</td>
                                                            <td class="text-center">{{ tongCongThongKe?.sach_qua_han_sv || 0 }}</td>
                                                            <td class="text-center">{{ tongCongThongKe?.sach_qua_han_cbgv || 0 }}</td>
                                                            <td class="text-center">{{ tongCongThongKe?.xu_ly_vi_pham_sv || 0 }}</td>
                                                            <td class="text-center">{{ tongCongThongKe?.xu_ly_vi_pham_cbgv || 0 }}</td>
                                                            <td class="text-center">{{ tongCongThongKe?.cap_the_1_sv || 0 }}</td>
                                                            <td class="text-center">{{ tongCongThongKe?.cap_the_1_cbgv || 0 }}</td>
                                                            <td class="text-center">{{ tongCongThongKe?.cap_the_2_sv || 0 }}</td>
                                                            <td class="text-center">{{ tongCongThongKe?.cap_the_2_cbgv || 0 }}</td>
                                                            <td class="text-center">{{ tongCongThongKe?.cap_the_3_sv || 0 }}</td>
                                                            <td class="text-center">{{ tongCongThongKe?.cap_the_3_cbgv || 0 }}</td>
                                                        </tr>
                                                        
                                                        <!-- Dòng tổng số -->
                                                        <tr class="bg-info text-white font-weight-bold">
                                                            <td colspan="2" class="text-center">Tổng cộng</td>
                                                            <td class="text-center" colspan="2">{{ (tongCongThongKe?.dg_den_tv_sv || 0) + (tongCongThongKe?.dg_den_tv_cbgv || 0) }}</td>
                                                            <td class="text-center" colspan="2">{{ (tongCongThongKe?.dg_muon_sach_sv || 0) + (tongCongThongKe?.dg_muon_sach_cbgv || 0) }}</td>
                                                            <td class="text-center" colspan="2">{{ (tongCongThongKe?.sach_doc_tai_cho_sv || 0) + (tongCongThongKe?.sach_doc_tai_cho_cbgv || 0) }}</td>
                                                            <td class="text-center" colspan="2">{{ (tongCongThongKe?.sach_muon_ve_nha_sv || 0) + (tongCongThongKe?.sach_muon_ve_nha_cbgv || 0) }}</td>
                                                            <td class="text-center" colspan="2">{{ (tongCongThongKe?.sach_tra_sv || 0) + (tongCongThongKe?.sach_tra_cbgv || 0) }}</td>
                                                            <td class="text-center" colspan="2">{{ (tongCongThongKe?.sach_qua_han_sv || 0) + (tongCongThongKe?.sach_qua_han_cbgv || 0) }}</td>
                                                            <td class="text-center" colspan="2">{{ (tongCongThongKe?.xu_ly_vi_pham_sv || 0) + (tongCongThongKe?.xu_ly_vi_pham_cbgv || 0) }}</td>
                                                            <td class="text-center" colspan="2">{{ (tongCongThongKe?.cap_the_1_sv || 0) + (tongCongThongKe?.cap_the_1_cbgv || 0) }}</td>
                                                            <td class="text-center" colspan="2">{{ (tongCongThongKe?.cap_the_2_sv || 0) + (tongCongThongKe?.cap_the_2_cbgv || 0) }}</td>
                                                            <td class="text-center" colspan="2">{{ (tongCongThongKe?.cap_the_3_sv || 0) + (tongCongThongKe?.cap_the_3_cbgv || 0) }}</td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </template>
                                    </Card>
                                </div>
                            </div>
                        </template>
                    </Card>
                </div>
            </div>
        </template>
    </ContentWrapper>
</template>

<script>
    export default {
        name: "PageBaoCaoHoatDongTV",
        data() {
            return {
                ds: [],
                currentPage: 1,
                formBaoCao: {
                    tu_ngay: "",
                    den_ngay: "",
                    id_dm_bao_cao: "",
                },
                dsBaoCaoOptions: [],
                isLoading: false,
                ketQuaThongKe: [],
                tongCongThongKe: null,
                headersSachDangMuon: [
                    { key: 'index', label: 'STT', sortable: false, width: '5%' },
                    { key: 'thong_tin_an_pham', label: 'Thông tin ấn phẩm', sortable: false, width: '30%' },
                    { key: 'thong_tin_doc_gia', label: 'Thông tin độc giả', sortable: false, width: '35%' },
                    { key: 'thong_tin_thoi_gian', label: 'Thời gian', sortable: false, width: '30%' },
                ],
                headersSachDaTra: [
                    { key: 'index', label: 'STT', sortable: false, width: '5%' },
                    { key: 'thong_tin_an_pham', label: 'Thông tin ấn phẩm', sortable: false, width: '25%' },
                    { key: 'thong_tin_doc_gia', label: 'Thông tin độc giả', sortable: false, width: '35%' },
                    { key: 'thong_tin_thoi_gian', label: 'Thời gian', sortable: false, width: '35%' },
                ],
                headersSachQuaHan: [
                    { key: 'index', label: 'STT', sortable: false, width: '5%' },
                    { key: 'thong_tin_an_pham', label: 'Thông tin ấn phẩm', sortable: false, width: '25%' },
                    { key: 'thong_tin_doc_gia', label: 'Thông tin độc giả', sortable: false, width: '35%' },
                    { key: 'thong_tin_thoi_gian', label: 'Thời gian và trạng thái', sortable: false, width: '35%' },
                ],
                headersBanDocDenThuVien: [
                    { key: 'index', label: 'STT', sortable: false, width: '5%' },
                    { key: 'thong_tin_doc_gia', label: 'Thông tin bạn đọc', sortable: false, width: '45%' },
                    { key: 'thong_tin_hoat_dong', label: 'Thông tin hoạt động', sortable: false, width: '50%' },
                ],
                headersTinhHinhPhucVuBanDoc: [
                    { key: 'index', label: 'STT', sortable: false, width: '5%' },
                    { key: 'chi_tieu', label: 'Chỉ tiêu', sortable: false, width: '35%' },
                    { key: 'sv', label: 'Sinh viên', sortable: false, width: '20%' },
                    { key: 'cbgv', label: 'Cán bộ - Giảng viên', sortable: false, width: '20%' },
                    { key: 'tong', label: 'Tổng', sortable: false, width: '20%' },
                ],
                activeTab: "ban-doc-den-thu-vien",
                banDocDenThuVien: {
                    tuNgay: this.getDefaultStartDate(),
                    denNgay: this.getDefaultEndDate(),
                    data: [],
                    loading: false,
                },
            }
        },
        watch: {
            'formBaoCao.id_dm_bao_cao': function() {
                // Reset kết quả thống kê khi loại báo cáo thay đổi
                this.ketQuaThongKe = [];
            }
        },                       
        mounted() {
            this.loadDMBaoCao();
            this.thongKeBanDocDenThuVien();
        },
        methods: {
            async loadDMBaoCao() {
            try {
                const response = await axios.get('/api/danh-muc/nghiep-vu-bo-sung/dm-bao-cao/list-dm-bao-cao-select-option');
                if (response.data.status === 200) {
                    this.dsBaoCaoOptions = response.data.data;
                    this.dsBaoCaoOptions = this.dsBaoCaoOptions.map(item => ({
                        value: item.id_dm_bao_cao,
                        text: item.ten_bao_cao
                    }));
                }
            } catch (error) {
                    console.error("Lỗi khi load báo cáo:", error);
                    toastr.error("Đã xảy ra lỗi khi tải danh sách báo cáo!");
                }
            },
            
            validateForm() {
                if (!this.formBaoCao.tu_ngay) {
                    toastr.error("Vui lòng chọn ngày bắt đầu!");
                    return false;
                }
                
                if (!this.formBaoCao.den_ngay) {
                    toastr.error("Vui lòng chọn ngày kết thúc!");
                    return false;
                }
                
                if (!this.formBaoCao.id_dm_bao_cao) {
                    toastr.error("Vui lòng chọn loại báo cáo!");
                    return false;
                }
                
                const tuNgay = new Date(this.formBaoCao.tu_ngay);
                const denNgay = new Date(this.formBaoCao.den_ngay);
                
                if (tuNgay > denNgay) {
                    toastr.error("Ngày bắt đầu phải nhỏ hơn hoặc bằng ngày kết thúc!");
                    return false;
                }
                
                return true;
            },
            
            async baoCao() {
                if (!this.validateForm()) {
                    return;
                }
                
                this.isLoading = true;
                this.ketQuaThongKe = [];
                
                try {
                    if (this.formBaoCao.id_dm_bao_cao == 2) { // Thống kê sách đang mượn
                        const response = await axios.post('/api/quan-ly-dich-vu/bao-cao/thong-ke-sach-dang-muon', {
                            tu_ngay: this.formBaoCao.tu_ngay,
                            den_ngay: this.formBaoCao.den_ngay
                        });
                        
                        if (response.data.status === 200) {
                            this.ketQuaThongKe = response.data.data;
                            this.tongCongThongKe = response.data.tongCongThongKe;
                            toastr.success(`Đã thống kê được ${this.ketQuaThongKe.length} sách đang được mượn`);
                        } else {
                            toastr.error(response.data.message || "Đã xảy ra lỗi khi thống kê!");
                        }
                    } else if (this.formBaoCao.id_dm_bao_cao == 3) { // Thống kê sách đã trả
                        const response = await axios.post('/api/quan-ly-dich-vu/bao-cao/thong-ke-sach-da-tra', {
                            tu_ngay: this.formBaoCao.tu_ngay,
                            den_ngay: this.formBaoCao.den_ngay
                        });
                        
                        if (response.data.status === 200) {
                            this.ketQuaThongKe = response.data.data;
                            this.tongCongThongKe = response.data.tongCongThongKe;
                            toastr.success(`Đã thống kê được ${this.ketQuaThongKe.length} sách đã trả`);
                        } else {
                            toastr.error(response.data.message || "Đã xảy ra lỗi khi thống kê!");
                        }
                    } else if (this.formBaoCao.id_dm_bao_cao == 4) { // Thống kê bạn đọc đến thư viện
                        const response = await axios.post('/api/quan-ly-dich-vu/bao-cao/thong-ke-ban-doc-den-thu-vien', {
                            tu_ngay: this.formBaoCao.tu_ngay,
                            den_ngay: this.formBaoCao.den_ngay
                        });
                        
                        if (response.data.status === 200) {
                            this.ketQuaThongKe = response.data.data;
                            this.tongCongThongKe = response.data.tongCongThongKe;
                            toastr.success(`Đã thống kê được ${this.ketQuaThongKe.length} bạn đọc đến thư viện`);
                        } else {
                            toastr.error(response.data.message || "Đã xảy ra lỗi khi thống kê!");
                        }
                    } else if (this.formBaoCao.id_dm_bao_cao == 5) { // Thống kê sách quá hạn
                        const response = await axios.post('/api/quan-ly-dich-vu/bao-cao/thong-ke-sach-qua-han', {
                            tu_ngay: this.formBaoCao.tu_ngay,
                            den_ngay: this.formBaoCao.den_ngay
                        });
                        
                        if (response.data.status === 200) {
                            this.ketQuaThongKe = response.data.data;
                            this.tongCongThongKe = response.data.tongCongThongKe;
                            toastr.success(`Đã thống kê được ${this.ketQuaThongKe.length} sách đang mượn quá hạn`);
                        } else {
                            toastr.error(response.data.message || "Đã xảy ra lỗi khi thống kê!");
                        }
                    } else if (this.formBaoCao.id_dm_bao_cao == 1) { // Báo cáo tình hình phục vụ bạn đọc
                        const response = await axios.post('/api/quan-ly-dich-vu/bao-cao/thong-ke-tinh-hinh-phuc-vu-ban-doc', {
                            tu_ngay: this.formBaoCao.tu_ngay,
                            den_ngay: this.formBaoCao.den_ngay
                        });
                        
                        if (response.data.status === 200) {
                            this.ketQuaThongKe = response.data.data;
                            this.tongCongThongKe = response.data.tongCongThongKe;
                            toastr.success(`Đã tạo báo cáo tình hình phục vụ bạn đọc thành công`);
                        } else {
                            toastr.error(response.data.message || "Đã xảy ra lỗi khi tạo báo cáo!");
                        }
                    } else {
                        toastr.warning("Loại báo cáo này chưa được hỗ trợ!");
                    }
                } catch (error) {
                    console.error("Lỗi khi thực hiện báo cáo:", error);
                    toastr.error(error.response?.data?.message || "Đã xảy ra lỗi khi thực hiện báo cáo!");
                } finally {
                    this.isLoading = false;
                }
            },
            
            xuatExcelSachDangMuon() {
                if (!this.ketQuaThongKe.length) {
                    toastr.error("Không có dữ liệu để xuất báo cáo!");
                    return;
                }
                
                // Tạo form để submit POST request và tải xuống file Excel
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '/api/quan-ly-dich-vu/bao-cao/xuat-excel-sach-dang-muon';
                form.target = '_blank';
                
                // Thêm input cho từ ngày
                const tuNgayInput = document.createElement('input');
                tuNgayInput.type = 'hidden';
                tuNgayInput.name = 'tu_ngay';
                tuNgayInput.value = this.formBaoCao.tu_ngay;
                form.appendChild(tuNgayInput);
                
                // Thêm input cho đến ngày
                const denNgayInput = document.createElement('input');
                denNgayInput.type = 'hidden';
                denNgayInput.name = 'den_ngay';
                denNgayInput.value = this.formBaoCao.den_ngay;
                form.appendChild(denNgayInput);
                
                // Thêm CSRF token nếu cần
                const tokenElement = document.head.querySelector('meta[name="csrf-token"]');
                if (tokenElement) {
                    const csrfInput = document.createElement('input');
                    csrfInput.type = 'hidden';
                    csrfInput.name = '_token';
                    csrfInput.value = tokenElement.content;
                    form.appendChild(csrfInput);
                }
                
                // Thêm form vào body, submit và sau đó xóa form
                document.body.appendChild(form);
                form.submit();
                document.body.removeChild(form);
            },
            
            xuatExcelSachDaTra() {
                if (!this.ketQuaThongKe.length) {
                    toastr.error("Không có dữ liệu để xuất báo cáo!");
                    return;
                }
                
                // Tạo form để submit POST request và tải xuống file Excel
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '/api/quan-ly-dich-vu/bao-cao/xuat-excel-sach-da-tra';
                form.target = '_blank';
                
                // Thêm input cho từ ngày
                const tuNgayInput = document.createElement('input');
                tuNgayInput.type = 'hidden';
                tuNgayInput.name = 'tu_ngay';
                tuNgayInput.value = this.formBaoCao.tu_ngay;
                form.appendChild(tuNgayInput);
                
                // Thêm input cho đến ngày
                const denNgayInput = document.createElement('input');
                denNgayInput.type = 'hidden';
                denNgayInput.name = 'den_ngay';
                denNgayInput.value = this.formBaoCao.den_ngay;
                form.appendChild(denNgayInput);
                
                // Thêm CSRF token nếu cần
                const tokenElement = document.head.querySelector('meta[name="csrf-token"]');
                if (tokenElement) {
                    const csrfInput = document.createElement('input');
                    csrfInput.type = 'hidden';
                    csrfInput.name = '_token';
                    csrfInput.value = tokenElement.content;
                    form.appendChild(csrfInput);
                }
                
                // Thêm form vào body, submit và sau đó xóa form
                document.body.appendChild(form);
                form.submit();
                document.body.removeChild(form);
            },
            
            xuatExcelBanDocDenThuVien() {
                if (!this.ketQuaThongKe.length) {
                    toastr.error("Không có dữ liệu để xuất báo cáo!");
                    return;
                }
                
                // Tạo form để submit POST request và tải xuống file Excel
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '/api/quan-ly-dich-vu/bao-cao/xuat-excel-ban-doc-den-thu-vien';
                form.target = '_blank';
                
                // Thêm input cho từ ngày
                const tuNgayInput = document.createElement('input');
                tuNgayInput.type = 'hidden';
                tuNgayInput.name = 'tu_ngay';
                tuNgayInput.value = this.formBaoCao.tu_ngay;
                form.appendChild(tuNgayInput);
                
                // Thêm input cho đến ngày
                const denNgayInput = document.createElement('input');
                denNgayInput.type = 'hidden';
                denNgayInput.name = 'den_ngay';
                denNgayInput.value = this.formBaoCao.den_ngay;
                form.appendChild(denNgayInput);
                
                // Thêm CSRF token nếu cần
                const tokenElement = document.head.querySelector('meta[name="csrf-token"]');
                if (tokenElement) {
                    const csrfInput = document.createElement('input');
                    csrfInput.type = 'hidden';
                    csrfInput.name = '_token';
                    csrfInput.value = tokenElement.content;
                    form.appendChild(csrfInput);
                }
                
                // Thêm form vào body, submit và sau đó xóa form
                document.body.appendChild(form);
                form.submit();
                document.body.removeChild(form);
            },
            
            xuatExcelSachQuaHan() {
                if (!this.ketQuaThongKe.length) {
                    toastr.error("Không có dữ liệu để xuất báo cáo!");
                    return;
                }
                
                // Tạo form để submit POST request và tải xuống file Excel
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '/api/quan-ly-dich-vu/bao-cao/xuat-excel-sach-qua-han';
                form.target = '_blank';
                
                // Thêm input cho từ ngày
                const tuNgayInput = document.createElement('input');
                tuNgayInput.type = 'hidden';
                tuNgayInput.name = 'tu_ngay';
                tuNgayInput.value = this.formBaoCao.tu_ngay;
                form.appendChild(tuNgayInput);
                
                // Thêm input cho đến ngày
                const denNgayInput = document.createElement('input');
                denNgayInput.type = 'hidden';
                denNgayInput.name = 'den_ngay';
                denNgayInput.value = this.formBaoCao.den_ngay;
                form.appendChild(denNgayInput);
                
                // Thêm CSRF token nếu cần
                const tokenElement = document.head.querySelector('meta[name="csrf-token"]');
                if (tokenElement) {
                    const csrfInput = document.createElement('input');
                    csrfInput.type = 'hidden';
                    csrfInput.name = '_token';
                    csrfInput.value = tokenElement.content;
                    form.appendChild(csrfInput);
                }
                
                // Thêm form vào body, submit và sau đó xóa form
                document.body.appendChild(form);
                form.submit();
                document.body.removeChild(form);
            },
            
            xuatExcelTinhHinhPhucVuBanDoc() {
                if (!this.ketQuaThongKe.length) {
                    toastr.error("Không có dữ liệu để xuất báo cáo!");
                    return;
                }
                
                // Tạo form để submit POST request và tải xuống file Excel
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '/api/quan-ly-dich-vu/bao-cao/xuat-excel-tinh-hinh-phuc-vu-ban-doc';
                form.target = '_blank';
                
                // Thêm input cho từ ngày
                const tuNgayInput = document.createElement('input');
                tuNgayInput.type = 'hidden';
                tuNgayInput.name = 'tu_ngay';
                tuNgayInput.value = this.formBaoCao.tu_ngay;
                form.appendChild(tuNgayInput);
                
                // Thêm input cho đến ngày
                const denNgayInput = document.createElement('input');
                denNgayInput.type = 'hidden';
                denNgayInput.name = 'den_ngay';
                denNgayInput.value = this.formBaoCao.den_ngay;
                form.appendChild(denNgayInput);
                
                // Thêm CSRF token nếu cần
                const tokenElement = document.head.querySelector('meta[name="csrf-token"]');
                if (tokenElement) {
                    const csrfInput = document.createElement('input');
                    csrfInput.type = 'hidden';
                    csrfInput.name = '_token';
                    csrfInput.value = tokenElement.content;
                    form.appendChild(csrfInput);
                }
                
                // Thêm form vào body, submit và sau đó xóa form
                document.body.appendChild(form);
                form.submit();
                document.body.removeChild(form);
            },
            
            formatDateTime(dateTimeStr) {
                if (!dateTimeStr) return '';
                
                const date = new Date(dateTimeStr);
                if (isNaN(date.getTime())) return dateTimeStr;
                
                return date.toLocaleDateString('vi-VN', {
                    year: 'numeric',
                    month: '2-digit',
                    day: '2-digit'
                });
            },
            thongKeBanDocDenThuVien() {
                if (!this.banDocDenThuVien.tuNgay || !this.banDocDenThuVien.denNgay) {
                    this.$toast.error("Vui lòng chọn đầy đủ khoảng thời gian");
                    return;
                }

                this.banDocDenThuVien.loading = true;
                axios
                    .post("/api/quan-ly-dich-vu/bao-cao/thong-ke-ban-doc-den-thu-vien", {
                        tu_ngay: this.banDocDenThuVien.tuNgay,
                        den_ngay: this.banDocDenThuVien.denNgay,
                    })
                    .then((response) => {
                        if (response.data.status === 200) {
                            this.banDocDenThuVien.data = response.data.data;
                        } else {
                            this.$toast.error(response.data.message);
                        }
                        this.banDocDenThuVien.loading = false;
                    })
                    .catch((error) => {
                        console.error("Lỗi khi lấy thống kê bạn đọc đến thư viện:", error);
                        this.$toast.error(
                            "Có lỗi xảy ra khi lấy thống kê bạn đọc đến thư viện"
                        );
                        this.banDocDenThuVien.loading = false;
                    });
            },
            getDefaultStartDate() {
                const date = new Date();
                date.setDate(date.getDate() - 30);
                return date.toISOString().slice(0, 10);
            },
            getDefaultEndDate() {
                return new Date().toISOString().slice(0, 10);
            },
        }
    }
</script>

<style scoped>
.table-responsive {
    overflow-x: auto;
}

/* CSS cho bảng báo cáo tình hình phục vụ bạn đọc */
.table-bordered th, 
.table-bordered td {
    border: 1px solid #dee2e6;
    padding: 8px;
    vertical-align: middle;
}

.thead-light th {
    background-color: #f8f9fa;
    color: #495057;
}

.align-middle {
    vertical-align: middle !important;
}

.text-center {
    text-align: center !important;
}

.font-weight-bold {
    font-weight: bold !important;
}

.pl-4 {
    padding-left: 1.5rem !important;
}

/* Highlight cho nhóm "Số lần cấp thẻ" */
.cap-the-highlight {
    background-color: rgba(255, 220, 220, 0.5); /* Màu đỏ nhạt */
}

/* Tạo viền đậm hơn xung quanh nhóm các dòng cấp thẻ */
.cap-the-highlight td {
    border: 1px solid #dc3545; /* Viền đỏ cho các ô trong nhóm cấp thẻ */
}
</style>
