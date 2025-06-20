// resources/js/router/index.js
import { createRouter, createWebHistory } from "vue-router";
import Admin from "@/pages/Admin/pageAdmin.vue";
import pagePhongKhoa from "@/pages/Admin/DanhMuc/ThongTinChung/PhongKhoa/pagePhongKhoa.vue";
import pageGiangVien from "@/pages/Admin/DanhMuc/ThongTinChung/pageGiangVien.vue";
import pageChucVu from "@/pages/Admin/DanhMuc/ThongTinChung/ChucVu/pageChucVu.vue";
import pageNamHoc from "@/pages/Admin/DanhMuc/ThongTinChung/NamHoc/pageNamHoc.vue";
import pageLopHoc from "@/pages/Admin/DanhMuc/ThongTinChung/LopHoc/pageLopHoc.vue";
import pageChuyenNganh from "@/pages/Admin/DanhMuc/ThongTinChung/ChuyenNganh/pageChuyenNganh.vue";
import pageNCC from "@/pages/Admin/DanhMuc/NghiepVuBoSung/NhaCungCap/pageNhaCungCap.vue";
import pageTrangThaiDon from "@/pages/Admin/DanhMuc/NghiepVuBoSung/TrangThaiDon/pageTrangThaiDon.vue";
import pageNguonNhan from "@/pages/Admin/DanhMuc/NghiepVuBoSung/NguonNhan/pageNguonNhan.vue";
import pageLoaiNhap from "@/pages/Admin/DanhMuc/NghiepVuBoSung/LoaiNhap/pageLoaiNhap.vue";
import pageDoiTuongBanDoc from "@/pages/Admin/DanhMuc/NghiepVuLuuThong/DoiTuongBanDoc/pageDoiTuongBanDoc.vue";
import pageDiemLuuThong from "@/pages/Admin/DanhMuc/NghiepVuLuuThong/DiemLuuThong/pageDiemLuuThong.vue";
import pageThamSoLuuThong from "@/pages/Admin/DanhMuc/NghiepVuLuuThong/ThamSoLuuThong/pageThamSoLuuThong.vue";
import pageCTThamSoLuuThong from "@/pages/Admin/DanhMuc/NghiepVuLuuThong/ThamSoLuuThong/pageCTThamSoLuuThong.vue";
import pagePhatBanDoc from "@/pages/Admin/DanhMuc/NghiepVuLuuThong/PhatBanDoc/pagePhatBanDoc.vue";
import pageTaiLieuThuMuc from "@/pages/Admin/DanhMuc/NghiepVuBienMuc/ThucMucTaiLieu/pageTaiLieuThuMuc.vue";
import pageDMKhoAnPham from "@/pages/Admin/QuanLyAnPham/KhoAnPham/pageDMKhoAnPham.vue";
import pageDonNhan from "@/pages/Admin/QuanLyAnPham/NhanSach/pageDonNhan.vue";
import pageCTDonNhan from "@/pages/Admin/QuanLyAnPham/NhanSach/pageCTDonNhan.vue";
import pageBienMucBieuGhiSach from "@/pages/Admin/QuanLyAnPham/NhanSach/pageBienMucBieuGhiSach.vue";
import pageInNhanDKCB from "@/pages/Admin/QuanLyAnPham/InNhan/pageInNhanDKCB.vue";
import pageInNhanPhanLoai from "@/pages/Admin/QuanLyAnPham/InNhan/pageInNhanPhanLoai.vue";
import pageBCNhanSachPhanKho from "@/pages/Admin/QuanLyAnPham/NhanSach/pageBCNhanSachPhanKho.vue";
import pageDocGia from "@/pages/Admin/QuanLyBanDoc/pageDocGia.vue";
import pageTKBanDocTheoDT from "@/pages/Admin/QuanLyBanDoc/pageTKBanDocTheoDT.vue";
import pageChiTietBanDocTheoDT from "@/pages/Admin/QuanLyBanDoc/pageChiTietBanDocTheoDT.vue";
import pageMuonTaiLieu from "@/pages/Admin/QuanLyDichVu/QuanLyLuuThong/pageMuonTaiLieu.vue";
import pageDocTaiCho from "@/pages/Admin/QuanLyDichVu/QuanLyLuuThong/pageDocTaiCho.vue";
import pageBaoCaoHoatDongTV from "@/pages/Admin/QuanLyDichVu/QuanLyLuuThong/pageBaoCaoHoatDongTV.vue";
import pageXuLyViPham from "@/pages/Admin/QuanLyDichVu/QuanLyLuuThong/pageXuLyViPham.vue";
import pageDMBaoCao from "@/pages/Admin/DanhMuc/NghiepVuBoSung/DMBaoCao/pageDMBaoCao.vue";
import AdminLayout from "@/components/layouts/AdminLayout.vue";
import PublicLayout from "@/components/layouts/PublicLayout.vue";
import OpacLayout from "@/components/layouts/OpacLayout.vue";
import OpacHome from "@/pages/Opac/OpacHome.vue";
import pageQuanLyBaiViet from "@/pages/Admin/QuanLyDichVu/ThongTinOpac/pageQuanLyBaiViet.vue";
import pageThietLapKiemKe from "@/pages/Admin/KiemKeThanhLy/pageThietLapKiemKe.vue";
import pageThanhLy from "@/pages/Admin/KiemKeThanhLy/pageThanhLy.vue";

// Cần tạo các component này sau
const OpacNoiQuy = () => import('@/pages/Opac/OpacNoiQuy.vue');
const OpacHuongDan = () => import('@/pages/Opac/OpacHuongDan.vue');
const OpacLienHe = () => import('@/pages/Opac/OpacLienHe.vue');
const OpacSearchResult = () => import('@/pages/Opac/OpacSearchResult.vue');
const OpacCTBaiViet = () => import('@/pages/Opac/OpacCTBaiViet.vue');
const OpacDanhSachSach = () => import('@/pages/Opac/OpacDanhSachSach.vue');
const OpacChiTietSach = () => import('@/pages/Opac/OpacChiTietSach.vue');
const SinhVienKhongTonTai = () => import('@/pages/Opac/SinhVienKhongTonTai.vue');
const ThongTinSinhVien = () => import('@/pages/Opac/ThongTinSinhVien.vue');
const LichSuMuon = () => import('@/pages/Opac/LichSuMuon.vue');

const routes = [
    {
        path: "/",
        component: OpacLayout,
        children: [
            {
                path: "",
                name: "OpacHome",
                component: OpacHome,
                meta: {
                    title: "Trang chủ thư viện"
                }
            },
            {
                path: "noi-quy",
                name: "OpacNoiQuy",
                component: OpacNoiQuy,
                meta: {
                    title: "Nội quy"
                }
            },
            {
                path: "huong-dan",
                name: "OpacHuongDan",
                component: OpacHuongDan,
                meta: {
                    title: "Hướng dẫn sử dụng"
                }
            },
            {
                path: "lien-he",
                name: "OpacLienHe",
                component: OpacLienHe,
                meta: {
                    title: "Liên hệ"
                }
            },
            {
                path: "tim-kiem",
                name: "OpacSearchResult",
                component: OpacSearchResult,
                meta: {
                    title: "Kết quả tìm kiếm"
                }
            },
            {
                path: "bai-viet/:id",
                name: "OpacCTBaiViet",
                component: OpacCTBaiViet,
                meta: {
                    title: "Chi tiết bài viết"
                }
            },
            {
                path: "danh-sach-sach",
                name: "OpacDanhSachSach",
                component: OpacDanhSachSach,
                meta: {
                    title: "Danh sách sách"
                }
            },
            {
                path: "tai-lieu/:id",
                name: "OpacDanhSachSachTheoTaiLieu",
                component: OpacDanhSachSach,
                meta: {
                    title: "Danh sách sách theo tài liệu"
                }
            },
            {
                path: "sach/:id",
                name: "OpacChiTietSach",
                component: OpacChiTietSach,
                meta: {
                    title: "Chi tiết sách"
                }
            },
            {
                path: "sinh-vien-khong-ton-tai",
                name: "SinhVienKhongTonTai",
                component: SinhVienKhongTonTai,
                meta: {
                    title: "Sinh viên không tồn tại"
                }
            },
            {
                path: "thong-tin-sinh-vien",
                name: "ThongTinSinhVien",
                component: ThongTinSinhVien,
                meta: {
                    title: "Thông tin sinh viên",
                    requiresAuth: true
                }
            },
            {
                path: "lich-su-muon",
                name: "LichSuMuon",
                component: LichSuMuon,
                meta: {
                    title: "Lịch sử mượn sách",
                    requiresAuth: true
                }
            }
        ]
    },
    {
        path: "/login",
        name: "Login",
        beforeEnter: (to, from, next) => {
            window.location.href = '/login';
        }
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
                                        { name: "Danh mục" },
                                        { name: "Thông tin chung" },
                                        { name: "Phòng/Khoa" },
                                    ],
                                },
                            },
                            {
                                path: "giang-vien",
                                name: "pageGiangVien",
                                component: pageGiangVien,
                                meta: {
                                    title: "Giảng viên",
                                    breadcrumb: [
                                        { name: "Home", path: "/" },
                                        { name: "Danh mục" },
                                        { name: "Thông tin chung" },
                                        { name: "Giảng viên" },
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
                                        { name: "Danh mục" },
                                        { name: "Thông tin chung" },
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
                                        { name: "Danh mục" },
                                        { name: "Thông tin chung" },
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
                                        { name: "Danh mục" },
                                        { name: "Thông tin chung" },
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
                                        { name: "Danh mục" },
                                        { name: "Thông tin chung" },
                                        { name: "Chuyên ngành" },
                                    ],
                                },
                            },
                        ],
                    },
                    {
                        path: "nghiep-vu-bo-sung",
                        children: [
                            {
                                path: "ncc",
                                name: "pageNCC",
                                component: pageNCC,
                                meta: {
                                    title: "Nhà cung cấp",
                                    breadcrumb: [
                                        { name: "Home", path: "/" },
                                        { name: "Danh mục" },
                                        { name: "Nghiệp vụ bổ sung" },
                                        { name: "Nhà cung cấp" },
                                    ],
                                },
                            },
                            {
                                path: "trang-thai-don",
                                name: "pageTrangThaiDon",
                                component: pageTrangThaiDon,
                                meta: {
                                    title: "Trạng thái đơn",
                                    breadcrumb: [
                                        { name: "Home", path: "/" },
                                        { name: "Danh mục" },
                                        { name: "Nghiệp vụ bổ sung" },
                                        { name: "Trạng thái đơn" },
                                    ],
                                },
                            },
                            {
                                path: "nguon-nhan",
                                name: "pageNguonNhan",
                                component: pageNguonNhan,
                                meta: {
                                    title: "Nguồn nhận",
                                    breadcrumb: [
                                        { name: "Home", path: "/" },
                                        { name: "Danh mục" },
                                        { name: "Nghiệp vụ bổ sung" },
                                        { name: "Nguồn nhận" },
                                    ],
                                },
                            },
                            {
                                path: "loai-nhap",
                                name: "pageLoaiNhap",
                                component: pageLoaiNhap,
                                meta: {
                                    title: "Loại nhập",
                                    breadcrumb: [
                                        { name: "Home", path: "/" },
                                        { name: "Danh mục" },
                                        { name: "Nghiệp vụ bổ sung" },
                                        { name: "Loại nhập" },
                                    ],
                                },
                            },
                            {
                                path: "dm-bao-cao",
                                name: "pageDMBaoCao",
                                component: pageDMBaoCao,
                                meta: {
                                    title: "Báo cáo",
                                    breadcrumb: [
                                        { name: "Home", path: "/" },
                                        { name: "Danh mục" },
                                        { name: "Nghiệp vụ bổ sung" },
                                        { name: "Báo cáo" },
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
                                        { name: "Nghiệp vụ biên mục" },
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
                                        { name: "Danh mục" },
                                        { name: "Nghiệp vụ lưu thông" },
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
                                        { name: "Danh mục" },
                                        { name: "Nghiệp vụ lưu thông" },
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
                                        { name: "Danh mục" },
                                        { name: "Nghiệp vụ lưu thông" },
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
                                        { name: "CT tham số lưu thông" },
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
                                        { name: "Danh mục" },
                                        { name: "Nghiệp vụ lưu thông" },
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
                                        { name: "Quản lý ấn phẩm" },
                                        { name: "Kho ấn phẩm" },
                                        { name: "Danh mục kho" },
                                    ],
                                },
                            },
                        ],
                    },
                    {
                        path: "quan-ly-nhan-sach",
                        children: [
                            {
                                path: "don-nhan",
                                name: "pageDonNhan",
                                component: pageDonNhan,
                                meta: {
                                    title: "Đơn nhận",
                                    breadcrumb: [
                                        { name: "Home", path: "/" },
                                        { name: "Quản lý ấn phẩm" },
                                        { name: "Quản lý nhận sách" },
                                        { name: "Đơn nhận" },
                                    ],
                                },
                            },
                            {
                                path: "don-nhan/chi-tiet-don-nhan/:id_don_nhan",
                                name: "pageCTDonNhan",
                                component: pageCTDonNhan,
                                meta: {
                                    title: "Chi tiết đơn nhận",
                                    breadcrumb: [
                                        { name: "Home", path: "/" },
                                        { name: "Quản lý ấn phẩm" },
                                        { name: "Quản lý nhận sách" },
                                        {
                                            name: "Đơn nhận",
                                            path: "/admin/quan-ly-an-pham/quan-ly-nhan-sach/don-nhan"
                                        },
                                        { name: "Chi tiết đơn nhận" },
                                    ],
                                },
                            },
                            {
                                path: "don-nhan/chi-tiet-don-nhan/:id_don_nhan/bien-muc/:id_sach",
                                name: "pageBienMucBieuGhiSach",
                                component: pageBienMucBieuGhiSach,
                                meta: {
                                    title: "Biên mục / Biểu ghi",
                                    breadcrumb: [
                                        { name: "Home", path: "/" },
                                        { name: "Quản lý ấn phẩm" },
                                        { name: "Quản lý nhận sách" },
                                        {
                                            name: "Đơn nhận",
                                            path: "/admin/quan-ly-an-pham/quan-ly-nhan-sach/don-nhan",
                                        },
                                        {
                                            name: "Chi tiết đơn nhận",
                                            path: route =>
                                                `/admin/quan-ly-an-pham/quan-ly-nhan-sach/don-nhan/chi-tiet-don-nhan/${route.params.id_don_nhan}`,
                                        },
                                        { name: "Biên mục" },
                                    ],
                                },
                            },
                            {
                                path: "in-nhan",
                                children: [
                                    {
                                        path: "dkcb",
                                        name: "pageInNhanDKCB",
                                        component: pageInNhanDKCB,
                                        meta: {
                                            title: "In nhãn DKCB",
                                            breadcrumb: [
                                                { name: "Home", path: "/" },
                                                { name: "Quản lý ấn phẩm" },
                                                { name: "In nhãn" },
                                                { name: "DKCB" },
                                            ],
                                        },
                                    },
                                    {
                                        path: "phan-loai",
                                        name: "pageInNhanPhanLoai",
                                        component: pageInNhanPhanLoai,
                                        meta: {
                                            title: "In nhãn phân loại",
                                            breadcrumb: [
                                                { name: "Home", path: "/" },
                                                { name: "Quản lý ấn phẩm" },
                                                { name: "In nhãn" },
                                                { name: "Phân loại" },
                                            ],
                                        },
                                    },
                                ],
                            },
                            {
                                path: "bao-cao-nhan-sach-phan-kho",
                                name: "pageBCNhanSachPhanKho",
                                component: pageBCNhanSachPhanKho,
                                meta: {
                                    title: "Báo cáo nhận sách phân kho",
                                    breadcrumb: [
                                        { name: "Home", path: "/" },
                                        { name: "Quản lý ấn phẩm" },
                                        { name: "Quản lý nhận sách" },
                                        { name: "Báo cáo nhận sách phân kho" },
                                    ],
                                },
                            },
                        ],
                    },
                ],
            },
            // Quản lý bạn đọc route
            {
                path: "quan-ly-ban-doc",
                children: [
                    {
                        path: "doc-gia",
                        name: "pageDocGia",
                        component: pageDocGia,
                        meta: {
                            title: "Quản lý bạn đọc",
                            breadcrumb: [
                                { name: "Home", path: "/" },
                                { name: "Quản lý bạn đọc" },
                                { name: "Danh sách bạn đọc" },
                            ],
                        },
                    },
                    {
                        path: "thong-ke-theo-doi-tuong",
                        name: "pageTKBanDocTheoDT",
                        component: pageTKBanDocTheoDT,
                        meta: {
                            title: "Thống kê bạn đọc theo đối tượng",
                            breadcrumb: [
                                { name: "Home", path: "/" },
                                { name: "Quản lý bạn đọc" },
                                { name: "Thống kê theo đối tượng" },
                            ],
                        },
                    },
                    {
                        path: "chi-tiet-doi-tuong/:ma_so_quy_uoc/:ten_doi_tuong",
                        name: "pageChiTietBanDocTheoDT",
                        component: pageChiTietBanDocTheoDT,
                        meta: {
                            title: "Chi tiết bạn đọc theo đối tượng",
                            breadcrumb: [
                                { name: "Home", path: "/" },
                                { name: "Quản lý bạn đọc" },
                                { name: "Thống kê theo đối tượng", path: "/admin/quan-ly-ban-doc/thong-ke-theo-doi-tuong" },
                                { name: "Chi tiết bạn đọc" },
                            ],
                        },
                    },
                ],
            },
            // Thêm mới phần Quản lý dịch vụ
            {
                path: "quan-ly-dich-vu",
                children: [
                    {
                        path: "quan-ly-luu-thong",
                        children: [
                            {
                                path: "muon-tai-lieu",
                                name: "pageMuonTaiLieu",
                                component: pageMuonTaiLieu,
                                meta: {
                                    title: "Mượn tài liệu",
                                    breadcrumb: [
                                        { name: "Home", path: "/" },
                                        { name: "Quản lý dịch vụ" },
                                        { name: "Quản lý lưu thông" },
                                        { name: "Mượn tài liệu" },
                                    ],
                                },
                            },
                            {
                                path: "doc-tai-cho",
                                name: "pageDocTaiCho",
                                component: pageDocTaiCho,
                                meta: {
                                    title: "Đọc tại chỗ",
                                    breadcrumb: [
                                        { name: "Home", path: "/" },
                                        { name: "Quản lý dịch vụ" },
                                        { name: "Quản lý lưu thông" },
                                        { name: "Đọc tại chỗ" },
                                    ],
                                },
                            },
                            {
                                path: "bao-cao-hoat-dong-tv",
                                name: "pageBaoCaoHoatDongTV",
                                component: pageBaoCaoHoatDongTV,
                                meta: {
                                    title: "Báo cáo hoạt động thư viện",
                                    breadcrumb: [
                                        { name: "Home", path: "/" },
                                        { name: "Quản lý dịch vụ" },
                                        { name: "Quản lý lưu thông" },
                                        { name: "Báo cáo hoạt động thư viện" },
                                    ],
                                },
                            },
                            {
                                path: "xu-ly-vi-pham",
                                name: "pageXuLyViPham",
                                component: pageXuLyViPham,
                                meta: {
                                    title: "Xử lý vi phạm",
                                    breadcrumb: [
                                        { name: "Home", path: "/" },
                                        { name: "Quản lý dịch vụ" },
                                        { name: "Quản lý lưu thông" },
                                        { name: "Xử lý vi phạm" },
                                    ],
                                },
                            },
                        ],
                    },
                    {
                        path: "thong-tin-opac",
                        children: [
                            {
                                path: "quan-ly-bai-viet",
                                name: "pageQuanLyBaiViet",
                                component: pageQuanLyBaiViet,
                                meta: {
                                    title: "Quản lý bài viết",
                                    breadcrumb: [
                                        { name: "Home", path: "/" },
                                        { name: "Quản lý dịch vụ" },
                                        { name: "Thông tin OPAC" },
                                        { name: "Quản lý bài viết" },
                                    ],
                                },
                            },
                        ],
                    },
                ],
            },
            {
                path: "kiem-ke-thanh-ly",
                children: [
                    {
                        path: "thiet-lap-kiem-ke",
                        name: "pageThietLapKiemKe",
                        component: pageThietLapKiemKe,
                        meta: {
                            title: "Thiết lập kiểm kê",
                            breadcrumb: [
                                { name: "Home", path: "/" },
                                { name: "Thanh lý kiểm kê" },
                                { name: "Thiết lập kiểm kê" },
                            ],
                        },
                    },
                    {
                        path: "thanh-ly",
                        name: "pageThanhLy",
                        component: pageThanhLy,
                        meta: {
                            title: "Thanh lý",
                            breadcrumb: [
                                { name: "Home", path: "/" },
                                { name: "Thanh lý kiểm kê" },
                                { name: "Thanh lý" },
                            ],
                        },
                    },
                ],
            },
        ],
    },
    {
        path: "/docgia",
        component: PublicLayout,
        children: [
            // ... existing routes ...
        ],
    },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

export default router;
