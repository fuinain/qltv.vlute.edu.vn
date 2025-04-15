<template>
    <ContentWrapper title="QUẢN LÝ NĂM HỌC">
        <template #ContentPage>
            <div class="row">
                <div class="col-12">
                    <Card>
                        <template #ContentCardHeader>
                            <button type="button" class="btn btn-primary" @click="dongBoHocKy">
                                <i class="fa fa-cloud-download-alt"></i>&nbsp;
                                Đồng bộ dữ liệu từ ems
                            </button>
                        </template>
                        <template #ContentCardBody>
                            <Table :columns="headers" :data="ds.data ?? []" :pagination="ds" :fetchData="fetchData">
                            </Table>
                        </template>
                    </Card>
                </div>
            </div>
        </template>
    </ContentWrapper>
</template>
<script>
export default {
    name: "PageChucVu",

    data() {
        return {
            ds: [],
            currentPage: 1,
            ChucVu: {
                ma_chuc_vu: "",
                ten_chuc_vu: ""
            },
            headers: [
                {key: 'index', label: 'STT', sortable: false},
                {key: 'ma_hoc_ky', label: 'Mã học kỳ'},
                {key: 'ten_hoc_ky', label: 'Tên học kỳ'},
                {key: 'nam_hoc', label: 'Năm học'},
                {key: 'ngay_cap_nhat', label: 'Ngày cập nhật', format: 'datetime'},
                {key: 'ngay_tao', label: 'Ngày tạo', format: 'datetime'},
            ],
        };
    },
    mounted() {
        this.fetchData(1);
    },
    methods: {
        async fetchData(page = 1) {
            try {
                const response = await axios.get(`/api/danh-muc/thong-tin-chung/hoc-ky?page=${page}`);
                if (response.data.status === 200) {
                    this.ds = response.data.data;
                    this.currentPage = this.ds.current_page;
                }
            } catch (error) {
                console.error("Lỗi khi load dữ liệu:", error);
            }
        },

        async dongBoHocKy() {
            const result = await Swal.fire({
                title: 'Xác nhận đồng bộ?',
                html: 'Nếu đồng bộ thì các dữ liệu hiện tại sẽ thay đổi. Bạn có chắc chắn muốn đồng bộ',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Đồng bộ ngay',
                cancelButtonText: 'Hủy'
            });

            if (!result.isConfirmed) return;

            try {
                const response = await axios.post('/api/danh-muc/thong-tin-chung/hoc-ky/sync');
                if (response.data.status === 200) {
                    toastr.success(response.data.message);
                    this.fetchData(this.currentPage);
                } else {
                    toastr.error(response.data.message || 'Đồng bộ thất bại');
                }
            } catch (error) {
                console.error("Lỗi khi đồng bộ:", error);
                toastr.error("Xảy ra lỗi khi đồng bộ học kỳ!");
            }
        }

    },
};
</script>
