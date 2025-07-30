<?php
/**
 * Student Dashboard
 * SMP Bina Informatika
 */

session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'student') {
    header('Location: login.php');
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
        // Student not found, logout
        session_destroy();
        header('Location: login.php?error=invalid_session');
        exit();
    }
    
} catch (Exception $e) {
    error_log("Dashboard error: " . $e->getMessage());
    $error_message = "Terjadi kesalahan sistem. Silakan coba lagi nanti.";
}

include 'includes/header.php';
?>

<style>
.dashboard-page {
    min-height: 100vh;
    background: linear-gradient(135deg, #f8fff8 0%, #e8f5e9 100%);
    padding-top: 80px;
}

.dashboard-header {
    background: linear-gradient(135deg, #4caf50, #388e3c);
    color: white;
    padding: 2rem 0;
    margin-bottom: 2rem;
    box-shadow: 0 4px 15px rgba(76, 175, 80, 0.3);
}

.dashboard-welcome {
    text-align: center;
}

.dashboard-welcome h1 {
    font-size: 2.5rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.dashboard-welcome p {
    font-size: 1.1rem;
    opacity: 0.9;
}

.dashboard-content {
    padding: 0 1rem;
}

.dashboard-card {
    background: white;
    border-radius: 15px;
    padding: 2rem;
    margin-bottom: 2rem;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
    border: 1px solid rgba(76, 175, 80, 0.1);
}

.dashboard-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
    border-color: rgba(76, 175, 80, 0.3);
}

.dashboard-card h3 {
    color: #2c5530;
    font-weight: 700;
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.dashboard-card h3 i {
    color: #4caf50;
}

.info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.info-item {
    background: #f8f9fa;
    padding: 1.5rem;
    border-radius: 10px;
    border-left: 4px solid #4caf50;
    transition: all 0.3s ease;
}

.info-item:hover {
    background: #e8f5e9;
    transform: translateX(5px);
}

.info-label {
    font-weight: 600;
    color: #666;
    margin-bottom: 0.5rem;
    font-size: 0.9rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.info-value {
    font-weight: 700;
    color: #2c5530;
    font-size: 1.1rem;
}

.status-badge {
    padding: 0.5rem 1rem;
    border-radius: 25px;
    font-weight: 600;
    font-size: 0.9rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
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

.status-completed {
    background: #cce5ff;
    color: #004085;
}

.action-buttons {
    display: flex;
    gap: 1rem;
    flex-wrap: wrap;
    margin-top: 1.5rem;
}

.btn-action {
    padding: 0.75rem 1.5rem;
    border-radius: 25px;
    text-decoration: none;
    font-weight: 600;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}

.btn-primary-action {
    background: linear-gradient(135deg, #4caf50, #388e3c);
    color: white;
    border: none;
}

.btn-primary-action:hover {
    background: linear-gradient(135deg, #388e3c, #2e7d32);
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(76, 175, 80, 0.3);
}

.btn-secondary-action {
    background: transparent;
    color: #4caf50;
    border: 2px solid #4caf50;
}

.btn-secondary-action:hover {
    background: #4caf50;
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(76, 175, 80, 0.3);
}

.progress-section {
    margin-top: 2rem;
}

.progress-item {
    margin-bottom: 1.5rem;
}

.progress-label {
    display: flex;
    justify-content: space-between;
    margin-bottom: 0.5rem;
    font-weight: 600;
    color: #2c5530;
}

.progress-bar {
    height: 8px;
    background: #e9ecef;
    border-radius: 4px;
    overflow: hidden;
}

.progress-fill {
    height: 100%;
    background: linear-gradient(135deg, #4caf50, #388e3c);
    border-radius: 4px;
    transition: width 0.3s ease;
}

.timeline {
    margin-top: 2rem;
}

.timeline-item {
    display: flex;
    gap: 1rem;
    margin-bottom: 1.5rem;
    padding: 1rem;
    background: #f8f9fa;
    border-radius: 10px;
    border-left: 4px solid #4caf50;
}

.timeline-icon {
    width: 40px;
    height: 40px;
    background: #4caf50;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.2rem;
    flex-shrink: 0;
}

.timeline-content h5 {
    color: #2c5530;
    font-weight: 700;
    margin-bottom: 0.25rem;
}

.timeline-content p {
    color: #666;
    margin-bottom: 0;
    font-size: 0.9rem;
}

.timeline-date {
    color: #888;
    font-size: 0.8rem;
    font-style: italic;
}

@media (max-width: 768px) {
    .dashboard-welcome h1 {
        font-size: 2rem;
    }
    
    .dashboard-content {
        padding: 0 0.5rem;
    }
    
    .dashboard-card {
        padding: 1.5rem;
    }
    
    .info-grid {
        grid-template-columns: 1fr;
    }
    
    .action-buttons {
        flex-direction: column;
    }
    
    .btn-action {
        justify-content: center;
    }
}
</style>

<div class="dashboard-page">
    <div class="dashboard-header">
        <div class="container">
            <div class="dashboard-welcome">
                <h1>Selamat Datang, <?php echo htmlspecialchars($student['name']); ?>!</h1>
                <p>Dashboard Siswa SMP Bina Informatika</p>
            </div>
        </div>
    </div>
    
    <div class="container">
        <div class="dashboard-content">
            <?php if (isset($error_message)): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    <?php echo htmlspecialchars($error_message); ?>
                </div>
            <?php endif; ?>
            
            <!-- Student Information Card -->
            <div class="dashboard-card">
                <h3>
                    <i class="fas fa-user-graduate"></i>
                    Informasi Siswa
                </h3>
                
                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-label">Username</div>
                        <div class="info-value"><?php echo htmlspecialchars($student['username']); ?></div>
                    </div>
                    
                    <div class="info-item">
                        <div class="info-label">Tahun Ajaran</div>
                        <div class="info-value"><?php echo htmlspecialchars($student['academic_year']); ?></div>
                    </div>
                    
                    <div class="info-item">
                        <div class="info-label">Kelas</div>
                        <div class="info-value"><?php echo htmlspecialchars($student['class']); ?></div>
                    </div>
                    
                    <div class="info-item">
                        <div class="info-label">Email Orang Tua</div>
                        <div class="info-value"><?php echo htmlspecialchars($student['parent_email']); ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">No. HP Orang Tua</div>
                        <div class="info-value"><?php echo htmlspecialchars($student['parent_phone']); ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Asal Sekolah</div>
                        <div class="info-value"><?php echo htmlspecialchars($student['origin_school']); ?></div>
                    </div>
                    
                    <div class="info-item">
                        <div class="info-label">Status</div>
                        <div class="info-value">
                            <span class="status-badge status-<?php echo $student['status']; ?>">
                                <?php 
                                $status_labels = [
                                    'pending' => 'Menunggu',
                                    'accepted' => 'Diterima',
                                    'rejected' => 'Ditolak',
                                    'completed' => 'Selesai'
                                ];
                                echo $status_labels[$student['status']] ?? $student['status'];
                                ?>
                            </span>
                        </div>
                    </div>
                </div>
                
                <div class="action-buttons">
                    <a href="profile.php" class="btn-action btn-primary-action">
                        <i class="fas fa-user-edit"></i>
                        Edit Profil
                    </a>
                    <a href="documents.php" class="btn-action btn-secondary-action">
                        <i class="fas fa-file-alt"></i>
                        Dokumen
                    </a>
                </div>
            </div>
            
            <!-- Registration Progress Card -->
            <div class="dashboard-card">
                <h3>
                    <i class="fas fa-tasks"></i>
                    Progress Pendaftaran
                </h3>
                
                <div class="progress-section">
                    <div class="progress-item">
                        <div class="progress-label">
                            <span>Form Pendaftaran</span>
                            <span>100%</span>
                        </div>
                        <div class="progress-bar">
                            <div class="progress-fill" style="width: 100%"></div>
                        </div>
                    </div>
                    
                    <div class="progress-item">
                        <div class="progress-label">
                            <span>Pembayaran</span>
                            <span><?php echo $student['payment_status'] === 'paid' ? '100%' : '0%'; ?></span>
                        </div>
                        <div class="progress-bar">
                            <div class="progress-fill" style="width: <?php echo $student['payment_status'] === 'paid' ? '100' : '0'; ?>%"></div>
                        </div>
                    </div>
                    
                    <div class="progress-item">
                        <div class="progress-label">
                            <span>Verifikasi Dokumen</span>
                            <span><?php echo $student['status'] === 'completed' ? '100%' : '0%'; ?></span>
                        </div>
                        <div class="progress-bar">
                            <div class="progress-fill" style="width: <?php echo $student['status'] === 'completed' ? '100' : '0'; ?>%"></div>
                        </div>
                    </div>
                </div>
                
                <?php if ($student['payment_status'] !== 'paid'): ?>
                <div class="action-buttons">
                    <a href="payment.php" class="btn-action btn-primary-action">
                        <i class="fas fa-credit-card"></i>
                        Lakukan Pembayaran
                    </a>
                </div>
                <?php endif; ?>
            </div>
            
            <!-- Payment Information Card -->
            <div class="dashboard-card">
                <h3>
                    <i class="fas fa-money-bill-wave"></i>
                    Informasi Pembayaran
                </h3>
                
                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-label">Status Pembayaran</div>
                        <div class="info-value">
                            <span class="status-badge status-<?php echo $student['payment_status']; ?>">
                                <?php 
                                $payment_labels = [
                                    'unpaid' => 'Belum Bayar',
                                    'paid' => 'Sudah Bayar',
                                    'verified' => 'Terverifikasi'
                                ];
                                echo $payment_labels[$student['payment_status']] ?? $student['payment_status'];
                                ?>
                            </span>
                        </div>
                    </div>
                    
                    <div class="info-item">
                        <div class="info-label">Bank Tujuan</div>
                        <div class="info-value">BRI</div>
                    </div>
                    
                    <div class="info-item">
                        <div class="info-label">Nomor Rekening</div>
                        <div class="info-value">70148022526</div>
                    </div>
                    
                    <div class="info-item">
                        <div class="info-label">Jumlah Pembayaran</div>
                        <div class="info-value">Rp 500.000</div>
                    </div>
                </div>
                
                <?php if ($student['payment_status'] === 'unpaid'): ?>
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Penting:</strong> Silakan lakukan pembayaran untuk melanjutkan proses pendaftaran.
                </div>
                <?php endif; ?>
            </div>
            
            <!-- Quick Actions Card -->
            <div class="dashboard-card">
                <h3>
                    <i class="fas fa-bolt"></i>
                    Aksi Cepat
                </h3>
                
                <div class="action-buttons">
                    <a href="payment.php" class="btn-action btn-primary-action">
                        <i class="fas fa-credit-card"></i>
                        Pembayaran
                    </a>
                    
                    <a href="documents.php" class="btn-action btn-secondary-action">
                        <i class="fas fa-upload"></i>
                        Upload Dokumen
                    </a>
                    
                    <a href="status.php" class="btn-action btn-secondary-action">
                        <i class="fas fa-chart-line"></i>
                        Cek Status
                    </a>
                    
                    <a href="contact.php" class="btn-action btn-secondary-action">
                        <i class="fas fa-headset"></i>
                        Bantuan
                    </a>
                </div>
            </div>
            
            <!-- Recent Activity Card -->
            <div class="dashboard-card">
                <h3>
                    <i class="fas fa-history"></i>
                    Aktivitas Terbaru
                </h3>
                
                <div class="timeline">
                    <div class="timeline-item">
                        <div class="timeline-icon"><i class="fas fa-check-circle"></i></div>
                        <div class="timeline-content">
                            <h5>Pendaftaran Berhasil</h5>
                            <p>Akun pendaftaran berhasil dibuat dengan username: <?php echo htmlspecialchars($student['username']); ?></p>
                            <div class="timeline-date"><?php echo date('d M Y H:i', strtotime($student['registration_date'])); ?></div>
                        </div>
                    </div>
                    
                    <?php if ($student['payment_date']): ?>
                    <div class="timeline-item">
                        <div class="timeline-icon"><i class="fas fa-file-invoice-dollar"></i></div>
                        <div class="timeline-content">
                            <h5>Pembayaran Diterima</h5>
                            <p>Bukti pembayaran telah diupload dan sedang diverifikasi</p>
                            <div class="timeline-date"><?php echo date('d M Y H:i', strtotime($student['payment_date'])); ?></div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.timeline {
    position: relative;
    padding-left: 2rem;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 0.5rem;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #e9ecef;
}

.timeline-item {
    position: relative;
    margin-bottom: 2rem;
}

.timeline-marker {
    position: absolute;
    left: -1.5rem;
    top: 0.5rem;
    width: 1rem;
    height: 1rem;
    background: #4caf50;
    border-radius: 50%;
    border: 3px solid white;
    box-shadow: 0 0 0 2px #4caf50;
}

.timeline-content {
    background: #f8f9fa;
    padding: 1rem;
    border-radius: 10px;
    border-left: 4px solid #4caf50;
}

.timeline-title {
    font-weight: 700;
    color: #2c5530;
    margin-bottom: 0.25rem;
}

.timeline-date {
    font-size: 0.9rem;
    color: #666;
    margin-bottom: 0.5rem;
}

.timeline-description {
    color: #666;
    font-size: 0.95rem;
    line-height: 1.5;
}
</style>

<script>
// Add some interactivity
document.addEventListener('DOMContentLoaded', function() {
    // Animate progress bars
    const progressBars = document.querySelectorAll('.progress-fill');
    progressBars.forEach(bar => {
        const width = bar.style.width;
        bar.style.width = '0%';
        setTimeout(() => {
            bar.style.width = width;
        }, 500);
    });
    
    // Add click handlers for action buttons
    document.querySelectorAll('.btn-action').forEach(btn => {
        btn.addEventListener('click', function(e) {
            // Add ripple effect
            const ripple = document.createElement('span');
            const rect = this.getBoundingClientRect();
            const size = Math.max(rect.width, rect.height);
            const x = e.clientX - rect.left - size / 2;
            const y = e.clientY - rect.top - size / 2;
            
            ripple.style.width = ripple.style.height = size + 'px';
            ripple.style.left = x + 'px';
            ripple.style.top = y + 'px';
            ripple.classList.add('ripple');
            
            this.appendChild(ripple);
            
            setTimeout(() => {
                ripple.remove();
            }, 600);
        });
    });
});

// Add CSS for ripple effect
const style = document.createElement('style');
style.textContent = `
    .btn-action {
        position: relative;
        overflow: hidden;
    }
    
    .ripple {
        position: absolute;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.3);
        transform: scale(0);
        animation: ripple-animation 0.6s linear;
        pointer-events: none;
    }
    
    @keyframes ripple-animation {
        to {
            transform: scale(4);
            opacity: 0;
        }
    }
`;
document.head.appendChild(style);
</script>

<?php include 'includes/footer.php'; ?> 