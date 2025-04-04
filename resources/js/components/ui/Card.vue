<template>
  <div class="card">
    <div class="card-header d-flex align-items-center">
      <slot name="ContentCardHeader"></slot>
    </div>
    <div class="card-body">
      <slot name="ContentCardBody"></slot>
    </div>
  </div>
</template>

<script>

export default {
  components: {},
  props: {
    title: String,
    showBackButton: Boolean,
  },
}
</script>

<style scoped>
.btn-back {
  cursor: pointer;
}
</style>
