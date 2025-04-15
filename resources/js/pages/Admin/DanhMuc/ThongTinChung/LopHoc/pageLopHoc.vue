<template>
    <ContentWrapper title="QUẢN LÝ LỚP HỌC">
        <template #ContentPage>
            <div class="row">
                <div class="col-12">
                    <Card>
                        <template #ContentCardHeader>
                            <button type="button" class="btn btn-primary" @click="themLopHoc">
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
                                            @click="suaLopHoc(row)">
                                        <i class="fas fa-edit"></i>&nbsp;
                                    </button>
                                    <button type="button"
                                            class="btn p-1 btn-primary border-0 bg-transparent text-danger shadow-none"
                                            @click="xoaLopHoc(row)">
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
                <Input v-model="LopHoc.ma_lop" label="Mã lớp" placeholder="Mã lớp ..." type="text"/>
            </div>
            <div class="col-md-6">
                <Input v-model="LopHoc.han_su_dung" label="Hạn sử dụng" type="date"/>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <Input v-model="LopHoc.ten_lop" label="Tên lớp" placeholder="Tên lớp ..." type="text"/>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <SelectOption
                    v-model="LopHoc.id_don_vi"
                    :options="dsDonVi"
                    label="Đơn vị"
                    placeholder="Chọn đơn vị"
                />
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <SelectOption
                    v-model="LopHoc.id_doi_tuong_ban_doc"
                    :options="dsDTBD"
                    label="Đối tượng"
                    placeholder="Chọn Đối tượng"
                />
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <SelectOption
                    v-model="LopHoc.khoa_hoc"
                    :options="dsKhoa"
                    label="Khoá"
                    placeholder="Chọn khoá"
                />
            </div>
            <div class="col-md-6">
                <SelectOption
                    v-model="LopHoc.nien_khoa"
                    :options="dsNienKhoa"
                    label="Niên khoá"
                    placeholder="Chọn niên khoá"
                />
            </div>
        </div>
    </Modal>
</template>

<script>
import Input from "@/components/ui/Input.vue";
import SelectOption from "@/components/ui/SelectOption.vue";

export default {
    name: "PageLopHoc",
    components: {SelectOption, Input},
    data() {
        return {
            ds: [],
            dsDonVi: [],
            dsDTBD: [],
            dsKhoa: [],
            dsNienKhoa: [],
            currentPage: 1,
            LopHoc: {
                ma_lop: "",
                han_su_dung: "",
                ten_lop: "",
                id_don_vi: "",
                doi_tuong: "",
                khoa_hoc: "",
                nien_khoa: "",
                id_doi_tuong_ban_doc: "",
            },
            headers: [
                {key: 'index', label: 'STT', sortable: false},
                {key: 'ma_lop', label: 'Mã lớp'},
                {key: 'han_su_dung', label: 'Hạn sử dụng', format: 'normalDateTime'},
                {key: 'ten_lop', label: 'Tên lớp'},
                {key: 'ten_don_vi', label: 'Khoa/Đơn vị trực thuộc'},
                {key: 'khoa_hoc', label: 'Khoá'},
                {key: 'nien_khoa', label: 'Niên khoá'},
                {key: 'ngay_cap_nhat', label: 'Ngày cập nhật', format: 'datetime'},
                {key: 'ngay_tao', label: 'Ngày tạo', format: 'datetime'},
                {key: 'actions', label: 'Hành động', sortable: false},
            ],
        };
    },
    mounted() {
        this.fetchData(1);
        this.getListDonVi();
        this.getListDTBD();
        this.getListKhoahoc();
        this.getListNienKhoa();
    },
    methods: {
        async fetchData(page = 1) {
            try {
                const response = await axios.get(`/api/danh-muc/thong-tin-chung/lop-hoc?page=${page}`);
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
                const res = await axios.get("/api/danh-muc/thong-tin-chung/don-vi/list-don-vi-select-option");
                if (res.data.status === 200) {
                    this.dsDonVi = res.data.data.map(item => ({
                        value: item.id_don_vi,
                        text: item.ten_don_vi,
                    }));
                }
            } catch (e) {
                console.error("Lỗi load dữ liệu:", e);
            }
        },

        async getListDTBD() {
            try {
                const res = await axios.get("/api/danh-muc/nghiep-vu-luu-thong/doi-tuong-ban-doc/list-dtbd-select-option");
                if (res.data.status === 200) {
                    this.dsDTBD = res.data.data.map(item => ({
                        value: item.id_doi_tuong_ban_doc,
                        text: item.ten_doi_tuong_ban_doc,
                    }));
                }
            } catch (e) {
                console.error("Lỗi load dữ liệu:", e);
            }
        },

        async getListKhoahoc() {
            try {
                const res = await axios.get("/api/danh-muc/thong-tin-chung/hoc-ky/list-khoa-hoc");
                if (res.data.status === 200) {
                    this.dsKhoa = res.data.data.map(item => ({
                        value: item,
                        text: `Khóa ${item}`
                    }));
                }
            } catch (e) {
                console.error("Lỗi load dữ liệu:", e);
            }
        },

        async getListNienKhoa() {
            try {
                const res = await axios.get("/api/danh-muc/thong-tin-chung/hoc-ky/list-nien-khoa");
                if (res.data.status === 200) {
                    this.dsNienKhoa = res.data.data.map(item => ({
                        value: item,
                        text: item,
                    }));
                }
            } catch (e) {
                console.error("Lỗi load dữ liệu:", e);
            }
        },

        async themLopHoc() {
            // Reset form cho trường hợp thêm mới
            this.$refs.modal.$data.title = "Thêm mới thông tin";
            this.$refs.modal.$data.save = "Thêm mới";
            this.LopHoc = {
                ma_lop: "",
                han_su_dung: "",
                ten_lop: "",
                id_don_vi: "",
                id_doi_tuong_ban_doc: "",
                khoa_hoc: "",
                nien_khoa: "",
            };

            // Mở modal
            const confirmed = await this.$refs.modal.openModal();
            if (!confirmed) return;

            // Kiểm tra validate
            try {
                const response = await axios.post("/api/danh-muc/thong-tin-chung/lop-hoc", this.LopHoc);
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

        async suaLopHoc(row) {
            try {
                const res = await axios.get(`/api/danh-muc/thong-tin-chung/lop-hoc/${row.id_lop_hoc}`);
                if (res.data.status === 200) {
                    const lop = res.data.data;

                    // Set tiêu đề và dữ liệu vào form
                    this.$refs.modal.$data.title = "Cập nhật thông tin";
                    this.$refs.modal.$data.save = "Cập nhật";
                    this.LopHoc = {
                        ma_lop: lop.ma_lop || "",
                        han_su_dung: lop.han_su_dung?.slice(0, 10) || "",
                        ten_lop: lop.ten_lop || "",
                        id_don_vi: String(lop.id_don_vi || ""),
                        id_doi_tuong_ban_doc: String(lop.id_doi_tuong_ban_doc || ""),
                        khoa_hoc: String(lop.khoa_hoc || ""),
                        nien_khoa: lop.nien_khoa || "",
                    };

                    // Mở modal
                    const confirmed = await this.$refs.modal.openModal();
                    if (!confirmed) return;

                    // Gửi yêu cầu cập nhật
                    const response = await axios.put(
                        `/api/danh-muc/thong-tin-chung/lop-hoc/${row.id_lop_hoc}`,
                        this.LopHoc
                    );

                    if (response.data.status === 200) {
                        toastr.success(response.data.message);
                        this.fetchData(this.currentPage);
                        this.$refs.modal.closeModal();
                    }
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
                    console.error("Lỗi khi cập nhật:", error);
                    toastr.error("Cập nhật thất bại!");
                }
            }
        },
        async xoaLopHoc(row) {
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
                        const response = await axios.delete(`/api/danh-muc/thong-tin-chung/lop-hoc/${row.id_lop_hoc}`);
                        if (response.data.status === 200) {
                            toastr.success(response.data.message);
                            this.fetchData(this.currentPage);
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
                            console.error("Lỗi khi cập nhật:", error);
                            toastr.error("Cập nhật thất bại!");
                        }
                    }

                }
            });
        }
    },
};
</script>
