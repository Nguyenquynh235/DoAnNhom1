<?php
session_start();
include 'ket_noi.php';

if (!isset($_SESSION['ban_doc'])) {
    header('Location: dang_nhap.php');
    exit();
}

$ma_bd = $_SESSION['ban_doc']['ma_ban_doc'];

$muon_sach = $conn->query("SELECT s.ten_sach, p.ngay_muon, p.ngay_tra
                           FROM phieu_muon p
                           JOIN chi_tiet_muon ct ON p.ma_phieu = ct.ma_phieu
                           JOIN sach s ON ct.ma_sach = s.ma_sach
                           WHERE p.ma_ban_doc = '$ma_bd'");

$dat_phong = $conn->query("SELECT ma_phong, thoi_gian_dat, thoi_gian_tra 
                           FROM chi_tiet_dat_phong 
                           WHERE ma_ban_doc = '$ma_bd'");
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Lịch sử mượn</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <style>
        html, body {
            height: 100%;
            margin: 0;
            font-family: Arial;
            background-color: #f5f5f5;
        }

        .wrapper {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            margin-left: 240px;
        }

        .sidebar {
            width: 240px;
            background-color: #007bff;
            color: white;
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            padding-top: 20px;
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
            z-index: 1000;
        }

        .main {
            flex: 1;
            padding: 20px;
            padding-top: 80px;
        }

        .table-container {
            max-width: 1000px;
            margin: auto;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.05);
        }

        footer {
            background-color: white;
            color: black;
            text-align: center;
            padding: 8px;
            border-top: 1px solid #ccc;
            width: 100%;
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

<div class="wrapper">
    <div class="main">
        <div class="table-container">
            <h4 class="text-primary mb-3">📚 Lịch sử mượn sách</h4>
            <table class="table table-bordered table-hover">
                <thead class="table-light">
                    <tr><th>Tên sách</th><th>Ngày mượn</th><th>Ngày trả</th></tr>
                </thead>
                <tbody>
                    <?php while ($row = $muon_sach->fetch_assoc()): ?>
                        <tr>
                            <td><?= $row['ten_sach'] ?></td>
                            <td><?= $row['ngay_muon'] ?></td>
                            <td><?= $row['ngay_tra'] ?: 'Chưa trả' ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>

            <h4 class="text-primary mt-5 mb-3">🏫 Lịch sử đặt phòng</h4>
            <table class="table table-bordered table-hover">
                <thead class="table-light">
                    <tr><th>Mã phòng</th><th>Thời gian đặt</th><th>Thời gian trả</th></tr>
                </thead>
                <tbody>
                    <?php while ($row = $dat_phong->fetch_assoc()): ?>
                        <tr>
                            <td><?= $row['ma_phong'] ?></td>
                            <td><?= $row['thoi_gian_dat'] ?></td>
                            <td><?= $row['thoi_gian_tra'] ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <footer class="footer bg-light text-center text-muted py-3 border-top">
        <strong>© 2025 Bản quyền thuộc về Nhóm 1 - DHMT16A1HN</strong>
    </footer>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<?php include 'bong_chat.php'; ?>
</body>
</html>
