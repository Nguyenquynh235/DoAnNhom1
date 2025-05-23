<?php
session_start();
include 'ket_noi.php';

// Kiểm tra quyền admin
if (!isset($_SESSION['ban_doc']) || $_SESSION['ban_doc']['vai_tro'] !== 'admin') {
    header('Location: dang_nhap.php');
    exit;
}

// Kiểm tra method POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: quan_ly_kho.php');
    exit;
}

// Kiểm tra có ma_sach không
if (!isset($_POST['ma_sach']) || empty($_POST['ma_sach'])) {
    $_SESSION['thong_bao'] = [
        'loai' => 'error',
        'noi_dung' => 'Không tìm thấy mã sách!'
    ];
    header('Location: quan_ly_kho.php');
    exit;
}

$ma_sach = intval($_POST['ma_sach']);

try {
    // Kiểm tra sách có tồn tại và đang trong kho không
    $check_sql = "SELECT ten_sach, trang_thai_sach FROM sach WHERE ma_sach = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("i", $ma_sach);
    $check_stmt->execute();
    $result = $check_stmt->get_result();
    
    if ($result->num_rows === 0) {
        $_SESSION['thong_bao'] = [
            'loai' => 'error',
            'noi_dung' => 'Không tìm thấy sách với mã ' . $ma_sach
        ];
        header('Location: quan_ly_kho.php');
        exit;
    }
    
    $sach = $result->fetch_assoc();
    
    if ($sach['trang_thai_sach'] !== 'trong_kho') {
        $_SESSION['thong_bao'] = [
            'loai' => 'warning',
            'noi_dung' => 'Sách "' . $sach['ten_sach'] . '" không ở trong kho!'
        ];
        header('Location: quan_ly_kho.php');
        exit;
    }
    
    // Cập nhật trạng thái sách
    $update_sql = "UPDATE sach SET trang_thai_sach = 'co_the_muon' WHERE ma_sach = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("i", $ma_sach);
    
    if ($update_stmt->execute()) {
        $_SESSION['thong_bao'] = [
            'loai' => 'success',
            'noi_dung' => '✅ Đã xuất sách "' . $sach['ten_sach'] . '" ra khỏi kho thành công!'
        ];
        
        // Cập nhật số thông báo (giảm đi 1)
        if (isset($_SESSION['so_thong_bao']) && $_SESSION['so_thong_bao'] > 0) {
            $_SESSION['so_thong_bao']--;
        }
    } else {
        $_SESSION['thong_bao'] = [
            'loai' => 'error',
            'noi_dung' => 'Lỗi khi xuất sách: ' . $conn->error
        ];
    }
    
} catch (Exception $e) {
    $_SESSION['thong_bao'] = [
        'loai' => 'error',
        'noi_dung' => 'Lỗi hệ thống: ' . $e->getMessage()
    ];
}

// Redirect về trang quản lý kho
header('Location: quan_ly_kho.php');
exit;
?>