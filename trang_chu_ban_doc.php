<?php
session_start();
include 'ket_noi.php';

// Chỉ đếm sách có thể mượn
$sach = $conn->query("SELECT COUNT(*) FROM sach WHERE (trang_thai_sach = 'co_the_muon' OR trang_thai_sach IS NULL OR trang_thai_sach = '')")->fetch_row()[0];
$noi_quy_thu_vien = '&nbsp;'; // hoặc chuỗi trắng đủ dài
$dat_phong = '&nbsp;';
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
    <title>Trang chủ bạn đọc- Thư viện</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet"> 

    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <style>
        body { margin: 0; font-family: Arial; }

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

        header {
    position: fixed;
    top: 0;
    left: 240px; 
    right: 0;
    height: 60px;
    background-color: white;
    z-index: 1000;
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
            object-position: top;
            background-color: #eee;
        }

        .card.h-100 {
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .card-body .badge {
            display: inline-block;
            width: 100px;
            text-align: center;
            margin-top: 5px;
            font-size: 13px;
        }

        .card-body .btn {
            width: 150px;
            font-size: 13px;
            padding: 6px 0;
            margin-top: 6px;
        }

        .the-loai-custom {
            background-color: rgba(0, 123, 255, 0.07);
            color: #000;
            display: inline-block;
            width: 100px;
            text-align: center;
            padding: 4px 6px;
            font-size: 13px;
            border-radius: 4px;
            margin-top: 6px;
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

        .list-group-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 14px;
            padding: 8px 12px;
        }

        .blink-hover:hover {
    animation: blink-border 0.6s linear infinite alternate;
    box-shadow: 0 0 0 2px #000; 
}

@keyframes blink-border {
    0%   { box-shadow: 0 0 0 2px #000; }
    100% { box-shadow: 0 0 10px 2px #000; }
}

.blink-hover:hover {
    animation: blink-border 0.6s linear infinite alternate !important;
    box-shadow: 0 0 0 2px #000 !important;
}
nav.fixed-top {
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
                <li><a class="dropdown-item" href="dang_xuat.php">🔓 Đăng xuất</a></li>
            </ul>
        <?php endif; ?>
    </div>
</nav>




<div class="sidebar">
    <!-- Logo và tiêu đề có thể click theo điều kiện -->
    <a href="<?php echo isset($_SESSION['ban_doc']) ? 'trang_chu.php' : 'thong_tin_ca_nhan.php'; ?>" 
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
    <div class="row text-center mb-4">
        <div class="col-md-3">
    <a href="sach.php" style="text-decoration: none;">
        <div class="card shadow-sm p-3 blink-hover">
            <h6 class="text-primary">SÁCH</h6>
            <h4><?= $sach ?></h4>
        </div>
    </a>
</div>

        <div class="col-md-3">
    <a href="noi_quy_thu_vien.php" style="text-decoration: none;">
        <div class="card shadow-sm p-3 blink-hover">
            <h6 class="text-success">NỘI QUY THƯ VIỆN</h6>
            <h4 style="height: 24px;">&nbsp;</h4> <!-- để giữ nguyên chiều cao -->
        </div>
    </a>
</div>
        <div class="col-md-3">
        <a href="dat_phong.php" style="text-decoration: none;">
            <div class="card shadow-sm p-3 blink-hover">
                <h6 class="text-info">ĐẶT PHÒNG</h6>
                <h4><?= $dat_phong ?></h4>
            </div>
        </a>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm p-3 blink-hover">
                <h6 class="text-warning">HÔM NAY</h6>
                <h4><?= $hom_nay ?></h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card p-3 mb-4 shadow-sm">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="mb-0">📚 Sách mới</h4>
                    <a href="tong_so_sach.php" class="btn btn-sm btn-primary blink-hover">Xem tất cả</a>
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
                                    <a href="chi_tiet_sach.php?id=<?= $s['ma_sach'] ?>" class="btn btn-primary btn-sm w-100 mb-2">Chi tiết</a>
                                </div>
                            </div>
                        </div>
                        <?php } ?>
                    <?php else: ?>
                        <div class="col-12 text-center py-4">
                            <i class="fas fa-book-open fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Chưa có sách nào có sẵn</h5>
                            <p class="text-muted">Vui lòng quay lại sau để xem sách mới</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card mb-4 p-3 blink-hover">
                <h5 class="mb-3 text-primary">Thể loại phổ biến</h5>
                <ul class="list-group mb-0">
                    <?php foreach ($the_loai as $ten => $sl) { ?>
                        <li class="list-group-item">
                            <?= $ten ?>
                            <span class="badge bg-primary rounded-pill"><?= $sl ?></span>
                        </li>
                    <?php } ?>
                </ul>
            </div>
            <div class="card p-3 blink-hover">
                <h5 class="mb-3 text-primary">Thông báo</h5>
                <div class="alert alert-info">📢 Chào mừng đến với hệ thống Quản lý Thư viện!</div>
                <div class="alert alert-warning">⚠️ Vui lòng trả sách đúng hạn để tránh phí phạt.</div>
                <div class="alert alert-success">✅ Thư viện vừa cập nhật nhiều đầu sách mới!</div>
            </div>
        </div>
    </div>
</div>
<?php include 'footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<?php include 'bong_chat.php'; ?>

</body>
</html>