<template>
  <div class="container-xl mt-4 mb-4">
    <div class="row">
      <!-- Sidebar -->
      <div class="col-md-3">
        <OpacSidebar />
      </div>
      
      <!-- Main content -->
      <div class="col-md-9">
        <!-- Tìm kiếm -->
        <OpacSearchBox @search="performSearch" :initialSearchParams="searchParams" />
        
        <!-- Kết quả tìm kiếm -->
        <div class="card">
          <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Kết quả tìm kiếm ({{ totalResults }})</h5>
            <div>
              <select v-model="sortOption" class="form-select form-select-sm" style="width: auto; display: inline-block;">
                <option value="relevance">Theo độ liên quan</option>
                <option value="newest">Mới nhất</option>
                <option value="title_asc">Tựa đề (A-Z)</option>
                <option value="title_desc">Tựa đề (Z-A)</option>
              </select>
            </div>
          </div>
          <div class="card-body">
            <div v-if="loading" class="text-center py-5">
              <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Đang tải...</span>
              </div>
              <p class="mt-2">Đang tìm kiếm...</p>
            </div>
            <div v-else-if="searchResults.length === 0" class="text-center py-5">
              <i class="bi bi-search fs-1 text-muted"></i>
              <p class="mt-2">Không tìm thấy kết quả nào phù hợp với từ khóa "{{ searchParams.query }}"</p>
              <p>Vui lòng thử lại với từ khóa khác hoặc điều chỉnh tiêu chí tìm kiếm</p>
            </div>
            <div v-else>
              <!-- Danh sách kết quả -->
              <div v-for="(result, index) in searchResults" :key="index" class="search-result-item mb-3 p-3 border-bottom">
                <div class="row">
                  <div class="col-md-2">
                    <img :src="result.coverImage || 'https://placehold.co/120x160'" class="img-fluid" alt="Book cover">
                  </div>
                  <div class="col-md-10">
                    <h5>{{ result.title }}</h5>
                    <p class="mb-1">
                      <strong>Tác giả:</strong> {{ result.author }}
                    </p>
                    <p class="mb-1">
                      <strong>Nhà xuất bản:</strong> {{ result.publisher }}, {{ result.year }}
                    </p>
                    <p class="mb-1">
                      <strong>Chủ đề:</strong> {{ result.subject }}
                    </p>
                    <p class="mb-1">
                      <strong>Mô tả:</strong> {{ result.description.length > 150 ? result.description.substring(0, 150) + '...' : result.description }}
                    </p>
                    <div class="mt-2">
                      <span :class="['badge', result.available ? 'bg-success' : 'bg-danger']">
                        {{ result.available ? 'Còn sách' : 'Đã mượn hết' }}
                      </span>
                      <span class="badge bg-info ms-2">
                        {{ result.documentType }}
                      </span>
                      <span v-if="result.hasFullText" class="badge bg-primary ms-2">
                        Có toàn văn
                      </span>
                    </div>
                    <div class="mt-2">
                      <button class="btn btn-sm btn-outline-primary">Xem chi tiết</button>
                      <button v-if="result.hasFullText" class="btn btn-sm btn-outline-success ms-2">Xem toàn văn</button>
                      <button v-if="result.available" class="btn btn-sm btn-outline-info ms-2">Đặt mượn</button>
                    </div>
                  </div>
                </div>
              </div>
              
              <!-- Phân trang -->
              <div class="d-flex justify-content-center mt-4">
                <nav aria-label="Page navigation">
                  <ul class="pagination">
                    <li :class="['page-item', currentPage === 1 ? 'disabled' : '']">
                      <a class="page-link" href="#" @click.prevent="changePage(currentPage - 1)">
                        <span aria-hidden="true">&laquo;</span>
                      </a>
                    </li>
                    <li v-for="page in totalPages" :key="page" :class="['page-item', currentPage === page ? 'active' : '']">
                      <a class="page-link" href="#" @click.prevent="changePage(page)">{{ page }}</a>
                    </li>
                    <li :class="['page-item', currentPage === totalPages ? 'disabled' : '']">
                      <a class="page-link" href="#" @click.prevent="changePage(currentPage + 1)">
                        <span aria-hidden="true">&raquo;</span>
                      </a>
                    </li>
                  </ul>
                </nav>
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
import OpacSearchBox from "@/components/opac/OpacSearchBox.vue";

export default {
  name: "OpacSearchResult",
  components: {
    OpacSidebar,
    OpacSearchBox
  },
  data() {
    return {
      searchParams: {
        query: '',
        type: 'all',
        docType: 'all',
        fullTextOnly: false
      },
      loading: true,
      searchResults: [],
      totalResults: 0,
      currentPage: 1,
      totalPages: 1,
      sortOption: 'relevance',
      // Mock data cho mục đích demo
      mockSearchResults: [
        {
          id: 1,
          title: 'Lập trình hướng đối tượng với Java',
          author: 'Nguyễn Văn A',
          publisher: 'NXB Giáo dục',
          year: '2021',
          subject: 'Công nghệ thông tin, Lập trình Java',
          description: 'Cuốn sách giới thiệu về các nguyên lý cơ bản của lập trình hướng đối tượng và cách áp dụng chúng trong ngôn ngữ lập trình Java. Sách bao gồm nhiều ví dụ thực tế và bài tập thực hành.',
          coverImage: 'https://placehold.co/120x160/3498db/ffffff?text=Java',
          available: true,
          documentType: 'Sách',
          hasFullText: true
        },
        {
          id: 2,
          title: 'Cơ sở dữ liệu nâng cao',
          author: 'Trần Văn B',
          publisher: 'NXB Khoa học Kỹ thuật',
          year: '2020',
          subject: 'Cơ sở dữ liệu, Công nghệ thông tin',
          description: 'Sách đề cập đến các kiến thức nâng cao về cơ sở dữ liệu, bao gồm các chủ đề như tối ưu hóa truy vấn, thiết kế cơ sở dữ liệu phân tán, xử lý giao dịch và an toàn dữ liệu.',
          coverImage: 'https://placehold.co/120x160/e74c3c/ffffff?text=SQL',
          available: false,
          documentType: 'Sách',
          hasFullText: false
        },
        {
          id: 3,
          title: 'Phân tích và thiết kế hệ thống thông tin',
          author: 'Lê Thị C',
          publisher: 'NXB Đại học Quốc gia',
          year: '2019',
          subject: 'Công nghệ thông tin, Hệ thống thông tin',
          description: 'Cuốn sách trình bày phương pháp luận và các kỹ thuật phân tích, thiết kế hệ thống thông tin hiện đại. Sách đi sâu vào các quy trình, kỹ thuật mô hình hóa và các công cụ CASE.',
          coverImage: 'https://placehold.co/120x160/2ecc71/ffffff?text=System',
          available: true,
          documentType: 'Giáo trình',
          hasFullText: true
        },
        {
          id: 4,
          title: 'Mạng máy tính và truyền thông dữ liệu',
          author: 'Phạm Văn D',
          publisher: 'NXB Bách khoa Hà Nội',
          year: '2022',
          subject: 'Mạng máy tính, Công nghệ thông tin',
          description: 'Sách giới thiệu các kiến thức cơ bản và nâng cao về mạng máy tính, các giao thức truyền thông, kiến trúc mạng và các ứng dụng hiện đại.',
          coverImage: 'https://placehold.co/120x160/9b59b6/ffffff?text=Network',
          available: true,
          documentType: 'Sách',
          hasFullText: false
        },
        {
          id: 5,
          title: 'Trí tuệ nhân tạo và ứng dụng',
          author: 'Võ Thị E',
          publisher: 'NXB Khoa học và Kỹ thuật',
          year: '2023',
          subject: 'Trí tuệ nhân tạo, Công nghệ thông tin',
          description: 'Cuốn sách trình bày tổng quan về trí tuệ nhân tạo, các thuật toán học máy, học sâu và các ứng dụng trong thực tế như xử lý ngôn ngữ tự nhiên, thị giác máy tính và robot.',
          coverImage: 'https://placehold.co/120x160/f1c40f/ffffff?text=AI',
          available: false,
          documentType: 'Sách',
          hasFullText: true
        }
      ]
    }
  },
  mounted() {
    // Lấy thông tin tìm kiếm từ query parameters nếu có
    const queryParams = new URLSearchParams(window.location.search);
    if (queryParams.has('query')) {
      this.searchParams.query = queryParams.get('query');
    }
    if (queryParams.has('type')) {
      this.searchParams.type = queryParams.get('type');
    }
    if (queryParams.has('docType')) {
      this.searchParams.docType = queryParams.get('docType');
    }
    if (queryParams.has('fullTextOnly')) {
      this.searchParams.fullTextOnly = queryParams.get('fullTextOnly') === 'true';
    }
    
    // Tìm kiếm với tham số đã có
    this.performSearch(this.searchParams);
  },
  methods: {
    performSearch(params) {
      // Cập nhật tham số tìm kiếm
      this.searchParams = {...params};
      
      // Hiển thị trạng thái loading
      this.loading = true;
      
      // Giả lập gọi API tìm kiếm (trong thực tế sẽ gọi API thực sự)
      setTimeout(() => {
        // Lọc kết quả giả lập dựa trên params
        let filteredResults = [...this.mockSearchResults];
        
        // Lọc theo từ khóa
        if (this.searchParams.query) {
          const keyword = this.searchParams.query.toLowerCase();
          filteredResults = filteredResults.filter(item => {
            return item.title.toLowerCase().includes(keyword) || 
                   item.author.toLowerCase().includes(keyword) || 
                   item.description.toLowerCase().includes(keyword) ||
                   item.subject.toLowerCase().includes(keyword);
          });
        }
        
        // Lọc theo loại tài liệu
        if (this.searchParams.docType !== 'all') {
          const docTypeMap = {
            'book': 'Sách',
            'thesis': 'Luận văn',
            'journal': 'Tạp chí',
            'ebook': 'Sách điện tử'
          };
          filteredResults = filteredResults.filter(item => 
            item.documentType === docTypeMap[this.searchParams.docType]
          );
        }
        
        // Lọc theo toàn văn
        if (this.searchParams.fullTextOnly) {
          filteredResults = filteredResults.filter(item => item.hasFullText);
        }
        
        // Sắp xếp kết quả
        switch (this.sortOption) {
          case 'newest':
            filteredResults.sort((a, b) => parseInt(b.year) - parseInt(a.year));
            break;
          case 'title_asc':
            filteredResults.sort((a, b) => a.title.localeCompare(b.title));
            break;
          case 'title_desc':
            filteredResults.sort((a, b) => b.title.localeCompare(a.title));
            break;
          // Mặc định là theo độ liên quan, giữ nguyên
        }
        
        // Cập nhật kết quả
        this.searchResults = filteredResults;
        this.totalResults = filteredResults.length;
        this.totalPages = Math.max(1, Math.ceil(this.totalResults / 10));
        this.currentPage = 1;
        
        // Kết thúc loading
        this.loading = false;
        
        // Cập nhật URL với tham số tìm kiếm
        this.updateQueryParams();
      }, 1000);
    },
    changePage(page) {
      if (page < 1 || page > this.totalPages) return;
      this.currentPage = page;
      // Trong thực tế, sẽ gọi API để lấy dữ liệu cho trang mới
    },
    updateQueryParams() {
      // Cập nhật URL với tham số tìm kiếm mà không reload trang
      const queryParams = new URLSearchParams();
      if (this.searchParams.query) {
        queryParams.set('query', this.searchParams.query);
      }
      if (this.searchParams.type !== 'all') {
        queryParams.set('type', this.searchParams.type);
      }
      if (this.searchParams.docType !== 'all') {
        queryParams.set('docType', this.searchParams.docType);
      }
      if (this.searchParams.fullTextOnly) {
        queryParams.set('fullTextOnly', 'true');
      }
      
      const newUrl = `${window.location.pathname}?${queryParams.toString()}`;
      window.history.replaceState({}, '', newUrl);
    }
  },
  watch: {
    sortOption() {
      // Khi thay đổi cách sắp xếp, thực hiện lại tìm kiếm
      this.performSearch(this.searchParams);
    }
  }
}
</script>

<style scoped>
.search-result-item:hover {
  background-color: #f8f9fa;
}
.search-result-item:last-child {
  border-bottom: none !important;
}
</style> 