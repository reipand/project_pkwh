<?php
/**
 * API untuk verifikasi siswa
 * SMP Bina Informatika
 */

header('Content-Type: application/json');
session_start();

// Check if user is logged in as admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    http_response_code(401);
    echo json_encode([
        'success' => false,
        'message' => 'Unauthorized access'
    ]);
    exit();
}

// Check if request method is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Method not allowed'
    ]);
    exit();
}

try {
    // Get JSON input
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input) {
        throw new Exception('Invalid JSON input');
    }
    
    $student_id = $input['student_id'] ?? null;
    $status = $input['status'] ?? null;
    
    // Validate input
    if (!$student_id || !$status) {
        throw new Exception('Student ID dan status harus diisi');
    }
    
    if (!in_array($status, ['accepted', 'rejected'])) {
        throw new Exception('Status tidak valid');
    }
    
    // Connect to database
    require_once '../config/database.php';
    $database = new Database();
    $db = $database->getConnection();
    
    // Check if student exists
    $checkQuery = "SELECT id, name, status FROM students WHERE id = ?";
    $checkStmt = $db->prepare($checkQuery);
    $checkStmt->bindParam(1, $student_id);
    $checkStmt->execute();
    
    if ($checkStmt->rowCount() === 0) {
        throw new Exception('Siswa tidak ditemukan');
    }
    
    $student = $checkStmt->fetch(PDO::FETCH_ASSOC);
    
    // Check if student is already processed
    if ($student['status'] !== 'pending') {
        throw new Exception('Siswa sudah diverifikasi sebelumnya');
    }
    
    // Update student status
    $updateQuery = "UPDATE students SET status = ?, updated_at = NOW() WHERE id = ?";
    $updateStmt = $db->prepare($updateQuery);
    $updateStmt->bindParam(1, $status);
    $updateStmt->bindParam(2, $student_id);
    
    if (!$updateStmt->execute()) {
        throw new Exception('Gagal memperbarui status siswa');
    }
    
    // Log activity
    $admin_id = $_SESSION['user_id'];
    $action = $status === 'accepted' ? 'verify_accepted' : 'verify_rejected';
    $description = $status === 'accepted' ? 
        "Menerima pendaftaran siswa: " . $student['name'] :
        "Menolak pendaftaran siswa: " . $student['name'];
    
    $logQuery = "INSERT INTO activity_logs (user_id, action, description, ip_address, user_agent) 
                 VALUES (?, ?, ?, ?, ?)";
    $logStmt = $db->prepare($logQuery);
    $logStmt->bindParam(1, $admin_id);
    $logStmt->bindParam(2, $action);
    $logStmt->bindParam(3, $description);
    $logStmt->bindParam(4, $_SERVER['REMOTE_ADDR'] ?? '');
    $logStmt->bindParam(5, $_SERVER['HTTP_USER_AGENT'] ?? '');
    $logStmt->execute();
    
    // Send success response
    echo json_encode([
        'success' => true,
        'message' => 'Status siswa berhasil diperbarui',
        'data' => [
            'student_id' => $student_id,
            'student_name' => $student['name'],
            'new_status' => $status
        ]
    ]);
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?> 