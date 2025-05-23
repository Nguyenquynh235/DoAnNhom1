<?php
session_start();
include 'ket_noi.php';

$ma_phieu = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($ma_phieu > 0) {
    // Lấy thông tin chi tiết phiếu mượn
    $stmt = $conn->prepare("
        SELECT pm.*, bd.ho_ten, bd.ten_dang_nhap, bd.email, bd.so_dien_thoai, bd.dia_chi
        FROM phieu_muon pm 
        JOIN ban_doc bd ON pm.ma_ban_doc = bd.ma_ban_doc 
        WHERE pm.ma_phieu = ?
    ");
    $stmt->bind_param("i", $ma_phieu);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $phieu = $result->fetch_assoc();
    } else {
        echo "Không tìm thấy phiếu mượn!";
        exit;
    }
} else {
    echo "ID phiếu mượn không hợp lệ!";
    exit;
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Phiếu mượn sách #<?= $phieu['ma_phieu'] ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        @media print {
            .no-print { display: none !important; }
            body { margin: 0; }
        }
        body { font-family: 'Times New Roman', serif; }
        .header { text-align: center; margin-bottom: 30px; }
        .logo { font-size: 24px; font-weight: bold; color: #007bff; }
        .content-table td { padding: 8px 12px; }
        .signature-section { margin-top: 50px; }
        .signature-box { text-align: center; padding: 20px; }
    </style>
</head>
<body>
<div class="container mt-4">
    <!-- Nút in -->
    <div class="no-print mb-3">
        <button onclick="window.print()" class="btn btn-primary">
            <i class="fas fa-print"></i> In phiếu
        </button>
        <button onclick="window.close()" class="btn btn-secondary">
            <i class="fas fa-times"></i> Đóng
        </button>
    </div>

    <!-- Nội dung phiếu in -->
    <div class="header">
        <div class="logo">THƯ VIỆN TRƯỜNG ĐẠI HỌC KINH TẾ - KỸ THUẬT CÔNG NGHIỆP</div>
        <div style="font-size: 14px; margin-top: 5px;">Địa chỉ: 456 Minh Khai, Hai Bà Trưng, Hà Nội</div>
        <div style="font-size: 14px;">Điện thoại: (024) 3869 4242 | Email: thuvien@uneti.edu.vn</div>
        <hr style="border: 2px solid #007bff; margin: 20px 0;">
        <h3 style="margin: 20px 0; font-weight: bold;">PHIẾU MƯỢN SÁCH</h3>
        <div style="font-size: 16px;">Số: <?= $phieu['ma_phieu'] ?></div>
    </div>

    <div class="row">
        <div class="col-12">
            <table class="table table-borderless content-table">
                <tr>
                    <td width="25%"><strong>Họ và tên:</strong></td>
                    <td width="40%"><?= htmlspecialchars($phieu['ho_ten']) ?></td>
                    <td width="15%"><strong>Mã độc giả:</strong></td>
                    <td width="20%"><?= htmlspecialchars($phieu['ten_dang_nhap']) ?></td>
                </tr>
                <tr>
                    <td><strong>Số điện thoại:</strong></td>
                    <td><?= htmlspecialchars($phieu['so_dien_thoai']) ?></td>
                    <td><strong>Email:</strong></td>
                    <td><?= htmlspecialchars($phieu['email']) ?></td>
                </tr>
                <tr>
                    <td><strong>Địa chỉ:</strong></td>
                    <td colspan="3"><?= htmlspecialchars($phieu['dia_chi']) ?></td>
                </tr>
            </table>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-12">
            <h5>Thông tin mượn sách:</h5>
            <table class="table table-bordered">
                <thead class="table-light">
                    <tr>
                        <th>STT</th>
                        <th>Tên sách</th>
                        <th>Mã sách</th>
                        <th>Ngày mượn</th>
                        <th>Ngày trả</th>
                        <th>Ghi chú</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>1</td>
                        <td>Thông tin sách (cần JOIN với bảng sach)</td>
                        <td>MS001</td>
                        <td><?= date('d/m/Y', strtotime($phieu['ngay_muon'])) ?></td>
                        <td><?= date('d/m/Y', strtotime($phieu['ngay_tra'])) ?></td>
                        <td>
                            <?= $phieu['trang_thai'] === 'da_tra' ? 'Đã trả' : 'Đang mượn' ?>
                        </td>
                    </tr>
                    <!-- Có thể thêm nhiều dòng sách khác -->
                </tbody>
            </table>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-12">
            <div class="alert alert-info">
                <h6>Quy định mượn sách:</h6>
                <ul class="mb-0">
                    <li>Thời gian mượn sách tối đa: 30 ngày</li>
                    <li>Có thể gia hạn 1 lần nếu không có người đặt trước</li>
                    <li>Trả sách muộn sẽ bị phạt 5,000 VNĐ/ngày/cuốn</li>
                    <li>Làm mất sách phải bồi thường theo quy định</li>
                </ul>
            </div>
        </div>
    </div>

    <div class="signature-section">
        <div class="row">
            <div class="col-6">
                <div class="signature-box">
                    <strong>NGƯỜI MƯỢN</strong><br>
                    <span style="font-style: italic;">(Ký tên)</span>
                    <div style="height: 80px;"></div>
                    <?= htmlspecialchars($phieu['ho_ten']) ?>
                </div>
            </div>
            <div class="col-6">
                <div class="signature-box">
                    <strong>THỦ THƯ</strong><br>
                    <span style="font-style: italic;">(Ký tên, đóng dấu)</span>
                    <div style="height: 80px;"></div>
                    
                </div>
            </div>
        </div>
    </div>

    <div class="text-center mt-4" style="font-size: 12px; color: #666;">
        <em>Ngày in: <?= date('d/m/Y H:i:s') ?></em>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Tự động in khi trang load (tùy chọn)
// window.onload = function() { window.print(); }
</script>
</body>
</html>