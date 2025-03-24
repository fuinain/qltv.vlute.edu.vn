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
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          <li class="nav-item">
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
      logoVLUTE: 'images/logoVLUTE.png',
    }
  }
}
</script>
