<template>
  <div class="container-xl mt-4 mb-4">
    <div class="row">  
      <!-- Main content -->
      <div class="col-md-8">
        <div class="card">
          <div class="card-header bg-primary text-white">
            <h2 class="mb-0">{{ baiViet.ten_bai_viet || 'Hướng dẫn sử dụng thư viện' }}</h2>
          </div>
          <div class="card-body">
            <div v-if="loading" class="text-center">
              <div class="spinner-border text-primary" role="status">
              </div>
            </div>
            
            <div v-else-if="error" class="alert alert-warning">
              <p>{{ error }}</p>
              <p>Vui lòng thử lại sau hoặc liên hệ quản trị viên.</p>
            </div>
            
            <div v-else v-html="baiViet.noi_dung"></div>
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
  name: "OpacHuongDan",
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
        const response = await axios.get('/api/opac/bai-viet/huong-dan');
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