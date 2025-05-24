<template>
  <div class="opac-sidebar">
    <!-- Danh mục tài liệu -->
    <div class="card card-primary card-outline mb-4">
      <div class="card-header">
        <h3 class="card-title">
          <i class="fas fa-book-open mr-2"></i>Danh mục tài liệu
        </h3>
      </div>
      <div class="card-body p-0">
        <ul class="nav nav-pills flex-column">
          <li class="nav-item">
            <a href="#" class="nav-link">
              <i class="fas fa-book mr-2"></i>Sách giáo trình
            </a>
          </li>
          <li class="nav-item">
            <a href="#" class="nav-link">
              <i class="fas fa-graduation-cap mr-2"></i>Luận văn, luận án
            </a>
          </li>
          <li class="nav-item">
            <a href="#" class="nav-link">
              <i class="fas fa-newspaper mr-2"></i>Tạp chí
            </a>
          </li>
          <li class="nav-item">
            <a href="#" class="nav-link">
              <i class="fas fa-file-alt mr-2"></i>Báo cáo khoa học
            </a>
          </li>
          <li class="nav-item">
            <a href="#" class="nav-link">
              <i class="fas fa-file-pdf mr-2"></i>Tài liệu tham khảo
            </a>
          </li>
          <li class="nav-item">
            <a href="#" class="nav-link">
              <i class="fas fa-laptop mr-2"></i>Tài liệu điện tử
            </a>
          </li>
        </ul>
      </div>
    </div>
    
    <!-- Thống kê -->
    <div class="card card-info card-outline mb-4">
      <div class="card-header">
        <h3 class="card-title">
          <i class="fas fa-chart-bar mr-2"></i>Thống kê
        </h3>
      </div>
      <div class="card-body p-0">
        <ul class="list-group list-group-flush">
          <li class="list-group-item d-flex justify-content-between align-items-center">
            <span><i class="fas fa-book mr-2"></i>Tổng số đầu sách</span>
            <span class="badge badge-primary">9,500</span>
          </li>
          <li class="list-group-item d-flex justify-content-between align-items-center">
            <span><i class="fas fa-users mr-2"></i>Bạn đọc đã đăng ký</span>
            <span class="badge badge-primary">12,350</span>
          </li>
          <li class="list-group-item d-flex justify-content-between align-items-center">
            <span><i class="fas fa-clock mr-2"></i>Lượt truy cập</span>
            <span class="badge badge-primary">145,678</span>
          </li>
        </ul>
      </div>
    </div>
    
    <!-- Liên kết -->
    <div class="card card-success card-outline">
      <div class="card-header">
        <h3 class="card-title">
          <i class="fas fa-link mr-2"></i>Liên kết hữu ích
        </h3>
      </div>
      <div class="card-body">
        <div class="row link-list">
          <div class="col-6 text-center mb-3">
            <a href="https://press.vnu.edu.vn/" target="_blank" title="NXB Đại học Quốc gia Hà Nội" class="link-item">
              <div class="link-image-container">
                <img src="/images/image_link/dhqghn.png" alt="NXB ĐHQG Hà Nội">
              </div>
              <span class="link-title">NXB ĐHQG Hà Nội</span>
            </a>
          </div>
          <div class="col-6 text-center mb-3">
            <a href="https://www.nxbdhsp.edu.vn/trang-chu" target="_blank" title="NXB Đại học Sư phạm" class="link-item">
              <div class="link-image-container">
                <img src="/images/image_link/dhsp.png" alt="NXB ĐH Sư phạm">
              </div>
              <span class="link-title">NXB ĐH Sư phạm</span>
            </a>
          </div>
          <div class="col-6 text-center mb-3">
            <a href="https://www.nxbgd.vn/" target="_blank" title="NXB Giáo dục Việt Nam" class="link-item">
              <div class="link-image-container">
                <img src="/images/image_link/gdhcm.png" alt="NXB Giáo dục">
              </div>
              <span class="link-title">NXB Giáo dục</span>
            </a>
          </div>
          <div class="col-6 text-center mb-3">
            <a href="https://www.sgd.edu.vn/" target="_blank" title="Nhà Sách Giáo Dục" class="link-item">
              <div class="link-image-container">
                <img src="/images/image_link/gdvn.png" alt="Nhà Sách Giáo Dục">
              </div>
              <span class="link-title">Nhà Sách Giáo Dục</span>
            </a>
          </div>
          <div class="col-6 text-center mb-3">
            <a href="https://nxbvanhoc.com.vn/" target="_blank" title="NXB Văn Học" class="link-item">
              <div class="link-image-container">
                <img src="/images/image_link/vanhoc.png" alt="NXB Văn Học">
              </div>
              <span class="link-title">NXB Văn Học</span>
            </a>
          </div>
          <div class="col-6 text-center mb-3">
            <a href="https://nxbthongtintruyenthong.vn/" target="_blank" title="NXB Thông tin và Truyền thông" class="link-item">
              <div class="link-image-container">
                <img src="/images/image_link/vhtt.jpg" alt="NXB TT-TT">
              </div>
              <span class="link-title">NXB TT-TT</span>
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "OpacSidebar"
}
</script>

<style scoped>
.opac-sidebar {
  margin-bottom: 20px;
}

.link-image-container {
  width: 90px;
  height: 70px;
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto 8px;
  background-color: #f8f9fa;
  border: 1px solid #eee;
  border-radius: 6px;
  padding: 5px;
  overflow: hidden;
}

.link-image-container img {
  max-width: 100%;
  max-height: 100%;
  object-fit: contain;
}

.link-title {
  font-size: 12px;
  font-weight: 500;
  color: #007bff;
  display: block;
}

.link-item {
  display: inline-block;
  text-decoration: none;
  transition: all 0.2s;
}

.link-item:hover {
  transform: translateY(-3px);
  text-decoration: none;
}

.link-item:hover .link-title {
  color: #0056b3;
}
</style> 