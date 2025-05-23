<?php
include 'ket_noi.php';

$ho_ten = $_POST['ho_ten'];
$ten_dang_nhap = $_POST['ten_dang_nhap'];
$ngay_sinh_input = $_POST['ngay_sinh']; // dd/mm/yyyy
$email = $_POST['email'];
$mat_khau = $_POST['mat_khau'];
$nhap_lai_mat_khau = $_POST['nhap_lai_mat_khau'];
$so_dien_thoai = $_POST['so_dien_thoai'];
$dia_chi = $_POST['dia_chi'];

// Mã hóa mật khẩu
$mat_khau_ma_hoa = password_hash($mat_khau, PASSWORD_DEFAULT);

// Chuyển đổi ngày sinh thành yyyy-mm-dd
$parts = explode('/', $ngay_sinh_input); // dd/mm/yyyy
$ngay_sinh = $parts[2] . '-' . $parts[1] . '-' . $parts[0]; // yyyy-mm-dd

// Insert
$stmt = $conn->prepare("INSERT INTO ban_doc (ten_dang_nhap, mat_khau, ho_ten, ngay_sinh, email, so_dien_thoai, dia_chi) VALUES (?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("sssssss", $ten_dang_nhap, $mat_khau_ma_hoa, $ho_ten, $ngay_sinh, $email, $so_dien_thoai, $dia_chi);

if ($stmt->execute()) {
    // ✅ Chuyển đến trang đăng nhập khi đăng ký thành công
    header("Location: dang_nhap.php?success=1");
    exit();
} else {
    echo "Đăng ký thất bại: " . $stmt->error;
}
?>
