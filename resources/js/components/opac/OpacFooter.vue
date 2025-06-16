<template>
  <footer>
    <!-- Main Footer -->
    <div class="main-footer bg-light m-0">
      <div class="container-xl">
        <div class="row">
          <!-- Tài liệu nội sinh -->
          <div class="col-md-4">
            <h5 class="text-primary mb-3">
              <i class="fas fa-book-reader mr-2"></i>Tài liệu nội sinh
            </h5>
            <ul class="list-unstyled">
              <li class="mb-2">
                <a href="#" class="text-secondary">
                  <i class="fas fa-angle-right mr-2"></i>Luận văn - Luận án
                </a>
              </li>
              <li class="mb-2">
                <a href="#" class="text-secondary">
                  <i class="fas fa-angle-right mr-2"></i>Đề tài nghiên cứu khoa học
                </a>
              </li>
              <li class="mb-2">
                <a href="#" class="text-secondary">
                  <i class="fas fa-angle-right mr-2"></i>Giáo trình - Bài giảng
                </a>
              </li>
              <li class="mb-2">
                <a href="#" class="text-secondary">
                  <i class="fas fa-angle-right mr-2"></i>Báo cáo đồ án tốt nghiệp
                </a>
              </li>
            </ul>
          </div>

          <!-- Thư viện và Bạn đọc -->
          <div class="col-md-4">
            <h5 class="text-primary mb-3">
              <i class="fas fa-users mr-2"></i>Thư viện và Bạn đọc
            </h5>
            <ul class="list-unstyled">
              <li class="mb-2">
                <a href="#" class="text-secondary">
                  <i class="fas fa-angle-right mr-2"></i>Câu lạc bộ bạn đọc
                </a>
              </li>
              <li class="mb-2">
                <a href="#" class="text-secondary">
                  <i class="fas fa-angle-right mr-2"></i>Xã hội hóa thư viện
                </a>
              </li>
              <li class="mb-2">
                <a href="#" class="text-secondary">
                  <i class="fas fa-angle-right mr-2"></i>Đặt mua tài liệu
                </a>
              </li>
              <li class="mb-2">
                <a href="#" class="text-secondary">
                  <i class="fas fa-angle-right mr-2"></i>Top bạn đọc mượn nhiều nhất
                </a>
              </li>
            </ul>
          </div>

          <!-- Lịch phục vụ -->
          <div class="col-md-4">
            <h5 class="text-primary mb-3">
              <i class="fas fa-calendar-alt mr-2"></i>Lịch phục vụ
            </h5>
            <ul class="list-unstyled">
              <li class="mb-2">
                <i class="fas fa-clock text-info mr-2"></i>Thứ 2 và thứ 7: Giờ hành chính
              </li>
              <li class="mb-2">
                <i class="fas fa-exclamation-circle text-warning mr-2"></i>Đóng cửa chủ nhật và các ngày lễ
              </li>
            </ul>
          </div>
        </div>
      </div>
    </div>

    <!-- Footer Info -->
    <div class="footer-copyright bg-dark text-white py-3">
      <div class="container-xl">
        <div class="row">
          <div class="col-md-7">
            <p class="mb-1">
              <strong>Trung tâm Thông tin - Thư viện Trường Đại học Sư phạm Kỹ thuật Vĩnh Long</strong>
            </p>
            <p class="mb-0 small">
              <i class="fas fa-map-marker-alt mr-2"></i>73 Nguyễn Huệ, Phường Long Châu, TP. Vĩnh Long, Vĩnh Long
              <span class="mx-2">|</span>
              <i class="fas fa-envelope mr-2"></i>Email: thuvien@vlute.edu.vn
            </p>
          </div>
          <div class="col-md-5 text-md-right">
            <p class="mb-1 small">
              <span class="badge badge-light">
                <i class="fas fa-chart-line mr-1"></i>Số lượt truy cập: 108095
              </span>
            </p>
            <p class="mb-0 small">
                Khoa Công nghệ thông tin - Trường Đại học Sư phạm Kỹ thuật Vĩnh Long
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

.main-footer a {
  transition: all 0.2s ease;
}

.main-footer a:hover {
  color: #007bff !important;
  text-decoration: none;
  padding-left: 5px;
}

.main-footer h5 {
  font-weight: 600;
  border-bottom: 1px solid #dee2e6;
  padding-bottom: 10px;
  position: relative;
}

.main-footer h5:after {
  content: '';
  position: absolute;
  width: 60px;
  height: 2px;
  background-color: #007bff;
  bottom: -1px;
  left: 0;
}

.footer-copyright {
  box-shadow: 0 -3px 5px rgba(0,0,0,0.05);
}

@media (max-width: 768px) {
  .text-md-right {
    text-align: left !important;
    margin-top: 15px;
  }
}
</style>
