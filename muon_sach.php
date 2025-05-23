<?php
session_start();
include 'ket_noi.php';

// Khởi tạo giỏ mượn nếu chưa có
if (!isset($_SESSION['gio_muon'])) {
    $_SESSION['gio_muon'] = [];
}

// Nếu có id từ GET, thêm vào giỏ nếu chưa có
if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    if (!in_array($id, $_SESSION['gio_muon'])) {
        $_SESSION['gio_muon'][] = $id;
    }
}

// Xử lý xóa sách khỏi giỏ
if (isset($_GET['xoa'])) {
    $xoa = (int)$_GET['xoa'];
    $_SESSION['gio_muon'] = array_filter($_SESSION['gio_muon'], fn($s) => $s != $xoa);
}

// Lấy thông tin sách trong giỏ
$danh_sach = [];
if (!empty($_SESSION['gio_muon'])) {
    $placeholders = implode(',', array_fill(0, count($_SESSION['gio_muon']), '?'));
    $stmt = $conn->prepare("SELECT * FROM sach WHERE ma_sach IN ($placeholders)");
    $stmt->bind_param(str_repeat('i', count($_SESSION['gio_muon'])), ...$_SESSION['gio_muon']);
    $stmt->execute();
    $danh_sach = $stmt->get_result();
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Giỏ mượn sách</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
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
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            padding: 20px 0;
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
        .wrapper {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .main {
            margin-left: 240px;
            padding: 100px 20px 40px;
            flex: 1;
        }
        .footer {
            background: #f9f9f9;
            text-align: center;
            padding: 12px 0;
            font-size: 13px;
            color: #555;
            margin-left: 240px;
            width: calc(100% - 240px);
        }
        h2 {
            text-align: center;
            color: #007bff;
            font-weight: bold;
            margin-bottom: 30px;
        }
        .card-img-top {
            height: 200px;
            object-fit: cover;
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
    <a href="sach.php">📘 Sách</a>
    <a href="dat_phong.php">🪑 Đặt phòng</a>
    <a href="noi_quy_thu_vien.php">📜 Nội quy</a>
    <a href="lien_he.php">📞 Liên hệ</a>
</div>

<div class="wrapper">
    <div class="main">
        <h2>🛒 GIỎ MƯỢN SÁCH</h2>
        <div class="row justify-content-center">
            <?php if (!empty($danh_sach)) { foreach ($danh_sach as $s) { ?>
                <div class="col-md-3 mb-4 d-flex">
                    <div class="card shadow-sm w-100" style="height: 100%; display: flex; flex-direction: column; justify-content: space-between;">
                        <img src="images/<?= $s['anh'] ?>" class="card-img-top" alt="<?= $s['ten_sach'] ?>">
                        <div class="card-body text-center">
                            <h6><?= $s['ten_sach'] ?></h6>
                            <p class="text-muted mb-2"><?= $s['tac_gia'] ?></p>
                            <a href="muon_sach.php?xoa=<?= $s['ma_sach'] ?>" class="btn btn-danger btn-sm w-100">❌ Xóa khỏi giỏ</a>
                        </div>
                    </div>
                </div>
            <?php }} else { ?>
                <p class="text-center">Chưa có sách nào trong giỏ mượn.</p>
            <?php } ?>
        </div>
        <?php if (!empty($danh_sach)) { ?>
            <div class="text-center">
                <a href="xac_nhan_muon_sach.php" class="btn btn-primary btn-lg px-5">📥 Xác nhận mượn sách</a>
            </div>
        <?php } ?>
    </div>

    <footer class="footer mt-auto border-top">
        <strong>© 2025 Bản quyền thuộc về Nhóm 1 - DHMT16A1HN</strong>
    </footer>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>