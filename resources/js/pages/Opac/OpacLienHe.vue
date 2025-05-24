<template>
  <div class="container-xl mt-4 mb-4">
    <div class="row">
      
      <!-- Main content -->
      <div class="col-md-8">
        <div class="card">
          <div class="card-header bg-primary text-white">
            <h2 class="mb-0">{{ baiViet.ten_bai_viet || 'Liên hệ' }}</h2>
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
            
            <div v-else class="mb-4" v-html="baiViet.noi_dung"></div>       
            <div class="mt-4">
              <h4>Bản đồ</h4>
              <div class="map-container">
                <iframe 
                  src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3926.0425714221657!2d105.96892177384654!3d10.248795789813902!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x310a82c32ee13431%3A0x447b2b608a02bda0!2zNzMgTmd1eeG7hW4gSHXhu4csIFBoxrDhu51uZyAyLCBWxKluaCBMb25nLCBWaeG7h3QgTmFt!5e0!3m2!1svi!2s!4v1723333326474!5m2!1svi!2s" 
                  width="100%" 
                  height="450" 
                  style="border:0; border-radius: 8px;" 
                  allowfullscreen="" 
                  loading="lazy"
                  referrerpolicy="no-referrer-when-downgrade">
                </iframe>
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
</template>

<script>
import OpacSidebar from "@/components/opac/OpacSidebar.vue";
import axios from 'axios';

export default {
  name: "OpacLienHe",
  components: {
    OpacSidebar
  },
  data() {
    return {
      baiViet: {},
      loading: true,
      error: null,
      contactForm: {
        fullName: '',
        email: '',
        phone: '',
        subject: '',
        message: ''
      },
      formSubmitted: false
    }
  },
  mounted() {
    this.layBaiViet();
  },
  methods: {
    async layBaiViet() {
      try {
        this.loading = true;
        const response = await axios.get('/api/opac/bai-viet/lien-he');
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
    submitForm() {
      // Tạm thời chỉ hiển thị thông báo đã gửi (trong thực tế sẽ gửi dữ liệu về server)
      console.log('Form data:', this.contactForm);
      this.formSubmitted = true;
      
      // Reset form
      this.contactForm = {
        fullName: '',
        email: '',
        phone: '',
        subject: '',
        message: ''
      };
      
      // Sau 5 giây, ẩn thông báo
      setTimeout(() => {
        this.formSubmitted = false;
      }, 5000);
    }
  }
}
</script>

<style scoped>
.map-container {
  position: relative;
  overflow: hidden;
  border-radius: 8px;
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
}

iframe {
  display: block;
}
</style> 