<?php
/**
 * API Endpoint untuk Upload Bukti Pembayaran
 * SMP Bina Informatika
 */

// Set response headers
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');

// Handle CORS preflight request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Method not allowed'
    ]);
    exit();
}

session_start();

// Check if user is logged in as student
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'student') {
    http_response_code(401);
    echo json_encode([
        'success' => false,
        'message' => 'Unauthorized access'
    ]);
    exit();
}

try {
    // Include necessary files
    require_once '../config/database.php';
    
    $student_id = $_SESSION['user_id'];
    
    // Check if payment proof already exists
    $database = new Database();
    $db = $database->getConnection();
    
    $checkQuery = "SELECT payment_proof, payment_status FROM students WHERE id = ?";
    $checkStmt = $db->prepare($checkQuery);
    $checkStmt->bindParam(1, $student_id);
    $checkStmt->execute();
    $student = $checkStmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$student) {
        throw new Exception('Siswa tidak ditemukan');
    }
    
    // Check if payment proof already uploaded
    if ($student['payment_proof']) {
        throw new Exception('Bukti pembayaran sudah diupload sebelumnya');
    }
    
    // Check if file was uploaded
    if (!isset($_FILES['payment_proof']) || $_FILES['payment_proof']['error'] !== UPLOAD_ERR_OK) {
        throw new Exception('File bukti pembayaran tidak ditemukan atau terjadi error');
    }
    
    $file = $_FILES['payment_proof'];
    
    // Validate file type
    $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];
    if (!in_array($file['type'], $allowedTypes)) {
        throw new Exception('Tipe file tidak didukung. Gunakan format JPG, PNG, atau GIF');
    }
    
    // Validate file size (max 5MB)
    $maxSize = 5 * 1024 * 1024; // 5MB
    if ($file['size'] > $maxSize) {
        throw new Exception('Ukuran file terlalu besar. Maksimal 5MB');
    }
    
    // Create upload directory if it doesn't exist
    $uploadDir = '../uploads/payments/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    
    // Generate unique filename
    $fileExtension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = 'payment_' . $student_id . '_' . time() . '.' . $fileExtension;
    $filepath = $uploadDir . $filename;
    
    // Move uploaded file
    if (!move_uploaded_file($file['tmp_name'], $filepath)) {
        throw new Exception('Gagal menyimpan file');
    }
    
    // Update database
    $updateQuery = "UPDATE students SET payment_proof = ?, payment_status = 'pending', payment_date = NOW() WHERE id = ?";
    $updateStmt = $db->prepare($updateQuery);
    $updateStmt->bindParam(1, $filename);
    $updateStmt->bindParam(2, $student_id);
    
    if (!$updateStmt->execute()) {
        // Delete uploaded file if database update fails
        unlink($filepath);
        throw new Exception('Gagal memperbarui database');
    }
    
    // Log activity
    $logQuery = "INSERT INTO activity_logs (user_id, action, description, ip_address, user_agent) VALUES (?, ?, ?, ?, ?)";
    $logStmt = $db->prepare($logQuery);
    $action = 'payment_uploaded';
    $description = "Siswa (ID: {$student_id}) mengupload bukti pembayaran: {$filename}";
    $ip_address = $_SERVER['REMOTE_ADDR'] ?? 'UNKNOWN';
    $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? 'UNKNOWN';
    $logStmt->execute([$student_id, $action, $description, $ip_address, $user_agent]);
    
    // Send success response
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'message' => 'Bukti pembayaran berhasil diupload',
        'data' => [
            'filename' => $filename,
            'payment_status' => 'pending'
        ]
    ]);
    
} catch (Exception $e) {
    // Log error
    error_log("Payment upload error: " . $e->getMessage());
    
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
} catch (Error $e) {
    // Log system error
    error_log("System error in payment upload: " . $e->getMessage());
    
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Internal server error. Please try again later.'
    ]);
}
?> 