
<?php
session_start();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Nội quy thư viện</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
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
        .sidebar a {
            display: block;
            padding: 12px 20px;
            color: white;
            text-decoration: none;
        }
        .sidebar a:hover {
            background-color: #0056b3;
        }
        .sidebar .sidebar-brand {
            text-align: center;
            margin-bottom: 30px;
        }
        .sidebar .sidebar-icon i {
            font-size: 48px;
        }
        .sidebar .sidebar-brand-text {
            font-size: 30px;
            font-weight: bold;
            line-height: 1.4;
        }
        nav.fixed-top {
            left: 240px;
            width: calc(100% - 240px);
        }
        .main {
            margin-left: 240px;
            padding-top: 120px;
            padding-left: 20px;
            padding-right: 20px;
        }
        .rules-box {
            background-color: white;
            padding: 30px;
            border: 1px solid #ddd;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            max-width: 900px;
            margin: auto;
        }
        h2 {
            color: #007bff;
            font-weight: bold;
            text-align: center;
            margin-bottom: 30px;
        }
        h5 {
            margin-top: 24px;
            color: #333;
            font-weight: bold;
        }
        ul li {
            margin-bottom: 12px;
            font-size: 16px;
        }
        .footer {
            background: #f9f9f9;
            text-align: center;
            padding: 10px 0;
            font-size: 13px;
            color: #555;
            margin-top: 50px;
            margin-left: 240px;
            width: calc(100% - 240px);
            box-shadow: 0 -1px 3px rgba(0, 0, 0, 0.05);
            position: relative;   //cách dòng
        }

    </style>
</head>
<body>

<nav class="navbar navbar-light bg-white shadow-sm px-4 py-2 d-flex justify-content-end align-items-center fixed-top">
    <div class="dropdown">
        <?php if (isset($_SESSION['ban_doc'])): ?>
            <a class="dropdown-toggle text-dark text-decoration-none" href="#" id="accountDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                Xin chào, <strong><?php echo $_SESSION['ban_doc']['ten_dang_nhap']; ?></strong>
            </a>
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="accountDropdown">
                <li><a class="dropdown-item" href="thong_tin_ca_nhan.php">👤 Thông tin cá nhân</a></li>
                <li><a class="dropdown-item" href="sua_thong_tin.php">🛠️ Sửa thông tin</a></li>
                <li><a class="dropdown-item" href="lich_su.php">📖 Lịch sử mượn</a></li>
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
    <!-- Logo và tiêu đề có thể click theo điều kiện -->
    <a href="<?php echo isset($_SESSION['ban_doc']) ? 'trang_chu_ban_doc.php' : 'trang_chu.php'; ?>" 
       class="sidebar-brand d-flex align-items-center justify-content-center flex-column mt-3 mb-4 text-white text-decoration-none">
        <div class="sidebar-icon">
            <i class="fas fa-book-reader fa-3x text-white"></i>
        </div>
        <div class="sidebar-brand-text font-weight-bold mt-2 text-center" style="line-height: 1.4; font-size: 30px;">
            QUẢN LÝ<br>THƯ VIỆN
        </div>
    </a>

    <!-- Mục Trang chủ có điều kiện -->
    <a href="<?php echo isset($_SESSION['ban_doc']) ? 'trang_chu_ban_doc.php' : 'trang_chu.php'; ?>">🏠 Trang chủ</a>

    <!-- Các mục còn lại cố định -->
    <a href="gioi_thieu.php">ℹ️ Giới thiệu</a>
    <a href="lien_he.php">📞 Liên hệ</a>
    <a href="dang_nhap.php">🔐 Đăng nhập</a>
</div>




<div class="main">
    <div class="rules-box">
        <h2>📜 Nội quy Thư viện</h2>
        <h5>I. Quy định chung</h5>
        <ul>
            <li>Thư viện là nơi học tập, nghiên cứu – yêu cầu bạn đọc giữ im lặng, tôn trọng không gian chung.</li>
            <li>Chỉ người có thẻ thư viện hợp lệ mới được sử dụng dịch vụ.</li>
        </ul>

        <h5>II. Vào thư viện</h5>
        <ul>
            <li>Xuất trình thẻ thư viện khi vào và khi mượn tài liệu.</li>
            <li>Cấm mang túi, đồ ăn, thức uống và vật dễ cháy nổ vào thư viện.</li>
            <li>Điện thoại ở chế độ im lặng, không làm ồn.</li>
        </ul>

        <h5>III. Mượn - trả tài liệu</h5>
        <ul>
            <li>Được mượn tối đa 5 cuốn / 14 ngày, gia hạn 1 lần nếu chưa có người đặt.</li>
            <li>Phạt 2.000đ/ngày/cuốn nếu trả trễ. Làm hỏng/mất sách bồi thường theo quy định.</li>
        </ul>

        <h5>IV. Sử dụng thiết bị</h5>
        <ul>
            <li>Không được tự ý tháo gỡ thiết bị hoặc truy cập nội dung vi phạm.</li>
            <li>Chỉ dùng máy tính để học tập, không chơi game hay lướt web không phù hợp.</li>
        </ul>

        <h5>V. Giữ gìn tài sản</h5>
        <ul>
            <li>Không viết, vẽ lên sách, bàn ghế, không làm hư hỏng thiết bị.</li>
            <li>Vi phạm có thể bị đình chỉ sử dụng thư viện từ 1 tuần đến 1 tháng.</li>
        </ul>

        <h5>VI. Trách nhiệm</h5>
        <ul>
            <li>Chủ động theo dõi tài khoản, cập nhật thông tin cá nhân khi thay đổi.</li>
            <li>Hợp tác với cán bộ thư viện khi được yêu cầu.</li>
        </ul>

        <h5>VII. Xử lý vi phạm</h5>
        <ul>
            <li>Phạt theo mức độ vi phạm và quy định thư viện.</li>
            <li>Mọi khiếu nại cần gửi trong vòng 3 ngày kể từ khi phát sinh sự việc.</li>
        </ul>

        <p class="text-center mt-4"><strong>📌 Xin cảm ơn sự hợp tác của bạn đọc!</strong></p>
    </div>
</div>

<footer class="bg-light text-center text-muted py-3 mt-4 border-top" style="margin-left: 240px;">
    <strong>© 2025 Bản quyền thuộc về Nhóm 1 - DHMT16A1HN</strong>
</footer>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
