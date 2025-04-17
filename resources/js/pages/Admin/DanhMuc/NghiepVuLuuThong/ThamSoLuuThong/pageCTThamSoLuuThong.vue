<template>
    <ContentWrapper title="CHI TIẾT THAM SỐ LƯU THÔNG">
        <template #ContentPage>
            <div class="row">
                <div class="col-12">
                    <Card>
                        <template #ContentCardHeader>
                            <h5 class="text-primary">{{ tenDoiTuong }}</h5>
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
        <div class="row font-weight-bold">
            <div class="col-md-3">Kho tài liệu</div>
            <div class="col-md-3">Số lượng mượn</div>
            <div class="col-md-3">Số ngày giữ</div>
            <div class="col-md-3">Số ngày gia hạn</div>
        </div>
        <div v-for="(item, index) in chiTietLuuThong" :key="index" class="row">
            <div class="col-md-3 d-flex align-items-center">
                {{ item.ten_kho ?? 'Không rõ tên kho' }}
            </div>
            <div class="col-md-3">
                <Input type="number" v-model="item.muon"/>
            </div>
            <div class="col-md-3">
                <Input type="number" v-model="item.giu"/>
            </div>
            <div class="col-md-3">
                <Input type="number" v-model="item.gia_han"/>
            </div>
        </div>
    </Modal>

</template>

<script>
import Input from "@/components/ui/Input.vue";

export default {
    name: "pageCTThamSoLuuThong",
    components: {Input},

    data() {
        return {
            ds: [],
            currentPage: 1,
            headers: [
                {key: 'index', label: 'STT', sortable: false},
                {key: 'ma_loai', label: 'Mã loại'},
                {key: 'ten_diem', label: 'Tên điểm lưu thông'},
                {key: 'actions', label: 'Hành động', sortable: false},
            ],
            chiTietLuuThong: [],
            tenDoiTuong: this.$route.query.ten || '',
        };
    },
    mounted() {
        this.fetchData(1);
    },
    methods: {
        async fetchData(page = 1) {
            const id = this.$route.params.id_doi_tuong_ban_doc;
            try {
                const response = await axios.get(`/api/danh-muc/nghiep-vu-luu-thong/diem-luu-thong/chi-tiet-luu-thong/${id}?page=${page}`);
                if (response.data.status === 200) {
                    this.ds = response.data.data;
                    this.currentPage = this.ds.current_page;
                }
            } catch (error) {
                console.error("Lỗi khi load dữ liệu:", error);
            }
        },

        async chiTietDiemLuuThong(row) {
            const id_doi_tuong = Number(this.$route.params.id_doi_tuong_ban_doc);
            const id_diem = Number(row.id_diem_luu_thong);

            try {
                // Bước 1: Tạo nếu chưa có
                await axios.post(`/api/danh-muc/nghiep-vu-luu-thong/diem-luu-thong/chi-tiet-luu-thong/chi-tiet-tslt`, {
                    id_doi_tuong_ban_doc: id_doi_tuong,
                    id_diem_luu_thong: id_diem,
                });

                // Bước 2: Lấy danh sách chi tiết để fill form
                const res = await axios.get(`/api/danh-muc/nghiep-vu-luu-thong/diem-luu-thong/chi-tiet-luu-thong/get-chi-tiet-tslt/${id_doi_tuong}/${id_diem}`);
                if (res.data.status === 200) {
                    this.chiTietLuuThong = res.data.data;
                    this.$refs.modal.$data.title = "Chi tiết lưu thông";
                    this.$refs.modal.$data.save = "Lưu";
                    const confirmed = await this.$refs.modal.openModal();
                    if (!confirmed) return;
                    const response = await axios.put(`/api/danh-muc/nghiep-vu-luu-thong/diem-luu-thong/chi-tiet-luu-thong/update-chi-tiet-tslt`, {
                        chi_tiet: this.chiTietLuuThong,
                    });
                    if (response.data.status === 200) {
                        toastr.success(response.data.message);
                        this.$refs.modal.closeModal();
                    }
                }
            } catch (e) {
                console.error("Lỗi khi mở modal:", e);
            }
        }

    },
};
</script>

