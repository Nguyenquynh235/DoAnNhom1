<?php
session_start();
include 'ket_noi.php';

// Query đã sửa để sử dụng đúng enum value 'da_tra' và thêm thông tin chi tiết
$sql = "SELECT bd.ho_ten, bd.ten_dang_nhap, bd.email, bd.so_dien_thoai, 
               COUNT(*) AS so_luot_muon,
               MAX(pm.ngay_muon) as lan_muon_gan_nhat
        FROM phieu_muon pm
        JOIN ban_doc bd ON pm.ma_ban_doc = bd.ma_ban_doc
        WHERE pm.trang_thai = 'da_tra'
        GROUP BY pm.ma_ban_doc, bd.ho_ten, bd.ten_dang_nhap, bd.email, bd.so_dien_thoai
        ORDER BY so_luot_muon DESC
        LIMIT 10";

$ds_tich_cuc = $conn->query($sql);

// Thống kê tổng quan
$sql_stats = "SELECT 
    COUNT(DISTINCT pm.ma_ban_doc) as tong_ban_doc_co_muon,
    COUNT(*) as tong_luot_muon,
    AVG(luot_muon_moi_nguoi.so_luot) as trung_binh_luot_muon
    FROM (
        SELECT ma_ban_doc, COUNT(*) as so_luot
        FROM phieu_muon 
        WHERE trang_thai = 'da_tra'
        GROUP BY ma_ban_doc
    ) luot_muon_moi_nguoi
    JOIN phieu_muon pm ON pm.ma_ban_doc = luot_muon_moi_nguoi.ma_ban_doc";

$result_stats = $conn->query($sql_stats);
$stats = $result_stats->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Bạn đọc tích cực</title>
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
    .rank-icon {
        font-size: 20px;
        margin-right: 8px;
    }
    .rank-1 { color: #ffd700; } /* Vàng */
    .rank-2 { color: #c0c0c0; } /* Bạc */
    .rank-3 { color: #cd7f32; } /* Đồng */
    .stats-card {
        background: white;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 20px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    .table-responsive {
        background: white;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
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
    
    <!-- Thống kê tổng quan -->
    <div class="stats-card">
        <div class="row text-center">
            <div class="col-md-4">
                <h5 class="text-primary"><?= $stats['tong_ban_doc_co_muon'] ?? 0 ?></h5>
                <p class="mb-0">Bạn đọc đã từng mượn</p>
            </div>
            <div class="col-md-4">
                <h5 class="text-success"><?= $stats['tong_luot_muon'] ?? 0 ?></h5>
                <p class="mb-0">Tổng lượt mượn</p>
            </div>
            <div class="col-md-4">
                <h5 class="text-info"><?= number_format($stats['trung_binh_luot_muon'] ?? 0, 1) ?></h5>
                <p class="mb-0">Trung bình lượt mượn/người</p>
            </div>
        </div>
    </div>

    <h4 class="mb-4 text-primary">🏆 TOP 10 BẠN ĐỌC TÍCH CỰC NHẤT</h4>
    
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-primary">
                <tr>
                    <th width="8%">Hạng</th>
                    <th width="25%">Họ tên</th>
                    <th width="15%">Tên đăng nhập</th>
                    <th width="20%">Thông tin liên hệ</th>
                    <th width="12%">Số lượt mượn</th>
                    <th width="20%">Lần mượn gần nhất</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($ds_tich_cuc && $ds_tich_cuc->num_rows > 0): ?>
                    <?php $i = 1; while ($bd = $ds_tich_cuc->fetch_assoc()): ?>
                        <tr>
                            <td>
                                <?php
                                $rank_class = '';
                                $rank_icon = 'fas fa-medal';
                                switch($i) {
                                    case 1:
                                        $rank_class = 'rank-1';
                                        $rank_icon = 'fas fa-crown';
                                        break;
                                    case 2:
                                        $rank_class = 'rank-2';
                                        break;
                                    case 3:
                                        $rank_class = 'rank-3';
                                        break;
                                    default:
                                        $rank_class = 'text-muted';
                                        $rank_icon = 'fas fa-trophy';
                                }
                                ?>
                                <i class="<?= $rank_icon ?> rank-icon <?= $rank_class ?>"></i>
                                <strong class="<?= $rank_class ?>">#<?= $i ?></strong>
                            </td>
                            <td>
                                <strong><?= htmlspecialchars($bd['ho_ten']) ?></strong>
                            </td>
                            <td>
                                <code><?= htmlspecialchars($bd['ten_dang_nhap']) ?></code>
                            </td>
                            <td>
                                <?php if (!empty($bd['email'])): ?>
                                    <small class="text-muted d-block">
                                        <i class="fas fa-envelope me-1"></i>
                                        <?= htmlspecialchars($bd['email']) ?>
                                    </small>
                                <?php endif; ?>
                                <?php if (!empty($bd['so_dien_thoai'])): ?>
                                    <small class="text-muted d-block">
                                        <i class="fas fa-phone me-1"></i>
                                        <?= htmlspecialchars($bd['so_dien_thoai']) ?>
                                    </small>
                                <?php endif; ?>
                                <?php if (empty($bd['email']) && empty($bd['so_dien_thoai'])): ?>
                                    <small class="text-muted">Chưa cập nhật</small>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="badge bg-primary fs-6">
                                    <?= $bd['so_luot_muon'] ?> lượt
                                </span>
                            </td>
                            <td>
                                <small class="text-muted">
                                    <?= !empty($bd['lan_muon_gan_nhat']) ? date('d/m/Y', strtotime($bd['lan_muon_gan_nhat'])) : 'Không có' ?>
                                </small>
                            </td>
                        </tr>
                        <?php $i++; ?>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="text-center py-5">
                            <i class="fas fa-user-slash fa-3x text-muted mb-3"></i>
                            <p class="text-muted mb-0">Chưa có bạn đọc nào hoàn thành việc mượn trả sách</p>
                            <small class="text-muted">Hãy khuyến khích bạn đọc tham gia mượn sách!</small>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Ghi chú hướng dẫn -->
    <div class="card mt-4">
        <div class="card-header bg-info text-white">
            <h5 class="mb-0"><i class="fas fa-info-circle"></i> Ghi chú</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h6>Tiêu chí xếp hạng:</h6>
                    <ul class="mb-0">
                        <li>Chỉ tính các phiếu mượn đã <strong>hoàn thành trả sách</strong></li>
                        <li>Sắp xếp theo số lượt mượn giảm dần</li>
                        <li>Hiển thị top 10 bạn đọc tích cực nhất</li>
                        <li>Cập nhật theo thời gian thực</li>
                    </ul>
                </div>
                <div class="col-md-6">
                     
                    </ul>
                </div>
            </div>
        </div>
    </div>

  </div>
</div>

<?php include 'footer1.php'; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto expand thống kê menu
    const thongKeMenu = new bootstrap.Collapse(document.getElementById('thongke'), {
        toggle: false
    });
    thongKeMenu.show();
});
</script>
</body>
</html>