<template>
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Quản lý chức vụ</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><router-link to="/admin">Home</router-link></li>
                            <li class="breadcrumb-item active">Danh mục</li>
                            <li class="breadcrumb-item active">Thông tin chung</li>
                            <li class="breadcrumb-item active">Chức vụ</li>
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
                       
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Thêm mới tài khoản -->

    </div>
</template>

<script>
import AddButton from '@/components/AddButton.vue';
import DataTable from '@/components/DataTable.vue';
import axios from 'axios';
export default {
    name: "pageChucVu",
    components: {
        AddButton,
        DataTable
    },
}
</script>

<style lang="scss" scoped></style>