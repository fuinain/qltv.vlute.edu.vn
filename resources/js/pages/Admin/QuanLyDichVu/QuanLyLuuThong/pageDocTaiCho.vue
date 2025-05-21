<template>
    <ContentWrapper title="QUẢN LÝ TÀI LIỆU ĐỌC TẠI CHỖ">
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
                                        <input type="text" class="form-control" placeholder="Nhập mã số sinh viên hoặc số thẻ..." v-model="searchBanDoc" @keyup.enter="timBanDoc">
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

                            <!-- Thông tin bạn đọc khi tìm thấy -->
                            <div class="row mt-4" v-if="banDoc.id_doc_gia">
                                <div class="col-12">
                                    <div class="card card-primary card-outline">
                                        <div class="card-header">
                                            <h3 class="card-title">Thông tin bạn đọc</h3>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <p><strong>Họ tên:</strong> {{ banDoc.ho_ten }}</p>
                                                    <p><strong>MSSV:</strong> {{ banDoc.mssv }}</p>
                                                </div>
                                                <div class="col-md-3">
                                                    <p><strong>Số thẻ:</strong> {{ banDoc.so_the }}</p>
                                                    <p><strong>Lớp:</strong> {{ banDoc.ten_lop }}</p>
                                                </div>
                                                <div class="col-md-3">
                                                    <p><strong>Đối tượng:</strong> {{ banDoc.ten_doi_tuong_ban_doc }}</p>
                                                    <p><strong>Hạn thẻ:</strong> {{ formatDate(banDoc.han_the) }}</p>
                                                </div>
                                                <div class="col-md-3">
                                                    <p><strong>SL có thể mượn:</strong> {{ thongTinMuon.soLuongCoTheMuon }}</p>
                                                    <p><strong>SL đang mượn:</strong> {{ danhSachDangMuon.length }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </Card>

                    <!-- Danh sách tài liệu đang mượn -->
                    <Card v-if="banDoc.id_doc_gia" class="mt-3">
                        <template #ContentCardHeader>
                            <div class="d-flex align-items-center justify-content-between w-100">
                                <h5 class="mb-0">Danh sách tài liệu đang mượn đọc tại chỗ</h5>
                                <button type="button" class="btn btn-success" @click="muonTaiLieu">
                                    <i class="fas fa-plus-circle"></i>&nbsp;
                                    Mượn tài liệu đọc tại chỗ
                                </button>
                            </div>
                        </template>
                        <template #ContentCardBody>
                            <Table 
                                :columns="headersDangMuon" 
                                :data="danhSachDangMuon" 
                                :pagination="false"
                                :hide-search="false"
                            >
                                <template v-slot:column-gio_muon="{ row }">
                                    {{ formatDateTime(row.gio_muon) }}
                                </template>
                                <template v-slot:column-gio_tra="{ row }">
                                    {{ formatDateTime(row.gio_tra) }}
                                </template>
                                <template v-slot:column-actions="{ row }">
                                    <div class="btn-group">
                                        <button 
                                            type="button" 
                                            class="btn btn-sm btn-danger" 
                                            @click="traSach(row)"
                                            title="Trả sách"
                                        >
                                            <i class="fas fa-undo-alt"></i>&nbsp;
                                            Trả sách
                                        </button>
                                    </div>
                                </template>
                            </Table>
                        </template>
                    </Card>
                </div>
            </div>
        </template>
    </ContentWrapper>

    <!-- Modal mượn tài liệu -->
    <Modal ref="modalMuon" size="lg">
        <div class="row mb-3">
            <div class="col-md-12">
                <div class="input-group">
                    <input 
                        type="text" 
                        v-model="maDKCB" 
                        class="form-control" 
                        placeholder="Nhập mã DKCB... (Mã DKCB có dạng: KD.000001)"
                        @keyup.enter="kiemTraDKCB"
                    />
                    <div class="input-group-append">
                        <button class="btn btn-primary" @click="kiemTraDKCB">
                            <i class="fas fa-search"></i> Kiểm tra
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Thông tin tài liệu -->
        <div v-if="thongTinDKCB.id_dkcb" class="row">
            <div class="col-md-12">
                <div class="alert alert-primary">
                    <div class="row">
                        <div class="col-md-6">
                            <p><b>Mã DKCB:</b> {{ thongTinDKCB.ma_dkcb }}</p>
                            <p><b>Tên sách:</b> {{ thongTinDKCB.nhan_de || 'Không xác định' }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><b>Kho:</b> {{ thongTinDKCB.ten_kho }}</p>
                            <p><b>Phân loại:</b> {{ thongTinDKCB.phan_loai || 'Chưa phân loại' }}</p>
                            <p><b>Trạng thái: </b> 
                                <span 
                                    :class="{'badge badge-success': thongTinDKCB.co_the_muon, 'badge badge-danger': !thongTinDKCB.co_the_muon}"
                                >
                                    {{ thongTinDKCB.co_the_muon ? 'Có thể mượn' : 'Không thể mượn' }}
                                </span>
                            </p>
                        </div>
                    </div>
                </div>
                
                <!-- Thêm thông báo lỗi riêng biệt khi không thể mượn -->
                <div v-if="thongTinDKCB.ly_do" class="alert alert-danger mt-3 font-weight-bold">
                    <i class="fas fa-exclamation-triangle fa-lg mr-2"></i> {{ thongTinDKCB.ly_do }}
                </div>
            </div>
        </div>

        <template #buttons>
            <button 
                type="button" 
                class="btn btn-primary text-bold"
                @click="xacNhanMuon"
                :disabled="!thongTinDKCB.co_the_muon"
            >
                <i class="far fa-save"></i>&nbsp;
                Xác nhận mượn
            </button>
        </template>
    </Modal>
</template>

<script>
export default {
    name: "PageDocTaiCho",
    
    data() {
        return {
            searchBanDoc: '',
            isLoading: false,
            banDoc: {},
            danhSachDangMuon: [],
            thongTinMuon: {
                soLuongCoTheMuon: 0,
                soNgayMuon: 0  // Không dùng cho đọc tại chỗ
            },
            maDKCB: '',
            thongTinDKCB: {},
            headersDangMuon: [
                {key: 'index', label: 'STT', sortable: false},
                {key: 'ma_dkcb', label: 'Mã DKCB'},
                {key: 'nhan_de', label: 'Tên ấn phẩm'},
                {key: 'phan_loai', label: 'Phân loại'},
                {key: 'gio_muon', label: 'Giờ mượn'},
                {key: 'gio_tra', label: 'Giờ hạn trả'},
                {key: 'actions', label: 'Hành động', sortable: false},
            ],
        }
    },
    mounted() {
        // Khởi tạo
    },
    methods: {
        timBanDoc() {
            if (!this.searchBanDoc) {
                toastr.error("Vui lòng nhập MSSV hoặc số thẻ để tìm kiếm");
                return;
            }

            this.isLoading = true;
            axios.get(`/api/quan-ly-dich-vu/doc-tai-cho/ban-doc/${this.searchBanDoc}`)
                .then(response => {
                    if (response.data.status === 200) {
                        this.banDoc = response.data.data.ban_doc;
                        this.thongTinMuon = response.data.data.thong_tin_muon;
                        // Thêm index và format giờ cho danh sách đang mượn
                        this.danhSachDangMuon = response.data.data.danh_sach_dang_muon.map((item, index) => {
                            return {
                                ...item,
                                index: index + 1
                            };
                        });
                    } else {
                        toastr.error(response.data.message);
                    }
                })
                .catch(error => {
                    console.error(error);
                    toastr.error("Không tìm thấy bạn đọc hoặc có lỗi xảy ra");
                })
                .finally(() => {
                    this.isLoading = false;
                });
        },
        muonTaiLieu() {
            this.$refs.modalMuon.title = "Mượn tài liệu đọc tại chỗ";
            this.$refs.modalMuon.save = "Xác nhận";
            this.maDKCB = '';
            this.thongTinDKCB = {};
            this.$refs.modalMuon.show();
        },
        kiemTraDKCB() {
            if (!this.maDKCB) {
                toastr.error("Vui lòng nhập mã DKCB");
                return;
            }

            axios.get(`/api/quan-ly-dich-vu/doc-tai-cho/kiem-tra-dkcb/${this.maDKCB}?id_ban_doc=${this.banDoc.id_doc_gia}`)
                .then(response => {
                    if (response.data.status === 200) {
                        this.thongTinDKCB = response.data.data;
                        
                        // Hiển thị thông báo toastr khi không thể mượn
                        if (!this.thongTinDKCB.co_the_muon && this.thongTinDKCB.ly_do) {
                            toastr.warning(this.thongTinDKCB.ly_do, 'Không thể mượn!', {
                                closeButton: true,
                                timeOut: 5000,
                                progressBar: true,
                                positionClass: 'toast-top-right'
                            });
                        }
                    } else {
                        toastr.error(response.data.message);
                    }
                })
                .catch(error => {
                    console.error(error);
                    toastr.error("Không tìm thấy mã DKCB hoặc có lỗi xảy ra");
                });
        },
        xacNhanMuon() {
            if (!this.thongTinDKCB.id_dkcb) {
                toastr.error("Vui lòng kiểm tra mã DKCB trước khi mượn");
                return;
            }

            const payload = {
                id_ban_doc: this.banDoc.id_doc_gia,
                id_dkcb: this.thongTinDKCB.id_dkcb
            };

            axios.post('/api/quan-ly-dich-vu/doc-tai-cho/muon', payload)
                .then(response => {
                    if (response.data.status === 200) {
                        toastr.success(response.data.message);
                        // Thêm index cho hiển thị STT
                        const newItem = {
                            ...response.data.data,
                            index: this.danhSachDangMuon.length + 1
                        };
                        
                        this.danhSachDangMuon.push(newItem);
                        
                        // Cập nhật số lượng có thể mượn
                        if (this.thongTinMuon.soLuongCoTheMuon > 0) {
                            this.thongTinMuon.soLuongCoTheMuon -= 1;
                        }
                        
                        this.$refs.modalMuon.closeModal();
                    } else {
                        toastr.error(response.data.message);
                    }
                })
                .catch(error => {
                    console.error(error);
                    toastr.error("Có lỗi xảy ra khi mượn tài liệu");
                });
        },
        traSach(row) {
            Swal.fire({
                title: 'Bạn có chắc chắn?',
                text: "Bạn có chắc chắn là SV đã trả sách này không?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Có, trả sách!',
                cancelButtonText: 'Hủy bỏ'
            }).then(result => {
                if (result.isConfirmed) {
                    axios.put(`/api/quan-ly-dich-vu/doc-tai-cho/tra-sach/${row.id_doc_tai_cho}`)
                        .then(response => {
                            if (response.data.status === 200) {
                                toastr.success(response.data.message);
                                // Xóa khỏi danh sách đang mượn
                                this.danhSachDangMuon = this.danhSachDangMuon.filter(
                                    item => item.id_doc_tai_cho !== row.id_doc_tai_cho
                                );
                                // Cập nhật lại số thứ tự
                                this.danhSachDangMuon = this.danhSachDangMuon.map((item, index) => {
                                    return { ...item, index: index + 1 };
                                });
                                
                                // Tăng số lượng có thể mượn
                                this.thongTinMuon.soLuongCoTheMuon += 1;
                            } else {
                                toastr.error(response.data.message);
                            }
                        })
                        .catch(error => {
                            console.error(error);
                            toastr.error("Có lỗi xảy ra khi trả sách");
                        });
                }
            });
        },
        formatDate(dateString) {
            if (!dateString) return '';
            const date = new Date(dateString);
            return date.toLocaleDateString('vi-VN');
        },
        formatDateTime(dateTimeString) {
            if (!dateTimeString) return '';
            const date = new Date(dateTimeString);
            // Đảm bảo hiển thị đúng múi giờ
            return date.toLocaleDateString('vi-VN') + ' ' + date.toLocaleTimeString('vi-VN', {
                hour: '2-digit',
                minute: '2-digit'
            });
        }
    }
}
</script>

<style scoped>
.search-group {
    margin-bottom: 15px;
}

.badge {
    font-size: 100%;
    padding: 5px 10px;
    font-weight: bold;
}

.badge-success {
    background-color: #28a745;
}

.badge-danger {
    background-color: #dc3545;
}

.btn-default {
    background-color: #f8f9fa;
    border-color: #ddd;
}

.btn-default:hover {
    background-color: #e9ecef;
}

/* Tăng cường độ tương phản cho thông báo lỗi */
.alert-danger {
    background-color: #f8d7da;
    border-color: #f5c6cb;
    color: #721c24;
    font-size: 1.1rem;
    padding: 15px;
    border-radius: 5px;
    box-shadow: 0 0 8px rgba(220, 53, 69, 0.3);
}

.alert-danger i {
    color: #dc3545;
    margin-right: 10px;
    font-size: 1.3rem;
    vertical-align: middle;
}

/* Cải thiện tương phản cho badge */
.badge {
    font-size: 100%;
    padding: 5px 10px;
    font-weight: bold;
}
</style>
