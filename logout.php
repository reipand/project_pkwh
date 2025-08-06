<?php
/**
 * Admin Logout
 * SMP Bina Informatika
 */

session_start();

// Log the logout activity if admin is logged in
if (isset($_SESSION['user_id']) && $_SESSION['user_type'] === 'admin') {
    try {
        require_once 'config/database.php';
        $database = new Database();
        $db = $database->getConnection();
        
        $admin_id = $_SESSION['user_id'];
        $action = 'logout';
        $description = "Admin logout: " . ($_SESSION['name'] ?? 'Unknown');
        
        $logQuery = "INSERT INTO activity_logs (user_id, action, description, ip_address, user_agent) 
                       VALUES (?, ?, ?, ?, ?)";
        $logStmt = $db->prepare($logQuery);
        $ip_address = $_SERVER['REMOTE_ADDR'] ?? '';
        $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
        $logStmt->bindParam(1, $admin_id);
        $logStmt->bindParam(2, $action);
        $logStmt->bindParam(3, $description);
        $logStmt->bindParam(4, $ip_address);
        $logStmt->bindParam(5, $user_agent);
        $logStmt->execute();
        
    } catch (Exception $e) {
        // It's important to log the error but not output anything here
        error_log("Logout logging error: " . $e->getMessage());
    }
}

// Unset all of the session variables
$_SESSION = array();

// Destroy the session
session_destroy();

// Redirect to login page with a success message
// This line must be executed before any browser output
header('Location: ../login.php?message=logout_success');
exit();
?>