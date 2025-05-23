<?php
session_start();
include 'ket_noi.php';

// Kiểm tra quyền admin
if (!isset($_SESSION['ban_doc']) || $_SESSION['ban_doc']['vai_tro'] !== 'admin') {
    header('Location: dang_nhap.php');
    exit;
}

$ma_phieu = isset($_GET['id']) ? intval($_GET['id']) : 0;
$thong_bao = '';
$loai_thong_bao = '';

if ($ma_phieu > 0) {
    // Lấy thông tin phiếu mượn
    $stmt = $conn->prepare("SELECT pm.*, bd.ho_ten FROM phieu_muon pm JOIN ban_doc bd ON pm.ma_ban_doc = bd.ma_ban_doc WHERE pm.ma_phieu = ?");
    $stmt->bind_param("i", $ma_phieu);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $phieu = $result->fetch_assoc();
        
        // Xử lý khi form được submit
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['xac_nhan_tra'])) {
                // Cập nhật trạng thái phiếu mượn
                $stmt_update = $conn->prepare("UPDATE phieu_muon SET trang_thai = 'da_tra' WHERE ma_phieu = ?");
                $stmt_update->bind_param("i", $ma_phieu);
                
                if ($stmt_update->execute()) {
                    $thong_bao = "Đã xử lý trả sách thành công cho phiếu mượn #$ma_phieu";
                    $loai_thong_bao = 'success';
                    // Cập nhật lại thông tin phiếu
                    $phieu['trang_thai'] = 'da_tra';
                } else {
                    $thong_bao = "Có lỗi xảy ra khi xử lý trả sách: " . $conn->error;
                    $loai_thong_bao = 'danger';
                }
            }
        }
    } else {
        $thong_bao = "Không tìm thấy phiếu mượn với ID: $ma_phieu";
        $loai_thong_bao = 'danger';
    }
} else {
    $thong_bao = "ID phiếu mượn không hợp lệ";
    $loai_thong_bao = 'danger';
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Xử lý trả sách</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
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
        html, body {
    height: 100%;
}

body {
    display: flex;
    flex-direction: column;
}

.main {
    flex: 1;
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
<div class="container mt-5" style="margin-left: 240px; padding-top: 80px;">

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-warning text-white">
                    <h4><i class="fas fa-undo"></i> Xử lý trả sách</h4>
                </div>
                <div class="card-body">
                    <?php if (!empty($thong_bao)): ?>
                        <div class="alert alert-<?= $loai_thong_bao ?> alert-dismissible fade show">
                            <?= htmlspecialchars($thong_bao) ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <?php if (isset($phieu)): ?>
                        <div class="row">
                            <div class="col-md-6">
                                <h5>Thông tin phiếu mượn</h5>
                                <table class="table table-borderless">
                                    <tr><td><strong>Mã phiếu:</strong></td><td>#<?= $phieu['ma_phieu'] ?></td></tr>
                                    <tr><td><strong>Người mượn:</strong></td><td><?= htmlspecialchars($phieu['ho_ten']) ?></td></tr>
                                    <tr><td><strong>Ngày mượn:</strong></td><td><?= date('d/m/Y', strtotime($phieu['ngay_muon'])) ?></td></tr>
                                    <tr><td><strong>Ngày trả dự kiến:</strong></td><td><?= date('d/m/Y', strtotime($phieu['ngay_tra'])) ?></td></tr>
                                    <tr>
                                        <td><strong>Trạng thái:</strong></td>
                                        <td>
                                            <?php if ($phieu['trang_thai'] === 'da_tra'): ?>
                                                <span class="badge bg-success">Đã trả</span>
                                            <?php else: ?>
                                                <span class="badge bg-warning">Đang mượn</span>
                                                <?php if (strtotime($phieu['ngay_tra']) < time()): ?>
                                                    <span class="badge bg-danger ms-1">Quá hạn</span>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <h5>Thông tin trả sách</h5>
                                <p><strong>Ngày trả thực tế:</strong> <?= date('d/m/Y') ?></p>
                                <?php if (strtotime($phieu['ngay_tra']) < time() && $phieu['trang_thai'] !== 'da_tra'): ?>
                                    <?php $ngay_qua_han = floor((time() - strtotime($phieu['ngay_tra'])) / 86400); ?>
                                    <div class="alert alert-warning">
                                        <i class="fas fa-exclamation-triangle"></i>
                                        <strong>Trả muộn <?= $ngay_qua_han ?> ngày!</strong>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <?php if ($phieu['trang_thai'] !== 'da_tra'): ?>
                            <hr>
                            <form method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xác nhận trả sách?')">
                                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                    <a href="cho_muon.php" class="btn btn-secondary">
                                        <i class="fas fa-arrow-left"></i> Quay lại
                                    </a>
                                    <button type="submit" name="xac_nhan_tra" class="btn btn-success">
                                        <i class="fas fa-check"></i> Xác nhận trả sách
                                    </button>
                                </div>
                            </form>
                        <?php else: ?>
                            <hr>
                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                <a href="cho_muon.php" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Quay lại
                                </a>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include 'footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>