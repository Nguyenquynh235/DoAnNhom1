<?php
include 'ket_noi.php';

$file = fopen("sach_data.csv", "r");
$header = fgetcsv($file);

$dem = 0;
while (($data = fgetcsv($file)) !== FALSE) {
    $ten_sach = $conn->real_escape_string($data[0]);
    $the_loai = $conn->real_escape_string($data[1]);
    $anh = $conn->real_escape_string($data[2]);

    $sql = "INSERT INTO sach (ten_sach, the_loai, anh, so_luong) 
            VALUES ('$ten_sach', '$the_loai', '$anh', 5)";
    if ($conn->query($sql)) {
        $dem++;
    }
}
fclose($file);

echo "<h3>Đã thêm $dem sách thành công!</h3>";
?>