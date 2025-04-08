<template>
    <ContentWrapper title="QUẢN LÝ PHÒNG KHOA">
        <template #ContentPage>
            <div class="row">
                <div class="col-12">
                    <Card>
                        <template #ContentCardHeader>
                            <button type="button" class="btn btn-primary" @click="themPhongKhoa">
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
                                            class="btn p-0 btn-primary border-0 bg-transparent text-primary shadow-none"
                                            @click="suaPhongKhoa(row)">
                                        <i class="fas fa-edit"></i>&nbsp;
                                    </button>
                                    |
                                    <button type="button"
                                            class="btn p-0 btn-primary border-0 bg-transparent text-danger shadow-none"
                                            @click="xoaPhongKhoa(row)">
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
            <div class="col-6">
                <Input v-model="DonVi.ma_don_vi" label="Mã đơn vị" placeholder="Mã đơn vị ..." type="text"/>
            </div>
            <div class="col-6">
                <Input v-model="DonVi.ten_don_vi" label="Tên đơn vị" placeholder="Tên đơn vị ..." type="text"/>
            </div>
        </div>
    </Modal>
</template>

<script>
export default {
    name: "PagePhongKhoa",
    data() {
        return {
            ds: [],
            currentPage: 1,
            // Dữ liệu cho form thêm/sửa
            DonVi: {
                ma_don_vi: "",
                ten_don_vi: ""
            },
            headers: [
                {key: 'index', label: 'STT', sortable: false},
                {key: 'ma_don_vi', label: 'Mã đơn vị'},
                {key: 'ten_don_vi', label: 'Tên đơn vị'},
                {key: 'ngay_cap_nhat', label: 'Ngày cập nhật', format: 'datetime'},
                {key: 'ngay_tao', label: 'Ngày tạo', format: 'datetime'},
                {key: 'actions', label: 'Hành động', sortable: false}
            ],
        };
    },
    mounted() {
        this.fetchData(1);
    },
    methods: {
        async fetchData(page = 1) {
            try {
                const response = await axios.get(`/api/don-vi?page=${page}`);
                if (response.data.status === 200) {
                    this.ds = response.data.data;
                    this.currentPage = this.ds.current_page;
                }
            } catch (error) {
                console.error("Lỗi khi load dữ liệu:", error);
            }
        },

        async themPhongKhoa() {
            // Reset form cho trường hợp thêm mới
            this.$refs.modal.$data.title = "Thêm mới thông tin";
            this.$refs.modal.$data.save = "Thêm mới";
            this.DonVi = {
                ma_don_vi: "",
                ten_don_vi: ""
            };

            // Mở modal
            const confirmed = await this.$refs.modal.openModal();
            if (!confirmed) return;

            // Kiểm tra validate
            if (
                !this.DonVi.ma_don_vi.trim() ||
                !this.DonVi.ten_don_vi.trim()
            ) {
                toastr.error("Vui lòng nhập đầy đủ thông tin bắt buộc.");
                return;
            }

            try {
                const response = await axios.post("/api/don-vi", this.DonVi);
                if (response.data.status === 200) {
                    toastr.success(response.data.message);
                    this.fetchData(this.currentPage);
                    this.$refs.modal.closeModal();
                }
            } catch (error) {
                console.error("Lỗi khi thêm:", error);
                toastr.error("Thêm đơn vị thất bại!");
            }
        },

        async suaPhongKhoa(row) {
            // Set tiêu đề và điền dữ liệu hiện có vào form
            this.$refs.modal.$data.title = "Cập nhật thông tin";
            this.$refs.modal.$data.save = "Cập nhật";
            this.DonVi = {...row};

            // Mở modal để chỉnh sửa
            const confirmed = await this.$refs.modal.openModal();
            if (!confirmed) return;

            try {
                const response = await axios.put(`/api/don-vi/${row.id_don_vi}`, this.DonVi);
                if (response.data.status === 200) {
                    toastr.success(response.data.message);
                    this.fetchData(this.currentPage);
                    this.$refs.modal.closeModal();
                }
            } catch (error) {
                console.error("Lỗi khi cập nhật:", error);
                toastr.error("Cập nhật đơn vị thất bại!");
            }
        },

        xoaPhongKhoa(row) {
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
                        const response = await axios.delete(`/api/don-vi/${row.id_don_vi}`);
                        if (response.data.status === 200) {
                            toastr.success(response.data.message);
                            this.fetchData(this.currentPage);
                        }
                    } catch (error) {
                        console.error("Lỗi khi xoá:", error);
                        toastr.error("Xoá đơn vị thất bại!");
                    }
                }
            });
        }
    },
};
</script>
