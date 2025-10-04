<?php
session_start();
// ลบ session ทั้งหมด
session_unset();
session_destroy();
// กลับไปหน้าแรก
header("Location: movies.php");
exit();
?>
