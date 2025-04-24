<template>
    <ContentWrapper title="QUẢN LÝ NGUỒN NHẬN">
        <template #ContentPage>
            <div class="row">
                <div class="col-12">
                    <Card>
                        <template #ContentCardHeader>
                            <button type="button" class="btn btn-primary" @click="themNguonNhan">
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
                                            @click="suaNguonNhan(row)">
                                        <i class="fas fa-edit"></i>&nbsp;
                                    </button>
                                    <button type="button"
                                            class="btn p-1 btn-primary border-0 bg-transparent text-danger shadow-none"
                                            @click="xoaNguonNhan(row)">
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
                <Input v-model="NguonNhan.ma_nguon_nhan" label="Mã nguồn nhận" placeholder="Mã nguồn nhận..." type="text"/>
            </div>
            <div class="col-md-6">
                <Input v-model="NguonNhan.ten_nguon" label="Tên nguồn nhận" placeholder="Tên nguồn nhận..." type="text"/>
            </div>
            <div class="col-md-12">
                <Input v-model="NguonNhan.kinh_phi" label="Kinh phí" placeholder="Kinh phí..." type="text"/>
            </div>
        </div>
    </Modal>
</template>

<script>
export default {
    name: "PageNguonNhan",

    data() {
        return {
            ds: [],
            currentPage: 1,
            NguonNhan: {
                ma_nguon_nhan: "",
                ten_nguon: "",
                kinh_phi: "",
            },
            headers: [
                {key: 'index', label: 'STT', sortable: false},
                {key: 'ma_nguon_nhan', label: 'Trạng thái đơn'},
                {key: 'ten_nguon', label: 'Tên nguồn'},
                {key: 'kinh_phi', label: 'Kinh phí'},
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
                const response = await axios.get(`/api/danh-muc/nghiep-vu-bo-sung/nguon-nhan?page=${page}`);
                if (response.data.status === 200) {
                    this.ds = response.data.data;
                    this.currentPage = this.ds.current_page;
                }
            } catch (error) {
                console.error("Lỗi khi load dữ liệu:", error);
            }
        },

        async themNguonNhan() {
            // Reset form cho trường hợp thêm mới
            this.$refs.modal.$data.title = "Thêm mới thông tin";
            this.$refs.modal.$data.save = "Thêm mới";
            this.NguonNhan = {
                trang_thai: "",
            };

            // Mở modal
            const confirmed = await this.$refs.modal.openModal();
            if (!confirmed) return;

            try {
                const response = await axios.post("/api/danh-muc/nghiep-vu-bo-sung/nguon-nhan", this.NguonNhan);
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
                    toastr.error("Thêm thất bại!");
                }
            }
        },

        async suaNguonNhan(row) {
            // Set tiêu đề và điền dữ liệu hiện có vào form
            this.$refs.modal.$data.title = "Cập nhật thông tin";
            this.$refs.modal.$data.save = "Cập nhật";
            this.NguonNhan = {...row};

            // Mở modal để chỉnh sửa
            const confirmed = await this.$refs.modal.openModal();
            if (!confirmed) return;

            try {
                const response = await axios.put(`/api/danh-muc/nghiep-vu-bo-sung/nguon-nhan/${row.id_nguon_nhan}`, this.NguonNhan);
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

        async xoaNguonNhan(row) {
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
                        const response = await axios.delete(`/api/danh-muc/nghiep-vu-bo-sung/nguon-nhan/${row.id_nguon_nhan}`);
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
