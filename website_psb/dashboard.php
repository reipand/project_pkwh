<?php
session_start();
if (!isset($_SESSION['login_user'])) {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
</head>
<body>
    <h1>Selamat datang, <?= $_SESSION['login_user']; ?>!</h1>
    <a href="logout.php">Logout</a>
</body>
</html>
