<?php
/**
 * Debug Payment Verification
 * SMP Bina Informatika
 */

session_start();

// Check if user is logged in as admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header('Location: ../login.php?error=unauthorized');
    exit();
}

require_once '../config/database.php';

try {
    $database = new Database();
    $db = $database->getConnection();
    
    // Get students with payment proof
    $query = "SELECT id, name, payment_proof, payment_status, payment_date FROM students WHERE payment_proof IS NOT NULL ORDER BY payment_date DESC LIMIT 5";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (Exception $e) {
    $error_message = "Error: " . $e->getMessage();
}

include '../includes/header.php';
?>

<style>
.debug-page {
    padding: 2rem;
    background: #f8f9fa;
    min-height: 100vh;
}

.debug-card {
    background: white;
    border-radius: 10px;
    padding: 1.5rem;
    margin-bottom: 1rem;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.test-form {
    background: #e8f5e9;
    padding: 1rem;
    border-radius: 8px;
    margin: 1rem 0;
}

.btn-test {
    background: #2196f3;
    color: white;
    border: none;
    padding: 0.5rem 1rem;
    border-radius: 5px;
    cursor: pointer;
    margin: 0.25rem;
}

.btn-test:hover {
    background: #1976d2;
}
</style>

<div class="debug-page">
    <div class="container">
        <h1>Debug Payment Verification</h1>
        
        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger"><?php echo $error_message; ?></div>
        <?php endif; ?>
        
        <div class="debug-card">
            <h3>Test Payment Verification</h3>
            
            <?php if (empty($students)): ?>
                <p>No students with payment proof found.</p>
            <?php else: ?>
                <?php foreach ($students as $student): ?>
                <div class="test-form">
                    <h4><?php echo htmlspecialchars($student['name']); ?> (ID: <?php echo $student['id']; ?>)</h4>
                    <p><strong>Payment Status:</strong> <?php echo $student['payment_status']; ?></p>
                    <p><strong>Payment Proof:</strong> <?php echo $student['payment_proof']; ?></p>
                    <p><strong>Upload Date:</strong> <?php echo $student['payment_date']; ?></p>
                    
                    <?php if (in_array($student['payment_status'], ['pending', 'unpaid'])): ?>
                    <form action="verify_payment.php" method="POST" style="display: inline;">
                        <input type="hidden" name="student_id" value="<?php echo $student['id']; ?>">
                        <input type="hidden" name="action" value="confirm">
                        <button type="submit" class="btn-test">Test Confirm</button>
                    </form>
                    
                    <form action="verify_payment.php" method="POST" style="display: inline;">
                        <input type="hidden" name="student_id" value="<?php echo $student['id']; ?>">
                        <input type="hidden" name="action" value="reject">
                        <input type="hidden" name="rejection_reason" value="Test rejection reason">
                        <button type="submit" class="btn-test">Test Reject</button>
                    </form>
                    <?php else: ?>
                    <p><em>Cannot test - payment status is <?php echo $student['payment_status']; ?></em></p>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        
        <div class="debug-card">
            <h3>Check Error Logs</h3>
            <p>Check the server error logs for payment verification debugging information.</p>
            <p>Look for entries starting with "Payment verification -"</p>
        </div>
        
        <div class="debug-card">
            <a href="dashboard.php" class="btn btn-primary">Back to Dashboard</a>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?> 