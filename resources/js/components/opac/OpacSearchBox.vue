<template>
  <div class="search-box-container">
    <div class="search-box">
      <h3 class="search-title">Tra cứu tài liệu</h3>
      <form @submit.prevent="searchDocuments">
        <div class="main-search">
          <div class="input-group mb-3">
            <input v-model="searchQuery" type="text" class="form-control" placeholder="Nhập từ khóa tìm kiếm...">
            <button class="btn btn-primary" type="submit">
              <i class="bi bi-search me-1"></i> Tìm kiếm
            </button>
          </div>
        </div>
        <div class="search-options">
          <div class="row g-2">
            <div class="col-md-4">
              <select v-model="searchType" class="form-select">
                <option value="all">Tất cả</option>
                <option value="title">Tựa đề</option>
                <option value="author">Tác giả</option>
                <option value="subject">Chủ đề</option>
                <option value="publisher">Nhà xuất bản</option>
              </select>
            </div>
            <div class="col-md-4">
              <select v-model="documentType" class="form-select">
                <option value="all">Tất cả tài liệu</option>
                <option value="book">Sách</option>
                <option value="thesis">Luận văn</option>
                <option value="journal">Báo/Tạp chí</option>
                <option value="ebook">Tài liệu điện tử</option>
              </select>
            </div>
            <div class="col-md-4">
              <div class="form-check form-switch">
                <input v-model="fullTextOnly" class="form-check-input" type="checkbox" id="fullTextOnly">
                <label class="form-check-label" for="fullTextOnly">Chỉ hiển thị tài liệu có toàn văn</label>
              </div>
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
.search-box-container {
  margin-bottom: 30px;
}

.search-box {
  background-color: #ffffff;
  border-radius: 8px;
  padding: 20px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
  border: 1px solid #eaeaea;
}

.search-title {
  color: #2c3e50;
  font-size: 1.5rem;
  font-weight: 600;
  margin-bottom: 20px;
  text-align: center;
  position: relative;
}

.search-title:after {
  content: '';
  display: block;
  width: 60px;
  height: 3px;
  background-color: #3498db;
  margin: 10px auto 20px;
  border-radius: 2px;
}

.main-search .form-control {
  height: 45px;
  border-radius: 4px 0 0 4px;
  font-size: 16px;
  border: 1px solid #ddd;
}

.main-search .btn {
  border-radius: 0 4px 4px 0;
  padding: 10px 20px;
  font-weight: 500;
}

.search-options {
  background-color: #f9f9f9;
  border-radius: 6px;
  padding: 15px;
  margin-top: 10px;
  border: 1px solid #eeeeee;
}

.search-options .form-select {
  height: 40px;
  border: 1px solid #ddd;
  border-radius: 4px;
}

.form-check-input:checked {
  background-color: #3498db;
  border-color: #3498db;
}

@media (max-width: 768px) {
  .search-options .row > div {
    margin-bottom: 10px;
  }
}
</style> 