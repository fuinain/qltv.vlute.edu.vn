<template>
    <ContentWrapper title="QUẢN LÝ CHỨC VỤ">
      <template #ContentPage>
        <div class="row">
          <div class="col-12">
            <Card>
              <template #ContentCardHeader>
                <button type="button" class="btn btn-primary" @click="themChucVu">
                  <i class="fas fa-plus-circle"></i>&nbsp;
                  Thêm mới
                </button>
              </template>
              <template #ContentCardBody>
                <!-- Bảng hiển thị dữ liệu -->
                <Table :columns="headers" :data="ds.data ?? []" :pagination="ds" :fetchData="fetchData">
                  <!-- Slot cho cột hành động -->
                  <template v-slot:column-actions="{ row }">
                    <button type="button" class="btn p-1 btn-primary border-0 bg-transparent text-primary shadow-none" @click="suaChucVu(row)">
                      <i class="fas fa-edit"></i>&nbsp;
                    </button>
                    <button type="button" class="btn p-1 btn-primary border-0 bg-transparent text-danger shadow-none" @click="xoaChucVu(row)">
                      <i class="fas fa-trash-alt"></i>&nbsp;
                    </button>
                  </template>
                </Table>
              </template>
            </Card>
          </div>
        </div>
      </template>
    </ContentWrapper>
  
    <!-- Modal (dùng chung cho thêm & sửa) -->
    <Modal ref="modal">
      <div class="row">
        <div class="col-6">
          <Input v-model="ChucVu.ma_chuc_vu" label="Mã chức vụ" placeholder="Mã chức vụ ..." type="text" />
        </div>
        <div class="col-6">
          <Input v-model="ChucVu.ten_chuc_vu" label="Tên chức vụ" placeholder="Tên chức vụ ..." type="text" />
        </div>
      </div>
    </Modal>
  </template>
  
  <script>
  export default {
    name: "PageChucVu",
    
    data() {
      return {
        ds: [],
        currentPage: 1,
        ChucVu: {
          ma_chuc_vu: "",
          ten_chuc_vu: ""
        },
        headers: [
          { key: 'index', label: 'STT', sortable: false },
          { key: 'ma_chuc_vu', label: 'Mã chức vụ' },
          { key: 'ten_chuc_vu', label: 'Tên chức vụ' },
          { key: 'actions', label: 'Hành động', sortable: false }
        ],
      };
    },
    mounted() {
      this.fetchData(1);
    },
    methods: {
      async fetchData(page = 1) {
        try {
          const response = await axios.get(`/api/chucvu?page=${page}`);
          if (response.data.status === 200) {
            this.ds = response.data.data;
            this.currentPage = this.ds.current_page;
          }
        } catch (error) {
          console.error("Lỗi khi load dữ liệu:", error);
        }
      },
    
      async themChucVu() {
        // Reset form cho trường hợp thêm mới
        this.$refs.modal.$data.title = "Thêm mới thông tin";
        this.$refs.modal.$data.save = "Thêm mới";
        this.ChucVu = {
          ma_chuc_vu: "",
          ten_chuc_vu: ""
        };
  
        // Mở modal
        const confirmed = await this.$refs.modal.openModal();
        if (!confirmed) return;
  
        try {
          const response = await axios.post("/api/chucvu", this.ChucVu);
          if (response.data.status === 200) {
            toastr.success(response.data.message);
            this.fetchData(this.currentPage);
            this.$refs.modal.closeModal();
          }
        } catch (error) {
          console.error("Lỗi khi thêm:", error);
          toastr.error("Thêm đơn vị thất bại!");
        }
      },
  
      async suaChucVu(row) {
        // Set tiêu đề và điền dữ liệu hiện có vào form
        this.$refs.modal.$data.title = "Cập nhật thông tin";
        this.$refs.modal.$data.save = "Cập nhật";
        this.ChucVu = { ...row };
  
        // Mở modal để chỉnh sửa
        const confirmed = await this.$refs.modal.openModal();
        if (!confirmed) return;
  
        try {
          const response = await axios.put(`/api/chucvu/${row.id_chuc_vu}`, this.ChucVu);
          if (response.data.status === 200) {
            toastr.success(response.data.message);
            this.fetchData(this.currentPage);
            this.$refs.modal.closeModal();
          }
        } catch (error) {
          console.error("Lỗi khi cập nhật:", error);
          toastr.error("Cập nhật đơn vị thất bại!");
        }
      },
  
      xoaChucVu(row) {
        Swal.fire({
          title: 'Bạn có chắc chắn?',
          text: "Dữ liệu sau khi bị xoá không thể khôi phục lại !!!",
          icon: 'warning',
          showCancelButton: true,
          confirmButtonText: 'Có, xoá nó!',
          cancelButtonText: 'Hủy bỏ'
        }).then(async (result) => {
          if (result.isConfirmed) {
            try {
              const response = await axios.delete(`/api/chucvu/${row.id_chuc_vu}`);
              if (response.data.status === 200) {
                toastr.success(response.data.message);
                this.fetchData(this.currentPage);
              }
            } catch (error) {
              console.error("Lỗi khi xoá:", error);
              toastr.error("Xoá đơn vị thất bại!");
            }
          }
        });
      }
    },
  };
  </script>
  