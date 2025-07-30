-- =====================================================
-- Database Schema untuk SMP Bina Informatika
-- Penerimaan Siswa Baru System
-- =====================================================

-- Buat database
CREATE DATABASE IF NOT EXISTS smp_bina_informatika
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE smp_bina_informatika;

-- Tabel untuk data siswa
CREATE TABLE students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    registration_type ENUM('reguler', 'prestasi', 'beasiswa') NOT NULL,
    academic_year VARCHAR(9) NOT NULL,
    parent_email VARCHAR(100) NOT NULL,
    class ENUM('VII', 'VIII', 'IX') NOT NULL,
    name VARCHAR(100) NOT NULL,
    birth_place VARCHAR(100) NOT NULL,
    birth_date DATE NOT NULL,
    parent_phone VARCHAR(20) NOT NULL,
    nik VARCHAR(16) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    origin_school VARCHAR(100) NOT NULL,
    username VARCHAR(50) UNIQUE NOT NULL,
    registration_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('pending', 'accepted', 'rejected', 'completed') DEFAULT 'pending',
    payment_status ENUM('unpaid', 'paid', 'verified') DEFAULT 'unpaid',
    payment_proof VARCHAR(255) NULL,
    payment_date TIMESTAMP NULL,
    test_score DECIMAL(5,2) NULL,
    notes TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_username (username),
    INDEX idx_nik (nik),
    INDEX idx_status (status),
    INDEX idx_registration_date (registration_date),
    INDEX idx_academic_year (academic_year),
    INDEX idx_parent_email (parent_email)
);

-- Tabel untuk pesan kontak
CREATE TABLE contact_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    subject VARCHAR(200) NOT NULL,
    message TEXT NOT NULL,
    ip_address VARCHAR(45) NULL,
    status ENUM('unread', 'read', 'replied') DEFAULT 'unread',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_email (email),
    INDEX idx_status (status),
    INDEX idx_created_at (created_at)
);

-- Tabel untuk admin/users
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    role ENUM('admin', 'staff', 'teacher') NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    last_login TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_username (username),
    INDEX idx_email (email),
    INDEX idx_role (role)
);

-- Tabel untuk pengaturan sistem
CREATE TABLE settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) UNIQUE NOT NULL,
    setting_value TEXT NOT NULL,
    description TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabel untuk log aktivitas
CREATE TABLE activity_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NULL,
    action VARCHAR(100) NOT NULL,
    description TEXT NOT NULL,
    ip_address VARCHAR(45) NULL,
    user_agent TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_user_id (user_id),
    INDEX idx_action (action),
    INDEX idx_created_at (created_at),
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- Tabel untuk file uploads
CREATE TABLE file_uploads (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NULL,
    file_name VARCHAR(255) NOT NULL,
    original_name VARCHAR(255) NOT NULL,
    file_path VARCHAR(500) NOT NULL,
    file_size INT NOT NULL,
    file_type VARCHAR(100) NOT NULL,
    upload_type ENUM('payment_proof', 'document', 'other') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_student_id (student_id),
    INDEX idx_upload_type (upload_type),
    INDEX idx_created_at (created_at),
    
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE
);

-- Insert default admin user
INSERT INTO users (username, password, email, full_name, role) VALUES 
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin@smpbinainformatika.sch.id', 'Administrator', 'admin');

-- Insert default settings
INSERT INTO settings (setting_key, setting_value, description) VALUES
('school_name', 'SMP Bina Informatika', 'Nama sekolah'),
('school_address', 'Jl. Tegal Rotan Raya No.8 A, Sawah Baru, Kec. Ciputat, Kota Tangerang Selatan, Banten 15412', 'Alamat sekolah'),
('school_phone', '083896226790', 'Nomor telepon sekolah'),
('school_email', 'info.sdpjointaro@spj.sch.id', 'Email sekolah'),
('registration_open', '1', 'Status buka tutup pendaftaran (1=open, 0=closed)'),
('registration_start_date', '2025-10-11', 'Tanggal mulai pendaftaran'),
('registration_end_date', '2026-06-30', 'Tanggal berakhir pendaftaran'),
('max_students', '200', 'Maksimal jumlah siswa yang diterima'),
('registration_fee', '500000', 'Biaya pendaftaran'),
('bank_account', '70148022526', 'Nomor rekening bank'),
('bank_name', 'BRI', 'Nama bank'),
('current_academic_year', '2025-2026', 'Tahun ajaran saat ini'),
('website_title', 'SMP Bina Informatika - Penerimaan Siswa Baru', 'Judul website'),
('website_description', 'Sekolah humanis yang mengedepankan kompetensi anak untuk berkembang sesuai dengan bakat dan minat masing-masing individu', 'Deskripsi website');

-- Buat view untuk statistik pendaftaran
CREATE VIEW registration_stats AS
SELECT 
    academic_year,
    COUNT(*) as total_registrations,
    SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending_count,
    SUM(CASE WHEN status = 'accepted' THEN 1 ELSE 0 END) as accepted_count,
    SUM(CASE WHEN status = 'rejected' THEN 1 ELSE 0 END) as rejected_count,
    SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed_count,
    SUM(CASE WHEN payment_status = 'paid' THEN 1 ELSE 0 END) as paid_count,
    SUM(CASE WHEN payment_status = 'verified' THEN 1 ELSE 0 END) as verified_count
FROM students 
GROUP BY academic_year;

-- View untuk statistik per kelas
CREATE VIEW class_stats AS
SELECT 
    academic_year,
    class,
    COUNT(*) as total_students,
    SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
    SUM(CASE WHEN status = 'accepted' THEN 1 ELSE 0 END) as accepted,
    SUM(CASE WHEN status = 'rejected' THEN 1 ELSE 0 END) as rejected
FROM students 
GROUP BY academic_year, class;

-- Buat trigger untuk update timestamp
DELIMITER //
CREATE TRIGGER update_students_timestamp
BEFORE UPDATE ON students
FOR EACH ROW
BEGIN
    SET NEW.updated_at = CURRENT_TIMESTAMP;
END//

CREATE TRIGGER update_contact_messages_timestamp
BEFORE UPDATE ON contact_messages
FOR EACH ROW
BEGIN
    SET NEW.updated_at = CURRENT_TIMESTAMP;
END//

CREATE TRIGGER update_users_timestamp
BEFORE UPDATE ON users
FOR EACH ROW
BEGIN
    SET NEW.updated_at = CURRENT_TIMESTAMP;
END//

CREATE TRIGGER update_settings_timestamp
BEFORE UPDATE ON settings
FOR EACH ROW
BEGIN
    SET NEW.updated_at = CURRENT_TIMESTAMP;
END//
DELIMITER ;

-- Stored Procedure untuk mendapatkan data siswa dengan filter
DELIMITER //
CREATE PROCEDURE GetStudentsWithFilter(
    IN p_academic_year VARCHAR(9),
    IN p_class ENUM('VII', 'VIII', 'IX'),
    IN p_status ENUM('pending', 'accepted', 'rejected', 'completed'),
    IN p_limit INT,
    IN p_offset INT
)
BEGIN
    SET @sql = CONCAT('
        SELECT 
            id, registration_type, academic_year, parent_email, class,
            name, birth_place, birth_date, parent_phone, nik, 
            origin_school, username, registration_date, status,
            payment_status, payment_date, test_score, notes
        FROM students 
        WHERE 1=1
    ');
    
    IF p_academic_year IS NOT NULL THEN
        SET @sql = CONCAT(@sql, ' AND academic_year = "', p_academic_year, '"');
    END IF;
    
    IF p_class IS NOT NULL THEN
        SET @sql = CONCAT(@sql, ' AND class = "', p_class, '"');
    END IF;
    
    IF p_status IS NOT NULL THEN
        SET @sql = CONCAT(@sql, ' AND status = "', p_status, '"');
    END IF;
    
    SET @sql = CONCAT(@sql, ' ORDER BY registration_date DESC');
    
    IF p_limit IS NOT NULL THEN
        SET @sql = CONCAT(@sql, ' LIMIT ', p_limit);
        IF p_offset IS NOT NULL THEN
            SET @sql = CONCAT(@sql, ' OFFSET ', p_offset);
        END IF;
    END IF;
    
    PREPARE stmt FROM @sql;
    EXECUTE stmt;
    DEALLOCATE PREPARE stmt;
END //
DELIMITER ;

-- Buat index untuk optimasi performa
CREATE INDEX idx_students_composite ON students(academic_year, status, payment_status);
CREATE INDEX idx_contact_messages_composite ON contact_messages(email, status, created_at);
CREATE INDEX idx_activity_logs_composite ON activity_logs(user_id, action, created_at);

-- Grant permissions (sesuaikan dengan user database Anda)
-- GRANT ALL PRIVILEGES ON smp_bina_informatika.* TO 'your_username'@'localhost';
-- FLUSH PRIVILEGES;

-- =====================================================
-- Selesai membuat struktur database
-- ===================================================== 