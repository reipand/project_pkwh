<?php
session_start();
// 1. Panggil autoloader Composer
require_once '../vendor/autoload.php';

// Panggil namespace FPDI
use setasign\Fpdi\Fpdi;

require_once '../config/koneksi.php';

if (!isset($_SESSION['is_admin_logged_in']) || $_SESSION['is_admin_logged_in'] !== true) {
    header("Location: admin_login.php");
    exit;
}

// ===================================================================
//  LANGKAH A: PENGAMBILAN DATA
// ===================================================================

// Ambil hasil voting
$sql_results = "SELECT nama_lengkap, jumlah_suara FROM kandidat ORDER BY jumlah_suara DESC";
$result_results = $koneksi->query($sql_results);
$all_results = [];
if ($result_results->num_rows > 0) {
    while ($row = $result_results->fetch_assoc()) {
        $all_results[] = $row;
    }
}
$total_akumulasi_suara = array_sum(array_column($all_results, 'jumlah_suara'));

// Hitung total suara masuk & pemilih terdaftar
$sql_total_votes = "SELECT COUNT(id) AS total FROM pemilih WHERE status_memilih = 1";
$result_total_votes = $koneksi->query($sql_total_votes);
$total_votes = $result_total_votes->fetch_assoc()['total'];

$sql_total_voters = "SELECT COUNT(id) AS total FROM pemilih";
$result_total_voters = $koneksi->query($sql_total_voters);
$total_voters = $result_total_voters->fetch_assoc()['total'];

// Ambil daftar pemilih (sudah dan belum memilih)
$sql_voted = "SELECT nama_lengkap, nis FROM pemilih WHERE status_memilih = 1 ORDER BY nama_lengkap ASC";
$result_voted = $koneksi->query($sql_voted);
$voted_list = [];
if ($result_voted->num_rows > 0) {
    while ($row = $result_voted->fetch_assoc()) {
        $voted_list[] = $row;
    }
}

$sql_not_voted = "SELECT nama_lengkap, nis FROM pemilih WHERE status_memilih = 0 ORDER BY nama_lengkap ASC";
$result_not_voted = $koneksi->query($sql_not_voted);
$not_voted_list = [];
if ($result_not_voted->num_rows > 0) {
    while ($row = $result_not_voted->fetch_assoc()) {
        $not_voted_list[] = $row;
    }
}
$koneksi->close();

// ===================================================================
//  LANGKAH B: PROSES GENERATE PDF MENGGUNAKAN FPDI
// ===================================================================

// Buat instance FPDI
$pdf = new Fpdi();
$pdf->AddPage();

// Atur path ke file template PDF
$templatePath = 'template_laporan.pdf';

// Cek apakah file template ada
if (!file_exists($templatePath)) {
    die("Error: File template PDF tidak ditemukan. Pastikan file '" . $templatePath . "' ada di direktori yang benar.");
}

// Impor halaman pertama dari template
$pdf->setSourceFile($templatePath);
$templateId = $pdf->importPage(1);
$size = $pdf->getTemplateSize($templateId);

// Gunakan halaman yang diimpor sebagai latar belakang
$pdf->useTemplate($templateId);

// Sekarang, tambahkan konten laporan ke PDF
$pdf->SetFont('Helvetica', '', 12);
$pdf->SetTextColor(0, 0, 0);

// Tambahkan ringkasan suara
$pdf->SetY(60); // Sesuaikan posisi Y agar tidak menimpa header
$pdf->Cell(0, 10, 'Total suara masuk: ' . $total_votes . ' dari ' . $total_voters . ' pemilih terdaftar.', 0, 1, 'C');

// Judul 'Persentase Hasil Suara'
$pdf->SetY(80); // Sesuaikan posisi Y
$pdf->SetFont('Helvetica', 'B', 14);
$pdf->Cell(0, 10, 'Persentase Hasil Suara', 0, 1, 'C');
$pdf->Ln(5);

// Tabel Hasil Suara
$pdf->SetFont('Helvetica', 'B', 10);
$pdf->Cell(80, 7, 'Nama Kandidat', 1);
$pdf->Cell(40, 7, 'Jumlah Suara', 1);
$pdf->Cell(40, 7, 'Persentase', 1, 1);
$pdf->SetFont('Helvetica', '', 10);
foreach ($all_results as $kandidat) {
    $percentage = ($total_akumulasi_suara > 0) ? ($kandidat['jumlah_suara'] / $total_akumulasi_suara) * 100 : 0;
    $pdf->Cell(80, 7, $kandidat['nama_lengkap'], 1);
    $pdf->Cell(40, 7, $kandidat['jumlah_suara'], 1);
    $pdf->Cell(40, 7, round($percentage, 2) . '%', 1, 1);
}
$pdf->Ln(10);

// Judul 'Pemilih Sudah Memberikan Suara'
$pdf->SetFont('Helvetica', 'B', 12);
$pdf->Cell(0, 10, 'Pemilih Sudah Memberikan Suara (' . count($voted_list) . ')', 0, 1);
$pdf->Ln(5);

// Tabel Pemilih Sudah Memilih
$pdf->SetFont('Helvetica', 'B', 10);
$pdf->Cell(80, 7, 'Nama Lengkap', 1);
$pdf->Cell(40, 7, 'NIS', 1, 1);
$pdf->SetFont('Helvetica', '', 10);
if (count($voted_list) > 0) {
    foreach ($voted_list as $voter) {
        $pdf->Cell(80, 7, $voter['nama_lengkap'], 1);
        $pdf->Cell(40, 7, $voter['nis'], 1, 1);
    }
} else {
    $pdf->Cell(120, 7, 'Tidak ada data.', 1, 1, 'C');
}
$pdf->Ln(10);

// Judul 'Pemilih Belum Memberikan Suara'
$pdf->SetFont('Helvetica', 'B', 12);
$pdf->Cell(0, 10, 'Pemilih Belum Memberikan Suara (' . count($not_voted_list) . ')', 0, 1);
$pdf->Ln(5);

// Tabel Pemilih Belum Memilih
$pdf->SetFont('Helvetica', 'B', 10);
$pdf->Cell(80, 7, 'Nama Lengkap', 1);
$pdf->Cell(40, 7, 'NIS', 1, 1);
$pdf->SetFont('Helvetica', '', 10);
if (count($not_voted_list) > 0) {
    foreach ($not_voted_list as $voter) {
        $pdf->Cell(80, 7, $voter['nama_lengkap'], 1);
        $pdf->Cell(40, 7, $voter['nis'], 1, 1);
    }
} else {
    $pdf->Cell(120, 7, 'Semua pemilih sudah memilih.', 1, 1, 'C');
}

// Outputkan PDF
$pdf->Output("laporan-hasil-voting-" . date("Y-m-d") . ".pdf", "D");
?>