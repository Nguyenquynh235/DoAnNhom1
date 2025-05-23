<?php
session_start();
include 'ket_noi.php';

// Đếm chỉ sách có thể mượn
$tong_sach = $conn->query("SELECT COUNT(*) FROM sach WHERE (trang_thai_sach = 'co_the_muon' OR trang_thai_sach IS NULL OR trang_thai_sach = '')")->fetch_row()[0];
$tong_ban_doc = $conn->query("SELECT COUNT(*) FROM ban_doc")->fetch_row()[0];
$sach_dang_muon = $conn->query("SELECT COUNT(*) FROM phieu_muon WHERE trang_thai = 'dang_muon'")->fetch_row()[0];
$phong_da_muon = $conn->query("SELECT COUNT(*) FROM phong WHERE trang_thai = 'da_muon'")->fetch_row()[0];

$hom_nay = date("d/m/Y");

// Chỉ hiển thị sách có thể mượn
$sach_moi = $conn->query("SELECT * FROM sach WHERE (trang_thai_sach = 'co_the_muon' OR trang_thai_sach IS NULL OR trang_thai_sach = '') ORDER BY ma_sach DESC LIMIT 8");

$the_loai = [
    "Văn hóa" => 12,
    "Tôn giáo" => 6,
    "Pháp luật" => 5,
    "Văn học" => 3,
    "Tâm lý học" => 2
];
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Trang chủ - Thư viện</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
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
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            overflow-y: auto;
            z-index: 1000;
        }
        .sidebar a,
        .sidebar .sidebar-section {
            display: block;
            padding: 12px 20px;
            color: white;
            text-decoration: none;
            font-weight: 500;
        }
        .sidebar a:hover,
        .sidebar .sidebar-section:hover {
            background-color: #0056b3;
        }
        .sidebar .sidebar-brand {
            text-align: center;
            padding: 20px 10px;
            border-bottom: 1px solid rgba(255,255,255,0.2);
        }
        .sidebar .sidebar-brand i {
            font-size: 48px;
        }
        .sidebar .sidebar-brand-text {
            font-size: 24px;
            font-weight: bold;
            line-height: 1.4;
            margin-top: 10px;
        }
        .sidebar .collapse a {
            padding-left: 40px;
            font-size: 14px;
            font-weight: normal;
        }
        header.navbar,
        nav.fixed-top {
            left: 240px;
            width: calc(100% - 240px);
            position: fixed;
            top: 0;
            z-index: 1001;
            background-color: white;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .main {
            margin-left: 240px;
            padding: 20px;
            padding-top: 80px;
            background-color: #f5f5f5;
        }
        .card-img-top {
            width: 100%;
            height: 260px;
            object-fit: cover;
            background-color: #eee;
        }
        .card.h-100 {
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .card-body {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-end;
            min-height: 120px;
        }
        .card-title {
            font-size: 14px;
            font-weight: 500;
            text-align: center;
            min-height: 40px;
            margin-bottom: 8px;
        }
        .the-loai-custom {
            background-color: rgba(0, 123, 255, 0.07);
            padding: 4px 6px;
            font-size: 13px;
            border-radius: 4px;
            margin-top: 6px;
            display: inline-block;
        }
    </style>
</head>
<body>
<nav class="navbar navbar-light bg-white shadow-sm px-4 py-2 d-flex justify-content-end align-items-center fixed-top">
    <div class="dropdown">
        <?php if (isset($_SESSION['ban_doc'])): ?>
            <a class="dropdown-toggle text-dark text-decoration-none" href="#" id="accountDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                <?php if (isset($_SESSION['ban_doc']['vai_tro']) && $_SESSION['ban_doc']['vai_tro'] === 'admin'): ?>
                    Xin chào, <strong>Admin</strong>
                <?php else: ?>
                    Xin chào, <strong><?= $_SESSION['ban_doc']['ten_dang_nhap'] ?></strong>
                <?php endif; ?>
            </a>
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="accountDropdown">
                
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
    <a href="trang_chu_admin.php" style="text-decoration: none;">
    <div class="sidebar-brand" style="cursor: pointer;">
        <i class="fas fa-book-reader text-white"></i>
        <div class="sidebar-brand-text">QUẢN LÝ<br> THƯ VIỆN</div>
    </div>
</a>

    <a href="trang_chu_admin.php"><i class="fas fa-home me-2"></i>Trang chủ</a>
    <a href="quan_ly_sach.php"><i class="fas fa-book me-2"></i>Quản lý sách</a>
    <a href="quan_ly_phong.php"><i class="fas fa-door-open me-2"></i>Quản lý phòng</a>
    <div class="sidebar-section" data-bs-toggle="collapse" href="#muontra" role="button" aria-expanded="false">
        <i class="fas fa-retweet me-2"></i>Quản lý mượn/trả
    </div>
    <div class="collapse" id="muontra">
        <a href="cho_muon.php"><i class="fas fa-arrow-right me-2"></i>Cho mượn sách</a>
        <a href="nhan_tra.php"><i class="fas fa-arrow-left me-2"></i>Nhận trả sách</a>
        <a href="ds_muon_tra.php"><i class="fas fa-list me-2"></i>Danh sách mượn/trả</a>
    </div>
    <div class="sidebar-section" data-bs-toggle="collapse" href="#thongke" role="button" aria-expanded="false">
        <i class="fas fa-chart-bar me-2"></i>Báo cáo thống kê
    </div>
    <div class="collapse" id="thongke">
        <a href="thong_ke_muon_nhieu.php"><i class="fas fa-chart-line me-2"></i>Sách mượn nhiều</a>
        <a href="ban_doc_tich_cuc.php"><i class="fas fa-user-check me-2"></i>Bạn đọc tích cực</a>
        <a href="sach_qua_han.php"><i class="fas fa-clock me-2"></i>Sách quá hạn mượn</a>
    </div>
    <div class="sidebar-section" data-bs-toggle="collapse" href="#hethong" role="button" aria-expanded="false">
        <i class="fas fa-cogs me-2"></i>Quản lý hệ thống
    </div>
    <div class="collapse" id="hethong">
        <a href="quan_ly_tai_khoan.php"><i class="fas fa-user-cog me-2"></i>Quản lý tài khoản</a>
        <a href="quan_ly_kho.php"><i class="fas fa-warehouse me-2"></i>Quản lý kho</a>
        <a href="quan_ly_the.php"><i class="fas fa-id-card me-2"></i>Quản lý thẻ</a>
        <a href="nha_cung_cap.php"><i class="fas fa-truck me-2"></i>Nhà cung cấp</a>
    </div>
    <a href="sua_gioi_thieu.php"><i class="fas fa-info-circle me-2"></i>Sửa giới thiệu</a>
    <a href="sua_lien_he.php"><i class="fas fa-envelope me-2"></i>Sửa liên hệ</a>
</div>

<div class="main">
    <div class="row">
        <div class="row text-center mb-4">
    <div class="col-md-3 mb-3">
        <a href="quan_ly_sach.php" style="text-decoration: none; color: inherit;">
        <div class="card shadow-sm py-4">
            <h5 class="text-primary">📚 Tổng số sách</h5>
            <h3 class="mt-2"><?= $tong_sach ?></h3>
        </div>
    </a>
    </div>
    <div class="col-md-3 mb-3">
        <a href="quan_ly_the.php" style="text-decoration: none; color: inherit;">
        <div class="card shadow-sm py-4">
            <h5 class="text-success">👥 Tổng bạn đọc</h5>
            <h3 class="mt-2"><?= $tong_ban_doc ?></h3>
        </div>
    </a>
    </div>
    <div class="col-md-3 mb-3">
        <a href="cho_muon.php" style="text-decoration: none; color: inherit;">
        <div class="card shadow-sm py-4">
            <h5 class="text-warning">📖 Sách đang mượn</h5>
            <h3 class="mt-2"><?= $sach_dang_muon ?></h3>
        </div>
    </a>
    </div>
    <div class="col-md-3 mb-3">
        <a href="quan_ly_phong.php" style="text-decoration: none; color: inherit;">
        <div class="card shadow-sm py-4">
            <h5 class="text-danger">🏠 Phòng đã đặt</h5>
            <h3 class="mt-2"><?= $phong_da_muon ?></h3>
        </div>
    </a>
    </div>
</div>


        <div class="col-md-8 mt-3">
            <div class="card p-3 mb-4 shadow-sm">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="mb-0">📚 Sách mới thêm</h4>
                    <a href="quan_ly_sach.php" class="btn btn-sm btn-primary">Xem tất cả</a>
                </div>
                <div class="row">
                    <?php if ($sach_moi->num_rows > 0): ?>
                        <?php while ($s = $sach_moi->fetch_assoc()) { ?>
                        <div class="col-md-3 mb-3">
                            <div class="card h-100 text-center">
                                <img src="images/<?= $s['anh'] ?>" class="card-img-top" alt="<?= $s['ten_sach'] ?>">
                                <div class="card-body">
                                    <h6 class="card-title"><?= $s['ten_sach'] ?></h6>
                                    <span class="the-loai-custom"><?= $s['the_loai'] ?? 'Chưa rõ' ?></span>
                                    <a href="chi_tiet_sach.php?id=<?= $s['ma_sach'] ?>" class="btn btn-primary btn-sm w-100 mb-2 mt-2">Chi tiết</a>
                                </div>
                            </div>
                        </div>
                        <?php } ?>
                    <?php else: ?>
                        <div class="col-12 text-center py-4">
                            <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Chưa có sách nào được xuất kho</h5>
                            <p class="text-muted">Hãy vào <a href="quan_ly_kho.php">Quản lý kho</a> để xuất sách ra trang chủ</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="col-md-4 mt-4">
            <div class="card shadow-sm p-3 text-center mb-4">
                <h6 class="text-warning">HÔM NAY</h6>
                <h4><?= $hom_nay ?></h4>
            </div>
            
            <?php
        $so_tb_moi = $_SESSION['so_thong_bao'] ?? 0;
        ?>

        <div class="card p-3">
        <h5 class="mb-3 text-primary position-relative">
        <i class="fas fa-bell text-warning" style="font-size: 22px; position: relative;">
            <?php if ($so_tb_moi > 0): ?>
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                    <?= $so_tb_moi ?>
                </span>
             <?php endif; ?>
                </i> Thông báo
            </h5>

            <?php if ($so_tb_moi > 0): ?>
            <div class="alert alert-warning">
            🔔 Bạn có <strong><?= $so_tb_moi ?></strong> thông báo mới!
            </div>
             <?php else: ?>
            <div class="alert alert-info">📢 Chào mừng Admin đến với hệ thống Quản lý Thư viện!</div>
             <?php endif; ?>
            </div>
        </div>
     
    </div>
</div>
<?php include 'footer1.php'; ?>
</body>
</html>