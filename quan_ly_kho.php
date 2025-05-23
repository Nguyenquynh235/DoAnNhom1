<?php
session_start();
include 'ket_noi.php';

// Kiểm tra quyền admin
if (!isset($_SESSION['ban_doc']) || $_SESSION['ban_doc']['vai_tro'] !== 'admin') {
    header('Location: dang_nhap.php');
    exit;
}

date_default_timezone_set('Asia/Ho_Chi_Minh');

// Tạo bảng kho nếu chưa tồn tại
$create_table_sql = "CREATE TABLE IF NOT EXISTS kho (
    ma_kho INT AUTO_INCREMENT PRIMARY KEY,
    ten_kho VARCHAR(255) NOT NULL,
    vi_tri VARCHAR(255) NOT NULL,
    mo_ta TEXT,
    suc_chua INT DEFAULT 1000,
    trang_thai ENUM('hoat_dong', 'bao_tri', 'ngung_hoat_dong') DEFAULT 'hoat_dong',
    ngay_tao TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
$conn->query($create_table_sql);

// Thêm dữ liệu mẫu nếu bảng trống
$check_data = $conn->query("SELECT COUNT(*) as count FROM kho");
$count = $check_data->fetch_assoc()['count'];

if ($count == 0) {
    $sample_data = [
        ['Kho Sách Khoa Học Tự Nhiên', 'Tầng 2 - Phía Đông', 'Lưu trữ sách về Toán, Lý, Hóa, Sinh học', 1500],
        ['Kho Sách Khoa Học Xã Hội', 'Tầng 2 - Phía Tây', 'Lưu trữ sách về Kinh tế, Luật, Xã hội học', 1200],
        ['Kho Sách Công Nghệ Thông Tin', 'Tầng 3 - Phía Nam', 'Lưu trữ sách về IT, Lập trình, Mạng', 800],
        ['Kho Sách Ngoại Ngữ', 'Tầng 1 - Phía Bắc', 'Lưu trữ sách tiếng Anh, tiếng Nhật, tiếng Hàn', 600],
        ['Kho Sách Văn Học', 'Tầng 1 - Phía Nam', 'Lưu trữ tiểu thuyết, thơ ca, truyện ngắn', 1000],
        ['Kho Lưu Trữ', 'Tầng Hầm - B1', 'Lưu trữ sách cũ và tài liệu lưu trữ', 2000]
    ];
    
    foreach ($sample_data as $data) {
        $stmt = $conn->prepare("INSERT INTO kho (ten_kho, vi_tri, mo_ta, suc_chua) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("sssi", $data[0], $data[1], $data[2], $data[3]);
        $stmt->execute();
    }
}

// Xử lý các thao tác
$thong_bao = '';
$loai_thong_bao = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['them_kho'])) {
        $ten_kho = $_POST['ten_kho'];
        $vi_tri = $_POST['vi_tri'];
        $mo_ta = $_POST['mo_ta'];
        $suc_chua = $_POST['suc_chua'];
        $trang_thai = $_POST['trang_thai'];
        
        $stmt = $conn->prepare("INSERT INTO kho (ten_kho, vi_tri, mo_ta, suc_chua, trang_thai) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssis", $ten_kho, $vi_tri, $mo_ta, $suc_chua, $trang_thai);
        
        if ($stmt->execute()) {
            $thong_bao = "Thêm kho thành công!";
            $loai_thong_bao = 'success';
        } else {
            $thong_bao = "Lỗi: " . $conn->error;
            $loai_thong_bao = 'danger';
        }
    }
    
    if (isset($_POST['cap_nhat_kho'])) {
        $ma_kho = $_POST['ma_kho'];
        $ten_kho = $_POST['ten_kho'];
        $vi_tri = $_POST['vi_tri'];
        $mo_ta = $_POST['mo_ta'];
        $suc_chua = $_POST['suc_chua'];
        $trang_thai = $_POST['trang_thai'];
        
        $stmt = $conn->prepare("UPDATE kho SET ten_kho = ?, vi_tri = ?, mo_ta = ?, suc_chua = ?, trang_thai = ? WHERE ma_kho = ?");
        $stmt->bind_param("sssisi", $ten_kho, $vi_tri, $mo_ta, $suc_chua, $trang_thai, $ma_kho);
        
        if ($stmt->execute()) {
            $thong_bao = "Cập nhật kho thành công!";
            $loai_thong_bao = 'success';
        } else {
            $thong_bao = "Lỗi cập nhật: " . $conn->error;
            $loai_thong_bao = 'danger';
        }
    }
    
    if (isset($_POST['xoa_kho'])) {
        $ma_kho = $_POST['ma_kho'];
        
        $stmt = $conn->prepare("DELETE FROM kho WHERE ma_kho = ?");
        $stmt->bind_param("i", $ma_kho);
        
        if ($stmt->execute()) {
            $thong_bao = "Xóa kho thành công!";
            $loai_thong_bao = 'success';
        } else {
            $thong_bao = "Lỗi xóa: " . $conn->error;
            $loai_thong_bao = 'danger';
        }
    }
}

// Lấy danh sách kho với dữ liệu sách thật
$sql = "SELECT k.*, 
        COALESCE(COUNT(DISTINCT s.ma_sach), 0) as so_loai_sach,
        COALESCE(SUM(s.so_luong), 0) as so_sach_hien_tai,
        COALESCE(SUM(s.gia_nhap * s.so_luong), 0) as gia_tri_kho,
        GROUP_CONCAT(DISTINCT ncc.ten_ncc SEPARATOR ', ') as nha_cung_cap
        FROM kho k 
        LEFT JOIN sach s ON k.ma_kho = s.ma_kho AND s.trang_thai_sach = 'trong_kho'
        LEFT JOIN nha_cung_cap ncc ON s.ma_ncc = ncc.ma_ncc
        GROUP BY k.ma_kho, k.ten_kho, k.vi_tri, k.mo_ta, k.suc_chua, k.trang_thai, k.ngay_tao
        ORDER BY k.ma_kho ASC";
$ds_kho = $conn->query($sql);

// Thống kê tổng quan với dữ liệu thật
$sql_stats = "SELECT 
    COUNT(DISTINCT k.ma_kho) as tong_kho,
    SUM(k.suc_chua) as tong_suc_chua,
    SUM(CASE WHEN k.trang_thai = 'hoat_dong' THEN 1 ELSE 0 END) as kho_hoat_dong,
    SUM(CASE WHEN k.trang_thai = 'bao_tri' THEN 1 ELSE 0 END) as kho_bao_tri,
    COALESCE(SUM(s.so_luong), 0) as tong_sach_trong_kho,
    COALESCE(SUM(s.gia_nhap * s.so_luong), 0) as tong_gia_tri
    FROM kho k
    LEFT JOIN sach s ON k.ma_kho = s.ma_kho AND s.trang_thai_sach = 'trong_kho'";
$result_stats = $conn->query($sql_stats);
$stats = $result_stats->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Quản lý kho</title>
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
    .status-bao-tri { background-color: #fff3cd; color: #856404; }
    .status-ngung-hoat-dong { background-color: #f8d7da; color: #721c24; }
    .progress-thin {
        height: 8px;
    }
    .table-responsive {
        background: white;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    .warehouse-card {
        transition: transform 0.2s;
    }
    .warehouse-card:hover {
        transform: translateY(-2px);
    }
    .warehouse-link {
        text-decoration: none;
        color: inherit;
    }
    .warehouse-link:hover {
        color: #007bff;
        text-decoration: none;
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
    <div class="collapse show" id="hethong">
        <a href="quan_ly_tai_khoan.php"><i class="fas fa-user-cog me-2"></i>Quản lý tài khoản</a>
        <a href="quan_ly_kho.php" style="background-color: #0056b3;"><i class="fas fa-warehouse me-2"></i>Quản lý kho</a>
        <a href="quan_ly_the.php"><i class="fas fa-id-card me-2"></i>Quản lý thẻ</a>
        <a href="nha_cung_cap.php"><i class="fas fa-truck me-2"></i>Nhà cung cấp</a>
    </div>
    <a href="sua_gioi_thieu.php"><i class="fas fa-info-circle me-2"></i>Sửa giới thiệu</a>
    <a href="sua_lien_he.php"><i class="fas fa-envelope me-2"></i>Sửa liên hệ</a>
</div>

<div class="main">
  <div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold text-primary mb-0">📦 QUẢN LÝ KHO</h4>
    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#themKhoModal">
        <i class="fas fa-plus"></i> Thêm kho mới
    </button>
</div>

    <!-- THÊM PHẦN THÔNG BÁO TỪ XU_LY_CHUYEN_TRANG_THAI.PHP -->
    <?php if (isset($_SESSION['thong_bao'])): ?>
        <div class="alert alert-<?= $_SESSION['thong_bao']['loai'] === 'error' ? 'danger' : ($_SESSION['thong_bao']['loai'] === 'warning' ? 'warning' : 'success') ?> alert-dismissible fade show" role="alert">
            <?= $_SESSION['thong_bao']['noi_dung'] ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['thong_bao']); ?>
    <?php endif; ?>
    
    <!-- Thông báo từ các thao tác trong trang này -->
    <?php if (!empty($thong_bao)): ?>
        <div class="alert alert-<?= $loai_thong_bao ?> alert-dismissible fade show">
            <?= htmlspecialchars($thong_bao) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    
    <!-- Thống kê tổng quan -->
    <div class="stats-card">
        <div class="row text-center">
            <div class="col-md-2">
                <h5 class="text-primary"><?= $stats['tong_kho'] ?></h5>
                <p class="mb-0">Tổng số kho</p>
            </div>
            <div class="col-md-2">
                <h5 class="text-info"><?= number_format($stats['tong_suc_chua']) ?></h5>
                <p class="mb-0">Tổng sức chứa</p>
            </div>
            <div class="col-md-2">
                <h5 class="text-success"><?= $stats['kho_hoat_dong'] ?></h5>
                <p class="mb-0">Kho hoạt động</p>
            </div>
            <div class="col-md-2">
                <h5 class="text-warning"><?= $stats['kho_bao_tri'] ?></h5>
                <p class="mb-0">Kho bảo trì</p>
            </div>
            <div class="col-md-2">
                <h5 class="text-secondary"><?= number_format($stats['tong_sach_trong_kho']) ?></h5>
                <p class="mb-0">Sách trong kho</p>
            </div>
            <div class="col-md-2">
                <h5 class="text-danger"><?= number_format($stats['tong_gia_tri']) ?> ₫</h5>
                <p class="mb-0">Tổng giá trị</p>
            </div>
        </div>
    </div>
    
    <!-- Danh sách kho dạng card -->
    <div class="row">
        <?php if ($ds_kho->num_rows > 0): ?>
            <?php while ($kho = $ds_kho->fetch_assoc()): ?>
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card warehouse-card h-100">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <a href="chi_tiet_kho.php?ma_kho=<?= $kho['ma_kho'] ?>" class="warehouse-link">
                                <h6 class="mb-0 fw-bold"><?= htmlspecialchars($kho['ten_kho']) ?></h6>
                            </a>
                            <span class="status-badge status-<?= $kho['trang_thai'] ?>">
                                <?= ucfirst(str_replace('_', ' ', $kho['trang_thai'])) ?>
                            </span>
                        </div>
                        <div class="card-body">
                            <p class="text-muted small mb-2">
                                <i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($kho['vi_tri']) ?>
                            </p>
                            <p class="small mb-3"><?= htmlspecialchars($kho['mo_ta']) ?></p>
                            
                            <div class="row text-center mb-3">
                                <div class="col-4">
                                    <strong class="text-primary"><?= number_format($kho['so_loai_sach']) ?></strong>
                                    <br><small>Loại sách</small>
                                </div>
                                <div class="col-4">
                                    <strong class="text-success"><?= number_format($kho['so_sach_hien_tai']) ?></strong>
                                    <br><small>Số lượng</small>
                                </div>
                                <div class="col-4">
                                    <strong class="text-warning"><?= number_format($kho['gia_tri_kho']) ?>₫</strong>
                                    <br><small>Giá trị</small>
                                </div>
                            </div>
                            
                            <!-- Progress bar -->
                            <?php 
                            $ti_le = $kho['suc_chua'] > 0 ? ($kho['so_sach_hien_tai'] / $kho['suc_chua']) * 100 : 0;
                            $color_class = $ti_le > 80 ? 'bg-danger' : ($ti_le > 60 ? 'bg-warning' : 'bg-success');
                            ?>
                            <div class="progress progress-thin mb-2">
                                <div class="progress-bar <?= $color_class ?>" style="width: <?= min($ti_le, 100) ?>%"></div>
                            </div>
                            <small class="text-muted">
                                <?= number_format($kho['so_sach_hien_tai']) ?>/<?= number_format($kho['suc_chua']) ?> 
                                (<?= number_format($ti_le, 1) ?>%)
                            </small>
                            
                            <?php if (!empty($kho['nha_cung_cap'])): ?>
                                <div class="mt-2">
                                    <small class="text-info">
                                        <i class="fas fa-truck"></i> <?= htmlspecialchars($kho['nha_cung_cap']) ?>
                                    </small>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="card-footer">
                            <div class="btn-group w-100">
                                <a href="chi_tiet_kho.php?ma_kho=<?= $kho['ma_kho'] ?>" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-eye"></i> Chi tiết
                                </a>
                                <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#suaKhoModal<?= $kho['ma_kho'] ?>">
                                    <i class="fas fa-edit"></i> Sửa
                                </button>
                                <button class="btn btn-sm btn-outline-danger" onclick="confirmDelete(<?= $kho['ma_kho'] ?>, '<?= htmlspecialchars($kho['ten_kho']) ?>')">
                                    <i class="fas fa-trash"></i> Xóa
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Modal sửa kho -->
                <div class="modal fade" id="suaKhoModal<?= $kho['ma_kho'] ?>" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Sửa thông tin kho</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <form method="POST">
                                <div class="modal-body">
                                    <input type="hidden" name="ma_kho" value="<?= $kho['ma_kho'] ?>">
                                    <div class="mb-3">
                                        <label class="form-label">Tên kho *</label>
                                        <input type="text" class="form-control" name="ten_kho" value="<?= htmlspecialchars($kho['ten_kho']) ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Vị trí *</label>
                                        <input type="text" class="form-control" name="vi_tri" value="<?= htmlspecialchars($kho['vi_tri']) ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Mô tả</label>
                                        <textarea class="form-control" name="mo_ta" rows="3"><?= htmlspecialchars($kho['mo_ta']) ?></textarea>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Sức chứa *</label>
                                                <input type="number" class="form-control" name="suc_chua" value="<?= $kho['suc_chua'] ?>" min="1" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Trạng thái *</label>
                                                <select class="form-select" name="trang_thai" required>
                                                    <option value="hoat_dong" <?= $kho['trang_thai'] == 'hoat_dong' ? 'selected' : '' ?>>Hoạt động</option>
                                                    <option value="bao_tri" <?= $kho['trang_thai'] == 'bao_tri' ? 'selected' : '' ?>>Bảo trì</option>
                                                    <option value="ngung_hoat_dong" <?= $kho['trang_thai'] == 'ngung_hoat_dong' ? 'selected' : '' ?>>Ngừng hoạt động</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                                    <button type="submit" name="cap_nhat_kho" class="btn btn-primary">Cập nhật</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="col-12">
                <div class="alert alert-info text-center">
                    <i class="fas fa-warehouse fa-3x mb-3"></i>
                    <h5>Chưa có kho nào</h5>
                    <p>Hãy thêm kho đầu tiên để bắt đầu quản lý.</p>
                </div>
            </div>
        <?php endif; ?>
    </div>
  </div>
</div>

<!-- Modal thêm kho mới -->
<div class="modal fade" id="themKhoModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Thêm kho mới</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Tên kho *</label>
                        <input type="text" class="form-control" name="ten_kho" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Vị trí *</label>
                        <input type="text" class="form-control" name="vi_tri" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Mô tả</label>
                        <textarea class="form-control" name="mo_ta" rows="3"></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Sức chứa *</label>
                                <input type="number" class="form-control" name="suc_chua" value="1000" min="1" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Trạng thái *</label>
                                <select class="form-select" name="trang_thai" required>
                                    <option value="hoat_dong">Hoạt động</option>
                                    <option value="bao_tri">Bảo trì</option>
                                    <option value="ngung_hoat_dong">Ngừng hoạt động</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" name="them_kho" class="btn btn-primary">Thêm kho</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Form ẩn để xóa -->
<form id="deleteForm" method="POST" style="display: none;">
    <input type="hidden" name="ma_kho" id="deleteId">
    <input type="hidden" name="xoa_kho" value="1">
</form>

<script>
function confirmDelete(id, ten) {
    if (confirm('Bạn có chắc chắn muốn xóa kho "' + ten + '"?\nLưu ý: Tất cả sách trong kho sẽ bị ảnh hưởng!')) {
        document.getElementById('deleteId').value = id;
        document.getElementById('deleteForm').submit();
    }
}
</script>

</body>
</html>