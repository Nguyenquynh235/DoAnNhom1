<?php
session_start();
include 'ket_noi.php';

$ds_muon = $conn->query("SELECT pm.*, bd.ho_ten FROM phieu_muon pm JOIN ban_doc bd ON pm.ma_ban_doc = bd.ma_ban_doc WHERE pm.trang_thai = 'dang_muon'");
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Nhận trả sách</title>
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

    </div>
    <a href="sua_gioi_thieu.php"><i class="fas fa-info-circle me-2"></i>Sửa giới thiệu</a>
    <a href="sua_lien_he.php"><i class="fas fa-envelope me-2"></i>Sửa liên hệ</a>
</div>
<div class="main">
  <div class="container-fluid">
    <h4 class="mb-4">QUẢN LÝ TRẢ SÁCH</h4>
    <table class="table table-bordered table-hover bg-white">
      <thead class="table-light">
        <tr>
          <th>Mã Phiếu</th><th>Họ tên</th><th>Ngày mượn</th><th>Ngày trả</th><th>Thao tác</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($pm = $ds_muon->fetch_assoc()) { ?>
          <tr>
            <td><?= $pm['ma_phieu'] ?></td>
            <td><?= $pm['ho_ten'] ?></td>
            <td><?= $pm['ngay_muon'] ?></td>
            <td><?= $pm['ngay_tra'] ?></td>
            <td><a href="tra_sach.php?id=<?= $pm['ma_phieu'] ?>" class="btn btn-danger btn-sm">Xác nhận trả</a></td>
          </tr>
        <?php } ?>
      </tbody>
    </table>
  </div>
</div>
<?php include 'footer1.php'; ?>
</body>
</html>
