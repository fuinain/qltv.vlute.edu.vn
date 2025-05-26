<template>
  <div class="container-xl mt-4 mb-5">
    <div class="row">
      <!-- Main content -->
      <div class="col-md-8">
        <div v-if="loading" class="text-center p-5">
          <div class="spinner-border text-primary" role="status">
          </div>
        </div>
        <div v-else-if="error" class="alert alert-warning">
          {{ error }}
        </div>
        <div v-else>
          <!-- Thông tin sách -->
          <div class="card mb-4">
            <div class="card-header bg-info text-white py-2">
              <h2 class="mb-0">{{ sach.nhan_de }}</h2>
            </div>
            <div class="card-body">
              <div class="row">
                <div class="col-md-3 text-center mb-3 mb-md-0">
                  <img src="/images/image_opac/anh_sach.png" alt="Sách" class="img-fluid rounded" style="max-width: 150px;">
                </div>
                <div class="col-md-9">
                  <h4>{{ sach.nhan_de }}</h4>
                  <p class="mb-2">
                    <strong>Tác giả:</strong> {{ sach.tac_gia || 'Không có thông tin' }}
                  </p>
                  <p class="mb-2">
                    <strong>Nhà xuất bản:</strong> {{ sach.nha_xuat_ban || 'Không có thông tin' }}
                  </p>
                  <p class="mb-2">
                    <strong>Nơi xuất bản:</strong> {{ sach.noi_xuat_ban || 'Không có thông tin' }}
                  </p>
                  <p class="mb-2">
                    <strong>Năm xuất bản:</strong> {{ sach.nam_xuat_ban || 'Không có thông tin' }}
                  </p>
                  <p class="mb-0">
                    <strong>Loại tài liệu:</strong> {{ taiLieu || 'Không có thông tin' }}
                  </p>
                </div>
              </div>
            </div>
          </div>
          
          <!-- Tab nội dung -->
          <div class="card card-primary card-outline card-outline-tabs">
            <div class="card-header p-0 border-bottom-0">
              <ul class="nav nav-tabs" id="book-detail-tabs" role="tablist">
                <li class="nav-item">
                  <a class="nav-link active" id="tab-tom-luoc" data-toggle="pill" href="#content-tom-luoc" role="tab" aria-controls="content-tom-luoc" aria-selected="true">
                    <i class="fas fa-list-alt mr-1"></i> Tóm lược
                  </a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" id="tab-mo-ta" data-toggle="pill" href="#content-mo-ta" role="tab" aria-controls="content-mo-ta" aria-selected="false">
                    <i class="fas fa-info-circle mr-1"></i> Mô tả đầy đủ
                  </a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" id="tab-marc" data-toggle="pill" href="#content-marc" role="tab" aria-controls="content-marc" aria-selected="false">
                    <i class="fas fa-database mr-1"></i> Marc 21
                  </a>
                </li>
              </ul>
            </div>
            <div class="card-body">
              <div class="tab-content" id="book-detail-content">
                <!-- Tab Tóm lược -->
                <div class="tab-pane fade show active" id="content-tom-luoc" role="tabpanel" aria-labelledby="tab-tom-luoc">
                  <div class="row mb-3">
                    <div class="col-md-3"><strong>Loại tài liệu:</strong></div>
                    <div class="col-md-9">{{ taiLieu || 'Không có thông tin' }}</div>
                  </div>
                  <div class="row mb-3">
                    <div class="col-md-3"><strong>Mô tả:</strong></div>
                    <div class="col-md-9">
                      <p class="mb-1">{{ moTa.nhan_de || 'Không có thông tin' }}</p>
                      <p class="mb-1">{{ moTa.tac_gia || 'Không có thông tin' }}</p>
                      <p class="mb-1">{{ moTa.xuat_ban || 'Không có thông tin' }}</p>
                      <p class="mb-0">{{ moTa.mo_ta_vat_ly || 'Không có thông tin' }}</p>
                    </div>
                  </div>
                </div>
                
                <!-- Tab Mô tả đầy đủ -->
                <div class="tab-pane fade" id="content-mo-ta" role="tabpanel" aria-labelledby="tab-mo-ta">
                  <div class="row mb-3">
                    <div class="col-md-3"><strong>Loại tài liệu:</strong></div>
                    <div class="col-md-9">{{ taiLieu || 'Không có thông tin' }}</div>
                  </div>
                  <div class="row mb-3">
                    <div class="col-md-3"><strong>Tác giả:</strong></div>
                    <div class="col-md-9">{{ moTa.tac_gia || 'Không có thông tin' }}</div>
                  </div>
                  <div class="row mb-3">
                    <div class="col-md-3"><strong>Nhan đề:</strong></div>
                    <div class="col-md-9">{{ moTa.nhan_de || 'Không có thông tin' }}</div>
                  </div>
                  <div class="row mb-3">
                    <div class="col-md-3"><strong>TT xuất bản:</strong></div>
                    <div class="col-md-9">{{ moTa.xuat_ban || 'Không có thông tin' }}</div>
                  </div>
                  <div class="row mb-3">
                    <div class="col-md-3"><strong>Tác giả khác:</strong></div>
                    <div class="col-md-9">{{ moTa.tac_gia_khac || 'Không có thông tin' }}</div>
                  </div>
                  <div class="row mb-3">
                    <div class="col-md-3"><strong>Mô tả vật lý:</strong></div>
                    <div class="col-md-9">{{ moTa.mo_ta_vat_ly || 'Không có thông tin' }}</div>
                  </div>
                  <div class="row mb-3">
                    <div class="col-md-3"><strong>Từ khóa:</strong></div>
                    <div class="col-md-9">{{ moTa.tu_khoa || 'Không có thông tin' }}</div>
                  </div>
                  <div class="row mb-3">
                    <div class="col-md-3"><strong>Số phân loại:</strong></div>
                    <div class="col-md-9">{{ moTa.so_phan_loai || 'Không có thông tin' }}</div>
                  </div>
                </div>
                
                <!-- Tab Marc 21 -->
                <div class="tab-pane fade" id="content-marc" role="tabpanel" aria-labelledby="tab-marc">
                  <Table 
                    :columns="marcColumns" 
                    :data="processedMarcData"
                    :hide-search="true"
                  >
                    <template #column-ma_truong_con="{ row }">
                      ${{ row.ma_truong_con }}
                    </template>
                  </Table>
                </div>
              </div>
            </div>
          </div>
          
          <!-- Thông tin xếp giá -->
          <div class="card mt-4">
            <div class="card-header bg-info text-white py-2">
              <h4 class="mb-0">Thông tin xếp giá</h4>
            </div>
            <div class="card-body">
              <Table 
                :columns="dkcbColumns" 
                :data="danhSachDKCB"
                :hide-search="true"
              >
                <template #column-tinh_trang="{ row }">
                  <span :class="row.tinh_trang === 'Sẵn sàng' ? 'text-success' : 'text-danger'">
                    <i :class="row.tinh_trang === 'Sẵn sàng' ? 'fas fa-check-circle' : 'fas fa-times-circle'"></i>
                    {{ row.tinh_trang }}
                  </span>
                </template>
              </Table>
              <div v-if="danhSachDKCB.length === 0" class="alert alert-info mt-2">
                Không có thông tin bản
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
import Table from '@/components/tables/Table.vue';

export default {
  name: 'OpacChiTietSach',
  components: {
    OpacSidebar,
    Table
  },
  data() {
    return {
      sach: {},
      bieuGhi: {},
      taiLieu: null,
      moTa: {},
      danhSachDKCB: [],
      marcData: [],
      loading: true,
      error: null,
      dkcbColumns: [
        { key: 'ma_dkcb', label: 'Số ĐKCB' },
        { key: 'tinh_trang', label: 'Tình trạng' },
        { key: 'kho_luu_tru', label: 'Kho lưu trữ' }
      ],
      marcColumns: [
        { key: 'ma_truong', label: 'Mã trường' },
        { key: 'ct1', label: 'Chỉ thị 1' },
        { key: 'ct2', label: 'Chỉ thị 2' },
        { key: 'ma_truong_con', label: 'Mã trường con' },
        { key: 'noi_dung', label: 'Nội dung' }
      ]
    };
  },
  created() {
    this.loadChiTietSach();
  },
  mounted() {
    // Kích hoạt tabs khi component được mount
    if (window.jQuery) {
      $('#book-detail-tabs a').on('click', function (e) {
        e.preventDefault();
        $(this).tab('show');
      });
    }
  },
  methods: {
    async loadChiTietSach() {
      this.loading = true;
      this.error = null;
      
      try {
        const idSach = this.$route.params.id;
        const response = await axios.get(`/api/opac/chi-tiet-sach/${idSach}`);
        
        if (response.data.status === 200) {
          const data = response.data.data;
          this.sach = data.sach;
          this.bieuGhi = data.bieuGhi;
          this.taiLieu = data.taiLieu;
          this.moTa = data.moTa;
          this.danhSachDKCB = data.danhSachDKCB;
          this.marcData = data.marcData;
        } else {
          this.error = response.data.message || 'Có lỗi xảy ra khi tải chi tiết sách';
        }
      } catch (error) {
        console.error('Lỗi khi tải chi tiết sách:', error);
        this.error = 'Không thể kết nối đến máy chủ';
      } finally {
        this.loading = false;
      }
    }
  },
  computed: {
    processedMarcData() {
      let result = [];
      
      this.marcData.forEach(marc => {
        marc.children.forEach((child, index) => {
          result.push({
            ma_truong: index === 0 ? marc.ma_truong : '',
            ct1: index === 0 ? (marc.ct1 || '#') : '',
            ct2: index === 0 ? (marc.ct2 || '#') : '',
            ma_truong_con: child.ma_truong_con,
            noi_dung: child.noi_dung,
            rowspan: index === 0 ? marc.children.length : 0
          });
        });
      });
      
      return result;
    }
  }
};
</script>

<style scoped>
@media (max-width: 768px) {
    .table {
        display: none;
        width: 100%;
        overflow-x: auto;
        white-space: nowrap;
    }
}
</style> 