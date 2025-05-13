<template>
  <div class="opac-sidebar">
    <!-- Danh mục tài liệu -->
    <div class="sidebar-card mb-4">
      <div class="sidebar-header">
        <h5 class="mb-0"><i class="bi bi-journal-bookmark me-2"></i>Danh mục tài liệu</h5>
      </div>
      <div class="sidebar-body">
        <ul class="sidebar-menu">
          <li>
            <a href="#"><i class="bi bi-book-half me-2"></i>Sách giáo trình</a>
          </li>
          <li>
            <a href="#"><i class="bi bi-journal-text me-2"></i>Luận văn, luận án</a>
          </li>
          <li>
            <a href="#"><i class="bi bi-journal me-2"></i>Tạp chí</a>
          </li>
          <li>
            <a href="#"><i class="bi bi-journal-album me-2"></i>Báo cáo khoa học</a>
          </li>
          <li>
            <a href="#"><i class="bi bi-file-earmark-text me-2"></i>Tài liệu tham khảo</a>
          </li>
          <li>
            <a href="#"><i class="bi bi-laptop me-2"></i>Tài liệu điện tử</a>
          </li>
        </ul>
      </div>
    </div>
    
    <!-- Thống kê -->
    <div class="sidebar-card mb-4">
      <div class="sidebar-header">
        <h5 class="mb-0"><i class="bi bi-bar-chart-line me-2"></i>Thống kê</h5>
      </div>
      <div class="sidebar-body">
        <ul class="stat-list">
          <li>
            <i class="bi bi-book me-2"></i>Tổng số đầu sách: <span class="badge">9,500</span>
          </li>
          <li>
            <i class="bi bi-person me-2"></i>Bạn đọc đã đăng ký: <span class="badge">12,350</span>
          </li>
          <li>
            <i class="bi bi-clock-history me-2"></i>Lượt truy cập: <span class="badge">145,678</span>
          </li>
        </ul>
      </div>
    </div>
    
    <!-- Liên kết -->
    <div class="sidebar-card">
      <div class="sidebar-header">
        <h5 class="mb-0"><i class="bi bi-link-45deg me-2"></i>Liên kết hữu ích</h5>
      </div>
      <div class="sidebar-body">
        <div class="link-list">
          <div class="link-item">
            <a href="https://press.vnu.edu.vn/" target="_blank" title="NXB Đại học Quốc gia Hà Nội">
              <div class="link-image-container">
                <img src="/images/image_link/dhqghn.png" alt="NXB ĐHQG Hà Nội">
              </div>
              <span class="link-title">NXB ĐHQG Hà Nội</span>
            </a>
          </div>
          <div class="link-item">
            <a href="https://www.nxbdhsp.edu.vn/trang-chu" target="_blank" title="NXB Đại học Sư phạm">
              <div class="link-image-container">
                <img src="/images/image_link/dhsp.png" alt="NXB ĐH Sư phạm">
              </div>
              <span class="link-title">NXB ĐH Sư phạm</span>
            </a>
          </div>
          <div class="link-item">
            <a href="https://www.nxbgd.vn/" target="_blank" title="NXB Giáo dục Việt Nam">
              <div class="link-image-container">
                <img src="/images/image_link/gdhcm.png" alt="NXB Giáo dục">
              </div>
              <span class="link-title">NXB Giáo dục</span>
            </a>
          </div>
          <div class="link-item">
            <a href="https://www.sgd.edu.vn/" target="_blank" title="Nhà Sách Giáo Dục">
              <div class="link-image-container">
                <img src="/images/image_link/gdvn.png" alt="Nhà Sách Giáo Dục">
              </div>
              <span class="link-title">Nhà Sách Giáo Dục</span>
            </a>
          </div>
          <div class="link-item">
            <a href="https://nxbvanhoc.com.vn/" target="_blank" title="NXB Văn Học">
              <div class="link-image-container">
                <img src="/images/image_link/vanhoc.png" alt="NXB Văn Học">
              </div>
              <span class="link-title">NXB Văn Học</span>
            </a>
          </div>
          <div class="link-item">
            <a href="https://nxbthongtintruyenthong.vn/" target="_blank" title="NXB Thông tin và Truyền thông">
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

.sidebar-card {
  border: 1px solid #ddd;
  border-radius: 5px;
  overflow: hidden;
  box-shadow: 0 2px 4px rgba(0,0,0,0.05);
}

.sidebar-header {
  background-color: #2980b9;
  color: white;
  padding: 12px 15px;
  border-bottom: 1px solid rgba(0,0,0,0.1);
}

.sidebar-header h5 {
  font-size: 16px;
  font-weight: 600;
}

.sidebar-body {
  background-color: #fff;
}

.sidebar-menu {
  list-style: none;
  margin: 0;
  padding: 0;
}

.sidebar-menu li {
  border-bottom: 1px solid #eee;
}

.sidebar-menu li:last-child {
  border-bottom: none;
}

.sidebar-menu li a {
  display: block;
  padding: 10px 15px;
  color: #333;
  text-decoration: none;
  transition: all 0.2s;
}

.sidebar-menu li a:hover {
  background-color: #f5f9ff;
  color: #2980b9;
}

.stat-list {
  list-style: none;
  margin: 0;
  padding: 10px 15px;
}

.stat-list li {
  padding: 8px 0;
  color: #444;
  font-size: 14px;
}

.stat-list .badge {
  background-color: #f5f5f5;
  color: #3498db;
  padding: 4px 8px;
  font-weight: 600;
  border-radius: 4px;
  float: right;
}

/* Styles cho phần liên kết */
.link-list {
  display: flex;
  flex-wrap: wrap;
  padding: 10px;
  justify-content: center;
}

.link-item {
  width: 50%;
  padding: 8px;
  text-align: center;
}

.link-item a {
  display: flex;
  flex-direction: column;
  align-items: center;
  text-decoration: none;
  color: #333;
  transition: all 0.3s;
}

.link-item a:hover {
  transform: translateY(-3px);
}

.link-image-container {
  width: 90px;
  height: 70px;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 8px;
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
  color: #2980b9;
  margin-top: 5px;
}

.link-item a:hover .link-title {
  color: #1a5889;
}

.link-item a:hover .link-image-container {
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
  border-color: #d0d0d0;
}
</style> 