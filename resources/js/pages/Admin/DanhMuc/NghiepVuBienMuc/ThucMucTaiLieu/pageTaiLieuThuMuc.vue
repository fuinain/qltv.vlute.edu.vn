<template>
    <ContentWrapper title="CẤU HÌNH THƯ MỤC TÀI LIỆU">
        <template #ContentPage>
            <div class="row">
                <div class="col-12">
                    <Card>
                        <template #ContentCardHeader>
                            <button type="button" class="btn btn-primary" @click="themTaiLieu">
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
                                            @click="suaTaiLieu(row)">
                                        <i class="fas fa-edit"></i>&nbsp;
                                    </button>
                                    <button type="button"
                                            class="btn p-1 btn-primary border-0 bg-transparent text-danger shadow-none"
                                            @click="xoaTaiLieu(row)">
                                        <i class="fas fa-trash-alt"></i>&nbsp;
                                    </button>
                                    <button type="button"
                                            class="btn p-1 btn-primary border-0 bg-transparent text-secondary shadow-none"
                                            @click="">
                                        <i class="fas fa-cog"></i>&nbsp;
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
                <Input v-model="TaiLieu.ma_tai_lieu" label="Mã tài liệu" placeholder="Mã tài liệu ..." type="text"/>
            </div>
            <div class="col-md-6">
                <Input v-model="TaiLieu.ten_tai_lieu" label="Tên tài liệu" placeholder="Tên tài liệu ..." type="text"/>
            </div>
        </div>
    </Modal>
</template>

<script>
export default {
    name: "PageTaiLieu",

    data() {
        return {
            ds: [],
            currentPage: 1,
            TaiLieu: {
                ma_tai_lieu: "",
                ten_tai_lieu: ""
            },
            headers: [
                {key: 'index', label: 'STT', sortable: false},
                {key: 'ma_tai_lieu', label: 'Mã tài liệu'},
                {key: 'ten_tai_lieu', label: 'Tên tài liệu'},
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
                const response = await axios.get(`/api/danh-muc/nghiep-vu-bien-muc/tai-lieu?page=${page}`);
                if (response.data.status === 200) {
                    this.ds = response.data.data;
                    this.currentPage = this.ds.current_page;
                }
            } catch (error) {
                console.error("Lỗi khi load dữ liệu:", error);
            }
        },

        async themTaiLieu() {
            // Reset form cho trường hợp thêm mới
            this.$refs.modal.$data.title = "Thêm mới thông tin";
            this.$refs.modal.$data.save = "Thêm mới";
            this.TaiLieu = {
                ma_tai_lieu: "",
                ten_tai_lieu: ""
            };

            // Mở modal
            const confirmed = await this.$refs.modal.openModal();
            if (!confirmed) return;

            // Kiểm tra validate
            if (
                !this.TaiLieu.ma_tai_lieu.trim() ||
                !this.TaiLieu.ten_tai_lieu.trim()
            ) {
                toastr.error("Vui lòng nhập đầy đủ thông tin bắt buộc.");
                return;
            }

            try {
                const response = await axios.post("/api/danh-muc/nghiep-vu-bien-muc/tai-lieu", this.TaiLieu);
                if (response.data.status === 200) {
                    toastr.success(response.data.message);
                    this.fetchData(this.currentPage);
                    this.$refs.modal.closeModal();
                }
            } catch (error) {
                if (error.response && error.response.status === 422) {
                    const errors = error.response.data.errors;
                    for (const key in errors) {
                        if (errors.hasOwnProperty(key)) {
                            toastr.error(errors[key][0]);
                        }
                    }
                } else {
                    console.error("Lỗi khi thêm:", error);
                    toastr.error("Thêm thất bại!");
                }
            }
        },

        async suaTaiLieu(row) {
            // Set tiêu đề và điền dữ liệu hiện có vào form
            this.$refs.modal.$data.title = "Cập nhật thông tin";
            this.$refs.modal.$data.save = "Cập nhật";
            this.TaiLieu = {...row};

            // Mở modal để chỉnh sửa
            const confirmed = await this.$refs.modal.openModal();
            if (!confirmed) return;

            try {
                const response = await axios.put(`/api/danh-muc/nghiep-vu-bien-muc/tai-lieu/${row.id_chuc_vu}`, this.TaiLieu);
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

        async xoaTaiLieu(row) {
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
                        const response = await axios.delete(`/api/danh-muc/nghiep-vu-bien-muc/tai-lieu/${row.id_chuc_vu}`);
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
