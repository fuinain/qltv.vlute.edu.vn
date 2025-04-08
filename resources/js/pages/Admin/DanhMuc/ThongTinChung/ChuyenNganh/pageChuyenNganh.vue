<template>
    <ContentWrapper title="QUẢN LÝ CHUYÊN NGÀNH">
        <template #ContentPage>
            <div class="row">
                <div class="col-12">
                    <Card>
                        <template #ContentCardHeader>
                            <button type="button" class="btn btn-primary" @click="themChuyenNganh">
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
                                            @click="suaChuyenNganh(row)">
                                        <i class="fas fa-edit"></i>&nbsp;
                                    </button>
                                    <button type="button"
                                            class="btn p-1 btn-primary border-0 bg-transparent text-danger shadow-none"
                                            @click="xoaChuyenNganh(row)">
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
                <Input v-model="ChuyenNganh.ma_chuyen_nganh"
                       label="Mã chuyên ngành"
                       placeholder="Mã chuyên ngành ..."
                       type="text"
                />
            </div>
            <div class="col-6">
                <Input v-model="ChuyenNganh.ten_chuyen_nganh"
                       label="Tên chuyên ngành"
                       placeholder="Tên chuyên ngành ..."
                       type="text"
                />
            </div>
            <div class="col-12">
                <SelectOption
                    v-model="ChuyenNganh.id_don_vi"
                    :options="dsDonVi"
                    label="Đơn vị"
                    placeholder="Chọn đơn vị"
                />

            </div>
        </div>
    </Modal>
</template>

<script>
import SelectOption from "@/components/ui/SelectOption.vue";

export default {
    name: "PageChuyenNganh",
    components: {SelectOption},

    data() {
        return {
            ds: [],
            currentPage: 1,
            ChuyenNganh: {
                ma_chuyen_nganh: "",
                ten_chuyen_nganh: "",
                id_don_vi: "",
            },
            headers: [
                {key: 'index', label: 'STT', sortable: false},
                {key: 'ma_chuyen_nganh', label: 'Mã chuyên ngành'},
                {key: 'ten_chuyen_nganh', label: 'Tên chuyên ngành'},
                {key: 'ten_don_vi', label: 'Đơn vị'},
                {key: 'ngay_cap_nhat', label: 'Ngày cập nhật', format: 'datetime'},
                {key: 'ngay_tao', label: 'Ngày tạo', format: 'datetime'},
                {key: 'actions', label: 'Hành động', sortable: false}
            ],
            dsDonVi: [],
        };
    },
    mounted() {
        this.fetchData(1);
        this.getListDonVi();
    },
    methods: {
        async fetchData(page = 1) {
            try {
                const response = await axios.get(`/api/chuyen-nganh?page=${page}`);
                if (response.data.status === 200) {
                    this.ds = response.data.data;
                    this.currentPage = this.ds.current_page;
                }
            } catch (error) {
                console.error("Lỗi khi load dữ liệu:", error);
            }
        },

        async getListDonVi() {
            try {
                const res = await axios.get("/api/don-vi/list-don-vi-select-option");
                if (res.data.status === 200) {
                    // Map dữ liệu từ API về định dạng: { value, text }
                    this.dsDonVi = res.data.data.map(item => ({
                        value: item.id_don_vi,
                        text: item.ten_don_vi,
                    }));
                }
            } catch (e) {
                console.error("Lỗi load danh sách đơn vị:", e);
            }
        },

        async themChuyenNganh() {
            // Reset form cho trường hợp thêm mới
            this.$refs.modal.$data.title = "Thêm mới thông tin";
            this.$refs.modal.$data.save = "Thêm mới";
            this.ChuyenNganh = {
                ma_chuyen_nganh: "",
                ten_chuyen_nganh: "",
                id_don_vi: "",
            };

            // Mở modal
            const confirmed = await this.$refs.modal.openModal();
            if (!confirmed) return;

            // Kiểm tra validate
            if (
                !this.ChuyenNganh.ma_chuyen_nganh.trim() ||
                !this.ChuyenNganh.ten_chuyen_nganh.trim() ||
                !this.ChuyenNganh.id_don_vi
            ) {
                toastr.error("Vui lòng nhập đầy đủ thông tin bắt buộc.");
                return;
            }

            try {
                const response = await axios.post("/api/chuyen-nganh", this.ChuyenNganh);
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

        async suaChuyenNganh(row) {
            this.$refs.modal.$data.title = "Cập nhật thông tin";
            this.$refs.modal.$data.save = "Cập nhật";
            this.ChuyenNganh = {...row};

            // Kiểm tra giá trị của id_don_vi
            console.log('ChuyenNganh.id_don_vi:', this.ChuyenNganh.id_don_vi);

            // Mở modal để chỉnh sửa
            const confirmed = await this.$refs.modal.openModal();
            if (!confirmed) return;

            try {
                const response = await axios.put(`/api/chuyen-nganh/${row.id_chuyen_nganh}`, this.ChuyenNganh);
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


        async xoaChuyenNganh(row) {
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
                        const response = await axios.delete(`/api/chuyen-nganh/${row.id_chuyen_nganh}`);
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
