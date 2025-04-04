<template>
  <div>
    <Topbar />
    <Sidebar />
    <RouterView />
    <Footer />
  </div>
</template>

<script>
import Topbar from "@/components/layouts/Topbar.vue";
import Sidebar from "@/components/layouts/Sidebar.vue";
import Footer from "@/components/layouts/Footer.vue";

export default {
  name: "AdminLayout",
  components: {
    Topbar,
    Sidebar,
    Footer,
  },
}
</script>