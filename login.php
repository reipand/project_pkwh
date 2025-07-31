<?php
/**
 * Login Page
 * SMP Bina Informatika
 */

session_start();

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['user_type'] === 'admin') {
        header('Location: admin/dashboard.php');
    } else {
        header('Location: dashboard.php');
    }
    exit();
}

$page_title = "Login";
$error_message = '';

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $user_type = $_POST['user_type'] ?? 'student';
    
    if (empty($username) || empty($password)) {
        $error_message = 'Username dan password harus diisi';
    } else {
        try {
            require_once 'config/database.php';
            $database = new Database();
            $db = $database->getConnection();
            
            if ($user_type === 'student') {
                // Student login
                $query = "SELECT id, username, name, password, status FROM students WHERE username = ?";
                $stmt = $db->prepare($query);
                $stmt->bindParam(1, $username);
                $stmt->execute();
                
                if ($stmt->rowCount() > 0) {
                    $student = $stmt->fetch(PDO::FETCH_ASSOC);
                    if (password_verify($password, $student['password'])) {
                        if ($student['status'] === 'pending') {
                            $error_message = 'Akun Anda masih dalam proses verifikasi. Silakan tunggu atau hubungi admin.';
                        } else {
                            $_SESSION['user_id'] = $student['id'];
                            $_SESSION['username'] = $student['username'];
                            $_SESSION['name'] = $student['name'];
                            $_SESSION['user_type'] = 'student';
                            $_SESSION['status'] = $student['status'];
                            
                            header('Location: dashboard.php');
                            exit();
                        }
                    } else {
                        $error_message = 'Password salah';
                    }
                } else {
                    $error_message = 'Username tidak ditemukan';
                }
            } else {
                // Admin login
                $query = "SELECT id, username, full_name, password, role FROM users WHERE username = ? AND is_active = 1";
                $stmt = $db->prepare($query);
                $stmt->bindParam(1, $username);
                $stmt->execute();
                
                if ($stmt->rowCount() > 0) {
                    $user = $stmt->fetch(PDO::FETCH_ASSOC);
                    if (password_verify($password, $user['password'])) {
                        $_SESSION['user_id'] = $user['id'];
                        $_SESSION['username'] = $user['username'];
                        $_SESSION['name'] = $user['full_name'];
                        $_SESSION['user_type'] = 'admin';
                        $_SESSION['role'] = $user['role'];
                        
                        // Update last login
                        $updateQuery = "UPDATE users SET last_login = NOW() WHERE id = ?";
                        $updateStmt = $db->prepare($updateQuery);
                        $updateStmt->bindParam(1, $user['id']);
                        $updateStmt->execute();
                        
                        header('Location: admin/dashboard.php');
                        exit();
                    } else {
                        $error_message = 'Password salah';
                    }
                } else {
                    $error_message = 'Username tidak ditemukan atau akun tidak aktif';
                }
            }
        } catch (Exception $e) {
            $error_message = 'Terjadi kesalahan sistem. Silakan coba lagi nanti.';
            error_log("Login error: " . $e->getMessage());
        }
    }
}

include 'includes/header.php';
?>

<style>
.login-page {
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, #e8f5e9 0%, #c8e6c9 100%);
    padding: 2rem 0;
}

.login-container {
    max-width: 450px;
    width: 100%;
    padding: 3rem 2rem;
    background: white;
    border-radius: 20px;
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
    position: relative;
    overflow: hidden;
}

.login-container::before {
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

.login-content {
    position: relative;
    z-index: 1;
}

.login-header {
    text-align: center;
    margin-bottom: 2rem;
}

.login-logo {
    width: 80px;
    height: 80px;
    background: linear-gradient(135deg, #4caf50, #388e3c);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1rem;
    color: white;
    font-size: 2rem;
}

.login-title {
    font-size: 1.75rem;
    font-weight: 700;
    color: #2c5530;
    margin-bottom: 0.5rem;
}

.login-subtitle {
    color: #666;
    font-size: 0.95rem;
}

.form-group {
    margin-bottom: 1.5rem;
}

.form-label {
    font-weight: 600;
    color: #2c5530;
    margin-bottom: 0.5rem;
    display: block;
}

.form-control {
    border: 2px solid #e0e0e0;
    border-radius: 10px;
    padding: 0.75rem 1rem;
    font-size: 1rem;
    transition: all 0.3s ease;
    background: white;
    width: 100%;
}

.form-control:focus {
    border-color: #4caf50;
    box-shadow: 0 0 0 0.2rem rgba(76, 175, 80, 0.25);
    outline: none;
}

.user-type-selector {
    display: flex;
    gap: 0.5rem;
    margin-bottom: 1.5rem;
}

.user-type-btn {
    flex: 1;
    padding: 0.75rem;
    border: 2px solid #e0e0e0;
    background: white;
    border-radius: 10px;
    cursor: pointer;
    transition: all 0.3s ease;
    font-weight: 500;
    color: #666;
}

.user-type-btn.active {
    border-color: #4caf50;
    background: #4caf50;
    color: white;
}

.user-type-btn:hover {
    border-color: #4caf50;
}

.btn-login {
    background: linear-gradient(135deg, #4caf50, #388e3c);
    border: none;
    border-radius: 50px;
    padding: 1rem 2rem;
    font-weight: 600;
    font-size: 1.1rem;
    color: white;
    width: 100%;
    transition: all 0.3s ease;
    margin-top: 1rem;
}

.btn-login:hover {
    background: linear-gradient(135deg, #388e3c, #2e7d32);
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(76, 175, 80, 0.4);
}

.alert {
    padding: 1rem;
    border-radius: 10px;
    margin-bottom: 1.5rem;
    font-weight: 500;
}

.alert-danger {
    background: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}

.alert-success {
    background: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

.login-footer {
    text-align: center;
    margin-top: 2rem;
    padding-top: 1.5rem;
    border-top: 1px solid #e0e0e0;
}

.login-footer a {
    color: #4caf50;
    text-decoration: none;
    font-weight: 500;
}

.login-footer a:hover {
    text-decoration: underline;
}

.forgot-password {
    text-align: center;
    margin-top: 1rem;
}

.forgot-password a {
    color: #666;
    text-decoration: none;
    font-size: 0.9rem;
}

.forgot-password a:hover {
    color: #4caf50;
}

@media (max-width: 576px) {
    .login-container {
        padding: 2rem 1.5rem;
        margin: 0 1rem;
    }
    
    .login-title {
        font-size: 1.5rem;
    }
    
    .user-type-selector {
        flex-direction: column;
    }
}
</style>

<div class="login-page">
    <div class="login-container">
        <div class="login-content">
            <div class="login-header">
                <div class="login-logo">
                    <i class="fas fa-graduation-cap"></i>
                </div>
                <h1 class="login-title">Selamat Datang</h1>
                <p class="login-subtitle">Silakan login untuk melanjutkan</p>
            </div>
            
            <?php if ($error_message): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    <?php echo htmlspecialchars($error_message); ?>
                </div>
            <?php endif; ?>
            
            <?php if (isset($_GET['message']) && $_GET['message'] === 'logout_success'): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle me-2"></i>
                    Anda berhasil logout dari sistem.
                </div>
            <?php endif; ?>
            
            <?php if (isset($_GET['error']) && $_GET['error'] === 'unauthorized'): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    Anda harus login terlebih dahulu untuk mengakses halaman tersebut.
                </div>
            <?php endif; ?>
            
            <form method="POST" action="login.php" id="loginForm">
                <div class="user-type-selector">
                    <button type="button" class="user-type-btn active" data-type="student">
                        <i class="fas fa-user-graduate me-2"></i>
                        Siswa
                    </button>
                    <button type="button" class="user-type-btn" data-type="admin">
                        <i class="fas fa-user-shield me-2"></i>
                        Admin
                    </button>
                </div>
                
                <input type="hidden" name="user_type" id="userType" value="student">
                
                <div class="form-group">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" name="username" id="username" class="form-control" 
                           placeholder="Masukkan username" required>
                </div>
                
                <div class="form-group">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" name="password" id="password" class="form-control" 
                           placeholder="Masukkan password" required>
                </div>
                
                <button type="submit" class="btn-login">
                    <i class="fas fa-sign-in-alt me-2"></i>
                    Login
                </button>
            </form>
            
            <div class="forgot-password">
                <a href="#" onclick="showForgotPassword()">Lupa password?</a>
            </div>
            
            <div class="login-footer">
                <p>Belum punya akun? <a href="index.php#pendaftaran">Daftar sekarang</a></p>
                <p><a href="index.php">Kembali ke Beranda</a></p>
            </div>
        </div>
    </div>
</div>

<script>
// User type selector
document.querySelectorAll('.user-type-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        // Remove active class from all buttons
        document.querySelectorAll('.user-type-btn').forEach(b => b.classList.remove('active'));
        
        // Add active class to clicked button
        this.classList.add('active');
        
        // Update hidden input
        document.getElementById('userType').value = this.dataset.type;
        
        // Update form action and validation
        updateFormForUserType(this.dataset.type);
    });
});

function updateFormForUserType(type) {
    const usernameField = document.getElementById('username');
    const passwordField = document.getElementById('password');
    
    if (type === 'student') {
        usernameField.placeholder = 'Masukkan username siswa';
        passwordField.placeholder = 'Masukkan password siswa';
    } else {
        usernameField.placeholder = 'Masukkan username admin';
        passwordField.placeholder = 'Masukkan password admin';
    }
}

// Form validation
document.getElementById('loginForm').addEventListener('submit', function(e) {
    const username = document.getElementById('username').value.trim();
    const password = document.getElementById('password').value.trim();
    
    if (!username || !password) {
        e.preventDefault();
        showToast('Username dan password harus diisi', 'error');
        return false;
    }
    
    // Show loading
    const submitBtn = this.querySelector('.btn-login');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Memproses...';
    submitBtn.disabled = true;
    
    // Restore button after 3 seconds (in case of error)
    setTimeout(() => {
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    }, 3000);
});

function showForgotPassword() {
    const userType = document.getElementById('userType').value;
    let message = '';
    
    if (userType === 'student') {
        message = 'Untuk reset password siswa, silakan hubungi admin sekolah atau kirim email ke info.sdpjointaro@spj.sch.id';
    } else {
        message = 'Untuk reset password admin, silakan hubungi super admin atau kirim email ke admin@smpbinainformatika.sch.id';
    }
    
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            icon: 'info',
            title: 'Lupa Password',
            text: message,
            confirmButtonColor: '#4caf50'
        });
    } else {
        alert(message);
    }
}

// Add some animation
document.addEventListener('DOMContentLoaded', function() {
    const loginContainer = document.querySelector('.login-container');
    const loginLogo = document.querySelector('.login-logo');
    
    // Fade in animation
    loginContainer.style.opacity = '0';
    loginContainer.style.transform = 'translateY(30px)';
    
    setTimeout(() => {
        loginContainer.style.transition = 'all 0.8s ease-out';
        loginContainer.style.opacity = '1';
        loginContainer.style.transform = 'translateY(0)';
    }, 100);
    
    // Pulse animation for logo
    setTimeout(() => {
        loginLogo.style.animation = 'pulse 2s infinite';
    }, 800);
});

// Add CSS animations
const style = document.createElement('style');
style.textContent = `
    @keyframes pulse {
        0% {
            transform: scale(1);
        }
        50% {
            transform: scale(1.05);
        }
        100% {
            transform: scale(1);
        }
    }
`;
document.head.appendChild(style);
</script>

<?php include 'includes/footer.php'; ?> 