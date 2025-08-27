<?php
// Mulai session
session_start();

// Ambil pesan error dari URL jika ada
$error_message = '';
if (isset($_GET['error'])) {
    $error_code = $_GET['error'];
    if ($error_code === 'fields_empty') {
        $error_message = 'NIS dan Password tidak boleh kosong.';
    } elseif ($error_code === 'invalid_credentials') {
        $error_message = 'NIS atau Password salah. Silakan coba lagi.';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - BiVote</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            background: #FFF;
            background-image: url('../assets/image/bg1.png'), url('../assets/image/bg2.png');
            background-position: right, left;   
            background-repeat: no-repeat, no-repeat;

        }
        .login-card {
            background-color: white;
            border-radius: 1.5rem;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.2), 0 4px 6px -2px rgba(0, 0, 0, 0.3);
        }
    </style>
</head>
<body class="flex items-center justify-center min-h-screen">
    <div class="login-card w-full max-w-lg p-8 space-y-6 text-center">
        <div class="flex flex-col items-center">
            <img src="../assets/image/logo.png" alt="Logo BiVote" class="w-40 h-30 mb-4">
            <h1 class="mt-2 text-3xl font-bold text-gray-900">
                Welcome <span class="text-indigo-600">Back!</span>
            </h1>
            <p class="mt-1 text-sm text-gray-600">
                Welcome Back To BiVOTE
            </p>
        </div>

        <!-- Feedback message (AJAX) -->
        <div id="feedback-message" class="text-sm mb-2"></div>

        <!-- Error message (fallback PHP) -->
        <?php if (!empty($error_message)): ?>
            <div class="p-4 text-sm text-red-700 bg-red-100 rounded-lg" role="alert">
                <span class="font-medium">Error!</span> <?php echo $error_message; ?>
            </div>
        <?php endif; ?>

        <form id="login-form" class="space-y-4" action="../api/login_siswa.php" method="POST" autocomplete="off">
            <div>
                <label for="nis" class="block text-left text-sm font-medium text-gray-700">NIS Siswa</label>
                <input id="nis" name="nis" type="text" inputmode="numeric" pattern="\\d*" oninput="this.value=this.value.replace(/[^0-9]/g,'')" autocomplete="off" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
            </div>
            <div>
                <div class="flex justify-between items-center">
                    <label for="password" class="block text-left text-sm font-medium text-gray-700">Password (Tanggal Lahir)</label>
                    
                </div>
                <input id="password" name="password" type="password" inputmode="numeric" pattern="\\d*" oninput="this.value=this.value.replace(/[^0-9]/g,'')" autocomplete="off" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
            </div>
            
            <div>
                <button id="login-btn" type="submit" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    LOGIN
                </button>
            </div>
        </form>
        
    </div>
    <p class="text-center text-gray-500 text-xs mt-4 w-full absolute bottom-4 left-0">
        &copy; 2025 BiVOTE. All rights reserved.
    </p>
    <script src="../assets/js/login.js" defer></script>
</body>
</html>