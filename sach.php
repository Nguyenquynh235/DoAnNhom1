<?php include 'includes/header.php'; ?>
<div class="container mt-4">
    <h3 class="text-center text-success">📘 Danh sách Sách</h3>
    <form method="POST" enctype="multipart/form-data" class="mb-4">
        <div class="mb-2">
            <label class="form-label">Tên sách</label>
            <input type="text" name="ten" class="form-control" required>
        </div>
        <div class="mb-2">
            <label class="form-label">Tác giả</label>
            <input type="text" name="tacgia" class="form-control" required>
        </div>
        <div class="mb-2">
            <label class="form-label">Ảnh bìa</label>
            <input type="file" name="anh" class="form-control" required>
        </div>
        <button class="btn btn-primary" type="submit">Thêm sách</button>
    </form>
    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $ten = $_POST["ten"];
        $tacgia = $_POST["tacgia"];
        $target = "uploads/" . basename($_FILES["anh"]["name"]);
        move_uploaded_file($_FILES["anh"]["tmp_name"], $target);
        echo "<div class='alert alert-success'>Đã thêm sách: <strong>$ten</strong> - $tacgia</div>";
        echo "<img src='$target' height='200'>";
    }
    ?>
</div>
<?php include 'includes/footer.php'; ?>
