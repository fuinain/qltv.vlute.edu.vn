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
                                    <button type="button" class="btn btn-success ml-2" @click="xuatExcel" :disabled="isLoading || !ketQuaThongKe.length">
                                        <i class="fas fa-file-excel"></i>&nbsp;
                                        Xuất Excel
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
                                        <template #ContentCardBody>
                                            <Table :columns="headersSachDangMuon" :data="ketQuaThongKe">
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
                                        <template #ContentCardBody>
                                            <Table :columns="headersSachDaTra" :data="ketQuaThongKe">
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
                                        <template #ContentCardBody>
                                            <Table :columns="headersSachQuaHan" :data="ketQuaThongKe">
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
            }
        },                       
        mounted() {
            this.loadDMBaoCao();
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
                            toastr.success(`Đã thống kê được ${this.ketQuaThongKe.length} sách đã trả`);
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
                            toastr.success(`Đã thống kê được ${this.ketQuaThongKe.length} sách đang mượn quá hạn`);
                        } else {
                            toastr.error(response.data.message || "Đã xảy ra lỗi khi thống kê!");
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
            
            xuatExcel() {
                if (this.formBaoCao.id_dm_bao_cao == 2) { // Thống kê sách đang mượn
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
                } else if (this.formBaoCao.id_dm_bao_cao == 3) { // Thống kê sách đã trả
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
                } else if (this.formBaoCao.id_dm_bao_cao == 5) { // Thống kê sách quá hạn
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
                } else {
                    toastr.warning("Loại báo cáo này chưa hỗ trợ xuất Excel!");
                }
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
            }
        }
    }
</script>

<style scoped>
.table-responsive {
    overflow-x: auto;
}
</style>
