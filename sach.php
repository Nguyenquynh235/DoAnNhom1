<?php
session_start();
include 'ket_noi.php';

// Tổng số sách
$tong_sach = $conn->query("SELECT COUNT(*) FROM sach")->fetch_row()[0];

// Thể loại đếm
$the_loai = ['Tin học', 'Ngôn ngữ học', 'Kế toán', 'Văn học', 'Tâm lý học', 'Pháp luật'];
$dem_the_loai = [];
foreach ($the_loai as $tl) {
    $stmt = $conn->prepare("SELECT COUNT(*) FROM sach WHERE the_loai = ?");
    $stmt->bind_param("s", $tl);
    $stmt->execute();
    $dem_the_loai[$tl] = $stmt->get_result()->fetch_row()[0];
}

// Xử lý tìm kiếm
$tu_khoa = $_GET['tu_khoa'] ?? '';
$ket_qua = [];
if (!empty($tu_khoa)) {
    $sql = "SELECT * FROM sach WHERE ten_sach LIKE ? OR tac_gia LIKE ? OR nha_xuat_ban LIKE ? OR the_loai LIKE ?";
    $like = "%" . $tu_khoa . "%";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $like, $like, $like, $like);
    $stmt->execute();
    $ket_qua = $stmt->get_result();
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý Sách</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <style>
        body { margin: 0; font-family: Arial; background-color: #f5f5f5; }
        .sidebar {
            width: 240px; background-color: #007bff; color: white;
            height: 100vh; position: fixed; top: 0; left: 0;
            padding: 20px 0; overflow-y: auto; z-index: 999;
        }
        .sidebar a {
            display: block; padding: 12px 20px;
            color: white; text-decoration: none;
        }
        .sidebar a:hover { background-color: #0056b3; }
        .sidebar .sidebar-brand { text-align: center; margin-bottom: 30px; }
        .sidebar .sidebar-icon i { font-size: 48px; }
        .sidebar .sidebar-brand-text {
            font-size: 30px; font-weight: bold; line-height: 1.4;
        }
        nav.fixed-top {
            left: 240px;
            width: calc(100% - 240px);
        }
        .main {
            margin-left: 240px;
            padding: 20px;
            padding-top: 80px;
        }
        html, body {
    height: 100%;
    margin: 0;
    display: flex;
    flex-direction: column;
}

.wrapper {
    flex: 1;
    display: flex;
    flex-direction: column;
}
.main {
    flex: 1;
}
@keyframes blink-border {
    0%   { box-shadow: 0 0 0 2px #000; }
    100% { box-shadow: 0 0 10px 2px #000; }
}

.blink-hover:hover {
    animation: blink-border 0.6s linear infinite alternate !important;
    box-shadow: 0 0 0 2px #000 !important;
}
        .card {
            transition: 0.3s;
        }
        .card:hover {
            box-shadow: 0 0 10px rgba(0,0,0,0.2);
        }
        .search-box {
            margin-bottom: 20px;
        }
        .footer {
            background: #f9f9f9;
            text-align: center;
            padding: 10px 0;
            font-size: 13px;
            color: #555;
            margin-top: 50px;
            margin-left: 240px;
            width: calc(100% - 240px);
            box-shadow: 0 -1px 3px rgba(0, 0, 0, 0.05);
            position: relative;   //cách dòng
        }
        h1{
            font-size: 42px;
            color: #007bff;
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



<div class="main text-center">
    <!-- Thanh tìm kiếm -->
     <h1> 📚Danh sách sách có trong thư viện </h1>
    <div class="mb-4">
        <form class="d-flex justify-content-center" method="GET" action="tim_kiem.php">
            <input type="text" name="tu_khoa" class="form-control w-50 me-2" placeholder="Tìm tên sách, tác giả, NXB hoặc thể loại">
            <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Tìm kiếm</button>
        </form>
    </div>

    <!-- Dòng 1: Tổng số sách -->
<div class="row justify-content-center mb-4">
    <div class="col-md-4">
        <a href="tong_so_sach.php" style="text-decoration: none; color: inherit; display: block;">
            <div class="card shadow-sm p-3 blink-hover">
                <h6 class="text-primary">TỔNG SỐ SÁCH</h6>
                <h4><?= $tong_sach ?></h4>
            </div>
        </a>
    </div>
</div>

<!-- Dòng 2: 3 thể loại đầu -->
<div class="row justify-content-center mb-4">
    <div class="col-md-3">
        <a href="sach_tin_hoc.php" style="text-decoration: none; color: inherit; display: block;">
            <div class="card shadow-sm p-3 blink-hover">
                <h6 class="text-success">TIN HỌC</h6>
                <h4><?= $dem_the_loai['Tin học'] ?></h4>
            </div>
        </a>
    </div>

    <div class="col-md-3">
        <a href="sach_ngon_ngu_hoc.php" style="text-decoration: none; color: inherit; display: block;">
            <div class="card shadow-sm p-3 blink-hover">
                <h6 class="text-success">NGÔN NGỮ HỌC</h6>
                <h4><?= $dem_the_loai['Ngôn ngữ học'] ?></h4>
            </div>
        </a>
    </div>

    <div class="col-md-3">
        <a href="sach_ke_toan.php" style="text-decoration: none; color: inherit; display: block;">
            <div class="card shadow-sm p-3 blink-hover">
                <h6 class="text-success">KẾ TOÁN</h6>
                <h4><?= $dem_the_loai['Kế toán'] ?></h4>
            </div>
        </a>
    </div>
</div>

<!-- Dòng 3: 3 thể loại tiếp theo -->
<div class="row justify-content-center mb-5">
    <div class="col-md-3">
        <a href="sach_van_hoc.php" style="text-decoration: none; color: inherit; display: block;">
            <div class="card shadow-sm p-3 blink-hover">
                <h6 class="text-success">VĂN HỌC</h6>
                <h4><?= $dem_the_loai['Văn học'] ?></h4>
            </div>
        </a>
    </div>

    <div class="col-md-3">
        <a href="sach_tam_ly_hoc.php" style="text-decoration: none; color: inherit; display: block;">
            <div class="card shadow-sm p-3 blink-hover">
                <h6 class="text-success">TÂM LÝ HỌC</h6>
                <h4><?= $dem_the_loai['Tâm lý học'] ?></h4>
            </div>
        </a>
    </div>

    <div class="col-md-3">
        <a href="sach_phap_luat.php" style="text-decoration: none; color: inherit; display: block;">
            <div class="card shadow-sm p-3 blink-hover">
                <h6 class="text-success">PHÁP LUẬT</h6>
                <h4><?= $dem_the_loai['Pháp luật'] ?></h4>
            </div>
        </a>
    </div>
</div>
</div>
<footer class="bg-light text-center text-muted py-3 mt-4 border-top" style="margin-left: 240px;">
    <strong>© 2025 Bản quyền thuộc về Nhóm 1 - DHMT16A1HN</strong>
</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
