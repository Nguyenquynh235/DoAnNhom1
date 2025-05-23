<?php
session_start();
include 'ket_noi.php';

if (isset($_POST['dang_nhap'])) {
    $ten_dang_nhap = $_POST['ten_dang_nhap'];
    $mat_khau = $_POST['mat_khau'];

    $sql = "SELECT * FROM ban_doc WHERE ten_dang_nhap = '$ten_dang_nhap'";
    $res = mysqli_query($conn, $sql);
    $ban_doc = mysqli_fetch_assoc($res);

    if ($ban_doc && password_verify($mat_khau, $ban_doc['mat_khau'])) {
        $_SESSION['ban_doc'] = $ban_doc;
        $_SESSION['vai_tro'] = $ban_doc['vai_tro'];

        if ($ban_doc['vai_tro'] === 'admin') {
            header("Location: trang_chu_admin.php");
        } else {
            header("Location: trang_chu_ban_doc.php");
        }
        exit;
    } else {
        echo "Đăng nhập thất bại";
    }
}

?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đăng nhập</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <style>
        html, body {
            height: 100%;
        }

        body {
            background-color: #f5f5f5;
            font-family: Arial, sans-serif;
            margin: 0;
        }

        .wrapper {
            min-height: 100vh;
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
            flex: 1;
            margin-left: 240px;
            padding-top: 80px;
            padding-left: 20px;
            padding-right: 20px;
        }

        .form-wrapper {
            max-width: 500px;
            margin: auto;
            background-color: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }

        h3 {
            text-align: center;
            margin-bottom: 30px;
            font-weight: bold;
            color: #007bff;
            font-size: 38px;
        }

        .form-label {
            font-weight: bold;
        }

        .btn-primary {
            width: 100%;
        }
    </style>
</head>
<body>
<div class="wrapper">

<!-- Navbar -->
<nav class="navbar navbar-light bg-white shadow-sm px-4 py-2 d-flex justify-content-end align-items-center fixed-top">
    <div class="dropdown">
        <a class="dropdown-toggle text-dark text-decoration-none" href="#" id="accountDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            Tài khoản
        </a>
        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="accountDropdown">
            <li><a class="dropdown-item" href="dang_ky.php">📝 Đăng ký</a></li>
        </ul>
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



<!-- Main Content -->
<div class="main">
    <div class="form-wrapper">
        <h3>🔐 Đăng nhập hệ thống</h3>

        <?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
            <div class="alert alert-success text-center">
                ✅ Đăng ký thành công! Bạn có thể đăng nhập ngay.
            </div>
        <?php endif; ?>

        <form method="post" action="xu_ly_dang_nhap.php">
            <div class="mb-3">
                <label class="form-label">Tên đăng nhập</label>
                <input type="text" name="ten_dang_nhap" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Mật khẩu</label>
                <input type="password" name="mat_khau" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary">Đăng nhập</button>
            <div class="text-center mt-3">
                <a href="dang_ky.php" class="text-primary">Chưa có tài khoản? Đăng ký</a>
            </div>
        </form>
    </div>
</div>

<?php include 'footer.php'; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
