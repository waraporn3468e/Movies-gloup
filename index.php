<?php
include 'connect.php'; // ดึงการเชื่อมต่อจาก connect.php

// เพิ่มหนัง
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["title"])) {
    $title = $conn->real_escape_string($_POST["title"]);
    $desc = $conn->real_escape_string($_POST["description"]);
    $conn->query("INSERT INTO movies (title, description) VALUES ('$title', '$desc')");
    $conn->query("INSERT INTO logs (action) VALUES ('Added movie: $title')");
    header("Location: index.php");
    exit();
}

// ลบหนัง
if (isset($_GET["delete"])) {
    $id = intval($_GET["delete"]);
    $res = $conn->query("SELECT title FROM movies WHERE id = $id");
    $title = $res->fetch_assoc()["title"] ?? '';
    $conn->query("DELETE FROM movies WHERE id = $id");
    $conn->query("INSERT INTO logs (action) VALUES ('Deleted movie: $title')");
    header("Location: index.php");
    exit();
}

// ดึงรายการหนัง
$movies = $conn->query("SELECT * FROM movies ORDER BY added_at DESC");

// ดึง log ล่าสุด
$logs = $conn->query("SELECT * FROM logs ORDER BY log_time DESC LIMIT 10");
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>🔮THE MOVIE</title>
    <style>
        body {
            background-color: #0f0f0f;
            color: #e0e0e0;
            font-family: 'Courier New', monospace;
        }
        header {
            background-color: #111;
            padding: 20px;
            text-align: center;
            font-size: 32px;
            color: #8ab4f8;
            box-shadow: 0 0 20px #8ab4f8;
        }
        .container {
            max-width: 800px;
            margin: auto;
            padding: 20px;
        }
        form {
            background-color: #1c1c1c;
            padding: 15px;
            margin-bottom: 30px;
            border-radius: 5px;
            box-shadow: 0 0 10px #333;
        }
        input, textarea {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
            background: #333;
            color: #fff;
            border: none;
            border-bottom: 2px solid #8ab4f8;
        }
        input[type="submit"] {
            background-color: #8ab4f8;
            color: #000;
            font-weight: bold;
            cursor: pointer;
        }
        .movie {
            background-color: #1a1a1a;
            margin-bottom: 15px;
            padding: 15px;
            border-left: 4px solid #8ab4f8;
            position: relative;
        }
        .delete-btn {
            position: absolute;
            top: 10px;
            right: 10px;
            background: #f06262;
            color: #000;
            padding: 5px 10px;
            text-decoration: none;
            border-radius: 3px;
        }
        .logs {
            background-color: #121212;
            padding: 10px;
            font-size: 14px;
            color: #aaa;
            border-radius: 5px;
        }
    </style>

</head>
<body>

    <h1>🔮THE MOVIES</h1>
    <p>สำรวจรายชื่อหนัง เพิ่มของคุณเอง และติดตามกิจกรรมย้อนหลัง</p>

    <!-- ✅ ใส่ปุ่มตรงนี้ -->
    <a href="movies.php" class="button">หน้าแนะนำหนัง</a>

    <header>🔮 แนะนำภาพยนตร์หรือหนังของคุณ</header>
    
    <div class="container">

        <h2>➕เพิ่มหนัง</h2>
        <form method="post">
            <input type="text" name="title" placeholder="ชื่อหนัง" required>
            <textarea name="description" placeholder="คำอธิบาย..." rows="3"></textarea>
            <input type="submit" value="เพิ่มหนัง">
        </form>

        <h2>📽 รายชื่อหนัง</h2>
        <?php while($row = $movies->fetch_assoc()): ?>
            <div class="movie">
                <strong><?= htmlspecialchars($row['title']) ?></strong><br>
                <small><?= nl2br(htmlspecialchars($row['description'])) ?></small><br>
                <small><i>เพิ่มเมื่อ: <?= $row['added_at'] ?></i></small>
                <a class="delete-btn" href="?delete=<?= $row['id'] ?>" onclick="return confirm('ลบหนังเรื่องนี้?')">ลบ</a>
            </div>
        <?php endwhile; ?>

        <h2>📜 บันทึกกิจกรรมล่าสุด</h2>
        <div class="logs">
            <?php while($log = $logs->fetch_assoc()): ?>
                <div>🕒 <?= $log['log_time'] ?> - <?= htmlspecialchars($log['action']) ?></div>
            <?php endwhile; ?>
        </div>

    </div>
</body>
</html>
