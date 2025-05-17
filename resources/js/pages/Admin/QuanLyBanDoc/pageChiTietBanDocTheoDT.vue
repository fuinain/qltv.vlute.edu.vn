<template>
    <ContentWrapper :title="'CHI TIẾT BẠN ĐỌC - ' + tenDoiTuong">
        <template #ContentPage>
            <div class="row">
                <div class="col-12">
                    <Card>
                        <template #ContentCardHeader>
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <button type="button" class="btn btn-secondary mr-2" @click="quayLai">
                                        <i class="fas fa-arrow-left"></i>&nbsp;
                                        Quay lại
                                    </button>
                                </div>
                            </div>
                        </template>
                        <template #ContentCardBody>
                            <div v-if="isLoading" class="text-center my-4">
                                <div class="spinner-border text-primary" role="status">
                                </div>
                            </div>
                            <div v-else>
                                <!-- Hiển thị bảng cho Cán bộ giảng viên -->
                                <Table v-if="maSoQuyUoc == 99" :columns="headerCanBo" :data="dsDocGia.data || []"
                                    :pagination="dsDocGia" :fetchData="fetchData" />

                                <!-- Hiển thị bảng cho Sinh viên và các đối tượng khác -->
                                <Table v-else :columns="headerSinhVien" :data="dsDocGia.data || []"
                                    :pagination="dsDocGia" :fetchData="fetchData" />
                            </div>
                        </template>
                    </Card>
                </div>
            </div>
        </template>
    </ContentWrapper>
</template>

<script>
export default {
    name: "pageChiTietBanDocTheoDT",

    data() {
        return {
            isLoading: false,
            maSoQuyUoc: this.$route.params.ma_so_quy_uoc,
            tenDoiTuong: this.$route.params.ten_doi_tuong || "Chưa xác định",
            perPage: 10,
            dsDocGia: {},
            headerCanBo: [
                { key: "index", label: "TT", sortable: true },
                { key: "so_the", label: "Số thẻ", sortable: true },
                { key: "ho_ten", label: "Tên bạn đọc", sortable: true },
                { key: "chuc_vu", label: "Chức vụ", sortable: true },
                { key: "ngay_cap_the", label: "Ngày cấp", format: "normalDateTime", sortable: true },
                { key: "don_vi", label: "Đơn vị", sortable: true }
            ],
            headerSinhVien: [
                { key: "index", label: "TT", sortable: true },
                { key: "so_the", label: "Số thẻ", sortable: true },
                { key: "ho_ten", label: "Tên bạn đọc", sortable: true },
                { key: "ten_lop", label: "Lớp", sortable: true },
                { key: "ngay_cap_the", label: "Ngày cấp", format: "normalDateTime", sortable: true },
                { key: "han_the", label: "Hạn thẻ", format: "normalDateTime", sortable: true },
                { key: "nien_khoa", label: "Niên khóa", sortable: true },
                { key: "khoa", label: "Khoa", sortable: true }
            ]
        };
    },

    mounted() {
        this.fetchData();
    },

    methods: {
        async fetchData(page = 1) {
            try {
                this.isLoading = true;
                const response = await axios.get(
                    `/api/danh-muc/nghiep-vu-luu-thong/doi-tuong-ban-doc/chi-tiet/${this.maSoQuyUoc}`,
                    { params: { perPage: this.perPage, page } }
                );

                if (response.data.status === 200) {
                    this.dsDocGia = response.data.data;
                } else {
                    if (this.$toast) {
                        this.$toast.error('Có lỗi khi tải dữ liệu!');
                    }
                }
            } catch (error) {
                console.error('Lỗi khi lấy dữ liệu chi tiết bạn đọc:', error);
                if (this.$toast) {
                    this.$toast.error('Có lỗi khi tải dữ liệu!');
                }
            } finally {
                this.isLoading = false;
            }
        },

        quayLai() {
            this.$router.push({ name: 'pageTKBanDocTheoDT' });
        }
    }
};
</script>

<style scoped>
.table-responsive {
    min-height: 300px;
}
</style>