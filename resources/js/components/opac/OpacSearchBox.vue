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
                <option value="all">Tất cả tài liệu</option>
                <option value="book">Sách</option>
                <option value="thesis">Luận văn</option>
                <option value="journal">Báo/Tạp chí</option>
                <option value="ebook">Tài liệu điện tử</option>
              </select>
            </div>
          </div>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
export default {
  name: "OpacSearchBox",
  props: {
    initialSearchParams: {
      type: Object,
      default: () => ({
        query: '',
        type: 'all',
        docType: 'all',
        fullTextOnly: false
      })
    }
  },
  data() {
    return {
      searchQuery: this.initialSearchParams.query || '',
      searchType: this.initialSearchParams.type || 'all',
      documentType: this.initialSearchParams.docType || 'all',
      fullTextOnly: this.initialSearchParams.fullTextOnly || false
    }
  },
  methods: {
    searchDocuments() {
      // Emit sự kiện search lên parent component
      this.$emit('search', {
        query: this.searchQuery,
        type: this.searchType,
        docType: this.documentType,
        fullTextOnly: this.fullTextOnly
      });
      
      // Log thông tin tìm kiếm (để debug)
      console.log('Tìm kiếm với thông số:', {
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
          this.documentType = newVal.docType || 'all';
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

@media (max-width: 768px) {
  .search-options .row > div {
    margin-bottom: 10px;
  }
}
</style> 