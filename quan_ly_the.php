<?php
session_start();
include 'ket_noi.php';

// Kiểm tra quyền admin
if (!isset($_SESSION['ban_doc']) || $_SESSION['ban_doc']['vai_tro'] !== 'admin') {
    header('Location: dang_nhap.php');
    exit;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['them_ban_doc'])) {
    $ho_ten = $_POST['ho_ten'];
    $ten_dang_nhap = $_POST['ten_dang_nhap'];
    $mat_khau = password_hash($_POST['mat_khau'], PASSWORD_DEFAULT);
    $email = $_POST['email'];
    $so_dien_thoai = $_POST['so_dien_thoai'];
    $dia_chi = $_POST['dia_chi'];
    $vai_tro = $_POST['vai_tro'];

    $stmt = $conn->prepare("INSERT INTO ban_doc (ho_ten, ten_dang_nhap, mat_khau, email, so_dien_thoai, dia_chi, vai_tro) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssss", $ho_ten, $ten_dang_nhap, $mat_khau, $email, $so_dien_thoai, $dia_chi, $vai_tro);
    $stmt->execute();
    $stmt->close();
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['gia_han_the'])) {
    $ma_ban_doc = $_POST['ma_ban_doc'];
    $stmt = $conn->prepare("UPDATE ban_doc SET ngay_het_han = DATE_ADD(ngay_het_han, INTERVAL 1 YEAR) WHERE ma_ban_doc = ?");
    $stmt->bind_param("i", $ma_ban_doc);
    $stmt->execute();
    $stmt->close();
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['khoa_mo_khoa'])) {
    $ma_ban_doc = $_POST['ma_ban_doc'];
    // Lấy trạng thái hiện tại
    $stmt = $conn->prepare("SELECT trang_thai FROM ban_doc WHERE ma_ban_doc = ?");
    $stmt->bind_param("i", $ma_ban_doc);
    $stmt->execute();
    $stmt->bind_result($trang_thai);
    $stmt->fetch();
    $stmt->close();

    // Đảo trạng thái
    $trang_thai_moi = ($trang_thai === 'khoa') ? 'mo' : 'khoa';

    $stmt = $conn->prepare("UPDATE ban_doc SET trang_thai = ? WHERE ma_ban_doc = ?");
    $stmt->bind_param("si", $trang_thai_moi, $ma_ban_doc);
    $stmt->execute();
    $stmt->close();
}

// Xử lý tìm kiếm
$tu_khoa = $_GET['tu_khoa'] ?? '';
if (!empty($tu_khoa)) {
    $sql = "SELECT * FROM ban_doc WHERE ho_ten LIKE ? OR ten_dang_nhap LIKE ? OR email LIKE ? OR so_dien_thoai LIKE ?";
    $like = '%' . $tu_khoa . '%';
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $like, $like, $like, $like);
    $stmt->execute();
    $danh_sach = $stmt->get_result();
} else {
    $danh_sach = $conn->query("SELECT * FROM ban_doc ORDER BY ma_ban_doc DESC");
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý thẻ bạn đọc</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        body, html {
            margin: 0;
            font-family: Arial, sans-serif;
            height: 100%;
            background-color: #f5f5f5;
        }
        .sidebar {
            width: 240px;
            background-color: #007bff;
            color: white;
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            padding: 20px 0;
            overflow-y: auto;
            z-index: 999;
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

        nav.fixed-top {
            left: 240px;
            width: calc(100% - 240px);
            z-index: 1001;
        }

        .main {
            margin-left: 240px;
            padding: 20px;
            padding-top: 80px;
            flex: 1;
        }

        h2.title {
            font-weight: bold;
            text-align: center;
            margin-bottom: 30px;
            color: #007bff;
        }

        .footer {
            background: #f9f9f9;
            text-align: center;
            padding: 10px 0;
            font-size: 13px;
            color: #555;
            margin-left: 240px;
            width: calc(100% - 240px);
            box-shadow: 0 -1px 3px rgba(0, 0, 0, 0.05);
        }

        .wrapper {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        
        .table-responsive {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.05);
        }
        
        .action-buttons {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
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
<!-- Nội dung chính -->
<div class="wrapper">
    <div class="main">
  <div class="container-xl">

        <h2 class="title">🆔 QUẢN LÝ THẺ BẠN ĐỌC</h2>
<!-- Form Thêm Bạn Đọc -->


       <div class="action-buttons">
    <form class="d-flex" method="GET">
        <input type="text" name="tu_khoa" class="form-control me-2" placeholder="Tìm kiếm theo tên, tên đăng nhập, email hoặc SĐT" value="<?= htmlspecialchars($tu_khoa) ?>">
        <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Tìm kiếm</button>
    </form>

    <!-- Nút hiển thị form thêm bạn đọc -->
    <button class="btn btn-success" type="button" data-bs-toggle="collapse" data-bs-target="#formThemBanDoc">
        <i class="fas fa-plus"></i> Thêm bạn đọc mới
    </button>
</div>

<!-- Form Thêm Bạn Đọc - Collapse (đặt ngay dưới action-buttons) -->
<div class="collapse mt-3" id="formThemBanDoc">
    <div class="card">
        <div class="card-body">
            <form method="POST">
                <div class="collapse mt-4" id="formThemBanDoc">
  <div class="card">
    <div class="card-header bg-success text-white">
      <h5 class="mb-0">Thêm Bạn Đọc Mới</h5>
    </div>
    <div class="card-body">
      <form method="POST">
        <div class="row">
          <div class="col-md-6 mb-3">
            <label>Họ tên</label>
            <input type="text" class="form-control" name="ho_ten" required>
          </div>
          <div class="col-md-6 mb-3">
            <label>Tên đăng nhập</label>
            <input type="text" class="form-control" name="ten_dang_nhap" required>
          </div>
          <div class="col-md-6 mb-3">
  <label>Ngày sinh</label>
  <input type="date" class="form-control" name="ngay_sinh" required>
</div>

          <div class="col-md-6 mb-3">
            <label>Mật khẩu</label>
            <input type="password" class="form-control" name="mat_khau" required>
          </div>
          <div class="col-md-6 mb-3">
            <label>Email</label>
            <input type="email" class="form-control" name="email">
          </div>
          <div class="col-md-6 mb-3">
            <label>Số điện thoại</label>
            <input type="text" class="form-control" name="so_dien_thoai">
          </div>
          <div class="col-md-6 mb-3">
            <label>Địa chỉ</label>
            <input type="text" class="form-control" name="dia_chi">
          </div>
          <div class="col-md-6 mb-3">
            <label>Vai trò</label>
            <select class="form-select" name="vai_tro" required>
              <option value="bandoc">Bạn đọc</option>
              <option value="admin">Admin</option>
            </select>
          </div>
        </div>
        <div class="text-end">
          <button type="submit" name="them_ban_doc" class="btn btn-primary"><i class="fas fa-save"></i> Lưu bạn đọc</button>
        </div>
      </form>
    </div>
  </div>
</div>
            </form>
        </div>
    </div>
</div>





        </div>

        <!-- Danh sách thẻ bạn đọc -->
        <div class="table-responsive">

            <table class="table table-striped table-hover">
                <thead class="table-primary">
                    <tr>
                        <th>Mã bạn đọc</th>
                        <th>Họ tên</th>
                        <th>Tên đăng nhập</th>
                        <th>Email</th>
                        <th>Số điện thoại</th>
                        <th>Địa chỉ</th>
                        <th>Vai trò</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (isset($danh_sach) && $danh_sach->num_rows > 0) { ?>
                        <?php while ($bd = $danh_sach->fetch_assoc()) { ?>
                            <tr>
                                <td><?= $bd['ma_ban_doc'] ?></td>
                                <td><?= $bd['ho_ten'] ?></td>
                                <td><?= $bd['ten_dang_nhap'] ?></td>
                                <td><?= $bd['email'] ?></td>
                                <td><?= $bd['so_dien_thoai'] ?></td>
                                <td><?= $bd['dia_chi'] ?></td>
                                <td>
                                    <?php if ($bd['vai_tro'] === 'admin'): ?>
                                        <span class="badge bg-danger">Admin</span>
                                    <?php else: ?>
                                        <span class="badge bg-success">Bạn đọc</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="in_the.php?id=<?= $bd['ma_ban_doc'] ?>" class="btn btn-sm btn-info mb-1" title="In thẻ"><i class="fas fa-print"></i></a>
                                    <form method="POST" style="display:inline;">
    <input type="hidden" name="ma_ban_doc" value="<?= $bd['ma_ban_doc'] ?>">
    <button type="submit" name="gia_han_the" class="btn btn-sm btn-warning mb-1" title="Gia hạn thẻ"><i class="fas fa-sync-alt"></i></button>
</form>

                                    <form method="POST" style="display:inline;">
    <input type="hidden" name="ma_ban_doc" value="<?= $bd['ma_ban_doc'] ?>">
    <button type="submit" name="khoa_mo_khoa" class="btn btn-sm btn-secondary mb-1" title="Khóa/Mở khóa thẻ"><i class="fas fa-lock"></i></button>
</form>

                                </td>
                            </tr>
                        <?php } ?>
                    <?php } else { ?>
                        <tr>
                            <td colspan="8" class="text-center">Không có dữ liệu</td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
        
        <!-- Thông tin về việc cấp thẻ -->
        <div class="card mt-4">

            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Thông tin về quản lý thẻ bạn đọc</h5>
            </div>
            <div class="card-body">
                <p><strong>Thẻ bạn đọc</strong> là tài khoản quan trọng để người dùng có thể sử dụng các dịch vụ của thư viện.</p>
                <h6>Quy trình quản lý thẻ:</h6>
                <ol>
                    <li>Bạn đọc đăng ký tài khoản trên hệ thống</li>
                    <li>Admin kiểm tra thông tin và phê duyệt</li>
                    <li>Cấp thẻ bạn đọc (in thẻ nếu cần)</li>
                    <li>Gia hạn thẻ khi cần thiết</li>
                    <li>Khóa/mở khóa thẻ tùy theo tình trạng</li>
                </ol>
                <h6>Lưu ý:</h6>
                <ul>
                    <li>Thẻ có thể bị khóa nếu vi phạm nội quy thư viện</li>
                    <li>Cần kiểm tra thông tin cá nhân của bạn đọc trước khi cấp thẻ</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <strong>© 2025 Bản quyền thuộc về Nhóm 1 - DHMT16A1HN</strong>
    </footer>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>