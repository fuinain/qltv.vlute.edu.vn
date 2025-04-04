<template>
  <ContentWrapper title="Trang web của ADMIN">
    <template #content>
      <div class="container-fluid">
        <div class="row">
          <div class="col-12">
            <!-- Nội dung chính của trang Admin -->
            <div class="card">
              <div class="card-body">
                <p>Nội dung trang admin</p>
              </div>
            </div>
          </div>
        </div>
      </div>
      </template>
    </ContentWrapper>
</template>

<script>
import ContentWrapper from "@/components/layouts/ContentWrapper.vue";
export default {
  name: "AdminIndex",
  components: {
    ContentWrapper
  }
}
</script>