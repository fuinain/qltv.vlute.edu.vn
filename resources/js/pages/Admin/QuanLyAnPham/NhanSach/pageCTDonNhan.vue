<template>
    <ContentWrapper>
        <template #customTitle>
            CHI TIẾT ĐƠN NHẬN <br>
            <span class="text-secondary">Mã đơn nhận: {{ maDonNhan }}</span> <br>
            <span class="text-secondary">Tên đơn nhận: {{ tenDonNhan }}</span>
        </template>
        <template #ContentPage>
            <div class="row">
                <div class="col-12">
                    <Card>
                        <template #ContentCardHeader>
                            <button type="button" class="btn btn-primary mr-1" @click="themSach">
                                <i class="fas fa-plus-circle"></i>&nbsp;
                                Thêm mới
                            </button>
                            <button type="button" class="btn btn-info mx-1" @click="importExcel">
                                <i class="fas fa-file-import"></i>&nbsp;
                                Import Excel
                            </button>
                            <button type="button" class="btn btn-success mx-1" @click="exportExcelDonNhan">
                                <i class="fas fa-file-excel"></i>&nbsp;
                                In đơn
                            </button>
                            <button type="button" class="btn btn-success mx-1" @click="exportExcelThongKeTL">
                                <i class="fas fa-file-excel"></i>&nbsp;
                                Thống kê loại tài liệu
                            </button>
                        </template>
                        <template #ContentCardBody>
                            <div class="row mb-2">
                                <div class="col-md-8">
                                </div>
                                <div class="col-md-4 d-flex justify-content-end">
                                    <div class="input-group">
                                        <input type="text" class="form-control" v-model="searchQuery"
                                            placeholder="Tìm kiếm..." @keyup.enter="searchGlobal"
                                            style="border-top-right-radius: 0; border-bottom-right-radius: 0;">
                                        <div class="input-group-append">
                                            <button type="button" class="btn btn-default" @click="searchGlobal">
                                                <i class="fas fa-search"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <Table :columns="headers" :data="ds.data ?? []" :pagination="ds"
                                        :fetchData="fetchData" :hideSearch="true">
                                        <!-- Slot cho cột nhan đề để giới hạn độ dài -->
                                        <template v-slot:column-nhan_de="{ row }">
                                            <div class="text-truncate" style="max-width: 350px;" :title="row.nhan_de">
                                                {{ row.nhan_de }}
                                            </div>
                                        </template>

                                        <!-- Slot cho cột hành động -->
                                        <template v-slot:column-actions="{ row }">
                                            <button type="button"
                                                class="btn p-1 btn-primary border-0 bg-transparent text-primary shadow-none"
                                                @click="suaCTDonNhan(row)">
                                                <i class="fas fa-edit"></i>&nbsp;
                                            </button>
                                            <button type="button"
                                                class="btn p-1 btn-primary border-0 bg-transparent text-danger shadow-none"
                                                @click="xoaCTDonNhan(row)">
                                                <i class="fas fa-trash-alt"></i>&nbsp;
                                            </button>
                                            <button type="button"
                                                class="btn p-1 btn-primary border-0 bg-transparent text-secondary shadow-none"
                                                @click="xemBienMucBieuGhiSach(row)">
                                                <i class="fas fa-cog"></i>&nbsp;
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

    <Modal ref="modal">
        <div class="row">
            <div class="col-md-12">
                <Input v-model="Sach.nhan_de" label="Nhan đề" placeholder="Nhan đề..." type="text" />
            </div>
            <div class="col-md-12">
                <Input v-model="Sach.tac_gia" label="Tác giả" placeholder="Tác giả..." type="text" />
            </div>
            <div class="col-md-4">
                <Input v-model="Sach.nam_xuat_ban" label="Năm xuất bản" placeholder="Năm XB..." type="text" />
            </div>
            <div class="col-md-4">
                <Input v-model="Sach.nha_xuat_ban" label="Nhà xuất bản" placeholder="Nhà XB..." type="text" />
            </div>
            <div class="col-md-4">
                <Input v-model="Sach.noi_xuat_ban" label="Nơi xuất bản" placeholder="Nơi XB.." type="text" />
            </div>
            <div class="col-md-6">
                <Input v-model="Sach.gia" label="Giá tiền" placeholder="Giá tiền..." type="text" />
            </div>
            <div class="col-md-6">
                <Input v-model="Sach.so_luong" label="Số lượng" placeholder="Số lượng..." type="number" />
            </div>
        </div>
    </Modal>

    <!-- Modal Import Excel -->
    <Modal ref="modalImport" size="lg">
        <div class="row">
            <div class="col-md-12 mb-2">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> Tải file mẫu Excel và điền thông tin sách vào các cột tương ứng,
                    sau
                    đó tải lên để import.
                </div>
            </div>
            <div class="col-md-12 mb-2">
                <a href="/template_excel/template_excel_import_sach.xlsx" class="btn btn-success" download>
                    <i class="fas fa-download"></i> Tải file mẫu Excel
                </a>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <label for="excelFile">Chọn file Excel để import</label>
                    <input type="file" class="form-control-file" id="excelFile" ref="excelFile"
                        @change="handleFileChange" accept=".xlsx, .xls">
                    <small class="form-text text-muted">Chỉ chấp nhận file Excel (.xlsx, .xls)</small>
                </div>
            </div>
            <div class="col-md-12 mt-3" v-if="isImporting">
                <div class="progress">
                    <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar"
                        :style="{ width: importProgress + '%' }">
                        {{ importProgress }}%
                    </div>
                </div>
                <p class="text-center mt-2">{{ importStatus }}</p>
            </div>
        </div>
        <template #buttons>
            <button type="button" class="btn btn-primary" @click="uploadExcel" :disabled="!selectedFile || isImporting">
                <i class="fas fa-upload"></i> Import
            </button>
            <button type="button" class="btn btn-secondary" @click="$refs.modalImport.closeModal()">
                <i class="fas fa-times"></i> Đóng
            </button>
        </template>
    </Modal>
</template>

<script>
import FileSaver from 'file-saver';
export default {
    name: "pageCTDonNhan",
    data() {
        return {
            ds: [],
            currentPage: 1,
            Sach: {
                nhan_de: "",
                tac_gia: "",
                nam_xuat_ban: "",
                nha_xuat_ban: "",
                noi_xuat_ban: "",
                gia: "",
                so_luong: "",
            },
            headers: [
                { key: 'index', label: 'STT', sortable: false },
                { key: 'nhan_de', label: 'Nhan đề', sortable: true },
                { key: 'tac_gia', label: 'Tác giả' },
                { key: 'nam_xuat_ban', label: 'NXB' },
                { key: 'nha_xuat_ban', label: 'Nhà xuất bản' },
                { key: 'noi_xuat_ban', label: 'Nơi xuất bản' },
                { key: 'gia', label: 'Giá', format: "VND" },
                { key: 'so_luong', label: 'SL' },
                { key: 'thanh_tien', label: 'Thành tiền', format: "VND" },
                { key: 'actions', label: 'Hành động', sortable: false },
            ],
            maDonNhan: this.$route.query.ma_don_nhan || '',
            tenDonNhan: this.$route.query.ten_don_nhan || '',
            // Thêm các biến cho import Excel
            selectedFile: null,
            isImporting: false,
            importProgress: 0,
            importStatus: '',
            // Thêm biến cho tìm kiếm toàn cục
            searchQuery: '',
            isSearching: false,
        };
    },
    mounted() {
        this.fetchData(1);
    },
    methods: {
        async fetchData(page = 1, search = '') {
            const id = this.$route.params.id_don_nhan;
            try {
                const res = await axios.get(
                    `/api/quan-ly-an-pham/nhan-sach/don-nhan/chi-tiet-don-nhan/${id}`, {
                    params: {
                        page: page,
                        search: search
                    }
                }
                );

                if (res.data.status === 200) {
                    this.ds = res.data.data;
                    this.currentPage = this.ds.current_page;

                    // Nếu đang tìm kiếm và không có kết quả, hiển thị thông báo
                    if (search && this.ds.data.length === 0 && !this.isSearching) {
                        toastr.info('Không tìm thấy kết quả phù hợp');
                    }
                    this.isSearching = false;
                } else {
                    toastr.error('Không tải được dữ liệu');
                }
            } catch (err) {
                console.error('Lỗi khi fetch dữ liệu:', err);
                toastr.error('Đã xảy ra lỗi khi tải dữ liệu');
            }
        },

        // Thêm phương thức tìm kiếm toàn cục
        searchGlobal() {
            if (this.searchQuery.trim() === '') {
                this.fetchData(1);
                return;
            }

            this.isSearching = true;
            toastr.info('Đang tìm kiếm...');
            this.fetchData(1, this.searchQuery);
        },

        async themSach() {
            // 1. Chuẩn bị dữ liệu & tiêu đề modal
            this.$refs.modal.$data.title = 'Thêm mới sách';
            this.$refs.modal.$data.save = 'Thêm';

            this.Sach = {
                id_don_nhan: this.$route.params.id_don_nhan,
                nhan_de: '',
                tac_gia: '',
                nam_xuat_ban: '',
                nha_xuat_ban: '',
                noi_xuat_ban: '',
                gia: 0,
                so_luong: 0,
                thanh_tien: 0
            };

            /* ---------- LOOP cho phép "Lưu" nhiều lần nếu server trả lỗi ---------- */
            while (true) {
                const confirmed = await this.$refs.modal.openModal();
                if (!confirmed) break;

                try {
                    const res = await axios.post(
                        '/api/quan-ly-an-pham/nhan-sach/don-nhan/chi-tiet-don-nhan',
                        this.Sach
                    );
                    if (res.data.status === 200) {
                        toastr.success(res.data.message);
                        this.fetchData(this.currentPage, this.searchQuery);
                        this.$refs.modal.closeModal();     // đóng modal
                        break;                             // kết thúc while
                    }
                } catch (err) {
                    if (err.response?.status === 422) {
                        Object.values(err.response.data.errors)
                            .forEach(msg => toastr.error(msg[0]));
                    } else {
                        toastr.error('Thêm thất bại');
                        console.error(err);
                        break; // lỗi khác → thoát
                    }
                }
            }
        },

        async suaCTDonNhan(row) {
            this.$refs.modal.$data.title = 'Cập nhật sách';
            this.$refs.modal.$data.save = 'Cập nhật';
            this.Sach = { ...row };

            while (true) {
                const confirmed = await this.$refs.modal.openModal();
                if (!confirmed) break;

                try {
                    const res = await axios.put(
                        `/api/quan-ly-an-pham/nhan-sach/don-nhan/chi-tiet-don-nhan/${row.id_sach}`,
                        this.Sach
                    );
                    if (res.data.status === 200) {
                        toastr.success(res.data.message);
                        this.fetchData(this.currentPage, this.searchQuery);
                        this.$refs.modal.closeModal();
                        break;
                    }
                } catch (err) {
                    if (err.response?.status === 422) {
                        Object.values(err.response.data.errors)
                            .forEach(msg => toastr.error(msg[0]));
                    } else {
                        toastr.error('Cập nhật thất bại');
                        console.error(err);
                        break;
                    }
                }
            }
        },

        async xoaCTDonNhan(row) {
            Swal.fire({
                title: 'Bạn có chắc chắn?',
                text: "Dữ liệu sau khi bị xoá không thể khôi phục lại !!!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Có, xoá!',
                cancelButtonText: 'Hủy',
            }).then(async (result) => {
                if (result.isConfirmed) {
                    try {
                        const res = await axios.delete(
                            `/api/quan-ly-an-pham/nhan-sach/don-nhan/chi-tiet-don-nhan/${row.id_sach}`
                        );
                        if (res.data.status === 200) {
                            toastr.success(res.data.message);
                            this.fetchData(this.currentPage, this.searchQuery);
                        }
                    } catch (err) {
                        toastr.error("Xoá thất bại");
                        console.error(err);
                    }
                }
            });
        },

        async xemBienMucBieuGhiSach(row) {
            this.$router.push({
                name: "pageBienMucBieuGhiSach",
                params: {
                    id_don_nhan: this.$route.params.id_don_nhan,
                    id_sach: row.id_sach
                },
                query: {
                    ten_sach: row.nhan_de
                }
            });
        },

        async exportExcelDonNhan() {
            const id_don_nhan = this.$route.params.id_don_nhan;
            if (!id_don_nhan) {
                toastr.error('Không tìm thấy ID đơn nhận.');
                return;
            }
            toastr.info('Đang chuẩn bị file Excel, vui lòng đợi...');
            try {
                // Xây dựng URL đến API export
                const exportUrl = `/api/quan-ly-an-pham/nhan-sach/don-nhan/chi-tiet-don-nhan/export-don-nhan/${id_don_nhan}`;
                window.location.href = exportUrl;
            } catch (error) {
                console.error('Lỗi không mong đợi khi cố gắng điều hướng tải file:', error);
                toastr.error('Có lỗi xảy ra khi yêu cầu xuất file.');
            } finally {

            }
        },

        async exportExcelThongKeTL() {
            const id_don_nhan = this.$route.params.id_don_nhan;
            if (!id_don_nhan) {
                toastr.error('Không tìm thấy ID đơn nhận.');
                return;
            }
            toastr.info('Đang chuẩn bị file Excel, vui lòng đợi...');
            try {
                const exportUrl = `/api/quan-ly-an-pham/nhan-sach/don-nhan/chi-tiet-don-nhan/export-thong-ke-tai-lieu/${id_don_nhan}`;
                window.location.href = exportUrl;
            } catch (error) {
                console.error('Lỗi không mong đợi khi cố gắng điều hướng tải file thống kê:', error);
                toastr.error('Có lỗi xảy ra khi yêu cầu xuất file Thống kê loại tài liệu.');
            }
        },

        // Phương thức mới cho import Excel
        importExcel() {
            this.$refs.modalImport.$data.title = 'Import sách từ Excel';
            this.selectedFile = null;
            this.isImporting = false;
            this.importProgress = 0;
            this.importStatus = '';
            this.$refs.modalImport.openModal();
        },

        handleFileChange(event) {
            this.selectedFile = event.target.files[0];
        },

        async uploadExcel() {
            if (!this.selectedFile) {
                toastr.error('Vui lòng chọn file Excel để import');
                return;
            }

            const id_don_nhan = this.$route.params.id_don_nhan;
            if (!id_don_nhan) {
                toastr.error('Không tìm thấy ID đơn nhận');
                return;
            }

            this.isImporting = true;
            this.importProgress = 10;
            this.importStatus = 'Đang tải file lên...';

            const formData = new FormData();
            formData.append('excel_file', this.selectedFile);
            formData.append('id_don_nhan', id_don_nhan);

            try {
                this.importProgress = 30;
                this.importStatus = 'Đang xử lý dữ liệu...';

                const response = await axios.post(
                    '/api/quan-ly-an-pham/nhan-sach/don-nhan/chi-tiet-don-nhan/import-excel',
                    formData,
                    {
                        headers: {
                            'Content-Type': 'multipart/form-data'
                        },
                        onUploadProgress: (progressEvent) => {
                            const percentCompleted = Math.round(
                                (progressEvent.loaded * 30) / progressEvent.total
                            ) + 30;
                            this.importProgress = Math.min(percentCompleted, 60);
                            this.importStatus = 'Đang xử lý dữ liệu...';
                        }
                    }
                );

                this.importProgress = 100;
                this.importStatus = 'Hoàn thành!';

                if (response.data.status === 200) {
                    toastr.success(response.data.message);
                    this.fetchData(this.currentPage, this.searchQuery);
                    setTimeout(() => {
                        this.$refs.modalImport.closeModal();
                        this.isImporting = false;
                    }, 1000);
                } else {
                    toastr.error(response.data.message || 'Import không thành công');
                    this.isImporting = false;
                }
            } catch (error) {
                this.isImporting = false;
                if (error.response?.status === 422) {
                    Object.values(error.response.data.errors)
                        .forEach(msg => toastr.error(msg[0]));
                } else {
                    toastr.error(error.response?.data?.message || 'Có lỗi xảy ra khi import dữ liệu');
                    console.error('Lỗi khi import:', error);
                }
            }
        }
    },
};
</script>

<style scoped>
/* Thêm CSS để đảm bảo cột nhan đề hiển thị đúng */
.text-truncate {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    display: block;
}
</style>
