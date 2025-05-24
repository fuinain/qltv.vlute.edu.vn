<template>
  <div class="container-xl mt-4 mb-4">
    <div class="row">
      <!-- Main content -->
      <div class="col-md-8">
        <div class="card">
          <div class="card-header bg-primary text-white">
            <h2 class="mb-0">{{ baiViet.ten_bai_viet || 'Nội quy thư viện' }}</h2>
          </div>
          <div class="card-body">
            <div v-if="loading" class="text-center">
              <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Đang tải...</span>
              </div>
            </div>
            
            <div v-else-if="error" class="alert alert-warning">
              <p>{{ error }}</p>
              <p>Vui lòng thử lại sau hoặc liên hệ quản trị viên.</p>
            </div>
            
            <div v-else class="noi-quy-content" v-html="baiViet.noi_dung"></div>
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
  name: "OpacNoiQuy",
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
        const response = await axios.get('/api/opac/bai-viet/noi-quy');
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
    }
  }
}
</script>

<style scoped>
.noi-quy-content {
  max-width: 800px;
  margin: 0 auto;
}

.noi-quy-content :deep(h1),
.noi-quy-content :deep(h2),
.noi-quy-content :deep(h3),
.noi-quy-content :deep(h4) {
  margin-top: 1.5rem;
  margin-bottom: 1rem;
  font-weight: bold;
  color: #333;
}

.noi-quy-content :deep(table) {
  width: 100%;
  max-width: 100%;
  margin-bottom: 1rem;
  border-collapse: collapse;
}

.noi-quy-content :deep(table th),
.noi-quy-content :deep(table td) {
  padding: 0.5rem;
  border: 1px solid #dee2e6;
}

.noi-quy-content :deep(table th) {
  background-color: #f8f9fa;
  vertical-align: middle;
}

.noi-quy-content :deep(p) {
  margin-bottom: 1rem;
  line-height: 1.6;
}

.noi-quy-content :deep(ul),
.noi-quy-content :deep(ol) {
  margin-bottom: 1rem;
  padding-left: 1.5rem;
}

.noi-quy-content :deep(li) {
  margin-bottom: 0.5rem;
}

.noi-quy-content :deep(img) {
  max-width: 100%;
  height: auto;
  margin: 1rem 0;
  border-radius: 4px;
}

.noi-quy-content :deep(blockquote) {
  padding: 1rem;
  margin: 1rem 0;
  border-left: 4px solid #007bff;
  background-color: #f8f9fa;
}
</style> 