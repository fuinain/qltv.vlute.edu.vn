<template>
    <ContentWrapper title="QUẢN LÝ ĐỐI TƯỢNG BẠN ĐỌC">
        <template #ContentPage>
            <div class="row">
                <div class="col-12">
                    <Card>
                        <template #ContentCardHeader>
                            <button type="button" class="btn btn-primary" @click="themDoiTuongBanDoc">
                                <i class="fas fa-plus-circle"></i>&nbsp;
                                Thêm mới
                            </button>
                        </template>
                        <template #ContentCardBody>
                            <!-- Bảng hiển thị dữ liệu -->
                            <Table :columns="headers" :data="ds.data ?? []" :pagination="ds" :fetchData="fetchData">
                                <!-- Slot cho cột hành động -->
                                <template v-slot:column-actions="{ row }">
                                    <button type="button"
                                            class="btn p-1 btn-primary border-0 bg-transparent text-primary shadow-none"
                                            @click="suaDoiTuongBanDoc(row)">
                                        <i class="fas fa-edit"></i>&nbsp;
                                    </button>
                                    <button type="button"
                                            class="btn p-1 btn-primary border-0 bg-transparent text-danger shadow-none"
                                            @click="xoaDoiTuongBanDoc(row)">
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
                <Input v-model="DoiTuongBanDoc.ma_doi_tuong_ban_doc" label="Mã đối tượng bạn đọc" placeholder="Mã đối tượng bạn đọc ..." type="text"/>
            </div>
            <div class="col-md-6">
                <Input v-model="DoiTuongBanDoc.ten_doi_tuong_ban_doc" label="Tên đối tượng bạn đọc" placeholder="Tên đối tượng bạn đọc ..." type="text"/>
            </div>
        </div>
    </Modal>
</template>

<script>
export default {
    name: "pageDoiTuongBanDoc",

    data() {
        return {
            ds: [],
            currentPage: 1,
            DoiTuongBanDoc: {
                ma_doi_tuong_ban_doc: "",
                ten_doi_tuong_ban_doc: ""
            },
            headers: [
                {key: 'index', label: 'STT', sortable: false},
                {key: 'ma_doi_tuong_ban_doc', label: 'Mã đối tượng bạn đọc'},
                {key: 'ten_doi_tuong_ban_doc', label: 'Tên đối tượng bạn đọc'},
                {key: 'ngay_cap_nhat', label: 'Ngày cập nhật', format: 'datetime'},
                {key: 'ngay_tao', label: 'Ngày tạo', format: 'datetime'},
                {key: 'actions', label: 'Hành động', sortable: false},
            ],
        };
    },
    mounted() {
        this.fetchData(1);
    },
    methods: {
        async fetchData(page = 1) {
            try {
                const response = await axios.get(`/api/danh-muc/nghiep-vu-luu-thong/doi-tuong-ban-doc?page=${page}`);
                if (response.data.status === 200) {
                    this.ds = response.data.data;
                    this.currentPage = this.ds.current_page;
                }
            } catch (error) {
                console.error("Lỗi khi load dữ liệu:", error);
            }
        },

        async themDoiTuongBanDoc() {
            // Reset form cho trường hợp thêm mới
            this.$refs.modal.$data.title = "Thêm mới thông tin";
            this.$refs.modal.$data.save = "Thêm mới";
            this.DoiTuongBanDoc = {
                ma_doi_tuong_ban_doc: "",
                ten_doi_tuong_ban_doc: ""
            };

            // Mở modal
            const confirmed = await this.$refs.modal.openModal();
            if (!confirmed) return;

            // Kiểm tra validate
            if (
                !this.DoiTuongBanDoc.ma_doi_tuong_ban_doc.trim() ||
                !this.DoiTuongBanDoc.ten_doi_tuong_ban_doc.trim()
            ) {
                toastr.error("Vui lòng nhập đầy đủ thông tin bắt buộc.");
                return;
            }

            try {
                const response = await axios.post("/api/danh-muc/nghiep-vu-luu-thong/doi-tuong-ban-doc", this.DoiTuongBanDoc);
                if (response.data.status === 200) {
                    toastr.success(response.data.message);
                    this.fetchData(this.currentPage);
                    this.$refs.modal.closeModal();
                }
            } catch (error) {
                console.error("Lỗi khi thêm:", error);
                toastr.error("Thêm thất bại!");
            }
        },

        async suaDoiTuongBanDoc(row) {
            // Set tiêu đề và điền dữ liệu hiện có vào form
            this.$refs.modal.$data.title = "Cập nhật thông tin";
            this.$refs.modal.$data.save = "Cập nhật";
            this.DoiTuongBanDoc = {...row};

            // Mở modal để chỉnh sửa
            const confirmed = await this.$refs.modal.openModal();
            if (!confirmed) return;

            try {
                const response = await axios.put(`/api/danh-muc/nghiep-vu-luu-thong/doi-tuong-ban-doc/${row.id_doi_tuong_ban_doc}`, this.DoiTuongBanDoc);
                if (response.data.status === 200) {
                    toastr.success(response.data.message);
                    this.fetchData(this.currentPage);
                    this.$refs.modal.closeModal();
                }
            } catch (error) {
                console.error("Lỗi khi cập nhật:", error);
                toastr.error("Cập nhật thất bại!");
            }
        },

        async xoaDoiTuongBanDoc(row) {
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
                        const response = await axios.delete(`/api/danh-muc/nghiep-vu-luu-thong/doi-tuong-ban-doc/${row.id_doi_tuong_ban_doc}`);
                        if (response.data.status === 200) {
                            toastr.success(response.data.message);
                            this.fetchData(this.currentPage);
                        }
                    } catch (error) {
                        console.error("Lỗi khi xoá:", error);
                        toastr.error("Xoá thất bại!");
                    }
                }
            });
        }
    },
};
</script>

