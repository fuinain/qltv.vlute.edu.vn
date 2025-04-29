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
                            <TableMarc
                                :rows="marcRowsSorted()"
                                @add-parent-after="handleAddParentAfter"
                                @remove-parent="handleRemoveParent"
                                @add-child="handleAddChild"
                                @remove-child="handleRemoveChild"
                                @update-parent="handleUpdateParent"
                                @update-child="handleUpdateChild"
                            />
                        </template>
                    </Card>
                </div>
            </div>
        </template>
    </ContentWrapper>

    <!-- ---------- MODAL: THÔNG TIN BIÊN MỤC (đã có) ---------- -->
    <Modal ref="modal">
        <div class="row">
            <div class="col-6">
                <SelectOption
                    v-model="BienMuc.id_tai_lieu"
                    :options="dsTaiLieu"
                    label="Loại tài liệu"
                    placeholder="Chọn loại tài liệu"
                />
            </div>
            <div class="col-6">
                <SelectOption
                    v-model="BienMuc.trang_thai_bieu_ghi"
                    :options="[
            { value: 'dang-bien-muc', text: 'Đang biên mục' },
            { value: 'da-duyet',      text: 'Đã duyệt' }
          ]"
                    label="Trạng thái"
                    placeholder="Chọn TT biểu ghi"
                />
            </div>
            <div class="col-6">
                <SelectOption
                    v-model="BienMuc.id_don_vi"
                    :options="dsDonVi"
                    label="Đơn vị"
                    placeholder="Chọn đơn vị"
                />
            </div>
            <div class="col-6">
                <SelectOption
                    v-model="BienMuc.id_chuyen_nganh"
                    :options="dsChuyenNganh"
                    label="Chuyên ngành"
                    :disabled="!BienMuc.id_don_vi"
                />
            </div>
        </div>
    </Modal>

    <!-- ---------- MODAL: THÊM TRƯỜNG CHA ---------- -->
    <Modal ref="modalAddParent">
        <template #title>THÊM TRƯỜNG CHA</template>
        <div class="row">
            <div class="col-4">
                <label class="small mb-1">Nhãn (3 chữ số)</label>
                <input v-model="newParent.ma_truong"
                       class="form-control form-control-sm"
                       maxlength="3" placeholder="vd: 050"/>
            </div>
            <div class="col-4">
                <label class="small mb-1">CT1 (1 ký tự)</label>
                <input v-model="newParent.ct1"
                       class="form-control form-control-sm"
                       maxlength="1" placeholder="a / # / …"/>
            </div>
            <div class="col-4">
                <label class="small mb-1">CT2 (1 ký tự)</label>
                <input v-model="newParent.ct2"
                       class="form-control form-control-sm"
                       maxlength="1" placeholder="# / 0 / …"/>
            </div>
        </div>
    </Modal>
</template>

<script>

export default {
    name: "pageBienMucBieuGhiBienMuc",

    data() {
        return {
            /* ---------- danh sách chọn ---------- */
            dsTaiLieu: [],
            dsDonVi: [],
            dsChuyenNganh: [],
            dsTrangThai: [
                {value: "dang-bien-muc", text: "Đang biên mục"},
                {value: "da-duyet", text: "Đã duyệt"},
            ],

            /* ---------- form Biên Mục ---------- */
            BienMuc: {
                id_sach: this.$route.params.id_sach,
                id_tai_lieu: null,
                trang_thai_bieu_ghi: "dang-bien-muc",
                id_don_vi: null,
                id_chuyen_nganh: null,
            },

            /* ---------- danh sách MARC ---------- */
            marcRows: [],
            tenSach: this.$route.query.ten_sach || "",
            isLoadingMarc: false,
            isLoadingBienMuc: false,

            /* ---------- form THÊM TRƯỜNG CHA ---------- */
            newParent: {ma_truong: "", nhan: "", ct1: "", ct2: ""},
        };
    },

    mounted() {
        this.getListTaiLieu();
        this.getListDonVi();
        this.fetchMarc();
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
                .map(p => ({...p, children: sortChildren(p.children || [])}));
        },

        /* ---------- COMBO DATA ---------- */
        async getListTaiLieu() {
            try {
                const {data} = await axios.get(
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
            const {data} = await axios.get(
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

            const {data} = await axios.get(
                `/api/danh-muc/thong-tin-chung/chuyen-nganh/by-don-vi/${id}`
            );
            if (data.status === 200) {
                this.dsChuyenNganh = data.data.map(d => ({
                    value: d.id_chuyen_nganh,
                    text: d.ten_chuyen_nganh,
                }));
            }
        },

        // async thongTinBienMuc() {
        //     this.$refs.modal.$data.title = 'Thông tin biên mục';
        //     Object.assign(this.BienMuc, {
        //         id_tai_lieu        : null,
        //         trang_thai_bieu_ghi: 'dang-bien-muc',
        //         id_don_vi          : null,
        //         id_chuyen_nganh    : null,
        //     });
        //
        //     const idSach = this.$route.params.id_sach;
        //
        //     try {
        //         const res = await axios.get(`/api/quan-ly-an-pham/nhan-sach/don-nhan/chi-tiet-don-nhan/bien-muc-bieu-ghi/${idSach}`);
        //
        //         if (res.data.status === 200 && res.data.data) {
        //             this.isLoadingBienMuc = true;
        //             Object.assign(this.BienMuc, res.data.data);
        //             await this.getChuyenNganhByDonVi(this.BienMuc.id_don_vi);
        //             this.isLoadingBienMuc = false;
        //         }
        //
        //         // Hiển thị modal khi có dữ liệu
        //         while (await this.$refs.modal.openModal()) {
        //             try {
        //                 const saveRes = await axios.post("/api/quan-ly-an-pham/nhan-sach/don-nhan/chi-tiet-don-nhan/bien-muc-bieu-ghi/", this.BienMuc);
        //
        //                 if (saveRes.data.status === 200) {
        //                     toastr.success(saveRes.data.message);
        //                     this.$refs.modal.closeModal();
        //                     break;
        //                 }
        //             } catch (err) {
        //                 if (err.response?.status === 422) {
        //                     Object.values(err.response.data.errors)
        //                         .forEach(msg => toastr.error(msg[0]));
        //                 } else {
        //                     toastr.error("Lưu biên mục thất bại");
        //                     console.error(err);
        //                     break;
        //                 }
        //             }
        //         }
        //     } catch (err) {
        //         toastr.error("Lỗi khi tải thông tin biên mục");
        //         console.error(err);
        //     }
        // },

        /* ---------- MARC ---------- */
        marcBase() {
            return (
                "/api/quan-ly-an-pham/nhan-sach/don-nhan/chi-tiet-don-nhan"
                + "/bien-muc-bieu-ghi/bien-muc/marc"
            );
        },


        async fetchMarc() {
            this.isLoadingMarc = true;
            const {data} = await axios.get(
                `${this.marcBase()}/by-sach/${this.$route.params.id_sach}`
            );
            if (data.status === 200) this.marcRows = data.data;
            this.isLoadingMarc = false;
        },

        /* ----------------- THÊM TRƯỜNG CHA (MODAL) ----------------- */
        async themTruongCha() {
            this.newParent = {ma_truong: "", nhan: "", ct1: "", ct2: ""};

            while (await this.$refs.modalAddParent.openModal()) {

                /* VALIDATE FE */
                if (!/^\d{3}$/.test(this.newParent.ma_truong)) {
                    toastr.error("Nhãn phải gồm đúng 3 chữ số");
                    continue;
                }

                try {
                    const {data} = await axios.post(`${this.marcBase()}/parent`, {
                        id_sach: this.$route.params.id_sach,
                        ...this.newParent,
                    });

                    /* THÀNH CÔNG */
                    this.marcRows.push({...data.data, children: []});
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
                const {data} = await axios.post(`${this.marcBase()}/add-parent-after`, {
                    id_sach: this.$route.params.id_sach,
                    index: idx,
                });
                if (data.status === 200) {
                    this.marcRows.splice(idx + 1, 0, {...data.data, children: []});
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
                const {data} = await axios.post(`${this.marcBase()}/child`, {
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
    },
};
</script>
