<template>
  <div class="container-xl py-5">
    <div class="row justify-content-center">
      <div class="col-md-8">
        <div class="card">
          <div class="card-header bg-danger text-white">
            <h4 class="mb-0"><i class="fas fa-exclamation-triangle mr-2"></i> Thông báo</h4>
          </div>
          <div class="card-body text-center">
            <img src="/images/image_opac/not_found.png" alt="Không tìm thấy" class="img-fluid mb-4" style="max-height: 200px;">
            <h3 class="text-danger mb-3">Sinh viên chưa được đăng ký trong hệ thống thư viện</h3>
            <p class="lead mb-4">
              Tài khoản của bạn chưa được đăng ký trong hệ thống thư viện. Vui lòng liên hệ với thư viện trường để được hỗ trợ.
            </p>
            <div class="alert alert-info">
              <p class="mb-0"><i class="fas fa-info-circle mr-2"></i> Để được hỗ trợ, vui lòng mang theo thẻ sinh viên đến thư viện trường hoặc liên hệ qua email: thuvien@vlute.edu.vn</p>
            </div>
            <div class="mt-4">
              <router-link to="/" class="btn btn-primary btn-lg">
                <i class="fas fa-home mr-2"></i> Quay về trang chủ
              </router-link>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "SinhVienKhongTonTai"
}
</script>

<style scoped>
.card {
  border: none;
  box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
  border-radius: 0.5rem;
  overflow: hidden;
}

.card-header {
  border-bottom: none;
}

.btn-lg {
  padding: 0.75rem 1.5rem;
  font-size: 1.1rem;
}
</style> 