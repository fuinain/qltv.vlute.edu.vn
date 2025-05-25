<template>
  <div class="card card-primary card-outline">
    <div class="card-header">
      <h3 class="card-title">
        <i class="fas fa-search mr-2"></i>Tra cứu tài liệu
      </h3>
    </div>
    <div class="card-body">
      <form @submit.prevent="searchDocuments">
        <div class="input-group mb-3">
          <input v-model="searchQuery" type="text" class="form-control" placeholder="Nhập từ khóa tìm kiếm...">
          <div class="input-group-append">
            <button class="btn btn-primary" type="submit">
              <i class="fas fa-search mr-1"></i> Tìm kiếm
            </button>
          </div>
        </div>
        
        <div class="search-options p-3 bg-light">
          <div class="row">
            <div class="col-md-6 mb-2">
              <label class="text-muted mb-1">Tìm theo</label>
              <select v-model="searchType" class="form-control">
                <option value="all">Tất cả</option>
                <option value="nhan_de">Nhan đề</option>
                <option value="tac_gia">Tác giả</option>
                <option value="nam_xuat_ban">Năm xuất bản</option>
                <option value="nha_xuat_ban">Nhà xuất bản</option>
                <option value="noi_xuat_ban">Nơi xuất bản</option>
              </select>
            </div>
            <div class="col-md-6 mb-2">
              <label class="text-muted mb-1">Loại tài liệu</label>
              <select v-model="documentType" class="form-control">
                <option value="">Tất cả tài liệu</option>
                <option v-for="taiLieu in danhSachTaiLieu" :key="taiLieu.id_tai_lieu" :value="taiLieu.id_tai_lieu">
                  {{ taiLieu.ten_tai_lieu }}
                </option>
              </select>
            </div>
          </div>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  name: "OpacSearchBox",
  props: {
    initialSearchParams: {
      type: Object,
      default: () => ({
        query: '',
        type: 'all',
        docType: '',
        fullTextOnly: false
      })
    }
  },
  data() {
    return {
      searchQuery: this.initialSearchParams.query || '',
      searchType: this.initialSearchParams.type || 'all',
      documentType: this.initialSearchParams.docType || '',
      fullTextOnly: this.initialSearchParams.fullTextOnly || false,
      danhSachTaiLieu: [],
      loading: false
    }
  },
  mounted() {
    this.loadDanhSachTaiLieu();
  },
  methods: {
    async loadDanhSachTaiLieu() {
      try {
        this.loading = true;
        const response = await axios.get('/api/opac/danh-sach-tai-lieu');
        if (response.data.status === 200) {
          this.danhSachTaiLieu = response.data.data;
        } else {
          console.error('Lỗi khi lấy danh sách tài liệu:', response.data);
        }
      } catch (error) {
        console.error('Lỗi khi gọi API danh sách tài liệu:', error);
      } finally {
        this.loading = false;
      }
    },
    searchDocuments() {
      // Nếu chọn loại tài liệu cụ thể
      if (this.documentType) {
        // Chuyển đến trang danh sách sách theo tài liệu
        this.$router.push({
          name: 'OpacDanhSachSachTheoTaiLieu',
          params: { id: this.documentType },
          query: { search: this.searchQuery }
        });
      } else {
        // Chuyển đến trang danh sách sách tổng hợp
        this.$router.push({
          name: 'OpacDanhSachSach',
          query: { 
            search: this.searchQuery,
            type: this.searchType
          }
        });
      }
      
      // Emit sự kiện search lên parent component (để dùng khi cần)
      this.$emit('search', {
        query: this.searchQuery,
        type: this.searchType,
        docType: this.documentType,
        fullTextOnly: this.fullTextOnly
      });
    }
  },
  watch: {
    initialSearchParams: {
      handler(newVal) {
        if (newVal) {
          this.searchQuery = newVal.query || '';
          this.searchType = newVal.type || 'all';
          this.documentType = newVal.docType || '';
          this.fullTextOnly = newVal.fullTextOnly || false;
        }
      },
      deep: true
    }
  }
}
</script>

<style scoped>
.search-options {
  border-radius: 4px;
  margin-top: 5px;
}

.search-options label {
  font-size: 0.9rem;
}

.icheck-primary {
  margin-bottom: 0;
}

.input-group {
  width: 100%;
}

.input-group .form-control {
  flex: 1;
}

.input-group-append {
  margin-left: auto;
}

@media (max-width: 768px) {
  .search-options .row > div {
    margin-bottom: 10px;
  }
}
</style> 