<template>
    <ContentWrapper>
        <!-- ---------- TITLE ---------- -->
        <template #customTitle>
            BIÊN MỤC BIỂU GHI
        </template>

        <!-- ---------- PAGE BODY ---------- -->
        <template #ContentPage>
            <div class="row">
                <div class="col-12">
                    <h5 class="text-secondary">Tên sách: {{ tenSach }}</h5>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <Card>
                        <!-- ---------- CARD HEADER ---------- -->
                        <template #ContentCardHeader>
                            <button type="button" class="btn btn-primary mr-1" @click="thongTinBienMuc">
                                <i class="fas fa-pen-alt"></i>&nbsp; Thông Tin Biên Mục
                            </button>
                            <button type="button" class="btn btn-success mr-1" @click="ganDKCB">
                                <i class="fas fa-sort-numeric-up-alt"></i>&nbsp; Gán số DKCB
                            </button>
                        </template>

                        <!-- ---------- CARD BODY ---------- -->
                        <template #ContentCardBody>
                            <!-- NÚT THÊM TRƯỜNG CHA -->
                            <button class="btn btn-success mr-1 mb-2" @click="themTruongCha">
                                <i class="fas fa-plus-circle"></i>&nbsp; Thêm trường cha
                            </button>

                            <!-- BẢNG MARC -->
                            <TableMarc :rows="marcRowsSorted()" @add-parent-after="handleAddParentAfter"
                                @remove-parent="handleRemoveParent" @add-child="handleAddChild"
                                @remove-child="handleRemoveChild" @update-parent="handleUpdateParent"
                                @update-child="handleUpdateChild" />
                        </template>
                    </Card>
                </div>
            </div>
        </template>
    </ContentWrapper>

    <!-- ---------- MODAL: THÔNG TIN BIÊN MỤC (đã có) ---------- -->
    <Modal ref="modal">
        <div class="row">
            <div class="col-4">
                <Input v-model="BienMuc.id_sach" label="Mã tài liệu" type="text" :isDisabled="true" />
            </div>
            <div class="col-4">
                <SelectOption v-model="BienMuc.id_tai_lieu" :options="dsTaiLieu" label="Loại tài liệu"
                    placeholder="Chọn loại tài liệu" />
            </div>
            <div class="col-4">
                <SelectOption v-model="BienMuc.trang_thai_bieu_ghi" :options="[
                    { value: 'dang-bien-muc', text: 'Đang biên mục' },
                    { value: 'da-duyet', text: 'Đã duyệt' }
                ]" label="Trạng thái" placeholder="Chọn TT biểu ghi" />
            </div>
            <div class="col-6">
                <SelectOption v-model="BienMuc.id_don_vi" :options="dsDonVi" label="Đơn vị" placeholder="Chọn đơn vị" />
            </div>
            <div class="col-6">
                <SelectOption v-model="BienMuc.id_chuyen_nganh" :options="dsChuyenNganh" label="Chuyên ngành"
                    :disabled="!BienMuc.id_don_vi" />
            </div>
        </div>
    </Modal>

    <!-- ---------- MODAL: GÁN SỐ DKCB ---------- -->
    <Modal ref="modalGanDKCB">
        <template #title>GÁN SỐ ĐĂNG KÝ CÁ BIỆT</template>
        <div class="row">
            <div class="col-md-12 mb-3">
                <p><strong>Tên sách:</strong> {{ tenSach }}</p>
                <p><strong>Số lượng cần gán:</strong> {{ soLuongSach }}</p>
                <p v-if="danhSachDKCBDaGan.length > 0"><strong>Số lượng đã gán:</strong> {{ danhSachDKCBDaGan.length
                    }}/{{
                    soLuongSach }}</p>
            </div>

            <!-- Hiển thị danh sách DKCB đã gán -->
            <div class="col-md-12 mb-3" v-if="danhSachDKCBDaGan.length > 0">
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <i class="fas fa-check-circle"></i> Mã DKCB đã gán ({{ danhSachDKCBDaGan.length }} mã)
                    </div>
                    <div class="card-body">
                        <div class="table-responsive" style="max-height: 300px; overflow-y: auto;">
                            <table class="table table-sm table-bordered table-hover">
                                <thead class="sticky-top bg-white">
                                    <tr class="bg-light">
                                        <th width="10%" class="text-center">STT</th>
                                        <th>Mã DKCB</th>
                                        <th width="15%" class="text-center">Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(dkcb, index) in danhSachDKCBDaGan" :key="index">
                                        <td class="text-center">{{ index + 1 }}</td>
                                        <td>{{ dkcb.ma_dkcb }}</td>
                                        <td class="text-center">
                                            <button class="btn btn-sm btn-danger" @click="xoaDKCB(dkcb)" :disabled="isLoadingDKCB">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    <tr v-if="danhSachDKCBDaGan.length === 0">
                                        <td colspan="3" class="text-center">Chưa có mã DKCB nào được gán cho sách này</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Hiển thị form nhập DKCB nếu chưa gán đủ -->
            <div class="col-md-12" v-if="danhSachDKCBDaGan.length < soLuongSach">
                <div class="form-group">
                    <div class="custom-control custom-checkbox mb-2">
                        <input type="checkbox" class="custom-control-input" id="autoAssignCheckbox"
                            v-model="formDKCB.isAutoAssign">
                        <label class="custom-control-label" for="autoAssignCheckbox">
                            Gán mã DKCB liên tiếp tự động
                        </label>
                    </div>

                    <label for="inputDKCB">Nhập mã DKCB</label>
                    <input type="text" class="form-control" id="inputDKCB" v-model="formDKCB.ma_dkcb"
                        placeholder="Ví dụ: KM.000001" />
                    <small class="form-text text-muted" v-if="formDKCB.isAutoAssign">
                        Nhập mã DKCB đầu tiên. Hệ thống sẽ tự động gán liên tiếp {{ soLuongSach -
                        danhSachDKCBDaGan.length
                        }} mã.
                    </small>
                    <small class="form-text text-muted" v-else>
                        Nhập mã DKCB để gán từng mã một. Còn lại {{ soLuongSach - danhSachDKCBDaGan.length }} mã cần
                        gán.
                    </small>
                </div>
            </div>

            <!-- Hiển thị thông báo đã gán đủ -->
            <div class="col-md-12" v-if="danhSachDKCBDaGan.length >= soLuongSach">
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> Đầu sách này đã được gán đủ {{ soLuongSach }} mã DKCB.
                </div>
            </div>

            <div class="col-md-12 mt-3" v-if="isLoadingDKCB">
                <div class="text-center">
                    <i class="fas fa-spinner fa-spin"></i> Đang xử lý...
                </div>
            </div>
            <div class="col-md-12 mt-3" v-if="thongBaoLoi">
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle"></i> {{ thongBaoLoi }}
                </div>
            </div>
        </div>
        <template #buttons>
            <!-- Chỉ hiển thị nút Lưu thông tin khi chưa gán đủ số DKCB -->
            <button v-if="danhSachDKCBDaGan.length < soLuongSach" type="button" class="btn btn-primary"
                @click="$refs.modalGanDKCB.submit()" :disabled="isLoadingDKCB || !formDKCB.ma_dkcb">
                <i class="fas fa-save"></i> Lưu thông tin
            </button>
            <button type="button" class="btn btn-secondary" @click="$refs.modalGanDKCB.closeModal()">
                <i class="fas fa-times"></i> Đóng
            </button>
        </template>
    </Modal>

    <!-- ---------- MODAL: THÊM TRƯỜNG CHA ---------- -->
    <Modal ref="modalAddParent">
        <template #title>THÊM TRƯỜNG CHA</template>
        <div class="row">
            <div class="col-4">
                <label class="small mb-1">Nhãn (3 chữ số)</label>
                <input v-model="newParent.ma_truong" class="form-control form-control-sm" maxlength="3"
                    placeholder="vd: 050" />
            </div>
            <div class="col-4">
                <label class="small mb-1">CT1 (1 ký tự)</label>
                <input v-model="newParent.ct1" class="form-control form-control-sm" maxlength="1"
                    placeholder="a / # / …" />
            </div>
            <div class="col-4">
                <label class="small mb-1">CT2 (1 ký tự)</label>
                <input v-model="newParent.ct2" class="form-control form-control-sm" maxlength="1"
                    placeholder="# / 0 / …" />
            </div>
        </div>
    </Modal>
</template>

<script>
import Swal from 'sweetalert2';

export default {
    name: "pageBienMucBieuGhiBienMuc",

    data() {
        return {
            /* ---------- danh sách chọn ---------- */
            dsTaiLieu: [],
            dsDonVi: [],
            dsChuyenNganh: [],
            dsTrangThai: [
                { value: "dang-bien-muc", text: "Đang biên mục" },
                { value: "da-duyet", text: "Đã duyệt" },
            ],

            /* ---------- form Biên Mục ---------- */
            BienMuc: {
                id_sach: this.$route.params.id_sach,
                id_tai_lieu: null,
                trang_thai_bieu_ghi: "dang-bien-muc",
                id_don_vi: null,
                id_chuyen_nganh: null,
            },

            /* ---------- form DKCB ---------- */
            formDKCB: {
                ma_dkcb: "",
                isAutoAssign: true
            },
            isLoadingDKCB: false,
            thongBaoLoi: "",
            soLuongSach: 0,

            /* ---------- danh sách MARC ---------- */
            marcRows: [],
            tenSach: this.$route.query.ten_sach || "",
            isLoadingMarc: false,
            isLoadingBienMuc: false,

            /* ---------- form THÊM TRƯỜNG CHA ---------- */
            newParent: { ma_truong: "", nhan: "", ct1: "", ct2: "" },

            /* ---------- danh sách DKCB đã gán ---------- */
            danhSachDKCBDaGan: [],
        };
    },

    mounted() {
        this.getListTaiLieu();
        this.getListDonVi();
        this.fetchMarc();
        this.getSoLuongSach();
        this.getDanhSachDKCBDaGan();
    },

    watch: {
        "BienMuc.id_don_vi"(val) {
            if (this.isLoadingBienMuc) return;
            this.BienMuc.id_chuyen_nganh = null;
            this.getChuyenNganhByDonVi(val);
        },
    },

    methods: {
        /* ---------- RÀNG BUỘC & SẮP XẾP MARC ---------- */
        marcRowsSorted() {
            const byNum = (a, b) => Number(a) - Number(b);

            const sortChildren = arr => {
                const num = arr.filter(c => /^\d+$/.test(c.ma_truong_con))
                    .sort((a, b) => byNum(a.ma_truong_con, b.ma_truong_con));
                const text = arr.filter(c => !/^\d+$/.test(c.ma_truong_con))
                    .sort((a, b) => a.ma_truong_con.localeCompare(b.ma_truong_con));
                return [...num, ...text];
            };

            return [...this.marcRows]
                .sort((a, b) => byNum(a.ma_truong, b.ma_truong))
                .map(p => ({ ...p, children: sortChildren(p.children || []) }));
        },

        /* ---------- COMBO DATA ---------- */
        async getListTaiLieu() {
            try {
                const { data } = await axios.get(
                    "/api/danh-muc/nghiep-vu-bien-muc/tai-lieu/list-tai-lieu-select-option"
                );
                if (data.status === 200) {
                    this.dsTaiLieu = data.data.map(d => ({
                        value: d.id_tai_lieu,
                        text: d.ten_tai_lieu,
                    }));
                }
            } catch (e) {
                console.error(e);
            }
        },

        async getListDonVi() {
            const { data } = await axios.get(
                "/api/danh-muc/thong-tin-chung/don-vi/list-don-vi-select-option"
            );
            if (data.status === 200) {
                this.dsDonVi = data.data.map(d => ({
                    value: d.id_don_vi,
                    text: d.ten_don_vi,
                }));
            }
        },

        async getChuyenNganhByDonVi(id) {
            if (!id) {
                this.dsChuyenNganh = [];
                return;
            }

            const { data } = await axios.get(
                `/api/danh-muc/thong-tin-chung/chuyen-nganh/by-don-vi/${id}`
            );
            if (data.status === 200) {
                this.dsChuyenNganh = data.data.map(d => ({
                    value: d.id_chuyen_nganh,
                    text: d.ten_chuyen_nganh,
                }));
            }
        },

        async thongTinBienMuc() {
            this.$refs.modal.$data.title = 'Thông tin biên mục';
            Object.assign(this.BienMuc, {
                id_tai_lieu: null,
                trang_thai_bieu_ghi: 'dang-bien-muc',
                id_don_vi: null,
                id_chuyen_nganh: null,
            });

            const idSach = this.$route.params.id_sach;

            try {
                const res = await axios.get(`/api/quan-ly-an-pham/nhan-sach/don-nhan/chi-tiet-don-nhan/bien-muc-bieu-ghi/${idSach}`);

                if (res.data.status === 200 && res.data.data) {
                    this.isLoadingBienMuc = true;
                    Object.assign(this.BienMuc, res.data.data);
                    await this.getChuyenNganhByDonVi(this.BienMuc.id_don_vi);
                    this.isLoadingBienMuc = false;
                }

                // Hiển thị modal khi có dữ liệu
                while (await this.$refs.modal.openModal()) {
                    try {
                        const saveRes = await axios.post("/api/quan-ly-an-pham/nhan-sach/don-nhan/chi-tiet-don-nhan/bien-muc-bieu-ghi/", this.BienMuc);

                        if (saveRes.data.status === 200) {
                            toastr.success(saveRes.data.message);
                            this.$refs.modal.closeModal();
                            break;
                        }
                    } catch (err) {
                        if (err.response?.status === 422) {
                            Object.values(err.response.data.errors)
                                .forEach(msg => toastr.error(msg[0]));
                        } else {
                            toastr.error("Lưu biên mục thất bại");
                            console.error(err);
                            break;
                        }
                    }
                }
            } catch (err) {
                toastr.error("Lỗi khi tải thông tin biên mục");
                console.error(err);
            }
        },

        /* ---------- MARC ---------- */
        marcBase() {
            return (
                "/api/quan-ly-an-pham/nhan-sach/don-nhan/chi-tiet-don-nhan"
                + "/bien-muc-bieu-ghi/bien-muc/marc"
            );
        },


        async fetchMarc() {
            this.isLoadingMarc = true;
            const { data } = await axios.get(
                `${this.marcBase()}/by-sach/${this.$route.params.id_sach}`
            );
            if (data.status === 200) this.marcRows = data.data;
            this.isLoadingMarc = false;
        },

        /* ----------------- THÊM TRƯỜNG CHA (MODAL) ----------------- */
        async themTruongCha() {
            this.newParent = { ma_truong: "", nhan: "", ct1: "", ct2: "" };

            while (await this.$refs.modalAddParent.openModal()) {

                /* VALIDATE FE */
                if (!/^\d{3}$/.test(this.newParent.ma_truong)) {
                    toastr.error("Nhãn phải gồm đúng 3 chữ số");
                    continue;
                }

                try {
                    const { data } = await axios.post(`${this.marcBase()}/parent`, {
                        id_sach: this.$route.params.id_sach,
                        ...this.newParent,
                    });

                    /* THÀNH CÔNG */
                    this.marcRows.push({ ...data.data, children: [] });
                    this.marcRows.sort((a, b) => Number(a.ma_truong) - Number(b.ma_truong));
                    toastr.success("Đã thêm trường cha");
                    this.$refs.modalAddParent.closeModal();
                    break;

                } catch (err) {
                    /* LỖI 422: hiện chi tiết */
                    if (err.response?.status === 422) {
                        Object.values(err.response.data.errors)
                            .forEach(msg => toastr.error(msg[0]));
                    } else {
                        toastr.error("Không thể thêm trường cha");
                    }
                    console.error(err);
                    break;
                }
            }
        },


        /* ---------- CALLBACKS TỪ TABLE ---------- */
        async handleAddParentAfter(idx) {
            try {
                const { data } = await axios.post(`${this.marcBase()}/add-parent-after`, {
                    id_sach: this.$route.params.id_sach,
                    index: idx,
                });
                if (data.status === 200) {
                    this.marcRows.splice(idx + 1, 0, { ...data.data, children: [] });
                    toastr.success("Đã thêm trường cha mới");
                }
            } catch (e) {
                console.error(e.response?.data || e);
                toastr.error("Không thể thêm trường cha");
            }
        },

        async handleRemoveParent(p) {
            await axios.delete(`${this.marcBase()}/parent/${p.id_bien_muc_truong_cha}`);
            this.fetchMarc();
        },

        async handleAddChild(parent) {
            try {
                const { data } = await axios.post(`${this.marcBase()}/child`, {
                    parent_id: parent.id_bien_muc_truong_cha,
                    ma_truong_con: "",       // hoặc gửi ký tự cụ thể tuỳ UI của bạn
                });

                /* ====== 200 – THÀNH CÔNG ====== */
                toastr.success("Đã thêm trường con");
                const real = this.marcRows.find(
                    p => p.id_bien_muc_truong_cha === parent.id_bien_muc_truong_cha);
                if (!real.children) this.$set(real, "children", []);
                real.children.push(data.data);
                this.$forceUpdate();

            } catch (err) {
                if (err.response?.status === 422) {
                    Object.values(err.response.data.errors)
                        .forEach(msg => toastr.error(msg[0]));
                } else {
                    toastr.error("Không thể thêm trường con");
                }
                console.error(err);
            }
        },

        async handleRemoveChild(parent, child) {
            await axios.delete(`${this.marcBase()}/child/${child.id_bien_muc_truong_con}`);
            this.fetchMarc();
        },

        async handleUpdateParent(parent) {
            if ((parent.ct1 && parent.ct1.length > 1) || (parent.ct2 && parent.ct2.length > 1)) {
                return toastr.error("CT1 / CT2 chỉ 1 ký tự");
            }
            if (parent.nhan && !/^\d{3}$/.test(parent.nhan)) {
                return toastr.error("Nhãn phải gồm 3 chữ số");
            }

            try {
                await axios.put(`${this.marcBase()}/parent/${parent.id_bien_muc_truong_cha}`, {
                    ma_truong: parent.ma_truong,
                    nhan: parent.nhan,
                    ct1: parent.ct1,
                    ct2: parent.ct2,
                });
                toastr.success("Đã lưu trường cha");
            } catch (e) {
                console.error(e.response?.data || e);
                toastr.error("Không thể lưu trường cha");
            }
        },

        async handleUpdateChild(parent, child) {
            if (!/^[0-9a-z]{1}$/i.test(child.ma_truong_con)) {
                return toastr.error("Trường con chỉ 1 ký tự 0-9 / a-z");
            }

            try {
                await axios.put(`${this.marcBase()}/child/${child.id_bien_muc_truong_con}`, {
                    ma_truong_con: child.ma_truong_con,
                    noi_dung: child.noi_dung,
                });
                toastr.success("Đã lưu trường con");
            } catch (err) {
                if (err.response?.status === 422) {
                    Object.values(err.response.data.errors)
                        .forEach(msg => toastr.error(msg[0]));
                } else {
                    toastr.error("Không thể thêm trường con");
                }
                console.error(err);
            }
        },

        async getSoLuongSach() {
            try {
                const idSach = this.$route.params.id_sach;
                const res = await axios.get(`/api/quan-ly-an-pham/nhan-sach/don-nhan/chi-tiet-don-nhan/sach/${idSach}`);

                if (res.data.status === 200 && res.data.data) {
                    this.soLuongSach = res.data.data.so_luong || 0;
                }
            } catch (e) {
                console.error(e);
                toastr.error("Lỗi khi tải thông tin số lượng sách");
            }
        },

        async ganDKCB() {
            // Reset form
            this.formDKCB = {
                ma_dkcb: "",
                isAutoAssign: true
            };
            this.thongBaoLoi = "";
            this.isLoadingDKCB = false;

            // Lấy danh sách DKCB đã gán
            await this.getDanhSachDKCBDaGan();

            // Mở modal gán DKCB
            while (await this.$refs.modalGanDKCB.openModal()) {
                try {
                    if (!this.formDKCB.ma_dkcb) {
                        toastr.error("Vui lòng nhập mã DKCB");
                        continue;
                    }

                    this.isLoadingDKCB = true;

                    // Thực hiện gán DKCB
                    const idSach = this.$route.params.id_sach;
                    const res = await axios.post('/api/quan-ly-an-pham/nhan-sach/don-nhan/chi-tiet-don-nhan/gan-dkcb', {
                        id_sach: idSach,
                        ma_dkcb: this.formDKCB.ma_dkcb,
                        so_luong: this.formDKCB.isAutoAssign ? (this.soLuongSach - this.danhSachDKCBDaGan.length) : 1,
                        auto_assign: this.formDKCB.isAutoAssign
                    });

                    this.isLoadingDKCB = false;

                    if (res.data.status === 200) {
                        toastr.success(res.data.message || "Đã gán số DKCB thành công");
                        // Cập nhật danh sách DKCB đã gán
                        await this.getDanhSachDKCBDaGan();

                        // Nếu đã gán đủ số lượng hoặc đang ở chế độ tự động liên tiếp thì đóng modal
                        if (this.danhSachDKCBDaGan.length >= this.soLuongSach || this.formDKCB.isAutoAssign) {
                            this.$refs.modalGanDKCB.closeModal();
                            break;
                        }

                        // Reset form để gán tiếp
                        this.formDKCB.ma_dkcb = "";
                    } else {
                        this.thongBaoLoi = res.data.message || "Gán số DKCB thất bại";
                        toastr.error(this.thongBaoLoi);
                    }
                } catch (e) {
                    this.isLoadingDKCB = false;

                    if (e.response?.status === 422) {
                        const errors = e.response.data.errors;
                        if (errors) {
                            this.thongBaoLoi = Object.values(errors)[0][0] || "Dữ liệu không hợp lệ";
                            toastr.error(this.thongBaoLoi);
                        }
                    } else {
                        this.thongBaoLoi = e.response?.data?.message || "Lỗi khi gán số DKCB";
                        toastr.error(this.thongBaoLoi);
                        console.error(e);
                    }
                }
            }
        },

        // Hàm mới để lấy danh sách DKCB đã gán cho sách
        async getDanhSachDKCBDaGan() {
            try {
                const idSach = this.$route.params.id_sach;
                const res = await axios.get(`/api/quan-ly-an-pham/nhan-sach/don-nhan/chi-tiet-don-nhan/sach/dkcb/${idSach}`);

                if (res.data.status === 200) {
                    this.danhSachDKCBDaGan = res.data.data || [];
                }
            } catch (e) {
                console.error(e);
                toastr.error("Lỗi khi tải danh sách DKCB đã gán");
            }
        },

        async xoaDKCB(dkcb) {
            try {
                // Hiển thị xác nhận trước khi xóa
                const result = await Swal.fire({
                    title: 'Bạn có chắc chắn?',
                    text: `Bạn có muốn xóa mã DKCB ${dkcb.ma_dkcb} khỏi sách này không?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Có, xóa nó!',
                    cancelButtonText: 'Hủy bỏ'
                });
                
                if (!result.isConfirmed) {
                    return;
                }
                
                this.isLoadingDKCB = true;
                const idSach = this.$route.params.id_sach;
                const res = await axios.delete(`/api/quan-ly-an-pham/nhan-sach/don-nhan/chi-tiet-don-nhan/sach/dkcb/${dkcb.id_dkcb}`);

                if (res.data.status === 200) {
                    Swal.fire(
                        'Đã xóa!',
                        res.data.message,
                        'success'
                    );
                    await this.getDanhSachDKCBDaGan();
                } else {
                    Swal.fire(
                        'Lỗi!',
                        res.data.message || "Lỗi khi xóa DKCB",
                        'error'
                    );
                }
                this.isLoadingDKCB = false;
            } catch (e) {
                console.error(e);
                Swal.fire(
                    'Lỗi!',
                    'Đã xảy ra lỗi khi xóa mã DKCB',
                    'error'
                );
                this.isLoadingDKCB = false;
            }
        },
    },
};
</script>
