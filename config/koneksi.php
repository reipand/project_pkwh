<?php
date_default_timezone_set('Asia/Jakarta');

// Konfigurasi database
$host = "localhost"; // Ganti dengan host database Anda
$user = "root"; // Ganti dengan username database Anda
$password = ""; // Ganti dengan password database Anda
$database = "db_pemilos"; // Ganti dengan nama database Anda

// Buat koneksi menggunakan mysqli (mode OOP)
$koneksi = new mysqli($host, $user, $password, $database);

// Cek koneksi
if ($koneksi->connect_error) {
    // Tampilkan pesan error yang lebih deskriptif
    die("Koneksi gagal: " . htmlspecialchars($koneksi->connect_error));
}

// Set charset untuk mencegah masalah encoding
$koneksi->set_charset("utf8mb4");

// Jika perlu, tambahkan logging error ke file atau sistem lain
// Misalnya, untuk debugging:
// error_log("Database connected successfully", 3, "/path/to/your/error.log");

// Kembalikan objek koneksi agar dapat digunakan di file lain
return $koneksi;
?>