<template>
    <ContentWrapper title="BÁO CÁO NHẬN SÁCH PHÂN KHO">
        <template #ContentPage>
            <div class="row">
                <div class="col-12">
                    <Card>
                        <template #ContentCardBody>
                            <div class="row mb-4">
                                <div class="col-md-12">
                                    <Card title="Tra cứu sách từ đơn nhận">
                                        <template #ContentCardBody>
                                            <div class="form-group row">
                                                <label class="col-sm-2 col-form-label">Từ đơn nhận: <span
                                                        class="text-danger">(*)</span></label>
                                                <div class="col-sm-4">
                                                    <input type="number" class="form-control"
                                                        v-model.number="formTraCuu.don_nhan_bat_dau" min="1" />
                                                    <small v-if="errors.don_nhan_bat_dau" class="text-danger">{{
                                                        errors.don_nhan_bat_dau }}</small>
                                                </div>
                                                <label class="col-sm-2 col-form-label">Đến đơn nhận: <span
                                                        class="text-danger">(*)</span></label>
                                                <div class="col-sm-4">
                                                    <input type="number" class="form-control"
                                                        v-model.number="formTraCuu.don_nhan_ket_thuc" min="1" />
                                                    <small v-if="errors.don_nhan_ket_thuc" class="text-danger">{{
                                                        errors.don_nhan_ket_thuc }}</small>
                                                </div>
                                            </div>
                                            <div class="text-right">
                                                <button type="button" class="btn btn-primary" @click="traCuuSach"
                                                    :disabled="isLoading">
                                                    <i class="fas fa-search" v-if="!isLoadingTraCuu"></i>
                                                    <i class="fas fa-spinner fa-spin" v-else></i>&nbsp;
                                                    Tra cứu
                                                </button>
                                            </div>
                                        </template>
                                    </Card>
                                </div>
                            </div>

                            <div class="row" v-if="danhSachSach.length > 0">
                                <div class="col-md-12">
                                    <Card title="Báo cáo nhận sách phân kho">
                                        <template #ContentCardBody>
                                            <div class="mb-3 text-right">
                                                <button type="button" class="btn btn-success" @click="xuatBaoCao"
                                                    :disabled="isLoading || danhSachSach.length === 0">
                                                    <i class="fas fa-file-excel" v-if="!isLoadingXuat"></i>
                                                    <i class="fas fa-spinner fa-spin" v-else></i>&nbsp;
                                                    Xuất báo cáo
                                                </button>
                                            </div>

                                            <Table :columns="headersSach" :data="danhSachSach">
                                                <!-- Slot cho cột STT -->
                                                <template v-slot:column-index="{ row, rowIndex }">
                                                    {{ rowIndex + 1 }}
                                                </template>

                                                <!-- Slot cho cột id_don_nhan -->
                                                <template v-slot:column-id_don_nhan="{ row }">
                                                    {{ row.id_don_nhan }}
                                                </template>
                                                
                                                <!-- Slot cho cột id_sach -->
                                                <template v-slot:column-id_sach="{ row }">
                                                    {{ row.id_sach }}
                                                </template>

                                                <!-- Slot cho cột nhan_de -->
                                                <template v-slot:column-nhan_de="{ row }">
                                                    <strong>{{ row.nhan_de }}</strong>
                                                </template>

                                                <!-- Slot cho cột tác giả -->
                                                <template v-slot:column-tac_gia="{ row }">
                                                    {{ row.tac_gia }}
                                                </template>

                                                <!-- Slot cho cột thông tin xuất bản -->
                                                <template v-slot:column-thong_tin_xuat_ban="{ row }">
                                                    <div>
                                                        <span v-if="row.noi_xuat_ban">{{ row.noi_xuat_ban }}</span>
                                                        <span v-if="row.noi_xuat_ban && (row.nha_xuat_ban || row.nam_xuat_ban)"> - </span>
                                                        <span v-if="row.nha_xuat_ban">{{ row.nha_xuat_ban }}</span>
                                                        <span v-if="row.nha_xuat_ban && row.nam_xuat_ban"> - </span>
                                                        <span v-if="row.nam_xuat_ban">{{ row.nam_xuat_ban }}</span>
                                                    </div>
                                                </template>

                                                <!-- Slot cho cột phân loại -->
                                                <template v-slot:column-phan_loai="{ row }">
                                                    <span v-if="row.phan_loai_1 || row.phan_loai_2">
                                                        {{ row.phan_loai_1 }} <span
                                                            v-if="row.phan_loai_1 && row.phan_loai_2">/</span> {{
                                                        row.phan_loai_2 }}
                                                    </span>
                                                    <span v-else class="text-muted font-italic">Chưa có</span>
                                                </template>

                                                <!-- Slot cho cột giá -->
                                                <template v-slot:column-gia="{ row }">
                                                    {{ formatCurrency(row.gia) }}
                                                </template>

                                                <!-- Slot cho cột số lượng -->
                                                <template v-slot:column-so_luong="{ row }">
                                                    {{ row.so_luong }}
                                                </template>

                                                <!-- Slot cho cột số lượng đăng ký -->
                                                <template v-slot:column-so_luong_dang_ky="{ row }">
                                                    {{ row.dkcb_list ? row.dkcb_list.length : 0 }}
                                                </template>

                                                <!-- Slot cho cột DKCB -->
                                                <template v-slot:column-dkcb_list="{ row }">
                                                    <div v-if="row.dkcb_list && row.dkcb_list.length > 0">
                                                        <div v-for="(dkcb, index) in row.dkcb_list" :key="index">
                                                            {{ dkcb }}
                                                        </div>
                                                    </div>
                                                    <span v-else class="text-muted font-italic">Chưa gán DKCB</span>
                                                </template>

                                                <!-- Slot cho cột thành tiền -->
                                                <template v-slot:column-thanh_tien="{ row }">
                                                    {{ formatCurrency(row.thanh_tien) }}
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
    name: "pageBCNhanSachPhanKho",
    data() {
        return {
            danhSachSach: [],
            headersSach: [
                { key: 'index', label: 'TT', sortable: false },
                { key: 'id_don_nhan', label: 'MĐ', sortable: true },
                { key: 'id_sach', label: 'MFN', sortable: true },
                { key: 'nhan_de', label: 'Nhan đề', sortable: true },
                { key: 'tac_gia', label: 'Tác giả', sortable: true },
                { key: 'thong_tin_xuat_ban', label: 'Thông tin xuất bản', sortable: false },
                { key: 'phan_loai', label: 'Phân loại', sortable: true },
                { key: 'gia', label: 'Giá', sortable: true },
                { key: 'so_luong', label: 'SL', sortable: true },
                { key: 'so_luong_dang_ky', label: 'SLĐK', sortable: true },
                { key: 'dkcb_list', label: 'ĐKCB', sortable: false },
                { key: 'thanh_tien', label: 'T.Tiền', sortable: true }
            ],
            formTraCuu: {
                don_nhan_bat_dau: 1,
                don_nhan_ket_thuc: 1
            },
            isLoading: false,
            isLoadingTraCuu: false,
            isLoadingXuat: false,
            errors: {
                don_nhan_bat_dau: '',
                don_nhan_ket_thuc: ''
            }
        };
    },
    methods: {
        resetErrors() {
            this.errors = {
                don_nhan_bat_dau: '',
                don_nhan_ket_thuc: ''
            };
        },

        validateFormTraCuu() {
            this.resetErrors();
            let isValid = true;

            if (!this.formTraCuu.don_nhan_bat_dau || this.formTraCuu.don_nhan_bat_dau < 1) {
                this.errors.don_nhan_bat_dau = 'Vui lòng nhập số đơn nhận bắt đầu hợp lệ';
                isValid = false;
            }

            if (!this.formTraCuu.don_nhan_ket_thuc || this.formTraCuu.don_nhan_ket_thuc < 1) {
                this.errors.don_nhan_ket_thuc = 'Vui lòng nhập số đơn nhận kết thúc hợp lệ';
                isValid = false;
            }

            if (this.formTraCuu.don_nhan_bat_dau > this.formTraCuu.don_nhan_ket_thuc) {
                this.errors.don_nhan_bat_dau = 'Đơn nhận bắt đầu phải nhỏ hơn hoặc bằng đơn nhận kết thúc';
                isValid = false;
            }

            return isValid;
        },

        formatCurrency(value) {
            if (!value) return '0';
            return new Intl.NumberFormat('vi-VN').format(value);
        },

        async traCuuSach() {
            if (!this.validateFormTraCuu()) {
                return;
            }

            this.isLoadingTraCuu = true;
            this.isLoading = true;
            this.danhSachSach = [];

            try {
                // Dùng cùng API với pageInNhanPhanLoai
                const res = await axios.post('/api/quan-ly-an-pham/in-nhan/phan-loai/danh-sach-sach', this.formTraCuu);

                if (res.data.status === 200) {
                    // Cần bổ sung thêm thông tin gia và thanh_tien
                    const sachIds = res.data.data.map(item => item.id_sach);
                    
                    // Lấy thông tin chi tiết cho từng sách
                    const sachDetails = await Promise.all(
                        sachIds.map(id => axios.get(`/api/quan-ly-an-pham/nhan-sach/don-nhan/chi-tiet-don-nhan/sach/${id}`))
                    );
                    
                    // Kết hợp thông tin
                    this.danhSachSach = res.data.data.map((sach, index) => {
                        const detail = sachDetails[index].data.data;
                        return {
                            ...sach,
                            gia: detail.gia || 0,
                            thanh_tien: detail.thanh_tien || 0,
                            noi_xuat_ban: detail.noi_xuat_ban || '',
                            nha_xuat_ban: detail.nha_xuat_ban || ''
                        };
                    });
                    
                    toastr.success(`Đã tìm thấy ${this.danhSachSach.length} sách từ ${res.data.don_nhan_ids.length} đơn nhận`);
                } else {
                    toastr.error(res.data.message || 'Lỗi khi tra cứu sách!');
                }
            } catch (e) {
                if (e.response && e.response.data) {
                    if (e.response.data.message) {
                        toastr.error(e.response.data.message);
                    }

                    // Xử lý lỗi validation từ Laravel
                    if (e.response.data.errors) {
                        const errors = e.response.data.errors;
                        if (errors.don_nhan_bat_dau) {
                            this.errors.don_nhan_bat_dau = errors.don_nhan_bat_dau[0];
                        }
                        if (errors.don_nhan_ket_thuc) {
                            this.errors.don_nhan_ket_thuc = errors.don_nhan_ket_thuc[0];
                        }
                    }
                } else {
                    toastr.error('Lỗi khi tra cứu sách!');
                }
                console.error('Lỗi:', e);
            } finally {
                this.isLoadingTraCuu = false;
                this.isLoading = false;
            }
        },

        async xuatBaoCao() {
            if (this.danhSachSach.length === 0) {
                toastr.error('Không có dữ liệu để xuất báo cáo!');
                return;
            }

            this.isLoadingXuat = true;
            this.isLoading = true;

            try {
                // Tạo URL với tham số query
                const baseUrl = '/api/quan-ly-an-pham/nhan-sach/bao-cao-nhan-sach-phan-kho/export';
                
                // Sử dụng form submit để đảm bảo tải xuống hoạt động trên mọi trình duyệt
                const form = document.createElement('form');
                form.method = 'GET';
                form.action = baseUrl;
                form.target = '_blank';
                
                // Thêm các trường dữ liệu
                const donBatDauInput = document.createElement('input');
                donBatDauInput.type = 'hidden';
                donBatDauInput.name = 'don_nhan_bat_dau';
                donBatDauInput.value = this.formTraCuu.don_nhan_bat_dau;
                form.appendChild(donBatDauInput);
                
                const donKetThucInput = document.createElement('input');
                donKetThucInput.type = 'hidden';
                donKetThucInput.name = 'don_nhan_ket_thuc';
                donKetThucInput.value = this.formTraCuu.don_nhan_ket_thuc;
                form.appendChild(donKetThucInput);
                
                // Thêm danh sách sách
                const sachInput = document.createElement('input');
                sachInput.type = 'hidden';
                sachInput.name = 'danhSachSach';
                sachInput.value = JSON.stringify(this.danhSachSach);
                form.appendChild(sachInput);
                
                // Thêm form vào body và submit
                document.body.appendChild(form);
                form.submit();
                
                // Xóa form sau khi submit
                setTimeout(() => {
                    document.body.removeChild(form);
                }, 100);
                
                toastr.success('Đã tạo báo cáo thành công!');
            } catch (e) {
                console.error('Lỗi khi xuất báo cáo:', e);
                toastr.error('Lỗi khi xuất báo cáo!');
            } finally {
                this.isLoadingXuat = false;
                this.isLoading = false;
            }
        }
    }
};
</script>

<style scoped>
.form-control.is-invalid {
    border-color: #dc3545;
}
</style>
