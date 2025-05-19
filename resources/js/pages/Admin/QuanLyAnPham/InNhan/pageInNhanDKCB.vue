<template>
    <ContentWrapper title="IN NHÃN ĐĂNG KÝ CÁ BIỆT">
        <template #ContentPage>
            <div class="row">
                <div class="col-12">
                    <Card>
                        <template #ContentCardBody>
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <Card title="Tạo Nhãn DKCB Mới">
                                        <template #ContentCardBody>
                                            <div class="form-group row">
                                                <label class="col-sm-4 col-form-label">Kho ấn phẩm:</label>
                                                <div class="col-sm-8">
                                                    <select class="form-control" v-model="formTaoNhan.id_kho_an_pham">
                                                        <option value="">-- Chọn kho ấn phẩm --</option>
                                                        <option v-for="kho in ds" :key="kho.id" :value="kho.id">{{
                                                            kho.ma_kho }} - {{ kho.ten_kho }}</option>
                                                    </select>
                                                    <small v-if="errors.id_kho_tao" class="text-danger">{{
                                                        errors.id_kho_tao }}</small>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-sm-4 col-form-label">Số bắt đầu:</label>
                                                <div class="col-sm-8">
                                                    <input type="number" class="form-control"
                                                        v-model.number="formTaoNhan.so_bat_dau" min="1" />
                                                    <small v-if="errors.so_bat_dau_tao" class="text-danger">{{
                                                        errors.so_bat_dau_tao }}</small>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-sm-4 col-form-label">Số lượng:</label>
                                                <div class="col-sm-8">
                                                    <input type="number" class="form-control"
                                                        v-model.number="formTaoNhan.so_luong" min="1" max="100" />
                                                    <small v-if="errors.so_luong_tao" class="text-danger">{{
                                                        errors.so_luong_tao }}</small>
                                                    <small class="text-muted">Tối đa 100 nhãn mỗi lần</small>
                                                </div>
                                            </div>
                                            <div class="text-right">
                                                <button type="button" class="btn btn-success" @click="taoNhanDKCB"
                                                    :disabled="isLoading">
                                                    <i class="fas fa-plus" v-if="!isLoadingTao"></i>
                                                    <i class="fas fa-spinner fa-spin" v-else></i>&nbsp;
                                                    Tạo nhãn
                                                </button>
                                            </div>
                                        </template>
                                    </Card>
                                </div>
                                <div class="col-md-6">
                                    <Card title="In nhãn DKCB">
                                        <template #ContentCardBody>
                                            <div class="form-group row">
                                                <label class="col-sm-4 col-form-label">Kho ấn phẩm:</label>
                                                <div class="col-sm-8">
                                                    <select class="form-control" v-model="formInNhan.id_kho_an_pham">
                                                        <option value="">-- Chọn kho ấn phẩm --</option>
                                                        <option v-for="kho in ds" :key="kho.id" :value="kho.id">
                                                            {{kho.ma_kho }} - {{ kho.ten_kho }}</option>
                                                    </select>
                                                    <small v-if="errors.id_kho_in" class="text-danger">{{
                                                        errors.id_kho_in }}</small>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-sm-4 col-form-label">Số bắt đầu:</label>
                                                <div class="col-sm-8">
                                                    <input type="number" class="form-control"
                                                        v-model.number="formInNhan.so_bat_dau" min="1" />
                                                    <small v-if="errors.so_bat_dau_in" class="text-danger">{{
                                                        errors.so_bat_dau_in }}</small>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-sm-4 col-form-label">Số lượng:</label>
                                                <div class="col-sm-8">
                                                    <input type="number" class="form-control"
                                                        v-model.number="formInNhan.so_luong" min="1" max="100" />
                                                    <small v-if="errors.so_luong_in" class="text-danger">{{
                                                        errors.so_luong_in }}</small>
                                                    <small class="text-muted">Tối đa 100 nhãn mỗi lần</small>
                                                </div>
                                            </div>
                                            <div class="text-right">
                                                <button type="button" class="btn btn-primary" @click="inNhanDKCB"
                                                    :disabled="isLoading">
                                                    <i class="fas fa-print" v-if="!isLoadingIn"></i>
                                                    <i class="fas fa-spinner fa-spin" v-else></i>&nbsp;
                                                    In nhãn
                                                </button>
                                            </div>
                                        </template>
                                    </Card>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <Card title="Danh sách kho">
                                        <template #ContentCardBody>
                                            <!-- Bảng hiển thị dữ liệu -->
                                            <Table :columns="headers" :data="ds">
                                                <!-- Slot cho cột hành động -->
                                                <template v-slot:column-actions="{ row }">
                                                    <button type="button"
                                                            class="btn p-1 btn-primary border-0 bg-transparent text-primary shadow-none"
                                                            @click="xemChiTietNhan(row)" :disabled="row.so_hien_tai === 0">
                                                        <i class="fas fa-eye"></i>&nbsp;
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

            <!-- Modal xem chi tiết nhãn -->
            <Modal ref="modalChiTietNhan" size="xl">
                <template v-slot>
                    <div v-if="isLoadingChiTiet" class="text-center py-4">
                        <i class="fas fa-spinner fa-spin fa-2x"></i>
                        <p class="mt-2">Đang tải chi tiết nhãn...</p>
                    </div>
                    <div v-else-if="dsChiTietNhan.length === 0" class="text-center py-4 text-muted">
                        <i class="fas fa-exclamation-circle fa-2x mb-2"></i>
                        <p>Không có nhãn DKCB nào</p>
                    </div>
                    <div v-else>
                        
                        <Table :columns="headersChiTietNhan" :data="dsChiTietNhanLoc">
                            <!-- Slot cho cột tên sách -->
                            <template v-slot:column-ten_sach="{ row }">
                                <span v-if="row.ten_sach">{{ row.ten_sach }}</span>
                                <span v-else class="text-muted font-italic">Chưa gán</span>
                            </template>
                            <!-- Slot cho cột hành động -->
                            <template v-slot:column-actions="{ row }">
                                <button type="button"
                                        class="btn p-1 btn-primary border-0 bg-transparent text-primary shadow-none"
                                        @click="inNhanDon(row)" title="In nhãn">
                                    <i class="fas fa-print"></i>&nbsp;
                                </button>
                            </template>
                        </Table>
                    </div>
                </template>
            </Modal>
        </template>
    </ContentWrapper>
</template>

<script>
import { createBarcodeWindow } from './printUtils';

export default {
    name: "pageInNhanDKCB",
    data() {
        return {
            ds: [],
            headers: [
                { key: 'index', label: 'STT', sortable: false },
                { key: 'ma_kho', label: 'Mã kho' },
                { key: 'ten_kho', label: 'Tên kho' },
                { key: 'so_hien_tai', label: 'Số nhãn hiện tại' },
                { key: 'actions', label: 'Chi tiết nhãn', sortable: false },
            ],
            formTaoNhan: {
                id_kho_an_pham: '',
                so_bat_dau: 1,
                so_luong: 1
            },
            formInNhan: {
                id_kho_an_pham: '',
                so_bat_dau: 1,
                so_luong: 1
            },
            isLoading: false,
            isLoadingTao: false,
            isLoadingIn: false,
            isLoadingChiTiet: false,
            errors: {
                id_kho_tao: '',
                so_bat_dau_tao: '',
                so_luong_tao: '',
                id_kho_in: '',
                so_bat_dau_in: '',
                so_luong_in: ''
            },
            // Dữ liệu cho chi tiết nhãn
            chiTietNhan: {
                id: null,
                ma_kho: '',
                ten_kho: ''
            },
            dsChiTietNhan: [],
            timKiemNhan: '',
            headersChiTietNhan: [
                { key: 'ma_dkcb', label: 'Mã DKCB' },
                { key: 'so_thu_tu', label: 'Số thứ tự' },
                { key: 'ten_sach', label: 'Tên sách' },
                { key: 'actions', label: 'Thao tác', sortable: false },
            ]
        };
    },
    computed: {
        dsChiTietNhanLoc() {
            if (!this.timKiemNhan) {
                return this.dsChiTietNhan;
            }
            const search = this.timKiemNhan.toLowerCase();
            return this.dsChiTietNhan.filter(nhan => {
                return nhan.ma_dkcb.toLowerCase().includes(search) || 
                       nhan.so_thu_tu.toString().includes(search) ||
                       (nhan.ten_sach && nhan.ten_sach.toLowerCase().includes(search));
            });
        }
    },
    methods: {
        resetErrors() {
            this.errors = {
                id_kho_tao: '',
                so_bat_dau_tao: '',
                so_luong_tao: '',
                id_kho_in: '',
                so_bat_dau_in: '',
                so_luong_in: ''
            };
        },

        validateFormTaoNhan() {
            this.resetErrors();
            let isValid = true;

            if (!this.formTaoNhan.id_kho_an_pham) {
                this.errors.id_kho_tao = 'Vui lòng chọn kho ấn phẩm';
                isValid = false;
            }

            if (!this.formTaoNhan.so_bat_dau || this.formTaoNhan.so_bat_dau < 1) {
                this.errors.so_bat_dau_tao = 'Số bắt đầu phải lớn hơn 0';
                isValid = false;
            }

            if (!this.formTaoNhan.so_luong || this.formTaoNhan.so_luong < 1 || this.formTaoNhan.so_luong > 100) {
                this.errors.so_luong_tao = 'Số lượng phải từ 1 đến 100';
                isValid = false;
            }

            return isValid;
        },

        validateFormInNhan() {
            this.resetErrors();
            let isValid = true;

            if (!this.formInNhan.id_kho_an_pham) {
                this.errors.id_kho_in = 'Vui lòng chọn kho ấn phẩm';
                isValid = false;
            }

            if (!this.formInNhan.so_bat_dau || this.formInNhan.so_bat_dau < 1) {
                this.errors.so_bat_dau_in = 'Số bắt đầu phải lớn hơn 0';
                isValid = false;
            }

            if (!this.formInNhan.so_luong || this.formInNhan.so_luong < 1 || this.formInNhan.so_luong > 100) {
                this.errors.so_luong_in = 'Số lượng phải từ 1 đến 100';
                isValid = false;
            }

            return isValid;
        },

        async fetchData() {
            this.isLoading = true;
            try {
                const res = await axios.get('/api/quan-ly-an-pham/in-nhan/dkcb');
                if (res.data.status === 200) {
                    this.ds = res.data.data;

                    // Tự động chọn kho đầu tiên nếu có và chưa có kho nào được chọn
                    if (this.ds.length > 0) {
                        if (!this.formTaoNhan.id_kho_an_pham) {
                            this.formTaoNhan.id_kho_an_pham = this.ds[0].id;
                        }
                        if (!this.formInNhan.id_kho_an_pham) {
                            this.formInNhan.id_kho_an_pham = this.ds[0].id;
                        }
                    }
                }
            } catch (e) {
                toastr.error('Lỗi khi tải dữ liệu kho!');
                console.error('Lỗi:', e);
            } finally {
                this.isLoading = false;
            }
        },

        async taoNhanDKCB() {
            if (!this.validateFormTaoNhan()) {
                return;
            }

            this.isLoadingTao = true;
            this.isLoading = true;

            try {
                const res = await axios.post('/api/quan-ly-an-pham/in-nhan/dkcb/tao-nhan', this.formTaoNhan);

                if (res.data.status === 200) {
                    toastr.success(res.data.message);
                    await this.fetchData();
                } else {
                    toastr.error(res.data.message || 'Lỗi khi tạo nhãn DKCB!');
                }
            } catch (e) {
                if (e.response && e.response.data) {
                    if (e.response.data.message) {
                        toastr.error(e.response.data.message);
                    }

                    // Xử lý lỗi validation từ Laravel
                    if (e.response.data.errors) {
                        const errors = e.response.data.errors;
                        if (errors.id_kho_an_pham) {
                            this.errors.id_kho_tao = errors.id_kho_an_pham[0];
                        }
                        if (errors.so_bat_dau) {
                            this.errors.so_bat_dau_tao = errors.so_bat_dau[0];
                        }
                        if (errors.so_luong) {
                            this.errors.so_luong_tao = errors.so_luong[0];
                        }
                    }
                } else {
                    toastr.error('Lỗi khi tạo nhãn DKCB!');
                }
                console.error('Lỗi:', e);
            } finally {
                this.isLoadingTao = false;
                this.isLoading = false;
            }
        },

        async inNhanDKCB() {
            if (!this.validateFormInNhan()) {
                return;
            }

            this.isLoadingIn = true;
            this.isLoading = true;

            try {
                const res = await axios.post('/api/quan-ly-an-pham/in-nhan/dkcb/in-nhan', this.formInNhan);

                if (res.data.status === 200) {
                    const danhSachNhan = res.data.data;

                    if (danhSachNhan && danhSachNhan.length > 0) {
                        // Sử dụng utility function để tạo cửa sổ in
                        const result = createBarcodeWindow(danhSachNhan);

                        if (!result) {
                            toastr.error('Vui lòng cho phép popup để in nhãn!');
                        } else {
                            toastr.success(`Đã mở cửa sổ in với ${danhSachNhan.length} nhãn`);
                        }
                    } else {
                        toastr.error('Không có nhãn nào để in!');
                    }
                } else {
                    toastr.error(res.data.message || 'Lỗi khi in nhãn DKCB!');
                }
            } catch (e) {
                if (e.response && e.response.data) {
                    if (e.response.data.message) {
                        toastr.error(e.response.data.message);
                    }

                    // Xử lý lỗi validation từ Laravel
                    if (e.response.data.errors) {
                        const errors = e.response.data.errors;
                        if (errors.id_kho_an_pham) {
                            this.errors.id_kho_in = errors.id_kho_an_pham[0];
                        }
                        if (errors.so_bat_dau) {
                            this.errors.so_bat_dau_in = errors.so_bat_dau[0];
                        }
                        if (errors.so_luong) {
                            this.errors.so_luong_in = errors.so_luong[0];
                        }
                    }
                } else {
                    toastr.error('Lỗi khi in nhãn DKCB!');
                }
                console.error('Lỗi:', e);
            } finally {
                this.isLoadingIn = false;
                this.isLoading = false;
            }
        },

        async xemChiTietNhan(kho) {
            this.chiTietNhan = {
                id: kho.id,
                ma_kho: kho.ma_kho,
                ten_kho: kho.ten_kho
            };
            
            this.dsChiTietNhan = [];
            this.timKiemNhan = '';
            this.isLoadingChiTiet = true;
            
            // Cập nhật tiêu đề modal
            this.$refs.modalChiTietNhan.title = `Chi tiết nhãn DKCB - ${kho.ma_kho} (${kho.ten_kho})`;
            
            try {
                const res = await axios.get(`/api/quan-ly-an-pham/in-nhan/dkcb/danh-sach-nhan/${kho.id}`);
                
                if (res.data.status === 200) {
                    this.dsChiTietNhan = res.data.data;
                }
            } catch (e) {
                toastr.error('Lỗi khi tải danh sách nhãn!');
                console.error('Lỗi:', e);
            } finally {
                this.isLoadingChiTiet = false;
                // Mở modal (thử cả hai cách)
                try {
                    this.$refs.modalChiTietNhan.openModal();
                } catch (e) {
                    // Nếu openModal không tồn tại, thử phương thức show
                    this.$refs.modalChiTietNhan.show();
                }
            }
        },
        
        inNhanDon(nhan) {
            // In một nhãn đơn lẻ
            const result = createBarcodeWindow([nhan]);
            
            if (!result) {
                toastr.error('Vui lòng cho phép popup để in nhãn!');
            } else {
                toastr.success('Đã mở cửa sổ in nhãn');
            }
        }
    },
    created() {
        // Tự động tải dữ liệu khi component được tạo
        this.fetchData();

        // Cập nhật dữ liệu mỗi 30 giây
        this.autoRefreshInterval = setInterval(() => {
            this.fetchData();
        }, 30000);
    },
    mounted() {
        // Cập nhật title của modal
        if (this.$refs.modalChiTietNhan) {
            this.$refs.modalChiTietNhan.title = 'Chi tiết nhãn DKCB';
        }
    },
    beforeDestroy() {
        // Xóa interval khi component bị hủy
        clearInterval(this.autoRefreshInterval);
    }
};
</script>

<style scoped>
/* Style cho component */
.form-control.is-invalid {
    border-color: #dc3545;
}
</style>
