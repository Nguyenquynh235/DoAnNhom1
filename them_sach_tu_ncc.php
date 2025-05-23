<?php
session_start();
include 'ket_noi.php';

// Kiểm tra quyền admin
if (!isset($_SESSION['ban_doc']) || $_SESSION['ban_doc']['vai_tro'] !== 'admin') {
    header('Location: dang_nhap.php');
    exit;
}

date_default_timezone_set('Asia/Ho_Chi_Minh');

// Xử lý thêm sách từ nhà cung cấp
$thong_bao = '';
$loai_thong_bao = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['them_sach'])) {
    $ten_sach = $_POST['ten_sach'];
    $tac_gia = $_POST['tac_gia'];
    $the_loai = $_POST['the_loai'];
    $mo_ta = $_POST['mo_ta'];
    $so_luong = $_POST['so_luong'];
    $ma_ncc = $_POST['ma_ncc'];
    $gia_nhap = $_POST['gia_nhap'];
    $ma_kho = isset($_POST['ma_kho']) ? $_POST['ma_kho'] : null;
    
    // VALIDATION BẮT BUỘC CHỌN KHO VÀ NCC
    if (empty($ma_kho)) {
        $thong_bao = "❌ Vui lòng chọn kho lưu trữ để thêm sách!";
        $loai_thong_bao = 'danger';
    } elseif (empty($ma_ncc)) {
        $thong_bao = "❌ Vui lòng chọn nhà cung cấp!";
        $loai_thong_bao = 'danger';
    } else {
        // Thêm sách vào database với trạng thái "trong_kho"
        $stmt = $conn->prepare("INSERT INTO sach (ten_sach, tac_gia, the_loai, mo_ta, so_luong, ma_ncc, gia_nhap, ma_kho, ngay_nhap, trang_thai_sach) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW(), 'trong_kho')");
        $stmt->bind_param("ssssidii", $ten_sach, $tac_gia, $the_loai, $mo_ta, $so_luong, $ma_ncc, $gia_nhap, $ma_kho);
        
        if ($stmt->execute()) {
            $thong_bao = "✅ Thêm sách vào kho thành công! Sách đang ở trạng thái 'Trong kho' - chưa hiển thị ở trang chủ.";
            $loai_thong_bao = 'success';
            
            // Reset form sau khi thêm thành công
            $_POST = array();
        } else {
            $thong_bao = "❌ Lỗi: " . $conn->error;
            $loai_thong_bao = 'danger';
        }
    }
}

// Lấy nhà cung cấp được chọn từ URL
$ncc_selected = isset($_GET['ncc']) ? intval($_GET['ncc']) : 0;

// Lấy danh sách nhà cung cấp hoạt động
$ds_ncc = $conn->query("SELECT * FROM nha_cung_cap WHERE trang_thai = 'hoat_dong' ORDER BY ten_ncc ASC");

// Lấy danh sách kho
$ds_kho = $conn->query("SELECT * FROM kho ORDER BY ten_kho ASC");

// Lấy danh sách sách đã nhập gần đây từ nhà cung cấp (chỉ sách trong kho)
$ds_sach_gan_day = $conn->query("
    SELECT s.*, ncc.ten_ncc, k.ten_kho 
    FROM sach s 
    LEFT JOIN nha_cung_cap ncc ON s.ma_ncc = ncc.ma_ncc 
    LEFT JOIN kho k ON s.ma_kho = k.ma_kho 
    WHERE s.ma_ncc IS NOT NULL AND s.trang_thai_sach = 'trong_kho'
    ORDER BY s.ngay_nhap DESC 
    LIMIT 10
");

// Thống kê sách theo nhà cung cấp
$sql_stats = "SELECT 
    ncc.ten_ncc,
    COUNT(s.ma_sach) as so_sach,
    SUM(s.so_luong) as tong_so_luong,
    AVG(s.gia_nhap) as gia_trung_binh
    FROM nha_cung_cap ncc 
    LEFT JOIN sach s ON ncc.ma_ncc = s.ma_ncc 
    WHERE ncc.trang_thai = 'hoat_dong'
    GROUP BY ncc.ma_ncc, ncc.ten_ncc
    ORDER BY so_sach DESC
    LIMIT 5";
$stats_ncc = $conn->query($sql_stats);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Thêm sách từ nhà cung cấp</title>
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
    .form-card {
        background: white;
        border-radius: 8px;
        padding: 25px;
        margin-bottom: 20px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    .stats-card {
        background: white;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 20px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    .recent-books {
        background: white;
        border-radius: 8px;
        padding: 20px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    .book-item {
        border-left: 4px solid #007bff;
        padding: 10px 15px;
        margin-bottom: 10px;
        background-color: #f8f9fa;
        border-radius: 0 4px 4px 0;
        position: relative;
    }
    .status-badge {
        position: absolute;
        top: 5px;
        right: 5px;
        font-size: 10px;
    }
  </style>
</head>
<body>
<nav class="navbar navbar-light bg-white shadow-sm fixed-top px-4 py-2 d-flex justify-content-between">
  <span class="fw-bold">📦 THÊM SÁCH VÀO KHO</span>
  <div>
    <a href="quan_ly_kho.php" class="btn btn-warning btn-sm me-2">
      <i class="fas fa-warehouse"></i> Quản lý kho
    </a>
    <a href="nha_cung_cap.php" class="btn btn-secondary btn-sm me-2">
      <i class="fas fa-truck"></i> Quản lý NCC
    </a>
    <a href="quan_ly_sach.php" class="btn btn-primary btn-sm">
      <i class="fas fa-book"></i> Quản lý sách
    </a>
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
            <i class="fas fa-info-circle"></i> <?= htmlspecialchars($thong_bao) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Info box -->
    <div class="alert alert-info">
        <h6><i class="fas fa-lightbulb"></i> <strong>Lưu ý quan trọng:</strong></h6>
        <p class="mb-0">Sách được thêm từ nhà cung cấp sẽ có trạng thái <span class="badge bg-warning text-dark">TRONG KHO</span> và <strong>chưa hiển thị ở trang chủ</strong>. 
        Để sách hiển thị cho bạn đọc mượn, hãy vào <a href="quan_ly_kho.php" class="text-decoration-none"><i class="fas fa-warehouse"></i> Quản lý kho</a> để chuyển trạng thái sang <span class="badge bg-success">CÓ THỂ MƯỢN</span>.</p>
    </div>

    <div class="row">
        <!-- Form thêm sách -->
        <div class="col-lg-8">
            <div class="form-card">
                <h5 class="text-primary mb-4">
                    <i class="fas fa-plus-circle"></i> Thêm sách mới vào kho từ nhà cung cấp
                </h5>
                
                <form method="POST">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label class="form-label">Tên sách *</label>
                                <input type="text" class="form-control" name="ten_sach" required value="<?= isset($_POST['ten_sach']) ? htmlspecialchars($_POST['ten_sach']) : '' ?>">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Số lượng *</label>
                                <input type="number" class="form-control" name="so_luong" min="1" value="<?= isset($_POST['so_luong']) ? $_POST['so_luong'] : '1' ?>" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Tác giả</label>
                                <input type="text" class="form-control" name="tac_gia" value="<?= isset($_POST['tac_gia']) ? htmlspecialchars($_POST['tac_gia']) : '' ?>">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Thể loại</label>
                                <select class="form-select" name="the_loai">
                                    <option value="">Chọn thể loại</option>
                                    <option value="Khoa học tự nhiên" <?= (isset($_POST['the_loai']) && $_POST['the_loai'] == 'Khoa học tự nhiên') ? 'selected' : '' ?>>Khoa học tự nhiên</option>
                                    <option value="Khoa học xã hội" <?= (isset($_POST['the_loai']) && $_POST['the_loai'] == 'Khoa học xã hội') ? 'selected' : '' ?>>Khoa học xã hội</option>
                                    <option value="Công nghệ thông tin" <?= (isset($_POST['the_loai']) && $_POST['the_loai'] == 'Công nghệ thông tin') ? 'selected' : '' ?>>Công nghệ thông tin</option>
                                    <option value="Văn học" <?= (isset($_POST['the_loai']) && $_POST['the_loai'] == 'Văn học') ? 'selected' : '' ?>>Văn học</option>
                                    <option value="Ngoại ngữ" <?= (isset($_POST['the_loai']) && $_POST['the_loai'] == 'Ngoại ngữ') ? 'selected' : '' ?>>Ngoại ngữ</option>
                                    <option value="Kinh tế" <?= (isset($_POST['the_loai']) && $_POST['the_loai'] == 'Kinh tế') ? 'selected' : '' ?>>Kinh tế</option>
                                    <option value="Y học" <?= (isset($_POST['the_loai']) && $_POST['the_loai'] == 'Y học') ? 'selected' : '' ?>>Y học</option>
                                    <option value="Giáo dục" <?= (isset($_POST['the_loai']) && $_POST['the_loai'] == 'Giáo dục') ? 'selected' : '' ?>>Giáo dục</option>
                                    <option value="Khác" <?= (isset($_POST['the_loai']) && $_POST['the_loai'] == 'Khác') ? 'selected' : '' ?>>Khác</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Nhà cung cấp *</label>
                                <select class="form-select" name="ma_ncc" required>
                                    <option value="">Chọn nhà cung cấp</option>
                                    <?php 
                                    $ds_ncc->data_seek(0);
                                    while ($ncc = $ds_ncc->fetch_assoc()) { ?>
                                        <option value="<?= $ncc['ma_ncc'] ?>" <?= (isset($_POST['ma_ncc']) && $_POST['ma_ncc'] == $ncc['ma_ncc']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($ncc['ten_ncc']) ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Kho lưu trữ *</label>
                                <select class="form-select" name="ma_kho" required>
                                    <option value="">Chọn kho lưu trữ</option>
                                    <?php 
                                    $ds_kho->data_seek(0);
                                    while ($kho = $ds_kho->fetch_assoc()) { ?>
                                        <option value="<?= $kho['ma_kho'] ?>" <?= (isset($_POST['ma_kho']) && $_POST['ma_kho'] == $kho['ma_kho']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($kho['ten_kho']) ?>
                                        </option>
                                    <?php } ?>
                                </select>
                                <div class="form-text">Sách sẽ được lưu vào kho này với trạng thái "Trong kho"</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Giá nhập</label>
                                <input type="number" class="form-control" name="gia_nhap" step="0.01" min="0" value="<?= isset($_POST['gia_nhap']) ? $_POST['gia_nhap'] : '' ?>">
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Mô tả</label>
                        <textarea class="form-control" name="mo_ta" rows="3"><?= isset($_POST['mo_ta']) ? htmlspecialchars($_POST['mo_ta']) : '' ?></textarea>
                    </div>
                    
                    <div class="d-flex gap-2">
                        <button type="submit" name="them_sach" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Thêm vào kho
                        </button>
                        <button type="reset" class="btn btn-secondary">
                            <i class="fas fa-undo"></i> Làm mới
                        </button>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Sidebar thống kê -->
        <div class="col-lg-4">
            <!-- Thống kê nhà cung cấp -->
            <div class="stats-card">
                <h6 class="text-secondary mb-3">
                    <i class="fas fa-chart-pie"></i> Thống kê theo NCC
                </h6>
                <?php while ($stat = $stats_ncc->fetch_assoc()) { ?>
                    <div class="d-flex justify-content-between align-items-center mb-2 p-2 bg-light rounded">
                        <div>
                            <strong><?= htmlspecialchars($stat['ten_ncc']) ?></strong><br>
                            <small class="text-muted"><?= $stat['so_sach'] ?> loại sách</small>
                        </div>
                        <div class="text-end">
                            <div class="text-primary fw-bold"><?= number_format($stat['tong_so_luong'] ?? 0) ?></div>
                            <small class="text-muted">cuốn</small>
                        </div>
                    </div>
                <?php } ?>
            </div>
            
            <!-- Sách nhập gần đây -->
            <div class="recent-books">
                <h6 class="text-secondary mb-3">
                    <i class="fas fa-clock"></i> Sách vừa nhập kho
                </h6>
                <?php if ($ds_sach_gan_day->num_rows > 0) { ?>
                    <?php while ($sach = $ds_sach_gan_day->fetch_assoc()) { ?>
                        <div class="book-item">
                            <span class="badge bg-warning text-dark status-badge">TRONG KHO</span>
                            <strong><?= htmlspecialchars($sach['ten_sach']) ?></strong><br>
                            <small class="text-muted">
                                <i class="fas fa-user"></i> <?= htmlspecialchars($sach['tac_gia'] ?? 'Chưa rõ') ?><br>
                                <i class="fas fa-warehouse"></i> <?= htmlspecialchars($sach['ten_kho'] ?? 'Chưa rõ') ?><br>
                                <i class="fas fa-truck"></i> <?= htmlspecialchars($sach['ten_ncc'] ?? 'Chưa rõ') ?><br>
                                <i class="fas fa-calendar"></i> <?= date('d/m/Y H:i', strtotime($sach['ngay_nhap'])) ?>
                            </small>
                        </div>
                    <?php } ?>
                <?php } else { ?>
                    <p class="text-muted text-center">Chưa có sách nào trong kho</p>
                <?php } ?>
            </div>
        </div>
    </div>
    
  </div>
</div>

<?php include 'footer.php'; ?>
</body>
</html>