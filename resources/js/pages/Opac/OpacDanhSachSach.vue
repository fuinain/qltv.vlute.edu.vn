<template>
  <div class="container-xl mt-4 mb-5">
    <div class="row">
      <!-- Main content -->
      <div class="col-md-8">
        <div class="card">
          <div class="card-header bg-info text-white py-2">
            <div class="d-md-flex justify-content-between align-items-center">
              <h2 class="mb-md-0 mb-3">
                {{ isTaiLieuMode ? 'Danh sách ' + tenTaiLieu : 'Danh sách sách' }}
              </h2>
              <form class="form-inline" @submit.prevent="timKiem">
                <div class="input-group input-group-sm">
                  <input class="form-control form-control-navbar" type="search" v-model="searchQuery" 
                    placeholder="Tìm kiếm..." aria-label="Tìm kiếm"
                    title="Tìm kiếm theo tên sách, tác giả, nhà xuất bản, năm xuất bản...">
                  <div class="input-group-append">
                    <button class="btn btn-navbar bg-white" type="submit" title="Tìm kiếm">
                      <i class="fas fa-search"></i>
                    </button>
                  </div>
                </div>
              </form>
            </div>
          </div>
          <div class="card-body p-0">
            <div v-if="searchQuery && !loading" class="alert alert-info m-3">
              Kết quả tìm kiếm cho "<strong>{{ searchQuery }}</strong>"
            </div>
            <div v-if="loading" class="text-center p-5">
              <div class="spinner-border text-primary" role="status">
              </div>
            </div>
            <div v-else-if="error" class="alert alert-warning m-3">
              {{ error }}
              <div class="mt-2">
                <button class="btn btn-outline-primary btn-sm" @click="xoaTimKiem">
                  <i class="fas fa-times mr-1"></i> Xóa tìm kiếm và thử lại
                </button>
              </div>
            </div>
            <div v-else-if="danhSachSach.length === 0 && !searchQuery" class="text-center p-5">
              <p class="text-muted">Không có sách nào.</p>
            </div>
            <div v-else-if="danhSachSach.length === 0 && searchQuery" class="text-center p-5">
              <p class="text-muted">Không tìm thấy kết quả phù hợp với "<strong>{{ searchQuery }}</strong>".</p>
              <button class="btn btn-outline-primary btn-sm mt-2" @click="xoaTimKiem">
                <i class="fas fa-times mr-1"></i> Xóa tìm kiếm
              </button>
            </div>
            <div v-else>
              <div class="table-responsive">
                <table class="table table-striped table-hover mb-0">
                  <thead>
                    <tr>
                      <th width="100"></th>
                      <th></th>
                      <th width="100"></th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="sach in danhSachSach" :key="sach.id_sach">
                      <td class="text-center">
                        <img src="/images/image_opac/anh_sach.png" alt="Sách" width="60" height="60" class="rounded">
                      </td>
                      <td>
                        <h5 class="mb-1">{{ sach.nhan_de }}</h5>
                        <p class="mb-1 text-muted">
                          <strong>Tác giả:</strong> {{ sach.tac_gia || 'Không có thông tin' }}
                        </p>
                        <p class="mb-0 small text-muted">
                          <span v-if="sach.nha_xuat_ban"><strong>NXB:</strong> {{ sach.nha_xuat_ban }}</span>
                          <span v-if="sach.noi_xuat_ban"> - {{ sach.noi_xuat_ban }}</span>
                          <span v-if="sach.nam_xuat_ban"> - {{ sach.nam_xuat_ban }}</span>
                        </p>
                      </td>
                      <td>
                        <router-link :to="{ name: 'OpacChiTietSach', params: { id: sach.id_sach }}" class="btn btn-sm btn-outline-primary">
                          <i class="fas fa-info-circle"></i> Chi tiết
                        </router-link>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
              
              <!-- Phân trang -->
              <div class="d-flex justify-content-center p-3">
                <nav v-if="pagination.last_page > 1">
                  <ul class="pagination pagination-sm">
                    <li class="page-item" :class="{ disabled: pagination.current_page === 1 }">
                      <a class="page-link" href="#" @click.prevent="changePage(1)">
                        <i class="fas fa-angle-double-left"></i>
                      </a>
                    </li>
                    <li class="page-item" :class="{ disabled: pagination.current_page === 1 }">
                      <a class="page-link" href="#" @click.prevent="changePage(pagination.current_page - 1)">
                        <i class="fas fa-angle-left"></i>
                      </a>
                    </li>
                    
                    <li v-for="page in paginationRange" :key="page" class="page-item" :class="{ active: pagination.current_page === page }">
                      <a class="page-link" href="#" @click.prevent="changePage(page)">{{ page }}</a>
                    </li>
                    
                    <li class="page-item" :class="{ disabled: pagination.current_page === pagination.last_page }">
                      <a class="page-link" href="#" @click.prevent="changePage(pagination.current_page + 1)">
                        <i class="fas fa-angle-right"></i>
                      </a>
                    </li>
                    <li class="page-item" :class="{ disabled: pagination.current_page === pagination.last_page }">
                      <a class="page-link" href="#" @click.prevent="changePage(pagination.last_page)">
                        <i class="fas fa-angle-double-right"></i>
                      </a>
                    </li>
                  </ul>
                </nav>
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
import OpacSidebar from '@/components/opac/OpacSidebar.vue';
import axios from 'axios';

export default {
  name: 'OpacDanhSachSach',
  components: {
    OpacSidebar
  },
  data() {
    return {
      danhSachSach: [],
      tenTaiLieu: '',
      loading: true,
      error: null,
      searchQuery: '',
      searchType: 'all',
      pagination: {
        current_page: 1,
        last_page: 1,
        per_page: 10,
        total: 0
      },
      debounceTimer: null
    };
  },
  computed: {
    isTaiLieuMode() {
      return this.$route.name === 'OpacDanhSachSachTheoTaiLieu';
    },
    paginationRange() {
      const range = [];
      const delta = 2;
      const left = this.pagination.current_page - delta;
      const right = this.pagination.current_page + delta + 1;
      
      for (let i = 1; i <= this.pagination.last_page; i++) {
        if (i === 1 || i === this.pagination.last_page || (i >= left && i < right)) {
          range.push(i);
        }
      }
      
      return range;
    }
  },
  created() {
    // Kiểm tra xem có tham số tìm kiếm trong URL không
    if (this.$route.query.search) {
      this.searchQuery = this.$route.query.search;
    }
    if (this.$route.query.type) {
      this.searchType = this.$route.query.type;
    }
    this.loadData();
  },
  watch: {
    '$route'(to, from) {
      // Kiểm tra xem route thay đổi có liên quan đến tìm kiếm không
      if (to.query.search !== from.query.search || to.params.id !== from.params.id) {
        this.searchQuery = to.query.search || '';
        this.searchType = to.query.type || 'all';
        this.pagination.current_page = 1;
        this.loadData();
      }
    },
    searchQuery(newVal) {
      // Thực hiện tìm kiếm với debounce 300ms
      clearTimeout(this.debounceTimer);
      if (newVal === '') {
        // Nếu xóa từ khóa tìm kiếm, tải lại ngay lập tức
        this.pagination.current_page = 1;
        this.loadData();
      } else {
        this.debounceTimer = setTimeout(() => {
          this.pagination.current_page = 1;
          this.loadData();
        }, 300);
      }
    }
  },
  methods: {
    async loadData() {
      this.loading = true;
      this.error = null;
      
      try {
        let response;
        
        if (this.isTaiLieuMode) {
          // Lấy danh sách sách theo tài liệu
          const idTaiLieu = this.$route.params.id;
          response = await axios.get(`/api/opac/sach-theo-tai-lieu/${idTaiLieu}`, {
            params: {
              per_page: this.pagination.per_page,
              page: this.pagination.current_page,
              search: this.searchQuery
            }
          });
          
          if (response.data.status === 200) {
            this.tenTaiLieu = response.data.tenTaiLieu;
          }
        } else {
          // Lấy tất cả sách
          response = await axios.get('/api/opac/danh-sach-sach', {
            params: {
              per_page: this.pagination.per_page,
              page: this.pagination.current_page,
              search: this.searchQuery,
              type: this.searchType
            }
          });
        }
        
        if (response.data.status === 200) {
          this.danhSachSach = response.data.data.data;
          this.pagination = {
            current_page: response.data.data.current_page,
            last_page: response.data.data.last_page,
            per_page: response.data.data.per_page,
            total: response.data.data.total
          };
        } else {
          this.error = response.data.message || 'Có lỗi xảy ra khi tải danh sách sách';
        }
      } catch (error) {
        console.error('Lỗi khi tải danh sách sách:', error);
        this.error = 'Có lỗi xảy ra khi tìm kiếm. Vui lòng thử lại hoặc xóa từ khóa tìm kiếm.';
      } finally {
        this.loading = false;
      }
    },
    
    changePage(page) {
      if (page < 1 || page > this.pagination.last_page) return;
      
      this.pagination.current_page = page;
      this.loadData();
      
      // Cuộn lên đầu trang
      window.scrollTo(0, 0);
    },
    
    timKiem() {
      this.pagination.current_page = 1;
      this.loadData();
    },
    
    xoaTimKiem() {
      this.searchQuery = '';
      this.pagination.current_page = 1;
      this.loadData();
    }
  }
};
</script>

<style scoped>
.pagination {
  margin-bottom: 0;
}

.form-inline {
  margin-left: auto;
}

.input-group-sm {
  min-width: 220px;
}

@media (max-width: 767.98px) {
  .form-inline {
    margin-left: 0;
    width: 100%;
  }
  
  .input-group-sm {
    width: 100%;
    min-width: 100%;
  }
}
</style> 