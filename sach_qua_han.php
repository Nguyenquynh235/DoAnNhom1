<?php
session_start();
include 'ket_noi.php';

// Query chi tiết hơn để lấy thông tin sách và số ngày quá hạn
$sql = "SELECT pm.ma_phieu, bd.ho_ten, bd.ten_dang_nhap, bd.email, bd.so_dien_thoai,
               pm.ngay_muon, pm.ngay_tra,
               DATEDIFF(CURDATE(), pm.ngay_tra) as so_ngay_qua_han
        FROM phieu_muon pm
        JOIN ban_doc bd ON pm.ma_ban_doc = bd.ma_ban_doc
        WHERE pm.trang_thai = 'dang_muon' AND pm.ngay_tra < CURDATE()
        ORDER BY pm.ngay_tra ASC";

$ds_qua_han = $conn->query($sql);

// Thống kê tổng quan
$sql_stats = "SELECT 
    COUNT(*) as tong_phieu_qua_han,
    AVG(DATEDIFF(CURDATE(), ngay_tra)) as trung_binh_ngay_qua_han,
    MAX(DATEDIFF(CURDATE(), ngay_tra)) as max_ngay_qua_han,
    MIN(DATEDIFF(CURDATE(), ngay_tra)) as min_ngay_qua_han
    FROM phieu_muon 
    WHERE trang_thai = 'dang_muon' AND ngay_tra < CURDATE()";

$result_stats = $conn->query($sql_stats);
$stats = $result_stats->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Sách quá hạn</title>
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
    .overdue-badge {
        font-size: 12px;
        padding: 4px 8px;
        border-radius: 4px;
    }
    .overdue-low { background-color: #fff3cd; color: #856404; } /* 1-7 ngày */
    .overdue-medium { background-color: #f8d7da; color: #721c24; } /* 8-30 ngày */
    .overdue-high { background-color: #d1ecf1; color: #0c5460; } /* >30 ngày */
    .urgent-row {
        background-color: #ffebee !important;
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
    <?php if ($stats['tong_phieu_qua_han'] > 0): ?>
    <div class="stats-card">
        <div class="row text-center">
            <div class="col-md-3">
                <h5 class="text-danger"><?= $stats['tong_phieu_qua_han'] ?></h5>
                <p class="mb-0">Phiếu quá hạn</p>
            </div>
            <div class="col-md-3">
                <h5 class="text-warning"><?= number_format($stats['trung_binh_ngay_qua_han'], 1) ?></h5>
                <p class="mb-0">Trung bình ngày quá hạn</p>
            </div>
            <div class="col-md-3">
                <h5 class="text-info"><?= $stats['min_ngay_qua_han'] ?></h5>
                <p class="mb-0">Ít nhất (ngày)</p>
            </div>
            <div class="col-md-3">
                <h5 class="text-primary"><?= $stats['max_ngay_qua_han'] ?></h5>
                <p class="mb-0">Nhiều nhất (ngày)</p>
            </div>
        </div>
        
        <?php if ($stats['max_ngay_qua_han'] > 30): ?>
        <div class="alert alert-danger mt-3 mb-0">
            <i class="fas fa-exclamation-triangle"></i> 
            <strong>Cảnh báo:</strong> Có phiếu mượn quá hạn hơn 30 ngày! Cần xử lý gấp.
        </div>
        <?php elseif ($stats['max_ngay_qua_han'] > 7): ?>
        <div class="alert alert-warning mt-3 mb-0">
            <i class="fas fa-info-circle"></i> 
            <strong>Lưu ý:</strong> Có phiếu mượn quá hạn hơn 1 tuần.
        </div>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <h4 class="mb-4 text-danger">⚠️ DANH SÁCH SÁCH QUÁ HẠN TRẢ</h4>
    
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-danger">
                <tr>
                    <th width="8%">STT</th>
                    <th width="15%">Mã phiếu</th>
                    <th width="20%">Người mượn</th>
                    <th width="20%">Thông tin liên hệ</th>
                    <th width="12%">Ngày mượn</th>
                    <th width="12%">Hạn trả</th>
                    <th width="13%">Quá hạn</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($ds_qua_han && $ds_qua_han->num_rows > 0): ?>
                    <?php $i = 1; while ($row = $ds_qua_han->fetch_assoc()): ?>
                        <?php
                        // Xác định mức độ nghiêm trọng
                        $overdue_class = '';
                        $row_class = '';
                        $icon = 'fas fa-clock';
                        
                        if ($row['so_ngay_qua_han'] <= 7) {
                            $overdue_class = 'overdue-low';
                        } elseif ($row['so_ngay_qua_han'] <= 30) {
                            $overdue_class = 'overdue-medium';
                        } else {
                            $overdue_class = 'overdue-high';
                            $row_class = 'urgent-row';
                            $icon = 'fas fa-exclamation-triangle';
                        }
                        ?>
                        <tr class="<?= $row_class ?>">
                            <td><?= $i++ ?></td>
                            <td>
                                <strong>#<?= $row['ma_phieu'] ?></strong>
                            </td>
                            <td>
                                <strong><?= htmlspecialchars($row['ho_ten']) ?></strong>
                                <br><small class="text-muted"><?= htmlspecialchars($row['ten_dang_nhap']) ?></small>
                            </td>
                            <td>
                                <?php if (!empty($row['email'])): ?>
                                    <small class="text-muted d-block">
                                        <i class="fas fa-envelope me-1"></i>
                                        <a href="mailto:<?= htmlspecialchars($row['email']) ?>"><?= htmlspecialchars($row['email']) ?></a>
                                    </small>
                                <?php endif; ?>
                                <?php if (!empty($row['so_dien_thoai'])): ?>
                                    <small class="text-muted d-block">
                                        <i class="fas fa-phone me-1"></i>
                                        <a href="tel:<?= htmlspecialchars($row['so_dien_thoai']) ?>"><?= htmlspecialchars($row['so_dien_thoai']) ?></a>
                                    </small>
                                <?php endif; ?>
                                <?php if (empty($row['email']) && empty($row['so_dien_thoai'])): ?>
                                    <small class="text-muted">Chưa có thông tin</small>
                                <?php endif; ?>
                            </td>
                            <td>
                                <small><?= date('d/m/Y', strtotime($row['ngay_muon'])) ?></small>
                            </td>
                            <td>
                                <small><?= date('d/m/Y', strtotime($row['ngay_tra'])) ?></small>
                            </td>
                            <td>
                                <span class="overdue-badge <?= $overdue_class ?>">
                                    <i class="<?= $icon ?> me-1"></i>
                                    <?= $row['so_ngay_qua_han'] ?> ngày
                                </span>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="text-center py-5">
                            <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                            <p class="text-success mb-0"><strong>Tuyệt vời! Hiện tại không có sách nào quá hạn.</strong></p>
                            <small class="text-muted">Tất cả bạn đọc đều trả sách đúng hạn.</small>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Hướng dẫn xử lý -->
    <div class="card mt-4">
        <div class="card-header bg-warning text-dark">
            <h5 class="mb-0"><i class="fas fa-exclamation-circle"></i> Hướng dẫn xử lý sách quá hạn</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h6>Mức độ ưu tiên xử lý:</h6>
                    <ul class="mb-0">
                        <li><span class="overdue-badge overdue-low"><i class="fas fa-clock"></i> 1-7 ngày</span> - Nhắc nhở qua email/điện thoại</li>
                        <li><span class="overdue-badge overdue-medium"><i class="fas fa-clock"></i> 8-30 ngày</span> - Cảnh báo và áp dụng phí phạt</li>
                        <li><span class="overdue-badge overdue-high"><i class="fas fa-exclamation-triangle"></i> >30 ngày</span> - Xử lý nghiêm khắc, báo cáo</li>
                    </ul>
                </div>
                <div class="col-md-6">
                    <h6>Các bước xử lý:</h6>
                    <ol class="mb-0">
                        <li>Liên hệ bạn đọc qua email/điện thoại</li>
                        <li>Nhắc nhở lịch trả sách</li>
                        <li>Áp dụng phí phạt theo quy định</li>
                        <li>Cập nhật trạng thái khi nhận được sách</li>
                    </ol>
                </div>
            </div>
            
            <?php if ($ds_qua_han && $ds_qua_han->num_rows > 0): ?>
            <hr>
            <div class="alert alert-info mb-0">
                <i class="fas fa-lightbulb"></i>
                <strong>Mẹo:</strong> Click vào email/số điện thoại để liên hệ trực tiếp với bạn đọc. 
                Ưu tiên xử lý các trường hợp quá hạn lâu nhất trước.
            </div>
            <?php endif; ?>
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