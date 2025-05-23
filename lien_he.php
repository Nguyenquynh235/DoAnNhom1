<?php
session_start();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Liên hệ - Thư viện</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f7fa;
            font-family: Arial, sans-serif;
            margin: 0;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
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
            padding-top: 80px;
            padding-left: 20px;
            padding-right: 20px;
            flex: 1;
        }
        .contact-box {
            max-width: 800px;
            margin: auto;
            background: white;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        .contact-info h4 {
            color: #007bff;
            font-size: 42px;
            font-weight: bold;
            text-align: center;
            margin-bottom: 30px;
        }
        .contact-info p {
            font-size: 16px;
            margin-bottom: 14px;
            display: flex;
            align-items: center;
        }
        .circle-num {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            color: white;
            display: inline-flex;
            justify-content: center;
            align-items: center;
            font-weight: bold;
            margin-right: 12px;
        }
        .n1 { background-color: #007bff; }
        .n2 { background-color: #28a745; }
        .n3 { background-color: #ffc107; color: black; }
        .n4 { background-color: #fd7e14; }
        .footer {
            background: #f9f9f9;
            text-align: center;
            padding: 15px 0;
            font-size: 14px;
            color: #555;
            position: relative;
            left: 240px;
            width: calc(100% - 240px);
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


<div class="main">
    <div class="contact-box">
        <div class="contact-info">
            <h4>📞 Thông tin liên hệ thư viện</h4>
            <?php
$nd = file_exists('lien_he_noi_dung.txt') ? file_get_contents('lien_he_noi_dung.txt') : 'Chưa có nội dung liên hệ.';
echo '<div class="contact-info">' . nl2br(htmlspecialchars($nd)) . '</div>';
?>

        </div>
    </div>

    <div class="contact-box mt-4 text-center">
        <h3 class="mb-4 fw-bold text-primary">
            <span style="font-size: 42px;">Tìm tôi tại đây👇</span>
        </h3>
        <div>
            <a href="https://www.google.com/maps?q=218+Lĩnh+Nam,+Hoàng+Mai,+Hà+Nội,+Việt+Nam" target="_blank">
                <img src="images/map_location.png" alt="Bản đồ địa chỉ thư viện"
                     style="width: 100%; max-width: 750px; height: auto; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.1);">
            </a>
        </div>
        <div style="margin-top: 20px;">
            <a href="https://www.google.com/maps?q=218+Lĩnh+Nam,+Hoàng+Mai,+Hà+Nội,+Việt+Nam"
               target="_blank"
               style="display: inline-block; background-color: #007bff; color: white; text-decoration: none; padding: 10px 20px; border-radius: 8px; font-weight: 500; font-size: 16px; box-shadow: 0 2px 6px rgba(0,0,0,0.2); transition: background 0.3s;">
                📍 Mở trên Google Maps
            </a>
        </div>
    </div>
</div>

<?php include 'bong_chat.php'; ?>
<?php include 'footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
