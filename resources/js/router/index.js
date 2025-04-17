// resources/js/router/index.js
import { createRouter, createWebHistory } from "vue-router";
import Admin from "@/pages/Admin/pageAdmin.vue";
import DocGia from "@/pages/DocGia/index.vue";
import pagePhongKhoa from "@/pages/Admin/DanhMuc/ThongTinChung/PhongKhoa/pagePhongKhoa.vue";
import pageChucVu from "@/pages/Admin/DanhMuc/ThongTinChung/ChucVu/pageChucVu.vue";
import pageNamHoc from "@/pages/Admin/DanhMuc/ThongTinChung/NamHoc/pageNamHoc.vue";
import pageLopHoc from "@/pages/Admin/DanhMuc/ThongTinChung/LopHoc/pageLopHoc.vue";
import pageChuyenNganh from "@/pages/Admin/DanhMuc/ThongTinChung/ChuyenNganh/pageChuyenNganh.vue";
import pageDoiTuongBanDoc from "@/pages/Admin/DanhMuc/NghiepVuLuuThong/DoiTuongBanDoc/pageDoiTuongBanDoc.vue";
import pageDiemLuuThong from "@/pages/Admin/DanhMuc/NghiepVuLuuThong/DiemLuuThong/pageDiemLuuThong.vue";
import pageThamSoLuuThong from "@/pages/Admin/DanhMuc/NghiepVuLuuThong/ThamSoLuuThong/pageThamSoLuuThong.vue";
import pageCTThamSoLuuThong from "@/pages/Admin/DanhMuc/NghiepVuLuuThong/ThamSoLuuThong/pageCTThamSoLuuThong.vue";
import pagePhatBanDoc from "@/pages/Admin/DanhMuc/NghiepVuLuuThong/PhatBanDoc/pagePhatBanDoc.vue";
import pageTaiLieuThuMuc from "@/pages/Admin/DanhMuc/NghiepVuBienMuc/ThucMucTaiLieu/pageTaiLieuThuMuc.vue";
import pageDMKhoAnPham from "@/pages/Admin/QuanLyAnPham/KhoAnPham/pageDMKhoAnPham.vue";
import AdminLayout from "@/components/layouts/AdminLayout.vue";
import PublicLayout from "@/components/layouts/PublicLayout.vue";
const routes = [
    {
        path: "/",
        redirect: (to) => {
            const userRole = window.userRole;

            if (userRole === "admin") {
                return "/admin";
            } else if (userRole === "docgia") {
                return "/docgia";
            } else {
                return "Bạn không có quyền truy cập vào trang này.";
            }
        },
    },
    {
        path: "/admin",
        component: AdminLayout,
        children: [
            {
                path: "",
                name: "AdminIndex",
                component: Admin,
            },
            {
                path: "danh-muc",
                children: [
                    {
                        path: "thong-tin-chung",
                        children: [
                            {
                                path: "phong-khoa",
                                name: "pagePhongKhoa",
                                component: pagePhongKhoa,
                                meta: {
                                    title: "Phòng/Khoa",
                                    breadcrumb: [
                                        { name: "Home", path: "/" },
                                        { name: "Danh mục"},
                                        { name: "Thông tin chung"},
                                        { name: "Phòng/Khoa" },
                                    ],
                                },
                            },
                            {
                                path: "chuc-vu",
                                name: "pageChucVu",
                                component: pageChucVu,
                                meta: {
                                    title: "Chức vụ",
                                    breadcrumb: [
                                        { name: "Home", path: "/" },
                                        { name: "Danh mục"},
                                        { name: "Thông tin chung"},
                                        { name: "Chức vụ" },
                                    ],
                                },
                            },
                            {
                                path: "nam-hoc",
                                name: "pageNamHoc",
                                component: pageNamHoc,
                                meta: {
                                    title: "Năm học",
                                    breadcrumb: [
                                        { name: "Home", path: "/" },
                                        { name: "Danh mục"},
                                        { name: "Thông tin chung"},
                                        { name: "Năm học" },
                                    ],
                                },
                            },
                            {
                                path: "lop-hoc",
                                name: "pageLopHoc",
                                component: pageLopHoc,
                                meta: {
                                    title: "Lớp học",
                                    breadcrumb: [
                                        { name: "Home", path: "/" },
                                        { name: "Danh mục"},
                                        { name: "Thông tin chung"},
                                        { name: "Lớp học" },
                                    ],
                                },
                            },
                            {
                                path: "chuyen-nganh",
                                name: "pageChuyenNganh",
                                component: pageChuyenNganh,
                                meta: {
                                    title: "Năm học",
                                    breadcrumb: [
                                        { name: "Home", path: "/" },
                                        { name: "Danh mục"},
                                        { name: "Thông tin chung"},
                                        { name: "Chuyên ngành" },
                                    ],
                                },
                            },
                        ],
                    },
                    {
                        path: "nghiep-vu-bien-muc",
                        children: [
                            {
                                path: "tai-lieu-thu-muc",
                                name: "pageTaiLieuThuMuc",
                                component: pageTaiLieuThuMuc,
                                meta: {
                                    title: "Cấu hình thư mục tài liệu",
                                    breadcrumb: [
                                        { name: "Home", path: "/" },
                                        { name: "Danh mục", path: "/admin" },
                                        { name: "Nghiệp vụ biên mục"},
                                        { name: "Tài liệu & Thư mục" },
                                    ],
                                },
                            },
                        ],
                    },
                    {
                        path: "nghiep-vu-luu-thong",
                        children: [
                            {
                                path: "doi-tuong-ban-doc",
                                name: "pageDoiTuongBanDoc",
                                component: pageDoiTuongBanDoc,
                                meta: {
                                    title: "Đối tượng bạn đọc",
                                    breadcrumb: [
                                        { name: "Home", path: "/" },
                                        { name: "Danh mục"},
                                        { name: "Nghiệp vụ lưu thông"},
                                        { name: "Đối tượng bạn đọc" },
                                    ],
                                },
                            },
                            {
                                path: "diem-luu-thong",
                                name: "pageDiemLuuThong",
                                component: pageDiemLuuThong,
                                meta: {
                                    title: "Khai báo điểm lưu thông",
                                    breadcrumb: [
                                        { name: "Home", path: "/" },
                                        { name: "Danh mục"},
                                        { name: "Nghiệp vụ lưu thông"},
                                        { name: "Điểm lưu thông" },
                                    ],
                                },
                            },
                            {
                                path: "tham-so-luu-thong",
                                name: "pageThamSoLuuThong",
                                component: pageThamSoLuuThong,
                                meta: {
                                    title: "Thiết lập tham số lưu thông",
                                    breadcrumb: [
                                        { name: "Home", path: "/" },
                                        { name: "Danh mục"},
                                        { name: "Nghiệp vụ lưu thông"},
                                        { name: "Thiết lập tham số lưu thông" },
                                    ],
                                },
                            },
                            {
                                path: "tham-so-luu-thong/chi-tiet-tham-so/:id_doi_tuong_ban_doc",
                                name: "pageCTThamSoLuuThong",
                                component: pageCTThamSoLuuThong,
                                meta: {
                                    title: "Chi tiết tham số lưu thông",
                                    breadcrumb: [
                                        { name: "Home", path: "/" },
                                        { name: "Danh mục" },
                                        { name: "Nghiệp vụ lưu thông" },
                                        {
                                            name: "Thiết lập tham số lưu thông",
                                            path: "/admin/danh-muc/nghiep-vu-luu-thong/tham-so-luu-thong",
                                        },
                                        { name: "Chi tiết tham số lưu thông" },
                                    ],
                                },
                            },
                            {
                                path: "phat-ban-doc",
                                name: "pagePhatBanDoc",
                                component: pagePhatBanDoc,
                                meta: {
                                    title: "Phạt bạn đọc",
                                    breadcrumb: [
                                        { name: "Home", path: "/" },
                                        { name: "Danh mục"},
                                        { name: "Nghiệp vụ lưu thông"},
                                        { name: "Phạt bạn đọc" },
                                    ],
                                },
                            },
                        ],
                    },
                ],
            },
            {
                path: "quan-ly-an-pham",
                children: [
                    {
                        path: "kho-an-pham",
                        children: [
                            {
                                path: "danh-muc-an-pham",
                                name: "pageDMKhoAnPham",
                                component: pageDMKhoAnPham,
                                meta: {
                                    title: "Danh mục kho ấn phẩm",
                                    breadcrumb: [
                                        { name: "Home", path: "/" },
                                        { name: "Quản lý ấn phẩm"},
                                        { name: "Kho ấn phẩm"},
                                        { name: "Danh mục kho" },
                                    ],
                                },
                            },
                        ],
                    },
                ],
            }
        ],
    },
    {
        path: "/docgia",
        component: PublicLayout,
        children: [
            {
                path: "",
                name: "DocGiaIndex",
                component: DocGia,
            },
        ],
    },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

export default router;
