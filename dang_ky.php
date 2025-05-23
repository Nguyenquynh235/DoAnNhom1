
<?php
session_start();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đăng ký bạn đọc</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <style>
        body {
            background-color: #f5f5f5;
            font-family: Arial, sans-serif;
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

        .sidebar h4 {
            text-align: center;
            font-size: 20px;
            font-weight: bold;
            padding: 10px;
            margin-bottom: 30px;
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
            padding-top: 80px;
            padding-left: 20px;
            padding-right: 20px;
        }

        .form-wrapper {
            max-width: 650px;
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

<nav class="navbar navbar-light bg-white shadow-sm px-4 py-2 d-flex justify-content-end align-items-center fixed-top">
    <div class="dropdown">
        <a class="dropdown-toggle text-dark text-decoration-none" href="#" id="accountDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            Tài khoản
        </a>
        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="accountDropdown">
            <li><a class="dropdown-item" href="dang_nhap.php">🔐 Đăng nhập</a></li>
        </ul>
    </div>
</nav>

<div class="sidebar">
    <div class="sidebar-brand d-flex align-items-center justify-content-center flex-column mt-3 mb-4">
        <div class="sidebar-icon">
            <i class="fas fa-book-reader fa-3x text-white"></i>
        </div>
        <div class="sidebar-brand-text text-white font-weight-bold mt-2 text-center" style="line-height: 1.4; font-size: 30px;">
            QUẢN LÝ<br>THƯ VIỆN
        </div>
    </div>
    <a href="trang_chu.php">🏠 Trang chủ</a>
    <a href="#">ℹ️ Giới thiệu</a>
    <a href="lien_he.php">📞 Liên hệ</a>
    <a href="dang_nhap.php">🔐 Đăng nhập</a>
</div>

<div class="main">
    <div class="form-wrapper">
        <h3>📝 Đăng ký bạn đọc</h3>
        <form method="post" action="xu_ly_dang_ky.php">
            <div class="row mb-3">
                <div class="col">
                    <label class="form-label">Họ và tên</label>
                    <input type="text" name="ho_ten" class="form-control" required>
                </div>
                <div class="col">
                    <label class="form-label">Tên đăng nhập</label>
                    <input type="text" name="ten_dang_nhap" class="form-control" required>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Ngày sinh</label>
                <input type="text" name="ngay_sinh" id="ngay_sinh" class="form-control" placeholder="dd/mm/yyyy" maxlength="10" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" required>
            </div>

            <div class="row mb-3">
                <div class="col">
                    <label class="form-label">Mật khẩu</label>
                    <input type="password" name="mat_khau" class="form-control" required>
                </div>
                <div class="col">
                    <label class="form-label">Nhập lại mật khẩu</label>
                    <input type="password" name="nhap_lai_mat_khau" class="form-control" required>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col">
                    <label class="form-label">Số điện thoại</label>
                    <input type="text" name="so_dien_thoai" class="form-control" required>
                </div>
                <div class="col">
                    <label class="form-label">Địa chỉ</label>
                    <input type="text" name="dia_chi" class="form-control" required>
                </div>
            </div>

            <button type="submit" class="btn btn-primary">Đăng ký</button>

            <div class="text-center mt-3">
                <a href="dang_nhap.php" class="text-primary">Đã có tài khoản? Đăng nhập</a>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const input = document.getElementById('ngay_sinh');
    input.addEventListener('input', function () {
        let val = input.value.replace(/\D/g, '');
        if (val.length > 2 && val.length <= 4)
            val = val.slice(0, 2) + '/' + val.slice(2);
        else if (val.length > 4)
            val = val.slice(0, 2) + '/' + val.slice(2, 4) + '/' + val.slice(4, 8);
        input.value = val;
    });
});
</script>
<?php include 'footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
