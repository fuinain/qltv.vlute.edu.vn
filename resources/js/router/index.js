// resources/js/router/index.js
import { createRouter, createWebHistory } from "vue-router";
import Admin from "@/pages/Admin/pageAdmin.vue";
import DocGia from "@/pages/DocGia/index.vue";
import pagePhongKhoa from "@/pages/Admin/DanhMuc/ThongTinChung/pagePhongKhoa.vue";
import pageChucVu from "@/pages/Admin/DanhMuc/ThongTinChung/pageChucVu.vue";
import AdminLayout from "@/components/layouts/AdminLayout.vue";
import PublicLayout from "@/components/layouts/PublicLayout.vue";
const routes = [
  {
    path: "/",
    redirect: (to) => {
      const userRole = window.userRole;
      
      if (userRole === 'admin') {
        return '/admin';
      } else if (userRole === 'docgia') {
        return '/docgia';
      } else {
        return "Bạn không có quyền truy cập vào trang này.";
      }
    }
  },
  {
    path: "/admin",
    component: AdminLayout,
    children: [
      {
        path: "",
        name: "AdminIndex",
        component: Admin,
      },
      {
        path: "/danh-muc/thong-tin-chung/phong-khoa",
        name: "pagePhongKhoa",
        component: pagePhongKhoa,
        meta: {
          title: "Phòng/Khoa",
          breadcrumb: [
            { name: "Home", path: "/" },
            { name: "Danh mục", path: "/admin" },
            { name: "Phòng/Khoa" }
          ]
        }
      },
      {
        path: "/danh-muc/thong-tin-chung/chuc-vu",
        name: "pageChucVu",
        component: pageChucVu,
        meta: {
          title: "Chức vụ",
          breadcrumb: [
            { name: "Home", path: "/" },
            { name: "Danh mục", path: "/admin" },
            { name: "Chức vụ" }
          ]
        }
      }
      
    ],
  },
  {
    path: "/docgia",
    component: PublicLayout,
    children: [
      {
        path: "",
        name: "DocGiaIndex",
        component: DocGia,
      },
    ],
  },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

export default router;
