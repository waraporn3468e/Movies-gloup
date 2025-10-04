<?php
session_start();
include 'connect.php';

$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($conn->real_escape_string($_POST['username']));
    $email = trim($conn->real_escape_string($_POST['email']));
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // จัดการรูปโปรไฟล์ (ถ้ามี)
    $profile_pic = null;
    if (!empty($_FILES['profile_pic']['name'])) {
        $targetDir = "uploads/profiles/";
        if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);
        $profile_pic = time() . "_" . basename($_FILES['profile_pic']['name']);
        move_uploaded_file($_FILES['profile_pic']['tmp_name'], $targetDir . $profile_pic);
    }

    // ตรวจสอบ email หรือ username ซ้ำ
    $check = $conn->query("SELECT * FROM users WHERE username='$username' OR email='$email'");
    if ($check->num_rows > 0) {
        $message = "ชื่อผู้ใช้หรืออีเมลนี้ถูกใช้งานแล้ว";
    } else {
        $stmt = $conn->prepare("INSERT INTO users (username,email,password,profile_pic) VALUES (?,?,?,?)");
        $stmt->bind_param("ssss", $username, $email, $password, $profile_pic);
        if ($stmt->execute()) {
            $message = "สมัครสมาชิกสำเร็จ! <a href='login.php'>เข้าสู่ระบบ</a>";
        } else {
            $message = "เกิดข้อผิดพลาด: " . $conn->error;
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<title>สมัครสมาชิก - THE MOVIES</title>
<style>
body{
    background:#0f0f0f;
    color:#f2f2f2;
    font-family:'Segoe UI',sans-serif;
    display:flex;
    justify-content:center;
    align-items:center;
    height:100vh;
    margin:0;
}
.form-box{
    background:#1c1c1c;
    padding:36px;
    border-radius:10px;
    box-shadow:0 0 15px #8ab4f8;
    width:360px;
    text-align:center;
}
.form-box h2{
    color:#8ab4f8;
    margin-bottom:16px;
    font-size:20px;
}
.form-box input[type="text"],
.form-box input[type="email"],
.form-box input[type="password"],
.form-box input[type="file"]{
    width:100%;
    padding:10px 12px;
    margin-bottom:12px;
    border:none;
    border-radius:6px;
    background:#222;
    color:#fff;
    box-sizing:border-box;
    font-size:14px;
}

/* แถวสำหรับ input รหัส เพื่อวางปุ่มแสดง/ซ่อน */
.pw-row {
    position: relative;
}

/* ปุ่มแสดง/ซ่อนตา */
.pw-toggle {
    position:absolute;
    right:10px;
    top:50%;
    transform:translateY(-50%);
    background:transparent;
    border:none;
    color:#bbb;
    font-size:16px;
    cursor:pointer;
    padding:4px;
}

/* ปรับปุ่ม submit และลิงก์ */
.form-box input[type="submit"]{
    width:100%;
    padding:10px;
    margin-top:6px;
    background:#8ab4f8;
    color:#000;
    border:none;
    border-radius:6px;
    font-weight:bold;
    cursor:pointer;
}
.form-box input[type="submit"]:hover{ background:#5f8dd3; }
.message{ color:#ff4c4c; margin-bottom:10px; text-align:left; font-size:13px; }
a.login-btn{ display:inline-block; margin-top:10px; padding:8px 14px; background:#2b2b2b; color:#8ab4f8; text-decoration:none; border-radius:6px; font-size:13px; }
.small-note{ color:#bbb; font-size:12px; margin-top:8px; }
</style>
</head>
<body>
<div class="form-box">
    <h2>สมัครสมาชิก</h2>
    <?php if($message) echo "<div class='message'>$message</div>"; ?>
    <form method="post" enctype="multipart/form-data" onsubmit="return validatePasswordLength();">
        <input type="text" name="username" placeholder="ชื่อผู้ใช้" required>
        <input type="email" name="email" placeholder="อีเมล" required>

        <div class="pw-row">
            <input id="password" type="password" name="password" placeholder="รหัสผ่าน" required aria-label="รหัสผ่าน">
            <button type="button" class="pw-toggle" onclick="togglePassword('password', this)" aria-label="แสดง/ซ่อน รหัสผ่าน"></button>
        </div>

        <input type="file" name="profile_pic" accept="image/*">
        <input type="submit" value="สมัครสมาชิก">
    </form>

    <div class="small-note">โดยการสมัคร คุณตกลงตามนโยบายของเว็บไซต์</div>
    <a class="login-btn" href="login.php">เข้าสู่ระบบ</a>
</div>

<script>
// ฟังก์ชันสลับแสดง/ซ่อนรหัสผ่าน (simple toggle)
function togglePassword(fieldId, btn){
    const field = document.getElementById(fieldId);
    if (!field) return;
    if (field.type === 'password') {
        field.type = 'text';
    } else {
        field.type = 'password';
    }
}


// ตัวอย่างตรวจความยาวรหัสผ่าน (ฝั่ง client)
function validatePasswordLength(){
    const p = document.getElementById('password').value;
    if (p.length < 6) {
        alert('รหัสผ่านควรมีความยาวอย่างน้อย 6 ตัวอักษร');
        return false;
    }
    return true;
}
</script>
</body>
</html>
