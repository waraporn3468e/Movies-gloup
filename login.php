<?php
session_start();
include 'connect.php';

$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($conn->real_escape_string($_POST['email']));
    $password = $_POST['password'];

    // ตรวจสอบผู้ใช้จาก email
    $result = $conn->query("SELECT * FROM users WHERE email='$email' LIMIT 1");

    if ($result && $result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            // login สำเร็จ
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username']; // ใช้ชื่อแสดง
            $_SESSION['email'] = $user['email'];       // เก็บอีเมลด้วย
            header("Location: movies.php"); // ไปหน้าแรก
            exit();
        } else {
            $message = "รหัสผ่านไม่ถูกต้อง";
        }
    } else {
        $message = "อีเมลไม่ถูกต้อง หรือยังไม่ได้สมัครสมาชิก";
    }
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<title>Login - THE MOVIES</title>
<style>
body { background:#0f0f0f; color:#f2f2f2; font-family:'Segoe UI',sans-serif; display:flex; justify-content:center; align-items:center; height:100vh; }
.login-box { background:#1c1c1c; padding:40px; border-radius:10px; box-shadow:0 0 15px #8ab4f8; width:350px; text-align:center; }
.login-box h2 { margin-bottom:20px; color:#8ab4f8; }
.login-box input { width:100%; padding:10px; margin-bottom:15px; border:none; border-radius:5px; background:#222; color:#fff; }
.login-box input[type="submit"] { background:#8ab4f8; color:#000; font-weight:bold; cursor:pointer; transition:0.3s; }
.login-box input[type="submit"]:hover { background:#5f8dd3; }
.message { color:#ff4c4c; margin-bottom:15px; }
a.home-btn { display:inline-block; margin-top:10px; padding:10px 20px; background:#8ab4f8; color:#000; text-decoration:none; border-radius:8px; transition:0.3s; }
a.home-btn:hover { background:#5f8dd3; }
</style>
</head>
<body>
<div class="login-box">
    <h2>เข้าสู่ระบบ</h2>
    <?php if($message) echo "<div class='message'>$message</div>"; ?>
    <form method="post">
        <input type="email" name="email" placeholder="อีเมล" required>
        <input type="password" name="password" placeholder="รหัสผ่าน" required>
        <input type="submit" value="Login">
    </form>
    <a class="home-btn" href="movies.php">🏠 กลับหน้าแรก</a>
</div>
</body>
</html>
