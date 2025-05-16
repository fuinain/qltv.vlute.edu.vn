<template>
  <div class="card">
    <div v-if="!hideHeader && (title || $slots.ContentCardHeader)" class="card-header d-flex align-items-center">
      <slot name="ContentCardHeader"></slot>
      <span v-if="title" class="ml-2 font-weight-bold">{{ title }}</span>
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
    hideHeader: { type: Boolean, default: false },
  },
}
</script>

<style scoped>
.btn-back {
  cursor: pointer;
}
</style>
