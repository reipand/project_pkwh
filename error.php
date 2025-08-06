<?php
/**
 * Error Page Handler
 * SMP Bina Informatika
 */

$error_code = $_GET['code'] ?? '404';
$page_title = "Error $error_code";

// Error messages
$error_messages = [
    '400' => [
        'title' => 'Bad Request',
        'message' => 'Permintaan yang Anda kirim tidak valid atau tidak dapat diproses.',
        'description' => 'Silakan periksa kembali URL atau form yang Anda kirim.'
    ],
    '401' => [
        'title' => 'Unauthorized',
        'message' => 'Anda tidak memiliki izin untuk mengakses halaman ini.',
        'description' => 'Silakan login terlebih dahulu untuk melanjutkan.'
    ],
    '403' => [
        'title' => 'Forbidden',
        'message' => 'Akses ke halaman ini dilarang.',
        'description' => 'Anda tidak memiliki hak akses untuk melihat konten ini.'
    ],
    '404' => [
        'title' => 'Page Not Found',
        'message' => 'Halaman yang Anda cari tidak ditemukan.',
        'description' => 'URL yang Anda masukkan mungkin salah atau halaman telah dipindahkan.'
    ],
    '500' => [
        'title' => 'Internal Server Error',
        'message' => 'Terjadi kesalahan pada server.',
        'description' => 'Tim kami sedang bekerja untuk memperbaiki masalah ini. Silakan coba lagi nanti.'
    ]
];

$error = $error_messages[$error_code] ?? $error_messages['404'];

include 'includes/header.php';
?>

<style>
.error-page {
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, #e8f5e9 0%, #c8e6c9 100%);
    padding: 2rem 0;
}

.error-container {
    text-align: center;
    max-width: 600px;
    padding: 3rem 2rem;
    background: white;
    border-radius: 20px;
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
    position: relative;
    overflow: hidden;
}

.error-container::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -50%;
    width: 100%;
    height: 100%;
    background: linear-gradient(135deg, rgba(76, 175, 80, 0.1), rgba(56, 142, 60, 0.1));
    border-radius: 50%;
    z-index: 0;
}

.error-content {
    position: relative;
    z-index: 1;
}

.error-code {
    font-size: 8rem;
    font-weight: 800;
    color: #4caf50;
    margin-bottom: 1rem;
    line-height: 1;
    text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.1);
}

.error-title {
    font-size: 2rem;
    font-weight: 700;
    color: #2c5530;
    margin-bottom: 1rem;
}

.error-message {
    font-size: 1.1rem;
    color: #666;
    margin-bottom: 1.5rem;
    line-height: 1.6;
}

.error-description {
    font-size: 0.95rem;
    color: #888;
    margin-bottom: 2rem;
    line-height: 1.5;
}

.error-actions {
    display: flex;
    gap: 1rem;
    justify-content: center;
    flex-wrap: wrap;
}

.error-icon {
    font-size: 4rem;
    color: #4caf50;
    margin-bottom: 1.5rem;
    opacity: 0.8;
}

@media (max-width: 768px) {
    .error-code {
        font-size: 6rem;
    }
    
    .error-title {
        font-size: 1.5rem;
    }
    
    .error-message {
        font-size: 1rem;
    }
    
    .error-actions {
        flex-direction: column;
        align-items: center;
    }
    
    .btn-primary-custom,
    .btn-secondary-custom {
        width: 100%;
        max-width: 250px;
    }
}

@media (max-width: 576px) {
    .error-container {
        padding: 2rem 1.5rem;
        margin: 0 1rem;
    }
    
    .error-code {
        font-size: 4rem;
    }
    
    .error-title {
        font-size: 1.25rem;
    }
    
    .error-message {
        font-size: 0.95rem;
    }
    
    .error-description {
        font-size: 0.9rem;
    }
}
</style>

<div class="error-page">
    <div class="error-container">
        <div class="error-content">
            <div class="error-icon">
                <?php if ($error_code == '404'): ?>
                    <i class="fas fa-search"></i>
                <?php elseif ($error_code == '403'): ?>
                    <i class="fas fa-ban"></i>
                <?php elseif ($error_code == '500'): ?>
                    <i class="fas fa-exclamation-triangle"></i>
                <?php else: ?>
                    <i class="fas fa-exclamation-circle"></i>
                <?php endif; ?>
            </div>
            
            <div class="error-code"><?php echo $error_code; ?></div>
            
            <h1 class="error-title"><?php echo $error['title']; ?></h1>
            
            <p class="error-message"><?php echo $error['message']; ?></p>
            
            <p class="error-description"><?php echo $error['description']; ?></p>
            
            <div class="error-actions">
                <a href="index.php" class="btn btn-primary-custom">
                    <i class="fas fa-home me-2"></i>
                    Kembali ke Beranda
                </a>
                
                <a href="javascript:history.back()" class="btn btn-secondary-custom">
                    <i class="fas fa-arrow-left me-2"></i>
                    Kembali
                </a>
            </div>
            
            <?php if ($error_code == '404'): ?>
            <div class="mt-4">
                <p class="text-muted small mb-2">Mungkin Anda mencari:</p>
                <div class="d-flex flex-wrap justify-content-center gap-2">
                    <a href="#pendaftaran" class="btn btn-sm btn-outline-primary">Pendaftaran</a>
                    <a href="#informasi" class="btn btn-sm btn-outline-primary">Informasi</a>
                    <a href="#kontak" class="btn btn-sm btn-outline-primary">Kontak</a>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
// Add some animation to the error page
document.addEventListener('DOMContentLoaded', function() {
    const errorContainer = document.querySelector('.error-container');
    const errorCode = document.querySelector('.error-code');
    const errorIcon = document.querySelector('.error-icon');
    
    // Fade in animation
    errorContainer.style.opacity = '0';
    errorContainer.style.transform = 'translateY(30px)';
    
    setTimeout(() => {
        errorContainer.style.transition = 'all 0.8s ease-out';
        errorContainer.style.opacity = '1';
        errorContainer.style.transform = 'translateY(0)';
    }, 100);
    
    // Bounce animation for error code
    setTimeout(() => {
        errorCode.style.animation = 'bounce 1s ease-in-out';
    }, 500);
    
    // Pulse animation for icon
    setTimeout(() => {
        errorIcon.style.animation = 'pulse 2s infinite';
    }, 800);
});

// Add CSS animations
const style = document.createElement('style');
style.textContent = `
    @keyframes bounce {
        0%, 20%, 50%, 80%, 100% {
            transform: translateY(0);
        }
        40% {
            transform: translateY(-10px);
        }
        60% {
            transform: translateY(-5px);
        }
    }
    
    @keyframes pulse {
        0% {
            transform: scale(1);
        }
        50% {
            transform: scale(1.1);
        }
        100% {
            transform: scale(1);
        }
    }
`;
document.head.appendChild(style);
</script>

<?php include 'includes/footer.php'; ?> 