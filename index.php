<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thư viện UNETI - Trang chủ</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #f0f6ff;
        }
        .hero {
            background: linear-gradient(to right, #0056b3, #007bff);
            color: white;
            padding: 80px 20px;
            text-align: center;
        }
        .hero h1 {
            font-size: 48px;
            font-weight: bold;
        }
        .hero p {
            font-size: 20px;
        }
        .hero .btn {
            font-size: 18px;
            margin: 10px;
        }
        .features {
            padding: 40px 20px;
        }
        .features h2 {
            text-align: center;
            margin-bottom: 30px;
            color: #0056b3;
        }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
  <div class="container-fluid">
    <span class="navbar-brand fw-bold">📚 Thư viện UNETI</span>
    <div class="d-flex">
      <a href="auth/login.php" class="btn btn-light me-2">Đăng nhập</a>
      <a href="auth/register.php" class="btn btn-outline-light">Đăng ký</a>
    </div>
  </div>
</nav>

<section class="hero">
    <h1>Chào mừng đến với Thư viện UNETI</h1>
    <p>Tra cứu sách, mượn sách, quản lý thông tin nhanh chóng, thuận tiện</p>
    <a href="auth/login.php" class="btn btn-light">Đăng nhập để bắt đầu</a>
</section>

<section class="features container">
    <h2>Chức năng nổi bật</h2>
    <div class="row text-center">
        <div class="col-md-4">
            <h4>📘 Tìm kiếm sách</h4>
            <p>Dễ dàng tra cứu tài liệu, sách và thông tin mượn.</p>
        </div>
        <div class="col-md-4">
            <h4>🔁 Mượn / Trả sách</h4>
            <p>Quản lý phiếu mượn và theo dõi lịch sử trả sách.</p>
        </div>
        <div class="col-md-4">
            <h4>📊 Quản lý cho thủ thư</h4>
            <p>Quản trị kho sách, bạn đọc, thống kê mượn trả.</p>
        </div>
    </div>
</section>

<footer class="text-center mt-5 p-4 bg-primary text-white">
    &copy; 2025 Thư viện UNETI. Phát triển bởi nhóm sinh viên.
</footer>
</body>
</html>
