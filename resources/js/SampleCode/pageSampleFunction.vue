<template>
  <LTEContentWrapper>
    <template #content>
      <div class="row">
        <div class="col-12">
          <LTECard title="Thông tin/Chức vụ" :showBackButton="true">
            <template #content>

              <button v-if="authStore.currentPermission.is_write" type="button" class="btn btn-primary"
                @click="themChucVu">
                <i class="fas fa-plus-circle"></i>&nbsp;
                Thêm mới
              </button>

              <div class="row">
                <div class="col-8"></div>
                <div class="col-4">
                  <div class="input-group input-group-sm" style="margin-bottom: 12px;">
                    <input @keyup.enter="loadChucVu('chuc-vu')" type="text" class="form-control float-right"
                      name="search" placeholder="Nhập từ khóa tìm kiếm và nhấn Enter ...">
                  </div>
                </div>
              </div>

              <LTETableV2 :data="tableChucVu.list" :headers="tableChucVu.headers" :pagination="tableChucVu.pagination"
                :fetchPage="loadChucVu" cssActions="width: 100px;">
                <template #actions="{ item }">
                  <i v-if="authStore.currentPermission.is_delete" @click="xoaChucVu(item)"
                    class="fas fa-trash icon-delete float-right"></i>
                  <i v-if="authStore.currentPermission.is_update" @click="chinhSuaChucVu(item)"
                    class="fas fa-edit icon-edit float-right"></i>
                </template>
              </LTETableV2>
            </template>
          </LTECard>
        </div>
      </div>
    </template>
  </LTEContentWrapper>

  <LTEModal ref="mdThem" @save="themChucVu">
    <div class="row">
      <div class="col-6">
        <LTEInput v-model="chucVu.ten_chuc_vu" label="Tên Chức vụ" placeholder="Chức vụ ...." type="text"></LTEInput>
      </div>
    </div>
  </LTEModal>

  <!-- Yes/No Modal -->
  <YesNoModal ref="confirm"></YesNoModal>

</template>

<script>
import { useAuthStore } from '@/store/auth';

// Const
const API_CHUC_VU = route('ChucVuController.danhSach');

export default {
  data() {
    return {
      authStore: useAuthStore(),
      chucVu: { ten_chuc_vu: '' },
      tableChucVu: {
        headers: [
          { key: 'col1', label: 'Chức vụ' },
          { key: 'col2', label: 'Cập nhật lần cuối' },
        ],
        pagination: {},
        list: []
      }
    };
  },
  mounted() {
    this.loadChucVu(API_CHUC_VU);
  },
  computed: {
  },
  methods: {
    async themChucVu() {
      this.chucVu.ten_chuc_vu = '';
      this.$refs.mdThem.$data.title = "Thêm mới thông tin";
      this.$refs.mdThem.$data.save = "Lưu thông tin";
      const result = await this.$refs.mdThem.openModal();
      if (!result) return;
      var params = {
        'ten_chuc_vu': this.chucVu.ten_chuc_vu,
      }
      axios.post(API_CHUC_VU, params)
        .then((response) => {
          let data = response.data;
          if (data.status === 200) {
            this.loadChucVu(API_CHUC_VU, this.tableChucVu.pagination.current_page);
            this.$func.toastSuccess(data.message);
            this.$refs.mdThem.closeModal();
          } else {
            this.themChucVu();
            this.$func.toastError(data.message);
          }
        });
    },

    loadChucVu(url, page) {
      const field = document.querySelector("input[name=search]").value;
      var params = { s: field, page: page }
      axios.get(url, { params: params }).then((response) => {
        this.tableChucVu.list = [];
        const rData = response.data.data;
        rData.data.forEach((item) => {
          this.tableChucVu.list.push(
            {
              'raw': item,
              'col1': item.ten_chuc_vu,
              'col2': this.$func.fromNow(item.ngay_cap_nhat),
            });
        });
        this.tableChucVu.pagination = rData;
        if (rData.data.length > 0) {
          // this.$func.toastSuccess("Tải dữ liệu thành công");
        } else {
          this.$func.toastError("Không có dữ liệu");
        }
      });
    },

    async chinhSuaChucVu(item) {
      this.chucVu.ten_chuc_vu = item.raw.ten_chuc_vu;
      this.$refs.mdThem.$data.title = "Cập nhật thông tin";
      this.$refs.mdThem.$data.save = "Cập nhật";
      const result = await this.$refs.mdThem.openModal();
      if (!result) return;

      var params = {
        'id_chuc_vu': item.raw.id_chuc_vu,
        'ten_chuc_vu': this.chucVu.ten_chuc_vu,
      }
      axios.put(API_CHUC_VU, params)
        .then((response) => {
          let data = response.data;
          if (data.status === 200) {
            this.loadChucVu(API_CHUC_VU, this.tableChucVu.pagination.current_page);
            this.$func.toastSuccess(data.message);
            this.$refs.mdThem.closeModal();
          } else {
            this.chinhSuaChucVu(item);
            this.$func.toastError(data.message);
          }
        });
    },

    async xoaChucVu(item) {
      const result = await this.$refs.confirm.openModal();
      if (!result) return;

      var params = { 'id_chuc_vu': item.raw.id_chuc_vu }
      axios.delete(API_CHUC_VU, { params: params })
        .then((response) => {
          let data = response.data;
          if (data.status === 200) {
            this.loadChucVu(API_CHUC_VU, this.tableChucVu.pagination.current_page);
            this.$func.toastSuccess(data.message);
            this.$refs.mdThem.closeModal();
          } else {
            this.openModal();
            this.$func.toastError(data.message);
          }
        });
    }
  }
};
</script>
