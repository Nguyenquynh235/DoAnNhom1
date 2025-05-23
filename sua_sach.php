<?php
session_start();
include 'ket_noi.php';

$id = $_GET['id'] ?? 0;
$thong_bao = "";

// Lấy thông tin sách cũ
$stmt = $conn->prepare("SELECT * FROM sach WHERE ma_sach = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$sach = $result->fetch_assoc();

if (!$sach) {
    echo "<script>alert('Không tìm thấy sách'); window.location.href='quan_ly_sach.php';</script>";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ten_sach = $_POST['ten_sach'];
    $tac_gia = $_POST['tac_gia'];
    $the_loai = $_POST['the_loai'];
    $nha_xuat_ban = $_POST['nha_xuat_ban'];

    if (!empty($_FILES['anh']['name'])) {
        $anh = $_FILES['anh']['name'];
        $duong_dan = 'images/' . basename($anh);
        move_uploaded_file($_FILES['anh']['tmp_name'], $duong_dan);
    } else {
        $anh = $sach['anh'];
    }

    $stmt_update = $conn->prepare("UPDATE sach SET ten_sach=?, tac_gia=?, the_loai=?, nha_xuat_ban=?, anh=? WHERE ma_sach=?");
    $stmt_update->bind_param("sssssi", $ten_sach, $tac_gia, $the_loai, $nha_xuat_ban, $anh, $id);
    $stmt_update->execute();

    header("Location: quan_ly_sach.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Sửa sách</title>
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
<nav class="navbar navbar-light bg-white fixed-top px-4 py-2 d-flex justify-content-between">
    <span class="fw-bold">Chỉnh Sửa Sách</span>
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
    </div>
    <a href="gioi_thieu.php"><i class="fas fa-info-circle me-2"></i>Giới thiệu</a>
    <a href="lien_he.php"><i class="fas fa-envelope me-2"></i>Liên hệ</a>
</div>
<div class="main">
    <div class="container">
        <form method="post" enctype="multipart/form-data" class="bg-white p-4 rounded shadow-sm">
            <div class="mb-3">
                <label class="form-label">Tên sách</label>
                <input type="text" name="ten_sach" class="form-control" value="<?= $sach['ten_sach'] ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Tác giả</label>
                <input type="text" name="tac_gia" class="form-control" value="<?= $sach['tac_gia'] ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Thể loại</label>
                <input type="text" name="the_loai" class="form-control" value="<?= $sach['the_loai'] ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Nhà xuất bản</label>
                <input type="text" name="nha_xuat_ban" class="form-control" value="<?= $sach['nha_xuat_ban'] ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Hình ảnh hiện tại:</label><br>
                <img src="images/<?= $sach['anh'] ?>" width="100">
            </div>
            <div class="mb-3">
                <label class="form-label">Chọn ảnh mới (nếu có):</label>
                <input type="file" name="anh" class="form-control" accept="image/*">
            </div>
            <button type="submit" class="btn btn-primary">Cập nhật</button>
            <a href="quan_ly_sach.php" class="btn btn-secondary">Hủy</a>
        </form>
    </div>
</div>
<?php include 'footer1.php'; ?>
</body>
</html>