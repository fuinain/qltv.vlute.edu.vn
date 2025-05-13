<template>
  <footer>
    <!-- Phần thông tin thư viện -->
    <div class="footer-content">
      <div class="container-xl">
        <div class="row">
          <!-- Tài liệu nội sinh -->
          <div class="col-md-4">
            <h5 class="footer-title">Tài liệu nội sinh</h5>
            <ul class="footer-links">
              <li><a href="#"><i class="bi bi-chevron-right"></i> Luận văn - Luận án</a></li>
              <li><a href="#"><i class="bi bi-chevron-right"></i> Đề tài nghiên cứu khoa học</a></li>
              <li><a href="#"><i class="bi bi-chevron-right"></i> Giáo trình - Bài giảng</a></li>
              <li><a href="#"><i class="bi bi-chevron-right"></i> Báo cáo đồ án tốt nghiệp</a></li>
            </ul>
          </div>
          
          <!-- Thư viện và Bạn đọc -->
          <div class="col-md-4">
            <h5 class="footer-title">Thư viện và Bạn đọc</h5>
            <ul class="footer-links">
              <li><a href="#"><i class="bi bi-chevron-right"></i> Cấu lạc bộ bạn đọc</a></li>
              <li><a href="#"><i class="bi bi-chevron-right"></i> Xã hội hóa thư viện</a></li>
              <li><a href="#"><i class="bi bi-chevron-right"></i> Đặt mua tài liệu</a></li>
              <li><a href="#"><i class="bi bi-chevron-right"></i> Top bạn đọc đặt thư viện nhiều nhất</a></li>
            </ul>
          </div>
          
          <!-- Lịch phục vụ -->
          <div class="col-md-4">
            <h5 class="footer-title">Lịch phục vụ</h5>
            <ul class="footer-schedule">
              <li><i class="bi bi-clock"></i> Thứ 2 đến thứ 6: 7h – 17h</li>
              <li><i class="bi bi-clock"></i> Thứ 7: 8h – 12h</li>
              <li><i class="bi bi-exclamation-circle"></i> Đóng cửa chủ nhật và các ngày lễ</li>
            </ul>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Phần thông tin liên hệ -->
    <div class="footer-info">
      <div class="container-xl">
        <div class="row">
          <div class="col-md-8">
            <p class="footer-name">Trung tâm Thông tin - Thư viện Trường Đại học Sư phạm Kỹ thuật Vĩnh Long</p>
            <p class="footer-address">
              <i class="bi bi-geo-alt"></i> 73 Nguyễn Huệ, Phường 2, TP. Vĩnh Long, Vĩnh Long
              <span class="divider">|</span>
              <i class="bi bi-envelope"></i> Email: thuvien@vlute.edu.vn
            </p>
          </div>
          <div class="col-md-4 text-md-end">
            <p class="visitor-counter">
              <span>Số lượt truy cập: 108095</span>
            </p>
            <p class="copyright">
              Copyright © {{ currentYear }} Khoa Công nghệ thông tin - Đại học Sư phạm Kỹ thuật Vĩnh Long
            </p>
          </div>
        </div>
      </div>
    </div>
  </footer>
</template>

<script>
export default {
  name: "OpacFooter",
  computed: {
    currentYear() {
      return new Date().getFullYear();
    }
  }
}
</script>

<style scoped>
footer {
  margin-top: 40px;
}

.footer-content {
  background-color: #f5f5f5;
  padding: 40px 0;
  border-top: 1px solid #ddd;
}

.footer-title {
  color: #2980b9;
  font-size: 18px;
  font-weight: 600;
  margin-bottom: 20px;
  padding-bottom: 10px;
  border-bottom: 2px solid #e5e5e5;
  position: relative;
}

.footer-title:after {
  content: '';
  position: absolute;
  width: 60px;
  height: 2px;
  background-color: #2980b9;
  bottom: -2px;
  left: 0;
}

.footer-links {
  list-style: none;
  padding: 0;
  margin: 0;
}

.footer-links li {
  padding: 6px 0;
}

.footer-links li a {
  color: #555;
  text-decoration: none;
  transition: color 0.3s;
}

.footer-links li a:hover {
  color: #2980b9;
  padding-left: 5px;
}

.footer-links li a i {
  margin-right: 8px;
  font-size: 12px;
}

.footer-schedule {
  list-style: none;
  padding: 0;
  margin: 0;
}

.footer-schedule li {
  padding: 6px 0;
  color: #555;
}

.footer-schedule li i {
  margin-right: 10px;
  color: #2980b9;
}

.footer-info {
  background-color: #2c3e50;
  color: #fff;
  padding: 20px 0;
}

.footer-name {
  font-weight: 600;
  margin-bottom: 5px;
}

.footer-address {
  opacity: 0.8;
  font-size: 14px;
}

.divider {
  margin: 0 10px;
}

.visitor-counter {
  background-color: rgba(255, 255, 255, 0.1);
  display: inline-block;
  padding: 5px 15px;
  border-radius: 20px;
  margin-bottom: 5px;
}

.copyright {
  opacity: 0.7;
  font-size: 14px;
}

@media (max-width: 768px) {
  .footer-content {
    padding: 30px 0;
  }
  
  .col-md-4 {
    margin-bottom: 20px;
  }
  
  .footer-info .col-md-4 {
    text-align: left !important;
    margin-top: 15px;
  }
}
</style> 