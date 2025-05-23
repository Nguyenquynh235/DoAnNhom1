<?php
session_start();
include 'ket_noi.php';

if (!isset($_SESSION['ban_doc'])) {
    header('Location: dang_nhap.php');
    exit();
}

$ban_doc = $_SESSION['ban_doc']; 
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thông tin cá nhân</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
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
        }

        .sidebar a {
            display: block;
            padding: 12px 20px;
            color: white;
            text-decoration: none;
        }

        .sidebar a:hover {
            background-color: #0056b3;
        }

        .sidebar .sidebar-brand {
            text-align: center;
            margin-bottom: 30px;
        }

        .sidebar-icon i {
            font-size: 48px;
        }

        .sidebar-brand-text {
            font-size: 30px;
            font-weight: bold;
            line-height: 1.4;
        }

        header {
            position: fixed;
            top: 0;
            left: 240px;
            right: 0;
            height: 60px;
            background-color: white;
            z-index: 1000;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .main {
            margin-left: 240px;
            padding: 80px 20px 60px; /* chừa khoảng cho footer */
            min-height: calc(100vh - 60px);
        }

        .info-box {
            background: white;
            border-radius: 10px;
            padding: 25px;
            box-shadow: 0 0 10px rgba(0,0,0,0.05);
            max-width: 600px;
            margin: auto;
        }

        .info-box h3 {
            margin-bottom: 20px;
            color: #007bff;
            text-align: center;
        }

        .info-box p {
            margin-bottom: 12px;
        }

        .label {
            font-weight: bold;
            width: 150px;
            display: inline-block;
        }

        footer {
            background-color: #fff;
            color: #333;
            text-align: center;
            padding: 10px 0;
            border-top: 1px solid #ddd;
            position: fixed;
            bottom: 0;
            left: 240px;
            width: calc(100% - 240px);
            z-index: 1000;
        }
    </style>
</head>
<body>

<nav class="navbar navbar-light bg-white shadow-sm px-4 py-2 d-flex justify-content-end align-items-center fixed-top">
    <div class="dropdown">
        <?php if (isset($_SESSION['ban_doc'])): ?>
            <a class="dropdown-toggle text-dark text-decoration-none" href="#" id="accountDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                Xin chào, <strong><?php echo $_SESSION['ban_doc']['ten_dang_nhap']; ?></strong>
            </a>
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="accountDropdown">
                <li><a class="dropdown-item" href="thong_tin_ca_nhan.php">👤 Thông tin cá nhân</a></li>
                <li><a class="dropdown-item" href="sua_thong_tin.php">🛠️ Sửa thông tin</a></li>
                <li><a class="dropdown-item" href="lich_su.php">📖 Lịch sử mượn</a></li>
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
    <!-- Logo và tiêu đề có thể click theo điều kiện -->
    <a href="<?php echo isset($_SESSION['ban_doc']) ? 'trang_chu_ban_doc.php' : 'trang_chu.php'; ?>" 
       class="sidebar-brand d-flex align-items-center justify-content-center flex-column mt-3 mb-4 text-white text-decoration-none">
        <div class="sidebar-icon">
            <i class="fas fa-book-reader fa-3x text-white"></i>
        </div>
        <div class="sidebar-brand-text font-weight-bold mt-2 text-center" style="line-height: 1.4; font-size: 30px;">
            QUẢN LÝ<br>THƯ VIỆN
        </div>
    </a>

    <!-- Mục Trang chủ có điều kiện -->
    <a href="<?php echo isset($_SESSION['ban_doc']) ? 'trang_chu_ban_doc.php' : 'trang_chu.php'; ?>">🏠 Trang chủ</a>

    <!-- Các mục còn lại cố định -->
    <a href="gioi_thieu.php">ℹ️ Giới thiệu</a>
    <a href="lien_he.php">📞 Liên hệ</a>
    <a href="dang_nhap.php">🔐 Đăng nhập</a>
</div>
<!-- Main -->
<div class="main d-flex justify-content-center">
    <div class="info-box w-100" style="max-width: 600px;">

        <h3>👤 Thông tin cá nhân</h3>
        <p><span class="label">Họ tên:</span> <?= $ban_doc['ho_ten'] ?></p>
        <p><span class="label">Tên đăng nhập:</span> <?= $ban_doc['ten_dang_nhap'] ?></p>
        <p><span class="label">Ngày sinh:</span> <?= $ban_doc['ngay_sinh'] ?></p>
        <p><span class="label">Email:</span> <?= $ban_doc['email'] ?></p>
        <p><span class="label">Số điện thoại:</span> <?= $ban_doc['so_dien_thoai'] ?></p>
        <p><span class="label">Địa chỉ:</span> <?= $ban_doc['dia_chi'] ?></p>
        <p><span class="label">Vai trò:</span> <?= $ban_doc['vai_tro'] ?></p>
    </div>
</div>

<!-- Footer chuẩn -->
<footer>
    <strong>© 2025 Bản quyền thuộc về Nhóm 1 - DHMT16A1HN</strong>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
