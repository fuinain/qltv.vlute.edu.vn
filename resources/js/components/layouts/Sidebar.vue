<template>
    <!-- Main Sidebar Container -->
    <aside class="main-sidebar sidebar-dark-primary elevation-4">
        <!-- Brand Logo -->
        <router-link to="/" class="brand-link d-flex justify-between">
            <img :src="logoVLUTE" alt="VLUTE Logo" class="brand-image img-circle">
            <span class="brand-text font-weight-light font-weight-bold align-middle">Quản Lý Thư Viện</span>
        </router-link>

        <div class="sidebar">
            <!-- Sidebar Menu -->
            <nav class="mt-2">
                <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu"
                    data-accordion="false">
                    <!-- DANH MỤC (menu cấp 1) -->
                    <li class="nav-item has-treeview mb-2"> <!-- Thêm has-treeview nếu cần thiết cho JS -->
                        <a href="#" class="nav-link font-weight-bold">
                            <i class="nav-icon fas fa-layer-group"></i>
                            <p>
                                DANH MỤC
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <!-- Menu cấp 2 thuộc DANH MỤC -->
                        <ul class="nav nav-treeview">
                            <li class="nav-item has-treeview">
                                <a href="#" class="nav-link">
                                    <!-- Icon cấp 2: Đổi thành far fa-circle -->
                                    <i class="nav-icon far fa-circle"></i>
                                    <p>
                                        Thông tin chung
                                        <i class="fas fa-angle-left right"></i>
                                    </p>
                                </a>
                                <!-- Menu cấp 3 thuộc Thông tin chung -->
                                <ul class="nav nav-treeview">
                                    <li class="nav-item">
                                        <router-link :to="{ name: 'pagePhongKhoa' }" class="nav-link">
                                            <!-- Icon cấp 3: Đổi thành fas fa-angle-right -->
                                            <i class="fas fa-angle-right nav-icon"></i>
                                            <p>Danh mục phòng/Khoa</p>
                                        </router-link>
                                    </li>
                                    <li class="nav-item">
                                        <router-link :to="{ name: 'pageGiangVien' }" class="nav-link">
                                            <i class="fas fa-angle-right nav-icon"></i>
                                            <p>Danh mục Giảng viên</p>
                                        </router-link>
                                    </li>
                                    <li class="nav-item">
                                        <router-link :to="{ name: 'pageChucVu' }" class="nav-link">
                                            <i class="fas fa-angle-right nav-icon"></i>
                                            <p>Danh mục chức vụ</p>
                                        </router-link>
                                    </li>
                                    <li class="nav-item">
                                        <router-link :to="{ name: 'pageNamHoc' }" class="nav-link">
                                            <i class="fas fa-angle-right nav-icon"></i>
                                            <p>Danh mục năm học</p>
                                        </router-link>
                                    </li>
                                    <li class="nav-item">
                                        <router-link :to="{ name: 'pageLopHoc' }" class="nav-link">
                                            <i class="fas fa-angle-right nav-icon"></i>
                                            <p>Danh mục Lớp học</p>
                                        </router-link>
                                    </li>
                                    <li class="nav-item">
                                        <router-link :to="{ name: 'pageChuyenNganh' }" class="nav-link">
                                            <i class="fas fa-angle-right nav-icon"></i>
                                            <p>Danh mục chuyên ngành</p>
                                        </router-link>
                                    </li>
                                </ul>
                            </li>
                            <li class="nav-item has-treeview">
                                <a href="#" class="nav-link">
                                    <i class="nav-icon far fa-circle"></i> <!-- Icon cấp 2 -->
                                    <p>
                                        Nghiệp vụ bổ sung
                                        <i class="fas fa-angle-left right"></i>
                                    </p>
                                </a>
                                <ul class="nav nav-treeview">
                                    <li class="nav-item">
                                        <router-link :to="{ name: 'pageNCC' }" class="nav-link">
                                            <i class="fas fa-angle-right nav-icon"></i> <!-- Icon cấp 3 -->
                                            <p>Nhà cung cấp</p>
                                        </router-link>
                                    </li>
                                    <li class="nav-item">
                                        <router-link :to="{ name: 'pageTrangThaiDon' }" class="nav-link">
                                            <i class="fas fa-angle-right nav-icon"></i> <!-- Icon cấp 3 -->
                                            <p>Trạng thái đơn</p>
                                        </router-link>
                                    </li>
                                    <li class="nav-item">
                                        <router-link :to="{ name: 'pageNguonNhan' }" class="nav-link">
                                            <i class="fas fa-angle-right nav-icon"></i> <!-- Icon cấp 3 -->
                                            <p>Nguồn nhận</p>
                                        </router-link>
                                    </li>
                                    <li class="nav-item">
                                        <router-link :to="{ name: 'pageLoaiNhap' }" class="nav-link">
                                            <i class="fas fa-angle-right nav-icon"></i> <!-- Icon cấp 3 -->
                                            <p>Loại nhập</p>
                                        </router-link>
                                    </li>
                                    <li class="nav-item">
                                        <router-link :to="{ name: 'pageDMBaoCao' }" class="nav-link">
                                            <i class="fas fa-angle-right nav-icon"></i> <!-- Icon cấp 3 -->
                                            <p>Báo cáo</p>
                                        </router-link>
                                    </li>
                                </ul>
                            </li>
                            <li class="nav-item has-treeview">
                                <a href="#" class="nav-link">
                                    <i class="nav-icon far fa-circle"></i> <!-- Icon cấp 2 -->
                                    <p>
                                        Nghiệp vụ biên mục
                                        <i class="fas fa-angle-left right"></i>
                                    </p>
                                </a>
                                <ul class="nav nav-treeview">
                                    <li class="nav-item">
                                        <router-link :to="{ name: 'pageTaiLieuThuMuc' }" class="nav-link">
                                            <i class="fas fa-angle-right nav-icon"></i> <!-- Icon cấp 3 -->
                                            <p>Cấu hình thư mục và loại tài liệu</p>
                                        </router-link>
                                    </li>
                                </ul>
                            </li>
                            <li class="nav-item has-treeview">
                                <a href="#" class="nav-link">
                                    <i class="nav-icon far fa-circle"></i> <!-- Icon cấp 2 -->
                                    <p>
                                        Nghiệp vụ lưu thông
                                        <i class="fas fa-angle-left right"></i>
                                    </p>
                                </a>
                                <ul class="nav nav-treeview">
                                    <li class="nav-item">
                                        <router-link :to="{ name: 'pageDoiTuongBanDoc' }" class="nav-link">
                                            <i class="fas fa-angle-right nav-icon"></i> <!-- Icon cấp 3 -->
                                            <p>Khai báo đối tượng bạn đọc</p>
                                        </router-link>
                                    </li>
                                    <li class="nav-item">
                                        <router-link :to="{ name: 'pageDiemLuuThong' }" class="nav-link">
                                            <i class="fas fa-angle-right nav-icon"></i> <!-- Icon cấp 3 -->
                                            <p>Khai báo điểm lưu thông</p>
                                        </router-link>
                                    </li>
                                    <li class="nav-item">
                                        <router-link :to="{ name: 'pageThamSoLuuThong' }" class="nav-link">
                                            <i class="fas fa-angle-right nav-icon"></i> <!-- Icon cấp 3 -->
                                            <p>Khai báo tham số lưu thông</p>
                                        </router-link>
                                    </li>
                                    <li class="nav-item">
                                        <router-link :to="{ name: 'pagePhatBanDoc' }" class="nav-link">
                                            <i class="fas fa-angle-right nav-icon"></i> <!-- Icon cấp 3 -->
                                            <p>Khai báo danh mục phạt bạn đọc</p>
                                        </router-link>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </li>

                    <!-- QL ẤN PHẨM (menu cấp 1) -->
                    <li class="nav-item has-treeview mb-2">
                        <a href="#" class="nav-link font-weight-bold">
                            <i class="nav-icon fas fa-feather-pointed"></i>
                            <p>
                                QL ẤN PHẨM
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <!-- Menu cấp 2 -->
                        <ul class="nav nav-treeview">
                            <li class="nav-item has-treeview">
                                <a href="#" class="nav-link">
                                    <i class="nav-icon far fa-circle"></i> <!-- Icon cấp 2 -->
                                    <p>
                                        Quản lý nhận sách
                                        <i class="fas fa-angle-left right"></i>
                                    </p>
                                </a>
                                <ul class="nav nav-treeview">
                                    <li class="nav-item">
                                        <router-link :to="{ name: 'pageDonNhan' }" class="nav-link">
                                            <i class="fas fa-angle-right nav-icon"></i> <!-- Icon cấp 3 -->
                                            <p>Quản lý đơn nhận</p>
                                        </router-link>
                                    </li>
                                    <li class="nav-item">
                                        <router-link :to="{ name: 'pageBCNhanSachPhanKho' }" class="nav-link">
                                            <i class="fas fa-angle-right nav-icon"></i> <!-- Icon cấp 3 -->
                                            <p>Báo cáo nhận sách - phân kho</p>
                                        </router-link>
                                    </li>
                                </ul>
                            </li>
                            <li class="nav-item has-treeview">
                                <a href="#" class="nav-link">
                                    <i class="nav-icon far fa-circle"></i> <!-- Icon cấp 2 -->
                                    <p>
                                        In nhãn
                                        <i class="fas fa-angle-left right"></i>
                                    </p>
                                </a>
                                <ul class="nav nav-treeview">
                                    <li class="nav-item">
                                        <router-link :to="{ name: 'pageInNhanPhanLoai' }" class="nav-link">
                                            <i class="fas fa-angle-right nav-icon"></i> <!-- Icon cấp 3 -->
                                            <p>In nhãn phân loại</p>
                                        </router-link>
                                    </li>
                                    <li class="nav-item">
                                        <router-link :to="{ name: 'pageInNhanDKCB' }" class="nav-link">
                                            <i class="fas fa-angle-right nav-icon"></i> <!-- Icon cấp 3 -->
                                            <p>In nhãn Đăng ký cá biệt</p>
                                        </router-link>
                                    </li>
                                </ul>
                            </li>
                            <li class="nav-item has-treeview">
                                <a href="#" class="nav-link">
                                    <i class="nav-icon far fa-circle"></i> <!-- Icon cấp 2 -->
                                    <p>
                                        Quản lý kho ấn phẩm
                                        <i class="fas fa-angle-left right"></i>
                                    </p>
                                </a>
                                <ul class="nav nav-treeview">
                                    <li class="nav-item">
                                        <router-link :to="{ name: 'pageDMKhoAnPham' }" class="nav-link">
                                            <i class="fas fa-angle-right nav-icon"></i> <!-- Icon cấp 3 -->
                                            <p>Danh mục kho ấn phẩm</p>
                                        </router-link>
                                    </li>
                                    <li class="nav-item">
                                        <a href="#" class="nav-link">
                                            <i class="fas fa-angle-right nav-icon"></i> <!-- Icon cấp 3 -->
                                            <p>Thống kê kho ấn phẩm</p>
                                        </a>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </li>

                    <!-- QL BẠN ĐỌC (menu cấp 1) -->
                    <li class="nav-item has-treeview mb-2">
                        <a href="#" class="nav-link font-weight-bold">
                            <i class="nav-icon fas fa-book-open-reader"></i>
                            <p>
                                QL BẠN ĐỌC
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <!-- Menu cấp 2 -->
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <router-link :to="{ name: 'pageDocGia' }" class="nav-link">
                                    <i class="nav-icon far fa-circle"></i> <!-- Icon cấp 2 (không có cấp 3) -->
                                    <p>Danh sách bạn đọc</p>
                                </router-link>
                            </li>
                            <li class="nav-item">
                                <router-link :to="{ name: 'pageTKBanDocTheoDT' }" class="nav-link">
                                    <i class="nav-icon far fa-circle"></i> <!-- Icon cấp 2 -->
                                    <p>Thống kê bạn đọc theo đối tượng</p>
                                </router-link>
                            </li>
                        </ul>
                    </li>

                    <!-- QL DỊCH VỤ (menu cấp 1) -->
                    <li class="nav-item has-treeview mb-2">
                        <a href="#" class="nav-link font-weight-bold">
                            <i class="nav-icon fas fa-gears"></i>
                            <p>
                                QL DỊCH VỤ
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <!-- Menu cấp 2 -->

                        <ul class="nav nav-treeview">
                            <li class="nav-item has-treeview">
                                <a href="#" class="nav-link">
                                    <i class="nav-icon far fa-circle"></i> <!-- Icon cấp 2 -->
                                    <p>
                                        Quản lý lưu thông TL
                                        <i class="fas fa-angle-left right"></i>
                                    </p>
                                </a>
                                <ul class="nav nav-treeview">
                                    <li class="nav-item">
                                        <router-link :to="{ name: 'pageMuonTaiLieu' }" class="nav-link">
                                            <i class="fas fa-angle-right nav-icon"></i> <!-- Icon cấp 2 -->
                                            <p>Quản lý mượn tài liệu</p>
                                        </router-link>
                                    </li>
                                    <li class="nav-item">
                                        <router-link :to="{ name: 'pageDocTaiCho' }" class="nav-link">
                                            <i class="fas fa-angle-right nav-icon"></i> <!-- Icon cấp 2 -->
                                            <p>Quản lý đọc tại chổ</p>
                                        </router-link>
                                    </li>
                                    <li class="nav-item">
                                        <router-link :to="{ name: 'pageXuLyViPham' }" class="nav-link">
                                            <i class="fas fa-angle-right nav-icon"></i> <!-- Icon cấp 2 -->
                                            <p>Xử lý vi phạm</p>
                                        </router-link>
                                    </li>
                                    <li class="nav-item">
                                        <router-link :to="{ name: 'pageBaoCaoHoatDongTV' }" class="nav-link">
                                            <i class="fas fa-angle-right nav-icon"></i> <!-- Icon cấp 2 -->
                                            <p>Báo cáo hoạt động</p>
                                        </router-link>
                                    </li>
                                </ul>
                            </li>
                            <li class="nav-item has-treeview">
                                <a href="#" class="nav-link">
                                    <i class="nav-icon far fa-circle"></i> <!-- Icon cấp 2 -->
                                    <p>
                                        Thông tin OPAC
                                        <i class="fas fa-angle-left right"></i>
                                    </p>
                                </a>
                                <ul class="nav nav-treeview">
                                    <li class="nav-item">
                                        <router-link :to="{ name: 'pageQuanLyBaiViet' }" class="nav-link">
                                            <i class="fas fa-angle-right nav-icon"></i> <!-- Icon cấp 2 -->
                                            <p>Quản lý tin tức-bài viết</p>
                                        </router-link>
                                    </li>                              
                                </ul>
                            </li>
                        </ul>
                    </li>

                    <!-- KIỂM KÊ - THANH LÝ (menu cấp 1) -->
                    
                </ul>
            </nav>
        </div>
    </aside>
</template>

<script>
export default {
    name: "Sidebar",
    data() {
        return {
            logoVLUTE: "/images/logoVLUTE.png", // Đảm bảo đường dẫn này đúng
        };
    },
};
</script>

