<?php
session_start();
include 'ket_noi.php';

$ma_phieu = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($ma_phieu > 0) {
    // Lấy thông tin chi tiết phiếu mượn
    $stmt = $conn->prepare("
        SELECT pm.*, bd.ho_ten, bd.ten_dang_nhap, bd.email, bd.so_dien_thoai, bd.dia_chi,
        CASE 
            WHEN pm.ngay_tra < CURDATE() AND pm.trang_thai = 'dang_muon' THEN 'qua_han'
            ELSE pm.trang_thai 
        END as trang_thai_hien_thi,
        DATEDIFF(CURDATE(), pm.ngay_tra) as so_ngay_qua_han
        FROM phieu_muon pm 
        JOIN ban_doc bd ON pm.ma_ban_doc = bd.ma_ban_doc 
        WHERE pm.ma_phieu = ?
    ");
    $stmt->bind_param("i", $ma_phieu);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $phieu = $result->fetch_assoc();
    } else {
        $error = "Không tìm thấy phiếu mượn với ID: $ma_phieu";
    }
} else {
    $error = "ID phiếu mượn không hợp lệ";
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Chi tiết phiếu mượn</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <style>
        body { font-family: Arial, sans-serif; background-color: #f5f5f5; }
        .status-badge { font-size: 14px; padding: 6px 12px; border-radius: 6px; }
        .status-dang-muon { background-color: #d4edda; color: #155724; }
        .status-da-tra { background-color: #d1ecf1; color: #0c5460; }
        .status-qua-han { background-color: #f8d7da; color: #721c24; }
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
        
        .sidebar .sidebar-brand {
    padding: 20px 10px;
    margin-top: 0; /* Không cần thêm margin-top nếu đã padding trên */
}
.navbar {
    z-index: 1100;
    margin-left: 240px; /* ✅ Thụt vào đúng bằng chiều rộng sidebar */
    width: calc(100% - 240px); /* ✅ Đảm bảo vẫn phủ ngang phần còn lại */
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
        .sidebar {
    z-index: 1000;
}
.navbar {
    z-index: 1100;
}

        body {
    margin: 0;
    font-family: Arial, sans-serif;
    background-color: #f5f5f5;
}
html, body {
    height: 100%;
}

body {
    display: flex;
    flex-direction: column;
}

.container {
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
<div class="container mt-5" style="margin-left: 240px; padding-top: 100px;">

    <div class="row justify-content-center">
        <div class="col-md-10">
            <?php if (isset($error)): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle"></i> <?= htmlspecialchars($error) ?>
                </div>
                <a href="cho_muon.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Quay lại
                </a>
            <?php else: ?>
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <h4><i class="fas fa-file-alt"></i> Chi tiết phiếu mượn #<?= $phieu['ma_phieu'] ?></h4>
                            <div>
                                <?php
                                $status_class = '';
                                $status_text = '';
                                switch($phieu['trang_thai_hien_thi']) {
                                    case 'dang_muon':
                                        $status_class = 'status-dang-muon';
                                        $status_text = 'Đang mượn';
                                        break;
                                    case 'da_tra':
                                        $status_class = 'status-da-tra';
                                        $status_text = 'Đã trả';
                                        break;
                                    case 'qua_han':
                                        $status_class = 'status-qua-han';
                                        $status_text = 'Quá hạn';
                                        break;
                                }
                                ?>
                                <span class="status-badge <?= $status_class ?>"><?= $status_text ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <!-- Thông tin phiếu mượn -->
                            <div class="col-md-6">
                                <h5><i class="fas fa-clipboard-list"></i> Thông tin phiếu mượn</h5>
                                <table class="table table-borderless">
                                    <tr>
                                        <td width="40%"><strong>Mã phiếu:</strong></td>
                                        <td>#<?= $phieu['ma_phieu'] ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Ngày mượn:</strong></td>
                                        <td><?= date('d/m/Y', strtotime($phieu['ngay_muon'])) ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Ngày trả dự kiến:</strong></td>
                                        <td><?= date('d/m/Y', strtotime($phieu['ngay_tra'])) ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Trạng thái:</strong></td>
                                        <td>
                                            <span class="status-badge <?= $status_class ?>"><?= $status_text ?></span>
                                            <?php if ($phieu['trang_thai_hien_thi'] === 'qua_han'): ?>
                                                <br><small class="text-danger">Quá hạn <?= $phieu['so_ngay_qua_han'] ?> ngày</small>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            
                            <!-- Thông tin người mượn -->
                            <div class="col-md-6">
                                <h5><i class="fas fa-user"></i> Thông tin người mượn</h5>
                                <table class="table table-borderless">
                                    <tr>
                                        <td width="40%"><strong>Họ tên:</strong></td>
                                        <td><?= htmlspecialchars($phieu['ho_ten']) ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Tên đăng nhập:</strong></td>
                                        <td><code><?= htmlspecialchars($phieu['ten_dang_nhap']) ?></code></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Email:</strong></td>
                                        <td>
                                            <?php if (!empty($phieu['email'])): ?>
                                                <a href="mailto:<?= htmlspecialchars($phieu['email']) ?>">
                                                    <?= htmlspecialchars($phieu['email']) ?>
                                                </a>
                                            <?php else: ?>
                                                <span class="text-muted">Chưa cập nhật</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Số điện thoại:</strong></td>
                                        <td>
                                            <?php if (!empty($phieu['so_dien_thoai'])): ?>
                                                <a href="tel:<?= htmlspecialchars($phieu['so_dien_thoai']) ?>">
                                                    <?= htmlspecialchars($phieu['so_dien_thoai']) ?>
                                                </a>
                                            <?php else: ?>
                                                <span class="text-muted">Chưa cập nhật</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Địa chỉ:</strong></td>
                                        <td>
                                            <?php if (!empty($phieu['dia_chi'])): ?>
                                                <?= htmlspecialchars($phieu['dia_chi']) ?>
                                            <?php else: ?>
                                                <span class="text-muted">Chưa cập nhật</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        <!-- Thông báo và cảnh báo -->
                        <?php if ($phieu['trang_thai_hien_thi'] === 'qua_han'): ?>
                            <div class="alert alert-danger mt-3">
                                <i class="fas fa-exclamation-triangle"></i>
                                <strong>Cảnh báo:</strong> Phiếu mượn này đã quá hạn <?= $phieu['so_ngay_qua_han'] ?> ngày!
                                Vui lòng liên hệ người mượn để xử lý.
                            </div>
                        <?php endif; ?>

                        <!-- Nút thao tác -->
                        <hr>
                        <div class="d-flex justify-content-between">
                            <a href="cho_muon.php" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Quay lại danh sách
                            </a>
                            <div>
                                <?php if ($phieu['trang_thai'] === 'dang_muon'): ?>
                                    <a href="tra_sach.php?id=<?= $phieu['ma_phieu'] ?>" class="btn btn-warning me-2">
                                        <i class="fas fa-undo"></i> Xử lý trả sách
                                    </a>
                                <?php endif; ?>
                                <a href="in_phieu.php?id=<?= $phieu['ma_phieu'] ?>" class="btn btn-info" target="_blank">
                                    <i class="fas fa-print"></i> In phiếu
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php include 'footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>