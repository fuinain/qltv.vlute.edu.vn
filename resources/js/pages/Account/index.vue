<template>
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">QL Tài khoản</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Tài khoản</li>
            </ol>
          </div>
        </div>
      </div>
    </div>

    <!-- Nội dung chính -->
    <div class="content">
      <div class="container-fluid">
        <div class="card">
          <div class="card-header">
            <!-- Sử dụng component AddButton, truyền modalTarget là id của modal -->
            <AddButton modalTarget="#modalThem" />
            <div class="card-tools">
              <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                <i class="fas fa-minus"></i>
              </button>
            </div>
          </div>
          <div class="card-body table-responsive p-0">
            <!-- Sử dụng component DataTable -->
            <DataTable :columns="columns" :data="accounts">
              <!-- Scoped slot cho cột action -->
              <template #column-action="{ row }">
                <button class="btn p-1 btn-primary border-0 bg-transparent text-primary shadow-none edit-btn"
                  @click="editAccount(row)">
                  <i class="fa fa-pencil"></i>
                </button>
                |
                <button class="btn p-1 btn-primary border-0 bg-transparent text-danger shadow-none delete-btn"
                  @click="deleteAccount(row.id_tai_khoan)">
                  <i class="fa fa-trash"></i>
                </button>
              </template>
            </DataTable>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal Thêm mới tài khoản -->
    <div class="modal fade" id="modalThem" tabindex="-1" aria-labelledby="modalThemLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title text-bold" id="modalThemLabel">Thêm mới tài khoản</h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">×</span>
            </button>
          </div>
          <!-- Sử dụng v-model để liên kết dữ liệu form -->
          <form @submit.prevent="addAccount">
            <div class="modal-body">
              <div class="row">
                <div class="col-md-12 mb-2">
                  <label for="ten_nguoi_dung">Tên người dùng</label>
                  <input class="form-control" type="text" placeholder="Tên người dùng" id="ten_nguoi_dung"
                    v-model="newAccount.ten_nguoi_dung" required>
                </div>
                <div class="col-md-12 mb-2">
                  <label for="ten_dang_nhap">Tên đăng nhập</label>
                  <input class="form-control" type="text" placeholder="Tên đăng nhập" id="ten_dang_nhap"
                    v-model="newAccount.ten_dang_nhap" required>
                </div>
                <div class="col-md-12 mb-2">
                  <label for="mat_khau">Mật khẩu</label>
                  <input class="form-control" type="password" placeholder="Mật khẩu" id="mat_khau"
                    v-model="newAccount.mat_khau" required>
                </div>
                <div class="col-md-12 mb-2">
                  <label for="quyen">Quyền</label>
                  <select class="form-control" id="quyen" v-model="newAccount.quyen" required>
                    <option value="admin">Admin</option>
                    <option value="nhanvien">Nhân viên</option>
                    <option value="sinhvien">Sinh viên</option>
                  </select>
                </div>
              </div>
            </div>
            <div class="modal-footer">
              <button type="submit" class="btn btn-primary btnLuu">Thêm</button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Modal Sửa tài khoản -->
    <div class="modal fade" id="modalSua" tabindex="-1" aria-labelledby="modalSuaLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title text-bold" id="modalSuaLabel">Chỉnh sửa tài khoản</h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">×</span>
            </button>
          </div>
          <form @submit.prevent="updateAccount">
            <div class="modal-body">
              <div class="row">
                <div class="col-md-12 mb-2">
                  <label for="edit_ten_nguoi_dung">Tên người dùng</label>
                  <input class="form-control" type="text" placeholder="Tên người dùng" id="edit_ten_nguoi_dung"
                    v-model="editAccountData.ten_nguoi_dung" required>
                </div>
                <div class="col-md-12 mb-2">
                  <label for="edit_ten_dang_nhap">Tên đăng nhập</label>
                  <input class="form-control" type="text" placeholder="Tên đăng nhập" id="edit_ten_dang_nhap"
                    v-model="editAccountData.ten_dang_nhap" required>
                </div>
                <div class="col-md-12 mb-2">
                  <label for="edit_quyen">Quyền</label>
                  <select class="form-control" id="edit_quyen" v-model="editAccountData.quyen" required>
                    <option value="admin">Admin</option>
                    <option value="nhanvien">Nhân viên</option>
                    <option value="sinhvien">Sinh viên</option>
                  </select>
                </div>
              </div>
            </div>
            <div class="modal-footer">
              <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import AddButton from '@/components/AddButton.vue';
import DataTable from '@/components/DataTable.vue';
import axios from 'axios';

export default {
  name: "AccountIndex",
  components: {
    AddButton,
    DataTable
  },
  data() {
    return {
      newAccount: {
        ten_dang_nhap: '',
        ten_nguoi_dung: '',
        mat_khau: '',
        quyen: 'admin'
      },
      // Dữ liệu cho modal chỉnh sửa
      editAccountData: {
        id_tai_khoan: null,
        ten_dang_nhap: '',
        ten_nguoi_dung: '',
        quyen: 'admin'
      },
      accounts: [],
      columns: [
        { key: 'index', label: 'STT' },
        { key: 'ten_dang_nhap', label: 'Tên đăng nhập' },
        { key: 'ten_nguoi_dung', label: 'Tên người dùng' },
        { key: 'quyen', label: 'Quyền' },
        { key: 'action', label: 'Hành động' }
      ]
    }
  },
  mounted() {
    this.loadAccounts();
  },
  methods: {
    // Hàm lấy danh sách tài khoản
    loadAccounts() {
      axios.get('/api/accounts')
        .then(response => {
          this.accounts = response.data.data;
        })
        .catch(error => {
          console.error('Lỗi lấy dữ liệu:', error);
        });
    },
    // Hàm thêm tài khoản
    addAccount() {
      axios.post('/api/accounts', this.newAccount)
        .then(response => {
          Swal.fire('Thành công', response.data.message, 'success');
          this.newAccount = { ten_dang_nhap: '', ten_nguoi_dung: '', mat_khau: '', quyen: 'admin' };
          $('#modalThem').modal('hide');
          this.loadAccounts(); // Reload danh sách sau khi thêm mới
        })
        .catch(error => {
          Swal.fire('Lỗi', 'Có lỗi xảy ra khi thêm tài khoản!', 'error');
          console.error(error);
        });
    },
    // Hàm hiển thị modal chỉnh sửa
    editAccount(row) {
      // Gán dữ liệu từ row sang biến editAccountData
      this.editAccountData = {
        id_tai_khoan: row.id_tai_khoan,
        ten_dang_nhap: row.ten_dang_nhap,
        ten_nguoi_dung: row.ten_nguoi_dung,
        quyen: row.quyen
      };
      // Hiển thị modal chỉnh sửa
      $('#modalSua').modal('show');
    },
    // Hàm cập nhật tài khoản
    updateAccount() {
      axios.put(`/api/accounts/${this.editAccountData.id_tai_khoan}`, this.editAccountData)
        .then(response => {
          Swal.fire('Thành công', 'Cập nhật tài khoản thành công!', 'success');
          $('#modalSua').modal('hide');
          this.loadAccounts();
        })
        .catch(error => {
          Swal.fire('Lỗi', 'Có lỗi xảy ra khi cập nhật tài khoản!', 'error');
          console.error(error);
        });
    },
    // Hàm xóa tài khoản
    deleteAccount(id) {
      Swal.fire({
        title: 'Bạn có chắc chắn muốn xóa?',
        text: "Hành động này không thể hoàn tác!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Đồng ý xóa',
        cancelButtonText: 'Hủy bỏ'
      }).then((result) => {
        if (result.isConfirmed) {
          axios.delete(`/api/accounts/${id}`)
            .then(response => {
              Swal.fire('Đã xóa!', 'Tài khoản đã được xóa.', 'success');
              this.loadAccounts();
            })
            .catch(error => {
              Swal.fire('Lỗi!', 'Có lỗi xảy ra khi xóa tài khoản!', 'error');
              console.error(error);
            });
        }
      });
    }
  }
}
</script>
