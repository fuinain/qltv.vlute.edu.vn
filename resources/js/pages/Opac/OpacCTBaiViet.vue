<template>
  <div class="container-xl mt-4 mb-4">
    <div class="row">
      <!-- Main content -->
      <div class="col-md-8">
        <div class="card">
          <div class="card-header bg-primary text-white">
            <h2 class="mb-0">{{ baiViet.ten_bai_viet || 'Chi tiết bài viết' }}</h2>
          </div>
          <div class="card-body">
            <div v-if="loading" class="text-center">
              <div class="spinner-border text-primary" role="status"></div>
            </div>
            
            <div v-else-if="error" class="alert alert-warning">
              <p>{{ error }}</p>
              <p>Vui lòng thử lại sau hoặc liên hệ quản trị viên.</p>
            </div>
            
            <div v-else>
              <div class="mb-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                  <span class="text-muted">
                    <i class="far fa-calendar-alt mr-1"></i> Ngày đăng: {{ formatDate(baiViet.ngay_tao) }}
                  </span>
                  <span class="text-muted">
                    <i class="far fa-clock mr-1"></i> Cập nhật: {{ formatDate(baiViet.ngay_cap_nhat) }}
                  </span>
                </div>
                
                <div v-if="baiViet.tom_tat" class="alert alert-light border">
                  <strong>Tóm tắt:</strong> {{ baiViet.tom_tat }}
                </div>
              </div>
              
              <!-- Nội dung bài viết -->
              <div class="bai-viet-content" v-html="baiViet.noi_dung"></div>
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
</template>

<script>
import OpacSidebar from "@/components/opac/OpacSidebar.vue";
import axios from 'axios';

export default {
  name: "OpacCTBaiViet",
  components: {
    OpacSidebar
  },
  data() {
    return {
      baiViet: {},
      loading: true,
      error: null
    }
  },
  mounted() {
    this.layBaiViet();
  },
  methods: {
    async layBaiViet() {
      try {
        this.loading = true;
        const id = this.$route.params.id;
        const response = await axios.get(`/api/opac/chi-tiet-bai-viet/${id}`);
        if (response.data.status === 200) {
          this.baiViet = response.data.data;
        } else {
          this.error = 'Không thể tải nội dung bài viết';
        }
      } catch (error) {
        console.error('Lỗi khi lấy bài viết:', error);
        this.error = 'Đã xảy ra lỗi khi tải nội dung';
      } finally {
        this.loading = false;
      }
    },
    
    formatDate(dateString) {
      if (!dateString) return '';
      const date = new Date(dateString);
      return date.toLocaleDateString('vi-VN', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric'
      });
    }
  }
}
</script>

<style scoped>
.bai-viet-content {
  max-width: 800px;
  margin: 0 auto;
}

.bai-viet-content :deep(h1),
.bai-viet-content :deep(h2),
.bai-viet-content :deep(h3),
.bai-viet-content :deep(h4) {
  margin-top: 1.5rem;
  margin-bottom: 1rem;
  font-weight: bold;
  color: #333;
}

.bai-viet-content :deep(table) {
  width: 100%;
  max-width: 100%;
  margin-bottom: 1rem;
  border-collapse: collapse;
  border: 1px solid #dee2e6;
}

.bai-viet-content :deep(table th),
.bai-viet-content :deep(table td) {
  padding: 0.5rem;
  border: 1px solid #dee2e6;
  vertical-align: middle;
}

.bai-viet-content :deep(table th) {
  background-color: #f8f9fa;
  vertical-align: middle;
}

.bai-viet-content :deep(p) {
  margin-bottom: 1rem;
  line-height: 1.6;
}

.bai-viet-content :deep(ul),
.bai-viet-content :deep(ol) {
  margin-bottom: 1rem;
  padding-left: 1.5rem;
}

.bai-viet-content :deep(li) {
  margin-bottom: 0.5rem;
}

.bai-viet-content :deep(img) {
  max-width: 100%;
  height: auto;
  margin: 1rem 0;
  border-radius: 4px;
}

.bai-viet-content :deep(blockquote) {
  padding: 1rem;
  margin: 1rem 0;
  border-left: 4px solid #007bff;
  background-color: #f8f9fa;
}

/* CSS đặc thù cho bảng tiêu đề */
.bai-viet-content :deep(table[border="0"]) {
  width: 100% !important;
  border: 1px dashed #bbb !important;
  font-family: Verdana, Arial, Helvetica, sans-serif;
}

.bai-viet-content :deep(table[border="0"] td) {
  border: 1px dashed #bbb !important;
  color: #000;
  padding: 8px;
  text-align: left;
}

.bai-viet-content :deep(table.mceItemTable) {
  border: 1px dashed #bbb !important;
}

.bai-viet-content :deep(table.mceItemTable td) {
  border: 1px dashed #bbb !important;
}
</style> 