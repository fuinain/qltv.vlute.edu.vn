<template>
  <div class="container-xl mt-4 mb-4">
    <div class="row">
      <!-- Sidebar -->
      <div class="col-md-3">
        <OpacSidebar />
      </div>
      
      <!-- Main content -->
      <div class="col-md-9">
        <div class="card">
          <div class="card-header bg-primary text-white">
            <h2 class="mb-0">Liên hệ</h2>
          </div>
          <div class="card-body">
            <div class="row">
              <div class="col-md-6">
                <h4>Thông tin liên hệ</h4>
                <p><strong>Trung tâm Thông tin - Thư viện</strong></p>
                <p><strong>Trường Đại học Sư phạm Kỹ thuật Vĩnh Long</strong></p>
                <p>
                  <i class="bi bi-geo-alt-fill me-2"></i>Địa chỉ: 73 Nguyễn Huệ, Phường 2, TP. Vĩnh Long, Vĩnh Long
                </p>
                <p>
                  <i class="bi bi-telephone-fill me-2"></i>Điện thoại: (0270) 3830 304
                </p>
                <p>
                  <i class="bi bi-envelope-fill me-2"></i>Email: thuvien@vlute.edu.vn
                </p>
                <p>
                  <i class="bi bi-globe me-2"></i>Website: https://thuvien.vlute.edu.vn
                </p>
                
                <h4 class="mt-4">Giờ phục vụ</h4>
                <p><i class="bi bi-clock-fill me-2"></i>Thứ 2 - Thứ 6: 7h00 - 17h00</p>
                <p><i class="bi bi-clock-fill me-2"></i>Thứ 7: 8h00 - 12h00</p>
                <p><i class="bi bi-clock-fill me-2"></i>Chủ nhật và ngày lễ: Đóng cửa</p>
                
                <h4 class="mt-4">Các bộ phận</h4>
                <p><i class="bi bi-people-fill me-2"></i>Phòng Mượn - Trả: Số điện thoại nội bộ 101</p>
                <p><i class="bi bi-people-fill me-2"></i>Phòng Đọc: Số điện thoại nội bộ 102</p>
                <p><i class="bi bi-people-fill me-2"></i>Phòng Tham khảo: Số điện thoại nội bộ 103</p>
                <p><i class="bi bi-people-fill me-2"></i>Phòng Đa phương tiện: Số điện thoại nội bộ 104</p>
              </div>
              <div class="col-md-6">
                <h4>Gửi thông tin liên hệ</h4>
                <form @submit.prevent="submitForm">
                  <div class="mb-3">
                    <label for="fullName" class="form-label">Họ và tên <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="fullName" v-model="contactForm.fullName" required>
                  </div>
                  <div class="mb-3">
                    <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                    <input type="email" class="form-control" id="email" v-model="contactForm.email" required>
                  </div>
                  <div class="mb-3">
                    <label for="phone" class="form-label">Số điện thoại</label>
                    <input type="tel" class="form-control" id="phone" v-model="contactForm.phone">
                  </div>
                  <div class="mb-3">
                    <label for="subject" class="form-label">Tiêu đề <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="subject" v-model="contactForm.subject" required>
                  </div>
                  <div class="mb-3">
                    <label for="message" class="form-label">Nội dung <span class="text-danger">*</span></label>
                    <textarea class="form-control" id="message" rows="5" v-model="contactForm.message" required></textarea>
                  </div>
                  <button type="submit" class="btn btn-primary">Gửi thông tin</button>
                </form>
                
                <div v-if="formSubmitted" class="alert alert-success mt-3">
                  Cảm ơn bạn đã gửi thông tin. Chúng tôi sẽ liên hệ lại với bạn trong thời gian sớm nhất!
                </div>
              </div>
            </div>
            
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
    </div>
  </div>
</template>

<script>
import OpacSidebar from "@/components/opac/OpacSidebar.vue";

export default {
  name: "OpacLienHe",
  components: {
    OpacSidebar
  },
  data() {
    return {
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
  methods: {
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