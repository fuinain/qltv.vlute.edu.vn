<template>
    <ContentWrapper title="KHAI BÁO ĐIỂM LƯU THÔNG">
        <template #ContentPage>
            <div class="row">
                <div class="col-12">
                    <Card>
                        <template #ContentCardHeader>
                            <button type="button" class="btn btn-primary" @click="themDiemLuuThong">
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
                                            @click="suaDiemLuuThong(row)">
                                        <i class="fas fa-edit"></i>&nbsp;
                                    </button>
                                    <button type="button"
                                            class="btn p-1 btn-primary border-0 bg-transparent text-danger shadow-none"
                                            @click="xoaDiemLuuThong(row)">
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
                <Input v-model="DiemLuuThong.ma_loai" label="Mã loại" type="text"/>
            </div>
            <div class="col-md-6">
                <Input v-model="DiemLuuThong.ten_diem" label="Tên điểm" type="text"/>
            </div>
            <div class="col-12 mt-3">
                <!-- Select2 sẽ được dùng ở đây-->
                <Select2
                    label="Danh mục kho tài liệu"
                    :options="dsKhoAnPham"
                    v-model="DiemLuuThong.id_kho_an_pham"
                />
            </div>
        </div>
    </Modal>

</template>

<script>
export default {
    name: "PageDiemLuuThong",

    data() {
        return {
            ds: [],
            dsKhoAnPham: [],
            currentPage: 1,
            DiemLuuThong: {
                ma_loai: "",
                ten_diem: "",
                id_kho_an_pham: []
            },
            headers: [
                {key: 'index', label: 'STT', sortable: false},
                {key: 'ma_loai', label: 'Mã loại'},
                {key: 'ten_diem', label: 'Tên điểm lưu thông'},
                {key: 'ten_kho', label: 'Danh mục kho tài liệu'},
                {key: 'ngay_cap_nhat', label: 'Ngày cập nhật', format: 'datetime'},
                {key: 'ngay_tao', label: 'Ngày tạo', format: 'datetime'},
                {key: 'actions', label: 'Hành động', sortable: false},
            ],
        };
    },
    mounted() {
        this.fetchData(1);
        this.getListKhoAnPham();
    },
    methods: {
        async getListKhoAnPham() {
            try {
                const res = await axios.get("/api/quan-ly-an-pham/kho-an-pham/danh-muc-kho/list-dmkho-select-option");
                if (res.data.status === 200) {
                    this.dsKhoAnPham = res.data.data.map(item => ({
                        value: item.id_kho_an_pham,
                        text: item.ten_kho,
                    }));
                }
            } catch (e) {
                console.error("Lỗi load dữ liệu:", e);
            }
        },

        async fetchData(page = 1) {
            try {
                const response = await axios.get(`/api/danh-muc/nghiep-vu-luu-thong/diem-luu-thong?page=${page}`);
                if (response.data.status === 200) {
                    this.ds = response.data.data;
                    this.currentPage = this.ds.current_page;
                }
            } catch (error) {
                console.error("Lỗi khi load dữ liệu:", error);
            }
        },

        async themDiemLuuThong() {
            // Reset form cho trường hợp thêm mới
            this.$refs.modal.$data.title = "Thêm mới thông tin";
            this.$refs.modal.$data.save = "Thêm mới";
            this.DiemLuuThong = {
                ma_loai: "",
                ten_diem: "",
                id_kho_an_pham: []
            };

            // Mở modal
            const confirmed = await this.$refs.modal.openModal();
            if (!confirmed) return;

            try {
                const payload = { ...this.DiemLuuThong };
                const response = await axios.post(
                    "/api/danh-muc/nghiep-vu-luu-thong/diem-luu-thong",
                    payload
                );
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
        async suaDiemLuuThong(row) {
            // Set tiêu đề và điền dữ liệu hiện có vào form
            this.$refs.modal.$data.title = "Cập nhật thông tin";
            this.$refs.modal.$data.save = "Cập nhật";
            this.DiemLuuThong = {...row};

            // Mở modal để chỉnh sửa
            const confirmed = await this.$refs.modal.openModal();
            if (!confirmed) return;

            try {
                const response = await axios.put(`/api/danh-muc/nghiep-vu-luu-thong/diem-luu-thong/${row.id_diem_luu_thong}`, this.DiemLuuThong);
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

        async xoaDiemLuuThong(row) {
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
                        const response = await axios.delete(`/api/danh-muc/nghiep-vu-luu-thong/diem-luu-thong/${row.id_diem_luu_thong}`);
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
