<?php
/**
 * API Endpoint untuk Registrasi Siswa Baru
 * SMP Bina Informatika
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// CORS preflight request
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

try {
    // Include necessary files
    require_once '../controllers/StudentController.php';
    
    // Get POST data
    $postData = $_POST;
    
    // Validate required fields
    $requiredFields = [
        'registration_type', 'academic_year', 'parent_email', 'class',
        'name', 'birth_place', 'birth_date', 'parent_phone', 'nik',
        'password', 'origin_school'
    ];
    
    foreach ($requiredFields as $field) {
        if (empty($postData[$field])) {
            throw new Exception("Field $field is required");
        }
    }
    
    // Additional validation
    if (!preg_match('/^\d{16}$/', $postData['nik'])) {
        throw new Exception("NIK must be 16 digits");
    }
    
    if (!preg_match('/^(\+62|62|0)8[1-9][0-9]{6,9}$/', $postData['parent_phone'])) {
        throw new Exception("Invalid phone number format");
    }
    
    if (strlen($postData['password']) < 6) {
        throw new Exception("Password must be at least 6 characters");
    }
    
    // Validate email format
    if (!filter_var($postData['parent_email'], FILTER_VALIDATE_EMAIL)) {
        throw new Exception("Invalid email format");
    }
    
    // Validate date format
    $birthDate = DateTime::createFromFormat('Y-m-d', $postData['birth_date']);
    if (!$birthDate || $birthDate->format('Y-m-d') !== $postData['birth_date']) {
        throw new Exception("Invalid birth date format");
    }
    
    // Check if student is not too old (max 18 years old for SMP)
    $today = new DateTime();
    $age = $today->diff($birthDate)->y;
    if ($age > 18) {
        throw new Exception("Student age must be 18 years or younger");
    }
    
    // Initialize controller
    $controller = new StudentController();
    
    // Register student
    $result = $controller->registerStudent($postData);
    
    if ($result['success']) {
        // Log successful registration
        error_log("New student registration: " . $postData['name'] . " - " . $result['data']['username']);
        
        // Send response
        http_response_code(200);
        echo json_encode($result);
    } else {
        http_response_code(400);
        echo json_encode($result);
    }
    
} catch (Exception $e) {
    // Log error
    error_log("Registration error: " . $e->getMessage());
    
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
} catch (Error $e) {
    // Log system error
    error_log("System error in registration: " . $e->getMessage());
    
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Internal server error. Please try again later.'
    ]);
}
?> 