<?php
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "movies_db";

// สร้างการเชื่อมต่อ
$conn = new mysqli($host, $user, $pass, $dbname);

// ตรวจสอบการเชื่อมต่อ
if ($conn->connect_error) {
    die("เชื่อมต่อฐานข้อมูลล้มเหลว: " . $conn->connect_error);
}

// ไม่ต้องมี echo หรือ return แค่ให้ตัวแปร $conn พร้อมใช้งาน
?>