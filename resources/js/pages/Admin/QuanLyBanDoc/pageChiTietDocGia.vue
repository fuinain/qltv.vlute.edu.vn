<template>
    <ContentWrapper title="CHI TIẾT BẠN ĐỌC">
        <template #ContentPage>
            <div class="row">
                <div class="col-12">
                    <Card>
                        <template #ContentCardHeader>
                            <div class="d-flex justify-content-between">
                                <h5>Thông tin bạn đọc</h5>
                                <div>
                                    <button type="button" class="btn btn-secondary me-2" @click="quayLai">
                                        <i class="fas fa-arrow-left"></i>&nbsp;
                                        Quay lại
                                    </button>
                                    <button type="button" class="btn btn-primary" @click="suaBanDoc">
                                        <i class="fas fa-edit"></i>&nbsp;
                                        Chỉnh sửa
                                    </button>
                                </div>
                            </div>
                        </template>
                        <template #ContentCardBody>
                            <div v-if="loading" class="text-center py-5">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Đang tải...</span>
                                </div>
                                <p class="mt-2">Đang tải thông tin bạn đọc...</p>
                            </div>
                            
                            <div v-else-if="docGia" class="row">
                                <div class="col-md-6">
                                    <div class="card mb-4">
                                        <div class="card-header bg-primary text-white">
                                            <h6 class="mb-0"><i class="fas fa-user me-2"></i>Thông tin cá nhân</h6>
                                        </div>
                                        <div class="card-body">
                                            <table class="table table-bordered">
                                                <tbody>
                                                    <tr>
                                                        <th style="width: 35%">Họ tên</th>
                                                        <td>{{ docGia.ho_ten }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>MSSV</th>
                                                        <td>{{ docGia.mssv }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>Ngày sinh</th>
                                                        <td>{{ formatDate(docGia.ngay_sinh) }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>Email</th>
                                                        <td>{{ docGia.email }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>Hộ khẩu</th>
                                                        <td>{{ docGia.ho_khau || 'Không có' }}</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="card mb-4">
                                        <div class="card-header bg-info text-white">
                                            <h6 class="mb-0"><i class="fas fa-graduation-cap me-2"></i>Thông tin học tập</h6>
                                        </div>
                                        <div class="card-body">
                                            <table class="table table-bordered">
                                                <tbody>
                                                    <tr>
                                                        <th style="width: 35%">Mã lớp</th>
                                                        <td>{{ docGia.ma_lop }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>Tên lớp</th>
                                                        <td>{{ docGia.ten_lop }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>Niên khóa</th>
                                                        <td>{{ docGia.nien_khoa || 'Không có' }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>Khoa/Đơn vị</th>
                                                        <td>{{ tenDonVi }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>Chuyên ngành</th>
                                                        <td>{{ tenChuyenNganh }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>Đối tượng bạn đọc</th>
                                                        <td>{{ tenDoiTuongBanDoc }}</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="card mb-4">
                                        <div class="card-header bg-success text-white">
                                            <h6 class="mb-0"><i class="fas fa-id-card me-2"></i>Thông tin thẻ</h6>
                                        </div>
                                        <div class="card-body">
                                            <table class="table table-bordered">
                                                <tbody>
                                                    <tr>
                                                        <th style="width: 35%">Số thẻ</th>
                                                        <td>{{ docGia.so_the }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>Năm cấp thẻ</th>
                                                        <td>{{ formatDate(docGia.ngay_cap_the) }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>Hạn thẻ</th>
                                                        <td>{{ formatDate(docGia.han_the) }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>Lần cấp thẻ</th>
                                                        <td>{{ docGia.lan_cap_the }}</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="card mb-4">
                                        <div class="card-header bg-warning text-dark">
                                            <h6 class="mb-0"><i class="fas fa-sticky-note me-2"></i>Thông tin khác</h6>
                                        </div>
                                        <div class="card-body">
                                            <table class="table table-bordered">
                                                <tbody>
                                                    <tr>
                                                        <th style="width: 35%">Ghi chú</th>
                                                        <td>{{ docGia.ghi_chu || 'Không có' }}</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div v-else class="alert alert-danger">
                                Không tìm thấy thông tin bạn đọc
                            </div>
                        </template>
                    </Card>
                </div>
            </div>
        </template>
    </ContentWrapper>
    
    <!-- Modal chỉnh sửa bạn đọc -->
    <Modal ref="modal">
        <div class="row">
            <div class="col-md-6">
                <Input v-model="banDocEdit.ho_ten" label="Họ tên" placeholder="Nhập họ tên..." type="text" required/>
            </div>
            <div class="col-md-6">
                <Input v-model="banDocEdit.mssv" label="MSSV" placeholder="Nhập MSSV..." type="text" required/>
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-md-6">
                <Input v-model="banDocEdit.ma_lop" label="Mã lớp" placeholder="Nhập mã lớp..." type="text" required/>
            </div>
            <div class="col-md-6">
                <Input v-model="banDocEdit.ten_lop" label="Tên lớp" placeholder="Nhập tên lớp..." type="text" required/>
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-md-6">
                <Input v-model="banDocEdit.so_the" label="Số thẻ" placeholder="Nhập số thẻ..." type="text" required/>
            </div>
            <div class="col-md-6">
                <Input v-model="banDocEdit.ngay_sinh" label="Ngày sinh" placeholder="Nhập ngày sinh (DD/MM/YYYY)..." type="text"/>
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-md-6">
                <Input v-model="banDocEdit.ngay_cap_the" label="Năm cấp thẻ" placeholder="Nhập năm cấp thẻ..." type="text"/>
            </div>
            <div class="col-md-6">
                <Input v-model="banDocEdit.han_the" label="Năm hết hạn" placeholder="Nhập năm hết hạn..." type="text"/>
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-md-6">
                <Input v-model="banDocEdit.nien_khoa" label="Niên khóa" placeholder="Nhập niên khóa..." type="text"/>
            </div>
            <div class="col-md-6">
                <Input v-model="banDocEdit.email" label="Email" placeholder="Nhập email..." type="text"/>
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-md-6">
                <SelectOption v-model="banDocEdit.ma_so_quy_uoc" label="Đối tượng bạn đọc" placeholder="Chọn đối tượng bạn đọc..." :options="dsDoiTuongBanDocOptions" required/>
            </div>
            <div class="col-md-6">
                <SelectOption v-model="banDocEdit.id_chuyen_nganh" label="Chuyên ngành" placeholder="Chọn chuyên ngành..." :options="dsChuyenNganhOptions" required/>
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-md-12">
                <Input v-model="banDocEdit.ho_khau" label="Hộ khẩu" placeholder="Nhập hộ khẩu..." type="text"/>
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-md-12">
                <Input v-model="banDocEdit.ghi_chu" label="Ghi chú" placeholder="Nhập ghi chú..." type="text"/>
            </div>
        </div>
    </Modal>
</template>

<script>
import SelectOption from "@/components/ui/SelectOption.vue";

export default {
    name: "pageChiTietDocGia",
    components: {SelectOption},
    data() {
        return {
            loading: true,
            docGia: null,
            banDocEdit: {
                ho_ten: "",
                mssv: "",
                ma_lop: "",
                ten_lop: "",
                so_the: "",
                ngay_sinh: "",
                ngay_cap_the: "",
                han_the: "",
                lan_cap_the: 1,
                ho_khau: "",
                ghi_chu: "",
                rut_han: 1,
                nien_khoa: "",
                ma_so_quy_uoc: "",
                id_chuyen_nganh: "",
                email: ""
            },
            dsDoiTuongBanDoc: [],
            dsDoiTuongBanDocOptions: [],
            dsChuyenNganh: [],
            dsChuyenNganhOptions: [],
        };
    },
    computed: {
        tenDoiTuongBanDoc() {
            const doiTuong = this.dsDoiTuongBanDoc.find(item => 
                item.ma_so_quy_uoc === this.docGia?.ma_so_quy_uoc);
            return doiTuong?.ten_doi_tuong_ban_doc || 'Không xác định';
        },
        tenChuyenNganh() {
            const chuyenNganh = this.dsChuyenNganh.find(item => 
                item.id_chuyen_nganh === this.docGia?.id_chuyen_nganh);
            return chuyenNganh?.ten_chuyen_nganh || 'Không xác định';
        },
        tenDonVi() {
            return this.docGia?.ten_don_vi || 'Không xác định';
        }
    },
    created() {
        this.fetchData();
        this.loadDoiTuongBanDoc();
        this.loadChuyenNganh();
    },
    methods: {
        async fetchData() {
            this.loading = true;
            try {
                const id = this.$route.params.id;
                if (!id) {
                    this.loading = false;
                    return;
                }
                
                const response = await axios.get(`/api/quan-ly-ban-doc/doc-gia/${id}`);
                if (response.data.status === 200) {
                    this.docGia = response.data.data;
                }
            } catch (error) {
                console.error("Lỗi khi tải thông tin bạn đọc:", error);
                toastr.error("Không thể tải thông tin bạn đọc");
            } finally {
                this.loading = false;
            }
        },

        async loadDoiTuongBanDoc() {
            try {
                const response = await axios.get('/api/quan-ly-ban-doc/doc-gia/list-dtbd-for-sync');
                if (response.data.status === 200) {
                    this.dsDoiTuongBanDoc = response.data.data;
                    this.dsDoiTuongBanDocOptions = this.dsDoiTuongBanDoc.map(item => ({
                        value: item.ma_so_quy_uoc,
                        text: item.ten_doi_tuong_ban_doc
                    }));
                }
            } catch (error) {
                console.error("Lỗi khi load đối tượng bạn đọc:", error);
            }
        },

        async loadChuyenNganh() {
            try {
                const response = await axios.get('/api/quan-ly-ban-doc/doc-gia/list-chuyen-nganh-for-sync');
                if (response.data.status === 200) {
                    this.dsChuyenNganh = response.data.data;
                    this.dsChuyenNganhOptions = this.dsChuyenNganh.map(item => ({
                        value: item.id_chuyen_nganh,
                        text: item.ten_chuyen_nganh
                    }));
                }
            } catch (error) {
                console.error("Lỗi khi load chuyên ngành:", error);
            }
        },
        
        quayLai() {
            this.$router.push({ name: 'pageDocGia' });
        },
        
        suaBanDoc() {
            if (!this.docGia) return;
            
            // Chuyển đổi định dạng ngày
            const formatDateToDisplay = (dateString) => {
                if (!dateString) return '';
                const date = new Date(dateString);
                return date.toLocaleDateString('vi-VN', { day: '2-digit', month: '2-digit', year: 'numeric' });
            };
            
            // Lấy chỉ năm từ ngày cấp thẻ và hạn thẻ
            const getYearFromDate = (dateString) => {
                if (!dateString) return '';
                return new Date(dateString).getFullYear().toString();
            };
            
            // Set tiêu đề và điền dữ liệu hiện có vào form
            this.$refs.modal.$data.title = "Cập nhật bạn đọc";
            this.$refs.modal.$data.save = "Cập nhật";
            
            this.banDocEdit = {
                ...this.docGia,
                ngay_sinh: formatDateToDisplay(this.docGia.ngay_sinh),
                ngay_cap_the: getYearFromDate(this.docGia.ngay_cap_the),
                han_the: getYearFromDate(this.docGia.han_the),
            };

            // Mở modal để chỉnh sửa
            this.$refs.modal.openModal().then(async (confirmed) => {
                if (!confirmed) return;
                
                try {
                    const response = await axios.put(`/api/quan-ly-ban-doc/doc-gia/${this.docGia.id_doc_gia}`, this.banDocEdit);
                    if (response.data.status === 200) {
                        toastr.success(response.data.message);
                        this.fetchData();
                        this.$refs.modal.closeModal();
                    }
                } catch (error) {
                    console.error("Lỗi khi cập nhật:", error);
                    if (error.response && error.response.data && error.response.data.errors) {
                        const errors = Object.values(error.response.data.errors).flat();
                        errors.forEach(err => toastr.error(err));
                    } else {
                        toastr.error("Cập nhật thất bại!");
                    }
                }
            });
        },
        
        formatDate(dateString) {
            if (!dateString) return 'Không có';
            const date = new Date(dateString);
            return date.toLocaleDateString('vi-VN', { day: '2-digit', month: '2-digit', year: 'numeric' });
        }
    }
};
</script>

<style scoped>
.card-header {
    font-weight: 600;
}
</style>