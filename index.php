<?php
include 'connect.php'; // เชื่อมต่อฐานข้อมูล

// เพิ่มหนัง
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["title"])) {
    $title = $conn->real_escape_string($_POST["title"]);
    $year = intval($_POST["year"]); 
    $desc = $conn->real_escape_string($_POST["description"]);
    

    // จัดการรูป
    $image = null;
    if (!empty($_FILES["image"]["name"])) {
        $targetDir = "uploads/";
        if (!is_dir($targetDir)) mkdir($targetDir);
        $image = time() . "_" . basename($_FILES["image"]["name"]);
        $targetFile = $targetDir . $image;
        move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile);
    }

    $conn->query("INSERT INTO movies (title, year, description, image) VALUES ('$title', '$year', '$desc', '$image')");

    $conn->query("INSERT INTO logs (action) VALUES ('Added movie: $title')");
    header("Location: index.php");
    exit();
}

// ลบหนัง
if (isset($_GET["delete"])) {
    $id = intval($_GET["delete"]);
    $res = $conn->query("SELECT title, image FROM movies WHERE id = $id");
    $movie = $res->fetch_assoc();
    $title = $movie["title"] ?? '';

    // ลบรูปไฟล์จริงด้วย
    if (!empty($movie["image"]) && file_exists("uploads/".$movie["image"])) {
        unlink("uploads/".$movie["image"]);
    }

    $conn->query("DELETE FROM movies WHERE id = $id");
    $conn->query("INSERT INTO logs (action) VALUES ('Deleted movie: $title')");
    header("Location: index.php");
    exit();
}

// ดึงรายการหนัง
$movies = $conn->query("SELECT * FROM movies ORDER BY added_at DESC");
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>🎬 THE MOVIES</title>
    <style>
        body {
            background: #0f0f0f;
            color: #f2f2f2;
            font-family: 'Segoe UI', sans-serif;
            margin: 0;
            padding: 0;
        }
        header {
            background: #111;
            padding: 20px;
            text-align: center;
            font-size: 32px;
            color: #8ab4f8;
            box-shadow: 0 0 15px #8ab4f8;
        }
        .container {
            width: 90%;
            margin: 20px auto;
        }
        form {
            background: #1c1c1c;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 40px;
            box-shadow: 0 0 10px #333;
        }
        input, textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            background: #222;
            color: #fff;
            border: none;
            border-radius: 5px;
        }
        input[type="submit"] {
            background: #8ab4f8;
            color: #000;
            font-weight: bold;
            cursor: pointer;
            transition: 0.3s;
        }
        input[type="submit"]:hover {
            background: #5f8dd3;
        }
        .movies-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: 20px;
        }
        .movie-card {
            background: #1a1a1a;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 0 15px #000;
            transition: transform 0.3s, box-shadow 0.3s;
        }
        .movie-card:hover {
            transform: scale(1.05);
            box-shadow: 0 0 20px #8ab4f8;
        }
        .movie-card img {
            width: 100%;
            height: 300px;
            object-fit: cover;
        }
        .movie-info {
            padding: 15px;
        }
        .movie-info h3 {
            margin: 0;
            color: #8ab4f8;
            font-size: 18px;
        }
        .movie-info p {
            font-size: 14px;
            color: #ccc;
            margin: 10px 0;
        }
        .delete-btn {
            display: inline-block;
            margin-top: 10px;
            padding: 6px 12px;
            background: #f06262;
            color: #000;
            text-decoration: none;
            border-radius: 5px;
            font-size: 13px;
        }
        .delete-btn:hover {
            background: #ff4c4c;
        }
        .back-btn {
            display: inline-block;
            margin: 20px 0;
            padding: 10px 20px;
            background: #8ab4f8;
            color: #000;
            text-decoration: none;
            font-weight: bold;
            border-radius: 8px;
            transition: 0.3s;
        }
        .back-btn:hover {
            background: #5f8dd3;
        }
    </style>
</head>
<body>
    <header>🎬 THE MOVIES</header>

    <div class="container">
        <a href="movies.php" class="back-btn">🏠 กลับหน้าแรก</a>

        <h2>➕ เพิ่มหนังใหม่</h2>
        <form method="post" enctype="multipart/form-data">
            <input type="text" name="title" placeholder="ชื่อหนัง" required>
            <input type="number" name="year" placeholder="ปีที่ออกฉาย" min="1900" max="2100" required>
            <textarea name="description" placeholder="คำอธิบาย..." rows="3"></textarea>
            <input type="file" name="image" accept="image/*">
            <input type="submit" value="เพิ่มหนัง">
        </form>

        <h2>📽 รายชื่อหนัง </h2>
        <div class="movies-grid">
            <?php while($row = $movies->fetch_assoc()): ?>
                <div class="movie-card">
                    <?php if(!empty($row['image'])): ?>
                        <img src="uploads/<?= $row['image'] ?>" alt="<?= htmlspecialchars($row['title']) ?>">
                    <?php else: ?>
                        <img src="https://via.placeholder.com/220x300?text=No+Image" alt="no image">
                    <?php endif; ?>
                    <div class="movie-info">
                        <h3><?= htmlspecialchars($row['title']) ?></h3>
                        <p><?= nl2br(htmlspecialchars($row['description'])) ?></p>
                        
                        <a class="delete-btn" href="?delete=<?= $row['id'] ?>" onclick="return confirm('ลบหนังเรื่องนี้?')">ลบ</a>
                        <a class="delete-btn" style="background:#8ab4f8; color:#000;" href="edit.php?id=<?= $row['id'] ?>">แก้ไข</a>

                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
</body>
</html>
