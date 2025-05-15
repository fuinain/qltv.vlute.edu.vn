/**
 * Utility function để tạo cửa sổ in nhãn DKCB
 * @param {Array} danhSachNhan - Danh sách nhãn cần in
 * @returns {boolean} - true nếu tạo cửa sổ thành công, false nếu thất bại
 */
export function createBarcodeWindow(danhSachNhan) {
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
    <title>In Nhãn DKCB</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <style type="text/css"> 
    p.breakhere {page-break-after: always}
    .style9 {font-size: 9px}
    .style12 {
        font-size: 18px;
        font-weight: bold;
        font-family:Arial, Helvetica, sans-serif, sans-serif;
    }
    .style21 {font-size: 14px; font-family: Arial, Helvetica, sans-serif; }
    .style25 {font-family: Arial, Helvetica, sans-serif; font-size: 12px; }
    .style28 {font-size: 16px; font-family: Arial, Helvetica, sans-serif; font-weight: bold; }
    .style29 {font-family:"3 of 9 Barcode"; font-size:16px; word-spacing:1px; letter-spacing:1px; font-size-adjust:20px;}
    .style30 {font-size: 12px; }
    .cb {font-size: 14px; font-family: Arial, Helvetica, sans-serif; font-weight:bold; }

    body {
        margin-left: 0px;
        margin-top: 10px;
        margin-right: 0px;
        margin-bottom: 0px;
    }
    body,td,th {
        font-family: Times New Roman, Times, serif;
    }
    
    /* Style cho barcode sử dụng JsBarcode */
    .barcode-svg {
        height: 25px;
        width: 100%;
        max-width: 120px;
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
    
    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>
    <script>
        // Hàm render barcode bằng JsBarcode
        function renderBarcodes() {
            const barcodeContainers = document.querySelectorAll('.barcode-container');
            barcodeContainers.forEach(container => {
                const maDkcb = container.getAttribute('data-barcode');
                if (maDkcb) {
                    const svgElement = container.querySelector('svg');
                    if (svgElement) {
                        try {
                            JsBarcode(svgElement, maDkcb, {
                                width: 1,
                                height: 25,
                                displayValue: false,
                                margin: 0,
                                lineColor: "#000",
                                background: "#ffffff",
                                format: "CODE39"
                            });
                            svgElement.style.display = 'block';
                        } catch (e) {
                            console.error('Lỗi tạo mã vạch:', e);
                        }
                    }
                }
            });
        }
        
        window.onload = function() {
            // Kiểm tra và chạy JsBarcode ngay
            if (window.JsBarcode) {
                renderBarcodes();
            } else {
                console.error("Không thể tải thư viện JsBarcode");
            }
            
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
        <h3>In Nhãn DKCB</h3>
        <p>Số lượng nhãn: ${danhSachNhan.length}</p>
        <button class="print-button" id="print-button">In Nhãn</button>
    </div>`;

    // Xử lý danh sách nhãn và tạo bảng
    const labelsPerPage = 80; // 16 hàng x 5 cột = 80 nhãn trên 1 trang
    const totalPages = Math.ceil(danhSachNhan.length / labelsPerPage);

    // Biến đếm nhãn tổng
    let totalLabelCount = 0;

    // Xử lý cho từng trang
    for (let pageIndex = 0; pageIndex < totalPages; pageIndex++) {
        const startIndex = pageIndex * labelsPerPage;
        const endIndex = Math.min(startIndex + labelsPerPage, danhSachNhan.length);
        const pageLabels = danhSachNhan.slice(startIndex, endIndex);

        // Mỗi trang có 16 hàng, mỗi hàng có 5 nhãn
        const rowsPerPage = 16;
        const labelsPerRow = 5;

        // Tạo các hàng của trang
        for (let row = 0; row < rowsPerPage; row++) {
            if (totalLabelCount >= danhSachNhan.length && row === 0) {
                // Nếu không còn nhãn nào và đây là hàng đầu tiên của trang mới, không tạo trang mới
                break;
            }

            if (totalLabelCount >= danhSachNhan.length) {
                // Nếu không còn nhãn nào mà vẫn còn hàng cần tạo, bỏ qua
                continue;
            }

            // Tạo 1 hàng mới với 5 nhãn
            htmlContent += `
<table width="100%" style="height:64px; margin-bottom:0px;" border="0" align="center" cellpadding="0" cellspacing="0" bordercolor="#CCCCFF">
  <tr valign="bottom" style="height:64px;">`;

            // Tạo 5 cột nhãn cho hàng hiện tại
            for (let col = 0; col < labelsPerRow; col++) {
                const labelIndex = row * labelsPerRow + col;
                if (labelIndex < pageLabels.length) {
                    const nhan = pageLabels[labelIndex];
                    const maDkcb = nhan.ma_dkcb || '';

                    htmlContent += `
    <td width="20%" align="center" valign="middle" nowrap="nowrap" style="background:url(khung_cb.png) no-repeat; background-position:center;">
        <div class="barcode-container" data-barcode="${maDkcb}">
            <div align="center" style="margin-top:7px; display:none;"><span class="style29">*${maDkcb}*</span></div>
            <svg class="barcode-svg"></svg>
            <span class="cb">${maDkcb}</span>
        </div>
    </td>`;
                } else {
                    // Tạo ô trống nếu không đủ nhãn
                    htmlContent += `
    <td width="20%" align="center" valign="middle" nowrap="nowrap" style="background:url(khung_cb.png) no-repeat; background-position:center;">
    </td>`;
                }
            }

            // Đóng hàng và bảng
            htmlContent += `
  </tr>
</table>`;

            totalLabelCount += labelsPerRow;
        }

        // Thêm page break sau mỗi trang (trừ trang cuối)
        if (pageIndex < totalPages - 1) {
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
