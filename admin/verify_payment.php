<?php
/**
 * Script untuk memproses verifikasi pembayaran oleh Admin (Diperbaiki)
 * SMP Bina Informatika
 */

session_start();

// Debug: Log all POST data
error_log("Payment verification - POST data: " . print_r($_POST, true));

// 1. Keamanan: Pastikan hanya admin yang login yang bisa mengakses
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    error_log("Payment verification - Unauthorized access attempt");
    header('Location: ../login.php?error=unauthorized');
    exit();
}

require_once '../config/database.php';

// 2. Validasi Input
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['student_id'], $_POST['action'])) {
    
    $student_id = filter_input(INPUT_POST, 'student_id', FILTER_VALIDATE_INT);
    $action = $_POST['action'];
    $rejection_reason = $_POST['rejection_reason'] ?? ''; // Ambil alasan penolakan
    $admin_id = $_SESSION['user_id'];
    
    error_log("Payment verification - Processing: student_id=$student_id, action=$action, admin_id=$admin_id");
    
    // Validasi dasar
    if (!$student_id || !in_array($action, ['confirm', 'reject'])) {
        error_log("Payment verification - Invalid input: student_id=$student_id, action=$action");
        $_SESSION['error_message'] = 'Aksi tidak valid atau ID siswa tidak ditemukan.';
        header('Location: dashboard.php');
        exit();
    }

    $db = null;
    try {
        $database = new Database();
        $db = $database->getConnection();
        $db->beginTransaction();

        $checkStmt = $db->prepare("SELECT payment_status, payment_proof, name FROM students WHERE id = ?");
        $checkStmt->bindParam(1, $student_id);
        $checkStmt->execute();
        $student = $checkStmt->fetch(PDO::FETCH_ASSOC);

        if (!$student) {
            error_log("Payment verification - Student not found: student_id=$student_id");
            throw new Exception('Siswa tidak ditemukan.');
        }

        error_log("Payment verification - Student found: " . print_r($student, true));

        // Check if student has payment proof and status allows verification
        if (!$student['payment_proof'] || !in_array($student['payment_status'], ['pending', 'unpaid'])) {
            error_log("Payment verification - Cannot verify: payment_proof=" . ($student['payment_proof'] ? 'yes' : 'no') . ", payment_status=" . $student['payment_status']);
            throw new Exception('Status pembayaran sudah diubah sebelumnya atau tidak ada bukti pembayaran.');
        }

        // --- FIX: Logika query diperbaiki ---
        if ($action === 'confirm') {
            // Jika dikonfirmasi, update status pembayaran menjadi 'confirmed' DAN status pendaftaran menjadi 'accepted'
            $query = "UPDATE students SET payment_status = 'confirmed', status = 'accepted' WHERE id = ?";
            $stmt = $db->prepare($query);
            $stmt->bindParam(1, $student_id);
            
            $log_action = 'payment_confirmed';
            $log_description = "Admin (ID: {$admin_id}) mengonfirmasi pembayaran untuk siswa {$student['name']} (ID: {$student_id}).";

        } else { // 'reject'
            // Jika ditolak, HANYA update status pembayaran menjadi 'rejected' dan simpan alasannya di kolom 'notes'
            $query = "UPDATE students SET payment_status = 'rejected', notes = ? WHERE id = ?";
            $stmt = $db->prepare($query);
            $stmt->bindParam(1, $rejection_reason);
            $stmt->bindParam(2, $student_id);

            $log_action = 'payment_rejected';
            $log_description = "Admin (ID: {$admin_id}) menolak pembayaran untuk siswa {$student['name']} (ID: {$student_id}) dengan alasan: " . $rejection_reason;
        }

        $stmt->execute();
        
        error_log("Payment verification - Database update successful for student_id=$student_id, action=$action");
        
        // Pencatatan Log Aktivitas
        $logQuery = "INSERT INTO activity_logs (user_id, action, description, ip_address, user_agent) VALUES (?, ?, ?, ?, ?)";
        $logStmt = $db->prepare($logQuery);
        $ip_address = $_SERVER['REMOTE_ADDR'] ?? 'UNKNOWN';
        $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? 'UNKNOWN';
        $logStmt->execute([$admin_id, $log_action, $log_description, $ip_address, $user_agent]);
        
        $db->commit();
        error_log("Payment verification - Transaction committed successfully");
        $_SESSION['success_message'] = 'Status pembayaran berhasil diperbarui.';

    } catch (Exception $e) {
        if ($db) $db->rollBack();
        error_log("Payment verification error: " . $e->getMessage());
        error_log("Payment verification - Stack trace: " . $e->getTraceAsString());
        $_SESSION['error_message'] = 'Terjadi kesalahan: ' . $e->getMessage();
    }
    
    // Redirect kembali ke halaman detail siswa
    header('Location: view_student.php?id=' . $student_id);
    exit();

} else {
    header('Location: dashboard.php');
    exit();
}
?>