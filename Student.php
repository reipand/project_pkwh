<?php
/**
 * Model Student
 * Mengelola operasi database untuk data siswa
 */

require_once '../config/database.php';

class Student {
    private $conn;
    private $table_name = "students";

    public $id;
    public $registration_type;
    public $academic_year;
    public $parent_email;
    public $class;
    public $name;
    public $birth_place;
    public $birth_date;
    public $parent_phone;
    public $nik;
    public $password;
    public $origin_school;
    public $username;
    public $registration_date;
    public $status;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Membuat siswa baru
    public function create() {
        $query = "INSERT INTO " . $this->table_name . "
                SET
                    registration_type = :registration_type,
                    academic_year = :academic_year,
                    parent_email = :parent_email,
                    class = :class,
                    name = :name,
                    birth_place = :birth_place,
                    birth_date = :birth_date,
                    parent_phone = :parent_phone,
                    nik = :nik,
                    password = :password,
                    origin_school = :origin_school,
                    username = :username,
                    registration_date = NOW(),
                    status = 'pending'";

        $stmt = $this->conn->prepare($query);

        // Sanitasi input
        $this->registration_type = htmlspecialchars(strip_tags($this->registration_type));
        $this->academic_year = htmlspecialchars(strip_tags($this->academic_year));
        $this->parent_email = htmlspecialchars(strip_tags($this->parent_email));
        $this->class = htmlspecialchars(strip_tags($this->class));
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->birth_place = htmlspecialchars(strip_tags($this->birth_place));
        $this->birth_date = htmlspecialchars(strip_tags($this->birth_date));
        $this->parent_phone = htmlspecialchars(strip_tags($this->parent_phone));
        $this->nik = htmlspecialchars(strip_tags($this->nik));
        $this->password = password_hash($this->password, PASSWORD_DEFAULT);
        $this->origin_school = htmlspecialchars(strip_tags($this->origin_school));
        $this->username = htmlspecialchars(strip_tags($this->username));

        // Bind parameter
        $stmt->bindParam(":registration_type", $this->registration_type);
        $stmt->bindParam(":academic_year", $this->academic_year);
        $stmt->bindParam(":parent_email", $this->parent_email);
        $stmt->bindParam(":class", $this->class);
        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":birth_place", $this->birth_place);
        $stmt->bindParam(":birth_date", $this->birth_date);
        $stmt->bindParam(":parent_phone", $this->parent_phone);
        $stmt->bindParam(":nik", $this->nik);
        $stmt->bindParam(":password", $this->password);
        $stmt->bindParam(":origin_school", $this->origin_school);
        $stmt->bindParam(":username", $this->username);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Membaca semua siswa
    public function read() {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY registration_date DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Membaca siswa berdasarkan ID
    public function readOne() {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = ? LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if($row) {
            $this->registration_type = $row['registration_type'];
            $this->academic_year = $row['academic_year'];
            $this->parent_email = $row['parent_email'];
            $this->class = $row['class'];
            $this->name = $row['name'];
            $this->birth_place = $row['birth_place'];
            $this->birth_date = $row['birth_date'];
            $this->parent_phone = $row['parent_phone'];
            $this->nik = $row['nik'];
            $this->origin_school = $row['origin_school'];
            $this->username = $row['username'];
            $this->registration_date = $row['registration_date'];
            $this->status = $row['status'];
            return true;
        }
        return false;
    }

    // Update status siswa
    public function updateStatus() {
        $query = "UPDATE " . $this->table_name . "
                SET status = :status
                WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        $this->status = htmlspecialchars(strip_tags($this->status));
        $this->id = htmlspecialchars(strip_tags($this->id));

        $stmt->bindParam(':status', $this->status);
        $stmt->bindParam(':id', $this->id);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Cek username sudah ada atau belum
    public function checkUsername($username) {
        $query = "SELECT id FROM " . $this->table_name . " WHERE username = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $username);
        $stmt->execute();
        
        if($stmt->rowCount() > 0) {
            return true; // Username sudah ada
        }
        return false; // Username belum ada
    }

    // Generate username unik
    public function generateUsername($name) {
        $base_username = strtolower(str_replace(' ', '', $name));
        $username = $base_username;
        $counter = 1;
        
        while($this->checkUsername($username)) {
            $username = $base_username . $counter;
            $counter++;
        }
        
        return $username;
    }
}
?> 