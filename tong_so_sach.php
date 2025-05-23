<?php
session_start();
include 'ket_noi.php';

$tu_khoa = $_GET['tu_khoa'] ?? '';
if (!empty($tu_khoa)) {
    $sql = "SELECT * FROM sach WHERE ten_sach LIKE ? OR tac_gia LIKE ? OR nha_xuat_ban LIKE ? OR the_loai LIKE ?";
    $like = '%' . $tu_khoa . '%';
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $like, $like, $like, $like);
    $stmt->execute();
    $danh_sach = $stmt->get_result();
} else {
    $danh_sach = $conn->query("SELECT * FROM sach ORDER BY ma_sach DESC");
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Tổng số sách</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <style>
        body, html {
            margin: 0;
            font-family: Arial, sans-serif;
            height: 100%;
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
            z-index: 1001;
        }

        .main {
            margin-left: 240px;
            padding: 20px;
            padding-top: 80px;
            flex: 1;
        }

        .card-img-top {
            width: 100%;
            height: 160px;
            object-fit: cover;
            object-position: top;
            background-color: #eee;
        }

        h2.title {
            font-weight: bold;
            text-align: center;
            margin-bottom: 30px;
            color: #007bff;
        }

        .card-title {
            font-size: 14px;
            font-weight: 500;
            min-height: 40px;
        }

        .card-body p {
            font-size: 13px;
            margin-bottom: 10px;
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
        }

        .card .btn {
            margin: 3px 0;
        }

        .wrapper {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
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
<div class="wrapper">
    <div class="main">
        <h2 class="title">📚 DANH SÁCH TẤT CẢ SÁCH</h2>

        <!-- Tìm kiếm -->
        <form class="d-flex justify-content-center mb-4" method="GET">
            <input type="text" name="tu_khoa" class="form-control w-50 me-2" placeholder="Tìm theo tên sách, tác giả, NXB hoặc thể loại" value="<?= htmlspecialchars($tu_khoa) ?>">
            <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Tìm kiếm</button>
        </form>

        <!-- Danh sách sách -->
        <div class="row justify-content-center">
            <?php while ($s = $danh_sach->fetch_assoc()) { ?>
                <div class="col-md-2 mb-3 d-flex">
                    <div class="card w-100 d-flex flex-column justify-content-between shadow-sm">
                        <img src="images/<?= $s['anh'] ?>" class="card-img-top" alt="<?= $s['ten_sach'] ?>">
                        <div class="card-body d-flex flex-column justify-content-between text-center p-2">
                            <h6 class="card-title"><?= $s['ten_sach'] ?></h6>
                            <p class="text-muted"><?= $s['the_loai'] ?? 'Chưa rõ' ?></p>
                            <div class="mt-auto">
    <a href="chi_tiet_sach.php?id=<?= $s['ma_sach'] ?>" class="btn btn-primary btn-sm w-100 mb-2">Chi tiết</a>
    <a href="muon_sach.php?id=<?= $s['ma_sach'] ?>" class="btn btn-success btn-sm w-100">Mượn sách</a>
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
</div>

<?php include 'bong_chat.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
