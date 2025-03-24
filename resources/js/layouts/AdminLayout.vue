<template>
    <div>
      <Topbar />
      <Sidebar />
      <RouterView />
      <Footer />
    </div>
  </template>
  
  <script>
  import Topbar from "../components/Topbar.vue";
  import Sidebar from "../components/Sidebar.vue";
  import Footer from "../components/Footer.vue";
  
  export default {
    name: "AdminLayout",
    components: {
      Topbar,
      Sidebar,
      Footer,
    },
  }
  </script>
  