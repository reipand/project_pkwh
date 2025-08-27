<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

session_start();
require_once '../config/koneksi.php';

// Pastikan hanya admin yang sudah login bisa akses
if (!isset($_SESSION['is_admin_logged_in']) || $_SESSION['is_admin_logged_in'] !== true) {
    http_response_code(403);
    echo json_encode(['status' => 'error', 'message' => 'Akses ditolak. Hanya untuk admin.']);
    exit;
}

// Data yang akan dikirim dalam respons JSON
$response = ["status" => "success"];

// Query 1: Ambil semua hasil suara kandidat
$sql_results = "SELECT nama_lengkap, jumlah_suara, foto_path FROM kandidat ORDER BY jumlah_suara DESC";
$result_results = $koneksi->query($sql_results);

$all_results = [];
if ($result_results->num_rows > 0) {
    while($row = $result_results->fetch_assoc()) {
        $all_results[] = $row;
    }
}

// Menentukan Ketos dan Waketos
$response['ketos'] = $all_results[0] ?? null;
$response['waketos'] = $all_results[1] ?? null;
$response['all_results'] = $all_results;

// Query 2: Total Registered Voters dari tabel 'pemilih'
$sql_voters = "SELECT COUNT(id) AS total_voters FROM pemilih";
$result_voters = $koneksi->query($sql_voters);
$row_voters = $result_voters->fetch_assoc();
$response['total_voters'] = (int)$row_voters['total_voters'];

// Query 3: Total Votes dari tabel 'pemilih' (berdasarkan kolom status_memilih)
$sql_votes = "SELECT COUNT(status_memilih) AS total_votes FROM pemilih WHERE status_memilih = 1";
$result_votes = $koneksi->query($sql_votes);
$row_votes = $result_votes->fetch_assoc();
$response['total_votes'] = (int)$row_votes['total_votes'];

// Query 4: New Registrations (pendaftar baru hari ini)
// Asumsi ada kolom 'tanggal_daftar' (DATETIME/DATE) di tabel 'pemilih'
$sql_registered_today = "SELECT COUNT(id) AS total FROM pemilih";
$result_registered = $koneksi->query($sql_registered_today);
$row_registered = $result_registered->fetch_assoc();
$response['total_registered'] = (int)$row_registered['total'];

// Kirim respons JSON
echo json_encode($response);

$koneksi->close();
?>