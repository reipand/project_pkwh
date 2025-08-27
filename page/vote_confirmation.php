<?php
session_start();

// Periksa apakah pengguna sudah login
if (!isset($_SESSION['is_logged_in']) || $_SESSION['is_logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

// Ambil status vote dari sesi
$status = $_SESSION['vote_status'] ?? 'failed';
$user_name = $_SESSION['user_nama'] ?? 'Pengguna';

// Hapus status vote dari sesi setelah ditampilkan
unset($_SESSION['vote_status']);

$message_title = '';
$message_body = '';
$icon_path = '';
$bg_color = '';
$text_color = '';

if ($status === 'success') {
    $message_title = 'Terima kasih, ' . htmlspecialchars($user_name) . '!';
    $message_body = 'Suara Anda berhasil dicatat. Partisipasi Anda sangat berarti.';
    $icon_path = '../assets/image/check.png';
    $bg_color = 'bg-green-500';
    $text_color = 'text-green-800';
} elseif ($status === 'already_voted') {
    $message_title = 'Anda Sudah Memilih';
    $message_body = 'Anda telah memberikan suara sebelumnya. Terima kasih atas partisipasi Anda.';
    $icon_path = '../assets/image/alert.png';
    $bg_color = 'bg-yellow-500';
    $text_color = 'text-yellow-800';
} else {
    $message_title = 'Terjadi Kesalahan!';
    $message_body = 'Suara Anda tidak dapat dicatat. Silakan coba kembali.';
    $icon_path = '../assets/image/x-button.png';
    $bg_color = 'bg-red-500';
    $text_color = 'text-red-800';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Konfirmasi Voting</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="../assets/css/custom.css">
    <style>
        .container-center {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
        }
    </style>
</head>
<body class="bg-gray-100 font-sans">
    <div class="container-center">
        <div class="bg-white p-8 rounded-3xl shadow-lg max-w-md w-full text-center">
            <div class="flex flex-col items-center justify-center mb-6">
                <img src="<?php echo $icon_path; ?>" alt="Status Icon" class="w-20 h-20 mb-4">
                <h1 class="text-3xl font-bold text-gray-800 mb-2"><?php echo htmlspecialchars($message_title); ?></h1>
                <p class="text-gray-600"><?php echo htmlspecialchars($message_body); ?></p>
            </div>
            <a href="logout.php" class="bg-indigo-600 text-white font-medium py-3 px-8 rounded-full hover:bg-indigo-700 transition-colors">Logout</a>
        </div>
    </div>
</body>
</html>