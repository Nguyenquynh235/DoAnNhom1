<?php
include 'ket_noi.php';

echo "<!DOCTYPE html>
<html lang='vi'>
<head>
    <meta charset='UTF-8'>
    <title>Debug Danh sách Kho</title>
    <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css' rel='stylesheet'>
</head>
<body>
<div class='container mt-5'>
    <div class='card'>
        <div class='card-header'>
            <h3>Debug Danh sách Kho</h3>
        </div>
        <div class='card-body'>";

// Kiểm tra bảng kho
$result = $conn->query("SELECT * FROM kho ORDER BY ten_kho ASC");
if ($result) {
    echo "<h5>Tất cả kho trong database:</h5>";
    echo "<table class='table table-striped'>";
    echo "<thead><tr><th>Mã kho</th><th>Tên kho</th><th>Vị trí</th><th>Sức chứa</th><th>Trạng thái</th></tr></thead>";
    echo "<tbody>";
    
    while ($kho = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $kho['ma_kho'] . "</td>";
        echo "<td>" . htmlspecialchars($kho['ten_kho']) . "</td>";
        echo "<td>" . htmlspecialchars($kho['vi_tri']) . "</td>";
        echo "<td>" . $kho['suc_chua'] . "</td>";
        echo "<td>" . (isset($kho['trang_thai']) ? $kho['trang_thai'] : 'Không có') . "</td>";
        echo "</tr>";
    }
    echo "</tbody></table>";
    
    // Tạo dropdown mẫu
    echo "<h5 class='mt-4'>Dropdown kho như sẽ hiển thị:</h5>";
    echo "<select class='form-select'>";
    echo "<option value=''>Chọn kho</option>";
    
    $result->data_seek(0); // Reset pointer
    while ($kho = $result->fetch_assoc()) {
        echo "<option value='" . $kho['ma_kho'] . "'>";
        echo htmlspecialchars($kho['ten_kho']) . " (" . htmlspecialchars($kho['vi_tri']) . ")";
        echo "</option>";
    }
    echo "</select>";
    
} else {
    echo "<div class='alert alert-danger'>Lỗi query: " . $conn->error . "</div>";
}

echo "</div>
        <div class='card-footer'>
            <a href='them_sach_tu_ncc.php' class='btn btn-primary'>Về trang thêm sách</a>
        </div>
    </div>
</div>
</body>
</html>";
?>