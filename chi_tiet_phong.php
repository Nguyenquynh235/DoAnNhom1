<?php
session_start();
include 'ket_noi.php';

$id = $_GET['id'] ?? 0;
$stmt = $conn->prepare("SELECT * FROM phong WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$phong = $stmt->get_result()->fetch_assoc();
if (!$phong) {
    die("Không tìm thấy phòng.");
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Chi tiết phòng</title>
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
            padding: 100px 40px 40px;
            margin-left: 240px;
            display: flex;
            justify-content: center;
        }
        .main > .content {
            max-width: 800px;
            width: 100%;
        }
        .main h2 {
            color: #007bff;
            font-weight: bold;
            text-align: center;
            margin-bottom: 30px;
        }
        .main img {
            width: 100%;
            max-height: 350px;
            object-fit: cover;
            border-radius: 8px;
            margin-bottom: 25px;
        }
        .main p {
            font-size: 16px;
            margin-bottom: 12px;
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
        .blink-hover:hover {
            animation: blink-border 0.6s linear infinite alternate;
            box-shadow: 0 0 0 2px #000;
        }
        @keyframes blink-border {
            0%   { box-shadow: 0 0 0 2px #000; }
            100% { box-shadow: 0 0 10px 2px #000; }
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
    <div class="text-center mb-4">
        <i class="fas fa-book-reader fa-3x text-white"></i>
        <div style="font-size: 24px; font-weight: bold;">QUẢN LÝ<br>THƯ VIỆN</div>
    </div>
    <a href="trang_chu_ban_doc.php">🏠 Trang chủ</a>
    <a href="sach.php">📘 Sách</a>
    <a href="dat_phong.php">🪑 Đặt phòng</a>
    <a href="noi_quy_thu_vien.php">📜 Nội quy</a>
    <a href="lien_he.php">📞 Liên hệ</a>
</div>
<div class="main">
    <div class="content">
        <h2>📝 Chi tiết phòng: <?= $phong['ten_phong'] ?></h2>
        <img src="images/<?= $phong['anh'] ?>" alt="<?= $phong['ten_phong'] ?>">
        <p><strong>Sức chứa:</strong> <?= $phong['suc_chua'] ?> người</p>
        <p><strong>Trạng thái:</strong>
        <?= $phong['trang_thai'] === 'da_muon' ? 'Đã Được Sử Dụng' : ($phong['trang_thai'] === 'Trong' ? 'Trống' : ucfirst($phong['trang_thai'])) ?>
        </p>

        <p><strong>Loại nhóm:</strong> <?= ucfirst($phong['loai_nhom']) ?></p>
        <p><strong>Mô tả:</strong><br><?= nl2br($phong['mo_ta'] ?? 'Chưa có mô tả.') ?></p>
        <div class="d-flex gap-2 mt-3">
    <?php if ($phong['trang_thai'] === 'da_muon') { ?>
        <button class="btn btn-secondary" disabled>📚 Đã được sử dụng</button>
    <?php } else { ?>
        <a href="xac_nhan_dat_phong.php?id_phong=<?= $phong['id'] ?>" class="btn btn-outline-primary blink-hover">📚 Mượn phòng này</a>
    <?php } ?>
    <a href="dat_phong.php" class="btn btn-outline-primary">🔍 Xem thêm</a>
</div>

    </div>
</div>
<footer class="footer mt-4 border-top">
    <strong>© 2025 Bản quyền thuộc về Nhóm 1 - DHMT16A1HN</strong>
</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
