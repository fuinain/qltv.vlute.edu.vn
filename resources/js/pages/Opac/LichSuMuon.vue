<template>
  <div class="container-xl py-4">
    <div class="row">
      <div class="col-md-12">
        <!-- Bảng sách đang mượn -->
        <div class="card mb-4">
          <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-book mr-2"></i> Sách đang mượn</h5>
          </div>
          <div class="card-body p-0">
            <div v-if="loading" class="text-center p-5">
              <div class="spinner-border text-primary" role="status"></div>
              <p class="mt-2">Đang tải dữ liệu...</p>
            </div>

            <div v-else-if="error" class="alert alert-danger m-3">
              {{ error }}
            </div>

            <div v-else-if="sachDangMuon.length === 0" class="text-center p-5">
              <i class="fas fa-book fa-4x text-muted mb-3"></i>
              <p>Bạn chưa mượn sách nào.</p>
            </div>

            <div v-else class="table-responsive">
              <table class="table table-hover">
                <thead>
                  <tr>
                    <th width="5%">#</th>
                    <th width="25%">Tên sách</th>
                    <th width="15%">Mã ĐKCB</th>
                    <th width="15%">Ngày mượn</th>
                    <th width="15%">Hạn trả</th>
                    <th width="10%">Trạng thái</th>
                    <th width="15%">Hành động</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(item, index) in sachDangMuon" :key="index" :class="getRowClass(item)">
                    <td>{{ index + 1 }}</td>
                    <td>
                      <router-link v-if="item.id_sach" :to="{ name: 'OpacChiTietSach', params: { id: item.id_sach }}" class="text-primary font-weight-medium">
                        {{ item.ten_sach }}
                      </router-link>
                      <span v-else class="text-primary font-weight-medium">{{ item.ten_sach }}</span>
                      <p v-if="item.tac_gia" class="small text-muted mb-0">{{ item.tac_gia }}</p>
                    </td>
                    <td>{{ item.ma_dkcb }}</td>
                    <td>{{ formatDate(item.ngay_muon) }}</td>
                    <td>{{ formatDate(item.han_tra) }}</td>
                    <td>
                      <span :class="getStatusClass(item.trang_thai)">{{ item.trang_thai }}</span>
                    </td>
                    <td>
                      <button 
                        v-if="coTheGiaHan(item)" 
                        class="btn btn-sm btn-success" 
                        @click="giaHanSach(item)"
                        :disabled="dangXuLy"
                      >
                        <i class="fas fa-sync-alt mr-1"></i> Gia hạn
                      </button>
                      <span v-else-if="item.gia_han > 0" class="badge badge-secondary">
                        Đã gia hạn
                      </span>
                      <span v-else class="badge badge-info">
                        Chưa đến hạn
                      </span>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <!-- Lịch sử mượn sách -->
        <div class="card">
          <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-history mr-2"></i> Lịch sử mượn sách</h5>
          </div>
          <div class="card-body p-0">
            <div v-if="loading" class="text-center p-5">
              <div class="spinner-border text-primary" role="status"></div>
              <p class="mt-2">Đang tải lịch sử mượn sách...</p>
            </div>

            <div v-else-if="error" class="alert alert-danger m-3">
              {{ error }}
            </div>

            <div v-else-if="sachDaTra.length === 0" class="text-center p-5">
              <i class="fas fa-book fa-4x text-muted mb-3"></i>
              <p>Bạn chưa có lịch sử mượn sách nào.</p>
            </div>

            <div v-else class="table-responsive">
              <table class="table table-hover">
                <thead>
                  <tr>
                    <th width="5%">#</th>
                    <th width="30%">Tên sách</th>
                    <th width="15%">Mã ĐKCB</th>
                    <th width="15%">Ngày mượn</th>
                    <th width="15%">Hạn trả</th>
                    <th width="10%">Ngày trả</th>
                    <th width="10%">Trạng thái</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(item, index) in paginatedSachDaTra" :key="index">
                    <td>{{ (currentPage - 1) * itemsPerPage + index + 1 }}</td>
                    <td>
                      <router-link v-if="item.id_sach" :to="{ name: 'OpacChiTietSach', params: { id: item.id_sach }}" class="text-primary font-weight-medium">
                        {{ item.ten_sach }}
                      </router-link>
                      <span v-else class="text-primary font-weight-medium">{{ item.ten_sach }}</span>
                      <p v-if="item.tac_gia" class="small text-muted mb-0">{{ item.tac_gia }}</p>
                    </td>
                    <td>{{ item.ma_dkcb }}</td>
                    <td>{{ formatDate(item.ngay_muon) }}</td>
                    <td>{{ formatDate(item.han_tra) }}</td>
                    <td>{{ formatDate(item.ngay_tra) }}</td>
                    <td>
                      <span :class="getStatusClass(item.trang_thai)">{{ item.trang_thai }}</span>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
            
            <!-- Phân trang -->
            <div v-if="totalPages > 1" class="card-footer">
              <div class="d-flex justify-content-between align-items-center">
                <div class="small text-muted">
                  Hiển thị {{ paginatedSachDaTra.length }} trong tổng số {{ sachDaTra.length }} kết quả
                </div>
                <nav aria-label="Page navigation">
                  <ul class="pagination mb-0">
                    <li class="page-item" :class="{ disabled: currentPage === 1 }">
                      <a class="page-link" href="#" @click.prevent="changePage(currentPage - 1)">
                        <i class="fas fa-chevron-left"></i>
                      </a>
                    </li>
                    <li v-for="page in displayedPages" :key="page" class="page-item" :class="{ active: page === currentPage }">
                      <a class="page-link" href="#" @click.prevent="changePage(page)">{{ page }}</a>
                    </li>
                    <li class="page-item" :class="{ disabled: currentPage === totalPages }">
                      <a class="page-link" href="#" @click.prevent="changePage(currentPage + 1)">
                        <i class="fas fa-chevron-right"></i>
                      </a>
                    </li>
                  </ul>
                </nav>
              </div>
            </div>
          </div>
        </div>

        <!-- Thống kê mượn sách -->
        <div class="row mt-4">
          <div class="col-md-4">
            <div class="small-box bg-info">
              <div class="inner">
                <h3>{{ sachDangMuon.length }}</h3>
                <p>Đang mượn</p>
              </div>
              <div class="icon">
                <i class="fas fa-book"></i>
              </div>
            </div>
          </div>
          <div class="col-md-4">
            <div class="small-box bg-success">
              <div class="inner">
                <h3>{{ sachDaTra.length }}</h3>
                <p>Đã trả</p>
              </div>
              <div class="icon">
                <i class="fas fa-check-circle"></i>
              </div>
            </div>
          </div>
          <div class="col-md-4">
            <div class="small-box bg-danger">
              <div class="inner">
                <h3>{{ sachQuaHan.length }}</h3>
                <p>Quá hạn</p>
              </div>
              <div class="icon">
                <i class="fas fa-exclamation-circle"></i>
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
import Swal from 'sweetalert2';

export default {
  name: "LichSuMuon",
  data() {
    return {
      idDocGia: '',
      lichSuMuon: [],
      loading: true,
      error: null,
      searchText: '',
      dangXuLy: false,
      
      // Phân trang
      currentPage: 1,
      itemsPerPage: 10
    };
  },
  computed: {
    // Lọc sách đang mượn
    sachDangMuon() {
      return this.lichSuMuon.filter(item => 
        item.trang_thai?.toLowerCase() === 'đang mượn' || 
        item.trang_thai?.toLowerCase() === 'quá hạn'
      );
    },
    
    // Lọc sách đã trả
    sachDaTra() {
      return this.lichSuMuon.filter(item => 
        item.trang_thai?.toLowerCase() === 'đã trả'
      );
    },
    
    // Lọc sách quá hạn
    sachQuaHan() {
      return this.lichSuMuon.filter(item => 
        item.trang_thai?.toLowerCase() === 'quá hạn'
      );
    },
    
    // Tính toán số trang
    totalPages() {
      return Math.ceil(this.sachDaTra.length / this.itemsPerPage);
    },
    
    // Danh sách các trang để hiển thị
    displayedPages() {
      const range = 2; // Số trang hiển thị trước và sau trang hiện tại
      let start = Math.max(1, this.currentPage - range);
      let end = Math.min(this.totalPages, this.currentPage + range);
      
      // Điều chỉnh để luôn hiển thị 5 trang nếu có thể
      if (end - start + 1 < 5 && this.totalPages > 5) {
        if (start === 1) {
          end = Math.min(this.totalPages, 5);
        } else if (end === this.totalPages) {
          start = Math.max(1, this.totalPages - 4);
        }
      }
      
      const pages = [];
      for (let i = start; i <= end; i++) {
        pages.push(i);
      }
      return pages;
    },
    
    // Các mục cho trang hiện tại
    paginatedSachDaTra() {
      const start = (this.currentPage - 1) * this.itemsPerPage;
      const end = start + this.itemsPerPage;
      return this.sachDaTra.slice(start, end);
    }
  },
  mounted() {
    // Lấy thông tin từ window.Laravel
    if (window.Laravel && window.Laravel.user && window.Laravel.user.isLogin) {
      this.idDocGia = window.Laravel.user.idDocGia || '';
      this.loadLichSuMuon();
    } else {
      this.error = "Không thể lấy thông tin sinh viên. Vui lòng đăng nhập lại.";
      this.loading = false;
    }
  },
  methods: {
    async loadLichSuMuon() {
      try {
        this.loading = true;
        this.error = null;
        
        if (this.idDocGia) {
          const response = await axios.get(`/api/opac/lich-su-muon/${this.idDocGia}`);
          if (response.data.status === 200) {
            this.lichSuMuon = response.data.data;
          } else {
            this.error = response.data.message || 'Không thể lấy lịch sử mượn sách';
          }
        } else {
          this.error = 'Không tìm thấy ID đọc giả';
        }
      } catch (error) {
        console.error('Lỗi khi lấy lịch sử mượn sách:', error);
        this.error = 'Không thể kết nối đến máy chủ';
      } finally {
        this.loading = false;
      }
    },
    
    async giaHanSach(item) {
      try {
        this.dangXuLy = true;
        
        const response = await axios.put(`/api/opac/gia-han-sach/${item.id_muon_sach}`);
        
        if (response.data.status === 200) {
          // Cập nhật thông tin sách trong danh sách
          const index = this.lichSuMuon.findIndex(sach => sach.id_muon_sach === item.id_muon_sach);
          if (index !== -1) {
            this.lichSuMuon[index].han_tra = response.data.data.han_tra;
            this.lichSuMuon[index].gia_han = response.data.data.gia_han;
          }
          
          // Hiển thị thông báo thành công với SweetAlert2
          Swal.fire({
            icon: 'success',
            title: 'Gia hạn thành công',
            text: response.data.message,
            confirmButtonText: 'Đóng',
            confirmButtonColor: '#28a745'
          });
        } else {
          // Hiển thị thông báo lỗi với SweetAlert2
          Swal.fire({
            icon: 'error',
            title: 'Không thể gia hạn',
            text: response.data.message,
            confirmButtonText: 'Đóng',
            confirmButtonColor: '#dc3545'
          });
        }
      } catch (error) {
        console.error('Lỗi khi gia hạn sách:', error);
        let errorMessage = 'Không thể kết nối đến máy chủ';
        
        if (error.response && error.response.data) {
          errorMessage = error.response.data.message || errorMessage;
        }
        
        // Hiển thị thông báo lỗi với SweetAlert2
        Swal.fire({
          icon: 'error',
          title: 'Lỗi',
          text: errorMessage,
          confirmButtonText: 'Đóng',
          confirmButtonColor: '#dc3545'
        });
      } finally {
        this.dangXuLy = false;
      }
    },
    
    coTheGiaHan(item) {
      // Chỉ cho phép gia hạn khi chưa gia hạn (gia_han = 0) và đã đến hạn trả
      if (item.gia_han > 0 || !item.han_tra || item.trang_thai?.toLowerCase() !== 'đang mượn') {
        return false;
      }
      
      const today = new Date();
      const hanTra = new Date(item.han_tra);
      
      // So sánh ngày (bỏ qua giờ, phút, giây)
      const todayDate = new Date(today.getFullYear(), today.getMonth(), today.getDate());
      const hanTraDate = new Date(hanTra.getFullYear(), hanTra.getMonth(), hanTra.getDate());
      
      // Chỉ cho phép gia hạn khi đã đến ngày hạn trả và chưa gia hạn trước đó
      return todayDate.getTime() >= hanTraDate.getTime();
    },
    
    changePage(page) {
      if (page >= 1 && page <= this.totalPages) {
        this.currentPage = page;
        // Cuộn lên đầu bảng
        window.scrollTo(0, this.$el.offsetTop);
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
    
    getRowClass(item) {
      switch (item.trang_thai?.toLowerCase()) {
        case 'quá hạn':
          return 'table-danger';
        case 'đang mượn':
          return 'table-info';
        case 'đọc tại chỗ':
          return 'table-primary';
        default:
          return '';
      }
    }
  }
};
</script>

<style scoped>
.page-title {
  font-size: 1.75rem;
  font-weight: 500;
}

.card {
  border: none;
  box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
  border-radius: 0.5rem;
  overflow: hidden;
}

.card-header {
  border-bottom: none;
}

.badge {
  padding: 0.5em 0.75em;
  font-weight: 500;
  font-size: 0.75rem;
}

.table th {
  background-color: #f8f9fa;
  font-weight: 500;
}

.font-weight-medium {
  font-weight: 500;
}

.small-box {
  border-radius: 0.5rem;
  position: relative;
  display: block;
  margin-bottom: 20px;
  box-shadow: 0 0 1px rgba(0,0,0,.125), 0 1px 3px rgba(0,0,0,.2);
  color: white;
}

.small-box > .inner {
  padding: 15px;
}

.small-box > .inner > h3 {
  font-size: 2.2rem;
  font-weight: 700;
  margin: 0 0 10px 0;
  white-space: nowrap;
  padding: 0;
}

.small-box > .inner > p {
  font-size: 1rem;
  margin-bottom: 0;
}

.small-box > .icon {
  position: absolute;
  top: 15px;
  right: 15px;
  font-size: 70px;
  color: rgba(255,255,255,0.2);
}

.bg-info {
  background-color: #17a2b8 !important;
}

.bg-success {
  background-color: #28a745 !important;
}

.bg-danger {
  background-color: #dc3545 !important;
}

@media (max-width: 767.98px) {
  .small-box {
    text-align: center;
  }
  
  .small-box > .icon {
    display: none;
  }
}
</style> 