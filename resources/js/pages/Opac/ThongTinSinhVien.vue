<template>
  <div class="container-xl py-4">
    <div class="row">
      <!-- Thông tin cá nhân -->
      <div class="col-md-4">
        <div class="card profile-card h-100">
          <div class="card-header bg-gradient-primary text-white">
            <h5 class="mb-0"><i class="fas fa-id-card mr-2"></i> Thông tin sinh viên</h5>
          </div>
          <div class="card-body">
            <div v-if="loading" class="text-center p-3">
              <div class="spinner-border text-primary" role="status"></div>
              <p class="mt-2">Đang tải thông tin...</p>
            </div>

            <div v-else-if="error" class="alert alert-danger">
              {{ error }}
            </div>

            <div v-else>
              <div class="text-center mb-4">
                <div class="avatar-circle">
                  <i class="fas fa-user-graduate fa-4x text-primary"></i>
                </div>
                <h4 class="mt-3 font-weight-bold">{{ hoTen }}</h4>
                <p class="text-muted mb-0">
                  <span class="badge badge-primary">
                    <i class="fas fa-id-badge mr-1"></i> {{ mssv }}
                  </span>
                </p>
              </div>

              <div class="info-list">
                <div class="info-item">
                  <div class="info-icon">
                    <i class="fas fa-envelope"></i>
                  </div>
                  <div class="info-content">
                    <span class="info-label">Email</span>
                    <span class="info-value">{{ thongTinSV.email || 'Chưa cập nhật' }}</span>
                  </div>
                </div>
                
                <div class="info-item">
                  <div class="info-icon">
                    <i class="fas fa-graduation-cap"></i>
                  </div>
                  <div class="info-content">
                    <span class="info-label">Lớp</span>
                    <span class="info-value">{{ thongTinSV.ten_lop || 'Chưa cập nhật' }}</span>
                  </div>
                </div>
                
                <div class="info-item">
                  <div class="info-icon">
                    <i class="fas fa-calendar-alt"></i>
                  </div>
                  <div class="info-content">
                    <span class="info-label">Ngày sinh</span>
                    <span class="info-value">{{ formatDate(thongTinSV.ngay_sinh) || 'Chưa cập nhật' }}</span>
                  </div>
                </div>
                
                <div class="info-item">
                  <div class="info-icon">
                    <i class="fas fa-user-tag"></i>
                  </div>
                  <div class="info-content">
                    <span class="info-label">Khoa/Ngành</span>
                    <span class="info-value">{{ thongTinSV.ten_chuyen_nganh || 'Chưa cập nhật' }}</span>
                  </div>
                </div>
                
                <div class="info-item">
                  <div class="info-icon">
                    <i class="fas fa-id-badge"></i>
                  </div>
                  <div class="info-content">
                    <span class="info-label">Số thẻ</span>
                    <span class="info-value">{{ thongTinSV.so_the || 'Chưa cập nhật' }}</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Thông tin thẻ thư viện -->
      <div class="col-md-8">
        <div class="card h-100">
          <div class="card-header bg-gradient-primary text-white">
            <h5 class="mb-0"><i class="fas fa-address-card mr-2"></i> Thẻ thư viện</h5>
          </div>
          <div class="card-body">
            <div v-if="loading" class="text-center p-3">
              <div class="spinner-border text-primary" role="status"></div>
              <p class="mt-2">Đang tải thông tin...</p>
            </div>

            <div v-else-if="error" class="alert alert-danger">
              {{ error }}
            </div>

            <div v-else>
              <div class="additional-info">
                <h6 class="text-primary mb-3"><i class="fas fa-info-circle mr-2"></i>Thông tin bổ sung</h6>
                <div class="row">
                  <div class="col-md-6">
                    <div class="info-box">
                      <div class="info-box-icon">
                        <i class="fas fa-calendar-plus"></i>
                      </div>
                      <div class="info-box-content">
                        <span class="info-box-label">Ngày cấp thẻ</span>
                        <span class="info-box-value">{{ formatDate(thongTinSV.ngay_cap_the) || 'Chưa cập nhật' }}</span>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="info-box">
                      <div class="info-box-icon">
                        <i class="fas fa-calendar-times"></i>
                      </div>
                      <div class="info-box-content">
                        <span class="info-box-label">Hạn thẻ</span>
                        <span class="info-box-value" :class="{'text-danger': isExpired}">
                          {{ formatDate(thongTinSV.han_the) || 'Chưa cập nhật' }}
                        </span>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="info-box">
                      <div class="info-box-icon">
                        <i class="fas fa-sort-numeric-up"></i>
                      </div>
                      <div class="info-box-content">
                        <span class="info-box-label">Lần cấp thẻ</span>
                        <span class="info-box-value">{{ thongTinSV.lan_cap_the || '1' }}</span>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="info-box">
                      <div class="info-box-icon">
                        <i class="fas fa-lock-open"></i>
                      </div>
                      <div class="info-box-content">
                        <span class="info-box-label">Trạng thái thẻ</span>
                        <span class="info-box-value">
                          <span :class="getRutHanClass(thongTinSV.rut_han)">
                            {{ getRutHanText(thongTinSV.rut_han) }}
                          </span>
                        </span>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="info-box">
                      <div class="info-box-icon">
                        <i class="fas fa-map-marker-alt"></i>
                      </div>
                      <div class="info-box-content">
                        <span class="info-box-label">Hộ khẩu</span>
                        <span class="info-box-value">{{ thongTinSV.ho_khau || 'Chưa cập nhật' }}</span>
                      </div>
                    </div>
                  </div>
                  
                </div>
                
                <div class="note-box mt-3" v-if="thongTinSV.ghi_chu">
                  <div class="note-header">
                    <i class="fas fa-sticky-note mr-2"></i> Ghi chú
                  </div>
                  <div class="note-content">
                    {{ thongTinSV.ghi_chu || 'Không có' }}
                  </div>
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
import axios from 'axios';

export default {
  name: "ThongTinSinhVien",
  data() {
    return {
      hoTen: '',
      mssv: '',
      idDocGia: '',
      loading: true,
      error: null,
      thongTinSV: {},
      lichSuMuon: [],
      loadingHistory: true,
      errorHistory: null
    };
  },
  computed: {
    isExpired() {
      if (!this.thongTinSV.han_the) return false;
      const hanThe = new Date(this.thongTinSV.han_the);
      const today = new Date();
      return hanThe < today;
    }
  },
  mounted() {
    // Lấy thông tin từ window.Laravel
    if (window.Laravel && window.Laravel.user && window.Laravel.user.isLogin) {
      this.hoTen = window.Laravel.user.hoTen || '';
      this.mssv = window.Laravel.user.mssv || '';
      this.idDocGia = window.Laravel.user.idDocGia || '';
      this.loadThongTinSinhVien();
      this.loadLichSuMuon();
    } else {
      this.error = "Không thể lấy thông tin sinh viên. Vui lòng đăng nhập lại.";
      this.loading = false;
    }
  },
  methods: {
    async loadThongTinSinhVien() {
      try {
        this.loading = true;
        this.error = null;
        
        // Nếu có id_doc_gia, sử dụng API để lấy thông tin chi tiết
        if (this.idDocGia) {
          const response = await axios.get(`/api/opac/thong-tin-sinh-vien/${this.idDocGia}`);
          if (response.data.status === 200) {
            this.thongTinSV = response.data.data;
          } else {
            this.error = response.data.message || 'Không thể lấy thông tin sinh viên';
          }
        } else {
          this.error = 'Không tìm thấy ID đọc giả';
        }
      } catch (error) {
        console.error('Lỗi khi lấy thông tin sinh viên:', error);
        this.error = 'Không thể kết nối đến máy chủ';
      } finally {
        this.loading = false;
      }
    },
    
    async loadLichSuMuon() {
      try {
        this.loadingHistory = true;
        this.errorHistory = null;
        
        if (this.idDocGia) {
          const response = await axios.get(`/api/opac/lich-su-muon/${this.idDocGia}`);
          if (response.data.status === 200) {
            this.lichSuMuon = response.data.data;
          } else {
            this.errorHistory = response.data.message || 'Không thể lấy lịch sử mượn sách';
          }
        } else {
          this.errorHistory = 'Không tìm thấy ID đọc giả';
        }
      } catch (error) {
        console.error('Lỗi khi lấy lịch sử mượn sách:', error);
        this.errorHistory = 'Không thể kết nối đến máy chủ';
      } finally {
        this.loadingHistory = false;
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
    },
    
    getStatusClass(status) {
      switch (status?.toLowerCase()) {
        case 'đã trả':
          return 'badge badge-success';
        case 'đang mượn':
          return 'badge badge-primary';
        case 'quá hạn':
          return 'badge badge-danger';
        case 'đọc tại chỗ':
          return 'badge badge-info';
        default:
          return 'badge badge-secondary';
      }
    },
    
    getRutHanText(rutHan) {
      if (rutHan === null || rutHan === undefined) return 'Chưa cập nhật';
      return parseInt(rutHan) === 1 ? 'Mở khóa' : 'Rút hạn';
    },
    
    getRutHanClass(rutHan) {
      if (rutHan === null || rutHan === undefined) return 'badge badge-secondary';
      return parseInt(rutHan) === 1 ? 'badge badge-success' : 'badge badge-danger';
    }
  }
};
</script>

<style scoped>
.card {
  border: none;
  box-shadow: 0 0.25rem 0.75rem rgba(0, 0, 0, 0.1);
  border-radius: 0.5rem;
  overflow: hidden;
  transition: transform 0.3s, box-shadow 0.3s;
}

.card:hover {
  transform: translateY(-5px);
  box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
}

.card-header {
  padding: 1rem 1.25rem;
  border-bottom: none;
}

.bg-gradient-primary {
  background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
}

.avatar-circle {
  width: 120px;
  height: 120px;
  background-color: #f0f4f8;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto;
  box-shadow: 0 0.25rem 0.75rem rgba(0, 0, 0, 0.1);
  border: 5px solid rgba(0, 123, 255, 0.1);
  transition: transform 0.3s;
}

.avatar-circle:hover {
  transform: scale(1.05);
}

.info-list {
  margin-top: 1.5rem;
}

.info-item {
  margin-bottom: 1rem;
  display: flex;
  align-items: flex-start;
  padding: 0.5rem;
  border-radius: 0.5rem;
  transition: background-color 0.2s;
}

.info-item:hover {
  background-color: rgba(0, 123, 255, 0.05);
}

.info-icon {
  width: 40px;
  height: 40px;
  background-color: rgba(0, 123, 255, 0.1);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-right: 1rem;
  color: #007bff;
}

.info-content {
  flex: 1;
}

.info-label {
  font-weight: 500;
  color: #6c757d;
  display: block;
  font-size: 0.85rem;
}

.info-value {
  color: #343a40;
  font-weight: 500;
}

/* Thẻ thư viện */
.library-card {
  background-color: white;
  border-radius: 0.5rem;
  overflow: hidden;
  box-shadow: 0 0.25rem 0.75rem rgba(0, 0, 0, 0.1);
  border: 1px solid rgba(0, 0, 0, 0.125);
}

.library-card-header {
  background-color: #007bff;
  color: white;
  padding: 1rem;
}

.library-logo {
  width: 50px;
  height: 50px;
  background-color: white;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #007bff;
  font-size: 1.5rem;
}

.library-card-body {
  padding: 1.5rem;
}

.avatar-placeholder {
  width: 100%;
  height: 150px;
  background-color: #f8f9fa;
  border-radius: 0.5rem;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #adb5bd;
  border: 1px dashed #dee2e6;
}

.card-info {
  margin-bottom: 1rem;
}

.card-info-item {
  margin-bottom: 0.5rem;
}

.card-info-label {
  font-weight: 500;
  color: #6c757d;
  margin-right: 0.5rem;
}

.card-info-value {
  font-weight: 600;
  color: #343a40;
}

/* Thông tin bổ sung */
.additional-info h6 {
  font-weight: 600;
  border-bottom: 2px solid rgba(0, 123, 255, 0.2);
  padding-bottom: 0.5rem;
}

.info-box {
  display: flex;
  align-items: center;
  margin-bottom: 1.25rem;
  background-color: #f8f9fa;
  border-radius: 0.5rem;
  padding: 1rem;
  transition: background-color 0.2s;
}

.info-box:hover {
  background-color: #e9ecef;
}

.info-box-icon {
  width: 40px;
  height: 40px;
  background-color: rgba(0, 123, 255, 0.1);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-right: 1rem;
  color: #007bff;
}

.info-box-content {
  flex: 1;
}

.info-box-label {
  display: block;
  font-size: 0.85rem;
  color: #6c757d;
  margin-bottom: 0.25rem;
}

.info-box-value {
  font-weight: 600;
  color: #343a40;
}

.note-box {
  background-color: #fff3cd;
  border-left: 4px solid #ffc107;
  border-radius: 0.25rem;
  padding: 1rem;
}

.note-header {
  font-weight: 600;
  color: #856404;
  margin-bottom: 0.5rem;
}

.note-content {
  color: #856404;
}

.text-danger {
  color: #dc3545 !important;
}

.badge {
  padding: 0.5em 0.75em;
  font-weight: 500;
  font-size: 0.75rem;
}

.badge-primary {
  background-color: #007bff;
}

.badge-danger {
  background-color: #dc3545;
}

@media (max-width: 767.98px) {
  .info-item {
    flex-direction: column;
  }
  
  .info-icon {
    margin-bottom: 0.5rem;
    margin-right: 0;
  }
  
  .library-card-header h5 {
    font-size: 1rem;
  }
}
</style> 