<template>
    <ContentWrapper>
        <template #customTitle>
            CHI TIẾT ĐƠN NHẬN <br>
            <span class="text-secondary">Mã đơn nhận: {{ maSach }}</span> <br>
            <span class="text-secondary">Tên đơn nhận: {{ tenSach }}</span>
        </template>
        <template #ContentPage>
            <div class="row">
                <div class="col-12">
                    <Card>
                        <template #ContentCardHeader>
                            <button type="button" class="btn btn-primary mr-1" @click="themSach">
                                <i class="fas fa-plus-circle"></i>&nbsp;
                                Thêm mới
                            </button>
                            <button type="button" class="btn btn-success mx-1" @click="inDon">
                                <i class="fas fa-file-excel"></i>&nbsp;
                                In đơn
                            </button>
                            <button type="button" class="btn btn-success mx-1" @click="thongKeTL">
                                <i class="fas fa-file-excel"></i>&nbsp;
                                Thống kê loại tài liệu
                            </button>
                        </template>
                        <template #ContentCardBody>
                            <!-- Bảng hiển thị dữ liệu -->
                            <Table :columns="headers" :data="ds.data ?? []" :pagination="ds" :fetchData="fetchData">
                                <!-- Slot cho cột hành động -->
                                <template v-slot:column-actions="{ row }">
                                    <button type="button"
                                            class="btn p-1 btn-primary border-0 bg-transparent text-success shadow-none"
                                            @click="chiTietDiemLuuThong(row)">
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

    <Modal ref="modal">
        <div class="row">
            <div class="col-md-12">
                <Input v-model="Sach.nhan_de" label="Nhan đề" placeholder="Nhan đề..." type="text"/>
            </div>
            <div class="col-md-12">
                <Input v-model="Sach.tac_gia" label="Tác giả" placeholder="Tác giả..." type="text"/>
            </div>
            <div class="col-md-4">
                <Input v-model="Sach.nam_xuat_ban" label="Năm xuất bản" placeholder="Năm XB..." type="text"/>
            </div>
            <div class="col-md-4">
                <Input v-model="Sach.nha_xuat_ban" label="Nhà xuất bản" placeholder="Nhà XB..." type="text"/>
            </div>
            <div class="col-md-4">
                <Input v-model="Sach.noi_xuat_ban" label="Nơi xuất bản" placeholder="Nơi XB.." type="text"/>
            </div>
            <div class="col-md-6">
                <Input v-model="Sach.gia" label="Giá tiền" placeholder="Giá tiền..." type="text"/>
            </div>
            <div class="col-md-6">
                <Input v-model="Sach.so_luong" label="Số chứng từ" placeholder="Số chứng từ..." type="number"/>
            </div>
        </div>
    </Modal>

</template>

<script>

export default {
    name: "pageCTThamSoLuuThong",
    data() {
        return {
            ds: [],
            currentPage: 1,
            Sach: {
                nhan_de: "",
                tac_gia: "",
                nam_xuat_ban: "",
                nha_xuat_ban: "",
                noi_xuat_ban: "",
                gia: "",
                so_luong: "",
            },
            headers: [
                {key: 'index', label: 'STT', sortable: false},
                {key: 'nhan_de', label: 'Nhan đề'},
                {key: 'tac_gia', label: 'Tác giả'},
                {key: 'nam_xuat_ban', label: 'NXB'},
                {key: 'nha_xuat_ban', label: 'Nhà xuất bản'},
                {key: 'noi_xuat_ban', label: 'Nơi xuất bản'},
                {key: 'gia', label: 'Giá'},
                {key: 'so_luong', label: 'SL'},
                {key: 'thanh_tien', label: 'Thành tiền'},
                {key: 'actions', label: 'Hành động', sortable: false},
            ],
            maSach: this.$route.query.ma_don_nhan || '',
            tenSach: this.$route.query.ten_don_nhan || '',
        };
    },
    mounted() {
        this.fetchData(1);
    },
    methods: {
        async fetchData(page = 1) {
            const id = this.$route.params.id_don_nhan;
            try {
                const response = await axios.get(`/api/quan-ly-an-pham/nhan-sach/don-nhan/chi-tiet-don-nhan${id}?page=${page}`);
                if (response.data.status === 200) {
                    this.ds = response.data.data;
                    this.currentPage = this.ds.current_page;
                }
            } catch (error) {
                console.error("Lỗi khi load dữ liệu:", error);
            }
        },

        async themSach() {
            // Reset form cho trường hợp thêm mới
            this.$refs.modal.$data.title = "Thêm mới thông tin";
            this.$refs.modal.$data.save = "Thêm mới";
            this.Sach = {
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

            // Mở modal
            const confirmed = await this.$refs.modal.openModal();
            if (!confirmed) return;

            try {
                const response = await axios.post("/api/quan-ly-an-pham/nhan-sach/don-nhan/chi-tiet-don-nhan", this.Sach);
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
    },
};
</script>

