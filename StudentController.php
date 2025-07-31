<?php
/**
 * Student Controller
 * Mengelola logika bisnis untuk operasi siswa
 */

require_once '../models/Student.php';
require_once '../config/database.php';

class StudentController {
    private $student;
    private $database;

    public function __construct() {
        $this->database = new Database();
        $db = $this->database->getConnection();
        $this->student = new Student($db);
    }

    // Menangani registrasi siswa baru
    public function registerStudent($data) {
        try {
            // Validasi input
            $validation = $this->validateRegistrationData($data);
            if (!$validation['valid']) {
                return [
                    'success' => false,
                    'message' => $validation['message']
                ];
            }

            // Set data siswa
            $this->student->registration_type = $data['registration_type'];
            $this->student->academic_year = $data['academic_year'];
            $this->student->parent_email = $data['parent_email'];
            $this->student->class = $data['class'];
            $this->student->name = $data['name'];
            $this->student->birth_place = $data['birth_place'];
            $this->student->birth_date = $data['birth_date'];
            $this->student->parent_phone = $data['parent_phone'];
            $this->student->nik = $data['nik'];
            $this->student->password = $data['password'];
            $this->student->origin_school = $data['origin_school'];

            // Generate username unik
            $this->student->username = $this->student->generateUsername($data['name']);

            // Simpan ke database
            if ($this->student->create()) {
                return [
                    'success' => true,
                    'message' => 'Registrasi berhasil!',
                    'username' => $this->student->username,
                    'data' => [
                        'name' => $this->student->name,
                        'username' => $this->student->username,
                        'academic_year' => $this->student->academic_year,
                        'class' => $this->student->class
                    ]
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Gagal menyimpan data registrasi'
                ];
            }
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ];
        }
    }

    // Validasi data registrasi
    private function validateRegistrationData($data) {
        $required_fields = [
            'registration_type', 'academic_year', 'parent_email', 'class',
            'name', 'birth_place', 'birth_date', 'parent_phone', 'nik',
            'password', 'origin_school'
        ];

        foreach ($required_fields as $field) {
            if (empty($data[$field])) {
                return [
                    'valid' => false,
                    'message' => "Field $field harus diisi"
                ];
            }
        }

        // Validasi email orang tua
        if (!filter_var($data['parent_email'], FILTER_VALIDATE_EMAIL)) {
            return [
                'valid' => false,
                'message' => 'Format email orang tua tidak valid'
            ];
        }

        // Validasi NIK (16 digit)
        if (!preg_match('/^\d{16}$/', $data['nik'])) {
            return [
                'valid' => false,
                'message' => 'NIK harus 16 digit angka'
            ];
        }

        // Validasi nomor telepon
        if (!preg_match('/^(\+62|62|0)8[1-9][0-9]{6,9}$/', $data['parent_phone'])) {
            return [
                'valid' => false,
                'message' => 'Format nomor telepon tidak valid'
            ];
        }

        // Validasi password (minimal 6 karakter)
        if (strlen($data['password']) < 6) {
            return [
                'valid' => false,
                'message' => 'Password minimal 6 karakter'
            ];
        }

        // Validasi tanggal lahir
        $birth_date = DateTime::createFromFormat('Y-m-d', $data['birth_date']);
        if (!$birth_date || $birth_date->format('Y-m-d') !== $data['birth_date']) {
            return [
                'valid' => false,
                'message' => 'Format tanggal lahir tidak valid (YYYY-MM-DD)'
            ];
        }

        return ['valid' => true];
    }

    // Mendapatkan semua data siswa
    public function getAllStudents() {
        try {
            $stmt = $this->student->read();
            $students = [];
            
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $students[] = $row;
            }
            
            return [
                'success' => true,
                'data' => $students
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Gagal mengambil data siswa: ' . $e->getMessage()
            ];
        }
    }

    // Mendapatkan data siswa berdasarkan ID
    public function getStudentById($id) {
        try {
            $this->student->id = $id;
            
            if ($this->student->readOne()) {
                return [
                    'success' => true,
                    'data' => [
                        'id' => $this->student->id,
                        'registration_type' => $this->student->registration_type,
                        'academic_year' => $this->student->academic_year,
                        'parent_email' => $this->student->parent_email,
                        'class' => $this->student->class,
                        'name' => $this->student->name,
                        'birth_place' => $this->student->birth_place,
                        'birth_date' => $this->student->birth_date,
                        'parent_phone' => $this->student->parent_phone,
                        'nik' => $this->student->nik,
                        'origin_school' => $this->student->origin_school,
                        'username' => $this->student->username,
                        'registration_date' => $this->student->registration_date,
                        'status' => $this->student->status
                    ]
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Siswa tidak ditemukan'
                ];
            }
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Gagal mengambil data siswa: ' . $e->getMessage()
            ];
        }
    }

    // Update status siswa
    public function updateStudentStatus($id, $status) {
        try {
            $this->student->id = $id;
            $this->student->status = $status;
            
            if ($this->student->updateStatus()) {
                return [
                    'success' => true,
                    'message' => 'Status siswa berhasil diperbarui'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Gagal memperbarui status siswa'
                ];
            }
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Gagal memperbarui status: ' . $e->getMessage()
            ];
        }
    }

    // Mendapatkan statistik registrasi
    public function getRegistrationStats() {
        try {
            $db = $this->database->getConnection();
            
            $query = "SELECT 
                        COUNT(*) as total_registrations,
                        SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
                        SUM(CASE WHEN status = 'accepted' THEN 1 ELSE 0 END) as accepted,
                        SUM(CASE WHEN status = 'rejected' THEN 1 ELSE 0 END) as rejected
                      FROM students";
            
            $stmt = $db->prepare($query);
            $stmt->execute();
            
            $stats = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return [
                'success' => true,
                'data' => $stats
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Gagal mengambil statistik: ' . $e->getMessage()
            ];
        }
    }
}
?> 