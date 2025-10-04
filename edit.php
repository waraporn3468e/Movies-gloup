<?php
include 'connect.php';

// ดึงข้อมูลหนังจาก id
if (!isset($_GET['id'])) {
    die("ไม่พบ ID หนัง");
}
$id = intval($_GET['id']);
$result = $conn->query("SELECT * FROM movies WHERE id = $id");
$movie = $result->fetch_assoc();
if (!$movie) {
    die("ไม่พบหนังในฐานข้อมูล");
}

// เมื่อกดบันทึกการแก้ไข
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $conn->real_escape_string($_POST["title"]);
    $year = intval($_POST["year"]);
    $desc = $conn->real_escape_string($_POST["description"]);

    // ถ้ามีการอัปโหลดรูปใหม่
    $image = $movie["image"];
    if (!empty($_FILES["image"]["name"])) {
        $targetDir = "uploads/";
        if (!is_dir($targetDir)) mkdir($targetDir);
        $newImage = time() . "_" . basename($_FILES["image"]["name"]);
        $targetFile = $targetDir . $newImage;
        move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile);

        // ลบไฟล์เก่าออก
        if (!empty($image) && file_exists("uploads/".$image)) {
            unlink("uploads/".$image);
        }
        $image = $newImage;
    }

    // อัปเดตข้อมูล
    $conn->query("UPDATE movies 
                  SET title='$title', year=$year, description='$desc', image='$image' 
                  WHERE id=$id");

    $conn->query("INSERT INTO logs (action) VALUES ('Edited movie: $title')");
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>แก้ไขหนัง</title>
    <style>
        body { background:#0f0f0f; color:#f2f2f2; font-family:sans-serif; }
        form { background:#1c1c1c; padding:20px; border-radius:10px; width:400px; margin:30px auto; }
        input, textarea { width:100%; margin-bottom:15px; padding:10px; border:none; border-radius:5px; background:#222; color:#fff; }
        input[type=submit] { background:#8ab4f8; color:#000; font-weight:bold; cursor:pointer; }
        input[type=submit]:hover { background:#5f8dd3; }
    </style>
</head>
<body>
    <h2 style="text-align:center">✏️ แก้ไขหนัง</h2>
    <form method="post" enctype="multipart/form-data">
        <input type="text" name="title" value="<?= htmlspecialchars($movie['title']) ?>" required>
        <input type="number" name="year" value="<?= htmlspecialchars($movie['year']) ?>" min="1900" max="2100" required>
        <textarea name="description" rows="3"><?= htmlspecialchars($movie['description']) ?></textarea>
        <p>โปสเตอร์ปัจจุบัน:</p>
        <?php if(!empty($movie['image'])): ?>
            <img src="uploads/<?= $movie['image'] ?>" alt="" width="150"><br>
        <?php endif; ?>
        <input type="file" name="image" accept="image/*">
        <input type="submit" value="บันทึกการแก้ไข">
    </form>
</body>
</html>
