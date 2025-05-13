<template>
  <div class="container-xl mt-4 mb-4">
    <div class="row">
      <!-- Sidebar -->
      <div class="col-md-3">
        <OpacSidebar />
      </div>
      
      <!-- Main content -->
      <div class="col-md-9">
        <div class="card">
          <div class="card-header bg-primary text-white">
            <h2 class="mb-0">Giới thiệu về Thư viện</h2>
          </div>
          <div class="card-body">
            <h3>Trung tâm Thông tin - Thư viện Trường Đại học Sư phạm Kỹ thuật Vĩnh Long</h3>
            
            <div class="mt-4">
              <p>Trung tâm Thông tin - Thư viện Trường Đại học Sư phạm Kỹ thuật Vĩnh Long được thành lập ngày 15/09/2017 theo quyết định số 1288/QĐ-ĐHSPKTVL của Hiệu trưởng trường Đại học Sư phạm Kỹ thuật Vĩnh Long.</p>
              
              <p>Trung tâm là đơn vị chức năng trực thuộc Ban Giám hiệu nhà trường, có nhiệm vụ xây dựng và quản lý hệ thống thông tin, tư liệu, thư viện phục vụ các hoạt động đào tạo, nghiên cứu khoa học và phát triển công nghệ, quản lý, chuyển giao công nghệ của nhà trường và cung cấp dịch vụ cho cộng đồng.</p>
            </div>
            
            <h4 class="mt-4">Sứ mệnh</h4>
            <p>Trung tâm Thông tin - Thư viện là trung tâm thông tin, học tập, nghiên cứu với sứ mệnh:</p>
            <ul>
              <li>Xây dựng, quản lý và cung cấp tài nguyên thông tin đáp ứng nhu cầu học tập, giảng dạy, nghiên cứu của cán bộ, giảng viên, sinh viên và nghiên cứu viên của trường</li>
              <li>Là cầu nối quan trọng để tiếp cận tri thức, phương pháp học tập tiên tiến, năng lực tự học, tự khám phá và đổi mới sáng tạo</li>
              <li>Là nơi lưu trữ, gìn giữ và phát huy các giá trị văn hóa tri thức của nhà trường</li>
            </ul>
            
            <h4 class="mt-4">Tầm nhìn</h4>
            <p>Đến năm 2030, Trung tâm Thông tin - Thư viện Trường Đại học Sư phạm Kỹ thuật Vĩnh Long sẽ trở thành:</p>
            <ul>
              <li>Thư viện hiện đại, tiên tiến, hoạt động theo các chuẩn mực quốc tế</li>
              <li>Trung tâm học liệu hiện đại hàng đầu của khu vực Đồng bằng Sông Cửu Long</li>
              <li>Trung tâm thông tin khoa học công nghệ mạnh trong hệ thống giáo dục quốc dân</li>
            </ul>
            
            <h4 class="mt-4">Cơ sở vật chất</h4>
            <div class="row">
              <div class="col-md-6">
                <img src="https://placehold.co/600x400" alt="Thư viện" class="img-fluid rounded mb-3">
              </div>
              <div class="col-md-6">
                <ul>
                  <li>Diện tích: 1.500 m²</li>
                  <li>600 chỗ ngồi</li>
                  <li>Kho sách mở: 40.000 đầu sách</li>
                  <li>Phòng đọc điện tử: 100 máy tính</li>
                  <li>Không gian sáng tạo</li>
                  <li>WiFi phủ sóng toàn bộ khu vực</li>
                </ul>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import OpacSidebar from "@/components/opac/OpacSidebar.vue";

export default {
  name: "OpacGioiThieu",
  components: {
    OpacSidebar
  }
}
</script> 