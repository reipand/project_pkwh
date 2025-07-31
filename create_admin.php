<?php
/**
 * Script untuk menambahkan admin baru
 * SMP Bina Informatika
 * 
 * Cara penggunaan:
 * 1. Akses file ini melalui browser
 * 2. Isi form dengan data admin yang ingin ditambahkan
 * 3. Klik "Tambah Admin"
 * 4. Hapus file ini setelah selesai untuk keamanan
 */

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        require_once 'config/database.php';
        $database = new Database();
        $db = $database->getConnection();
        
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';
        $email = $_POST['email'] ?? '';
        $full_name = $_POST['full_name'] ?? '';
        $role = $_POST['role'] ?? 'admin';
        
        // Validate input
        if (empty($username) || empty($password) || empty($email) || empty($full_name)) {
            throw new Exception('Semua field harus diisi');
        }
        
        // Check if username already exists
        $checkQuery = "SELECT id FROM users WHERE username = ?";
        $checkStmt = $db->prepare($checkQuery);
        $checkStmt->bindParam(1, $username);
        $checkStmt->execute();
        
        if ($checkStmt->rowCount() > 0) {
            throw new Exception('Username sudah digunakan');
        }
        
        // Check if email already exists
        $checkEmailQuery = "SELECT id FROM users WHERE email = ?";
        $checkEmailStmt = $db->prepare($checkEmailQuery);
        $checkEmailStmt->bindParam(1, $email);
        $checkEmailStmt->execute();
        
        if ($checkEmailStmt->rowCount() > 0) {
            throw new Exception('Email sudah digunakan');
        }
        
        // Hash password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        // Insert new admin
        $insertQuery = "INSERT INTO users (username, password, email, full_name, role, is_active) 
                       VALUES (?, ?, ?, ?, ?, 1)";
        $insertStmt = $db->prepare($insertQuery);
        $insertStmt->bindParam(1, $username);
        $insertStmt->bindParam(2, $hashedPassword);
        $insertStmt->bindParam(3, $email);
        $insertStmt->bindParam(4, $full_name);
        $insertStmt->bindParam(5, $role);
        
        if ($insertStmt->execute()) {
            $success_message = "Admin berhasil ditambahkan! Username: $username";
        } else {
            throw new Exception('Gagal menambahkan admin');
        }
        
    } catch (Exception $e) {
        $error_message = $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Admin - SMP Bina Informatika</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #e8f5e9 0%, #c8e6c9 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 0;
        }
        .form-container {
            max-width: 500px;
            width: 100%;
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            padding: 2rem;
        }
        .form-title {
            text-align: center;
            color: #2c5530;
            margin-bottom: 2rem;
        }
        .btn-primary {
            background: #4caf50;
            border-color: #4caf50;
        }
        .btn-primary:hover {
            background: #388e3c;
            border-color: #388e3c;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h2 class="form-title">
            <i class="fas fa-user-plus"></i>
            Tambah Admin Baru
        </h2>
        
        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle me-2"></i>
                <?php echo htmlspecialchars($error_message); ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($success_message)): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle me-2"></i>
                <?php echo htmlspecialchars($success_message); ?>
            </div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" class="form-control" id="username" name="username" required>
            </div>
            
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            
            <div class="mb-3">
                <label for="full_name" class="form-label">Nama Lengkap</label>
                <input type="text" class="form-control" id="full_name" name="full_name" required>
            </div>
            
            <div class="mb-3">
                <label for="role" class="form-label">Role</label>
                <select class="form-control" id="role" name="role">
                    <option value="admin">Admin</option>
                    <option value="staff">Staff</option>
                    <option value="teacher">Teacher</option>
                </select>
            </div>
            
            <div class="d-grid">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>
                    Tambah Admin
                </button>
            </div>
        </form>
        
        <div class="mt-3 text-center">
            <small class="text-muted">
                <i class="fas fa-exclamation-triangle me-1"></i>
                Hapus file ini setelah selesai untuk keamanan
            </small>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 