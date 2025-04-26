<template>
    <ContentWrapper title="QUẢN LÝ ĐƠN NHẬN">
        <template #ContentPage>
            <div class="row">
                <div class="col-12">
                    <Card>
                        <template #ContentCardHeader>
                            <button type="button" class="btn btn-primary" @click="themDonNhan">
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
                                            @click="suaDonNhan(row)">
                                        <i class="fas fa-edit"></i>&nbsp;
                                    </button>
                                    <button type="button"
                                            class="btn p-1 btn-primary border-0 bg-transparent text-danger shadow-none"
                                            @click="xoaDonNhan(row)">
                                        <i class="fas fa-trash-alt"></i>&nbsp;
                                    </button>
                                    <button type="button"
                                            class="btn p-1 btn-primary border-0 bg-transparent text-success shadow-none"
                                            @click="chiTietDonNhan(row)">
                                        <i class="fas fa-eye"></i>&nbsp;
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
            <div class="col-md-12">
                <Input v-model="DonNhan.ten_don_nhan" label="Tên đơn nhận" placeholder="Tên đơn nhận ..." type="text"/>
            </div>
            <div class="col-md-6">
                <SelectOption
                    v-model="DonNhan.id_nguon_nhan"
                    :options="dsNguonNhan"
                    label="Nguồn nhận"
                    placeholder="Chọn Nguồn nhận"
                />
            </div>
            <div class="col-md-6">
                <SelectOption
                    v-model="DonNhan.id_loai_nhap"
                    :options="dsLoaiNhap"
                    label="Loại nhập"
                    placeholder="Chọn loại nhập"
                />
            </div>
            <div class="col-md-6">
                <SelectOption
                    v-model="DonNhan.id_trang_thai_don"
                    :options="dsTrangThaiDon"
                    label="Trạng thái đơn"
                    placeholder="Chọn trạng thái đơn"
                />
            </div>
            <div class="col-md-6">
                <SelectOption
                    v-model="DonNhan.id_nha_cung_cap"
                    :options="dsNhaCungCap"
                    label="Nhà cung cấp"
                    placeholder="Chọn nhà cung cấp"
                />
            </div>
            <div class="col-md-6">
                <Input v-model="DonNhan.ngay_nhan" label="Ngày nhận" type="date"/>
            </div>
            <div class="col-md-6">
                <Input v-model="DonNhan.so_chung_tu" label="Số chứng từ" placeholder="Số chứng từ ..." type="text"/>
            </div>
            <div class="col-md-12">
                <Input v-model="DonNhan.ghi_chu" label="Ghi chú" placeholder="Ghi chú ..." type="text"/>
            </div>
        </div>
    </Modal>
</template>

<script>

export default {
    name: "PageDonNhan",
    data() {
        return {
            ds: [],
            dsNguonNhan: [],
            dsLoaiNhap: [],
            dsTrangThaiDon: [],
            dsNhaCungCap: [],
            currentPage: 1,
            DonNhan: {
                nguoi_tao: "",
                ten_don_nhan: "",
                id_nguon_nhan: "",
                id_loai_nhap: "",
                id_trang_thai_don: "",
                ngay_nhan: "",
                id_nha_cung_cap: "",
                so_chung_tu: "",
                ghi_chu: "",
            },
            headers: [
                {key: 'index', label: 'STT', sortable: false},
                {key: 'id_don_nhan', label: 'Mã đơn'},
                {key: 'ten_don_nhan', label: 'Tên đơn'},
                {key: 'ngay_nhan', label: 'Ngày nhận', format: 'normalDateTime'},
                {key: 'nguon_nhan_hien_thi', label: 'Nguồn nhận'},
                {key: 'ten_nha_cung_cap', label: 'Tên NCC'},
                {key: 'so_luong_tong_hop', label: 'SL Tên/Bản/Trị giá'},
                {key: 'ngay_cap_nhat', label: 'Ngày cập nhật', format: 'datetime'},
                {key: 'ngay_tao', label: 'Ngày tạo', format: 'datetime'},
                {key: 'actions', label: 'Hành động', sortable: false},
            ],
        };
    },
    mounted() {
        this.fetchData(1);
        this.getListNguonNhan();
        this.getListLoaiNhap();
        this.getListTTDon();
        this.getListNCC();
    },
    methods: {
        async fetchData(page = 1) {
            try {
                const response = await axios.get(`/api/quan-ly-an-pham/nhan-sach/don-nhan?page=${page}`);
                if (response.data.status === 200) {
                    this.ds = response.data.data;
                    this.currentPage = this.ds.current_page;
                }
            } catch (error) {
                console.error("Lỗi khi load dữ liệu:", error);
            }
        },

        async getListNguonNhan() {
            try {
                const res = await axios.get("/api/danh-muc/nghiep-vu-bo-sung/nguon-nhan/list-nguon-nhan-select-option");
                if (res.data.status === 200) {
                    this.dsNguonNhan = res.data.data.map(item => ({
                        value: item.id_nguon_nhan,
                        text: item.ten_nguon,
                    }));
                }
            } catch (e) {
                console.error("Lỗi load dữ liệu:", e);
            }
        },

        async getListTTDon() {
            try {
                const res = await axios.get("/api/danh-muc/nghiep-vu-bo-sung/trang-thai-don/list-trang-thai-don-select-option");
                if (res.data.status === 200) {
                    this.dsTrangThaiDon = res.data.data.map(item => ({
                        value: item.id_trang_thai_don,
                        text: item.trang_thai,
                    }));
                }
            } catch (e) {
                console.error("Lỗi load dữ liệu:", e);
            }
        },

        async getListNCC() {
            try {
                const res = await axios.get("/api/danh-muc/nghiep-vu-bo-sung/ncc/list-ncc-select-option");
                if (res.data.status === 200) {
                    this.dsNhaCungCap = res.data.data.map(item => ({
                        value: item.id_nha_cung_cap,
                        text: item.ten_nha_cung_cap,
                    }));
                }
            } catch (e) {
                console.error("Lỗi load dữ liệu:", e);
            }
        },

        async getListLoaiNhap() {
            try {
                const res = await axios.get("/api/danh-muc/nghiep-vu-bo-sung/loai-nhap/list-loai-nhap-select-option");
                if (res.data.status === 200) {
                    this.dsLoaiNhap = res.data.data.map(item => ({
                        value: item.id_loai_nhap,
                        text: item.ten_loai_nhap,
                    }));
                }
            } catch (e) {
                console.error("Lỗi load dữ liệu:", e);
            }
        },

        async themDonNhan() {
            // Reset form cho trường hợp thêm mới
            this.$refs.modal.$data.title = "Thêm mới thông tin";
            this.$refs.modal.$data.save = "Thêm mới";
            this.DonNhan = {
                nguoi_tao: "",
                ten_don_nhan: "",
                id_nguon_nhan: null,
                id_loai_nhap: null,
                id_trang_thai_don: null,
                ngay_nhan: "",
                id_nha_cung_cap: null,
                so_chung_tu: "",
                ghi_chu: "",
            };

            while (true){
                const confirmed = await this.$refs.modal.openModal();
                if (!confirmed) break

                try {
                    const response = await axios.post("/api/quan-ly-an-pham/nhan-sach/don-nhan", this.DonNhan);
                    if (response.data.status === 200) {
                        toastr.success(response.data.message);
                        this.fetchData(this.currentPage);
                        this.$refs.modal.closeModal();
                        break;
                    }
                } catch (err) {
                    if (err.response?.status === 422) {
                        Object.values(err.response.data.errors)
                            .forEach(msg => toastr.error(msg[0]));
                    } else {
                        toastr.error('Thêm thất bại');
                        console.error(err);
                        break;
                    }
                }
            }
        },

        async suaDonNhan(row) {
            this.$refs.modal.$data.title = "Cập nhật thông tin";
            this.$refs.modal.$data.save = "Cập nhật";
            this.DonNhan = {...row};

            const confirmed = await this.$refs.modal.openModal();
            if (!confirmed) return;

            try {
                const response = await axios.put(`/api/quan-ly-an-pham/nhan-sach/don-nhan/${row.id_don_nhan}`, this.DonNhan);
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

        async xoaDonNhan(row) {
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
                        const response = await axios.delete(`/api/quan-ly-an-pham/nhan-sach/don-nhan/${row.id_don_nhan}`);
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
        },

        async chiTietDonNhan(row) {
            this.$router.push({
                name: "pageCTDonNhan",
                params: { id_don_nhan: row.id_don_nhan },
                query: {
                    ma_don_nhan: row.id_don_nhan,
                    ten_don_nhan: row.ten_don_nhan },
            });
        }
    },
};
</script>

