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

    <!-- Menu chính - sử dụng các class của AdminLTE3 -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
      <div class="container-xl">
        <div class="navbar-content">
          <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarMain"
            aria-controls="navbarMain" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
          </button>
          <div class="collapse navbar-collapse" id="navbarMain">
            <ul class="navbar-nav mr-auto">
              <li class="nav-item">
                <router-link class="nav-link" :class="{ active: isActive('/') }" to="/">
                  <i class="fas fa-home mr-1"></i> TRANG CHỦ
                </router-link>
              </li>
              <li class="nav-item">
                <router-link class="nav-link" :class="{ active: isActive('/noi-quy') }" to="/noi-quy">
                  <i class="fas fa-book mr-1"></i> NỘI QUY
                </router-link>
              </li>
              <li class="nav-item">
                <router-link class="nav-link" :class="{ active: isActive('/huong-dan') }" to="/huong-dan">
                  <i class="fas fa-question-circle mr-1"></i> HƯỚNG DẪN
                </router-link>
              </li>
              <li class="nav-item">
                <router-link class="nav-link" :class="{ active: isActive('/lien-he') }" to="/lien-he">
                  <i class="fas fa-envelope mr-1"></i> LIÊN HỆ
                </router-link>
              </li>
            </ul>
            <div class="navbar-nav">
              <router-link to="/login" class="nav-link">
                <i class="fas fa-sign-in-alt mr-1"></i> ĐĂNG NHẬP
              </router-link>
            </div>
          </div>
        </div>
      </div>
    </nav>
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
  background-color: rgba(255, 255, 255, 0.1);
  max-width: 1140px;
  margin: 0 auto;
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

.navbar {
  padding: 0;
  border-radius: 0;
  background-color: #007bff;
  max-width: 1125px;
  margin: 0 auto;
}

.navbar-content {
  width: 100%;
}

.navbar .container-xl {
  padding-left: 0;
  padding-right: 0;
}

.navbar-nav .nav-link {
  padding: 12px 20px;
  font-weight: 500;
  font-size: 14px;
  letter-spacing: 0.5px;
  transition: all 0.3s ease;
}

.navbar-nav .nav-link:hover,
.navbar-nav .nav-link.active {
  background-color: rgba(255, 255, 255, 0.1);
}

@media (max-width: 768px) {
  .banner-img {
    max-height: 120px;
  }

  .navbar {
    padding: 0 7.5px 0 7.5px;
  }
}
</style>