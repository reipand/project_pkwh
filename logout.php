<?php
/**
 * Logout Handler
 * SMP Bina Informatika
 */

session_start();

// Log the logout activity
if (isset($_SESSION['user_id'])) {
    try {
        require_once 'config/database.php';
        $database = new Database();
        $db = $database->getConnection();
        
        $user_id = $_SESSION['user_id'];
        $user_type = $_SESSION['user_type'] ?? 'unknown';
        $username = $_SESSION['username'] ?? 'unknown';
        
        // Log activity
        $query = "INSERT INTO activity_logs (user_id, action, description, ip_address, user_agent) 
                  VALUES (?, 'logout', ?, ?, ?)";
        $stmt = $db->prepare($query);
        $description = "User $username ($user_type) logged out";
        $ip_address = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
        
        $stmt->bindParam(1, $user_id);
        $stmt->bindParam(2, $description);
        $stmt->bindParam(3, $ip_address);
        $stmt->bindParam(4, $user_agent);
        $stmt->execute();
        
    } catch (Exception $e) {
        error_log("Logout logging error: " . $e->getMessage());
    }
}

// Clear all session data
$_SESSION = array();

// Destroy the session cookie
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Destroy the session
session_destroy();

// Redirect to home page with success message
header('Location: index.php?logout=success');
exit();
?> 