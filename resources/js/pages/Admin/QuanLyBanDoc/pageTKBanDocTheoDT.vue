<template>
    <ContentWrapper title="THỐNG KÊ BẠN ĐỌC THEO ĐỐI TƯỢNG">
        <template #ContentPage>
            <div class="row">
                <div class="col-12">
                    <Card>
                        <template #ContentCardBody>
                            <div>
                                <div v-if="isLoading" class="text-center my-4">
                                    <div class="spinner-border text-primary" role="status">
                                    </div>
                                </div>
                                <div v-else>
                                    <Table :columns="headers" :data="dsThongKe" :hideSearch="false">
                                        <!-- Slot cho cột hành động -->
                                        <template v-slot:column-actions="{ row }">
                                            <button type="button"
                                                class="btn p-1 btn-primary border-0 bg-transparent text-success shadow-none"
                                                @click="xemChiTietDoiTuongBanDoc(row)">
                                                <i class="fas fa-eye"></i>&nbsp;
                                            </button>
                                        </template>
                                    </Table>
                                </div>
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
    name: "pageTKBanDocTheoDT",

    data() {
        return {
            isLoading: false,
            dsThongKe: [],
            headers: [
                { key: "index", label: "STT", sortable: true },
                { key: "ten_doi_tuong_ban_doc", label: "Tên đối tượng bạn đọc", sortable: true },
                { key: "so_luong_ban_doc", label: "Số lượng", sortable: true },
                { key: "actions", label: "Hành động", sortable: false }
            ]
        };
    },

    mounted() {
        this.fetchData();
    },

    methods: {
        async fetchData() {
            try {
                this.isLoading = true;
                const response = await axios.get('/api/danh-muc/nghiep-vu-luu-thong/doi-tuong-ban-doc/thong-ke');
                
                if (response.data.status === 200) {
                    this.dsThongKe = response.data.data;
                } else {
                    console.error('Lỗi khi lấy dữ liệu thống kê');
                    // Kiểm tra xem $toast có tồn tại không
                    if (this.$toast) {
                        this.$toast.error('Có lỗi khi tải dữ liệu!');
                    }
                }
            } catch (error) {
                console.error('Lỗi khi lấy dữ liệu thống kê:', error);
                // Kiểm tra xem $toast có tồn tại không
                if (this.$toast) {
                    this.$toast.error('Có lỗi khi tải dữ liệu!');
                }
            } finally {
                this.isLoading = false;
            }
        },

        xemChiTietDoiTuongBanDoc(row) {
            this.$router.push({
                name: 'pageChiTietBanDocTheoDT',
                params: { 
                    ma_so_quy_uoc: row.ma_so_quy_uoc,
                    ten_doi_tuong: row.ten_doi_tuong_ban_doc 
                }
            });
        }
    }
};
</script>

<style scoped>
</style>
