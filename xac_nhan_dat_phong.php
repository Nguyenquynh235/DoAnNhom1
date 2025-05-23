<?php
session_start();
include 'ket_noi.php';

if (!isset($_GET['id_phong'])) {
    echo "<script>alert('Chưa chọn phòng!'); window.location.href='dat_phong.php';</script>";
    exit;
}

if (!isset($_SESSION['ban_doc']['ma_ban_doc'])) {
    echo "<script>alert('Bạn chưa đăng nhập!'); window.location.href='dang_nhap.php';</script>";
    exit;
}

$ma_ban_doc = $_SESSION['ban_doc']['ma_ban_doc'];
$ma_phong = intval($_GET['id_phong']);
$ngay_muon = date('Y-m-d');
$ngay_tra = date('Y-m-d', strtotime('+7 days'));
$trang_thai_pm = 'dang_muon';
$thoi_gian_dat = date('Y-m-d H:i:s');
$thoi_gian_tra = date('Y-m-d H:i:s', strtotime('+2 hours'));
$trang_thai_phong = 'da_muon';

$stmt_pm = $conn->prepare("INSERT INTO phieu_muon (ma_ban_doc, ngay_muon, ngay_tra, trang_thai) VALUES (?, ?, ?, ?)");
$stmt_pm->bind_param("isss", $ma_ban_doc, $ngay_muon, $ngay_tra, $trang_thai_pm);
$stmt_pm->execute();
$ma_phieu = $conn->insert_id;

$stmt_dp = $conn->prepare("INSERT INTO chi_tiet_dat_phong (ma_phieu, ma_phong, thoi_gian_dat, thoi_gian_tra, trang_thai, ma_ban_doc)
                           VALUES (?, ?, ?, ?, ?, ?)");
$stmt_dp->bind_param("iissss", $ma_phieu, $ma_phong, $thoi_gian_dat, $thoi_gian_tra, $trang_thai_phong, $ma_ban_doc);
$stmt_dp->execute();

$conn->query("UPDATE phong SET trang_thai = 'da_muon', ma_phieu = $ma_phieu WHERE id = $ma_phong");

echo "<script>alert('✅ Đặt phòng thành công!'); window.location.href='lich_su.php';</script>";
?>


<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Xác nhận đặt phòng</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <style>
        * { box-sizing: border-box; }

        html, body {
            margin: 0;
            padding: 0;
            height: 100%;
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
        }

        .sidebar {
            width: 240px;
            background-color: #007bff;
            color: white;
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            padding-top: 20px;
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

        .sidebar-brand {
            text-align: center;
            margin-bottom: 30px;
        }

        .sidebar-icon i {
            font-size: 48px;
        }

        .sidebar-brand-text {
            font-size: 30px;
            font-weight: bold;
        }

        nav.fixed-top {
            left: 240px;
            width: calc(100% - 240px);
        }

        .main {
            margin-left: 240px;
            padding: 100px 20px 40px;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: calc(100vh - 100px);
        }

        .card {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            max-width: 500px;
            width: 100%;
            text-align: center;
        }

        .footer {
            background-color: #fff;
            color: #555;
            font-size: 13px;
            text-align: center;
            padding: 10px 0;
            position: fixed;
            bottom: 0;
            left: 240px;
            width: calc(100% - 240px);
            border-top: 1px solid #ddd;
        }
    </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-light bg-white shadow-sm px-4 py-2 fixed-top d-flex justify-content-end">
    <div class="dropdown">
        <a class="dropdown-toggle text-dark text-decoration-none" href="#" data-bs-toggle="dropdown">
            Tài khoản
        </a>
        <ul class="dropdown-menu dropdown-menu-end">
            <li><a class="dropdown-item" href="dang_xuat.php">🔓 Đăng xuất</a></li>
        </ul>
    </div>
</nav>

<!-- Sidebar -->
<div class="sidebar">
    <div class="sidebar-brand d-flex flex-column align-items-center">
        <div class="sidebar-icon">
            <i class="fas fa-book-reader text-white"></i>
        </div>
        <div class="sidebar-brand-text mt-2">QUẢN LÝ<br>THƯ VIỆN</div>
    </div>
    <a href="trang_chu_ban_doc.php">🏠 Trang chủ</a>
    <a href="sach.php">📘 Sách</a>
    <a href="dat_phong.php">🪑 Đặt phòng</a>
    <a href="noi_quy_thu_vien.php">📜 Nội quy</a>
    <a href="lien_he.php">📞 Liên hệ</a>
</div>

<!-- Main Content -->
<div class="main">
    <div class="card">
        <h4 class="text-success mb-3">✅ Đặt phòng thành công!</h4>
        <p>Mã phiếu: <strong><?= $ma_phieu ?></strong></p>
        <p>Mã phòng: <strong><?= $ma_phong ?></strong></p>
        <a href="dat_phong.php" class="btn btn-primary mt-3">🔙 Quay lại danh sách phòng</a>
    </div>
</div>

<!-- Footer -->
<footer class="footer">
    <strong>© 2025 Bản quyền thuộc về Nhóm 1 - DHMT16A1HN</strong>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
