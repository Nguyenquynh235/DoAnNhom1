<?php
session_start();
include 'ket_noi.php';
$danh_sach = $conn->query("SELECT * FROM phong WHERE loai_nhom = 'nho'");
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Phòng Nhóm Nhỏ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <style>
        * { box-sizing: border-box; }

        html, body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            height: 100%;
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
            line-height: 1.4;
        }

        nav.fixed-top {
            left: 240px;
            width: calc(100% - 240px);
        }

        .main {
            margin-left: 240px;
            padding: 100px 20px 40px;
            min-height: calc(100vh - 80px);
        }

        h2 {
            text-align: center;
            color: #007bff;
            font-weight: bold;
            margin-bottom: 30px;
        }

        .card-img-top {
            width: 100%;
            height: 180px;
            object-fit: cover;
            object-position: center;
        }

        .card {
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            transition: 0.3s;
        }

        .card:hover {
            animation: blink-border 0.6s linear infinite alternate;
            box-shadow: 0 0 10px 2px #000;
        }

        @keyframes blink-border {
            0% { box-shadow: 0 0 0 2px #000; }
            100% { box-shadow: 0 0 10px 2px #000; }
        }

        .card-body h6 {
            font-size: 15px;
            font-weight: bold;
        }

        .footer {
            background: #fff;
            text-align: center;
            padding: 10px 0;
            font-size: 13px;
            color: #555;
            position: fixed;
            bottom: 0;
            left: 240px;
            width: calc(100% - 240px);
            border-top: 1px solid #ddd;
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



<!-- Nội dung chính -->
<div class="main">
    <h2>📁 DANH SÁCH PHÒNG NHÓM NHỎ (5–7 NGƯỜI)</h2>
    <div class="row">
        <?php while ($p = $danh_sach->fetch_assoc()) { ?>
            <div class="col-md-3 mb-4 d-flex">
                <div class="card shadow-sm w-100 d-flex flex-column justify-content-between">
                    <img src="images/<?= $p['anh'] ?>" class="card-img-top" alt="<?= $p['ten_phong'] ?>">
                    <div class="card-body text-center d-flex flex-column">
                        <h6><?= $p['ten_phong'] ?></h6>
                        <p class="text-muted mb-1">Sức chứa: <?= $p['suc_chua'] ?> người</p>
                        <span class="badge bg-light text-dark mb-2">
                        <?= $p['trang_thai'] === 'da_muon' ? 'Đã Được Sử Dụng' : ($p['trang_thai'] === 'trong' ? 'Phòng Trống' : ucfirst($p['trang_thai'])) ?>
                        </span>

                        <div class="d-grid gap-2">
                            <a href="chi_tiet_phong.php?id=<?= $p['id'] ?>" class="btn btn-primary btn-sm">Chi tiết</a>
                            <?php if ($p['trang_thai'] === 'da_muon') { ?>
                                <button class="btn btn-secondary" disabled>Đã đặt</button>
                            <?php } else { ?>
                                <a href="xac_nhan_dat_phong.php?id_phong=<?= $p['id'] ?>" class="btn btn-success">Đặt phòng</a>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php } ?>
    </div>
</div>


<!-- Footer -->
<footer class="footer">
    <strong>© 2025 Bản quyền thuộc về Nhóm 1 - DHMT16A1HN</strong>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
