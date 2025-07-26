<?php
session_start();
$conn = new mysqli("localhost", "root", "", "db_psb");

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = md5($_POST['password']); // Enkripsi MD5

    $sql = "SELECT * FROM users WHERE username='$username' AND password='$password'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $_SESSION['login_user'] = $username;
        header("Location: dashboard.php");
    } else {
        $message = "Username atau password salah!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Login Siswa</title>
  <style>
    body {
      background: #2f4562;
      font-family: Arial, sans-serif;
      display: flex;
      height: 100vh;
      justify-content: center;
      align-items: center;
    }
    .login-box {
      background: #d62828;
      border-radius: 20px;
      padding: 40px;
      width: 300px;
      box-shadow: 3px 5px 10px rgba(0,0,0,0.3);
      text-align: center;
      color: white;
    }
    .login-box img {
      width: 50px;
      margin-bottom: 15px;
    }
    .login-box input[type="text"],
    .login-box input[type="password"] {
      width: 100%;
      padding: 10px;
      margin: 10px 0;
      border: none;
      border-radius: 10px;
      box-shadow: inset 5px 5px 5px #aaa;
    }
    .login-box input[type="checkbox"] {
      margin-right: 5px;
    }
    .login-box button {
      padding: 10px 30px;
      border: none;
      border-radius: 10px;
      background: white;
      color: black;
      font-weight: bold;
      cursor: pointer;
      margin-top: 20px;
    }
    .error {
      color: yellow;
      font-size: 14px;
    }
  </style>
</head>
<body>
  <form method="post">
    <div class="login-box">
      <img src="https://cdn-icons-png.flaticon.com/512/2890/2890334.png" alt="icon sekolah">
      <h2>SMP Bina Informatika</h2>
      <?php if ($message): ?>
        <p class="error"><?= $message ?></p>
      <?php endif; ?>
      <input type="text" name="username" placeholder="Username" required>
      <input type="password" name="password" placeholder="Password" required>
      <div style="text-align: left;">
        <label><input type="checkbox"> Remember me</label>
      </div>
      <button type="submit">Login</button>
    </div>
  </form>
</body>
</html>
