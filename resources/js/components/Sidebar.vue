<template>
  <!-- Main Sidebar Container -->
  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <router-link to="/" class="brand-link d-flex justify-between">
      <img :src="logoVLUTE" alt="VLUTE Logo" class="brand-image img-circle">
      <span class="brand-text font-weight-light font-weight-bold align-middle"
        >Quản Lý Thư Viện</span
      >
    </router-link>

    <div class="sidebar">
      <!-- Sidebar Menu -->
      <nav class="mt-2">
        <ul
          class="nav nav-pills nav-sidebar flex-column"
          data-widget="treeview"
          role="menu"
          data-accordion="false"
        >
          <!-- DANH MỤC (menu cấp 1) -->
          <li class="nav-item">
            <a href="#" class="nav-link font-weight-bold">
              <i class="nav-icon fas fa-layer-group"></i>
              <p>
                DANH MỤC
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <!-- Menu cấp 2 thuộc DANH MỤC -->
            <ul class="nav nav-treeview" style="display: none;">
              <li class="nav-item">
                <a href="#" class="nav-link">
                  <!-- Icon cho menu cấp 2 -->
                  <i class="nav-icon fas fa-circle"></i>
                  <p>
                    Thông tin chung
                    <i class="fas fa-angle-left right"></i>
                  </p>
                </a>
                <!-- Menu cấp 3 thuộc Thông tin chung -->
                <ul class="nav nav-treeview" style="display: none;">
                  <li class="nav-item">
                    <a href="#" class="nav-link">
                      <!-- Icon cho menu cấp 3 -->
                      <i class="far fa-circle nav-icon"></i>
                      <p>Danh mục phòng/Khoa</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="#" class="nav-link">
                      <i class="far fa-circle nav-icon"></i>
                      <p>Danh mục chức vụ</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="#" class="nav-link">
                      <i class="far fa-circle nav-icon"></i>
                      <p>Danh mục năm học</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="#" class="nav-link">
                      <i class="far fa-circle nav-icon"></i>
                      <p>Danh mục Lớp học</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="#" class="nav-link">
                      <i class="far fa-circle nav-icon"></i>
                      <p>Danh mục chuyên ngành</p>
                    </a>
                  </li>
                </ul>
              </li>
              <li class="nav-item">
                <a href="#" class="nav-link">
                  <i class="nav-icon fas fa-circle"></i>
                  <p>
                    Nghiệp vụ biên mục
                    <i class="fas fa-angle-left right"></i>
                  </p>
                </a>
                <ul class="nav nav-treeview" style="display: none;">
                  <li class="nav-item">
                    <a href="#" class="nav-link">
                      <i class="far fa-circle nav-icon"></i>
                      <p>Cấu hình thư mục và loại tài liệu</p>
                    </a>
                  </li>
                </ul>
              </li>
              <li class="nav-item">
                <a href="#" class="nav-link">
                  <i class="nav-icon fas fa-circle"></i>
                  <p>
                    Nghiệp vụ lưu thông
                    <i class="fas fa-angle-left right"></i>
                  </p>
                </a>
                <ul class="nav nav-treeview" style="display: none;">
                  <li class="nav-item">
                    <a href="#" class="nav-link">
                      <i class="far fa-circle nav-icon"></i>
                      <p>Khai báo đối tượng bạn đọc</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="#" class="nav-link">
                      <i class="far fa-circle nav-icon"></i>
                      <p>Khai báo điểm lưu thông</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="#" class="nav-link">
                      <i class="far fa-circle nav-icon"></i>
                      <p>Khai báo tham số lưu thông</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="#" class="nav-link">
                      <i class="far fa-circle nav-icon"></i>
                      <p>Khai báo danh mục phạt bạn đọc</p>
                    </a>
                  </li>
                </ul>
              </li>
              <li class="nav-item">
                <a href="#" class="nav-link">
                  <i class="nav-icon fas fa-circle"></i>
                  <p>
                    Quản trị hệ thống
                    <i class="fas fa-angle-left right"></i>
                  </p>
                </a>
                <ul class="nav nav-treeview" style="display: none;">
                  <li class="nav-item">
                    <a href="#" class="nav-link">
                      <i class="far fa-circle nav-icon"></i>
                      <p>Khai báo người dùng</p>
                    </a>
                  </li>
                </ul>
              </li>
            </ul>
          </li>

          <!-- QL ẤN PHẨM (menu cấp 1) -->
          <li class="nav-item">
            <a href="#" class="nav-link font-weight-bold">
              <i class="nav-icon fas fa-feather-pointed"></i>
              <p>
                QL ẤN PHẨM
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <!-- Menu cấp 2 thuộc QL ẤN PHẨM -->
            <ul class="nav nav-treeview" style="display: none;">
              <li class="nav-item">
                <a href="#" class="nav-link">
                  <i class="nav-icon fas fa-circle"></i>
                  <p>
                    Quản lý nhận sách
                    <i class="fas fa-angle-left right"></i>
                  </p>
                </a>
                <!-- Menu cấp 3 -->
                <ul class="nav nav-treeview" style="display: none;">
                  <li class="nav-item">
                    <a href="#" class="nav-link">
                      <i class="far fa-circle nav-icon"></i>
                      <p>Quản lý đơn nhận</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="#" class="nav-link">
                      <i class="far fa-circle nav-icon"></i>
                      <p>Báo cáo nhận sách - phân kho</p>
                    </a>
                  </li>
                </ul>
              </li>
              <li class="nav-item">
                <a href="#" class="nav-link">
                  <i class="nav-icon fas fa-circle"></i>
                  <p>
                    In nhãn
                    <i class="fas fa-angle-left right"></i>
                  </p>
                </a>
                <ul class="nav nav-treeview" style="display: none;">
                  <li class="nav-item">
                    <a href="#" class="nav-link">
                      <i class="far fa-circle nav-icon"></i>
                      <p>In nhãn phân loại</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="#" class="nav-link">
                      <i class="far fa-circle nav-icon"></i>
                      <p>In nhãn Đăng ký cá biệt</p>
                    </a>
                  </li>
                </ul>
              </li>
              <li class="nav-item">
                <a href="#" class="nav-link">
                  <i class="nav-icon fas fa-circle"></i>
                  <p>
                    Quản lý kho ấn phẩm
                    <i class="fas fa-angle-left right"></i>
                  </p>
                </a>
                <ul class="nav nav-treeview" style="display: none;">
                  <li class="nav-item">
                    <a href="#" class="nav-link">
                      <i class="far fa-circle nav-icon"></i>
                      <p>Danh mục kho ấn phẩm</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="#" class="nav-link">
                      <i class="far fa-circle nav-icon"></i>
                      <p>Thống kê kho ấn phẩm</p>
                    </a>
                  </li>
                </ul>
              </li>
            </ul>
          </li>

          <!-- QL BẠN ĐỌC (menu cấp 1) -->
          <li class="nav-item">
            <a href="#" class="nav-link font-weight-bold">
              <i class="nav-icon fas fa-book-open-reader"></i>
              <p>
                QL BẠN ĐỌC
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <!-- Menu cấp 2 -->
            <ul class="nav nav-treeview" style="display: none;">
              <li class="nav-item">
                <a href="#" class="nav-link">
                  <i class="nav-icon fas fa-circle"></i>
                  <p>Nhập mới bạn đọc</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="#" class="nav-link">
                  <i class="nav-icon fas fa-circle"></i>
                  <p>Tra cứu cập nhật hồ sơ bạn đọc</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="#" class="nav-link">
                  <i class="nav-icon fas fa-circle"></i>
                  <p>Thống kê bạn đọc theo đối tượng</p>
                </a>
              </li>
            </ul>
          </li>

          <!-- QL DỊCH VỤ (menu cấp 1) -->
          <li class="nav-item">
            <a href="#" class="nav-link font-weight-bold">
              <i class="nav-icon fas fa-gears"></i>
              <p>
                QL DỊCH VỤ
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <!-- Menu cấp 2 -->
            <ul class="nav nav-treeview" style="display: none;">
              <li class="nav-item">
                <a href="#" class="nav-link">
                  <i class="nav-icon fas fa-circle"></i>
                  <p>Mượn tài liệu về nhà</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="#" class="nav-link">
                  <i class="nav-icon fas fa-circle"></i>
                  <p>Mượn tài liệu đọc tại chỗ</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="#" class="nav-link">
                  <i class="nav-icon fas fa-circle"></i>
                  <p>Quản lý tin tức-bài viết</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="#" class="nav-link">
                  <i class="nav-icon fas fa-circle"></i>
                  <p>Thống kê truy cập OPAC</p>
                </a>
              </li>
            </ul>
          </li>

          <!-- KIỂM KÊ - THANH LÝ (menu cấp 1) -->
          <li class="nav-item">
            <a href="#" class="nav-link font-weight-bold">
              <i class="nav-icon fas fa-clipboard-list"></i>
              <p>
                KIỂM KÊ THANH LÝ
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <!-- Menu cấp 2 -->
            <ul class="nav nav-treeview" style="display: none;">
              <li class="nav-item">
                <a href="#" class="nav-link">
                  <i class="nav-icon fas fa-circle"></i>
                  <p>
                    Kiểm kê
                    <i class="fas fa-angle-left right"></i>
                  </p>
                </a>
                <!-- Menu cấp 3 -->
                <ul class="nav nav-treeview" style="display: none;">
                  <li class="nav-item">
                    <a href="#" class="nav-link">
                      <i class="far fa-circle nav-icon"></i>
                      <p>Thiết lập kiểm kê</p>
                    </a>
                  </li>
                </ul>
              </li>
              <li class="nav-item">
                <a href="#" class="nav-link">
                  <i class="nav-icon fas fa-circle"></i>
                  <p>
                    Thanh lý
                    <i class="fas fa-angle-left right"></i>
                  </p>
                </a>
                <ul class="nav nav-treeview" style="display: none;">
                  <li class="nav-item">
                    <a href="#" class="nav-link">
                      <i class="far fa-circle nav-icon"></i>
                      <p>Quản lý danh mục thanh lý</p>
                    </a>
                  </li>
                </ul>
              </li>
            </ul>
          </li>
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
      logoVLUTE: "images/logoVLUTE.png",
    };
  },
};
</script>


<!-- <li class="nav-item">
            <router-link to="/categories" class="nav-link">
              <i class="far fa-circle nav-icon"></i>
              <p>QL Danh mục</p>
            </router-link>
          </li>
          <li class="nav-item">
            <router-link to="/readers" class="nav-link">
              <i class="far fa-circle nav-icon"></i>
              <p>QL Bạn đọc</p>
            </router-link>
          </li>
          <li class="nav-item">
            <router-link to="/services" class="nav-link">
              <i class="far fa-circle nav-icon"></i>
              <p>QL Dịch vụ</p>
            </router-link>
          </li>
          <li class="nav-item">
            <router-link to="/liquidation" class="nav-link">
              <i class="far fa-circle nav-icon"></i>
              <p>QL Thanh lý</p>
            </router-link>
          </li>
          <li class="nav-item">
            <router-link to="/account" class="nav-link">
              <i class="far fa-circle nav-icon"></i>
              <p>QL Tài khoản</p>
            </router-link>
          </li> -->