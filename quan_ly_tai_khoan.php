<?php
session_start();
include 'ket_noi.php';

// Kiểm tra quyền admin
if (!isset($_SESSION['ban_doc']) || $_SESSION['ban_doc']['vai_tro'] !== 'admin') {
    header('Location: dang_nhap.php');
    exit;
}

date_default_timezone_set('Asia/Ho_Chi_Minh');

// Xử lý các thao tác
$thong_bao = '';
$loai_thong_bao = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['them_tai_khoan'])) {
        $ten_dang_nhap = $_POST['ten_dang_nhap'];
        $mat_khau = $_POST['mat_khau']; // Nên hash trong thực tế
        $ho_ten = $_POST['ho_ten'];
        $email = $_POST['email'];
        $vai_tro = $_POST['vai_tro'];
        $so_dien_thoai = $_POST['so_dien_thoai'];
        $dia_chi = $_POST['dia_chi'];
        
        $stmt = $conn->prepare("INSERT INTO ban_doc (ten_dang_nhap, mat_khau, ho_ten, email, vai_tro, so_dien_thoai, dia_chi) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssss", $ten_dang_nhap, $mat_khau, $ho_ten, $email, $vai_tro, $so_dien_thoai, $dia_chi);
        
        if ($stmt->execute()) {
            $thong_bao = "Thêm tài khoản thành công!";
            $loai_thong_bao = 'success';
        } else {
            $thong_bao = "Lỗi: " . $conn->error;
            $loai_thong_bao = 'danger';
        }
    }
    
    if (isset($_POST['cap_nhat_vai_tro'])) {
        $ma_ban_doc = $_POST['ma_ban_doc'];
        $vai_tro_moi = $_POST['vai_tro_moi'];
        
        $stmt = $conn->prepare("UPDATE ban_doc SET vai_tro = ? WHERE ma_ban_doc = ?");
        $stmt->bind_param("si", $vai_tro_moi, $ma_ban_doc);
        
        if ($stmt->execute()) {
            $thong_bao = "Cập nhật vai trò thành công!";
            $loai_thong_bao = 'success';
        } else {
            $thong_bao = "Lỗi cập nhật: " . $conn->error;
            $loai_thong_bao = 'danger';
        }
    }
    
    if (isset($_POST['xoa_tai_khoan'])) {
        $ma_ban_doc = $_POST['ma_ban_doc'];
        
        // Kiểm tra xem có phiếu mượn nào không
        $check_stmt = $conn->prepare("SELECT COUNT(*) as count FROM phieu_muon WHERE ma_ban_doc = ?");
        $check_stmt->bind_param("i", $ma_ban_doc);
        $check_stmt->execute();
        $result = $check_stmt->get_result();
        $count = $result->fetch_assoc()['count'];
        
        if ($count > 0) {
            $thong_bao = "Không thể xóa tài khoản này vì đã có lịch sử mượn sách!";
            $loai_thong_bao = 'warning';
        } else {
            $stmt = $conn->prepare("DELETE FROM ban_doc WHERE ma_ban_doc = ?");
            $stmt->bind_param("i", $ma_ban_doc);
            
            if ($stmt->execute()) {
                $thong_bao = "Xóa tài khoản thành công!";
                $loai_thong_bao = 'success';
            } else {
                $thong_bao = "Lỗi xóa: " . $conn->error;
                $loai_thong_bao = 'danger';
            }
        }
    }
}

// Lấy danh sách tài khoản với thông tin chi tiết
$tu_khoa = isset($_GET['tu_khoa']) ? $_GET['tu_khoa'] : '';
$vai_tro_filter = isset($_GET['vai_tro']) ? $_GET['vai_tro'] : '';

$sql = "SELECT bd.*, 
        (SELECT COUNT(*) FROM phieu_muon pm WHERE pm.ma_ban_doc = bd.ma_ban_doc) as so_luot_muon,
        (SELECT COUNT(*) FROM phieu_muon pm WHERE pm.ma_ban_doc = bd.ma_ban_doc AND pm.trang_thai = 'dang_muon') as dang_muon,
        (SELECT MAX(pm.ngay_muon) FROM phieu_muon pm WHERE pm.ma_ban_doc = bd.ma_ban_doc) as lan_hoat_dong_cuoi
        FROM ban_doc bd";

$where_conditions = [];
$params = [];
$types = "";

if (!empty($tu_khoa)) {
    $where_conditions[] = "(bd.ho_ten LIKE ? OR bd.ten_dang_nhap LIKE ? OR bd.email LIKE ?)";
    $search_param = "%$tu_khoa%";
    $params[] = $search_param;
    $params[] = $search_param;
    $params[] = $search_param;
    $types .= "sss";
}

if (!empty($vai_tro_filter)) {
    $where_conditions[] = "bd.vai_tro = ?";
    $params[] = $vai_tro_filter;
    $types .= "s";
}

if (!empty($where_conditions)) {
    $sql .= " WHERE " . implode(" AND ", $where_conditions);
}

$sql .= " ORDER BY bd.vai_tro DESC, bd.ho_ten ASC";

$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$ds_tai_khoan = $stmt->get_result();

// Thống kê
$sql_stats = "SELECT 
    COUNT(*) as tong_tai_khoan,
    SUM(CASE WHEN vai_tro = 'admin' THEN 1 ELSE 0 END) as admin_count,
    SUM(CASE WHEN vai_tro = 'bandoc' THEN 1 ELSE 0 END) as bandoc_count
    FROM ban_doc";
$result_stats = $conn->query($sql_stats);
$stats = $result_stats->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Quản lý tài khoản</title>
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
    .role-badge {
        font-size: 12px;
        padding: 4px 8px;
        border-radius: 4px;
    }
    .role-admin { background-color: #dc3545; color: white; }
    .role-bandoc { background-color: #198754; color: white; }
    .filters {
        background: white;
        padding: 20px;
        border-radius: 8px;
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
    <a href="gioi_thieu.php"><i class="fas fa-info-circle me-2"></i>Sửa giới thiệu</a>
    <a href="lien_he.php"><i class="fas fa-envelope me-2"></i>Sửa liên hệ</a>
</div>

<div class="main">
  <div class="container-fluid">
    <div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold text-primary mb-0">📦 QUẢN LÝ TÀI KHOẢN</h4>
    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#themTaiKhoanModal">
        <i class="fas fa-plus"></i> Thêm tài khoản mới
    </button>
</div>

    <!-- Thông báo -->
    <?php if (!empty($thong_bao)): ?>
        <div class="alert alert-<?= $loai_thong_bao ?> alert-dismissible fade show">
            <?= htmlspecialchars($thong_bao) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    
    <!-- Thống kê -->
    <div class="stats-card">
        <div class="row text-center">
            <div class="col-md-4">
                <h5 class="text-primary"><?= $stats['tong_tai_khoan'] ?></h5>
                <p class="mb-0">Tổng tài khoản</p>
            </div>
            <div class="col-md-4">
                <h5 class="text-danger"><?= $stats['admin_count'] ?></h5>
                <p class="mb-0">Quản trị viên</p>
            </div>
            <div class="col-md-4">
                <h5 class="text-success"><?= $stats['bandoc_count'] ?></h5>
                <p class="mb-0">Bạn đọc</p>
            </div>
        </div>
    </div>

    <!-- Bộ lọc -->
    <div class="filters">
        <form method="GET" class="row g-3">
            <div class="col-md-6">
                <label for="tu_khoa" class="form-label">Tìm kiếm</label>
                <input type="text" class="form-control" id="tu_khoa" name="tu_khoa" 
                       placeholder="Tên, tên đăng nhập, email..." value="<?= htmlspecialchars($tu_khoa) ?>">
            </div>
            <div class="col-md-4">
                <label for="vai_tro" class="form-label">Vai trò</label>
                <select class="form-select" id="vai_tro" name="vai_tro">
                    <option value="">Tất cả vai trò</option>
                    <option value="admin" <?= $vai_tro_filter === 'admin' ? 'selected' : '' ?>>Quản trị viên</option>
                    <option value="bandoc" <?= $vai_tro_filter === 'bandoc' ? 'selected' : '' ?>>Bạn đọc</option>
                </select>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100"><i class="fas fa-search"></i> Tìm</button>
            </div>
        </form>
    </div>

    <!-- Bảng danh sách -->
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-primary">
                <tr>
                    <th width="8%">STT</th>
                    <th width="15%">Tên đăng nhập</th>
                    <th width="20%">Họ tên</th>
                    <th width="20%">Thông tin liên hệ</th>
                    <th width="10%">Vai trò</th>
                    <th width="12%">Hoạt động</th>
                    <th width="15%">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($ds_tai_khoan->num_rows > 0): ?>
                    <?php $i = 1; while ($row = $ds_tai_khoan->fetch_assoc()): ?>
                        <tr>
                            <td><?= $i++ ?></td>
                            <td>
                                <strong><?= htmlspecialchars($row['ten_dang_nhap']) ?></strong>
                                <br><small class="text-muted">ID: <?= $row['ma_ban_doc'] ?></small>
                            </td>
                            <td><?= htmlspecialchars($row['ho_ten']) ?></td>
                            <td>
                                <?php if (!empty($row['email'])): ?>
                                    <small class="text-muted d-block">
                                        <i class="fas fa-envelope me-1"></i>
                                        <?= htmlspecialchars($row['email']) ?>
                                    </small>
                                <?php endif; ?>
                                <?php if (!empty($row['so_dien_thoai'])): ?>
                                    <small class="text-muted d-block">
                                        <i class="fas fa-phone me-1"></i>
                                        <?= htmlspecialchars($row['so_dien_thoai']) ?>
                                    </small>
                                <?php endif; ?>
                                <?php if (empty($row['email']) && empty($row['so_dien_thoai'])): ?>
                                    <small class="text-muted">Chưa cập nhật</small>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="role-badge role-<?= $row['vai_tro'] ?>">
                                    <?= $row['vai_tro'] === 'admin' ? 'Quản trị viên' : 'Bạn đọc' ?>
                                </span>
                            </td>
                            <td>
                                <small class="text-muted d-block"><?= $row['so_luot_muon'] ?> lượt mượn</small>
                                <?php if ($row['dang_muon'] > 0): ?>
                                    <small class="text-warning d-block">Đang mượn: <?= $row['dang_muon'] ?></small>
                                <?php endif; ?>
                                <?php if (!empty($row['lan_hoat_dong_cuoi'])): ?>
                                    <small class="text-muted d-block">Hoạt động: <?= date('d/m/Y', strtotime($row['lan_hoat_dong_cuoi'])) ?></small>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="btn-group-vertical btn-group-sm">
                                    <button class="btn btn-warning btn-sm mb-1" onclick="capNhatVaiTro(<?= $row['ma_ban_doc'] ?>, '<?= $row['vai_tro'] ?>')" title="Đổi vai trò">
                                        <i class="fas fa-user-cog"></i>
                                    </button>
                                    <?php if ($row['so_luot_muon'] == 0): ?>
                                        <button class="btn btn-danger btn-sm" onclick="xoaTaiKhoan(<?= $row['ma_ban_doc'] ?>, '<?= htmlspecialchars($row['ho_ten']) ?>')" title="Xóa tài khoản">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    <?php else: ?>
                                        <button class="btn btn-secondary btn-sm" disabled title="Có lịch sử mượn sách">
                                            <i class="fas fa-lock"></i>
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="text-center py-4">
                            <i class="fas fa-users-slash fa-3x text-muted mb-3"></i>
                            <p class="text-muted">Không tìm thấy tài khoản nào</p>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Hướng dẫn -->
    <div class="card mt-4">
        <div class="card-header bg-info text-white">
            <h5 class="mb-0"><i class="fas fa-info-circle"></i> Hướng dẫn quản lý tài khoản</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h6>Vai trò hệ thống:</h6>
                    <ul class="mb-0">
                        <li><span class="role-badge role-admin">Quản trị viên</span> - Toàn quyền quản lý hệ thống</li>
                        <li><span class="role-badge role-bandoc">Bạn đọc</span> - Chỉ có thể mượn/trả sách</li>
                    </ul>
                </div>
                <div class="col-md-6">
                    <h6>Thao tác có thể thực hiện:</h6>
                    <ul class="mb-0">
                        <li><i class="fas fa-plus text-primary"></i> Thêm tài khoản mới</li>
                        <li><i class="fas fa-user-cog text-warning"></i> Thay đổi vai trò</li>
                        <li><i class="fas fa-trash text-danger"></i> Xóa tài khoản (chỉ nếu chưa có lịch sử)</li>
                        <li><i class="fas fa-search text-info"></i> Tìm kiếm và lọc</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

  </div>
</div>

<!-- Modal thêm tài khoản -->
<div class="modal fade" id="themTaiKhoanModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-user-plus"></i> Thêm tài khoản mới</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Tên đăng nhập *</label>
                                <input type="text" class="form-control" name="ten_dang_nhap" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Mật khẩu *</label>
                                <input type="password" class="form-control" name="mat_khau" required>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Họ tên *</label>
                        <input type="text" class="form-control" name="ho_ten" required>
                    </div>
                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control" name="email">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Vai trò *</label>
                                <select class="form-select" name="vai_tro" required>
                                    <option value="bandoc">Bạn đọc</option>
                                    <option value="admin">Quản trị viên</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Số điện thoại</label>
                                <input type="tel" class="form-control" name="so_dien_thoai">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Địa chỉ</label>
                                <input type="text" class="form-control" name="dia_chi">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" name="them_tai_khoan" class="btn btn-primary">
                        <i class="fas fa-save"></i> Lưu tài khoản
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Forms ẩn cho thao tác -->
<form id="capNhatVaiTroForm" method="POST" style="display: none;">
    <input type="hidden" name="ma_ban_doc" id="capNhatMaBanDoc">
    <input type="hidden" name="vai_tro_moi" id="capNhatVaiTroMoi">
    <input type="hidden" name="cap_nhat_vai_tro" value="1">
</form>

<form id="xoaTaiKhoanForm" method="POST" style="display: none;">
    <input type="hidden" name="ma_ban_doc" id="xoaMaBanDoc">
    <input type="hidden" name="xoa_tai_khoan" value="1">
</form>

<?php include 'footer1.php'; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto expand hệ thống menu
    const heThongMenu = new bootstrap.Collapse(document.getElementById('hethong'), {
        toggle: false
    });
    heThongMenu.show();
});

function capNhatVaiTro(maBanDoc, vaiTroHienTai) {
    const vaiTroMoi = vaiTroHienTai === 'admin' ? 'bandoc' : 'admin';
    const tenVaiTroMoi = vaiTroMoi === 'admin' ? 'Quản trị viên' : 'Bạn đọc';
    
    if (confirm(`Bạn có chắc muốn đổi vai trò thành "${tenVaiTroMoi}"?`)) {
        document.getElementById('capNhatMaBanDoc').value = maBanDoc;
        document.getElementById('capNhatVaiTroMoi').value = vaiTroMoi;
        document.getElementById('capNhatVaiTroForm').submit();
    }
}

function xoaTaiKhoan(maBanDoc, hoTen) {
    if (confirm(`Bạn có chắc muốn xóa tài khoản "${hoTen}"?\nHành động này không thể hoàn tác!`)) {
        document.getElementById('xoaMaBanDoc').value = maBanDoc;
        document.getElementById('xoaTaiKhoanForm').submit();
    }
}
</script>
</body>
</html>