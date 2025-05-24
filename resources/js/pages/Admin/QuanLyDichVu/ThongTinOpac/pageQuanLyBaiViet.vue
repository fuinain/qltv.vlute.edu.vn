<template>
    <ContentWrapper title="QUẢN LÝ BÀI VIẾT">
        <template #ContentPage>
            <Card>
                <template #ContentCardHeader>
                    <button type="button" class="btn btn-primary" @click="themBaiViet">
                        <i class="fas fa-plus-circle"></i>&nbsp;
                        Thêm mới
                    </button>
                </template>
                <template #ContentCardBody>
                    <div class="row mb-2">
                        <div class="col-md-8">                               
                        </div>
                        <div class="col-md-4 d-flex justify-content-end">
                            <div class="input-group search-group" style="width: 320px;">
                                <input type="text" v-model="search" class="form-control"
                                    placeholder="Tìm kiếm..." @keyup.enter="timKiem"
                                    style="border-top-right-radius: 0; border-bottom-right-radius: 0;">
                                <div class="input-group-append">
                                    <button type="button" class="btn btn-default" @click="timKiem">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <Table
                        :data="danhSachBaiViet"
                        :columns="columns"
                        :hideSearch="true"
                        :pagination="pagination"
                        :fetchData="fetchData"
                    >
                        <template #column-actions="{ row }">
                            <button type="button"
                                class="btn p-1 btn-primary border-0 bg-transparent text-primary shadow-none"
                                @click="suaBaiViet(row)">
                                <i class="fas fa-edit"></i>&nbsp;
                            </button>
                            <button type="button"
                                class="btn p-1 btn-primary border-0 bg-transparent text-danger shadow-none"
                                @click="xoaBaiViet(row)">
                                <i class="fas fa-trash-alt"></i>&nbsp;
                            </button>
                        </template>
                    </Table>
                </template>
            </Card>
        </template>
    </ContentWrapper>

    <!-- Modal Thêm/Sửa Bài viết -->
    <Modal ref="modalBaiViet" size="xl">
        <div class="row">
            <div class="col-md-4">
                <Input 
                    label="Tên bài viết" 
                    placeholder="Nhập tên bài viết" 
                    v-model="formBaiViet.ten_bai_viet"
                />
            </div>
            <div class="col-md-4">
                <SelectOption 
                    label="Chủ đề" 
                    placeholder="Chọn chủ đề" 
                    v-model="formBaiViet.id_chu_de_bai_viet" 
                    :options="danhSachChuDe"
                />
            </div>
            <div class="col-md-4">
                <SelectOption v-model="formBaiViet.noi_dung_navbar" :options="[
                    { value: 'khong', text: 'Không' },
                    { value: 'noi-quy', text: 'Nội quy' },
                    { value: 'huong-dan', text: 'Hướng dẫn' },
                    { value: 'lien-he', text: 'Liên hệ' },
                ]" label="Nội dung navbar" placeholder="Chọn nội dung navbar" />
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <TextArea 
                    label="Tóm tắt" 
                    placeholder="Nhập tóm tắt bài viết" 
                    v-model="formBaiViet.tom_tat"
                />
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label>Nội dung</label>
                    <textarea ref="summernote" class="summernote"></textarea>
                </div>
            </div>
        </div>
    </Modal>
</template>

<script>
import ContentWrapper from '@/components/layouts/ContentWrapper.vue';
import Card from '@/components/ui/Card.vue';
import Input from '@/components/ui/Input.vue';
import SelectOption from '@/components/ui/SelectOption.vue';
import TextArea from '@/components/ui/TextArea.vue';
import Modal from '@/components/modals/Modal.vue';
import Table from '@/components/tables/Table.vue';
import axios from 'axios';
import Swal from 'sweetalert2';

export default {
    name: "pageQuanLyBaiViet",
    components: {
        ContentWrapper,
        Card,
        Input,
        SelectOption,
        TextArea,
        Modal,
        Table
    },
    data() {
        return {
            danhSachBaiViet: [],
            danhSachChuDe: [],
            formBaiViet: {
                id_bai_viet: null,
                ten_bai_viet: '',
                tom_tat: '',
                noi_dung: '',
                id_chu_de_bai_viet: '',
                noi_dung_navbar: 'khong',
            },
            loading: false,
            search: '',
            currentPage: 1,
            columns: [
                { key: 'index', label: 'STT', sortable: false },
                { key: 'ten_bai_viet', label: 'Tên bài viết' },
                { key: 'ten_chu_de_bai_viet', label: 'Chủ đề' },
                { key: 'tom_tat', label: 'Tóm tắt' },
                { key: 'noi_dung_navbar', label: 'Navbar', format: (value) => this.formatNavbar(value) },
                { key: 'ngay_cap_nhat', label: 'Ngày cập nhật', format: 'datetime' },
                { key: 'actions', label: 'Thao tác', sortable: false },
            ],
            pagination: null
        };
    },
    mounted() {
        this.fetchData(1);
        this.layDanhSachChuDe();
    },
    methods: {
        initSummernote() {
            try {
                $(this.$refs.summernote).summernote({
                    height: 300,
                    tabsize: 2,
                    lang: 'vi-VN',
                    toolbar: [
                        ['style', ['style']],
                        ['font', ['bold', 'underline', 'clear']],
                        ['color', ['color']],
                        ['para', ['ul', 'ol', 'paragraph']],
                        ['table', ['table']],
                        ['insert', ['link', 'picture', 'video']],
                        ['view', ['fullscreen', 'codeview']]
                    ],
                    callbacks: {
                        onChange: (contents) => {
                            this.formBaiViet.noi_dung = contents;
                        }
                    }
                });
            } catch (error) {
                console.error('Lỗi khởi tạo Summernote:', error);
            }
        },
        setSummernoteContent(content) {
            $(this.$refs.summernote).summernote('code', content);
        },
        destroySummernote() {
            $(this.$refs.summernote).summernote('destroy');
        },
        async fetchData(page = 1) {
            try {
                this.loading = true;
                const params = {
                    page: page,
                    search: this.search
                };
                const response = await axios.get('/api/quan-ly-dich-vu/bai-viet', { params });
                if (response.data.status === 200) {
                    this.danhSachBaiViet = response.data.data.data || [];
                    this.pagination = response.data.data;
                }
            } catch (error) {
                console.error('Lỗi khi lấy danh sách bài viết:', error);
                toastr.error("Không thể lấy danh sách bài viết");
            } finally {
                this.loading = false;
            }
        },
        async layDanhSachChuDe() {
            try {
                const response = await axios.get('/api/quan-ly-dich-vu/bai-viet/chu-de');
                this.danhSachChuDe = response.data.map(item => ({
                    value: item.id_chu_de_bai_viet,
                    text: item.ten_chu_de_bai_viet
                }));
            } catch (error) {
                console.error('Lỗi khi lấy danh sách chủ đề:', error);
                toastr.error("Không thể lấy danh sách chủ đề");
            }
        },
        themBaiViet() {
            // Reset form cho trường hợp thêm mới
            this.$refs.modalBaiViet.$data.title = "Thêm bài viết mới";
            this.$refs.modalBaiViet.$data.save = "Thêm mới";
            this.formBaiViet = {
                id_bai_viet: null,
                ten_bai_viet: '',
                tom_tat: '',
                noi_dung: '',
                id_chu_de_bai_viet: '',
                noi_dung_navbar: 'khong',
            };

            // Mở modal
            this.$refs.modalBaiViet.openModal().then(confirmed => {
                if (!confirmed) {
                    this.destroySummernote();
                    return;
                }
                
                // Kiểm tra validate
                if (!this.formBaiViet.ten_bai_viet) {
                    toastr.error("Vui lòng nhập đầy đủ thông tin bắt buộc");
                    return;
                }

                this.luuBaiViet();
            });
            
            // Khởi tạo Summernote sau khi modal hiển thị
            this.$nextTick(() => {
                this.initSummernote();
            });
        },
        suaBaiViet(baiViet) {
            // Set tiêu đề và điền dữ liệu hiện có vào form
            this.$refs.modalBaiViet.$data.title = "Cập nhật bài viết";
            this.$refs.modalBaiViet.$data.save = "Cập nhật";
            
            this.formBaiViet = { ...baiViet };
            
            // Mở modal để chỉnh sửa
            this.$refs.modalBaiViet.openModal().then(confirmed => {
                if (!confirmed) {
                    this.destroySummernote();
                    return;
                }
                
                // Kiểm tra validate
                if (!this.formBaiViet.ten_bai_viet) {
                    toastr.error("Vui lòng nhập đầy đủ thông tin bắt buộc");
                    return;
                }
                
                this.luuBaiViet();
            });
            
            // Khởi tạo Summernote và đặt nội dung sau khi modal hiển thị
            this.$nextTick(() => {
                this.initSummernote();
                this.setSummernoteContent(baiViet.noi_dung);
            });
        },
        async luuBaiViet() {
            try {
                let response;

                if (this.formBaiViet.id_bai_viet) {
                    response = await axios.put(`/api/quan-ly-dich-vu/bai-viet/${this.formBaiViet.id_bai_viet}`, this.formBaiViet);
                } else {
                    response = await axios.post('/api/quan-ly-dich-vu/bai-viet', this.formBaiViet);
                }

                if (response.data.status === 200) {
                    toastr.success(response.data.message);
                    this.fetchData(this.currentPage);
                    this.destroySummernote();
                    this.$refs.modalBaiViet.closeModal();
                }
            } catch (error) {
                console.error('Lỗi khi lưu bài viết:', error);
                if (error.response && error.response.data && error.response.data.errors) {
                    // Chỉ hiển thị mỗi thông báo lỗi một lần
                    const errors = Object.values(error.response.data.errors).flat();
                    const uniqueErrors = [...new Set(errors)];
                    uniqueErrors.forEach(err => toastr.error(err));
                    
                    // Không cần hiển thị lỗi cụ thể cho noi_dung_navbar nữa vì đã bao gồm trong errors ở trên
                } else {
                    toastr.error("Lưu bài viết thất bại!");
                }
            }
        },
        async xoaBaiViet(row) {
            Swal.fire({
                title: 'Bạn có chắc chắn?',
                text: "Dữ liệu sau khi bị xoá không thể khôi phục lại !!!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Có, xoá nó!',
                cancelButtonText: 'Hủy bỏ'
            }).then(async (result) => {
                if (result.isConfirmed) {
                    try {
                        const response = await axios.delete(`/api/quan-ly-dich-vu/bai-viet/${row.id_bai_viet}`);
                        if (response.data.status === 200) {
                            toastr.success(response.data.message);
                            this.fetchData(this.currentPage);
                        }
                    } catch (error) {
                        console.error('Lỗi khi xóa bài viết:', error);
                        toastr.error("Xóa bài viết thất bại!");
                    }
                }
            });
        },
        timKiem() {
            this.fetchData(1);
        },
        formatNavbar(value) {
            const navbarOptions = {
                'khong': 'Không',
                'noi-quy': 'Nội quy',
                'huong-dan': 'Hướng dẫn',
                'lien-he': 'Liên hệ'
            };
            return navbarOptions[value] || value;
        },
    }
};
</script>

<style scoped>
.search-group .form-control {
    border-right: none;
    box-shadow: none;
}

.btn-search {
    background: transparent;
    border: 1px solid #ced4da;
    border-left: none;
    color: #007bff;
    transition: background 0.2s, color 0.2s;
}

.btn-search:hover {
    background: #f0f4fa;
    color: #0056b3;
}
</style>
