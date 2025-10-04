<?php
session_start();
include 'connect.php';

$profile_pic = null;
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $result = $conn->query("SELECT profile_pic FROM users WHERE id='$user_id' LIMIT 1");
    $user = $result->fetch_assoc();
    $profile_pic = $user['profile_pic'];
}

// ดึงข้อมูลหนังตามปกติ
$movies = $conn->query("SELECT * FROM movies ORDER BY added_at DESC");
?>

<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<title>🎥 THE MOVIES</title>
<style>
body {
    background: linear-gradient(145deg, #0f0f0f, #1c1c1c);
    color: #f2f2f2;
    font-family: 'Segoe UI', sans-serif;
    margin: 0;
    padding: 0;
}
header {
    padding: 30px;
    text-align: center;
    font-size: 36px;
    color: #8ab4f8;
    text-shadow: 0 0 10px #8ab4f8;
    position: relative;
}
.top-right {
    position: absolute;
    top: 20px;
    right: 20px;
}

.top-right span {
    font-size: 12px;  
    margin-right: 5px; 
    color: #fff;
}

.top-right a {
    display: inline-block;
    margin-left: 5px;       
    padding: 5px 10px;      
    font-size: 12px;        
    background: #8ab4f8;
    color: #000;
    text-decoration: none;
    font-weight: bold;
    border-radius: 5px;
    transition: 0.3s;
}


p.sub {
    text-align: center;
    font-size: 18px;
    color: #ccc;
    margin-bottom: 30px;
}
a.button {
    display: inline-block;
    margin: 10px auto;
    padding: 12px 25px;
    background-color: #8ab4f8;
    color: #000;
    text-decoration: none;
    font-weight: bold;
    border-radius: 8px;
    box-shadow: 0 0 15px #8ab4f8;
    transition: 0.3s;
}
a.button:hover {
    background-color: #5f8dd3;
    box-shadow: 0 0 25px #5f8dd3;
}
.movies-grid {
    width: 90%;
    margin: auto;
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
    gap: 20px;
}
.movie-card {
    background: #1c1c1c;
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
    font-size: 20px;
}
.movie-info p {
    font-size: 14px;
    color: #ccc;
    margin: 10px 0 0;
}
.profile-img {
    width: 30px;
    height: 30px;
    border-radius: 50%;
    object-fit: cover;   /* ป้องกันรูปโดนบีบ */
    vertical-align: middle;
    margin-right: 5px;
}

</style>

</head>
<body>

<header>
    🎬 ยินดีต้อนรับสู่เว็บไซต์แนะนำหนัง
    <div class="top-right">
        <?php if(isset($_SESSION['username'])): ?>
            <?php if(!empty($profile_pic)): ?>
                <img src="uploads/profiles/<?= htmlspecialchars($profile_pic) ?>" 
                    alt="Profile" class="profile-img">

            <?php endif; ?>
            <span><?= htmlspecialchars($_SESSION['username']) ?></span>
            <a href="logout.php">Logout</a>
        <?php else: ?>
            <a href="login.php">Login</a>
            <a href="register.php">สมัครสมาชิก</a>
        <?php endif; ?>
    </div>
</header>

<p class="sub">สำรวจรายชื่อภาพยนตร์ เพิ่มข้อมูลของคุณเอง และดูบันทึกกิจกรรมย้อนหลัง</p>

<div style="text-align:center;">
    <a href="index.php" class="button">➕ เพิ่มข้อมูลหนัง</a>
</div>

<h2 style="text-align:center; margin:30px 0;">📽 หนังที่แนะนำ</h2>
<div class="movies-grid">
    <?php while($row = $movies->fetch_assoc()): ?>
        <div class="movie-card">
            <?php if(!empty($row['image'])): ?>
                <img src="uploads/<?= $row['image'] ?>" alt="<?= htmlspecialchars($row['title']) ?>">
            <?php else: ?>
                <img src="https://via.placeholder.com/220x300?text=No+Image" alt="no image">
            <?php endif; ?>
            <div class="movie-info">
                <h3><?= htmlspecialchars($row['title']) ?> (<?= $row['year'] ?>)</h3>
                <p><?= nl2br(htmlspecialchars($row['description'])) ?></p>
            </div>
        </div>
    <?php endwhile; ?>
</div>
</body>
</html>
