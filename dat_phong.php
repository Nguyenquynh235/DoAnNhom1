<?php session_start(); ?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đặt phòng</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <style>
        * { box-sizing: border-box; }

        html, body {
            height: 100%;
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
        }

        .sidebar {
            width: 240px;
            background-color: #007bff;
            color: white;
            padding: 20px 0;
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
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
            flex: 1;
            min-height: calc(100vh - 60px);
            padding: 100px 20px 40px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .content-center {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }

        .card {
    transition: 0.3s ease;
    border: 1px solid transparent;
}

.card:hover {
    border: 2px solid black;
    box-shadow: 0 0 8px rgba(0, 0, 0, 0.2);
}

        h2 {
            text-align: center;
            color: #007bff;
            font-weight: bold;
            margin-bottom: 40px;
            font-size: 42px;
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
            position: relative;
            bottom: 0;
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



<!-- Main content -->
<!-- Main content -->
<div class="main">
    <h2>📅 ĐẶT PHÒNG NHÓM</h2>
    <div class="content-center" style="width: 100%;">
        <div class="row justify-content-center text-center" style="max-width: 960px; width: 100%;">
            <div class="col-md-4 mb-4">
                <a href="dat_phong_nho.php" style="text-decoration: none;">
                    <div class="card p-3 shadow-sm blink-hover">
                        <h5 class="text-success">Nhóm 5–7 người</h5>
                        <p>Phù hợp với nhóm học nhỏ cần không gian yên tĩnh.</p>
                    </div>
                </a>
            </div>
            <div class="col-md-4 mb-4">
                <a href="dat_phong_vua.php" style="text-decoration: none;">
                    <div class="card p-3 shadow-sm blink-hover">
                        <h5 class="text-success">Nhóm 7–20 người</h5>
                        <p>Không gian nhóm vừa, có trang thiết bị.</p>
                    </div>
                </a>
            </div>
            <div class="col-md-4 mb-4">
                <a href="dat_phong_lon.php" style="text-decoration: none;">
                    <div class="card p-3 shadow-sm blink-hover">
                        <h5 class="text-success">Nhóm 20–30 người</h5>
                        <p>Phòng hội thảo mini, phù hợp cho thảo luận mở.</p>
                    </div>
                </a>
            </div>
        </div>
    </div>
</div>


<!-- Footer -->
<footer class="footer bg-light text-center text-muted py-3 border-top">
    <strong>© 2025 Bản quyền thuộc về Nhóm 1 - DHMT16A1HN</strong>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
