/**
 * Utility function để tạo cửa sổ in nhãn phân loại
 * @param {Array} danhSachNhan - Danh sách nhãn phân loại cần in
 * @returns {boolean} - true nếu tạo cửa sổ thành công, false nếu thất bại
 */
export function createClassificationLabelWindow(danhSachNhan) {
    // Tạo cửa sổ in mới
    const printWindow = window.open('', '_blank', 'width=800,height=600');

    if (!printWindow) {
        console.error('Không thể mở cửa sổ popup. Vui lòng kiểm tra cài đặt trình duyệt.');
        return false; // Không thể mở cửa sổ popup
    }

    // Chuẩn bị trang in theo mẫu được cung cấp
    let htmlContent = `<!DOCTYPE html>
<html>
<head>
    <title>In Nhãn Phân Loại</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <style type="text/css"> 
    p.breakhere {page-break-after: always}
    .style3 {font-size: 10px;}
    .pl {font-size:22px; text-align:center;}
    .ct {font-size:18px; text-align:center;}

    .mv{font-size:12px; font-style:normal; font-family:"3 of 9 Barcode";text-align:center;}
    body {
        margin-left: 10px;
        margin-right: 10px;
        margin-top: 2px;
    }
    
    /* Thêm các style cần thiết cho việc in ấn */
    @page {
        size: A4;
        margin: 10mm;
    }
    @media print {
        .no-print {
            display: none;
        }
    }
    
    /* Style cho nút in */
    .print-controls {
        padding: 10px;
        background: #f5f5f5;
        text-align: center;
        margin-bottom: 20px;
    }
    .print-button {
        padding: 8px 15px;
        background: #007bff;
        color: white;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-weight: bold;
    }
    </style>
    
    <script>
        window.onload = function() {
            // Xử lý sự kiện nút in
            const printButton = document.getElementById('print-button');
            if (printButton) {
                printButton.addEventListener('click', function() {
                    window.print();
                });
            }
        };
    </script>
</head>
<body>
    <div class="print-controls no-print">
        <h3>In Nhãn Phân Loại</h3>
        <p>Số lượng nhãn: ${danhSachNhan.length}</p>
        <button class="print-button" id="print-button">In Nhãn</button>
    </div>`;

    // Xử lý danh sách nhãn và tạo bảng
    const labelsPerRow = 5; // 5 nhãn mỗi hàng
    const rows = Math.ceil(danhSachNhan.length / labelsPerRow);
    
    // Xử lý cho từng hàng nhãn
    for (let row = 0; row < rows; row++) {
        const startIndex = row * labelsPerRow;
        const endIndex = Math.min(startIndex + labelsPerRow, danhSachNhan.length);
        const rowLabels = danhSachNhan.slice(startIndex, endIndex);
        
        // Tạo một hàng nhãn
        htmlContent += `
<table width="100%" border="0" cellpadding="0" cellspacing="0" style="padding:0px; margin-top:0px;">
  <tr valign="middle">`;

        // Tạo 5 ô nhãn cho hàng hiện tại
        for (let col = 0; col < labelsPerRow; col++) {
            const labelIndex = col;
            if (labelIndex < rowLabels.length) {
                const nhan = rowLabels[labelIndex];
                const phanLoai1 = nhan.phan_loai_1 || '';
                const phanLoai2 = nhan.phan_loai_2 || '';
                
                htmlContent += `
    <td style="width:16%;" height="64" align="center" valign="top" nowrap>
      <div align="center" style="text-align:center;"><b class="pl">${phanLoai1}</b><br><span style='font-size:22px;'>${phanLoai2}</span></div>
    </td>`;
            } else {
                // Ô trống nếu không đủ nhãn
                htmlContent += `
    <td style="width:16%;" align="center" valign="top" nowrap>
      <div align="center" style="text-align:center;"><b class="pl"></b><br><span style='font-size:22px;'></span></div>
    </td>`;
            }
        }
        
        // Đóng hàng và bảng
        htmlContent += `
  </tr>
</table>`;
        
        // Thêm ngắt trang sau mỗi 15 hàng (75 nhãn)
        if ((row + 1) % 15 === 0 && row < rows - 1) {
            htmlContent += `<p class='breakhere'></p>`;
        }
    }

    // Đóng các thẻ HTML
    htmlContent += `
</body>
</html>`;

    // Ghi HTML vào cửa sổ mới
    printWindow.document.open();
    printWindow.document.write(htmlContent);
    printWindow.document.close();

    return true;
} 