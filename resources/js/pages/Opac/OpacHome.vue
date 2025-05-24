<template>
  <div>
    <!-- Phần slider -->
    <OpacSlider />

    <div class="container-xl">
      <!-- Thanh tìm kiếm chính -->
      <OpacSearchBox @search="performSearch" />

      <div class="row">
        <!-- Main content -->
        <div class="col-md-8">
          <!-- Card chung cho tin tức và sách mới với tabs -->
          <div class="card card-primary card-outline card-outline-tabs mb-4">
            <div class="card-header p-0 border-bottom-0">
              <ul class="nav nav-tabs" id="opac-tabs" role="tablist">
                <li class="nav-item">
                  <a class="nav-link active" id="news-tab" data-toggle="pill" href="#news-content" role="tab" aria-controls="news-content" aria-selected="true">
                    <i class="fas fa-newspaper mr-1"></i> Tin tức - Thông báo
                  </a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" id="books-tab" data-toggle="pill" href="#books-content" role="tab" aria-controls="books-content" aria-selected="false">
                    <i class="fas fa-book-open mr-1"></i> Sách mới
                  </a>
                </li>
              </ul>
            </div>
            <div class="card-body p-0">
              <div class="tab-content" id="opac-tabs-content">
                <!-- Tab Tin tức - Thông báo -->
                <div class="tab-pane fade show active" id="news-content" role="tabpanel" aria-labelledby="news-tab">
                  <div class="news-list">
                    <div class="news-item d-flex p-3 border-bottom">
                      <div class="news-date text-center bg-light rounded mr-3 p-2">
                        <span class="date d-block font-weight-bold">7</span>
                        <span class="month text-muted small">Th6</span>
                      </div>
                      <div class="news-content">
                        <h5 class="mb-1">Thông báo lịch phục vụ hè 2023</h5>
                        <p class="text-muted mb-1">Thông báo về lịch phục vụ của thư viện trong kỳ nghỉ hè 2023.</p>
                        <a href="#" class="text-primary small">Chi tiết <i class="fas fa-arrow-right"></i></a>
                      </div>
                    </div>

                    <div class="news-item d-flex p-3 border-bottom">
                      <div class="news-date text-center bg-light rounded mr-3 p-2">
                        <span class="date d-block font-weight-bold">1</span>
                        <span class="month text-muted small">Th6</span>
                      </div>
                      <div class="news-content">
                        <h5 class="mb-1">Hướng dẫn sử dụng CSDL mới</h5>
                        <p class="text-muted mb-1">Hướng dẫn sử dụng cơ sở dữ liệu mới về tài liệu điện tử và phần mềm
                          quản lý tài liệu điện tử.</p>
                        <a href="#" class="text-primary small">Chi tiết <i class="fas fa-arrow-right"></i></a>
                      </div>
                    </div>

                    <div class="news-item d-flex p-3">
                      <div class="news-date text-center bg-light rounded mr-3 p-2">
                        <span class="date d-block font-weight-bold">24</span>
                        <span class="month text-muted small">Th5</span>
                      </div>
                      <div class="news-content">
                        <h5 class="mb-1">Triển lãm sách mới</h5>
                        <p class="text-muted mb-1">Thông báo về triển lãm sách mới từ 15/5 đến 30/5/2023.</p>
                        <a href="#" class="text-primary small">Chi tiết <i class="fas fa-arrow-right"></i></a>
                      </div>
                    </div>
                  </div>
                  <div class="card-footer bg-white text-right">
                    <a href="#" class="btn btn-sm btn-outline-primary">Xem tất cả <i class="fas fa-arrow-right"></i></a>
                  </div>
                </div>
                
                <!-- Tab Sách mới -->
                <div class="tab-pane fade" id="books-content" role="tabpanel" aria-labelledby="books-tab">
                  <ul class="products-list product-list-in-card pl-2 pr-2">
                    <li class="item">
                      <div class="product-img">
                        <img src="https://placehold.co/80x100/3498db/ffffff?text=Python" alt="Sách Python"
                          class="img-size-50">
                      </div>
                      <div class="product-info">
                        <a href="#" class="product-title">Lập trình Python</a>
                        <span class="product-description">
                          Minh Tuấn • 2023
                        </span>
                      </div>
                    </li>
                    <li class="item">
                      <div class="product-img">
                        <img src="https://placehold.co/80x100/e74c3c/ffffff?text=IoT" alt="Sách IoT"
                          class="img-size-50">
                      </div>
                      <div class="product-info">
                        <a href="#" class="product-title">Cơ sở IoT</a>
                        <span class="product-description">
                          Nguyễn Văn A • 2023
                        </span>
                      </div>
                    </li>
                    <li class="item">
                      <div class="product-img">
                        <img src="https://placehold.co/80x100/2ecc71/ffffff?text=IT" alt="Sách IT" class="img-size-50">
                      </div>
                      <div class="product-info">
                        <a href="#" class="product-title">Công nghệ thông tin đại cương</a>
                        <span class="product-description">
                          Nhiều tác giả • 2022
                        </span>
                      </div>
                    </li>
                  </ul>
                  <div class="card-footer bg-white text-right">
                    <a href="#" class="btn btn-sm btn-outline-success">Xem tất cả <i class="fas fa-arrow-right"></i></a>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Sidebar -->
        <div class="col-md-4">
          <OpacSidebar />
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import OpacSlider from "@/components/opac/OpacSlider.vue";
import OpacSidebar from "@/components/opac/OpacSidebar.vue";
import OpacSearchBox from "@/components/opac/OpacSearchBox.vue";

export default {
  name: "OpacHome",
  components: {
    OpacSlider,
    OpacSidebar,
    OpacSearchBox
  },
  mounted() {
    // Kích hoạt tabs khi component được mount
    if (window.jQuery) {
      $('#opac-tabs a').on('click', function (e) {
        e.preventDefault();
        $(this).tab('show');
      });
    }
  },
  methods: {
    performSearch(searchParams) {
      console.log('Đang tìm kiếm với thông số:', searchParams);
      // Thực hiện tìm kiếm hoặc chuyển đến trang kết quả tìm kiếm
      this.$router.push({
        path: '/search',
        query: {
          q: searchParams.query,
          type: searchParams.type,
          docType: searchParams.docType,
          fullText: searchParams.fullTextOnly ? '1' : '0'
        }
      });
    }
  }
}
</script>

<style scoped>
.info-box {
  box-shadow: 0 0 1px rgba(0, 0, 0, 0.125), 0 1px 3px rgba(0, 0, 0, 0.2);
  border-radius: 0.25rem;
  background-color: #fff;
  display: flex;
  margin-bottom: 1rem;
  min-height: 80px;
  position: relative;
  width: 100%;
}

.info-box-icon {
  border-radius: 0.25rem 0 0 0.25rem;
  display: flex;
  align-items: center;
  justify-content: center;
  width: 70px;
  height: 100%;
  text-align: center;
  font-size: 1.875rem;
  color: #fff;
}

.info-box-content {
  display: flex;
  flex-direction: column;
  padding: 10px;
  flex: 1;
}

.info-box-text {
  font-size: 1rem;
  margin: 0;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.info-box-desc {
  font-size: 0.875rem;
  margin: 0;
  color: #666;
}

.news-date {
  min-width: 60px;
}

.news-date .date {
  font-size: 1.2rem;
  line-height: 1;
}

.products-list .product-img {
  float: left;
  width: 80px;
}

.products-list .product-img img {
  height: 100px;
  width: 80px;
}

.products-list .product-info {
  margin-left: 90px;
}

.products-list .product-title {
  font-weight: 600;
  display: block;
  color: #007bff;
}

.products-list .product-description {
  display: block;
  color: #6c757d;
  font-size: 0.875rem;
}

.products-list>.item {
  padding: 10px;
  border-bottom: 1px solid rgba(0, 0, 0, 0.125);
}

.products-list>.item:last-child {
  border-bottom-width: 0;
}

.nav-tabs .nav-link {
  font-weight: 500;
}

.nav-tabs .nav-link.active {
  border-top: 3px solid #007bff;
}
</style>