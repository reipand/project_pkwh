<?php
/**
 * Admin Dashboard - Verifikasi Registrasi Siswa
 * SMP Bina Informatika
 */

session_start();

// Check if user is logged in as admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header('Location: ../login.php?error=unauthorized');
    exit();
}

$page_title = "Admin Dashboard - Verifikasi Siswa";

try {
    require_once '../config/database.php';
    $database = new Database();
    $db = $database->getConnection();
    
    // Get admin data
    $admin_id = $_SESSION['user_id'];
    $query = "SELECT * FROM users WHERE id = ?";
    $stmt = $db->prepare($query);
    $stmt->bindParam(1, $admin_id);
    $stmt->execute();
    
    if ($stmt->rowCount() > 0) {
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);
    } else {
        session_destroy();
        header('Location: ../login.php?error=invalid_session');
        exit();
    }
    
    // Get statistics
    $statsQuery = "SELECT 
        COUNT(*) as total_students,
        SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending_count,
        SUM(CASE WHEN status = 'accepted' THEN 1 ELSE 0 END) as accepted_count,
        SUM(CASE WHEN status = 'rejected' THEN 1 ELSE 0 END) as rejected_count,
        SUM(CASE WHEN payment_status = 'paid' THEN 1 ELSE 0 END) as paid_count
        FROM students";
    $statsStmt = $db->prepare($statsQuery);
    $statsStmt->execute();
    $stats = $statsStmt->fetch(PDO::FETCH_ASSOC);
    
    // Get recent registrations
    $recentQuery = "SELECT * FROM students ORDER BY registration_date DESC LIMIT 10";
    $recentStmt = $db->prepare($recentQuery);
    $recentStmt->execute();
    $recentStudents = $recentStmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (Exception $e) {
    error_log("Admin dashboard error: " . $e->getMessage());
    $error_message = "Terjadi kesalahan sistem. Silakan coba lagi nanti.";
}

include '../includes/header.php';
?>

<style>
.admin-dashboard {
    min-height: 100vh;
    background: linear-gradient(135deg, #f8fff8 0%, #e8f5e9 100%);
    padding-top: 80px;
}

.admin-header {
    background: linear-gradient(135deg, #2196f3, #1976d2);
    color: white;
    padding: 2rem 0;
    margin-bottom: 2rem;
    box-shadow: 0 4px 15px rgba(33, 150, 243, 0.3);
}

.admin-welcome {
    text-align: center;
}

.admin-welcome h1 {
    font-size: 2.5rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.admin-welcome p {
    font-size: 1.1rem;
    opacity: 0.9;
}

.admin-content {
    padding: 0 1rem;
}

.admin-card {
    background: white;
    border-radius: 15px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
    padding: 2rem;
    margin-bottom: 2rem;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.admin-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.15);
}

.admin-card h3 {
    color: #2196f3;
    font-size: 1.5rem;
    font-weight: 600;
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.stat-card {
    background: linear-gradient(135deg, #4caf50, #388e3c);
    color: white;
    padding: 1.5rem;
    border-radius: 12px;
    text-align: center;
    box-shadow: 0 4px 15px rgba(76, 175, 80, 0.3);
}

.stat-card.warning {
    background: linear-gradient(135deg, #ff9800, #f57c00);
}

.stat-card.info {
    background: linear-gradient(135deg, #2196f3, #1976d2);
}

.stat-card.danger {
    background: linear-gradient(135deg, #f44336, #d32f2f);
}

.stat-number {
    font-size: 2.5rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
}

.stat-label {
    font-size: 0.9rem;
    opacity: 0.9;
}

.students-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 1rem;
}

.students-table th,
.students-table td {
    padding: 1rem;
    text-align: left;
    border-bottom: 1px solid #e0e0e0;
}

.students-table th {
    background: #f5f5f5;
    font-weight: 600;
    color: #333;
}

.students-table tr:hover {
    background: #f8f9fa;
}

.status-badge {
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 500;
    text-transform: uppercase;
}

.status-pending {
    background: #fff3cd;
    color: #856404;
}

.status-accepted {
    background: #d4edda;
    color: #155724;
}

.status-rejected {
    background: #f8d7da;
    color: #721c24;
}

.action-buttons {
    display: flex;
    gap: 0.5rem;
}

.btn-verify {
    background: #28a745;
    color: white;
    border: none;
    padding: 0.5rem 1rem;
    border-radius: 5px;
    cursor: pointer;
    font-size: 0.8rem;
    transition: background 0.3s ease;
}

.btn-verify:hover {
    background: #218838;
}

.btn-reject {
    background: #dc3545;
    color: white;
    border: none;
    padding: 0.5rem 1rem;
    border-radius: 5px;
    cursor: pointer;
    font-size: 0.8rem;
    transition: background 0.3s ease;
}

.btn-reject:hover {
    background: #c82333;
}

.btn-view {
    background: #17a2b8;
    color: white;
    border: none;
    padding: 0.5rem 1rem;
    border-radius: 5px;
    cursor: pointer;
    font-size: 0.8rem;
    transition: background 0.3s ease;
}

.btn-view:hover {
    background: #138496;
}

.search-filter {
    display: flex;
    gap: 1rem;
    margin-bottom: 1.5rem;
    flex-wrap: wrap;
}

.search-filter input,
.search-filter select {
    padding: 0.5rem;
    border: 1px solid #ddd;
    border-radius: 5px;
    flex: 1;
    min-width: 200px;
}

.search-filter button {
    background: #2196f3;
    color: white;
    border: none;
    padding: 0.5rem 1.5rem;
    border-radius: 5px;
    cursor: pointer;
    transition: background 0.3s ease;
}

.search-filter button:hover {
    background: #1976d2;
}

@media (max-width: 768px) {
    .stats-grid {
        grid-template-columns: repeat(2, 1fr);
    }
    
    .students-table {
        font-size: 0.9rem;
    }
    
    .students-table th,
    .students-table td {
        padding: 0.5rem;
    }
    
    .action-buttons {
        flex-direction: column;
    }
}
</style>

<div class="admin-dashboard">
    <div class="admin-header">
        <div class="container">
            <div class="admin-welcome">
                <h1>
                    Admin Dashboard
                </h1>
                <p>Selamat datang, <?php echo htmlspecialchars($admin['full_name']); ?> | Verifikasi Registrasi Siswa</p>
            </div>
        </div>
    </div>
    
    <div class="container">
        <div class="admin-content">
            <?php if (isset($error_message)): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    <?php echo htmlspecialchars($error_message); ?>
                </div>
            <?php endif; ?>
            
            <!-- Statistics Cards -->
            <div class="admin-card">
                <h3>
                    Statistik Pendaftaran
                </h3>
                
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-number"><?php echo $stats['total_students']; ?></div>
                        <div class="stat-label">Total Siswa</div>
                    </div>
                    <div class="stat-card warning">
                        <div class="stat-number"><?php echo $stats['pending_count']; ?></div>
                        <div class="stat-label">Menunggu Verifikasi</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number"><?php echo $stats['accepted_count']; ?></div>
                        <div class="stat-label">Diterima</div>
                    </div>
                    <div class="stat-card danger">
                        <div class="stat-number"><?php echo $stats['rejected_count']; ?></div>
                        <div class="stat-label">Ditolak</div>
                    </div>
                    <div class="stat-card info">
                        <div class="stat-number"><?php echo $stats['paid_count']; ?></div>
                        <div class="stat-label">Sudah Bayar</div>
                    </div>
                </div>
            </div>
            
            <!-- Recent Registrations -->
            <div class="admin-card">
                <h3>
                    Pendaftaran Terbaru
                </h3>
                
                <div class="search-filter">
                    <input type="text" id="searchName" placeholder="Cari berdasarkan nama...">
                    <select id="filterStatus">
                        <option value="">Semua Status</option>
                        <option value="pending">Menunggu Verifikasi</option>
                        <option value="accepted">Diterima</option>
                        <option value="rejected">Ditolak</option>
                    </select>
                    <button onclick="filterStudents()">
                        <i class="fas fa-search"></i> Filter
                    </button>
                </div>
                
                <div class="table-responsive">
                    <table class="students-table">
                        <thead>
                            <tr>
                                <th>Nama</th>
                                <th>Username</th>
                                <th>Kelas</th>
                                <th>Email Orang Tua</th>
                                <th>Status</th>
                                <th>Pembayaran</th>
                                <th>Tanggal Daftar</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="studentsTableBody">
                            <?php foreach ($recentStudents as $student): ?>
                            <tr data-status="<?php echo $student['status']; ?>">
                                <td><?php echo htmlspecialchars($student['name']); ?></td>
                                <td><?php echo htmlspecialchars($student['username']); ?></td>
                                <td><?php echo htmlspecialchars($student['class']); ?></td>
                                <td><?php echo htmlspecialchars($student['parent_email']); ?></td>
                                <td>
                                    <span class="status-badge status-<?php echo $student['status']; ?>">
                                        <?php 
                                        switch($student['status']) {
                                            case 'pending': echo 'Menunggu'; break;
                                            case 'accepted': echo 'Diterima'; break;
                                            case 'rejected': echo 'Ditolak'; break;
                                            default: echo $student['status'];
                                        }
                                        ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="status-badge status-<?php echo $student['payment_status']; ?>">
                                        <?php 
                                        switch($student['payment_status']) {
                                            case 'unpaid': echo 'Belum Bayar'; break;
                                            case 'paid': echo 'Sudah Bayar'; break;
                                            case 'verified': echo 'Terverifikasi'; break;
                                            default: echo $student['payment_status'];
                                        }
                                        ?>
                                    </span>
                                </td>
                                <td><?php echo date('d/m/Y H:i', strtotime($student['registration_date'])); ?></td>
                                <td>
                                    <div class="action-buttons">
                                        <button class="btn-view" onclick="viewStudent(<?php echo $student['id']; ?>)">
                                            <i class="fas fa-eye"></i> Detail
                                        </button>
                                        <?php if ($student['status'] === 'pending'): ?>
                                        <button class="btn-verify" onclick="verifyStudent(<?php echo $student['id']; ?>, 'accepted')">
                                            <i class="fas fa-check"></i> Terima
                                        </button>
                                        <button class="btn-reject" onclick="verifyStudent(<?php echo $student['id']; ?>, 'rejected')">
                                            <i class="fas fa-times"></i> Tolak
                                        </button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function filterStudents() {
    const searchName = document.getElementById('searchName').value.toLowerCase();
    const filterStatus = document.getElementById('filterStatus').value;
    const tableBody = document.getElementById('studentsTableBody');
    const rows = tableBody.getElementsByTagName('tr');
    
    for (let row of rows) {
        const name = row.cells[0].textContent.toLowerCase();
        const status = row.getAttribute('data-status');
        
        const nameMatch = name.includes(searchName);
        const statusMatch = !filterStatus || status === filterStatus;
        
        if (nameMatch && statusMatch) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    }
}

function viewStudent(studentId) {
    // Redirect ke halaman detail siswa
    window.location.href = `view_student.php?id=${studentId}`;
}

function verifyStudent(studentId, status) {
    const action = status === 'accepted' ? 'menerima' : 'menolak';
    
    if (confirm(`Apakah Anda yakin ingin ${action} pendaftaran siswa ini?`)) {
        // Implementasi untuk verifikasi siswa
        fetch('../api/verify_student.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                student_id: studentId,
                status: status
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(`Siswa berhasil ${action}!`);
                location.reload();
            } else {
                alert('Terjadi kesalahan: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan sistem');
        });
    }
}

// Auto refresh setiap 30 detik
setInterval(() => {
    // Refresh halaman untuk mendapatkan data terbaru
    // location.reload();
}, 30000);
</script>

<?php include '../includes/footer.php'; ?> 