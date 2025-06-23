<template>
    <ContentWrapper title="QUẢN LÝ GIẢNG VIÊN">
        <template #ContentPage>
            <div class="row">
                <div class="col-12">
                    <Card>
                        <template #ContentCardHeader>
                            <button type="button" class="btn btn-primary" @click="themGiangVien">
                                <i class="fas fa-plus-circle"></i>&nbsp;
                                Thêm mới
                            </button>

                            <button type="button" class="btn btn-primary ml-2" @click="importGiangVien">
                                <i class="fas fa-file-excel"></i>&nbsp;
                                Import
                            </button>


                        </template>
                        <template #ContentCardBody>
                            <div class="row">
                                <div class="col-md-4">
                                    <SelectOption
                                        label="Đơn vị"
                                        :options="donViOptions"
                                        v-model="id_don_vi"
                                        :placeholder="''"
                                    />
                                </div>
                                <div class="col-md-4"></div>
                                <div class="col-md-4" style="margin-top: 32px">
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

                            <!-- Bảng hiển thị dữ liệu -->
                            <Table :columns="headers" :data="ds.data ?? []" :pagination="ds" :fetchData="loadGiangVien" :hideSearch="true">
                                <!-- Slot cho cột hành động -->
                                <template v-slot:column-actions="{ row }">
                                    <button type="button"
                                            class="btn p-0 btn-primary border-0 bg-transparent text-primary shadow-none"
                                            @click="suaGiangVien(row)">
                                        <i class="fas fa-edit"></i>&nbsp;
                                    </button>
                                    |
                                    <button type="button"
                                            class="btn p-0 btn-primary border-0 bg-transparent text-danger shadow-none"
                                            @click="xoaGiangVien(row)">
                                        <i class="fas fa-trash-alt"></i>&nbsp;
                                    </button>
                                </template>
                            </Table>
                        </template>
                    </Card>
                </div>
            </div>
        </template>
    </ContentWrapper>

    <!-- Modal (dùng chung cho thêm & sửa) -->
    <Modal ref="modal">
        <div class="row">
            <div class="col-md-6">
                <Input
                    v-model="giangVien.ho_ten"
                    label="Họ và tên"
                    placeholder="Nhập họ và tên ..."
                    type="text"
                />
            </div>
            <div class="col-md-6">
                <Input
                    v-model="giangVien.email"
                    label="Email"
                    placeholder="Nhập Email ..."
                    type="text"
                />
            </div>
            <div class="col-md-12">
                <SelectOption
                    label="Đơn vị"
                    :options="donViOptions"
                    v-model="giangVien.id_don_vi"
                    :placeholder="''"
                />
            </div>
        </div>
    </Modal>

    <Modal ref="modalImport">
        <div class="row">
            <div class="col-12">
                <div class="custom-file">
                    <input ref="fileInput" type="file" class="custom-file-input" id="customFile" @change="handleFileUpload">
                    <label class="custom-file-label" for="customFile">{{ fileGiangVien.fileName || "Chọn file" }}</label>
                </div>
            </div>
        </div>
    </Modal>

    <Modal ref="mdLoiGiangVien">
        <div class="row">
            <div class="col-12">
            <pre
                v-if="errorsGV.length"
                style="max-height: 400px; overflow-y: auto; white-space: pre-wrap;"
            >
                {{ errorsGV.join('\n') }}
            </pre>
            </div>
        </div>
    </Modal>

</template>

<script>

const API_GIANG_VIEN = '/api/danh-muc/thong-tin-chung/giang-vien';
const API_DON_VI = '/api/danh-muc/thong-tin-chung/don-vi/list-don-vi-select-option';

export default {
    data() {
        return {
            currentPage: 1,
            ds: [],
            // Dữ liệu cho form thêm/sửa
            giangVien: {
                ho_ten: "",
                email: "",
                id_don_vi: 0,
            },
            headers: [
                {key: 'index', label: 'STT', sortable: false},
                {key: 'ho_ten', label: 'Họ và tên'},
                {key: 'email', label: 'Email'},
                {key: 'ten_don_vi', label: 'Đơn vị'},
                {key: 'ngay_cap_nhat', label: 'Ngày cập nhật', format: 'datetime'},
                {key: 'ngay_tao', label: 'Ngày tạo', format: 'datetime'},
                {key: 'actions', label: 'Hành động', sortable: false}
            ],

            donViOptions: [],
            id_don_vi: 0,

            fileGiangVien:{
                file: '',
                fileName: '',
            },

            errorsGV: [],
            searchQuery: '',
            isSearching: false,
        };
    },
    mounted() {
        this.loadDonViOptions(API_DON_VI);
        this.loadGiangVien();
    },
    methods: {
        async loadDonViOptions(url) {
            axios.get(url).then(response => {
                const rData = response.data.data;

                this.donViOptions = [{ value: 0, text: "Tất cả" }].concat(
                    rData.map(item => ({
                        value: item.id_don_vi,
                        text: `${item.ma_don_vi} - ${item.ten_don_vi}`
                    }))
                );
                this.id_don_vi = this.donViOptions[0].value;
            }).catch(error => {
                console.error("Lỗi load đơn vị:", error);
            });
        },

        async loadGiangVien(page = 1, search='') {
            const params = {
                id_don_vi: this.id_don_vi,
                page: page,
                search: search
            }

            const response = await axios.get(`/api/danh-muc/thong-tin-chung/giang-vien?page=${page}`, {params: params});
            if (response.data.status === 200) {
                this.ds = response.data.data;
                this.currentPage = this.ds.current_page;

                // Nếu đang tìm kiếm và không có kết quả, hiển thị thông báo
                if (this.ds.data.length === 0) {
                    toastr.info('Không tìm thấy kết quả phù hợp');
                }
                this.isSearching = false;
            } else {
                toastr.error('Không tải được dữ liệu');
            }

        },

        searchGlobal() {
            if (this.searchQuery.trim() === '') {
                this.loadGiangVien(1);
                return;
            }

            this.isSearching = true;
            this.loadGiangVien(1, this.searchQuery);
        },

        async themGiangVien() {
            this.giangVien = {
                ho_ten: "",
                email: "",
                id_don_vi: 0,
            };

            // Reset form cho trường hợp thêm mới
            this.$refs.modal.$data.title = "Thêm mới thông tin";
            this.$refs.modal.$data.save = "Thêm mới";
            // Mở modal
            const confirmed = await this.$refs.modal.openModal();
            if (!confirmed) return;

            const params = {
                ho_ten: this.giangVien.ho_ten,
                email: this.giangVien.email,
                id_don_vi: this.giangVien.id_don_vi,
            }

            await axios.post(API_GIANG_VIEN, params).then(response => {
                const rData = response.data;
                if (rData.status === 200) {
                    toastr.success(rData.message);
                    this.loadGiangVien(this.ds.current_page);
                    this.$refs.modal.closeModal();
                }
                else {
                    toastr.error(rData.message);
                    this.themGiangVien();
                }
            });
        },

        async suaGiangVien(row) {
            this.giangVien = {
                ho_ten: row.ho_ten,
                email: row.email,
                id_don_vi: row.id_don_vi,
            };

            // Set tiêu đề và điền dữ liệu hiện có vào form
            this.$refs.modal.$data.title = "Cập nhật thông tin";
            this.$refs.modal.$data.save = "Cập nhật";

            // Mở modal để chỉnh sửa
            const confirmed = await this.$refs.modal.openModal();
            if (!confirmed) return;

            const params = {
                id_giang_vien: row.id_giang_vien,
                ho_ten: this.giangVien.ho_ten,
                email: this.giangVien.email,
                id_don_vi: this.giangVien.id_don_vi,
            }

            await axios.put(API_GIANG_VIEN, params).then(response => {
                const rData = response.data;
                if (rData.status === 200) {
                    toastr.success(rData.message);
                    this.loadGiangVien(this.ds.current_page);
                    this.$refs.modal.closeModal();
                }
                else {
                    toastr.error(rData.message);
                    this.suaGiangVien(row);
                }
            });
        },

        xoaGiangVien(row) {
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
                        await axios.delete(API_GIANG_VIEN, {id_giang_vien: row.id_giang_vien})
                            .then(response => {
                                if(response.data.status === 200){
                                    toastr.success(response.data.message);
                                    this.loadGiangVien(this.ds.currentPage);
                                }
                            })
                            .catch(error => {
                                console.error("Lỗi khi xoá:", error);
                                toastr.error("Xoá thất bại!");
                            });
                    } catch (error) {
                    }
                }
            });
        },

        handleFileUpload(event) {
            const file = event.target.files[0];
            if (file) {
                this.fileGiangVien.file = file;
                this.fileGiangVien.fileName = file.name;
            }
        },

        async importGiangVien() {
            const inputFile = this.$refs.fileInput;

            this.$refs.modalImport.$data.title = "Thêm mới thông tin";
            this.$refs.modalImport.$data.save = "Lưu thông tin";

            const result = await this.$refs.modalImport.openModal();
            if (!result) {
                this.fileGiangVien.file = null;
                this.fileGiangVien.fileName = "";
                inputFile.value = "";
                return;
            }

            // Kiểm tra nếu không có file được chọn
            if (!this.fileGiangVien.file) {
                alert("Vui lòng chọn file Excel!");
                return;
            }

            let formData = new FormData();
            formData.append("file", this.fileGiangVien.file);

            try {
                document.body.style.cursor = "progress";
                let response = await axios.post(
                    '/api/danh-muc/thong-tin-chung/giang-vien/import',
                    formData,
                    { headers: { "Content-Type": "multipart/form-data" } }
                );

                let data = response.data;
                if (data.status === 200) {
                    toastr.success(data.message);
                    this.$refs.modalImport.closeModal();
                    this.loadGiangVien();
                } else if (data.status === 409){
                    this.errorsGV = data.data;
                    // Thông báo cho người dùng
                    toastr.warning(data.message);
                    this.$refs.modalImport.closeModal();
                    this.loadGiangVien();

                    this.$refs.mdLoiGiangVien.$data.title = "Thông báo các đơn vị không tồn tại trong CSDL";
                    this.$refs.mdLoiGiangVien.$data.save = "Hoàn tất";
                    const result = await this.$refs.mdLoiGiangVien.openModal();
                    if (!result)
                        return;
                    else {
                        this.$refs.mdLoiGiangVien.closeModal();
                    }
                } else {
                    this.loadGiangVien();
                }
            } catch (error) {
                toastr.error("Import thất bại! Vui lòng thử lại.");
                this.fileGiangVien.file = null;
                this.fileGiangVien.fileName = "";
                inputFile.value = "";
                this.$refs.modalImport.closeModal()
            } finally {
                document.body.style.cursor = "default";
                this.fileGiangVien.file = null;
                this.fileGiangVien.fileName = "";
                inputFile.value = "";
                this.$refs.modalImport.closeModal()
            }
        },


    },
};
</script>
