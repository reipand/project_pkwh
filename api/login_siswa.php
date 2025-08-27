<?php
session_start();
require_once '../config/koneksi.php';

header('Content-Type: application/json');

$response = ['status' => 'error', 'message' => 'Input tidak valid.'];

if (isset($_POST['nis']) && isset($_POST['password'])) {
    $nis = $_POST['nis'];
    $tanggal_lahir = $_POST['password']; // Format DD/MM/YY

    // Validasi sederhana format DD/MM/YY
    if (!preg_match('/^\d{2}\/\d{2}\/\d{2}$/', $tanggal_lahir)) {
        $response['message'] = 'Format tanggal lahir tidak valid. Gunakan format DD/MM/YY.';
        echo json_encode($response);
        exit;
    }

    // Query menggunakan prepared statement untuk keamanan
    $stmt = $koneksi->prepare("SELECT id, nis, nama_lengkap, status_memilih, id_kandidat_dipilih FROM pemilih WHERE nis = ? AND tanggal_lahir = ?");
    $stmt->bind_param("ss", $nis, $tanggal_lahir);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        
        $_SESSION['user_id'] = $user['id']; // Menambahkan ID pengguna ke sesi
        $_SESSION['user_nis'] = $user['nis'];
        $_SESSION['user_nama'] = $user['nama_lengkap'];
        $_SESSION['is_logged_in'] = true;

        if ($user['status_memilih'] == 1) {
            // Jika sudah memilih, alihkan ke halaman konfirmasi
            $_SESSION['vote_status'] = 'already_voted';
            $response = [
                'status' => 'success',
                'message' => 'Login berhasil!',
                'redirect' => 'vote_confirmation.php' 
            ];
        } else {
            // Jika belum memilih, alihkan ke dashboard
            $response = [
                'status' => 'success',
                'message' => 'Login berhasil!',
                'redirect' => 'dashboard.php' 
            ];
        }
    } else {
        $response['message'] = 'NIS atau Tanggal Lahir salah.';
    }
    $stmt->close();
}

echo json_encode($response);

$koneksi->close();
?>