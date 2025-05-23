<?php
session_start();
include 'ket_noi.php';

if (!isset($_SESSION['ban_doc'])) {
    header('Location: dang_nhap.php');
    exit();
}

$ten_dn = $_SESSION['ban_doc']['ten_dang_nhap'];
$ban_doc = $conn->query("SELECT * FROM ban_doc WHERE ten_dang_nhap = '$ten_dn'")->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ho_ten = $_POST['ho_ten'];
    $ngay_sinh = $_POST['ngay_sinh'];
    $email = $_POST['email'];
    $so_dien_thoai = $_POST['so_dien_thoai'];
    $dia_chi = $_POST['dia_chi'];

    $conn->query("UPDATE ban_doc SET ho_ten='$ho_ten', ngay_sinh='$ngay_sinh', email='$email', so_dien_thoai='$so_dien_thoai', dia_chi='$dia_chi' WHERE ten_dang_nhap = '$ten_dn'");
    echo "<script>
    alert('Cập nhật thành công!');
    window.location.href = 'trang_chu_ban_doc.php';
</script>";
exit();

}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Sửa Thông Tin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <style>
        body {
            margin: 0;
            font-family: Arial;
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
            margin-left: 240px;
            padding: 20px;
            padding-top: 80px;
            min-height: calc(100vh - 60px - 40px);
        }
        .form-container {
            max-width: 700px;
            margin: auto;
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
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
        h4{
            font-size: 42px;
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
    <div class="form-container">
        <h4 class="text-center mb-4 text-primary">Sửa thông tin cá nhân</h4>
        <form method="POST">
            <label class="form-label">Tên đăng nhập</label>
            <input type="text" class="form-control" value="<?= $ban_doc['ten_dang_nhap'] ?>" readonly>

            <label class="form-label">Họ tên</label>
            <input type="text" name="ho_ten" class="form-control" value="<?= $ban_doc['ho_ten'] ?>" required>

            <label class="form-label">Ngày sinh</label>
            <input type="date" name="ngay_sinh" class="form-control" value="<?= $ban_doc['ngay_sinh'] ?>" required>

            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" value="<?= $ban_doc['email'] ?>" required>

            <label class="form-label">Số điện thoại</label>
            <input type="text" name="so_dien_thoai" class="form-control" value="<?= $ban_doc['so_dien_thoai'] ?>" required>

            <label class="form-label">Địa chỉ</label>
            <input type="text" name="dia_chi" class="form-control" value="<?= $ban_doc['dia_chi'] ?>">

            <button type="submit" class="btn btn-primary w-100 mt-3">Cập nhật thông tin</button>
        </form>
    </div>
</div>

<footer class="footer bg-light text-center text-muted py-3 border-top">
    <strong>© 2025 Bản quyền thuộc về Nhóm 1 - DHMT16A1HN</strong>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<?php include 'bong_chat.php'; ?>
</body>
</html>
