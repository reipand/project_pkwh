<?php
// Deteksi apakah berada di folder admin
$is_admin = strpos($_SERVER['REQUEST_URI'], '/admin/') !== false;
$base_path = $is_admin ? '../' : './';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . ' - ' : ''; ?>SMP Bina Informatika</title>
    
    <!-- Meta tags untuk SEO -->
    <meta name="description" content="Penerimaan Siswa Baru SMP Bina Informatika Tahun Ajaran 2025-2026. Sekolah humanis yang mengedepankan kompetensi anak untuk berkembang sesuai bakat dan minat.">
    <meta name="keywords" content="SMP Bina Informatika, Penerimaan Siswa Baru, Sekolah Menengah Pertama, Tangerang Selatan">
    <meta name="author" content="SMP Bina Informatika">
    
    <!-- Open Graph tags -->
    <meta property="og:title" content="Penerimaan Siswa Baru SMP Bina Informatika 2025-2026">
    <meta property="og:description" content="Sekolah humanis yang mengedepankan kompetensi anak untuk berkembang sesuai bakat dan minat masing-masing individu">
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?php echo 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']; ?>">
    
    <!-- CSS Files -->
    <link rel="stylesheet" href="<?php echo $base_path; ?>assets/css/style.css">
    <link rel="stylesheet" href="<?php echo $base_path; ?>assets/css/responsive.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Custom CSS untuk animasi -->
    <style>
        .fade-in {
            animation: fadeIn 0.8s ease-in;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .slide-in-left {
            animation: slideInLeft 0.8s ease-out;
        }
        
        @keyframes slideInLeft {
            from { opacity: 0; transform: translateX(-50px); }
            to { opacity: 1; transform: translateX(0); }
        }
        
        .slide-in-right {
            animation: slideInRight 0.8s ease-out;
        }
        
        @keyframes slideInRight {
            from { opacity: 0; transform: translateX(50px); }
            to { opacity: 1; transform: translateX(0); }
        }
    </style>
</head>
<body>
    <!-- Header Navigation -->
    <header class="navbar navbar-expand-lg fixed-top">
        <div class="container">
            <div class="navbar-brand">
                <div class="logo-container">
                    <div class="logo-img" style="width: 40px; height: 40px; background: #4caf50; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; margin-right: 10px;">
                        <i class="fas fa-graduation-cap"></i>
                    </div>
                    <span class="logo-text">SMP Bina Informatika</span>
                </div>
            </div>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <?php if (!$is_admin): ?>
                        <?php if (isset($_SESSION['user_id']) && $_SESSION['user_type'] === 'student'): ?>
                        <!-- Menu untuk siswa yang sudah login -->
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo $base_path; ?>index.php">Beranda</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo $base_path; ?>dashboard.php">Dashboard</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link btn btn-danger login-btn" href="<?php echo $base_path; ?>logout.php">Logout</a>
                        </li>
                        <?php else: ?>
                        <!-- Menu untuk pengunjung umum -->
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo $base_path; ?>index.php">Beranda</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo $base_path; ?>index.php#informasi">Informasi</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo $base_path; ?>index.php#prosedur">Prosedur</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo $base_path; ?>index.php#pendaftaran">Pendaftaran</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo $base_path; ?>index.php#gallery">Galeri</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo $base_path; ?>index.php#kontak">Kontak</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link btn btn-primary login-btn" href="<?php echo $base_path; ?>login.php">Login</a>
                        </li>
                        <?php endif; ?>
                    <?php else: ?>
                    <!-- Menu untuk admin -->
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo $base_path; ?>index.php">Beranda</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="dashboard.php">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link btn btn-danger login-btn" href="logout.php">Logout</a>
                    </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </header>

    <!-- Main Content Container --> 
    <!-- Main Content Container --> 