<template>
    <ContentWrapper title="QUẢN LÝ BẠN ĐỌC">
        <template #ContentPage>
            <div class="row">
                <div class="col-12">
                    <Card>
                        <template #ContentCardHeader>
                            <div class="d-flex justify-content-between">
                                <div>
                                    <button type="button" class="btn btn-primary me-2" @click="themBanDoc">
                                        <i class="fas fa-plus-circle"></i>&nbsp;
                                        Thêm mới
                                    </button>
                                    <button type="button" class="btn btn-success" @click="showSyncModal">
                                        <i class="fas fa-sync"></i>&nbsp;
                                        Đồng bộ dữ liệu
                                    </button>
                                </div>                              
                            </div>
                        </template>
                        <template #ContentCardBody>
                            <div class="row mb-2">
                                <div class="col-md-8">                               
                                </div>
                                <div class="col-md-4">
                                    <div class="input-group search-group float-end" style="width: 320px;">
                                        <input type="text" v-model="search" class="form-control"
                                            placeholder="Tìm kiếm..." @keyup.enter="timKiem"
                                            style="border-top-right-radius: 0; border-bottom-right-radius: 0;">
                                        <button type="button" class="btn btn-search" @click="timKiem"
                                            style="border-top-left-radius: 0; border-bottom-left-radius: 0;">
                                            <i class="fas fa-search"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <!-- Bảng hiển thị dữ liệu -->
                                    <Table :columns="headers" :data="ds.data ?? []" :pagination="ds"
                                        :fetchData="fetchData" :hideSearch="true">
                                        <!-- Slot cho cột hành động -->
                                        <template v-slot:column-actions="{ row }">
                                            <button type="button"
                                                class="btn p-1 btn-primary border-0 bg-transparent text-primary shadow-none"
                                                @click="suaBanDoc(row)">
                                                <i class="fas fa-edit"></i>&nbsp;
                                            </button>
                                            <button type="button"
                                                class="btn p-1 btn-primary border-0 bg-transparent text-danger shadow-none"
                                                @click="xoaBanDoc(row)">
                                                <i class="fas fa-trash-alt"></i>&nbsp;
                                            </button>
                                            <button type="button"
                                                class="btn p-1 btn-primary border-0 bg-transparent text-info shadow-none"
                                                @click="xemChiTiet(row)">
                                                <i class="fas fa-eye"></i>&nbsp;
                                            </button>
                                        </template>
                                    </Table>
                                </div>
                            </div>
                        </template>
                    </Card>
                </div>
            </div>
        </template>
    </ContentWrapper>

    <!-- Modal thêm/sửa bạn đọc -->
    <Modal ref="modal">
        <div class="row">
            <div class="col-md-6">
                <Input v-model="banDoc.ho_ten" label="Họ tên" placeholder="Nhập họ tên..." type="text" required />
            </div>
            <div class="col-md-6">
                <Input v-model="banDoc.mssv" label="MSSV" placeholder="Nhập MSSV..." type="text" required />
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <Input v-model="banDoc.ma_lop" label="Mã lớp" placeholder="Nhập mã lớp..." type="text" required />
            </div>
            <div class="col-md-6">
                <Input v-model="banDoc.ten_lop" label="Tên lớp" placeholder="Nhập tên lớp..." type="text" required />
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <Input v-model="banDoc.so_the" label="Số thẻ" placeholder="Nhập số thẻ..." type="text" required />
            </div>
            <div class="col-md-6">
                <Input v-model="banDoc.ngay_sinh" label="Ngày sinh" placeholder="Nhập ngày sinh (DD/MM/YYYY)..."
                    type="text" />
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <Input v-model="banDoc.ngay_cap_the" label="Năm cấp thẻ" placeholder="Nhập năm cấp thẻ..."
                    type="text" />
            </div>
            <div class="col-md-6">
                <Input v-model="banDoc.han_the" label="Năm hết hạn" placeholder="Nhập năm hết hạn..." type="text" />
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <Input v-model="banDoc.nien_khoa" label="Niên khóa" placeholder="Nhập niên khóa..." type="text" />
            </div>
            <div class="col-md-6">
                <Input v-model="banDoc.email" label="Email" placeholder="Nhập email..." type="text" />
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <SelectOption v-model="banDoc.ma_so_quy_uoc" label="Đối tượng bạn đọc"
                    placeholder="Chọn đối tượng bạn đọc..." :options="dsDoiTuongBanDocOptions" required />
            </div>
            <div class="col-md-6">
                <SelectOption v-model="banDoc.id_chuyen_nganh" label="Chuyên ngành" placeholder="Chọn chuyên ngành..."
                    :options="dsChuyenNganhOptions" required />
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <Input v-model="banDoc.ho_khau" label="Hộ khẩu" placeholder="Nhập hộ khẩu..." type="text" />
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <Input v-model="banDoc.ghi_chu" label="Ghi chú" placeholder="Nhập ghi chú..." type="text" />
            </div>
        </div>
    </Modal>

    <!-- Modal đồng bộ dữ liệu -->
    <Modal ref="syncModal" title="Đồng bộ dữ liệu bạn đọc" save="Đồng bộ">
        <div class="row">
            <div class="col-md-4">
                <Input v-model="syncData.nam" label="Năm học" placeholder="Nhập năm học (VD: 2023)..." type="number"
                    required />
            </div>
            <div class="col-md-4">
                <SelectOption v-model="syncData.ma_so_quy_uoc" label="Đối tượng bạn đọc"
                    placeholder="Chọn đối tượng bạn đọc..." :options="dsDoiTuongBanDocOptions" required />
            </div>
            <div class="col-md-4">
                <SelectOption v-model="syncData.id_chuyen_nganh" label="Chuyên ngành" placeholder="Chọn chuyên ngành..."
                    :options="dsChuyenNganhOptions" required />
            </div>
        </div>

        <div v-if="isSyncing" class="mt-4">
            <div class="text-center my-4">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Đang đồng bộ...</span>
                </div>
                <p class="mt-2 mb-0">Đang đồng bộ dữ liệu, vui lòng đợi...</p>
                <p class="text-muted small">Quá trình này có thể diễn ra trong vài phút. Vui lòng không tắt ứng dụng.
                </p>
            </div>

            <div v-if="syncStats" class="mt-3">
                <div class="alert alert-success">
                    <h6 class="mb-2">Đồng bộ hoàn tất!</h6>
                    <p class="mb-1"><strong>Tổng số sinh viên kiểm tra:</strong> {{ syncStats.total }}</p>
                    <p class="mb-1"><strong>Số bạn đọc thêm thành công:</strong> {{ syncStats.success }}</p>
                    <p class="mb-1"><strong>Số bạn đọc đã tồn tại:</strong> {{ syncStats.existing }}</p>
                    <p class="mb-0"><strong>Số bản ghi lỗi:</strong> {{ syncStats.error }}</p>
                </div>

                <div v-if="syncNote" class="alert alert-info mt-2">
                    <i class="fas fa-info-circle me-2"></i> {{ syncNote }}
                </div>
            </div>
        </div>
    </Modal>
</template>

<script>
import SelectOption from "@/components/ui/SelectOption.vue";

export default {
    name: "pageDocGia",
    components: { SelectOption },

    data() {
        return {
            ds: [],
            currentPage: 1,
            search: '',
            banDoc: {
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
            headers: [
                { key: 'index', label: 'STT', sortable: false },
                { key: 'mssv', label: 'MSSV' },
                { key: 'ho_ten', label: 'Tên SV' },
                { key: 'ten_lop', label: 'Lớp' },
                { key: 'ngay_cap_the', label: 'Năm cấp', format: 'normalDateTime' },
                { key: 'nien_khoa', label: 'NK năm học' },
                { key: 'ten_don_vi', label: 'Khoa' },
                { key: 'actions', label: 'Hành động', sortable: false },
            ],
            dsDoiTuongBanDoc: [],
            dsDoiTuongBanDocOptions: [],
            dsChuyenNganh: [],
            dsChuyenNganhOptions: [],
            syncData: {
                nam: new Date().getFullYear(),
                ma_so_quy_uoc: "",
                id_chuyen_nganh: ""
            },
            isSyncing: false,
            syncStats: null,
            syncNote: null
        };
    },
    mounted() {
        this.fetchData();
        this.loadDoiTuongBanDoc();
        this.loadChuyenNganh();
    },
    methods: {
        async fetchData(page = 1) {
            try {
                const response = await axios.get(`/api/quan-ly-ban-doc/doc-gia`, {
                    params: {
                        page: page,
                        search: this.search
                    }
                });
                if (response.data.status === 200) {
                    this.ds = response.data.data;
                    this.currentPage = this.ds.current_page;
                }
            } catch (error) {
                console.error("Lỗi khi load dữ liệu:", error);
                toastr.error("Không thể tải dữ liệu bạn đọc");
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

        async themBanDoc() {
            // Reset form cho trường hợp thêm mới
            this.$refs.modal.$data.title = "Thêm mới bạn đọc";
            this.$refs.modal.$data.save = "Thêm mới";
            this.banDoc = {
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
            };

            // Mở modal
            const confirmed = await this.$refs.modal.openModal();
            if (!confirmed) return;

            // Kiểm tra validate
            if (
                !this.banDoc.ho_ten.trim() ||
                !this.banDoc.mssv.trim() ||
                !this.banDoc.ma_lop.trim() ||
                !this.banDoc.ten_lop.trim() ||
                !this.banDoc.so_the.trim() ||
                !this.banDoc.ma_so_quy_uoc ||
                !this.banDoc.id_chuyen_nganh
            ) {
                toastr.error("Vui lòng nhập đầy đủ thông tin bắt buộc.");
                return;
            }

            try {
                const response = await axios.post("/api/quan-ly-ban-doc/doc-gia", this.banDoc);
                if (response.data.status === 200) {
                    toastr.success(response.data.message);
                    this.fetchData();
                    this.$refs.modal.closeModal();
                }
            } catch (error) {
                console.error("Lỗi khi thêm:", error);
                if (error.response && error.response.data && error.response.data.errors) {
                    const errors = Object.values(error.response.data.errors).flat();
                    errors.forEach(err => toastr.error(err));
                } else {
                    toastr.error("Thêm thất bại!");
                }
            }
        },

        async suaBanDoc(row) {
            // Set tiêu đề và điền dữ liệu hiện có vào form
            this.$refs.modal.$data.title = "Cập nhật bạn đọc";
            this.$refs.modal.$data.save = "Cập nhật";
            this.banDoc = { ...row };

            // Mở modal để chỉnh sửa
            const confirmed = await this.$refs.modal.openModal();
            if (!confirmed) return;

            try {
                const response = await axios.put(`/api/quan-ly-ban-doc/doc-gia/${row.id_doc_gia}`, this.banDoc);
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
        },

        async xoaBanDoc(row) {
            Swal.fire({
                title: 'Bạn có chắc chắn?',
                text: "Dữ liệu sau khi bị xoá không thể khôi phục lại !!!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Có, xoá nó!',
                cancelButtonText: 'Hủy bỏ'
            }).then(async (result) => {
                if (result.isConfirmed) {
                    try {
                        const response = await axios.delete(`/api/quan-ly-ban-doc/doc-gia/${row.id_doc_gia}`);
                        if (response.data.status === 200) {
                            toastr.success(response.data.message);
                            this.fetchData();
                        }
                    } catch (error) {
                        console.error("Lỗi khi xoá:", error);
                        toastr.error("Xoá thất bại!");
                    }
                }
            });
        },

        async showSyncModal() {
            this.$refs.syncModal.$data.title = "Đồng bộ dữ liệu bạn đọc";
            this.$refs.syncModal.$data.save = "Đồng bộ";
            this.isSyncing = false;
            this.syncStats = null;
            this.syncNote = null;

            const confirmed = await this.$refs.syncModal.openModal();
            if (!confirmed) return;

            if (
                !this.syncData.nam ||
                !this.syncData.ma_so_quy_uoc ||
                !this.syncData.id_chuyen_nganh
            ) {
                toastr.error("Vui lòng nhập đầy đủ thông tin đồng bộ.");
                return;
            }

            this.isSyncing = true;

            try {
                this.$refs.syncModal.$data.loading = true;
                this.$refs.syncModal.$data.disableButtons = true;

                const response = await axios.post('/api/quan-ly-ban-doc/doc-gia/sync', this.syncData);

                if (response.data.status === 200) {
                    this.syncStats = response.data.data;
                    this.syncNote = response.data.note;
                    toastr.success(response.data.message);
                    this.fetchData();
                } else {
                    toastr.error("Đồng bộ thất bại!");
                }
            } catch (error) {
                console.error("Lỗi khi đồng bộ:", error);
                toastr.error("Đồng bộ thất bại!");
            } finally {
                this.isSyncing = false;
                this.$refs.syncModal.$data.loading = false;
                this.$refs.syncModal.$data.disableButtons = false;
            }
        },

        async xemChiTiet(row) {
            if (!row || !row.id_doc_gia) return;

            // Chuyển hướng đến trang chi tiết
            this.$router.push({
                name: 'pageChiTietDocGia',
                params: { id: row.id_doc_gia }
            });
        },

        timKiem() {
            this.fetchData(1);
        }
    },
};
</script>

<style scoped>
.sync-results {
    border: 1px solid #ddd;
    padding: 10px;
    border-radius: 5px;
}

.sync-results .alert {
    margin-bottom: 5px;
    padding: 5px 10px;
}

.search-group .form-control {
    border-right: none;
    box-shadow: none;
}

.btn-search {
    background: transparent;
    border: 1px solid #ced4da;
    border-left: none;
    color: #007bff;
    transition: background 0.2s, color 0.2s;
}

.btn-search:hover {
    background: #f0f4fa;
    color: #0056b3;
}
</style>