<template>
  <ContentWrapper title="Tổng quan hệ thống">
    <template #ContentPage>
      <div class="container-fluid">
        <div class="row">
          <div class="col-md-3 col-6">
            <!-- small box -->
            <div class="small-box bg-info">
              <div class="inner">
                <h3>{{ thongKe.tongDonNhan }}</h3>

                <p>Tổng số đơn nhận</p>
              </div>
              <div class="icon">
                <i class="fas fa-file-invoice"></i>
              </div>
              <router-link :to="{ name: 'pageDonNhan' }" class="small-box-footer">Xem chi tiết <i class="fas fa-arrow-circle-right"></i></router-link>
            </div>
          </div>
          <!-- ./col -->
          <div class="col-md-3 col-6">
            <!-- small box -->
            <div class="small-box bg-success">
              <div class="inner">
                <h3>{{ thongKe.tongSach }}</h3>

                <p>Tổng số đầu sách</p>
              </div>
              <div class="icon">
                <i class="fas fa-book"></i>
              </div>
              <router-link :to="{ name: 'pageDonNhan' }" class="small-box-footer">Xem chi tiết <i class="fas fa-arrow-circle-right"></i></router-link>
            </div>
          </div>
          <!-- ./col -->
          <div class="col-md-3 col-6">
            <!-- small box -->
            <div class="small-box bg-warning">
              <div class="inner">
                <h3>{{ thongKe.tongDocGia }}</h3>

                <p>Tổng số độc giả</p>
              </div>
              <div class="icon">
                <i class="fas fa-users"></i>
              </div>
              <router-link :to="{ name: 'pageDocGia' }" class="small-box-footer">Xem chi tiết <i class="fas fa-arrow-circle-right"></i></router-link>
            </div>
          </div>
          <!-- ./col -->
          <div class="col-md-3 col-6">
            <!-- small box -->
            <div class="small-box bg-danger">
              <div class="inner">
                <h3>{{ thongKe.tongDKCB }}</h3>

                <p>Tổng số mã DKCB</p>
              </div>
              <div class="icon">
                <i class="fas fa-barcode"></i>
              </div>
              <router-link :to="{ name: 'pageInNhanDKCB' }" class="small-box-footer">Xem chi tiết <i class="fas fa-arrow-circle-right"></i></router-link>
            </div>
          </div>
          <!-- ./col -->
        </div>

        <!-- Thêm hàng mới cho chi tiết DKCB -->
        <div class="row">
          <div class="col-md-12">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Chi tiết mã DKCB</h3>
              </div>
              <div class="card-body">
                <div class="row">
                  <div class="col-md-4">
                    <div class="info-box">
                      <span class="info-box-icon bg-info"><i class="fas fa-barcode"></i></span>
                      <div class="info-box-content">
                        <span class="info-box-text">Tổng số mã DKCB</span>
                        <span class="info-box-number">{{ thongKe.tongDKCB }}</span>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="info-box">
                      <span class="info-box-icon bg-success"><i class="fas fa-check"></i></span>
                      <div class="info-box-content">
                        <span class="info-box-text">Đã gán cho sách</span>
                        <span class="info-box-number">{{ thongKe.dkcbDaGan }}</span>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="info-box">
                      <span class="info-box-icon bg-warning"><i class="fas fa-times"></i></span>
                      <div class="info-box-content">
                        <span class="info-box-text">Chưa gán cho sách</span>
                        <span class="info-box-number">{{ thongKe.dkcbChuaGan }}</span>
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
  </ContentWrapper>
</template>

<script>
import ContentWrapper from "@/components/layouts/ContentWrapper.vue";
import axios from 'axios';

export default {
  name: "AdminIndex",
  components: {
    ContentWrapper
  },
  data() {
    return {
      thongKe: {
        tongDonNhan: 0,
        tongSach: 0,
        tongDocGia: 0,
        tongDKCB: 0,
        dkcbDaGan: 0,
        dkcbChuaGan: 0
      },
      loading: false
    };
  },
  created() {
    this.getThongKeTongQuan();
  },
  methods: {
    async getThongKeTongQuan() {
      try {
        this.loading = true;
        const response = await axios.get('/api/thong-ke-tong-quan');
        this.thongKe.tongDonNhan = response.data.tong_don_nhan;
        this.thongKe.tongSach = response.data.tong_sach;
        this.thongKe.tongDocGia = response.data.tong_doc_gia;
        
        if (response.data.thong_ke_dkcb) {
          this.thongKe.tongDKCB = response.data.thong_ke_dkcb.tong_dkcb;
          this.thongKe.dkcbDaGan = response.data.thong_ke_dkcb.dkcb_da_gan;
          this.thongKe.dkcbChuaGan = response.data.thong_ke_dkcb.dkcb_chua_gan;
        }
      } catch (error) {
        console.error('Lỗi khi lấy thông tin thống kê:', error);
      } finally {
        this.loading = false;
      }
    }
  }
}
</script>

<style scoped>
.info-box {
  min-height: 100px;
  background: #fff;
  width: 100%;
  box-shadow: 0 1px 1px rgba(0,0,0,0.1);
  border-radius: 2px;
  margin-bottom: 15px;
  display: flex;
}

.info-box-icon {
  border-top-left-radius: 2px;
  border-top-right-radius: 0;
  border-bottom-right-radius: 0;
  border-bottom-left-radius: 2px;
  display: block;
  width: 90px;
  text-align: center;
  font-size: 45px;
  line-height: 90px;
  background: rgba(0,0,0,0.2);
  color: #fff;
}

.info-box-content {
  padding: 5px 10px;
  margin-left: 90px;
  display: flex;
  flex-direction: column;
  justify-content: center;
}

.info-box-text {
  display: block;
  font-size: 14px;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  text-transform: uppercase;
}

.info-box-number {
  display: block;
  font-weight: bold;
  font-size: 18px;
}
</style>