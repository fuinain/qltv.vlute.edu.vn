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
                  <div v-if="loadingBaiViet" class="text-center p-4">
                    <div class="spinner-border text-primary" role="status">
                    </div>
                  </div>
                  <div v-else-if="errorBaiViet" class="alert alert-warning m-3">
                    {{ errorBaiViet }}
                  </div>
                  <div v-else class="news-list">
                    <div v-for="baiViet in danhSachBaiViet" :key="baiViet.id_bai_viet" class="news-item d-flex p-3 border-bottom">
                      <div class="news-date text-center mr-3">
                        <img src="/images/image_opac/anh_thong_bao.png" alt="Thông báo" width="60" height="60" class="rounded">
                      </div>
                      <div class="news-content">
                        <h5 class="mb-1">{{ baiViet.ten_bai_viet }}</h5>
                        <p v-if="baiViet.tom_tat" class="text-muted mb-1 small">{{ baiViet.tom_tat }}</p>
                        <p class="text-muted mb-1 small">{{ formatDate(baiViet.ngay_tao) }}</p>
                        <router-link :to="{ name: 'OpacCTBaiViet', params: { id: baiViet.id_bai_viet }}" class="text-primary small">Chi tiết <i class="fas fa-arrow-right"></i></router-link>
                      </div>
                    </div>
                    <div v-if="danhSachBaiViet.length === 0" class="text-center p-4">
                      <p class="text-muted">Không có thông báo nào.</p>
                    </div>
                  </div>                
                </div>
                
                <!-- Tab Sách mới -->
                <div class="tab-pane fade" id="books-content" role="tabpanel" aria-labelledby="books-tab">
                  <div v-if="loadingSachMoi" class="text-center p-4">
                    <div class="spinner-border text-primary" role="status">
                    </div>
                  </div>
                  <div v-else-if="errorSachMoi" class="alert alert-warning m-3">
                    {{ errorSachMoi }}
                  </div>
                  <ul v-else class="products-list product-list-in-card pl-2 pr-2">
                    <li v-for="sach in danhSachSachMoi" :key="sach.id_sach" class="item">
                      <div class="product-img">
                        <img src="/images/image_opac/anh_sach.png" :alt="sach.nhan_de" width="60" height="60" class="rounded">
                      </div>
                      <div class="product-info">
                        <div class="d-flex justify-content-between align-items-start">
                          <div>
                            <router-link :to="{ name: 'OpacChiTietSach', params: { id: sach.id_sach }}" class="product-title">
                              {{ sach.nhan_de }}
                            </router-link>
                            <span class="product-description d-block">
                              <strong>Tác giả:</strong> {{ sach.tac_gia || 'Không có thông tin' }}
                            </span>
                            <span class="product-description small text-muted d-block">
                              {{ formatInfoSach(sach) }}
                            </span>
                          </div>
                          <router-link :to="{ name: 'OpacChiTietSach', params: { id: sach.id_sach }}" class="btn btn-sm btn-outline-primary ml-2">
                            <i class="fas fa-info-circle"></i> Chi tiết
                          </router-link>
                        </div>
                      </div>
                    </li>
                    <li v-if="danhSachSachMoi.length === 0" class="item text-center">
                      <p class="text-muted">Không có sách mới nào.</p>
                    </li>
                  </ul>
                  <div class="card-footer bg-white text-right">
                    <router-link to="/danh-sach-sach" class="btn btn-sm btn-outline-success">Xem tất cả <i class="fas fa-arrow-right"></i></router-link>
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
import axios from 'axios';

export default {
  name: "OpacHome",
  components: {
    OpacSlider,
    OpacSidebar,
    OpacSearchBox
  },
  data() {
    return {
      // Dữ liệu cho bài viết
      danhSachBaiViet: [],
      loadingBaiViet: true,
      errorBaiViet: null,

      // Dữ liệu cho sách mới
      danhSachSachMoi: [],
      loadingSachMoi: true,
      errorSachMoi: null
    };
  },
  mounted() {
    // Kích hoạt tabs khi component được mount
    if (window.jQuery) {
      $('#opac-tabs a').on('click', function (e) {
        e.preventDefault();
        $(this).tab('show');
      });
    }

    // Tải dữ liệu khi component được mount
    this.loadBaiViet();
    this.loadSachMoi();
  },
  methods: {
    performSearch(searchParams) {
      // Xử lý tìm kiếm - chuyển đến trang kết quả tìm kiếm
      // Lưu ý: Thực tế việc này đã được xử lý trong component OpacSearchBox
      console.log('Đang tìm kiếm với thông số:', searchParams);
    },

    // Phương thức tải bài viết
    async loadBaiViet() {
      try {
        this.loadingBaiViet = true;
        this.errorBaiViet = null;
        const response = await axios.get('/api/opac/danh-sach-bai-viet-theo-chu-de/Thông báo');
        if (response.data.status === 200) {
          this.danhSachBaiViet = response.data.data;
        } else {
          this.errorBaiViet = response.data.message || 'Có lỗi xảy ra khi tải thông báo';
        }
      } catch (error) {
        console.error('Lỗi khi tải bài viết:', error);
        this.errorBaiViet = 'Không thể kết nối đến máy chủ';
      } finally {
        this.loadingBaiViet = false;
      }
    },

    // Phương thức tải sách mới
    async loadSachMoi() {
      try {
        this.loadingSachMoi = true;
        this.errorSachMoi = null;
        const response = await axios.get('/api/opac/sach-moi');
        if (response.data.status === 200) {
          this.danhSachSachMoi = response.data.data;
        } else {
          this.errorSachMoi = response.data.message || 'Có lỗi xảy ra khi tải sách mới';
        }
      } catch (error) {
        console.error('Lỗi khi tải sách mới:', error);
        this.errorSachMoi = 'Không thể kết nối đến máy chủ';
      } finally {
        this.loadingSachMoi = false;
      }
    },

    // Format ngày tháng
    formatDate(dateString) {
      if (!dateString) return '';
      const date = new Date(dateString);
      return date.toLocaleDateString('vi-VN', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric'
      });
    },

    // Format thông tin sách
    formatInfoSach(sach) {
      let info = [];
      if (sach.nha_xuat_ban) info.push(sach.nha_xuat_ban);
      if (sach.noi_xuat_ban) info.push(sach.noi_xuat_ban);
      if (sach.nam_xuat_ban) info.push(sach.nam_xuat_ban);
      return info.join(' - ') || 'Không có thông tin';
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
  width: 60px;
  height: 60px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.products-list .product-img img {
  max-height: 60px;
  max-width: 60px;
  object-fit: contain;
}

.products-list .product-info {
  margin-left: 70px;
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

/* CSS cho phần sách mới */
.products-list .btn {
  white-space: nowrap;
}

@media (max-width: 576px) {
  .products-list .product-info .d-flex {
    flex-direction: column;
    align-items: flex-start !important;
  }
  
  .products-list .product-info .btn {
    margin-left: 0 !important;
    margin-top: 0.5rem;
    font-size: 0.75rem;
  }
  
  .products-list .product-title {
    font-size: 0.9rem;
  }
  
  .products-list .product-description {
    font-size: 0.8rem;
  }
}
</style>