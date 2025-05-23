<?php
session_start();
include 'ket_noi.php';

// Kiểm tra quyền admin
if (!isset($_SESSION['ban_doc']) || $_SESSION['ban_doc']['vai_tro'] !== 'admin') {
    header('Location: dang_nhap.php');
    exit;
}

$ma_kho = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($ma_kho <= 0) {
    header('Location: quan_ly_kho.php');
    exit;
}

// Lấy thông tin kho
$sql_kho = "SELECT * FROM kho WHERE ma_kho = ?";
$stmt = $conn->prepare($sql_kho);
$stmt->bind_param("i", $ma_kho);
$stmt->execute();
$kho = $stmt->get_result()->fetch_assoc();

if (!$kho) {
    header('Location: quan_ly_kho.php');
    exit;
}

// Lấy danh sách sách trong kho
$sql_sach = "SELECT s.*, ncc.ten_ncc 
             FROM sach s 
             LEFT JOIN nha_cung_cap ncc ON s.ma_ncc = ncc.ma_ncc 
             WHERE s.ma_kho = ? AND s.trang_thai_sach = 'trong_kho'
             ORDER BY s.ngay_nhap DESC";
$stmt = $conn->prepare($sql_sach);
$stmt->bind_param("i", $ma_kho);
$stmt->execute();
$ds_sach = $stmt->get_result();

// Thống kê kho
$sql_stats = "SELECT 
    COUNT(*) as so_loai_sach,
    SUM(so_luong) as tong_so_luong,
    SUM(gia_nhap * so_luong) as tong_gia_tri
    FROM sach 
    WHERE ma_kho = ? AND trang_thai_sach = 'trong_kho'";
$stmt = $conn->prepare($sql_stats);
$stmt->bind_param("i", $ma_kho);
$stmt->execute();
$stats = $stmt->get_result()->fetch_assoc();

// Xử lý thông báo
$success = isset($_GET['success']) && $_GET['success'] == 1;
$error = isset($_GET['error']) && $_GET['error'] == 1;
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Chi tiết <?= htmlspecialchars($kho['ten_kho']) ?></title>
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
  </style>
</head>
<body>

<nav class="navbar navbar-light bg-white shadow-sm fixed-top px-4 py-2 d-flex justify-content-between">
  <span class="fw-bold">📦 CHI TIẾT KHO: <?= htmlspecialchars($kho['ten_kho']) ?></span>
  <a href="quan_ly_kho.php" class="btn btn-secondary btn-sm">
    <i class="fas fa-arrow-left"></i> Quay lại
  </a>
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
    <div class="collapse show" id="hethong">
        <a href="quan_ly_tai_khoan.php"><i class="fas fa-user-cog me-2"></i>Quản lý tài khoản</a>
        <a href="quan_ly_kho.php" style="background-color: #0056b3;"><i class="fas fa-warehouse me-2"></i>Quản lý kho</a>
        <a href="quan_ly_the.php"><i class="fas fa-id-card me-2"></i>Quản lý thẻ</a>
        <a href="nha_cung_cap.php"><i class="fas fa-truck me-2"></i>Nhà cung cấp</a>
    </div>
    <a href="gioi_thieu.php"><i class="fas fa-info-circle me-2"></i>Giới thiệu</a>
    <a href="lien_he.php"><i class="fas fa-envelope me-2"></i>Liên hệ</a>
</div>

<div class="main">
  <div class="container-fluid">
    
    <!-- Thông báo -->
    <?php if ($success): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle"></i> Đã chuyển sách ra khỏi kho thành công!
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php elseif ($error): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="fas fa-exclamation-triangle"></i> Có lỗi xảy ra khi chuyển sách!
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    
    <!-- Thông tin kho -->
    <div class="stats-card">
        <div class="row">
            <div class="col-md-8">
                <h5 class="text-primary mb-3">
                    <i class="fas fa-warehouse"></i> <?= htmlspecialchars($kho['ten_kho']) ?>
                </h5>
                <p class="mb-2">
                    <i class="fas fa-map-marker-alt text-muted"></i> 
                    <strong>Vị trí:</strong> <?= htmlspecialchars($kho['vi_tri']) ?>
                </p>
                <p class="mb-2">
                    <i class="fas fa-info-circle text-muted"></i> 
                    <strong>Mô tả:</strong> <?= htmlspecialchars($kho['mo_ta']) ?>
                </p>
                <p class="mb-0">
                    <i class="fas fa-calendar text-muted"></i> 
                    <strong>Ngày tạo:</strong> <?= date('d/m/Y H:i', strtotime($kho['ngay_tao'])) ?>
                </p>
            </div>
            <div class="col-md-4">
                <div class="text-center">
                    <span class="badge bg-<?= $kho['trang_thai'] == 'hoat_dong' ? 'success' : ($kho['trang_thai'] == 'bao_tri' ? 'warning' : 'danger') ?> fs-6">
                        <?php
                        switch($kho['trang_thai']) {
                            case 'hoat_dong': echo 'Hoạt động'; break;
                            case 'bao_tri': echo 'Bảo trì'; break;
                            case 'ngung_hoat_dong': echo 'Ngừng hoạt động'; break;
                        }
                        ?>
                    </span>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Thống kê sách trong kho -->
    <div class="stats-card">
        <div class="row text-center">
            <div class="col-md-3">
                <h4 class="text-primary"><?= number_format($stats['so_loai_sach']) ?></h4>
                <p class="mb-0">Loại sách</p>
            </div>
            <div class="col-md-3">
                <h4 class="text-success"><?= number_format($stats['tong_so_luong']) ?></h4>
                <p class="mb-0">Tổng số lượng</p>
            </div>
            <div class="col-md-3">
                <h4 class="text-info"><?= number_format($kho['suc_chua']) ?></h4>
                <p class="mb-0">Sức chứa tối đa</p>
            </div>
            <div class="col-md-3">
                <h4 class="text-warning"><?= number_format($stats['tong_gia_tri']) ?> ₫</h4>
                <p class="mb-0">Tổng giá trị</p>
            </div>
        </div>
        
        <?php if ($stats['tong_so_luong'] > 0): ?>
        <div class="mt-3">
            <div class="progress" style="height: 10px;">
                <?php $ty_le = round(($stats['tong_so_luong'] / $kho['suc_chua']) * 100, 1); ?>
                <div class="progress-bar" role="progressbar" style="width: <?= $ty_le ?>%" aria-valuenow="<?= $ty_le ?>" aria-valuemin="0" aria-valuemax="100">
                    <?= $ty_le ?>%
                </div>
            </div>
            <small class="text-muted">Tỷ lệ sử dụng kho</small>
        </div>
        <?php endif; ?>
    </div>

    <!-- Danh sách sách trong kho -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="fas fa-list"></i> Danh sách sách trong kho
                <?php if ($stats['so_loai_sach'] > 0): ?>
                    <span class="badge bg-primary"><?= $stats['so_loai_sach'] ?> loại</span>
                <?php endif; ?>
            </h5>
        </div>
        <div class="card-body">
            <?php if ($ds_sach->num_rows > 0): ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Mã sách</th>
                                <th>Tên sách</th>
                                <th>Tác giả</th>
                                <th>Thể loại</th>
                                <th>Số lượng</th>
                                <th>Giá nhập</th>
                                <th>Nhà cung cấp</th>
                                <th>Ngày nhập</th>
                                <th>Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($sach = $ds_sach->fetch_assoc()): ?>
                            <tr>
                                <td><?= $sach['ma_sach'] ?></td>
                                <td>
                                    <strong><?= htmlspecialchars($sach['ten_sach']) ?></strong>
                                </td>
                                <td><?= htmlspecialchars($sach['tac_gia'] ?? 'Chưa rõ') ?></td>
                                <td>
                                    <span class="badge bg-light text-dark"><?= htmlspecialchars($sach['the_loai'] ?? 'Chưa phân loại') ?></span>
                                </td>
                                <td>
                                    <span class="badge bg-primary"><?= number_format($sach['so_luong']) ?> cuốn</span>
                                </td>
                                <td><?= number_format($sach['gia_nhap']) ?> ₫</td>
                                <td><?= htmlspecialchars($sach['ten_ncc'] ?? 'Không rõ') ?></td>
                                <td><?= date('d/m/Y H:i', strtotime($sach['ngay_nhap'])) ?></td>
                                <td>
                                    <button class="btn btn-sm btn-success" onclick="chuyenTrangThai(<?= $sach['ma_sach'] ?>)">
                                        <i class="fas fa-check"></i> Xuất kho
                                    </button>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">Kho hiện tại không có sách nào</h5>
                    <p class="text-muted">Sách sẽ xuất hiện ở đây khi được thêm từ nhà cung cấp</p>
                    <a href="them_sach_tu_ncc.php" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Thêm sách từ NCC
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
  </div>
</div>

<script>
function chuyenTrangThai(ma_sach) {
    if (confirm('Chuyển sách này từ "Trong kho" sang "Có thể mượn"?\nSách sẽ xuất hiện ở trang chủ cho bạn đọc mượn.')) {
        // Tạo form để submit
        var form = document.createElement('form');
        form.method = 'POST';
        form.action = 'xu_ly_chuyen_trang_thai.php';
        form.style.display = 'none';
        
        var input1 = document.createElement('input');
        input1.type = 'hidden';
        input1.name = 'ma_sach';
        input1.value = ma_sach;
        form.appendChild(input1);
        
        var input2 = document.createElement('input');
        input2.type = 'hidden';
        input2.name = 'ma_kho';
        input2.value = <?= $ma_kho ?>;
        form.appendChild(input2);
        
        document.body.appendChild(form);
        form.submit();
    }
}
</script>

</body>
</html>