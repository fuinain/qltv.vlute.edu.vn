<template>
  <div>
    <OpacHeader />
    <router-view></router-view>
    <OpacFooter />
  </div>
</template>

<script>
import OpacHeader from "@/components/opac/OpacHeader.vue";
import OpacFooter from "@/components/opac/OpacFooter.vue";

export default {
  name: "OpacLayout",
  components: {
    OpacHeader,
    OpacFooter
  }
}
</script>

<style>
body {
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  background-color: #f8f9fa;
}
</style> 