<template>
  <!-- Navbar -->
  <nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
      </li>
      <li class="nav-item d-none d-sm-inline-block">
        <router-link to="/" class="nav-link">Home</router-link>
      </li>
    </ul>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
      <!-- Navbar Search -->
      <li class="nav-item">
        <a class="nav-link" data-widget="navbar-search" href="#" role="button">
          <i class="fas fa-search"></i>
        </a>
        <div class="navbar-search-block">
          <form class="form-inline">
            <div class="input-group input-group-sm">
              <input class="form-control form-control-navbar" type="search" placeholder="Search" aria-label="Search">
              <div class="input-group-append">
                <button class="btn btn-navbar" type="submit">
                  <i class="fas fa-search"></i>
                </button>
                <button class="btn btn-navbar" type="button" data-widget="navbar-search">
                  <i class="fas fa-times"></i>
                </button>
              </div>
            </div>
          </form>
        </div>
      </li>
      <!-- Notifications Dropdown Menu -->
      <li class="nav-item">
        <a class="nav-link" data-widget="fullscreen" href="#" role="button">
          <i class="fas fa-expand-arrows-alt"></i>
        </a>
      </li>
      <!-- Navbar user -->
      <li class="nav-item dropdown user-menu">
        <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown">
          <i class="fas fa-user"></i> &nbsp;
          <span class="hidden-xs">{{ hoTen }}</span>
        </a>
        <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
          <li class="user-header" style="height: 100px;">
            <p>
              {{ hoTen }}
              <small>{{ email }}</small>
            </p>
          </li>
          <li class="user-footer">
            <div style="float: left">
              <a :href="routeThayDoiMatKhau" class="btn btn-primary">Đổi mật khẩu</a>
            </div>
            <div style="float: right">
              <a :href="routeLogout" class="btn btn-danger">Đăng xuất</a>
            </div>
          </li>
        </ul>
      </li>
    </ul>
  </nav>
  <!-- /.navbar -->
</template>
<script>
export default {
  name: "Topbar",
  data() {
    return {
      hoTen: '',
      email: '',
      routeThayDoiMatKhau: '/thayDoiMatKhau',
      routeLogout: '/logout'
    }
  },
  mounted() {
    this.getUserInfo();
  },
  methods: {
    getUserInfo() {
      axios.get('/api/user-info')
        .then(response => {
          const data = response.data;
          this.hoTen = data.hoTen;
          this.email = data.email;
          this.quyen = data.quyen;
        })
        .catch(error => {
          console.error('Lỗi:', error);
        });
    }
  }
}
</script>