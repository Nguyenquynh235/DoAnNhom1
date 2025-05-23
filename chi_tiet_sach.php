<?php
session_start();
include 'ket_noi.php';

$id = $_GET['id'] ?? 0;
$sql = "SELECT * FROM sach WHERE ma_sach = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$sach = $stmt->get_result()->fetch_assoc();

if (!$sach) {
    die("Không tìm thấy sách.");
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Chi tiết sách</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <style>
        html, body {
            height: 100%;
            margin: 0;
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
        .sidebar .sidebar-brand {
            text-align: center;
            margin-bottom: 30px;
        }
        .sidebar .sidebar-icon i {
            font-size: 48px;
        }
        .sidebar .sidebar-brand-text {
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
            flex: 1;
        }

        .card {
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }

        .card-img-top {
            width: 100%;
            height: 300px;
            object-fit: cover;
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
    <!-- Logo và tiêu đề có thể click -->
    <a href="trang_chu.php" 
       class="sidebar-brand d-flex align-items-center justify-content-center flex-column mt-3 mb-4 text-white text-decoration-none">
        <div class="sidebar-icon">
            <i class="fas fa-book-reader fa-3x text-white"></i>
        </div>
        <div class="sidebar-brand-text font-weight-bold mt-2 text-center" style="line-height: 1.4; font-size: 30px;">
            QUẢN LÝ<br>THƯ VIỆN
        </div>
    </a>

    <!-- Danh sách liên kết -->
    <a href="trang_chu.php">🏠 Trang chủ</a>
    <a href="gioi_thieu.php">ℹ️ Giới thiệu</a>
    <a href="lien_he.php">📞 Liên hệ</a>
    <a href="dang_nhap.php">🔐 Đăng nhập</a>
</div>


<!-- Main Content -->
<div class="wrapper">
    <div class="main">
        <div class="container">
                     <h2 class="text-center text-primary fw-bold mb-4">📖 CHI TIẾT SÁCH</h2>

            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card p-4">
                        <div class="row">
                            <div class="col-md-5">
                                <img src="images/<?= $sach['anh'] ?>" class="card-img-top" alt="<?= $sach['ten_sach'] ?>">
                            </div>
                            <div class="col-md-7">
                                <h4 class="text-primary"><?= $sach['ten_sach'] ?></h4>
                                <p><strong>Tác giả:</strong> <?= $sach['tac_gia'] ?></p>
                                <p><strong>Thể loại:</strong> <?= $sach['the_loai'] ?></p>
                                <p><strong>Nhà xuất bản:</strong> <?= $sach['nha_xuat_ban'] ?></p>
                                <p><strong>Tóm tắt:</strong> <?= $sach['mo_ta'] ?: 'Chưa có mô tả.' ?></p>
                                <div class="d-flex gap-2 mt-3">
    <a href="muon_sach.php?id=<?= $sach['ma_sach'] ?>" class="btn btn-outline-primary">📚 Mượn sách này</a>
    <a href="tong_so_sach.php" class="btn btn-outline-primary">🔍 Xem thêm</a>
</div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer mt-auto border-top">
        <strong>© 2025 Bản quyền thuộc về Nhóm 1 - DHMT16A1HN</strong>
    </footer>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
