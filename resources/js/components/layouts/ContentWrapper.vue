<!-- ContentWrapper.vue -->
<template>
  <div class="content-wrapper">
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0 text-dark" style="font-size: 25px;">
              {{ title }}
            </h1>
          </div>
          <Breadcrumb />
        </div>
      </div>
    </div>

    <section class="content">
      <div class="container-fluid">
        <slot name="ContentPage"></slot>
      </div>
    </section>
  </div>
</template>

<script>
import Breadcrumb from "@/components/ui/Breadcrumb.vue";

export default {
  props: {
    title: String,
  },
  components: {
    Breadcrumb,
  },
};
</script>
