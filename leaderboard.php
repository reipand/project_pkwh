<?php
/**
 * Leaderboard
 * SMP Bina Informatika
 */

session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php?error=unauthorized');
    exit();
}

$page_title = "Leaderboard Siswa";

// Asumsi file database.php sudah ada dan berfungsi
require_once 'config/database.php';
require_once 'includes/header.php';

$leaderboard_data = [];
$error_message = null;

try {
    $database = new Database();
    $db = $database->getConnection();
    
    // Query untuk mengambil data leaderboard dengan filter umur <= 18 tahun
    $query = "
        SELECT 
            name, 
            score,
            date_of_birth,
            TIMESTAMPDIFF(YEAR, date_of_birth, CURDATE()) AS age
        FROM 
            students
        WHERE 
            status = 'accepted' AND TIMESTAMPDIFF(YEAR, date_of_birth, CURDATE()) <= 18
        ORDER BY 
            score DESC, name ASC
    ";
    
    $stmt = $db->prepare($query);
    $stmt->execute();
    
    $leaderboard_data = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    // Log error untuk debugging
    error_log("Leaderboard error: " . $e->getMessage());
    $error_message = "Terjadi kesalahan saat mengambil data leaderboard. Silakan coba lagi nanti.";
}
?>

<style>
/* CSS kustom untuk halaman leaderboard */
.leaderboard-container {
    padding: 80px 0;
    min-height: 100vh;
    background-color: #f0f2f5;
}

.leaderboard-header {
    background: linear-gradient(135deg, #2196f3, #1976d2);
    color: white;
    padding: 2rem 0;
    text-align: center;
    margin-bottom: 2rem;
    box-shadow: 0 4px 15px rgba(33, 150, 243, 0.3);
}

.leaderboard-header h1 {
    font-size: 2.5rem;
    font-weight: 700;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.leaderboard-table-card {
    background: white;
    border-radius: 15px;
    padding: 2rem;
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
}

.table-leaderboard th, .table-leaderboard td {
    vertical-align: middle;
}

.table-leaderboard th {
    background-color: #e9ecef;
    font-weight: 600;
    color: #333;
}

.leaderboard-rank {
    font-size: 1.25rem;
    font-weight: bold;
    color: #4caf50;
}

.first-place, .second-place, .third-place {
    background-color: #fffde7;
}

.first-place .leaderboard-rank {
    color: #ffc107;
    font-size: 1.5rem;
}

.second-place .leaderboard-rank {
    color: #bdbdbd;
}

.third-place .leaderboard-rank {
    color: #cd7f32;
}
</style>

<div class="leaderboard-container">
    <div class="leaderboard-header">
        <div class="container">
            <h1>🥇 Leaderboard Siswa Berprestasi</h1>
            <p>Siswa dengan nilai tertinggi dan usia maksimal 18 tahun.</p>
        </div>
    </div>
    
    <div class="container">
        <div class="leaderboard-table-card">
            <?php if ($error_message): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    <?php echo htmlspecialchars($error_message); ?>
                </div>
            <?php else: ?>
                <?php if (count($leaderboard_data) > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover table-leaderboard">
                            <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Nama Siswa</th>
                                    <th scope="col">Nilai</th>
                                    <th scope="col">Usia</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $rank = 1; ?>
                                <?php foreach ($leaderboard_data as $student): ?>
                                <tr class="<?php 
                                    if ($rank === 1) echo 'first-place';
                                    elseif ($rank === 2) echo 'second-place';
                                    elseif ($rank === 3) echo 'third-place';
                                ?>">
                                    <td class="leaderboard-rank"><?php echo $rank++; ?></td>
                                    <td><?php echo htmlspecialchars($student['name']); ?></td>
                                    <td><?php echo htmlspecialchars($student['score']); ?></td>
                                    <td><?php echo htmlspecialchars($student['age']); ?> tahun</td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info text-center">
                        <i class="fas fa-info-circle me-2"></i>
                        Belum ada data siswa yang memenuhi kriteria leaderboard.
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>