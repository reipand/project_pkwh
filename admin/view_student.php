<?php
/**
 * Halaman Detail Siswa
 * SMP Bina Informatika
 */

session_start();

// Check if user is logged in as admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header('Location: ../login.php?error=unauthorized');
    exit();
}

$page_title = "Detail Siswa";

// Get student ID from URL
$student_id = $_GET['id'] ?? null;

if (!$student_id) {
    header('Location: dashboard.php');
    exit();
}

try {
    require_once '../config/database.php';
    $database = new Database();
    $db = $database->getConnection();
    
    // Get student data
    $query = "SELECT * FROM students WHERE id = ?";
    $stmt = $db->prepare($query);
    $stmt->bindParam(1, $student_id);
    $stmt->execute();
    
    if ($stmt->rowCount() === 0) {
        header('Location: dashboard.php?error=student_not_found');
        exit();
    }
    
    $student = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Get admin data
    $admin_id = $_SESSION['user_id'];
    $adminQuery = "SELECT * FROM users WHERE id = ?";
    $adminStmt = $db->prepare($adminQuery);
    $adminStmt->bindParam(1, $admin_id);
    $adminStmt->execute();
    $admin = $adminStmt->fetch(PDO::FETCH_ASSOC);
    
} catch (Exception $e) {
    error_log("View student error: " . $e->getMessage());
    $error_message = "Terjadi kesalahan sistem. Silakan coba lagi nanti.";
}

include '../includes/header.php';
?>

<style>
.student-detail-page {
    min-height: 100vh;
    background: linear-gradient(135deg, #f8fff8 0%, #e8f5e9 100%);
    padding-top: 80px;
}

.student-header {
    background: linear-gradient(135deg, #2196f3, #1976d2);
    color: white;
    padding: 2rem 0;
    margin-bottom: 2rem;
    box-shadow: 0 4px 15px rgba(33, 150, 243, 0.3);
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
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
    padding: 2rem;
    margin-bottom: 2rem;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.student-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.15);
}

.student-card h3 {
    color: #2196f3;
    font-size: 1.5rem;
    font-weight: 600;
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 1.5rem;
}

.info-item {
    background: #f8f9fa;
    padding: 1rem;
    border-radius: 8px;
    border-left: 4px solid #2196f3;
}

.info-label {
    font-weight: 600;
    color: #333;
    margin-bottom: 0.5rem;
    font-size: 0.9rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.info-value {
    color: #666;
    font-size: 1rem;
}

.status-badge {
    display: inline-block;
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-size: 0.9rem;
    font-weight: 500;
    text-transform: uppercase;
    margin-top: 0.5rem;
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

.action-buttons {
    display: flex;
    gap: 1rem;
    margin-top: 2rem;
    flex-wrap: wrap;
}

.btn-back {
    background: #6c757d;
    color: white;
    border: none;
    padding: 0.75rem 1.5rem;
    border-radius: 8px;
    cursor: pointer;
    font-size: 1rem;
    transition: background 0.3s ease;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}

.btn-back:hover {
    background: #5a6268;
    color: white;
    text-decoration: none;
}

.btn-verify {
    background: #28a745;
    color: white;
    border: none;
    padding: 0.75rem 1.5rem;
    border-radius: 8px;
    cursor: pointer;
    font-size: 1rem;
    transition: background 0.3s ease;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}

.btn-verify:hover {
    background: #218838;
}

.btn-reject {
    background: #dc3545;
    color: white;
    border: none;
    padding: 0.75rem 1.5rem;
    border-radius: 8px;
    cursor: pointer;
    font-size: 1rem;
    transition: background 0.3s ease;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}

.btn-reject:hover {
    background: #c82333;
}

.status-unpaid { background: #f8d7da; color: #721c24; }
.status-pending { background: #fff3cd; color: #856404; }
.status-confirmed { background: #d4edda; color: #155724; }
.status-rejected { background: #f8d7da; color: #721c24; }

@media (max-width: 768px) {
    .info-grid {
        grid-template-columns: 1fr;
    }
    
    .action-buttons {
        flex-direction: column;
    }
    
    .btn-back,
    .btn-verify,
    .btn-reject {
        width: 100%;
        justify-content: center;
    }
}
</style>

<div class="student-detail-page">
    <div class="student-header">
        <div class="container">
            <div class="student-welcome">
                <h1>
                    Detail Siswa
                </h1>
                <p>Informasi lengkap pendaftaran siswa</p>
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
            
            <!-- Student Information -->
            <div class="student-card">
                <h3>
                    Informasi Pribadi
                </h3>
                
                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-label">Nama Lengkap</div>
                        <div class="info-value"><?php echo htmlspecialchars($student['name']); ?></div>
                    </div>
                    
                    <div class="info-item">
                        <div class="info-label">Username</div>
                        <div class="info-value"><?php echo htmlspecialchars($student['username']); ?></div>
                    </div>
                    
                    <div class="info-item">
                        <div class="info-label">Tempat Lahir</div>
                        <div class="info-value"><?php echo htmlspecialchars($student['birth_place']); ?></div>
                    </div>
                    
                    <div class="info-item">
                        <div class="info-label">Tanggal Lahir</div>
                        <div class="info-value"><?php echo date('d/m/Y', strtotime($student['birth_date'])); ?></div>
                    </div>
                    
                    <div class="info-item">
                        <div class="info-label">NIK</div>
                        <div class="info-value"><?php echo htmlspecialchars($student['nik']); ?></div>
                    </div>
                    
                    <div class="info-item">
                        <div class="info-label">Asal Sekolah</div>
                        <div class="info-value"><?php echo htmlspecialchars($student['origin_school']); ?></div>
                    </div>
                </div>
            </div>
            
            <!-- Registration Information -->
            <div class="student-card">
                <h3>
                    Informasi Pendaftaran
                </h3>
                
                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-label">Jenis Pendaftaran</div>
                        <div class="info-value">
                            <?php 
                            switch($student['registration_type']) {
                                case 'reguler': echo 'Reguler'; break;
                                case 'prestasi': echo 'Prestasi'; break;
                                case 'beasiswa': echo 'Beasiswa'; break;
                                default: echo $student['registration_type'];
                            }
                            ?>
                        </div>
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
                        <div class="info-label">Tanggal Pendaftaran</div>
                        <div class="info-value"><?php echo date('d/m/Y H:i', strtotime($student['registration_date'])); ?></div>
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
                                    default: echo $student['status'];
                                }
                                ?>
                            </span>
                        </div>
                    </div>
                    
                    <div class="info-item">
                        <div class="info-label">Status Pembayaran</div>
                        <div class="info-value">
                            <span class="status-badge status-<?php echo $student['payment_status']; ?>">
                                <?php 
                                switch($student['payment_status']) {
                                    case 'unpaid': echo 'Belum Bayar'; break;
                                    case 'pending': echo 'Menunggu Konfirmasi'; break;
                                    case 'paid': echo 'Sudah Bayar'; break;
                                    case 'verified': echo 'Terverifikasi'; break;
                                    default: echo $student['payment_status'];
                                }
                                ?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment Verification -->
             <?php if ($student['payment_status'] === 'pending'): ?>
<div class="student-card">
    <h3><i class="fas fa-money-check-alt"></i> Verifikasi Pembayaran</h3>
    
    <div class="info-item" style="text-align: center; padding: 20px;">
        <div class="info-label">Bukti Pembayaran yang Diupload</div>
        <a href="../uploads/payments/<?php echo htmlspecialchars($student['payment_proof']); ?>" target="_blank" title="Klik untuk melihat ukuran penuh">
            <img src="../uploads/payments/<?php echo htmlspecialchars($student['payment_proof']); ?>" alt="Bukti Pembayaran" style="max-width: 100%; max-height: 400px; border-radius: 8px; margin-top: 10px; border: 1px solid #ddd;">
        </a>
        <div class="info-value" style="margin-top: 10px;">Diupload pada: <?php echo date('d M Y H:i', strtotime($student['payment_date'])); ?></div>
    </div>
    
    <form id="verificationForm" action="verify_payment.php" method="POST" style="margin-top: 20px;">
        <input type="hidden" name="student_id" value="<?php echo htmlspecialchars($student['id']); ?>">
        
        <div class="form-group" style="margin-top: 1.5rem;">
            <label for="rejection_reason" class="info-label">Alasan Penolakan (Opsional, isi jika menolak)</label>
            <textarea name="rejection_reason" id="rejection_reason" rows="3" class="form-control" placeholder="Contoh: Nominal transfer tidak sesuai, bukti pembayaran tidak jelas, dll."></textarea>
        </div>

        <div class="action-buttons">
            <button type="submit" name="action" value="confirm" class="btn-verify">
                <i class="fas fa-check-circle"></i> Konfirmasi Pembayaran
            </button>
            <button type="submit" name="action" value="reject" class="btn-reject">
                <i class="fas fa-times-circle"></i> Tolak Pembayaran
            </button>
        </div>
    </form>
</div>
<?php endif; ?>

            <!-- Parent Information -->
            <div class="student-card">
                <h3>
                    Informasi Orang Tua/Wali
                </h3>
                
                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-label">Email Orang Tua/Wali</div>
                        <div class="info-value"><?php echo htmlspecialchars($student['parent_email']); ?></div>
                    </div>
                    
                    <div class="info-item">
                        <div class="info-label">No. HP Orang Tua/Wali</div>
                        <div class="info-value"><?php echo htmlspecialchars($student['parent_phone']); ?></div>
                    </div>
                </div>
            </div>
            
            <!-- Action Buttons -->
            <div class="student-card">
                <h3>
                    Aksi
                </h3>
                
                <div class="action-buttons">
                    <a href="dashboard.php" class="btn-back">
                        <i class="fas fa-arrow-left"></i>
                        Kembali ke Dashboard
                    </a>
                    
                    <?php if ($student['status'] === 'pending'): ?>
                    <button class="btn-verify" onclick="verifyStudent(<?php echo $student['id']; ?>, 'accepted')">
                        Terima Siswa
                    </button>
                    
                    <button class="btn-reject" onclick="verifyStudent(<?php echo $student['id']; ?>, 'rejected')">
                        Tolak Siswa
                    </button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function verifyStudent(studentId, status) {
    const action = status === 'accepted' ? 'menerima' : 'menolak';
    
    if (confirm(`Apakah Anda yakin ingin ${action} pendaftaran siswa ini?`)) {
        fetch('../api/verify_student.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                student_id: studentId,
                status: status
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(`Siswa berhasil ${action}!`);
                window.location.href = 'dashboard.php';
            } else {
                alert('Terjadi kesalahan: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan sistem');
        });
    }
}

document.getElementById('verificationForm').addEventListener('submit', function(event) {
    // Mencegah form submit secara langsung
    event.preventDefault(); 
    
    const form = event.target;
    const action = document.activeElement.value; // Mendeteksi tombol mana yang diklik
    const reasonTextarea = document.getElementById('rejection_reason');
    let confirmationMessage = '';

    if (action === 'confirm') {
        confirmationMessage = 'Apakah Anda yakin ingin MENGONFIRMASI pembayaran ini?';
    } else if (action === 'reject') {
        if (reasonTextarea.value.trim() === '') {
            alert('Harap isi alasan penolakan sebelum melanjutkan.');
            reasonTextarea.focus();
            return; // Hentikan proses jika alasan kosong
        }
        confirmationMessage = `Apakah Anda yakin ingin MENOLAK pembayaran ini dengan alasan:\n"${reasonTextarea.value}"`;
    }

    // Tampilkan dialog konfirmasi
    if (confirmationMessage && confirm(confirmationMessage)) {
        // Jika admin setuju, tambahkan action ke form dan submit
        const hiddenActionInput = document.createElement('input');
        hiddenActionInput.type = 'hidden';
        hiddenActionInput.name = 'action';
        hiddenActionInput.value = action;
        form.appendChild(hiddenActionInput);
        
        // Nonaktifkan tombol untuk mencegah submit ganda
        form.querySelectorAll('button').forEach(button => button.disabled = true);
        
        form.submit();
    }
});
</script>

<?php include '../includes/footer.php'; ?>