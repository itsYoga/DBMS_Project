<?php
// welcome.php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php"); // Redirect to login if not logged in
    exit;
}

echo "<h1>Welcome, " . $_SESSION['username'] . "!</h1>";
echo "<p><a href='schedule.php'>查看個人課表</a></p>"; // 新增課表查詢的連結
echo "<p><a href='logout.php'>Logout</a></p>";
?>