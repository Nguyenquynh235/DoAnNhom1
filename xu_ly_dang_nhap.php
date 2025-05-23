<?php
session_start();
include 'ket_noi.php';

if (isset($_POST['ten_dang_nhap']) && isset($_POST['mat_khau'])) {
    $ten_dang_nhap = $_POST['ten_dang_nhap'];
    $mat_khau = $_POST['mat_khau'];

    $sql = "SELECT * FROM ban_doc WHERE ten_dang_nhap = '$ten_dang_nhap'";
    $res = mysqli_query($conn, $sql);
    $ban_doc = mysqli_fetch_assoc($res);

    if ($ban_doc && password_verify($mat_khau, $ban_doc['mat_khau'])) {
        $_SESSION['ban_doc'] = $ban_doc;
        $_SESSION['vai_tro'] = $ban_doc['vai_tro'];

        if ($ban_doc['vai_tro'] === 'admin') {
            header("Location: trang_chu_admin.php");
        } else {
            header("Location: trang_chu_ban_doc.php");
        }
        exit;
    } else {
        header("Location: dang_nhap.php?error=1");
        exit;
    }
}
?>
