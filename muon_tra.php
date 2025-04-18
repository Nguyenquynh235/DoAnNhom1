<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: auth/login.php");
    exit;
}
include 'db.php';

// Mượn sách
if (isset($_GET['muon'])) {
    $book_id = intval($_GET['muon']);
    $user_id = $_SESSION['user_id'];
    $stmt = $conn->prepare("INSERT INTO borrowings (user_id, book_id, borrow_date, status) VALUES (?, ?, CURDATE(), 'borrowed')");
    $stmt->bind_param("ii", $user_id, $book_id);
    $stmt->execute();
    header("Location: muon_tra.php");
    exit;
}

// Trả sách
if (isset($_GET['tra'])) {
    $id = intval($_GET['tra']);
    $stmt = $conn->prepare("UPDATE borrowings SET status='returned', return_date=CURDATE() WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header("Location: muon_tra.php");
    exit;
}

// Lấy danh sách sách đã mượn
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT b.id AS borrow_id, k.title, k.author, k.cover_image, b.borrow_date, b.return_date, b.status
                        FROM borrowings b
                        JOIN books k ON b.book_id = k.id
                        WHERE b.user_id = ? ORDER BY b.borrow_date DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Mượn / Trả sách</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <h3 class="text-center text-success">📚 Lịch sử mượn / trả sách</h3>
    <table class="table table-bordered table-striped mt-4">
        <thead class="table-primary">
            <tr>
                <th>Tên sách</th>
                <th>Tác giả</th>
                <th>Ngày mượn</th>
                <th>Ngày trả</th>
                <th>Trạng thái</th>
                <th>Thao tác</th>
            </tr>
        </thead>
        <tbody>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= $row['title'] ?></td>
                <td><?= $row['author'] ?></td>
                <td><?= $row['borrow_date'] ?></td>
                <td><?= $row['return_date'] ?? '-' ?></td>
                <td><?= $row['status'] === 'borrowed' ? 'Đang mượn' : 'Đã trả' ?></td>
                <td>
                    <?php if ($row['status'] === 'borrowed'): ?>
                        <a href="muon_tra.php?tra=<?= $row['borrow_id'] ?>" class="btn btn-warning btn-sm">Trả sách</a>
                    <?php else: ?>
                        <span class="text-muted">✔</span>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
    <div class="text-center">
        <a href="index.php" class="btn btn-secondary">⬅ Quay lại trang chính</a>
    </div>
</div>
</body>
</html>
