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
            <h2 class="mb-0">Hướng dẫn sử dụng thư viện</h2>
          </div>
          <div class="card-body">
            <!-- Tabs navigation -->
            <ul class="nav nav-tabs mb-4" id="huongDanTab" role="tablist">
              <li class="nav-item" role="presentation">
                <button class="nav-link active" id="quy-dinh-tab" data-bs-toggle="tab" data-bs-target="#quy-dinh" type="button" role="tab" aria-controls="quy-dinh" aria-selected="true">
                  Quy định chung
                </button>
              </li>
              <li class="nav-item" role="presentation">
                <button class="nav-link" id="the-tv-tab" data-bs-toggle="tab" data-bs-target="#the-tv" type="button" role="tab" aria-controls="the-tv" aria-selected="false">
                  Thẻ thư viện
                </button>
              </li>
              <li class="nav-item" role="presentation">
                <button class="nav-link" id="muon-tra-tab" data-bs-toggle="tab" data-bs-target="#muon-tra" type="button" role="tab" aria-controls="muon-tra" aria-selected="false">
                  Mượn trả tài liệu
                </button>
              </li>
              <li class="nav-item" role="presentation">
                <button class="nav-link" id="tim-kiem-tab" data-bs-toggle="tab" data-bs-target="#tim-kiem" type="button" role="tab" aria-controls="tim-kiem" aria-selected="false">
                  Tìm kiếm tài liệu
                </button>
              </li>
            </ul>
            
            <!-- Tabs content -->
            <div class="tab-content" id="huongDanTabContent">
              <!-- Quy định chung -->
              <div class="tab-pane fade show active" id="quy-dinh" role="tabpanel" aria-labelledby="quy-dinh-tab">
                <h4>Nội quy thư viện</h4>
                <ol>
                  <li>Bạn đọc cần xuất trình thẻ thư viện hoặc thẻ sinh viên khi vào thư viện.</li>
                  <li>Không mang thức ăn, nước uống vào thư viện (trừ nước đóng chai).</li>
                  <li>Giữ gìn trật tự, vệ sinh chung, không làm ồn trong khu vực thư viện.</li>
                  <li>Không sử dụng điện thoại ở chế độ chuông, không nghe nhạc có âm thanh làm ảnh hưởng đến người khác.</li>
                  <li>Không hút thuốc, không mang chất dễ gây cháy nổ vào thư viện.</li>
                  <li>Không tự ý di chuyển bàn ghế, trang thiết bị của thư viện.</li>
                  <li>Không viết, vẽ lên tài liệu, sách, báo và các trang thiết bị của thư viện.</li>
                  <li>Bạn đọc không tự ý mang tài liệu ra khỏi thư viện khi chưa làm thủ tục mượn.</li>
                  <li>Tự bảo quản tài sản cá nhân, nếu mất thư viện không chịu trách nhiệm.</li>
                </ol>
                
                <h4 class="mt-4">Giờ phục vụ</h4>
                <table class="table table-bordered">
                  <thead>
                    <tr>
                      <th>Ngày</th>
                      <th>Giờ mở cửa</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td>Thứ 2 đến Thứ 6</td>
                      <td>7h00 - 17h00</td>
                    </tr>
                    <tr>
                      <td>Thứ 7</td>
                      <td>8h00 - 12h00</td>
                    </tr>
                    <tr>
                      <td>Chủ nhật và ngày lễ</td>
                      <td>Đóng cửa</td>
                    </tr>
                  </tbody>
                </table>
              </div>
              
              <!-- Thẻ thư viện -->
              <div class="tab-pane fade" id="the-tv" role="tabpanel" aria-labelledby="the-tv-tab">
                <h4>Đối tượng được làm thẻ thư viện</h4>
                <ul>
                  <li>Cán bộ, giảng viên, nhân viên trong trường</li>
                  <li>Sinh viên các hệ đào tạo của trường</li>
                  <li>Học viên cao học, nghiên cứu sinh</li>
                  <li>Độc giả ngoài trường có nhu cầu sử dụng tài liệu của thư viện</li>
                </ul>
                
                <h4 class="mt-4">Hồ sơ làm thẻ</h4>
                <p><strong>Đối với cán bộ, giảng viên, nhân viên và sinh viên trong trường:</strong></p>
                <ul>
                  <li>Sinh viên: Đăng ký trực tiếp tại thư viện bằng thẻ sinh viên</li>
                  <li>Cán bộ, giảng viên: Đăng ký trực tiếp tại thư viện kèm theo thẻ cán bộ</li>
                </ul>
                
                <p><strong>Đối với độc giả ngoài trường:</strong></p>
                <ul>
                  <li>01 đơn đăng ký làm thẻ thư viện (theo mẫu)</li>
                  <li>01 ảnh 3x4 (chụp không quá 6 tháng)</li>
                  <li>01 bản sao CMND/CCCD</li>
                  <li>Phí làm thẻ: 50.000đ/năm</li>
                </ul>
                
                <h4 class="mt-4">Thời hạn sử dụng thẻ</h4>
                <ul>
                  <li>Sinh viên: Theo thời gian đào tạo</li>
                  <li>Cán bộ, giảng viên, nhân viên: 5 năm</li>
                  <li>Độc giả ngoài trường: 1 năm</li>
                </ul>
              </div>
              
              <!-- Mượn trả tài liệu -->
              <div class="tab-pane fade" id="muon-tra" role="tabpanel" aria-labelledby="muon-tra-tab">
                <h4>Quy định mượn, trả tài liệu</h4>
                
                <h5 class="mt-3">Số lượng tài liệu được mượn</h5>
                <table class="table table-bordered">
                  <thead>
                    <tr>
                      <th>Đối tượng</th>
                      <th>Số lượng</th>
                      <th>Thời gian mượn</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td>Giảng viên, nghiên cứu viên</td>
                      <td>10 cuốn</td>
                      <td>30 ngày</td>
                    </tr>
                    <tr>
                      <td>Sinh viên đại học</td>
                      <td>05 cuốn</td>
                      <td>14 ngày</td>
                    </tr>
                    <tr>
                      <td>Cán bộ, nhân viên</td>
                      <td>05 cuốn</td>
                      <td>14 ngày</td>
                    </tr>
                    <tr>
                      <td>Độc giả ngoài trường</td>
                      <td>02 cuốn</td>
                      <td>07 ngày</td>
                    </tr>
                  </tbody>
                </table>
                
                <h5 class="mt-4">Quy trình mượn tài liệu</h5>
                <ol>
                  <li>Bạn đọc tìm kiếm tài liệu trên máy tính hoặc tra cứu trực tiếp tại kệ sách</li>
                  <li>Chọn tài liệu cần mượn và mang đến quầy thủ thư</li>
                  <li>Xuất trình thẻ và điền phiếu yêu cầu mượn tài liệu</li>
                  <li>Thủ thư kiểm tra, ghi nhận và cho mượn tài liệu</li>
                </ol>
                
                <h5 class="mt-4">Quy trình trả tài liệu</h5>
                <ol>
                  <li>Mang tài liệu đã mượn đến quầy thủ thư</li>
                  <li>Thủ thư kiểm tra tình trạng tài liệu và ghi nhận việc trả tài liệu</li>
                  <li>Nếu tài liệu bị hư hỏng, bạn đọc có trách nhiệm bồi thường theo quy định</li>
                </ol>
                
                <h5 class="mt-4">Gia hạn mượn tài liệu</h5>
                <ul>
                  <li>Mỗi tài liệu được gia hạn tối đa 01 lần</li>
                  <li>Thời gian gia hạn bằng 1/2 thời gian mượn ban đầu</li>
                  <li>Chỉ được gia hạn khi tài liệu chưa đến hạn trả và không có người đặt trước</li>
                </ul>
                
                <h5 class="mt-4">Quy định xử lý vi phạm</h5>
                <ul>
                  <li>Trả tài liệu muộn: 2.000đ/ngày/cuốn</li>
                  <li>Làm mất hoặc hư hỏng tài liệu: Bồi thường 2-5 lần giá trị tài liệu tùy theo mức độ quý hiếm</li>
                </ul>
              </div>
              
              <!-- Tìm kiếm tài liệu -->
              <div class="tab-pane fade" id="tim-kiem" role="tabpanel" aria-labelledby="tim-kiem-tab">
                <h4>Hướng dẫn tìm kiếm tài liệu</h4>
                
                <h5 class="mt-3">Tìm kiếm trên OPAC</h5>
                <p>Bạn đọc có thể tìm kiếm tài liệu trên hệ thống OPAC theo các bước sau:</p>
                <ol>
                  <li>Truy cập website thư viện</li>
                  <li>Nhập từ khóa tìm kiếm vào ô tìm kiếm</li>
                  <li>Lựa chọn các tiêu chí tìm kiếm: tựa đề, tác giả, chủ đề, v.v.</li>
                  <li>Nhấn nút "Tìm kiếm"</li>
                  <li>Kết quả tìm kiếm sẽ hiển thị danh sách các tài liệu liên quan</li>
                  <li>Tìm kiếm nâng cao cho phép tìm kiếm chi tiết hơn với nhiều tiêu chí</li>
                </ol>
                
                <h5 class="mt-4">Tìm kiếm theo kệ sách</h5>
                <p>Thư viện sử dụng hệ thống phân loại DDC (Dewey Decimal Classification). Các tài liệu được sắp xếp theo chủ đề:</p>
                <ul>
                  <li>000: Tổng quát</li>
                  <li>100: Triết học & Tâm lý học</li>
                  <li>200: Tôn giáo</li>
                  <li>300: Khoa học xã hội</li>
                  <li>400: Ngôn ngữ</li>
                  <li>500: Khoa học tự nhiên</li>
                  <li>600: Công nghệ & Khoa học ứng dụng</li>
                  <li>700: Nghệ thuật & Giải trí</li>
                  <li>800: Văn học</li>
                  <li>900: Lịch sử & Địa lý</li>
                </ul>
                
                <h5 class="mt-4">Truy cập cơ sở dữ liệu điện tử</h5>
                <p>Thư viện cung cấp truy cập đến các cơ sở dữ liệu điện tử sau:</p>
                <ul>
                  <li>Cơ sở dữ liệu sách điện tử nội sinh</li>
                  <li>Cơ sở dữ liệu luận văn, luận án</li>
                  <li>Cơ sở dữ liệu bài báo khoa học</li>
                  <li>Các nguồn học liệu mở</li>
                </ul>
                <p>Để truy cập các cơ sở dữ liệu này, bạn đọc cần đăng nhập bằng tài khoản thư viện.</p>
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
  name: "OpacHuongDan",
  components: {
    OpacSidebar
  }
}
</script> 