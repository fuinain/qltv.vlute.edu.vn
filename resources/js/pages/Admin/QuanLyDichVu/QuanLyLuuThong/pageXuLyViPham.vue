<template>
    <ContentWrapper title="QUẢN LÝ XỬ LÝ VI PHẠM">
        <template #ContentPage>
            <div class="row">
                <div class="col-12">
                    <Card>
                        <template #ContentCardHeader>
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h5 class="card-title">Tìm kiếm bạn đọc</h5>
                                </div>
                            </div>
                        </template>
                        <template #ContentCardBody>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="input-group mb-3">
                                        <input type="text" class="form-control" placeholder="Nhập mã số sinh viên..." v-model="mssv" @keyup.enter="timBanDoc">
                                        <div class="input-group-append">
                                            <button class="btn btn-primary" type="button" @click="timBanDoc" :disabled="isLoading">
                                                <i class="fas fa-search" v-if="!isLoading"></i>
                                                <i class="fas fa-spinner fa-spin" v-else></i>
                                                Tìm kiếm
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Hiển thị thông tin bạn đọc -->
                            <div class="row mt-4" v-if="banDoc">
                                <div class="col-12">
                                    <div class="card card-primary card-outline">
                                        <div class="card-header">
                                            <h3 class="card-title">Thông tin bạn đọc</h3>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <p><strong>Mã số SV:</strong> {{ banDoc.mssv }}</p>
                                                    <p><strong>Họ tên:</strong> {{ banDoc.ho_ten }}</p>
                                                    <p><strong>Số thẻ:</strong> {{ banDoc.so_the }}</p>
                                                </div>
                                                <div class="col-md-6">
                                                    <p><strong>Ngày sinh:</strong> {{ formatDate(banDoc.ngay_sinh) }}</p>
                                                    <p><strong>Số điện thoại:</strong> {{ banDoc.sdt || 'Không có' }}</p>
                                                    <p><strong>Email:</strong> {{ banDoc.email || 'Không có' }}</p>
                                                </div>
                                            </div>
                                            <div class="row mt-3">
                                                <div class="col-md-12">
                                                    <div v-if="dangViPham" class="alert alert-danger">
                                                        <strong>Cảnh báo:</strong> Bạn đọc này đang bị xử lý vi phạm. Hạn xử lý: {{ formatDate(thongTinViPham.ngay_het_han_phat) }}
                                                    </div>
                                                    <div v-else class="alert alert-success">
                                                        <strong>Thông báo:</strong> Bạn đọc này hiện không có vi phạm đang xử lý.
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row mt-3">
                                                <div class="col-md-12 text-right">
                                                    <button class="btn btn-warning" @click="showModalThemViPham">
                                                        <i class="fas fa-exclamation-triangle"></i> Xử lý vi phạm
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Hiển thị lịch sử vi phạm -->
                            <div class="row mt-4" v-if="banDoc && danhSachViPham.length > 0">
                                <div class="col-12">
                                    <div class="card card-danger card-outline">
                                        <div class="card-header">
                                            <h3 class="card-title">Lịch sử vi phạm</h3>
                                        </div>
                                        <div class="card-body">
                                            <Table :columns="headersViPham" :data="danhSachViPham" :hideSearch="false">
                                                <!-- Slot cho cột STT -->
                                                <template v-slot:column-index="{ row, rowIndex }">
                                                    {{ rowIndex + 1 }}
                                                </template>

                                                <!-- Slot cho ngày phạt -->
                                                <template v-slot:column-ngay_phat="{ row }">
                                                    {{ formatDate(row.ngay_phat) }}
                                                </template>

                                                <!-- Slot cho hạn xử lý -->
                                                <template v-slot:column-ngay_het_han_phat="{ row }">
                                                    {{ formatDate(row.ngay_het_han_phat) }}
                                                </template>

                                                <!-- Slot cho hình thức phạt -->
                                                <template v-slot:column-hinh_thuc_phat="{ row }">
                                                    {{ row.hinh_thuc_phat }}
                                                </template>

                                                <!-- Slot cho số tiền -->
                                                <template v-slot:column-so_tien="{ row }">
                                                    {{ formatCurrency(row.so_tien) }}
                                                </template>

                                                <!-- Slot cho lần phạt -->
                                                <template v-slot:column-lan_phat="{ row }">
                                                    {{ row.lan_phat }}
                                                </template>

                                                <!-- Slot cho ghi chú -->
                                                <template v-slot:column-ghi_chu="{ row }">
                                                    {{ row.ghi_chu || 'Không có' }}
                                                </template>

                                                <!-- Slot cho hành động -->
                                                <template v-slot:column-actions="{ row }">
                                                    <button class="btn btn-sm btn-danger" @click="xacNhanXoaViPham(row)">
                                                        <i class="fas fa-trash"></i> Xóa
                                                    </button>
                                                </template>
                                            </Table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </Card>
                </div>
            </div>

            <!-- Modal thêm vi phạm -->
            <div class="modal fade" id="modalThemViPham" tabindex="-1" role="dialog" aria-labelledby="modalThemViPhamLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header bg-warning">
                            <h5 class="modal-title" id="modalThemViPhamLabel">Xử lý vi phạm</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form @submit.prevent="themViPham">
                                <div class="form-group">
                                    <label>Bạn đọc:</label>
                                    <input type="text" class="form-control" :value="banDoc ? banDoc.ho_ten + ' (' + banDoc.mssv + ')' : ''" disabled>
                                </div>
                                <div class="form-group">
                                    <SelectOption
                                        label="Loại vi phạm"
                                        v-model="formViPham.id_phat_ban_doc"
                                        :options="dsPhatBanDocOptions"
                                        placeholder="Chọn loại vi phạm..."
                                        required />
                                </div>
                                <div class="form-group">
                                    <label>Mã ĐKCB</label>
                                    <input
                                        class="form-control"
                                        v-model="formViPham.ma_dkcb"
                                        placeholder="Nhập mã dkcb ..."
                                        required
                                    />
                                </div>
                                <div class="form-group">
                                    <label>Ngày phạt:</label>
                                    <input type="date" class="form-control" :value="ngayHienTai" disabled>
                                </div>
                                <div class="form-group">
                                    <label>Ngày hết hạn phạt: <span class="text-danger">(*)</span></label>
                                    <input type="date" class="form-control" v-model="formViPham.ngay_het_han_phat" required>
                                </div>
                                <div class="form-group">
                                    <label>Số tiền phạt: <span class="text-danger">(*)</span></label>
                                    <input type="number" class="form-control" v-model="formViPham.so_tien" min="0" required>
                                </div>
                                <div class="form-group">
                                    <label>Hình thức phạt:</label>
                                    <input type="text" class="form-control" v-model="formViPham.hinh_thuc_phat" value="Phạt tiền" disabled>
                                </div>
                                <div class="form-group">
                                    <label>Lần phạt:</label>
                                    <input type="number" class="form-control" v-model="formViPham.lan_phat" min="1" required>
                                </div>
                                <div class="form-group">
                                    <label>Ghi chú:</label>
                                    <textarea class="form-control" v-model="formViPham.ghi_chu" rows="3"></textarea>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
                            <button type="button" class="btn btn-primary" @click="themViPham" :disabled="isSubmitting">
                                <i class="fas fa-save" v-if="!isSubmitting"></i>
                                <i class="fas fa-spinner fa-spin" v-else></i>
                                Lưu
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal xác nhận xóa vi phạm -->
            <div class="modal fade" id="modalXoaViPham" tabindex="-1" role="dialog" aria-labelledby="modalXoaViPhamLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header bg-danger">
                            <h5 class="modal-title text-white" id="modalXoaViPhamLabel">Xác nhận xóa vi phạm</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <p>Bạn có chắc chắn muốn xóa vi phạm này không?</p>
                            <p><strong>Lưu ý:</strong> Hành động này không thể hoàn tác.</p>
                            <div v-if="viPhamCanXoa">
                                <p><strong>Ngày phạt:</strong> {{ formatDate(viPhamCanXoa.ngay_phat) }}</p>
                                <p><strong>Số tiền:</strong> {{ formatCurrency(viPhamCanXoa.so_tien) }}</p>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Hủy</button>
                            <button type="button" class="btn btn-danger" @click="xoaViPham" :disabled="isDeleting">
                                <i class="fas fa-trash" v-if="!isDeleting"></i>
                                <i class="fas fa-spinner fa-spin" v-else></i>
                                Xóa
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </ContentWrapper>
</template>

<script>
export default {
    name: "PageXuLyViPham",
    data() {
        return {
            mssv: "",
            isLoading: false,
            isSubmitting: false,
            isDeleting: false,
            banDoc: null,
            dangViPham: false,
            thongTinViPham: null,
            soLanViPham: 0,
            danhSachViPham: [],
            dsPhatBanDocOptions: [],
            ngayHienTai: new Date().toISOString().substr(0, 10),
            formViPham: {
                id_phat_ban_doc: "",
                ngay_het_han_phat: new Date(new Date().setMonth(new Date().getMonth() + 1)).toISOString().substr(0, 10),
                so_tien: 0,
                hinh_thuc_phat: "Phạt tiền",
                lan_phat: 1,
                ghi_chu: "",
                ma_dkcb: '',
            },
            headersViPham: [
                { key: 'index', label: 'STT', width: '5%' },
                { key: 'ngay_phat', label: 'Ngày phạt', width: '10%' },
                { key: 'ngay_het_han_phat', label: 'Hạn xử lý', width: '10%' },
                { key: 'hinh_thuc_phat', label: 'Hình thức phạt', width: '15%' },
                { key: 'so_tien', label: 'Số tiền', width: '10%' },
                { key: 'lan_phat', label: 'Lần phạt', width: '10%' },
                { key: 'ten_loai_phat', label: 'Lý do phạt', width: '' },
                { key: 'ma_dkcb', label: 'ĐKCB', width: '' },
                { key: 'ghi_chu', label: 'Ghi chú', width: '25%' },
                { key: 'actions', label: 'Thao tác', width: '15%' }
            ],
            viPhamCanXoa: null
        };
    },
    mounted() {
        this.loadDSPhatBanDoc();
    },
    methods: {
        // Tìm kiếm bạn đọc
        async timBanDoc() {
            if (!this.mssv) {
                toastr.error("Vui lòng nhập mã số sinh viên!");
                return;
            }

            this.isLoading = true;
            try {
                const response = await axios.get(`/api/quan-ly-dich-vu/xu-ly-vi-pham/ban-doc/${this.mssv}`);

                if (response.data.status === 200) {
                    this.banDoc = response.data.data.ban_doc;
                    this.dangViPham = response.data.data.dang_vi_pham;
                    this.thongTinViPham = response.data.data.thong_tin_vi_pham;
                    this.soLanViPham = response.data.data.so_lan_vi_pham;

                    // Cập nhật lần phạt
                    this.formViPham.lan_phat = this.soLanViPham + 1;

                    // Lấy danh sách vi phạm
                    this.layDanhSachViPham();
                } else {
                    toastr.error(response.data.message || "Có lỗi xảy ra khi tìm kiếm bạn đọc!");
                }
            } catch (error) {
                console.error("Lỗi khi tìm kiếm bạn đọc:", error);
                toastr.error(error.response?.data?.message || "Có lỗi xảy ra khi tìm kiếm bạn đọc!");
            } finally {
                this.isLoading = false;
            }
        },

        // Lấy danh sách vi phạm
        async layDanhSachViPham() {
            if (!this.mssv) return;

            try {
                const response = await axios.get(`/api/quan-ly-dich-vu/xu-ly-vi-pham/danh-sach-vi-pham/${this.mssv}`);

                if (response.data.status === 200) {
                    this.danhSachViPham = response.data.data;
                } else {
                    toastr.error(response.data.message || "Có lỗi xảy ra khi lấy danh sách vi phạm!");
                }
            } catch (error) {
                console.error("Lỗi khi lấy danh sách vi phạm:", error);
                toastr.error(error.response?.data?.message || "Có lỗi xảy ra khi lấy danh sách vi phạm!");
            }
        },

        // Lấy danh sách loại phạt bạn đọc
        async loadDSPhatBanDoc() {
            try {
                const response = await axios.get('/api/danh-muc/nghiep-vu-luu-thong/phat-ban-doc/list-phat-ban-doc-select-option');

                if (response.data.status === 200) {
                    this.dsPhatBanDocOptions = response.data.data.map(item => ({
                        value: item.id_phat_ban_doc,
                        text: item.ten_loai_phat
                    }));
                } else {
                    toastr.error(response.data.message || "Có lỗi xảy ra khi lấy danh sách loại phạt!");
                }
            } catch (error) {
                console.error("Lỗi khi lấy danh sách loại phạt:", error);
                toastr.error(error.response?.data?.message || "Có lỗi xảy ra khi lấy danh sách loại phạt!");
            }
        },

        // Hiển thị modal thêm vi phạm
        showModalThemViPham() {
            if (!this.banDoc) {
                toastr.error("Vui lòng tìm kiếm bạn đọc trước!");
                return;
            }

            // Reset form
            this.formViPham = {
                id_phat_ban_doc: "",
                ngay_het_han_phat: new Date(new Date().setMonth(new Date().getMonth() + 1)).toISOString().substr(0, 10),
                so_tien: 0,
                hinh_thuc_phat: "Phạt tiền",
                lan_phat: this.soLanViPham + 1,
                ghi_chu: ""
            };

            // Hiển thị modal
            $('#modalThemViPham').modal('show');
        },

        // Thêm vi phạm
        async themViPham() {
            if (!this.banDoc) {
                toastr.error("Vui lòng tìm kiếm bạn đọc trước!");
                return;
            }

            if (!this.formViPham.id_phat_ban_doc) {
                toastr.error("Vui lòng chọn loại vi phạm!");
                return;
            }

            if (!this.formViPham.ngay_het_han_phat) {
                toastr.error("Vui lòng chọn ngày hết hạn phạt!");
                return;
            }

            // Kiểm tra ngày hết hạn phải lớn hơn ngày hiện tại
            const ngayHienTai = new Date();
            const ngayHetHan = new Date(this.formViPham.ngay_het_han_phat);

            if (ngayHetHan <= ngayHienTai) {
                toastr.error("Ngày hết hạn phạt phải lớn hơn ngày hiện tại!");
                return;
            }

            this.isSubmitting = true;
            try {
                const response = await axios.post('/api/quan-ly-dich-vu/xu-ly-vi-pham/them-vi-pham', {
                    id_ban_doc: this.banDoc.id_doc_gia,
                    id_phat_ban_doc: this.formViPham.id_phat_ban_doc,
                    ngay_het_han_phat: this.formViPham.ngay_het_han_phat,
                    so_tien: this.formViPham.so_tien,
                    hinh_thuc_phat: this.formViPham.hinh_thuc_phat,
                    lan_phat: this.formViPham.lan_phat,
                    ma_dkcb: this.formViPham.ma_dkcb,
                    ghi_chu: this.formViPham.ghi_chu
                });

                if (response.data.status === 200) {
                    toastr.success("Thêm vi phạm thành công!");
                    $('#modalThemViPham').modal('hide');

                    // Cập nhật lại thông tin
                    this.timBanDoc();
                } else {
                    toastr.error(response.data.message || "Có lỗi xảy ra khi thêm vi phạm!");
                }
            } catch (error) {
                console.error("Lỗi khi thêm vi phạm:", error);
                toastr.error(error.response?.data?.message || "Có lỗi xảy ra khi thêm vi phạm!");
            } finally {
                this.isSubmitting = false;
            }
        },

        // Xác nhận xóa vi phạm
        xacNhanXoaViPham(viPham) {
            this.viPhamCanXoa = viPham;
            $('#modalXoaViPham').modal('show');
        },

        // Xóa vi phạm
        async xoaViPham() {
            if (!this.viPhamCanXoa) {
                toastr.error("Không có thông tin vi phạm để xóa!");
                return;
            }

            this.isDeleting = true;
            try {
                const response = await axios.delete(`/api/quan-ly-dich-vu/xu-ly-vi-pham/xoa-vi-pham/${this.viPhamCanXoa.id_xu_ly_vi_pham}`);

                if (response.data.status === 200) {
                    toastr.success("Xóa vi phạm thành công!");
                    $('#modalXoaViPham').modal('hide');

                    // Cập nhật lại thông tin
                    this.layDanhSachViPham();
                    this.timBanDoc();
                } else {
                    toastr.error(response.data.message || "Có lỗi xảy ra khi xóa vi phạm!");
                }
            } catch (error) {
                console.error("Lỗi khi xóa vi phạm:", error);
                toastr.error(error.response?.data?.message || "Có lỗi xảy ra khi xóa vi phạm!");
            } finally {
                this.isDeleting = false;
            }
        },

        // Format date
        formatDate(dateString) {
            if (!dateString) return '';

            const date = new Date(dateString);
            return date.toLocaleDateString('vi-VN');
        },

        // Format currency
        formatCurrency(value) {
            if (!value) return '0 VNĐ';

            return new Intl.NumberFormat('vi-VN', {
                style: 'currency',
                currency: 'VND'
            }).format(value);
        }
    }
};
</script>

<style scoped>
.form-control:disabled {
    background-color: #e9ecef;
}
</style>
