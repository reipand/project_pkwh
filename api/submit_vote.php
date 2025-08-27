<?php
session_start();
require_once '../config/koneksi.php';

header('Content-Type: application/json');

// Pastikan user sudah login dan belum memilih
if (!isset($_SESSION['is_logged_in']) || !isset($_POST['kandidat_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Permintaan tidak valid.']);
    exit;
}

$user_id = $_SESSION['user_id'];
$kandidat_id = (int)$_POST['kandidat_id'];

// Menggunakan transaksi untuk memastikan kedua query berhasil atau tidak sama sekali
$koneksi->begin_transaction();

try {
    // 1. Cek lagi apakah user sudah memilih (keamanan ganda)
    $stmt_check = $koneksi->prepare("SELECT status_memilih FROM pemilih WHERE id = ?");
    $stmt_check->bind_param("i", $user_id);
    $stmt_check->execute();
    $status_result = $stmt_check->get_result()->fetch_assoc();
    $stmt_check->close();

    if ($status_result['status_memilih'] == 1) {
        // Set status voting di session dan lempar exception
        $_SESSION['vote_status'] = 'already_voted';
        throw new Exception('Anda sudah memilih sebelumnya.');
    }

    // 2. Tambah jumlah suara kandidat
    $stmt_update_kandidat = $koneksi->prepare("UPDATE kandidat SET jumlah_suara = jumlah_suara + 1 WHERE id = ?");
    $stmt_update_kandidat->bind_param("i", $kandidat_id);
    $stmt_update_kandidat->execute();

  // 3. Update status pemilih menjadi "sudah memilih"
    $stmt_update_pemilih = $koneksi->prepare("UPDATE pemilih SET status_memilih = 1 WHERE id = ?");
    $stmt_update_pemilih->bind_param("i", $user_id);
    $stmt_update_pemilih->execute();
        
    // Jika semua berhasil, commit transaksi
    $koneksi->commit();
    
    // Set status voting di session dan kirim respons sukses
    $_SESSION['vote_status'] = 'success';
    echo json_encode(['status' => 'success', 'message' => 'Terima kasih! Suara Anda telah berhasil dicatat.']);

} catch (Exception $e) {
    $koneksi->rollback();
    
    $response = ['status' => 'error'];
    
    // Gunakan pesan error dari exception untuk detail
    $errorMessage = $e->getMessage();
    
    if (strpos($errorMessage, 'Anda sudah memilih') !== false) {
        $response['message'] = 'Anda sudah memilih. Tidak bisa memilih lagi.';
    } else {
        $response['message'] = 'Terjadi kesalahan saat menyimpan suara. Silakan coba lagi.';
    }

    echo json_encode($response);
}

$koneksi->close();
?>