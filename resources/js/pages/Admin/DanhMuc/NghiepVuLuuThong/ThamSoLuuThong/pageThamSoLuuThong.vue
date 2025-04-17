<template>
    <ContentWrapper title="THIẾT LẬP THAM SỐ LƯU THÔNG">
        <template #ContentPage>
            <div class="row">
                <div class="col-12">
                    <Card>
                        <template #ContentCardBody>
                            <!-- Bảng hiển thị dữ liệu -->
                            <Table :columns="headers" :data="ds.data ?? []" :pagination="ds" :fetchData="fetchData">
                                <!-- Slot cho cột hành động -->
                                <template v-slot:column-actions="{ row }">
                                    <button type="button"
                                            class="btn p-1 btn-primary border-0 bg-transparent text-success shadow-none"
                                            @click="xemChiTietThamSoLuuThong(row)">
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
</template>

<script>
export default {
    name: "pageThamSoLuuThong",

    data() {
        return {
            ds: [],
            currentPage: 1,
            headers: [
                {key: 'index', label: 'STT', sortable: false},
                {key: 'ma_doi_tuong_ban_doc', label: 'Mã đối tượng bạn đọc'},
                {key: 'ten_doi_tuong_ban_doc', label: 'Tên đối tượng bạn đọc'},
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
                const response = await axios.get(`/api/danh-muc/nghiep-vu-luu-thong/doi-tuong-ban-doc?page=${page}`);
                if (response.data.status === 200) {
                    this.ds = response.data.data;
                    this.currentPage = this.ds.current_page;
                }
            } catch (error) {
                console.error("Lỗi khi load dữ liệu:", error);
            }
        },

        xemChiTietThamSoLuuThong(row) {
            this.$router.push({
                name: "pageCTThamSoLuuThong",
                params: { id_doi_tuong_ban_doc: row.id_doi_tuong_ban_doc },
                query: { ten: row.ten_doi_tuong_ban_doc },
            });
        }

    },
};
</script>

