import { createRouter, createWebHistory } from "vue-router";
import AdminLayout from "../layouts/AdminLayout.vue";
import PublicLayout from "../layouts/PublicLayout.vue";
import Admin from "../pages/Admin/index.vue";
import DocGia from "../pages/DocGia/index.vue";

const routes = [
  {
    path: "/admin",
    component: AdminLayout,
    children: [
      {
        path: "",
        name: "AdminIndex",
        component: Admin,
      },
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
