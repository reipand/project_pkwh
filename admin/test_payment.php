<?php
/**
 * Test Payment Verification
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
    
    // Get all students with payment proof
    $query = "SELECT id, name, payment_proof, payment_status, payment_date FROM students WHERE payment_proof IS NOT NULL ORDER BY payment_date DESC";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (Exception $e) {
    $error_message = "Error: " . $e->getMessage();
}

include '../includes/header.php';
?>

<style>
.test-page {
    padding: 2rem;
    background: #f8f9fa;
    min-height: 100vh;
}

.test-card {
    background: white;
    border-radius: 10px;
    padding: 1.5rem;
    margin-bottom: 1rem;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.status-badge {
    display: inline-block;
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 600;
    text-transform: uppercase;
}

.status-pending { background: #fff3cd; color: #856404; }
.status-unpaid { background: #f8d7da; color: #721c24; }
.status-confirmed { background: #d4edda; color: #155724; }
.status-rejected { background: #f8d7da; color: #721c24; }
.status-verified { background: #d1ecf1; color: #0c5460; }
.status-paid { background: #d4edda; color: #155724; }
</style>

<div class="test-page">
    <div class="container">
        <h1>Payment Verification Test</h1>
        
        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger"><?php echo $error_message; ?></div>
        <?php endif; ?>
        
        <div class="test-card">
            <h3>Students with Payment Proof</h3>
            <p>Total: <?php echo count($students); ?></p>
            
            <?php if (empty($students)): ?>
                <p>No students with payment proof found.</p>
            <?php else: ?>
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Payment Proof</th>
                            <th>Payment Status</th>
                            <th>Upload Date</th>
                            <th>Can Verify?</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($students as $student): ?>
                        <tr>
                            <td><?php echo $student['id']; ?></td>
                            <td><?php echo htmlspecialchars($student['name']); ?></td>
                            <td>
                                <?php if ($student['payment_proof']): ?>
                                    <a href="../uploads/payments/<?php echo htmlspecialchars($student['payment_proof']); ?>" target="_blank">
                                        <?php echo htmlspecialchars($student['payment_proof']); ?>
                                    </a>
                                <?php else: ?>
                                    No proof
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="status-badge status-<?php echo $student['payment_status']; ?>">
                                    <?php echo $student['payment_status']; ?>
                                </span>
                            </td>
                            <td><?php echo $student['payment_date'] ? date('d/m/Y H:i', strtotime($student['payment_date'])) : 'N/A'; ?></td>
                            <td>
                                <?php 
                                $canVerify = $student['payment_proof'] && in_array($student['payment_status'], ['pending', 'unpaid']);
                                echo $canVerify ? '<span style="color: green;">Yes</span>' : '<span style="color: red;">No</span>';
                                ?>
                            </td>
                            <td>
                                <?php if ($canVerify): ?>
                                    <a href="view_student.php?id=<?php echo $student['id']; ?>" class="btn btn-primary btn-sm">
                                        Verify
                                    </a>
                                <?php else: ?>
                                    <a href="view_student.php?id=<?php echo $student['id']; ?>&debug=1" class="btn btn-secondary btn-sm">
                                        View
                                    </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
        
        <div class="test-card">
            <h3>Payment Status Summary</h3>
            <?php
            $statusCounts = [];
            foreach ($students as $student) {
                $status = $student['payment_status'];
                $statusCounts[$status] = ($statusCounts[$status] ?? 0) + 1;
            }
            ?>
            <ul>
                <?php foreach ($statusCounts as $status => $count): ?>
                <li><strong><?php echo $status; ?>:</strong> <?php echo $count; ?> students</li>
                <?php endforeach; ?>
            </ul>
        </div>
        
        <div class="test-card">
            <a href="dashboard.php" class="btn btn-primary">Back to Dashboard</a>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?> 