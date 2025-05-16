<template>
    <ContentWrapper title="IN NHÃN PHÂN LOẠI">
        <template #ContentPage>
            <div class="row">
                <div class="col-12">
                    <Card>
                        <template #ContentCardBody>
                            <div class="row mb-4">
                                <div class="col-md-12">
                                    <Card title="Tra cứu sách từ đơn nhận">
                                        <template #ContentCardBody>
                                            <div class="form-group row">
                                                <label class="col-sm-2 col-form-label">Từ đơn nhận: <span
                                                        class="text-danger">(*)</span></label>
                                                <div class="col-sm-4">
                                                    <input type="number" class="form-control"
                                                        v-model.number="formTraCuu.don_nhan_bat_dau" min="1" />
                                                    <small v-if="errors.don_nhan_bat_dau" class="text-danger">{{
                                                        errors.don_nhan_bat_dau }}</small>
                                                </div>
                                                <label class="col-sm-2 col-form-label">Đến đơn nhận: <span
                                                        class="text-danger">(*)</span></label>
                                                <div class="col-sm-4">
                                                    <input type="number" class="form-control"
                                                        v-model.number="formTraCuu.don_nhan_ket_thuc" min="1" />
                                                    <small v-if="errors.don_nhan_ket_thuc" class="text-danger">{{
                                                        errors.don_nhan_ket_thuc }}</small>
                                                </div>
                                            </div>
                                            <div class="text-right">
                                                <button type="button" class="btn btn-primary" @click="traCuuSach"
                                                    :disabled="isLoading">
                                                    <i class="fas fa-search" v-if="!isLoadingTraCuu"></i>
                                                    <i class="fas fa-spinner fa-spin" v-else></i>&nbsp;
                                                    Tra cứu
                                                </button>
                                            </div>
                                        </template>
                                    </Card>
                                </div>
                            </div>

                            <div class="row" v-if="danhSachSach.length > 0">
                                <div class="col-md-12">
                                    <Card title="Danh sách sách">
                                        <template #ContentCardBody>
                                            <div class="mb-3 text-right">
                                                <button type="button" class="btn btn-success" @click="inTatCaNhan"
                                                    :disabled="isLoading || danhSachSach.length === 0">
                                                    <i class="fas fa-print" v-if="!isLoadingIn"></i>
                                                    <i class="fas fa-spinner fa-spin" v-else></i>&nbsp;
                                                    In tất cả nhãn
                                                </button>
                                            </div>

                                            <Table :columns="headersSach" :data="danhSachSach">
                                                <!-- Slot cho cột mã đơn -->
                                                <template v-slot:column-id_don_nhan="{ row }">
                                                    {{ row.id_don_nhan }}
                                                </template>

                                                <!-- Slot cho cột thông tin sách -->
                                                <template v-slot:column-nhan_de="{ row }">
                                                    <strong>{{ row.nhan_de }}</strong>
                                                </template>

                                                <!-- Slot cho cột tác giả -->
                                                <template v-slot:column-tac_gia="{ row }">
                                                    {{ row.tac_gia }}
                                                </template>

                                                <!-- Slot cho cột năm xuất bản -->
                                                <template v-slot:column-nam_xuat_ban="{ row }">
                                                    {{ row.nam_xuat_ban }}
                                                </template>

                                                <!-- Slot cho cột phân loại -->
                                                <template v-slot:column-phan_loai="{ row }">
                                                    <span v-if="row.phan_loai_1 || row.phan_loai_2">
                                                        {{ row.phan_loai_1 }} <span
                                                            v-if="row.phan_loai_1 && row.phan_loai_2">/</span> {{
                                                        row.phan_loai_2 }}
                                                    </span>
                                                    <span v-else class="text-muted font-italic">Chưa có</span>
                                                </template>

                                                <!-- Slot cho cột số lượng -->
                                                <template v-slot:column-so_luong="{ row }">
                                                    {{ row.so_luong }}
                                                </template>

                                                <!-- Slot cho cột số DKCB -->
                                                <template v-slot:column-dkcb_list="{ row }">
                                                    <div v-if="row.dkcb_list && row.dkcb_list.length > 0">
                                                        <div v-for="(dkcb, index) in row.dkcb_list" :key="index">
                                                            {{ dkcb }}
                                                        </div>
                                                    </div>
                                                    <span v-else class="text-muted font-italic">Chưa gán DKCB</span>
                                                </template>

                                                <!-- Slot cho cột hành động -->
                                                <template v-slot:column-actions="{ row }">
                                                    <button type="button"
                                                        class="btn p-1 btn-primary border-0 bg-transparent text-primary shadow-none"
                                                        @click="inNhanPhanLoai(row)"
                                                        :disabled="!row.phan_loai_1 && !row.phan_loai_2"
                                                        title="In nhãn phân loại">
                                                        <i class="fas fa-print"></i>&nbsp;
                                                    </button>
                                                </template>
                                            </Table>
                                        </template>
                                    </Card>
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
import { createClassificationLabelWindow } from './printClassificationUtils';

export default {
    name: "pageInNhanPhanLoai",
    data() {
        return {
            danhSachSach: [],
            headersSach: [
                { key: 'index', label: 'STT', sortable: false },
                { key: 'id_don_nhan', label: 'Mã đơn', sortable: true },
                { key: 'nhan_de', label: 'Thông tin đơn nhận', sortable: true },
                { key: 'tac_gia', label: 'Tác giả', sortable: true },
                { key: 'nam_xuat_ban', label: 'NXB', sortable: true },
                { key: 'phan_loai', label: 'Phân loại', sortable: true },
                { key: 'so_luong', label: 'SL nhãn in', sortable: true },
                { key: 'dkcb_list', label: 'SL Số DKCB', sortable: false },
                { key: 'actions', label: 'Thao tác', sortable: false },
            ],
            formTraCuu: {
                don_nhan_bat_dau: 1,
                don_nhan_ket_thuc: 1
            },
            isLoading: false,
            isLoadingTraCuu: false,
            isLoadingIn: false,
            errors: {
                don_nhan_bat_dau: '',
                don_nhan_ket_thuc: ''
            }
        };
    },
    methods: {
        resetErrors() {
            this.errors = {
                don_nhan_bat_dau: '',
                don_nhan_ket_thuc: ''
            };
        },

        validateFormTraCuu() {
            this.resetErrors();
            let isValid = true;

            if (!this.formTraCuu.don_nhan_bat_dau || this.formTraCuu.don_nhan_bat_dau < 1) {
                this.errors.don_nhan_bat_dau = 'Vui lòng nhập số đơn nhận bắt đầu hợp lệ';
                isValid = false;
            }

            if (!this.formTraCuu.don_nhan_ket_thuc || this.formTraCuu.don_nhan_ket_thuc < 1) {
                this.errors.don_nhan_ket_thuc = 'Vui lòng nhập số đơn nhận kết thúc hợp lệ';
                isValid = false;
            }

            if (this.formTraCuu.don_nhan_bat_dau > this.formTraCuu.don_nhan_ket_thuc) {
                this.errors.don_nhan_bat_dau = 'Đơn nhận bắt đầu phải nhỏ hơn hoặc bằng đơn nhận kết thúc';
                isValid = false;
            }

            return isValid;
        },

        async traCuuSach() {
            if (!this.validateFormTraCuu()) {
                return;
            }

            this.isLoadingTraCuu = true;
            this.isLoading = true;
            this.danhSachSach = [];

            try {
                const res = await axios.post('/api/quan-ly-an-pham/in-nhan/phan-loai/danh-sach-sach', this.formTraCuu);

                if (res.data.status === 200) {
                    this.danhSachSach = res.data.data;
                    toastr.success(`Đã tìm thấy ${this.danhSachSach.length} sách từ ${res.data.don_nhan_ids.length} đơn nhận`);
                } else {
                    toastr.error(res.data.message || 'Lỗi khi tra cứu sách!');
                }
            } catch (e) {
                if (e.response && e.response.data) {
                    if (e.response.data.message) {
                        toastr.error(e.response.data.message);
                    }

                    // Xử lý lỗi validation từ Laravel
                    if (e.response.data.errors) {
                        const errors = e.response.data.errors;
                        if (errors.don_nhan_bat_dau) {
                            this.errors.don_nhan_bat_dau = errors.don_nhan_bat_dau[0];
                        }
                        if (errors.don_nhan_ket_thuc) {
                            this.errors.don_nhan_ket_thuc = errors.don_nhan_ket_thuc[0];
                        }
                    }
                } else {
                    toastr.error('Lỗi khi tra cứu sách!');
                }
                console.error('Lỗi:', e);
            } finally {
                this.isLoadingTraCuu = false;
                this.isLoading = false;
            }
        },

        async inNhanPhanLoai(sach) {
            if (!sach || (!sach.phan_loai_1 && !sach.phan_loai_2)) {
                toastr.error('Sách này chưa có thông tin phân loại!');
                return;
            }

            this.isLoading = true;

            try {
                const res = await axios.post('/api/quan-ly-an-pham/in-nhan/phan-loai/tao-nhan', {
                    id_sach: [sach.id_sach]
                });

                if (res.data.status === 200) {
                    const danhSachNhan = res.data.data;

                    if (danhSachNhan && danhSachNhan.length > 0) {
                        // Sử dụng utility function để tạo cửa sổ in
                        const result = createClassificationLabelWindow(danhSachNhan);

                        if (!result) {
                            toastr.error('Vui lòng cho phép popup để in nhãn!');
                        } else {
                            toastr.success(`Đã mở cửa sổ in với ${danhSachNhan.length} nhãn`);
                        }
                    } else {
                        toastr.error('Không có nhãn nào để in!');
                    }
                } else {
                    toastr.error(res.data.message || 'Lỗi khi in nhãn phân loại!');
                }
            } catch (e) {
                console.error('Lỗi:', e);
                toastr.error('Lỗi khi in nhãn phân loại!');
            } finally {
                this.isLoading = false;
            }
        },

        async inTatCaNhan() {
            if (this.danhSachSach.length === 0) {
                toastr.error('Không có sách nào để in nhãn!');
                return;
            }

            const sachCoNhan = this.danhSachSach.filter(sach => sach.phan_loai_1 || sach.phan_loai_2);

            if (sachCoNhan.length === 0) {
                toastr.error('Không có sách nào có thông tin phân loại!');
                return;
            }

            this.isLoadingIn = true;
            this.isLoading = true;

            try {
                // Lấy tất cả id sách có phân loại
                const idSach = sachCoNhan.map(sach => sach.id_sach);

                const res = await axios.post('/api/quan-ly-an-pham/in-nhan/phan-loai/tao-nhan', {
                    id_sach: idSach
                });

                if (res.data.status === 200) {
                    const danhSachNhan = res.data.data;

                    if (danhSachNhan && danhSachNhan.length > 0) {
                        // Sử dụng utility function để tạo cửa sổ in
                        const result = createClassificationLabelWindow(danhSachNhan);

                        if (!result) {
                            toastr.error('Vui lòng cho phép popup để in nhãn!');
                        } else {
                            toastr.success(`Đã mở cửa sổ in với ${danhSachNhan.length} nhãn`);
                        }
                    } else {
                        toastr.error('Không có nhãn nào để in!');
                    }
                } else {
                    toastr.error(res.data.message || 'Lỗi khi in nhãn phân loại!');
                }
            } catch (e) {
                console.error('Lỗi:', e);
                toastr.error('Lỗi khi in nhãn phân loại!');
            } finally {
                this.isLoadingIn = false;
                this.isLoading = false;
            }
        }
    }
};
</script>

<style scoped>
/* Style cho component */
.form-control.is-invalid {
    border-color: #dc3545;
}
</style>
