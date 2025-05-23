<?php
session_start();
include 'ket_noi.php';

$sql = "SELECT * FROM sach WHERE the_loai = 'Ngôn ngữ học'";
$danh_sach = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Sách Ngôn ngữ học</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <style>
        html, body {
            margin: 0;
            padding: 0;
            height: 100%;
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
        }

        body {
            display: flex;
            flex-direction: column;
        }

        .wrapper {
            flex: 1;
            display: flex;
            flex-direction: column;
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

        .sidebar a {
            display: block;
            padding: 12px 20px;
            color: white;
            text-decoration: none;
        }

        .sidebar a:hover {
            background-color: #0056b3;
        }

        nav.fixed-top {
            left: 240px;
            width: calc(100% - 240px);
        }

        .main {
            margin-left: 240px;
            padding: 100px 20px 40px;
            flex: 1;
        }

        .card-img-top {
            width: 100%;
            height: 180px;
            object-fit: cover;
        }

        .card:hover {
            animation: blink-border 0.6s linear infinite alternate;
            box-shadow: 0 0 10px 2px #000;
        }

        @keyframes blink-border {
            0% { box-shadow: 0 0 0 2px #000; }
            100% { box-shadow: 0 0 10px 2px #000; }
        }

        h2 {
            text-align: center;
            color: #007bff;
            font-weight: bold;
            margin-bottom: 30px;
        }

        .footer {
            background: #f9f9f9;
            text-align: center;
            padding: 12px 0;
            font-size: 13px;
            color: #555;
            margin-left: 240px;
            width: calc(100% - 240px);
            box-shadow: 0 -1px 3px rgba(0, 0, 0, 0.05);
        }

        .card {
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        .card-body {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
    </style>
</head>
<body>

<nav class="navbar navbar-light bg-white shadow-sm px-4 py-2 fixed-top d-flex justify-content-end">
    <div class="dropdown">
        <a class="dropdown-toggle text-dark text-decoration-none" href="#" data-bs-toggle="dropdown">
            Tài khoản
        </a>
        <ul class="dropdown-menu dropdown-menu-end">
            <?php if (isset($_SESSION['user'])): ?>
                <li><span class="dropdown-item-text">👤 <?= $_SESSION['user']['ten_dang_nhap'] ?></span></li>
                <li><a class="dropdown-item" href="dang_xuat.php">🔓 Đăng xuất</a></li>
            <?php else: ?>
                <li><a class="dropdown-item" href="dang_nhap.php">🔐 Đăng nhập</a></li>
            <?php endif; ?>
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
        <h2>📘 DANH SÁCH SÁCH THỂ LOẠI NGÔN NGỮ HỌC</h2>
        <div class="row justify-content-center">
            <?php while ($s = $danh_sach->fetch_assoc()) { ?>
                <div class="col-md-3 mb-4 d-flex">
                    <div class="card shadow-sm w-100">
                        <img src="images/<?= $s['anh'] ?>" class="card-img-top" alt="<?= $s['ten_sach'] ?>">
                        <div class="card-body d-flex flex-column justify-content-between text-center">
                            <div>
                                <h6><?= $s['ten_sach'] ?></h6>
                                <p class="text-muted mb-3"><?= $s['tac_gia'] ?></p>
                            </div>
                            <div>
                                <a href="chi_tiet_sach.php?id=<?= $s['ma_sach'] ?>" class="btn btn-primary btn-sm w-100 mb-2">Chi tiết</a>
                                <a href="muon_sach.php?id=<?= $s['ma_sach'] ?>" class="btn btn-success btn-sm w-100">Mượn sách</a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>

    <footer class="footer mt-auto border-top">
        <strong>© 2025 Bản quyền thuộc về Nhóm 1 - DHMT16A1HN</strong>
    </footer>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
