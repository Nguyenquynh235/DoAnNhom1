<?php
session_start();
include 'ket_noi.php';

// Query cho danh sách phiếu mượn với tìm kiếm
$tu_khoa = isset($_GET['tu_khoa']) ? $_GET['tu_khoa'] : '';
$trang_thai = isset($_GET['trang_thai']) ? $_GET['trang_thai'] : '';

$sql_simple = "SELECT pm.*, bd.ho_ten, bd.email, bd.so_dien_thoai, bd.ten_dang_nhap,
        CASE 
            WHEN pm.ngay_tra < CURDATE() AND pm.trang_thai = 'dang_muon' THEN 'qua_han'
            ELSE pm.trang_thai 
        END as trang_thai_hien_thi,
        DATEDIFF(CURDATE(), pm.ngay_tra) as so_ngay_qua_han
        FROM phieu_muon pm 
        JOIN ban_doc bd ON pm.ma_ban_doc = bd.ma_ban_doc";

$where_conditions = [];
$params = [];
$types = "";

if (!empty($tu_khoa)) {
    $where_conditions[] = "(bd.ho_ten LIKE ? OR bd.ten_dang_nhap LIKE ?)";
    $search_param = "%$tu_khoa%";
    $params[] = $search_param;
    $params[] = $search_param;
    $types .= "ss";
}

if (!empty($trang_thai)) {
    $where_conditions[] = "pm.trang_thai = ?";
    $params[] = $trang_thai;
    $types .= "s";
}

if (!empty($where_conditions)) {
    $sql_simple .= " WHERE " . implode(" AND ", $where_conditions);
}

$sql_simple .= " ORDER BY pm.ngay_muon DESC";

$stmt = $conn->prepare($sql_simple);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$ds_sach_muon = $stmt->get_result();

// Lấy thống kê (cập nhật cho đúng với enum values)
$sql_stats = "SELECT 
    COUNT(*) as tong_phieu,
    SUM(CASE WHEN trang_thai = 'dang_muon' THEN 1 ELSE 0 END) as dang_muon,
    SUM(CASE WHEN trang_thai = 'da_tra' THEN 1 ELSE 0 END) as da_tra,
    SUM(CASE WHEN ngay_tra < CURDATE() AND trang_thai = 'dang_muon' THEN 1 ELSE 0 END) as qua_han
    FROM phieu_muon";
$result_stats = $conn->query($sql_stats);
$stats = $result_stats->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Quản lý phiếu mượn</title>
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
        .status-badge {
            font-size: 12px;
            padding: 4px 8px;
            border-radius: 4px;
        }
        .status-dang-muon { background-color: #d4edda; color: #155724; }
        .status-da-tra { background-color: #d1ecf1; color: #0c5460; }
        .status-qua-han { background-color: #f8d7da; color: #721c24; }
        .filters {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
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
    
    <!-- Phần danh sách phiếu mượn -->
    <h4 class="mb-4 text-primary">📚 QUẢN LÝ MƯỢN SÁCH</h4>
    
    <!-- Thống kê -->
    <div class="stats-card">
        <div class="row text-center">
            <div class="col-md-4">
                <h5 class="text-primary"><?= $stats['tong_phieu'] ?></h5>
                <p class="mb-0">Tổng phiếu mượn</p>
            </div>
            <div class="col-md-4">
                <h5 class="text-success"><?= $stats['dang_muon'] ?></h5>
                <p class="mb-0">Đang mượn</p>
            </div>
            <div class="col-md-4">
                <h5 class="text-info"><?= $stats['da_tra'] ?></h5>
                <p class="mb-0">Đã trả</p>
            </div>
        </div>
        <?php if ($stats['qua_han'] > 0): ?>
        <div class="alert alert-warning mt-3 mb-0">
            <i class="fas fa-exclamation-triangle"></i> Có <strong><?= $stats['qua_han'] ?></strong> phiếu mượn quá hạn cần xử lý!
        </div>
        <?php endif; ?>
    </div>

    <!-- Bộ lọc -->
    <div class="filters">
        <form method="GET" class="row g-3">
            <div class="col-md-6">
                <label for="tu_khoa" class="form-label">Tìm kiếm</label>
                <input type="text" class="form-control" id="tu_khoa" name="tu_khoa" 
                       placeholder="Tên người mượn, tên đăng nhập..." value="<?= htmlspecialchars($tu_khoa) ?>">
            </div>
            <div class="col-md-4">
                <label for="trang_thai" class="form-label">Trạng thái</label>
                <select class="form-select" id="trang_thai" name="trang_thai">
                    <option value="">Tất cả trạng thái</option>
                    <option value="dang_muon" <?= $trang_thai === 'dang_muon' ? 'selected' : '' ?>>Đang mượn</option>
                    <option value="da_tra" <?= $trang_thai === 'da_tra' ? 'selected' : '' ?>>Đã trả</option>
                </select>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100"><i class="fas fa-search"></i> Tìm kiếm</button>
            </div>
        </form>
    </div>

    <!-- Bảng danh sách phiếu mượn -->
    <div class="table-responsive bg-white rounded shadow-sm">
        <table class="table table-hover">
            <thead class="table-primary">
                <tr>
                    <th>Mã phiếu</th>
                    <th>Người mượn</th>
                    <th>Tên đăng nhập</th>
                    <th>Thông tin liên hệ</th>
                    <th>Ngày mượn</th>
                    <th>Ngày trả</th>
                    <th>Trạng thái</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($ds_sach_muon->num_rows > 0): ?>
                    <?php while ($phieu = $ds_sach_muon->fetch_assoc()): ?>
                        <tr>
                            <td><strong><?= $phieu['ma_phieu'] ?></strong></td>
                            <td><?= htmlspecialchars($phieu['ho_ten']) ?></td>
                            <td><code><?= htmlspecialchars($phieu['ten_dang_nhap']) ?></code></td>
                            <td>
                                <?php if (!empty($phieu['email'])): ?>
                                    <small class="text-muted d-block">📧 <?= htmlspecialchars($phieu['email']) ?></small>
                                <?php endif; ?>
                                <?php if (!empty($phieu['so_dien_thoai'])): ?>
                                    <small class="text-muted d-block">📱 <?= htmlspecialchars($phieu['so_dien_thoai']) ?></small>
                                <?php endif; ?>
                            </td>
                            <td><?= date('d/m/Y', strtotime($phieu['ngay_muon'])) ?></td>
                            <td>
                                <?= date('d/m/Y', strtotime($phieu['ngay_tra'])) ?>
                                <?php if ($phieu['trang_thai_hien_thi'] === 'qua_han'): ?>
                                    <br><small class="text-danger"><i class="fas fa-exclamation-triangle"></i> Quá hạn <?= $phieu['so_ngay_qua_han'] ?> ngày</small>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php
                                $status_class = '';
                                $status_text = '';
                                $status_icon = '';
                                switch($phieu['trang_thai_hien_thi']) {
                                    case 'dang_muon':
                                        $status_class = 'status-dang-muon';
                                        $status_text = 'Đang mượn';
                                        $status_icon = 'fas fa-book-open';
                                        break;
                                    case 'da_tra':
                                        $status_class = 'status-da-tra';
                                        $status_text = 'Đã trả';
                                        $status_icon = 'fas fa-check-circle';
                                        break;
                                    case 'qua_han':
                                        $status_class = 'status-qua-han';
                                        $status_text = 'Quá hạn';
                                        $status_icon = 'fas fa-exclamation-triangle';
                                        break;
                                    default:
                                        $status_class = 'status-dang-muon';
                                        $status_text = ucfirst($phieu['trang_thai']);
                                        $status_icon = 'fas fa-question';
                                }
                                ?>
                                <span class="status-badge <?= $status_class ?>">
                                    <i class="<?= $status_icon ?>"></i> <?= $status_text ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($phieu['trang_thai'] === 'dang_muon'): ?>
                                    <a href="tra_sach.php?id=<?= $phieu['ma_phieu'] ?>" class="btn btn-warning btn-sm mb-1" title="Xử lý trả sách">
                                        <i class="fas fa-undo"></i>
                                    </a>
                                <?php endif; ?>
                                <a href="chi_tiet_phieu.php?id=<?= $phieu['ma_phieu'] ?>" class="btn btn-info btn-sm mb-1" title="Xem chi tiết">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="in_phieu.php?id=<?= $phieu['ma_phieu'] ?>" class="btn btn-secondary btn-sm mb-1" title="In phiếu mượn" target="_blank">
                                    <i class="fas fa-print"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" class="text-center py-4">
                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                            <p class="text-muted">Không có phiếu mượn nào</p>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    
    <!-- Thông tin hướng dẫn -->
    <div class="card mt-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="fas fa-info-circle"></i> Hướng dẫn quản lý phiếu mượn </h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h6>Các trạng thái phiếu mượn:</h6>
                    <ul>
                        <li><span class="status-badge status-dang-muon"><i class="fas fa-book-open"></i> Đang mượn</span> - Phiếu đang được sử dụng</li>
                        <li><span class="status-badge status-da-tra"><i class="fas fa-check-circle"></i> Đã trả</span> - Đã hoàn thành trả sách</li>
                        <li><span class="status-badge status-qua-han"><i class="fas fa-exclamation-triangle"></i> Quá hạn</span> - Đã quá thời hạn trả</li>
                    </ul>
                </div>
                <div class="col-md-6">
                    <h6>Các thao tác có thể thực hiện:</h6>
                    <ul>
                        <li><i class="fas fa-undo text-warning"></i> Xử lý trả sách (cho phiếu đang mượn)</li>
                        <li><i class="fas fa-eye text-info"></i> Xem chi tiết phiếu mượn</li>
                        <li><i class="fas fa-print text-secondary"></i> In phiếu mượn</li>
                        <li><i class="fas fa-search text-primary"></i> Tìm kiếm theo tên hoặc tên đăng nhập</li>
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
    // Auto expand menu based on current page
    const currentPath = window.location.pathname;
    const filename = currentPath.substring(currentPath.lastIndexOf('/') + 1);
    
    // Handle "Quản lý mượn/trả" submenu
    if (filename === 'cho_muon.php' || filename === 'nhan_tra.php' || filename === 'ds_muon_tra.php') {
        const muonTraMenu = new bootstrap.Collapse(document.getElementById('muontra'), {
            toggle: false
        });
        muonTraMenu.show();
    }
});
</script>
</body>
</html>