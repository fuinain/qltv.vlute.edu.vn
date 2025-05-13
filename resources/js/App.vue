<template>
  <div>
    <div v-if="errorMessage" class="alert alert-danger alert-dismissible fade show" role="alert">
      {{ errorMessage }}
      <button type="button" class="btn-close" @click="clearError" aria-label="Close"></button>
    </div>
    <RouterView />
  </div>
</template>

<script>
export default {
  name: "App",
  data() {
    return {
      errorMessage: ''
    };
  },
  mounted() {
    // Kiểm tra nếu có thông báo lỗi từ Laravel session flash
    if (window.Laravel && window.Laravel.error) {
      this.errorMessage = window.Laravel.error;
    }
  },
  methods: {
    clearError() {
      this.errorMessage = '';
    }
  }
};
</script>

<style>
.alert {
  position: fixed;
  top: 20px;
  left: 50%;
  transform: translateX(-50%);
  z-index: 9999;
  min-width: 300px;
}
</style>