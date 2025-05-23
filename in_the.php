<?php
session_start();
include 'ket_noi.php';

// Kiểm tra quyền admin
if (!isset($_SESSION['ban_doc']) || $_SESSION['ban_doc']['vai_tro'] !== 'admin') {
    header('Location: dang_nhap.php');
    exit;
}

// Lấy ID bạn đọc
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id <= 0) {
    header('Location: quan_ly_the.php');
    exit;
}

// Lấy thông tin bạn đọc
$sql = "SELECT * FROM ban_doc WHERE ma_ban_doc = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header('Location: quan_ly_the.php');
    exit;
}

$ban_doc = $result->fetch_assoc();

// Tạo mã thẻ từ mã bạn đọc
$ma_the = "TV" . str_pad($ban_doc['ma_ban_doc'], 5, "0", STR_PAD_LEFT);

// Tạo ngày cấp thẻ và ngày hết hạn
$ngay_cap = date("d/m/Y");
$ngay_het_han = date("d/m/Y", strtotime("+1 year"));
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>In thẻ bạn đọc</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <style>
        body, html {
            margin: 0;
            font-family: Arial, sans-serif;
            height: 100%;
            background-color: #f5f5f5;
        }
        .sidebar {
            width: 240px;
            background-color: #007bff;
            color: white;
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            padding: 20px 0;
            overflow-y: auto;
            z-index: 999;
        }
        .sidebar a,
        .sidebar .sidebar-section {
            display: block;
            padding: 12px 20px;
            color: white;
            text-decoration: none;
            font-weight: 500;
        }
        .sidebar a:hover,
        .sidebar .sidebar-section:hover {
            background-color: #0056b3;
        }
        .sidebar .sidebar-brand {
            text-align: center;
            padding: 20px 10px;
            border-bottom: 1px solid rgba(255,255,255,0.2);
        }
        .sidebar .sidebar-brand i {
            font-size: 48px;
        }
        .sidebar .sidebar-brand-text {
            font-size: 24px;
            font-weight: bold;
            line-height: 1.4;
            margin-top: 10px;
        }
        .sidebar .collapse a {
            padding-left: 40px;
            font-size: 14px;
            font-weight: normal;
        }

        nav.fixed-top {
            left: 240px;
            width: calc(100% - 240px);
            z-index: 1001;
        }

        .main {
            margin-left: 240px;
            padding: 20px;
            padding-top: 80px;
            flex: 1;
        }

        h2.title {
            font-weight: bold;
            text-align: center;
            margin-bottom: 30px;
            color: #007bff;
        }

        .footer {
            background: #f9f9f9;
            text-align: center;
            padding: 10px 0;
            font-size: 13px;
            color: #555;
            margin-left: 240px;
            width: calc(100% - 240px);
            box-shadow: 0 -1px 3px rgba(0, 0, 0, 0.05);
        }

        .wrapper {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        
        .the-container {
            width: 100%;
            max-width: 800px;
            margin: 0 auto;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        
        .the {
            width: 86mm;  /* Kích thước thẻ tiêu chuẩn */
            height: 54mm; /* Kích thước thẻ tiêu chuẩn */
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
            overflow: hidden;
            margin-bottom: 30px;
            position: relative;
            border: 1px solid #ddd;
        }
        
        .the-header {
            background-color: #007bff;
            color: white;
            padding: 10px;
            text-align: center;
            font-weight: bold;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .the-header img {
            width: 30px;
            height: 30px;
            margin-right: 8px;
        }
        
        .the-body {
            padding: 15px;
            display: flex;
        }
        
        .the-info {
            flex: 1;
        }
        
        .the-info p {
            margin: 5px 0;
            font-size: 12px;
        }
        
        .the-photo {
            width: 60px;
            height: 70px;
            background-color: #f5f5f5;
            border: 1px solid #ddd;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-left: 10px;
        }
        
        .the-photo i {
            font-size: 30px;
            color: #aaa;
        }
        
        .the-footer {
            background-color: #f5f5f5;
            padding: 5px 15px;
            font-size: 10px;
            text-align: center;
            border-top: 1px solid #ddd;
            position: absolute;
            bottom: 0;
            width: 100%;
        }
        
        .print-buttons {
            margin-top: 20px;
            display: flex;
            gap: 10px;
        }
        
        @media print {
            body * {
                visibility: hidden;
            }
            .the, .the * {
                visibility: visible;
            }
            .the {
                position: absolute;
                left: 0;
                top: 0;
                width: 86mm;
                height: 54mm;
                box-shadow: none;
                margin: 0;
            }
            .sidebar, .footer, nav, .print-buttons, h2.title {
                display: none;
            }
        }
    </style>
</head>
<body>

<nav class="navbar navbar-light bg-white shadow-sm px-4 py-2 d-flex justify-content-end align-items-center fixed-top">
    <div class="dropdown">
        <?php if (isset($_SESSION['ban_doc'])): ?>
            <a class="dropdown-toggle text-dark text-decoration-none" href="#" id="accountDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                <?php if (isset($_SESSION['ban_doc']['vai_tro']) && $_SESSION['ban_doc']['vai_tro'] === 'admin'): ?>
                    Xin chào, <strong>Admin</strong>
                <?php else: ?>
                    Xin chào, <strong><?= $_SESSION['ban_doc']['ten_dang_nhap'] ?></strong>
                <?php endif; ?>
            </a>
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="accountDropdown">
                <li><a class="dropdown-item" href="dang_xuat.php">🔓 Đăng xuất</a></li>
            </ul>
        <?php else: ?>
            <a class="dropdown-toggle text-dark text-decoration-none" href="#" id="accountDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                Tài khoản
            </a>
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="accountDropdown">
                <li><a class="dropdown-item" href="dang_nhap.php">🔐 Đăng nhập</a></li>
                <li><a class="dropdown-item" href="dang_ky.php">📝 Đăng ký</a></li>
            </ul>
        <?php endif; ?>
    </div>
</nav>

<div class="sidebar">
    <div class="sidebar-brand">
        <i class="fas fa-book-reader text-white"></i>
        <div class="sidebar-brand-text">QUẢN LÝ THƯ VIỆN</div>
    </div>
    <a href="trang_chu_admin.php"><i class="fas fa-home me-2"></i>Trang chủ</a>
    <a href="quan_ly_sach.php"><i class="fas fa-book me-2"></i>Quản lý sách</a>
    <a href="quan_ly_ban_doc.php"><i class="fas fa-users me-2"></i>Quản lý bạn đọc</a>
    <a href="quan_ly_the.php"><i class="fas fa-id-card me-2"></i>Quản lý thẻ</a>
    <div class="sidebar-section" data-bs-toggle="collapse" href="#muontra" role="button" aria-expanded="false">
        <i class="fas fa-retweet me-2"></i>Quản lý mượn/trả
    </div>
    <div class="collapse" id="muontra">
        <a href="cho_muon.php"><i class="fas fa-arrow-right me-2"></i>Cho mượn sách</a>
        <a href="nhan_tra.php"><i class="fas fa-arrow-left me-2"></i>Nhận trả sách</a>
        <a href="ds_muon_tra.php"><i class="fas fa-list me-2"></i>Danh sách mượn/trả</a>
    </div>
    <div class="sidebar-section" data-bs-toggle="collapse" href="#thongke" role="button" aria-expanded="false">
        <i class="fas fa-chart-bar me-2"></i>Báo cáo thống kê
    </div>
    <div class="collapse" id="thongke">
        <a href="thong_ke_muon_nhieu.php"><i class="fas fa-chart-line me-2"></i>Sách mượn nhiều</a>
        <a href="ban_doc_tich_cuc.php"><i class="fas fa-user-check me-2"></i>Bạn đọc tích cực</a>
        <a href="sach_qua_han.php"><i class="fas fa-clock me-2"></i>Sách quá hạn mượn</a>
    </div>
    <div class="sidebar-section" data-bs-toggle="collapse" href="#hethong" role="button" aria-expanded="false">
        <i class="fas fa-cogs me-2"></i>Quản lý hệ thống
    </div>
    <div class="collapse" id="hethong">
        <a href="quan_ly_tai_khoan.php"><i class="fas fa-user-cog me-2"></i>Quản lý tài khoản</a>
        <a href="quan_ly_kho.php"><i class="fas fa-warehouse me-2"></i>Quản lý kho</a>
        <a href="sao_luu.php"><i class="fas fa-database me-2"></i>Sao lưu dữ liệu</a>
    </div>
    <a href="gioi_thieu.php"><i class="fas fa-info-circle me-2"></i>Giới thiệu</a>
    <a href="lien_he.php"><i class="fas fa-envelope me-2"></i>Liên hệ</a>
</div>

<!-- Nội dung chính -->
<div class="wrapper">
    <div class="main">
        <h2 class="title">🖨️ IN THẺ BẠN ĐỌC</h2>
        
        <div class="the-container">
            <div class="the">
                <div class="the-header">
                    <img src="images/logo_mini.png" alt="Logo" onerror="this.src='data:image/svg+xml;charset=UTF-8,%3Csvg%20width%3D%2230%22%20height%3D%2230%22%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20viewBox%3D%220%200%2030%2030%22%20preserveAspectRatio%3D%22xMidYMid%20slice%22%3E%3Crect%20width%3D%22100%25%22%20height%3D%22100%25%22%20fill%3D%22%23FFFFFF%22%3E%3C%2Frect%3E%3Ctext%20x%3D%2250%25%22%20y%3D%2250%25%22%20style%3D%22fill%3A%23007bff%3Bfont-weight%3Abold%3Bfont-size%3A15px%3Btext-anchor%3Amiddle%3Bdominant-baseline%3Amiddle%22%3ETV%3C%2Ftext%3E%3C%2Fsvg%3E'"> THƯ VIỆN UNETI
                </div>
                <div class="the-body">
                    <div class="the-info">
                        <p><strong>MÃ THẺ:</strong> <?= $ma_the ?></p>
                        <p><strong>Họ tên:</strong> <?= $ban_doc['ho_ten'] ?></p>
                        <p><strong>Email:</strong> <?= $ban_doc['email'] ?></p>
                        <p><strong>SĐT:</strong> <?= $ban_doc['so_dien_thoai'] ?></p>
                        <p><strong>Ngày cấp:</strong> <?= $ngay_cap ?></p>
                        <p><strong>Hết hạn:</strong> <?= $ngay_het_han ?></p>
                    </div>
                    <div class="the-photo">
                        <i class="fas fa-user"></i>
                    </div>
                </div>
                <div class="the-footer">
                    <p>Thẻ này là tài sản của Thư viện UNETI</p>
                    <p>Vui lòng hoàn trả nếu tìm thấy. Địa chỉ: tầng 2, tòa Ha10, 218 Lĩnh Nam, Q. Hoàng Mai, Hà Nội</p>
                </div>
            </div>
            
            <div class="print-buttons">
                <button onclick="window.print()" class="btn btn-primary"><i class="fas fa-print"></i> In thẻ</button>
                <a href="quan_ly_the.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Quay lại</a>
            </div>
        </div>
        
        <div class="card mt-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Hướng dẫn in thẻ</h5>
            </div>
            <div class="card-body">
                <ol>
                    <li>Sử dụng máy in kết nối với máy tính</li>
                    <li>Chuẩn bị giấy in cứng cáp phù hợp (thường là giấy có định lượng ≥ 230g/m²)</li>
                    <li>Kiểm tra thông tin trên thẻ thật kỹ trước khi in</li>
                    <li>Nhấn nút "In thẻ" ở trên</li>
                    <li>Chọn cài đặt in phù hợp (không có viền, kích cỡ thật)</li>
                    <li>Sau khi in, có thể cắt tỉa và ép plastic để bảo vệ thẻ</li>
                </ol>
                <div class="alert alert-info mt-3">
                    <i class="fas fa-info-circle"></i> Lưu ý: Thẻ có kích thước tiêu chuẩn 86mm x 54mm (tương đương kích thước thẻ ATM).
                </div>
                <div class="alert alert-success mt-3">
                    <h6><i class="fas fa-info-circle"></i> Thông tin liên hệ thư viện:</h6>
                    <p class="mb-1">- Địa chỉ: tầng 2, tòa Ha10, 218 Lĩnh Nam, Q. Hoàng Mai, Hà Nội</p>
                    <p class="mb-1">- Điện thoại: (096) 2030 970</p>
                    <p class="mb-1">- Email: thuvienw@uneti.edu.vn</p>
                    <p class="mb-1">- Website: <a href="http://www.uneti.edu.vn" target="_blank">www.uneti.edu.vn</a></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <strong>© 2025 Bản quyền thuộc về UNETI</strong>
    </footer>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>