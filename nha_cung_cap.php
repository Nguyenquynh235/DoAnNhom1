<?php
session_start();
include 'ket_noi.php';

// Kiểm tra quyền admin
if (!isset($_SESSION['ban_doc']) || $_SESSION['ban_doc']['vai_tro'] !== 'admin') {
    header('Location: dang_nhap.php');
    exit;
}

date_default_timezone_set('Asia/Ho_Chi_Minh');

// Tạo bảng nhà cung cấp nếu chưa tồn tại
$create_table_sql = "CREATE TABLE IF NOT EXISTS nha_cung_cap (
    ma_ncc INT AUTO_INCREMENT PRIMARY KEY,
    ten_ncc VARCHAR(255) NOT NULL,
    dia_chi TEXT,
    so_dien_thoai VARCHAR(20),
    email VARCHAR(100),
    nguoi_lien_he VARCHAR(100),
    trang_thai ENUM('hoat_dong', 'ngung_hop_tac') DEFAULT 'hoat_dong',
    ngay_tao TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
$conn->query($create_table_sql);

// Thêm dữ liệu mẫu nếu bảng trống
$check_data = $conn->query("SELECT COUNT(*) as count FROM nha_cung_cap");
$count = $check_data->fetch_assoc()['count'];

if ($count == 0) {
    $sample_data = [
        ['Nhà Xuất Bản Giáo Dục Việt Nam', '81 Trần Hưng Đạo, Hoàn Kiếm, Hà Nội', '024-3822-3434', 'info@nxbgd.vn', 'Nguyễn Văn A'],
        ['Nhà Xuất Bản Trẻ', '161B Lý Chính Thắng, Q.3, TP.HCM', '028-3930-5859', 'info@nxbtre.com.vn', 'Trần Thị B'],
        ['Nhà Xuất Bản Thế Giới', '7 Nguyễn Thị Minh Khai, Q.1, TP.HCM', '028-3822-2340', 'thegioi@nxbthegioi.vn', 'Lê Văn C'],
        ['Công Ty TNHH Sách Alpha', '123 Điện Biên Phủ, Ba Đình, Hà Nội', '024-3736-2612', 'contact@alphabooks.vn', 'Phạm Minh D'],
        ['Nhà Phát Hành Fahasa', '60-62 Lê Lợi, Q.1, TP.HCM', '028-3822-4477', 'fahasa@fahasa.com', 'Hoàng Thị E'],
        ['Công Ty CP Đầu Tư và Phát Triển Giáo Dục Phương Nam', '112 Nguyễn Văn Cừ, Q.1, TP.HCM', '028-3848-4499', 'phuongnam@pnbook.com', 'Võ Văn F']
    ];
    
    foreach ($sample_data as $data) {
        $stmt = $conn->prepare("INSERT INTO nha_cung_cap (ten_ncc, dia_chi, so_dien_thoai, email, nguoi_lien_he) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $data[0], $data[1], $data[2], $data[3], $data[4]);
        $stmt->execute();
    }
}

// Xử lý các thao tác
$thong_bao = '';
$loai_thong_bao = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['them_ncc'])) {
        $ten_ncc = $_POST['ten_ncc'];
        $dia_chi = $_POST['dia_chi'];
        $so_dien_thoai = $_POST['so_dien_thoai'];
        $email = $_POST['email'];
        $nguoi_lien_he = $_POST['nguoi_lien_he'];
        $trang_thai = $_POST['trang_thai'];
        
        $stmt = $conn->prepare("INSERT INTO nha_cung_cap (ten_ncc, dia_chi, so_dien_thoai, email, nguoi_lien_he, trang_thai) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $ten_ncc, $dia_chi, $so_dien_thoai, $email, $nguoi_lien_he, $trang_thai);
        
        if ($stmt->execute()) {
            $thong_bao = "Thêm nhà cung cấp thành công!";
            $loai_thong_bao = 'success';
        } else {
            $thong_bao = "Lỗi: " . $conn->error;
            $loai_thong_bao = 'danger';
        }
    }
    
    if (isset($_POST['cap_nhat_ncc'])) {
        $ma_ncc = $_POST['ma_ncc'];
        $ten_ncc = $_POST['ten_ncc'];
        $dia_chi = $_POST['dia_chi'];
        $so_dien_thoai = $_POST['so_dien_thoai'];
        $email = $_POST['email'];
        $nguoi_lien_he = $_POST['nguoi_lien_he'];
        $trang_thai = $_POST['trang_thai'];
        
        $stmt = $conn->prepare("UPDATE nha_cung_cap SET ten_ncc = ?, dia_chi = ?, so_dien_thoai = ?, email = ?, nguoi_lien_he = ?, trang_thai = ? WHERE ma_ncc = ?");
        $stmt->bind_param("ssssssi", $ten_ncc, $dia_chi, $so_dien_thoai, $email, $nguoi_lien_he, $trang_thai, $ma_ncc);
        
        if ($stmt->execute()) {
            $thong_bao = "Cập nhật nhà cung cấp thành công!";
            $loai_thong_bao = 'success';
        } else {
            $thong_bao = "Lỗi cập nhật: " . $conn->error;
            $loai_thong_bao = 'danger';
        }
    }
    
    if (isset($_POST['xoa_ncc'])) {
        $ma_ncc = $_POST['ma_ncc'];
        
        $stmt = $conn->prepare("DELETE FROM nha_cung_cap WHERE ma_ncc = ?");
        $stmt->bind_param("i", $ma_ncc);
        
        if ($stmt->execute()) {
            $thong_bao = "Xóa nhà cung cấp thành công!";
            $loai_thong_bao = 'success';
        } else {
            $thong_bao = "Lỗi xóa: " . $conn->error;
            $loai_thong_bao = 'danger';
        }
    }
}

// Lấy danh sách nhà cung cấp
$tu_khoa = isset($_GET['tu_khoa']) ? $_GET['tu_khoa'] : '';
$trang_thai_filter = isset($_GET['trang_thai']) ? $_GET['trang_thai'] : '';

$sql = "SELECT * FROM nha_cung_cap";
$where_conditions = [];
$params = [];
$types = "";

if (!empty($tu_khoa)) {
    $where_conditions[] = "(ten_ncc LIKE ? OR nguoi_lien_he LIKE ? OR email LIKE ?)";
    $search_param = "%$tu_khoa%";
    $params[] = $search_param;
    $params[] = $search_param;
    $params[] = $search_param;
    $types .= "sss";
}

if (!empty($trang_thai_filter)) {
    $where_conditions[] = "trang_thai = ?";
    $params[] = $trang_thai_filter;
    $types .= "s";
}

if (!empty($where_conditions)) {
    $sql .= " WHERE " . implode(" AND ", $where_conditions);
}

$sql .= " ORDER BY ten_ncc ASC";

$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$ds_ncc = $stmt->get_result();

// Thống kê
$sql_stats = "SELECT 
    COUNT(*) as tong_ncc,
    SUM(CASE WHEN trang_thai = 'hoat_dong' THEN 1 ELSE 0 END) as dang_hop_tac,
    SUM(CASE WHEN trang_thai = 'ngung_hop_tac' THEN 1 ELSE 0 END) as ngung_hop_tac
    FROM nha_cung_cap";
$result_stats = $conn->query($sql_stats);
$stats = $result_stats->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Quản lý nhà cung cấp</title>
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
    .status-hoat-dong { background-color: #d4edda; color: #155724; }
    .status-ngung-hop-tac { background-color: #f8d7da; color: #721c24; }
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
<nav class="navbar navbar-light bg-white shadow-sm fixed-top px-4 py-2 d-flex justify-content-between">
  <span class="fw-bold">🏢 QUẢN LÝ NHÀ CUNG CẤP</span>
  <div>
    <a href="them_sach_tu_ncc.php" class="btn btn-success btn-sm me-2">
      <i class="fas fa-plus-circle"></i> Thêm sách từ NCC
    </a>
    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#themNCCModal">
      <i class="fas fa-plus"></i> Thêm nhà cung cấp
    </button>
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
    <a href="gioi_thieu.php"><i class="fas fa-info-circle me-2"></i>Giới thiệu</a>
    <a href="lien_he.php"><i class="fas fa-envelope me-2"></i>Liên hệ</a>
</div>

<div class="main">
  <div class="container-fluid">
    
    <!-- Thông báo -->
    <?php if (!empty($thong_bao)): ?>
        <div class="alert alert-<?= $loai_thong_bao ?> alert-dismissible fade show">
            <?= htmlspecialchars($thong_bao) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    
    <!-- Thống kê tổng quan -->
    <div class="stats-card">
        <div class="row text-center">
            <div class="col-md-4">
                <h5 class="text-primary"><?= $stats['tong_ncc'] ?></h5>
                <p class="mb-0">Tổng nhà cung cấp</p>
            </div>
            <div class="col-md-4">
                <h5 class="text-success"><?= $stats['dang_hop_tac'] ?></h5>
                <p class="mb-0">Đang hợp tác</p>
            </div>
            <div class="col-md-4">
                <h5 class="text-danger"><?= $stats['ngung_hop_tac'] ?></h5>
                <p class="mb-0">Ngừng hợp tác</p>
            </div>
        </div>
    </div>

    <!-- Bộ lọc -->
    <div class="filters">
        <form method="GET" class="row g-3">
            <div class="col-md-8">
                <label for="tu_khoa" class="form-label">Tìm kiếm</label>
                <input type="text" class="form-control" id="tu_khoa" name="tu_khoa" 
                       placeholder="Tên nhà cung cấp, người liên hệ, email..." value="<?= htmlspecialchars($tu_khoa) ?>">
            </div>
            <div class="col-md-2">
                <label for="trang_thai" class="form-label">Trạng thái</label>
                <select class="form-select" id="trang_thai" name="trang_thai">
                    <option value="">Tất cả</option>
                    <option value="hoat_dong" <?= $trang_thai_filter === 'hoat_dong' ? 'selected' : '' ?>>Hoạt động</option>
                    <option value="ngung_hop_tac" <?= $trang_thai_filter === 'ngung_hop_tac' ? 'selected' : '' ?>>Ngừng hợp tác</option>
                </select>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100"><i class="fas fa-search"></i> Tìm</button>
            </div>
        </form>
    </div>

    <h4 class="mb-4 text-primary">🏢 Danh sách nhà cung cấp</h4>
    
    <!-- Bảng danh sách -->
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-primary">
                <tr>
                    <th width="5%">STT</th>
                    <th width="20%">Tên nhà cung cấp</th>
                    <th width="25%">Địa chỉ</th>
                    <th width="15%">Liên hệ</th>
                    <th width="15%">Người liên hệ</th>
                    <th width="10%">Trạng thái</th>
                    <th width="10%">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($ds_ncc && $ds_ncc->num_rows > 0): ?>
                    <?php $i = 1; while ($ncc = $ds_ncc->fetch_assoc()): ?>
                        <tr>
                            <td><?= $i++ ?></td>
                            <td>
                                <strong><?= htmlspecialchars($ncc['ten_ncc']) ?></strong>
                                <br><small class="text-muted">ID: <?= $ncc['ma_ncc'] ?></small>
                            </td>
                            <td>
                                <small><?= htmlspecialchars($ncc['dia_chi']) ?></small>
                            </td>
                            <td>
                                <?php if (!empty($ncc['so_dien_thoai'])): ?>
                                    <small class="text-muted d-block">
                                        <i class="fas fa-phone me-1"></i>
                                        <a href="tel:<?= htmlspecialchars($ncc['so_dien_thoai']) ?>">
                                            <?= htmlspecialchars($ncc['so_dien_thoai']) ?>
                                        </a>
                                    </small>
                                <?php endif; ?>
                                <?php if (!empty($ncc['email'])): ?>
                                    <small class="text-muted d-block">
                                        <i class="fas fa-envelope me-1"></i>
                                        <a href="mailto:<?= htmlspecialchars($ncc['email']) ?>">
                                            <?= htmlspecialchars($ncc['email']) ?>
                                        </a>
                                    </small>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?= htmlspecialchars($ncc['nguoi_lien_he']) ?>
                                <br><small class="text-muted">Từ: <?= date('d/m/Y', strtotime($ncc['ngay_tao'])) ?></small>
                            </td>
                            <td>
                                <span class="status-badge status-<?= $ncc['trang_thai'] ?>">
                                    <?= $ncc['trang_thai'] === 'hoat_dong' ? 'Hoạt động' : 'Ngừng hợp tác' ?>
                                </span>
                            </td>
                            <td>
                                <div class="btn-group-vertical btn-group-sm">
                                    <button class="btn btn-primary btn-sm mb-1" onclick="suaNCC(<?= htmlspecialchars(json_encode($ncc)) ?>)" title="Sửa thông tin">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-danger btn-sm" onclick="xoaNCC(<?= $ncc['ma_ncc'] ?>, '<?= htmlspecialchars($ncc['ten_ncc']) ?>')" title="Xóa nhà cung cấp">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="text-center py-4">
                            <i class="fas fa-truck-loading fa-3x text-muted mb-3"></i>
                            <p class="text-muted">Không tìm thấy nhà cung cấp nào</p>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

  </div>
</div>

<!-- Modal thêm nhà cung cấp -->
<div class="modal fade" id="themNCCModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-truck"></i> Thêm nhà cung cấp mới</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label class="form-label">Tên nhà cung cấp *</label>
                                <input type="text" class="form-control" name="ten_ncc" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Trạng thái *</label>
                                <select class="form-select" name="trang_thai" required>
                                    <option value="hoat_dong">Hoạt động</option>
                                    <option value="ngung_hop_tac">Ngừng hợp tác</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Địa chỉ</label>
                        <textarea class="form-control" name="dia_chi" rows="2"></textarea>
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
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control" name="email">
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Người liên hệ</label>
                        <input type="text" class="form-control" name="nguoi_lien_he">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" name="them_ncc" class="btn btn-primary">
                        <i class="fas fa-save"></i> Thêm nhà cung cấp
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal sửa nhà cung cấp -->
<div class="modal fade" id="suaNCCModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-edit"></i> Sửa thông tin nhà cung cấp</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <input type="hidden" name="ma_ncc" id="sua_ma_ncc">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label class="form-label">Tên nhà cung cấp *</label>
                                <input type="text" class="form-control" name="ten_ncc" id="sua_ten_ncc" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Trạng thái *</label>
                                <select class="form-select" name="trang_thai" id="sua_trang_thai" required>
                                    <option value="hoat_dong">Hoạt động</option>
                                    <option value="ngung_hop_tac">Ngừng hợp tác</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Địa chỉ</label>
                        <textarea class="form-control" name="dia_chi" id="sua_dia_chi" rows="2"></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Số điện thoại</label>
                                <input type="tel" class="form-control" name="so_dien_thoai" id="sua_so_dien_thoai">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control" name="email" id="sua_email">
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Người liên hệ</label>
                        <input type="text" class="form-control" name="nguoi_lien_he" id="sua_nguoi_lien_he">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" name="cap_nhat_ncc" class="btn btn-primary">
                        <i class="fas fa-save"></i> Cập nhật
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Form ẩn để xóa -->
<form id="xoaNCCForm" method="POST" style="display: none;">
    <input type="hidden" name="ma_ncc" id="xoa_ma_ncc">
    <input type="hidden" name="xoa_ncc" value="1">
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

function suaNCC(ncc) {
    document.getElementById('sua_ma_ncc').value = ncc.ma_ncc;
    document.getElementById('sua_ten_ncc').value = ncc.ten_ncc;
    document.getElementById('sua_dia_chi').value = ncc.dia_chi || '';
    document.getElementById('sua_so_dien_thoai').value = ncc.so_dien_thoai || '';
    document.getElementById('sua_email').value = ncc.email || '';
    document.getElementById('sua_nguoi_lien_he').value = ncc.nguoi_lien_he || '';
    document.getElementById('sua_trang_thai').value = ncc.trang_thai;
    
    new bootstrap.Modal(document.getElementById('suaNCCModal')).show();
}

function xoaNCC(maNCC, tenNCC) {
    if (confirm(`Bạn có chắc muốn xóa nhà cung cấp "${tenNCC}"?\nHành động này không thể hoàn tác!`)) {
        document.getElementById('xoa_ma_ncc').value = maNCC;
        document.getElementById('xoaNCCForm').submit();
    }
}
</script>
</body>
</html>