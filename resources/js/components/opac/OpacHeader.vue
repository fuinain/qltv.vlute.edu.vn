<template>
  <div>
    <!-- Banner chính -->
    <div class="banner-container p-0">
      <div class="container-xl">
        <div class="banner-image-wrapper">
          <img :src="bannerUrl" alt="VLUTE Banner" class="banner-img">
        </div>
      </div>
    </div>

    <!-- Menu chính -->

    <div class="container-xl">
      <nav class="navbar navbar-expand-lg">
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarMain">
          <ul class="navbar-nav me-auto">
            <li class="nav-item">
              <router-link class="nav-link" :class="{ active: isActive('/') }" to="/">TRANG CHỦ</router-link>
            </li>
            <li class="nav-item">
              <router-link class="nav-link" :class="{ active: isActive('/gioi-thieu') }" to="/gioi-thieu">GIỚI
                THIỆU</router-link>
            </li>
            <li class="nav-item">
              <router-link class="nav-link" :class="{ active: isActive('/huong-dan') }" to="/huong-dan">HƯỚNG
                DẪN</router-link>
            </li>
            <li class="nav-item">
              <router-link class="nav-link" :class="{ active: isActive('/lien-he') }" to="/lien-he">LIÊN
                HỆ</router-link>
            </li>
          </ul>
          <div class="d-flex">
            <router-link to="/login" class="btn btn-sm btn-outline-light m-2">ĐĂNG NHẬP</router-link>
          </div>
        </div>
      </nav>
    </div>
  </div>
</template>

<script>
export default {
  name: "OpacHeader",
  data() {
    return {
      logoUrl: '/images/logoVLUTE.png',
      bannerUrl: '/images/banner/banner_vlute.jpg'
    }
  },
  methods: {
    isActive(routePath) {
      return this.$route.path === routePath;
    }
  }
}
</script>

<style scoped>
.banner-container {
  background-color: #fff;
}

.banner-image-wrapper {
  width: 100%;
  display: flex;
  justify-content: center;
  align-items: center;
  overflow: hidden;
  padding: 0;
  margin: 0;
}

.banner-img {
  width: 100%;
  height: auto;
  max-height: 180px;
  object-fit: contain;
  display: block;
}

.nav-bg-container {
  background-color: #0d6efd;
}

.navbar {
  background-color: #0d6efd;
  padding: 0;
  border-radius: 0;
}

.navbar-nav .nav-link {
  padding: 12px 20px;
  font-weight: 500;
  font-size: 14px;
  letter-spacing: 0.5px;
  transition: all 0.3s ease;
  color: white;
}

.navbar-nav .nav-link:hover,
.navbar-nav .nav-link.active {
  background-color: rgba(255, 255, 255, 0.1);
  color: white;
}


.btn-outline-light:hover {
  background-color: white;
  color: #0d6efd;
}

@media (max-width: 768px) {
  .banner-img {
    max-height: 120px;
  }
}
</style>