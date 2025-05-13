<template>
  <div>
    <!-- Phần slider -->
    <OpacSlider />
    
    <div class="container-xl">
      <!-- Thanh tìm kiếm chính -->
      <div class="main-search-box mb-4">
        <div class="tab-content" id="search-tab-content">
          <div class="tab-pane fade show active" id="basic-search" role="tabpanel">
            <div class="search-form-container">
              <h4 class="search-title">Tra cứu tài liệu</h4>
              <div class="search-form">
                <div class="input-group mb-3">
                  <input type="text" class="form-control" placeholder="Nhập từ khóa tìm kiếm..." aria-label="Tìm kiếm">
                  <button class="btn btn-primary" type="button">
                    <i class="bi bi-search me-1"></i> Tìm kiếm
                  </button>
                </div>
                <div class="search-options">
                  <div class="row">
                    <div class="col-lg-4 col-md-6 mb-2 mb-lg-0">
                      <select class="form-select">
                        <option selected>Tất cả</option>
                        <option>Tựa đề</option>
                        <option>Tác giả</option>
                        <option>Chủ đề</option>
                      </select>
                    </div>
                    <div class="col-lg-4 col-md-6 mb-2 mb-lg-0">
                      <select class="form-select">
                        <option selected>Tất cả tài liệu</option>
                        <option>Sách</option>
                        <option>Luận văn</option>
                        <option>Tạp chí</option>
                      </select>
                    </div>
                    <div class="col-lg-4 col-md-12">
                      <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="fullTextSearch">
                        <label class="form-check-label" for="fullTextSearch">
                          Chỉ hiển thị tài liệu có toàn văn
                        </label>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      
      <div class="row">
        <!-- Sidebar -->
        <div class="col-md-3">
          <OpacSidebar />
        </div>
        
        <!-- Main content -->
        <div class="col-md-9">
          <!-- Dịch vụ thư viện -->
          <div class="row service-section mb-4">
            <h3 class="section-title">Dịch vụ thư viện</h3>
            <div class="col-md-4">
              <div class="service-card">
                <div class="service-icon">
                  <i class="bi bi-book"></i>
                </div>
                <h4>Mượn trả sách</h4>
                <p>Tra cứu, đặt mượn và quản lý tài liệu mượn trực tuyến.</p>
              </div>
            </div>
            <div class="col-md-4">
              <div class="service-card">
                <div class="service-icon">
                  <i class="bi bi-laptop"></i>
                </div>
                <h4>Truy cập tài liệu điện tử</h4>
                <p>Kho tài liệu điện tử, sách, luận văn, bài báo tại chỗ.</p>
              </div>
            </div>
            <div class="col-md-4">
              <div class="service-card">
                <div class="service-icon">
                  <i class="bi bi-headset"></i>
                </div>
                <h4>Hỗ trợ trực tuyến</h4>
                <p>Hỗ trợ trực tuyến với các dịch vụ thư viện.</p>
              </div>
            </div>
          </div>
          
          <!-- Tin tức - Thông báo -->
          <div class="row mb-4">
            <div class="col-md-7">
              <div class="news-section">
                <h3 class="section-title">Tin tức - Thông báo</h3>
                <div class="news-list">
                  <div class="news-item">
                    <div class="news-date">
                      <span class="date">7</span>
                      <span class="month">Th6</span>
                    </div>
                    <div class="news-content">
                      <h5>Thông báo lịch phục vụ hè 2023</h5>
                      <p>Thông báo về lịch phục vụ của thư viện trong kỳ nghỉ hè 2023.</p>
                      <a href="#" class="read-more">Chi tiết <i class="bi bi-arrow-right"></i></a>
                    </div>
                  </div>
                  
                  <div class="news-item">
                    <div class="news-date">
                      <span class="date">1</span>
                      <span class="month">Th6</span>
                    </div>
                    <div class="news-content">
                      <h5>Hướng dẫn sử dụng CSDL mới</h5>
                      <p>Hướng dẫn sử dụng cơ sở dữ liệu mới về tài liệu điện tử và phần mềm quản lý tài liệu điện tử.</p>
                      <a href="#" class="read-more">Chi tiết <i class="bi bi-arrow-right"></i></a>
                    </div>
                  </div>
                  
                  <div class="news-item">
                    <div class="news-date">
                      <span class="date">24</span>
                      <span class="month">Th5</span>
                    </div>
                    <div class="news-content">
                      <h5>Triển lãm sách mới</h5>
                      <p>Thông báo về triển lãm sách mới từ 15/5 đến 30/5/2023.</p>
                      <a href="#" class="read-more">Chi tiết <i class="bi bi-arrow-right"></i></a>
                    </div>
                  </div>
                </div>
                
                <div class="text-end mt-3">
                  <a href="#" class="btn btn-outline-primary btn-sm">Xem tất cả <i class="bi bi-arrow-right"></i></a>
                </div>
              </div>
            </div>
            
            <!-- Sách mới -->
            <div class="col-md-5">
              <div class="new-books-section">
                <h3 class="section-title">Sách mới</h3>
                <div class="new-books-list">
                  <div class="book-item">
                    <div class="book-cover">
                      <img src="https://placehold.co/80x100/3498db/ffffff?text=Python" alt="Sách Python">
                    </div>
                    <div class="book-info">
                      <h5>Lập trình Python</h5>
                      <p>Minh Tuấn • 2023</p>
                    </div>
                  </div>
                  
                  <div class="book-item">
                    <div class="book-cover">
                      <img src="https://placehold.co/80x100/e74c3c/ffffff?text=IoT" alt="Sách IoT">
                    </div>
                    <div class="book-info">
                      <h5>Cơ sở IoT</h5>
                      <p>Nguyễn Văn A • 2023</p>
                    </div>
                  </div>
                  
                  <div class="book-item">
                    <div class="book-cover">
                      <img src="https://placehold.co/80x100/2ecc71/ffffff?text=IT" alt="Sách IT">
                    </div>
                    <div class="book-info">
                      <h5>Công nghệ thông tin đại cương</h5>
                      <p>Nhiều tác giả • 2022</p>
                    </div>
                  </div>
                </div>
                
                <div class="text-end mt-3">
                  <a href="#" class="btn btn-outline-primary btn-sm">Xem tất cả <i class="bi bi-arrow-right"></i></a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import OpacSlider from "@/components/opac/OpacSlider.vue";
import OpacSidebar from "@/components/opac/OpacSidebar.vue";

export default {
  name: "OpacHome",
  components: {
    OpacSlider,
    OpacSidebar
  },
  methods: {
    performSearch(searchParams) {
      // Chuyển hướng đến trang kết quả tìm kiếm với các tham số
      this.$router.push({
        path: '/search',
        query: searchParams
      });
    }
  }
}
</script>

<style scoped>
/* Thanh tìm kiếm chính */
.main-search-box {
  position: relative;
  margin-top: -30px;
  z-index: 100;
}

.search-form-container {
  background-color: #fff;
  border-radius: 10px;
  box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
  padding: 25px;
  border: 1px solid #eaeaea;
}

.search-title {
  color: #2c3e50;
  font-size: 1.5rem;
  font-weight: 600;
  margin-bottom: 20px;
  text-align: center;
  position: relative;
}

.search-title:after {
  content: '';
  display: block;
  width: 60px;
  height: 3px;
  background-color: #3498db;
  margin: 10px auto;
  border-radius: 2px;
}

.search-form .form-control {
  height: 46px;
  font-size: 16px;
  border-color: #ddd;
  border-radius: 6px 0 0 6px;
}

.search-form .btn {
  border-radius: 0 6px 6px 0;
  padding: 0 20px;
  font-weight: 500;
}

.search-options {
  margin-top: 15px;
  background-color: #f9f9f9;
  padding: 15px;
  border-radius: 6px;
  border: 1px solid #eeeeee;
}

.search-options .form-select {
  height: 40px;
  border-color: #ddd;
}

.search-options .form-check-input:checked {
  background-color: #3498db;
  border-color: #3498db;
}

/* Tiêu đề các phần */
.section-title {
  color: #2c3e50;
  font-size: 1.4rem;
  font-weight: 600;
  margin-bottom: 20px;
  padding-bottom: 10px;
  border-bottom: 2px solid #eaeaea;
  position: relative;
}

.section-title:after {
  content: '';
  position: absolute;
  width: 60px;
  height: 2px;
  background-color: #3498db;
  bottom: -2px;
  left: 0;
}

/* Dịch vụ thư viện */
.service-section {
  margin-bottom: 30px;
}

.service-card {
  background-color: #fff;
  border-radius: 8px;
  padding: 20px;
  box-shadow: 0 3px 10px rgba(0, 0, 0, 0.08);
  height: 100%;
  transition: transform 0.3s;
  border: 1px solid #eee;
}

.service-card:hover {
  transform: translateY(-5px);
}

.service-icon {
  background-color: #e8f4fc;
  width: 60px;
  height: 60px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 15px;
}

.service-icon i {
  font-size: 26px;
  color: #3498db;
}

.service-card h4 {
  font-size: 18px;
  font-weight: 600;
  margin-bottom: 10px;
  color: #2c3e50;
}

.service-card p {
  color: #7f8c8d;
  font-size: 14px;
  margin-bottom: 0;
}

/* Tin tức và thông báo */
.news-section, .new-books-section {
  background-color: #fff;
  border-radius: 8px;
  padding: 20px;
  box-shadow: 0 3px 10px rgba(0, 0, 0, 0.08);
  height: 100%;
  border: 1px solid #eee;
}

.news-item {
  display: flex;
  padding: 15px 0;
  border-bottom: 1px solid #eee;
}

.news-item:last-child {
  border-bottom: none;
}

.news-date {
  background-color: #3498db;
  color: white;
  min-width: 50px;
  height: 60px;
  border-radius: 4px;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  margin-right: 15px;
}

.news-date .date {
  font-size: 20px;
  font-weight: bold;
  line-height: 1;
}

.news-date .month {
  font-size: 14px;
}

.news-content h5 {
  font-size: 16px;
  font-weight: 600;
  margin-bottom: 5px;
}

.news-content p {
  color: #7f8c8d;
  font-size: 14px;
  margin-bottom: 5px;
}

.read-more {
  font-size: 14px;
  color: #3498db;
  text-decoration: none;
}

.read-more:hover {
  text-decoration: underline;
}

/* Sách mới */
.book-item {
  display: flex;
  padding: 15px 0;
  border-bottom: 1px solid #eee;
}

.book-item:last-child {
  border-bottom: none;
}

.book-cover {
  margin-right: 15px;
}

.book-cover img {
  border-radius: 4px;
  box-shadow: 0 2px 5px rgba(0,0,0,0.1);
}

.book-info h5 {
  font-size: 16px;
  font-weight: 600;
  margin-bottom: 5px;
}

.book-info p {
  color: #7f8c8d;
  font-size: 14px;
  margin-bottom: 0;
}

@media (max-width: 992px) {
  .search-options .row > div {
    margin-bottom: 10px;
  }
}

@media (max-width: 768px) {
  .service-card, .news-section, .new-books-section {
    margin-bottom: 20px;
  }
  
  .main-search-box {
    margin-top: -15px;
    margin-bottom: 30px;
  }
  
  .search-form-container {
    padding: 15px;
  }
}
</style> 