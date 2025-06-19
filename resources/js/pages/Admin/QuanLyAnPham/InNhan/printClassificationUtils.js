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
    /*.pl {font-size:22px; text-align:center;}*/
    /*.ct {font-size:18px; text-align:center;}*/

    .mv{font-size:12px; font-style:normal; font-family:"3 of 9 Barcode";text-align:center;}
    /*body {*/
    /*    margin-left: 10px;*/
    /*    margin-right: 10px;*/
    /*    margin-top: 2px;*/
    /*}*/

    /* Thêm các style cần thiết cho việc in ấn */
    /*@page {*/
    /*    size: A4;*/
    /*    margin: 10mm;*/
    /*}*/
    @media print {
        .no-print {
            display: none;
        }
    }

    table.nhan-table {
    width: 100%;
    table-layout: fixed;
    border-collapse: collapse;
    margin: 0;
    padding: 0;
}
td.nhan-cell {
    width: 20%; /* 100% / 5 nhãn */
    height: 17mm;
    padding: 0;
    margin: 0;
    text-align: center;
    vertical-align: middle;
}
td.pl {
    font-size: 22px;
    line-height: 1.2;
}
td.ct {
    font-size: 18px;
}
body {
    margin: 0;
}
@page {
    size: A4;
    margin: 10mm;
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
<table class="nhan-table">
  <tr>`;
        for (let col = 0; col < labelsPerRow; col++) {
            if (col < rowLabels.length) {
                const nhan = rowLabels[col];
                const phanLoai1 = nhan.phan_loai_1 || '';
                const phanLoai2 = nhan.phan_loai_2 || '';
                htmlContent += `
                    <td class="nhan-cell">
                        <b class="pl">${phanLoai1}</b><br>
                        <span class="ct">${phanLoai2}</span>
                    </td>`;
            } else {
                htmlContent += `<td class="nhan-cell"></td>`;
            }
        }
        htmlContent += `
  </tr>
</table>`;

        // Thêm ngắt trang sau mỗi 15 hàng (75 nhãn)
        if ((row + 1) % 16 === 0 && row < rows - 1) {
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
