<template>
    <ContentWrapper>
        <template #customTitle>
            CHI TIẾT ĐƠN NHẬN <br>
            <span class="text-secondary">Mã đơn nhận: {{ maDonNhan }}</span> <br>
            <span class="text-secondary">Tên đơn nhận: {{ tenDonNhan }}</span>
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
                                            class="btn p-1 btn-primary border-0 bg-transparent text-primary shadow-none"
                                            @click="suaCTDonNhan(row)">
                                        <i class="fas fa-edit"></i>&nbsp;
                                    </button>
                                    <button type="button"
                                            class="btn p-1 btn-primary border-0 bg-transparent text-danger shadow-none"
                                            @click="xoaCTDonNhan(row)">
                                        <i class="fas fa-trash-alt"></i>&nbsp;
                                    </button>
                                    <button type="button"
                                            class="btn p-1 btn-primary border-0 bg-transparent text-secondary shadow-none"
                                            @click="xemBienMucBieuGhiSach(row)">
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
                <Input v-model="Sach.so_luong" label="Số lượng" placeholder="Số lượng..." type="number"/>
            </div>
        </div>
    </Modal>

</template>

<script>

export default {
    name: "pageCTDonNhan",
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
                {key: 'gia', label: 'Giá', format: "VND"},
                {key: 'so_luong', label: 'SL'},
                {key: 'thanh_tien', label: 'Thành tiền', format: "VND" },
                {key: 'actions', label: 'Hành động', sortable: false},
            ],
            maDonNhan: this.$route.query.ma_don_nhan || '',
            tenDonNhan: this.$route.query.ten_don_nhan || '',
        };
    },
    mounted() {
        this.fetchData(1);
    },
    methods: {
        async fetchData(page = 1) {
            const id = this.$route.params.id_don_nhan;
            try {
                const res = await axios.get(
                    `/api/quan-ly-an-pham/nhan-sach/don-nhan/chi-tiet-don-nhan/${id}?page=${page}`
                );

                if (res.data.status === 200) {
                    this.ds = res.data.data;
                    this.currentPage = this.ds.current_page;
                } else {
                    toastr.error('Không tải được dữ liệu');
                }
            } catch (err) {
                console.error('Lỗi khi fetch dữ liệu:', err);
                toastr.error('Đã xảy ra lỗi khi tải dữ liệu');
            }
        },

        async themSach () {
            // 1. Chuẩn bị dữ liệu & tiêu đề modal
            this.$refs.modal.$data.title = 'Thêm mới sách';
            this.$refs.modal.$data.save  = 'Thêm';

            this.Sach = {
                id_don_nhan : this.$route.params.id_don_nhan,
                nhan_de     : '',
                tac_gia     : '',
                nam_xuat_ban: '',
                nha_xuat_ban: '',
                noi_xuat_ban: '',
                gia         : 0,
                so_luong    : 0,
                thanh_tien  : 0
            };

            /* ---------- LOOP cho phép “Lưu” nhiều lần nếu server trả lỗi ---------- */
            while (true) {
                const confirmed = await this.$refs.modal.openModal();
                if (!confirmed) break;

                try {
                    const res = await axios.post(
                        '/api/quan-ly-an-pham/nhan-sach/don-nhan/chi-tiet-don-nhan',
                        this.Sach
                    );
                    if (res.data.status === 200) {
                        toastr.success(res.data.message);
                        this.fetchData(this.currentPage);
                        this.$refs.modal.closeModal();     // đóng modal
                        break;                             // kết thúc while
                    }
                } catch (err) {
                    if (err.response?.status === 422) {
                        Object.values(err.response.data.errors)
                            .forEach(msg => toastr.error(msg[0]));
                    } else {
                        toastr.error('Thêm thất bại');
                        console.error(err);
                        break; // lỗi khác → thoát
                    }
                }
            }
        },

        async suaCTDonNhan (row) {
            this.$refs.modal.$data.title = 'Cập nhật sách';
            this.$refs.modal.$data.save  = 'Cập nhật';
            this.Sach = { ...row };

            while (true) {
                const confirmed = await this.$refs.modal.openModal();
                if (!confirmed) break;

                try {
                    const res = await axios.put(
                        `/api/quan-ly-an-pham/nhan-sach/don-nhan/chi-tiet-don-nhan/${row.id_sach}`,
                        this.Sach
                    );
                    if (res.data.status === 200) {
                        toastr.success(res.data.message);
                        this.fetchData(this.currentPage);
                        this.$refs.modal.closeModal();
                        break;
                    }
                } catch (err) {
                    if (err.response?.status === 422) {
                        Object.values(err.response.data.errors)
                            .forEach(msg => toastr.error(msg[0]));
                    } else {
                        toastr.error('Cập nhật thất bại');
                        console.error(err);
                        break;
                    }
                }
            }
        },

        async xoaCTDonNhan(row) {
            Swal.fire({
                title: 'Bạn có chắc chắn?',
                text: "Dữ liệu sau khi bị xoá không thể khôi phục lại !!!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Có, xoá!',
                cancelButtonText: 'Hủy',
            }).then(async (result) => {
                if (result.isConfirmed) {
                    try {
                        const res = await axios.delete(
                            `/api/quan-ly-an-pham/nhan-sach/don-nhan/chi-tiet-don-nhan/${row.id_sach}`
                        );
                        if (res.data.status === 200) {
                            toastr.success(res.data.message);
                            this.fetchData(this.currentPage);
                        }
                    } catch (err) {
                        toastr.error("Xoá thất bại");
                        console.error(err);
                    }
                }
            });
        },

        async xemBienMucBieuGhiSach(row) {
            this.$router.push({
                name: "pageBienMucBieuGhiSach",
                params: {
                    id_don_nhan: this.$route.params.id_don_nhan,
                    id_sach:     row.id_sach
                },
                query: {
                    ten_sach: row.nhan_de
                }
            });
        }
    },
};
</script>

