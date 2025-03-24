import { createRouter, createWebHistory } from "vue-router";
import PublicLayout from "../layouts/PublicLayout.vue";
import AdminLayout from "../layouts/AdminLayout.vue";
import Login from "../pages/Login/index.vue";
import DashBoard from "../pages/DashBoard/Index.vue";
import Categories from "../pages/Categories/Index.vue";
import Readers from "../pages/Readers/Index.vue";
import Services from "../pages/Services/Index.vue";
import Liquidation from "../pages/Liquidation/Index.vue";
import Account from "../pages/Account/Index.vue";

const routes = [
  {
    path: "/",
    component: PublicLayout,
    children: [
      {
        path: "",
        name: "Login",
        component: Login,
      },
    ],
  },
  {
    path: "/",
    component: AdminLayout,
    children: [
      {
        path: "dashboard",
        name: "DashBoard",
        component: DashBoard,
      },
      {
        path: "categories",
        name: "Categories",
        component: Categories,
      },
      {
        path: "readers",
        name: "Readers",
        component: Readers,
      },
      {
        path: "services",
        name: "Services",
        component: Services,
      },
      {
        path: "liquidation",
        name: "Liquidation",
        component: Liquidation,
      },
      {
        path: "account",
        name: "Account",
        component: Account,
      },
    ],
  },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

export default router;
