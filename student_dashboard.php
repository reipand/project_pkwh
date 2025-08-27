<?php
/**
 * Student Dashboard
 * SMP Bina Informatika
 */

session_start();

// Check if user is logged in as student
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'student') {
    header('Location: login.php?error=unauthorized');
    exit();
}

$page_title = "Dashboard Siswa";

try {
    require_once 'config/database.php';
    $database = new Database();
    $db = $database->getConnection();
    
    // Get student data
    $student_id = $_SESSION['user_id'];
    $query = "SELECT * FROM students WHERE id = ?";
    $stmt = $db->prepare($query);
    $stmt->bindParam(1, $student_id);
    $stmt->execute();
    
    if ($stmt->rowCount() > 0) {
        $student = $stmt->fetch(PDO::FETCH_ASSOC);
    } else {
        session_destroy();
        header('Location: login.php?error=invalid_session');
        exit();
    }
    
} catch (Exception $e) {
    error_log("Student dashboard error: " . $e->getMessage());
    $error_message = "Terjadi kesalahan sistem. Silakan coba lagi nanti.";
}

// Asumsi file database.php sudah ada dan berfungsi

$leaderboard_data = [];
$error_message = null;

try {
    // Query untuk mengambil data leaderboard dengan filter umur <= 18 tahun
    $query = "
        SELECT 
            name, 
            score,
            date_of_birth,
            TIMESTAMPDIFF(YEAR, date_of_birth, CURDATE()) AS age
        FROM 
            students
        WHERE 
            status = 'accepted' AND TIMESTAMPDIFF(YEAR, date_of_birth, CURDATE()) <= 18
        ORDER BY 
            date_of_birth ASC, score DESC
    ";
    
    $stmt = $db->prepare($query);
    $stmt->execute();
    
    $leaderboard_data = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    // Log error untuk debugging
    error_log("Leaderboard error: " . $e->getMessage());
    $error_message = "Terjadi kesalahan saat mengambil data leaderboard. Silakan coba lagi nanti.";
}
include 'includes/header.php';
?>

<style>
.student-dashboard {
    min-height: 100vh;
    background: linear-gradient(135deg, #f8fff8 0%, #e8f5e9 100%);
    padding-top: 80px;
}

.student-header {
    background: linear-gradient(135deg, #4caf50, #388e3c);
    color: white;
    padding: 2rem 0;
    margin-bottom: 2rem;
    box-shadow: 0 4px 15px rgba(76, 175, 80, 0.3);
}

.student-welcome {
    text-align: center;
}

.student-welcome h1 {
    font-size: 2.5rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.student-welcome p {
    font-size: 1.1rem;
    opacity: 0.9;
}

.student-content {
    padding: 0 1rem;
}

.student-card {
    background: white;
    border-radius: 15px;
    padding: 2rem;
    margin-bottom: 2rem;
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
}

.student-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 35px rgba(0, 0, 0, 0.15);
}

.student-info {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 2rem;
    margin-bottom: 2rem;
}

.info-item {
    background: white;
    border-radius: 15px;
    padding: 1.5rem;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    border-left: 4px solid #4caf50;
}

.info-label {
    font-weight: 600;
    color: #2c5530;
    margin-bottom: 0.5rem;
    font-size: 0.9rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.info-value {
    font-size: 1.1rem;
    color: #333;
    font-weight: 500;
}

.status-badge {
    display: inline-block;
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 600;
    text-transform: uppercase;
}

.status-pending {
    background: #fff3cd;
    color: #856404;
}

.status-accepted {
    background: #d4edda;
    color: #155724;
}

.status-rejected {
    background: #f8d7da;
    color: #721c24;
}

.status-confirmed {
    background: #d1ecf1;
    color: #0c5460;
}

.student-actions {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1rem;
    margin-top: 2rem;
}

.leaderboard-container {
    padding: 80px 0;
    min-height: 100vh;
    background-color: #f0f2f5;
}

.leaderboard-header {
    background: linear-gradient(135deg, #2196f3, #1976d2);
    color: white;
    padding: 2rem 0;
    text-align: center;
    margin-bottom: 2rem;
    box-shadow: 0 4px 15px rgba(33, 150, 243, 0.3);
}

.leaderboard-header h1 {
    font-size: 2.5rem;
    font-weight: 700;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.leaderboard-table-card {
    background: white;
    border-radius: 15px;
    padding: 2rem;
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
}

.table-leaderboard th, .table-leaderboard td {
    vertical-align: middle;
}

.table-leaderboard th {
    background-color: #e9ecef;
    font-weight: 600;
    color: #333;
}

.leaderboard-rank {
    font-size: 1.25rem;
    font-weight: bold;
    color: #4caf50;
}

.first-place, .second-place, .third-place {
    background-color: #fffde7;
}

.first-place .leaderboard-rank {
    color: #ffc107;
    font-size: 1.5rem;
}

.second-place .leaderboard-rank {
    color: #bdbdbd;
}

.third-place .leaderboard-rank {
    color: #cd7f32;
}

.action-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 1rem;
    border-radius: 10px;
    text-decoration: none;
    color: white;
    font-weight: 600;
    transition: all 0.3s ease;
    border: none;
    cursor: pointer;
}

.action-btn:hover {
    transform: translateY(-2px);
    text-decoration: none;
    color: white;
}

.btn-primary-custom {
    background: linear-gradient(135deg, #4caf50, #388e3c);
}

.btn-secondary-custom {
    background: linear-gradient(135deg, #2196f3, #1976d2);
}

.btn-warning-custom {
    background: linear-gradient(135deg, #ff9800, #f57c00);
}

.btn-danger-custom {
    background: linear-gradient(135deg, #f44336, #d32f2f);
}

@media (max-width: 768px) {
    .student-welcome h1 {
        font-size: 2rem;
    }
    
    .student-info {
        grid-template-columns: 1fr;
    }
    
    .student-actions {
        grid-template-columns: 1fr;
    }
}
</style>

<div class="student-dashboard">
    <div class="student-header">
        <div class="container">
            <div class="student-welcome">
                <h1>Selamat Datang, <?php echo htmlspecialchars($student['name']); ?>!</h1>
                <p>Dashboard Siswa SMP Bina Informatika</p>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="student-content">
            <?php if (isset($error_message)): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    <?php echo htmlspecialchars($error_message); ?>
                </div>
            <?php endif; ?>

            <div class="student-info">
                <div class="info-item">
                    <div class="info-label">Nama Lengkap</div>
                    <div class="info-value"><?php echo htmlspecialchars($student['name']); ?></div>
                </div>
                
                <div class="info-item">
                    <div class="info-label">Username</div>
                    <div class="info-value"><?php echo htmlspecialchars($student['username']); ?></div>
                </div>
                
                <div class="info-item">
                    <div class="info-label">Status Pendaftaran</div>
                    <div class="info-value">
                        <span class="status-badge status-<?php echo $student['status']; ?>">
                            <?php 
                            switch($student['status']) {
                                case 'pending': echo 'Menunggu Verifikasi'; break;
                                case 'accepted': echo 'Diterima'; break;
                                case 'rejected': echo 'Ditolak'; break;
                                default: echo ucfirst($student['status']);
                            }
                            ?>
                        </span>
                    </div>
                </div>
                
                <div class="info-item">
                    <div class="info-label">Status Pembayaran</div>
                    <div class="info-value">
                        <span class="status-badge status-<?php echo $student['payment_status'] ?? 'unpaid'; ?>">
                            <?php 
                            $payment_status = $student['payment_status'] ?? 'unpaid';
                            switch($payment_status) {
                                case 'unpaid': echo 'Belum Bayar'; break;
                                case 'pending': echo 'Menunggu Konfirmasi'; break;
                                case 'paid': echo 'Sudah Bayar'; break;
                                case 'verified': echo 'Terverifikasi'; break;
                                case 'confirmed': echo 'Dikonfirmasi'; break;
                                case 'rejected': echo 'Ditolak'; break;
                                default: echo ucfirst($payment_status);
                            }
                            ?>
                        </span>
                    </div>
                </div>
                
                <div class="info-item">
                    <div class="info-label">Tanggal Pendaftaran</div>
                    <div class="info-value">
                        <?php echo date('d F Y', strtotime($student['registration_date'])); ?>
                    </div>
                </div>
                
                <div class="info-item">
                    <div class="info-label">Terakhir Login</div>
                    <div class="info-value">
                        <?php echo isset($student['last_login']) ? date('d F Y H:i', strtotime($student['last_login'])) : 'Belum pernah login'; ?>
                    </div>
                </div>
            </div>

            <div class="student-card">
                <h3 class="mb-3">
                    <i class="fas fa-info-circle me-2"></i>
                    Informasi Penting
                </h3>
                <?php if (($student['payment_status'] ?? 'unpaid') === 'rejected'): ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-times-circle me-2"></i>
                        <strong>Bukti pembayaran Anda ditolak.</strong><br>
                        <?php if (!empty($student['notes'])): ?>
                            Alasan penolakan: <em><?php echo htmlspecialchars($student['notes']); ?></em>
                        <?php else: ?>
                            Silakan unggah ulang bukti pembayaran sesuai ketentuan yang benar.
                        <?php endif; ?>
                        <div class="mt-2">
                            <a href="payment.php" class="btn btn-sm btn-danger">
                                <i class="fas fa-upload me-1"></i> Unggah Ulang Bukti Pembayaran
                            </a>
                        </div>
                    </div>
                <?php endif; ?>
                
                <?php if ($student['status'] === 'pending'): ?>
                    <div class="alert alert-warning">
                        <i class="fas fa-clock me-2"></i>
                        <strong>Status Anda masih dalam proses verifikasi.</strong><br>
                        Tim admin akan memverifikasi data pendaftaran Anda dalam waktu 1-3 hari kerja.
                        Anda akan mendapat notifikasi melalui email atau WhatsApp setelah verifikasi selesai.
                    </div>
                <?php elseif ($student['status'] === 'accepted'): ?>
                    <?php if (($student['payment_status'] ?? 'unpaid') !== 'rejected'): ?>
                        <?php if (!$student['payment_proof']): ?>
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle me-2"></i>
                            <strong>Selamat! Pendaftaran Anda telah diterima.</strong><br>
                            Silakan lakukan pembayaran dan upload bukti pembayaran untuk melanjutkan proses pendaftaran.
                        </div>
                        <?php else: ?>
                        <div class="alert alert-info">
                            <i class="fas fa-upload me-2"></i>
                            <strong>Bukti pembayaran telah diupload.</strong><br>
                            Tim admin akan memverifikasi bukti pembayaran Anda dalam waktu 1-3 hari kerja.
                            Status akan diperbarui setelah verifikasi selesai.
                        </div>
                        <?php endif; ?>
                    <?php endif; ?>
                <?php elseif ($student['status'] === 'rejected'): ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-times-circle me-2"></i>
                        <strong>Mohon maaf, pendaftaran Anda tidak dapat diproses.</strong><br>
                        Silakan hubungi admin untuk informasi lebih lanjut.
                    </div>
                <?php endif; ?>
            </div>

            <div class="student-actions">
                <a href="index.php" class="action-btn btn-primary-custom">
                    <i class="fas fa-home me-2"></i>
                    Kembali ke Beranda
                </a>
                
                <?php if (!$student['payment_proof'] && $student['status'] === 'accepted'): ?>
                <a href="upload_payment.php" class="action-btn btn-secondary-custom">
                    <i class="fas fa-upload me-2"></i>
                    Upload Bukti Pembayaran
                </a>
                <?php endif; ?>
                
                <a href="index.php#informasi" class="action-btn btn-warning-custom">
                    <i class="fas fa-info-circle me-2"></i>
                    Informasi Sekolah
                </a>
                
                <a href="index.php#gallery" class="action-btn btn-warning-custom">
                    <i class="fas fa-images me-2"></i>
                    Galeri Sekolah
                </a>
                
                <?php if (($student['payment_status'] ?? 'unpaid') === 'rejected'): ?>
                <a href="payment.php" class="action-btn btn-danger-custom">
                    <i class="fas fa-upload me-2"></i>
                    Unggah Ulang Bukti Pembayaran
                </a>
                <?php endif; ?>

                <a href="logout.php" class="action-btn btn-danger-custom">
                    <i class="fas fa-sign-out-alt me-2"></i>
                    Logout
                </a>
            </div>
        </div>
        <div class="leaderboard-container">
    <div class="leaderboard-header">
        <div class="container">
            <h1>🥇 Leaderboard Siswa Berprestasi</h1>
            <p>Siswa dengan nilai tertinggi dan usia maksimal 18 tahun.</p>
        </div>
    </div>
    
    <div class="container">
        <div class="leaderboard-table-card">
            <?php if ($error_message): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    <?php echo htmlspecialchars($error_message); ?>
                </div>
            <?php else: ?>
                <?php if (count($leaderboard_data) > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover table-leaderboard">
                            <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Nama Siswa</th>
                                    <th scope="col">Nilai</th>
                                    <th scope="col">Usia</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $rank = 1; ?>
                                <?php foreach ($leaderboard_data as $student): ?>
                                <tr class="<?php 
                                    if ($rank === 1) echo 'first-place';
                                    elseif ($rank === 2) echo 'second-place';
                                    elseif ($rank === 3) echo 'third-place';
                                ?>">
                                    <td class="leaderboard-rank"><?php echo $rank++; ?></td>
                                    <td><?php echo htmlspecialchars($student['name']); ?></td>
                                    <td><?php echo htmlspecialchars($student['score']); ?></td>
                                    <td><?php echo htmlspecialchars($student['age']); ?> tahun</td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info text-center">
                        <i class="fas fa-info-circle me-2"></i>
                        Belum ada data siswa yang memenuhi kriteria leaderboard.
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</div>
    </div>
</div>

<?php include 'includes/footer.php'; ?> 