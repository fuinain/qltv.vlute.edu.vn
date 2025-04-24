<template>
    <ContentWrapper title="QUẢN LÝ NHÀ CUNG CẤP">
        <template #ContentPage>
            <div class="row">
                <div class="col-12">
                    <Card>
                        <template #ContentCardHeader>
                            <button type="button" class="btn btn-primary" @click="themNCC">
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
                                            @click="suaNCC(row)">
                                        <i class="fas fa-edit"></i>&nbsp;
                                    </button>
                                    <button type="button"
                                            class="btn p-1 btn-primary border-0 bg-transparent text-danger shadow-none"
                                            @click="xoaNCC(row)">
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
                <Input v-model="NCC.ma_nha_cung_cap" label="Mã NCC" placeholder="Mã NCC..." type="text"/>
            </div>
            <div class="col-md-6">
                <Input v-model="NCC.ten_nha_cung_cap" label="Tên NCC" placeholder="Tên NCC..." type="text"/>
            </div>
            <div class="col-md-6">
                <Input v-model="NCC.dia_chi" label="Địa chỉ" placeholder="Địa chỉ..." type="text"/>
            </div>
            <div class="col-md-6">
                <Input v-model="NCC.dien_thoai" label="SĐT" placeholder="SĐT..." type="text"/>
            </div>
            <div class="col-md-6">
                <Input v-model="NCC.email" label="Email" placeholder="Email..." type="text"/>
            </div>
            <div class="col-md-6">
                <Input v-model="NCC.lien_he" label="Liên hệ" placeholder="Liên hệ..." type="text"/>
            </div>
            <div class="col-md-6">
                <Input v-model="NCC.stk" label="Số tài khoản" placeholder="STK..." type="text"/>
            </div>
            <div class="col-md-6">
                <Input v-model="NCC.ngan_hang" label="Ngân hàng" placeholder="Ngân hàng..." type="text"/>
            </div>
        </div>
    </Modal>
</template>

<script>
export default {
    name: "PageNCC",

    data() {
        return {
            ds: [],
            currentPage: 1,
            NCC: {
                ma_nha_cung_cap: "",
                ten_nha_cung_cap: "",
                dia_chi: "",
                dien_thoai: "",
                email: "",
                lien_he: "",
                stk: "",
                ngan_hang: "",
            },
            headers: [
                {key: 'index', label: 'STT', sortable: false},
                {key: 'ma_nha_cung_cap', label: 'Mã NCC'},
                {key: 'ten_nha_cung_cap', label: 'Tên NCC'},
                {key: 'dia_chi', label: 'Địa chỉ'},
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
                const response = await axios.get(`/api/danh-muc/nghiep-vu-bo-sung/ncc?page=${page}`);
                if (response.data.status === 200) {
                    this.ds = response.data.data;
                    this.currentPage = this.ds.current_page;
                }
            } catch (error) {
                console.error("Lỗi khi load dữ liệu:", error);
            }
        },

        async themNCC() {
            // Reset form cho trường hợp thêm mới
            this.$refs.modal.$data.title = "Thêm mới thông tin";
            this.$refs.modal.$data.save = "Thêm mới";
            this.NCC = {
                ma_nha_cung_cap: "",
                ten_nha_cung_cap: "",
                dia_chi: "",
                dien_thoai: "",
                email: "",
                lien_he: "",
                stk: "",
                ngan_hang: "",
            };

            // Mở modal
            const confirmed = await this.$refs.modal.openModal();
            if (!confirmed) return;

            try {
                const response = await axios.post("/api/danh-muc/nghiep-vu-bo-sung/ncc", this.NCC);
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

        async suaNCC(row) {
            // Set tiêu đề và điền dữ liệu hiện có vào form
            this.$refs.modal.$data.title = "Cập nhật thông tin";
            this.$refs.modal.$data.save = "Cập nhật";
            this.NCC = {...row};

            // Mở modal để chỉnh sửa
            const confirmed = await this.$refs.modal.openModal();
            if (!confirmed) return;

            try {
                const response = await axios.put(`/api/danh-muc/nghiep-vu-bo-sung/ncc/${row.id_nha_cung_cap}`, this.NCC);
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

        async xoaNCC(row) {
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
                        const response = await axios.delete(`/api/danh-muc/nghiep-vu-bo-sung/ncc/${row.id_nha_cung_cap}`);
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
