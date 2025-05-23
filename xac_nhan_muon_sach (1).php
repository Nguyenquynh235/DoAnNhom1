<?php
session_start();
include 'ket_noi.php';

if (!isset($_SESSION['gio_muon']) || empty($_SESSION['gio_muon'])) {
    echo "<script>alert('Giỏ mượn đang trống!'); window.location.href='muon_sach.php';</script>";
    exit;
}

if (!isset($_SESSION['ban_doc']['ma_ban_doc'])) {
    echo "<script>alert('Không xác định được bạn đọc!'); window.location.href='muon_sach.php';</script>";
    exit;
}

$ma_ban_doc = $_SESSION['ban_doc']['ma_ban_doc'];
$ngay_muon = date('Y-m-d');
$ngay_tra = date('Y-m-d', strtotime('+30 days'));
$trang_thai = 'dang_muon';
$ma_sachs = $_SESSION['gio_muon'];

// 1. Thêm vào phiếu mượn
$stmt = $conn->prepare("INSERT INTO phieu_muon (ma_ban_doc, ngay_muon, ngay_tra, trang_thai) VALUES (?, ?, ?, ?)");
$stmt->bind_param("isss", $ma_ban_doc, $ngay_muon, $ngay_tra, $trang_thai);
$stmt->execute();
$ma_phieu = $stmt->insert_id;

// 2. Thêm chi tiết mượn
$stmt_ct = $conn->prepare("INSERT INTO chi_tiet_muon (ma_phieu, ma_sach, so_luong, ten_sach, ngay_muon, ngay_tra, trang_thai, ma_ban_doc) VALUES (?, ?, 1, ?, ?, ?, ?, ?)");

foreach ($ma_sachs as $ma_sach) {
    $result = $conn->query("SELECT ten_sach FROM sach WHERE ma_sach = $ma_sach");
    if ($result->num_rows === 0) continue;

    $sach = $result->fetch_assoc();
    $ten_sach = $sach['ten_sach'];
    $stmt_ct->bind_param("iissssi", $ma_phieu, $ma_sach, $ten_sach, $ngay_muon, $ngay_tra, $trang_thai, $ma_ban_doc);
    $stmt_ct->execute();
}

// 3. Xóa giỏ mượn
unset($_SESSION['gio_muon']);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Xác nhận mượn sách</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body, html { margin: 0; height: 100%; font-family: Arial, sans-serif; background-color: #f5f5f5; }
        .sidebar { width: 240px; background-color: #007bff; color: white; position: fixed; height: 100vh; padding: 20px 0; }
        .sidebar a { display: block; padding: 12px 20px; color: white; text-decoration: none; }
        .sidebar a:hover { background-color: #0056b3; }
        nav.fixed-top { left: 240px; width: calc(100% - 240px); }
        .wrapper { min-height: 100vh; display: flex; flex-direction: column; }
        .main { margin-left: 240px; padding: 100px 20px 40px; flex: 1; }
        .footer { background: #f9f9f9; text-align: center; padding: 12px 0; font-size: 13px; color: #555; margin-left: 240px; width: calc(100% - 240px); }
        .card { max-width: 600px; margin: 0 auto; padding: 30px; text-align: center; }
    </style>
</head>
<body>
<nav class="navbar navbar-light bg-white shadow-sm px-4 py-2 fixed-top d-flex justify-content-end">
    <div class="dropdown">
        <a class="dropdown-toggle text-dark text-decoration-none" href="#" data-bs-toggle="dropdown">Tài khoản</a>
        <ul class="dropdown-menu dropdown-menu-end">
            <li><a class="dropdown-item" href="dang_xuat.php">🔓 Đăng xuất</a></li>
        </ul>
    </div>
</nav>
<div class="sidebar">
    <div class="sidebar-brand d-flex flex-column align-items-center mt-3 mb-4">
        <i class="fas fa-book-reader fa-3x text-white"></i>
        <div class="sidebar-brand-text text-white text-center mt-2">QUẢN LÝ<br>THƯ VIỆN</div>
    </div>
    <a href="trang_chu_ban_doc.php">🏠 Trang chủ</a>
    <a href="sach.php">📘 Sách</a>
    <a href="dat_phong.php">🪑 Đặt phòng</a>
    <a href="noi_quy_thu_vien.php">📜 Nội quy</a>
    <a href="lien_he.php">📞 Liên hệ</a>
</div>
<div class="wrapper">
    <div class="main">
        <div class="card shadow-sm bg-white">
            <h2 class="text-success mb-3">✅ Mượn sách thành công!</h2>
            <p>Phiếu mượn #<?= $ma_phieu ?> đã được ghi nhận với <?= count($ma_sachs) ?> đầu sách.</p>
            <p class="text-info">📅 Hạn trả sách là: <strong><?= date('d/m/Y', strtotime($ngay_tra)) ?></strong></p>
            <a href="sach.php" class="btn btn-primary mt-3">🔙 Quay lại danh sách sách</a>
        </div>
    </div>
    <footer class="footer mt-auto border-top">
        <strong>© 2025 Bản quyền thuộc về Nhóm 1 - DHMT16A1HN</strong>
    </footer>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
