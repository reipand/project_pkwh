<?php
/**
 * Payment Page - Simple Version
 * SMP Bina Informatika
 */

session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'student') {
    header('Location: login.php');
    exit();
}

$page_title = "Pembayaran";
$success_message = '';
$error_message = '';

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
        header('Location: login.php?error=invalid_session');
        exit();
    }
    
    // Handle payment proof upload
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_FILES['payment_proof']) && $_FILES['payment_proof']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['payment_proof'];
            $allowed_types = ['image/jpeg', 'image/png', 'image/jpg'];
            $max_size = 5 * 1024 * 1024; // 5MB
            
            // Validate file type
            if (!in_array($file['type'], $allowed_types)) {
                $error_message = 'Tipe file tidak didukung. Gunakan JPG, JPEG, atau PNG.';
            }
            // Validate file size
            elseif ($file['size'] > $max_size) {
                $error_message = 'Ukuran file terlalu besar. Maksimal 5MB.';
            }
            else {
                // Generate unique filename
                $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
                $filename = 'payment_' . $student_id . '_' . time() . '.' . $extension;
                $upload_path = 'uploads/payments/' . $filename;
                
                // Create directory if not exists
                if (!is_dir('uploads/payments/')) {
                    mkdir('uploads/payments/', 0755, true);
                }
                
                // Move uploaded file
                if (move_uploaded_file($file['tmp_name'], $upload_path)) {
                    // Update database
                    $updateQuery = "UPDATE students SET 
                                   payment_proof = ?, 
                                   payment_status = 'paid',
                                   payment_date = NOW() 
                                   WHERE id = ?";
                    $updateStmt = $db->prepare($updateQuery);
                    $updateStmt->bindParam(1, $filename);
                    $updateStmt->bindParam(2, $student_id);
                    
                    if ($updateStmt->execute()) {
                        $success_message = 'Bukti pembayaran berhasil diupload. Tim kami akan memverifikasi dalam 1-2 hari kerja.';
                        
                        // Refresh student data
                        $stmt->execute();
                        $student = $stmt->fetch(PDO::FETCH_ASSOC);
                    } else {
                        $error_message = 'Gagal menyimpan data pembayaran. Silakan coba lagi.';
                    }
                } else {
                    $error_message = 'Gagal mengupload file. Silakan coba lagi.';
                }
            }
        } else {
            $error_message = 'Silakan pilih file bukti pembayaran.';
        }
    }
    
} catch (Exception $e) {
    error_log("Payment error: " . $e->getMessage());
    $error_message = 'Terjadi kesalahan sistem. Silakan coba lagi nanti.';
}

include 'includes/header.php';
?>

<style>
.payment-page {
    background: #f8f9fa;
    min-height: 100vh;
    padding: 100px 0 50px 0;
}

.payment-container {
    max-width: 800px;
    margin: 0 auto;
    padding: 0 20px;
}

.payment-card {
    background: white;
    border-radius: 15px;
    padding: 30px;
    margin-bottom: 30px;
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
    border: 1px solid #e9ecef;
}

.payment-title {
    text-align: center;
    margin-bottom: 30px;
}

.payment-title h1 {
    color: #2c5530;
    font-size: 2.5rem;
    font-weight: 700;
    margin-bottom: 10px;
}

.payment-title p {
    color: #666;
    font-size: 1.1rem;
}

.payment-info {
    background: #f8f9fa;
    padding: 25px;
    border-radius: 10px;
    margin-bottom: 25px;
    border-left: 4px solid #4caf50;
}

.payment-info h3 {
    color: #2c5530;
    font-weight: 700;
    margin-bottom: 20px;
}

.info-row {
    display: flex;
    justify-content: space-between;
    padding: 12px 0;
    border-bottom: 1px solid #e9ecef;
}

.info-row:last-child {
    border-bottom: none;
}

.info-label {
    font-weight: 600;
    color: #555;
}

.info-value {
    font-weight: 700;
    color: #2c5530;
}

.payment-amount {
    text-align: center;
    font-size: 2.5rem;
    font-weight: 800;
    color: #4caf50;
    margin: 20px 0;
    padding: 20px;
    background: #e8f5e9;
    border-radius: 10px;
}

.bank-info {
    background: white;
    border: 2px solid #4caf50;
    border-radius: 10px;
    padding: 25px;
    margin-bottom: 25px;
}

.bank-info h4 {
    color: #2c5530;
    font-weight: 700;
    margin-bottom: 20px;
    text-align: center;
}

.upload-section {
    background: #f8f9fa;
    padding: 25px;
    border-radius: 10px;
    border: 2px dashed #4caf50;
}

.upload-area {
    text-align: center;
    padding: 40px 20px;
    border: 2px dashed #ccc;
    border-radius: 10px;
    background: white;
    cursor: pointer;
    transition: all 0.3s ease;
}

.upload-area:hover {
    border-color: #4caf50;
    background: #f8f9fa;
}

.upload-icon {
    font-size: 3rem;
    color: #4caf50;
    margin-bottom: 15px;
}

.upload-text {
    color: #555;
    margin-bottom: 10px;
    font-size: 1.1rem;
}

.upload-hint {
    color: #888;
    font-size: 0.9rem;
}

.file-input {
    display: none;
}

.btn-upload {
    background: #4caf50;
    border: none;
    border-radius: 25px;
    padding: 12px 25px;
    color: white;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    margin-top: 15px;
}

.btn-upload:hover {
    background: #388e3c;
    transform: translateY(-2px);
}

.btn-submit {
    background: #4caf50;
    border: none;
    border-radius: 25px;
    padding: 15px 30px;
    color: white;
    font-weight: 600;
    font-size: 1.1rem;
    width: 100%;
    transition: all 0.3s ease;
    margin-top: 20px;
}

.btn-submit:hover {
    background: #388e3c;
    transform: translateY(-2px);
}

.btn-submit:disabled {
    background: #ccc;
    cursor: not-allowed;
    transform: none;
}

.preview-image {
    max-width: 100%;
    max-height: 300px;
    border-radius: 10px;
    margin-top: 20px;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
}

.alert {
    padding: 15px;
    border-radius: 10px;
    margin-bottom: 20px;
    font-weight: 500;
}

.alert-success {
    background: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

.alert-danger {
    background: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}

.alert-warning {
    background: #fff3cd;
    color: #856404;
    border: 1px solid #ffeaa7;
}

.instructions {
    background: white;
    padding: 25px;
    border-radius: 10px;
    border: 1px solid #e9ecef;
}

.instructions h4 {
    color: #2c5530;
    font-weight: 700;
    margin-bottom: 20px;
}

.instructions ol {
    color: #555;
    line-height: 1.8;
}

.instructions strong {
    color: #2c5530;
}

@media (max-width: 768px) {
    .payment-title h1 {
        font-size: 2rem;
    }
    
    .payment-container {
        padding: 0 15px;
    }
    
    .payment-card {
        padding: 20px;
    }
    
    .info-row {
        flex-direction: column;
        gap: 5px;
    }
    
    .payment-amount {
        font-size: 2rem;
    }
    
    .upload-area {
        padding: 30px 15px;
    }
    
    .upload-icon {
        font-size: 2.5rem;
    }
}
</style>

<div class="payment-page">
    <div class="payment-container">
        <div class="payment-title">
            <h1>Pembayaran Pendaftaran</h1>
            <p>Upload bukti pembayaran untuk melanjutkan proses pendaftaran</p>
        </div>
        
        <?php if ($success_message): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle me-2"></i>
                <?php echo htmlspecialchars($success_message); ?>
            </div>
        <?php endif; ?>
        
        <?php if ($error_message): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle me-2"></i>
                <?php echo htmlspecialchars($error_message); ?>
            </div>
        <?php endif; ?>
        
        <!-- Payment Information -->
        <div class="payment-card">
            <div class="payment-info">
                <h3>Detail Pembayaran</h3>
                
                <div class="info-row">
                    <span class="info-label">Nama Siswa:</span>
                    <span class="info-value"><?php echo htmlspecialchars($student['name']); ?></span>
                </div>
                
                <div class="info-row">
                    <span class="info-label">Username:</span>
                    <span class="info-value"><?php echo htmlspecialchars($student['username']); ?></span>
                </div>
                
                <div class="info-row">
                    <span class="info-label">Tahun Ajaran:</span>
                    <span class="info-value"><?php echo htmlspecialchars($student['academic_year']); ?></span>
                </div>
                
                <div class="info-row">
                    <span class="info-label">Kelas:</span>
                    <span class="info-value"><?php echo htmlspecialchars($student['class']); ?></span>
                </div>
                
                <div class="payment-amount">
                    Rp 500.000
                </div>
            </div>
            
            <div class="bank-info">
                <h4>Transfer ke Bank BRI</h4>
                
                <div class="info-row">
                    <span class="info-label">Bank:</span>
                    <span class="info-value">BRI</span>
                </div>
                
                <div class="info-row">
                    <span class="info-label">Nomor Rekening:</span>
                    <span class="info-value">70148022526</span>
                </div>
                
                <div class="info-row">
                    <span class="info-label">Atas Nama:</span>
                    <span class="info-value">SMP Bina Informatika</span>
                </div>
                
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Penting:</strong> Pastikan transfer dilakukan dengan nominal yang tepat (Rp 500.000) dan simpan bukti transfer.
                </div>
            </div>
        </div>
        
        <!-- Upload Section -->
        <?php if ($student['payment_status'] !== 'paid'): ?>
        <div class="payment-card">
            <h3 style="color: #2c5530; margin-bottom: 20px;">
                <i class="fas fa-upload me-2"></i>
                Upload Bukti Pembayaran
            </h3>
            
            <form method="POST" action="payment.php" enctype="multipart/form-data" id="paymentForm">
                <div class="upload-section">
                    <div class="upload-area" id="uploadArea">
                        <div class="upload-icon">
                            <i class="fas fa-cloud-upload-alt"></i>
                        </div>
                        <div class="upload-text">
                            Klik atau drag & drop file bukti pembayaran di sini
                        </div>
                        <div class="upload-hint">
                            Format: JPG, JPEG, PNG (Maksimal 5MB)
                        </div>
                        <input type="file" name="payment_proof" id="paymentProof" class="file-input" accept="image/*" required>
                    </div>
                    
                    <div id="previewContainer" style="display: none;">
                        <img id="previewImage" class="preview-image" alt="Preview">
                    </div>
                    
                    <button type="submit" class="btn-submit" id="submitBtn" disabled>
                        <i class="fas fa-upload me-2"></i>
                        Upload Bukti Pembayaran
                    </button>
                </div>
            </form>
        </div>
        <?php else: ?>
        <div class="payment-card">
            <h3 style="color: #2c5530; margin-bottom: 20px;">
                <i class="fas fa-check-circle me-2"></i>
                Status Pembayaran
            </h3>
            
            <div class="alert alert-success">
                <i class="fas fa-check-circle me-2"></i>
                <strong>Pembayaran telah diupload!</strong> Tim kami sedang memverifikasi bukti pembayaran Anda. Proses verifikasi memakan waktu 1-2 hari kerja.
            </div>
            
            <?php if ($student['payment_proof']): ?>
            <div class="text-center">
                <h5 style="color: #2c5530; margin-bottom: 15px;">Bukti Pembayaran yang Diupload:</h5>
                <img src="uploads/payments/<?php echo htmlspecialchars($student['payment_proof']); ?>" 
                     alt="Bukti Pembayaran" class="preview-image" style="max-width: 300px;">
                <p style="color: #666; margin-top: 10px;">Upload pada: <?php echo date('d M Y H:i', strtotime($student['payment_date'])); ?></p>
            </div>
            <?php endif; ?>
        </div>
        <?php endif; ?>
        
        <!-- Instructions -->
        <div class="payment-card">
            <div class="instructions">
                <h4>
                    <i class="fas fa-list-ol me-2"></i>
                    Cara Pembayaran
                </h4>
                
                <div class="row">
                    <div class="col-md-6">
                        <h5 style="color: #2c5530; margin-bottom: 15px;">Melalui ATM BRI:</h5>
                        <ol>
                            <li>Masukkan kartu ATM dan PIN</li>
                            <li>Pilih menu "Transfer"</li>
                            <li>Pilih "Ke Rekening BRI"</li>
                            <li>Masukkan nomor rekening: <strong>70148022526</strong></li>
                            <li>Masukkan nominal: <strong>Rp 500.000</strong></li>
                            <li>Konfirmasi dan selesai</li>
                            <li>Simpan bukti transfer</li>
                        </ol>
                    </div>
                    
                    <div class="col-md-6">
                        <h5 style="color: #2c5530; margin-bottom: 15px;">Melalui Mobile Banking:</h5>
                        <ol>
                            <li>Buka aplikasi BRI Mobile</li>
                            <li>Login dengan User ID dan MPIN</li>
                            <li>Pilih menu "Transfer"</li>
                            <li>Pilih "Transfer ke BRI"</li>
                            <li>Masukkan nomor rekening: <strong>70148022526</strong></li>
                            <li>Masukkan nominal: <strong>Rp 500.000</strong></li>
                            <li>Konfirmasi dan selesai</li>
                            <li>Simpan bukti transfer</li>
                        </ol>
                    </div>
                </div>
                
                <div class="alert alert-warning" style="margin-top: 20px;">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>Catatan:</strong> Setelah melakukan pembayaran, silakan upload bukti transfer di atas. Tim kami akan memverifikasi dalam 1-2 hari kerja.
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const uploadArea = document.getElementById('uploadArea');
    const fileInput = document.getElementById('paymentProof');
    const previewContainer = document.getElementById('previewContainer');
    const previewImage = document.getElementById('previewImage');
    const submitBtn = document.getElementById('submitBtn');
    
    if (!uploadArea) return;
    
    // Click to upload
    uploadArea.addEventListener('click', () => {
        fileInput.click();
    });
    
    // Drag and drop
    uploadArea.addEventListener('dragover', (e) => {
        e.preventDefault();
        uploadArea.style.borderColor = '#4caf50';
        uploadArea.style.background = '#f8f9fa';
    });
    
    uploadArea.addEventListener('dragleave', () => {
        uploadArea.style.borderColor = '#ccc';
        uploadArea.style.background = 'white';
    });
    
    uploadArea.addEventListener('drop', (e) => {
        e.preventDefault();
        uploadArea.style.borderColor = '#ccc';
        uploadArea.style.background = 'white';
        
        const files = e.dataTransfer.files;
        if (files.length > 0) {
            fileInput.files = files;
            handleFileSelect(files[0]);
        }
    });
    
    // File input change
    fileInput.addEventListener('change', (e) => {
        if (e.target.files.length > 0) {
            handleFileSelect(e.target.files[0]);
        }
    });
    
    function handleFileSelect(file) {
        // Validate file type
        const allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
        if (!allowedTypes.includes(file.type)) {
            alert('Tipe file tidak didukung. Gunakan JPG, JPEG, atau PNG.');
            return;
        }
        
        // Validate file size (5MB)
        if (file.size > 5 * 1024 * 1024) {
            alert('Ukuran file terlalu besar. Maksimal 5MB.');
            return;
        }
        
        // Show preview
        const reader = new FileReader();
        reader.onload = (e) => {
            previewImage.src = e.target.result;
            previewContainer.style.display = 'block';
            submitBtn.disabled = false;
        };
        reader.readAsDataURL(file);
    }
    
    // Form submission
    const paymentForm = document.getElementById('paymentForm');
    if (paymentForm) {
        paymentForm.addEventListener('submit', function(e) {
            const submitBtn = this.querySelector('.btn-submit');
            const originalText = submitBtn.innerHTML;
            
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Mengupload...';
            submitBtn.disabled = true;
            
            // Re-enable after 10 seconds in case of error
            setTimeout(() => {
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            }, 10000);
        });
    }
});
</script>

<?php include 'includes/footer.php'; ?> 