<?php
session_start();
require_once '../config/koneksi.php';

if (!isset($_SESSION['is_admin_logged_in']) || $_SESSION['is_admin_logged_in'] !== true) {
    header("Location: admin_login.php");
    exit;
}

// Ambil hasil voting
$sql_results = "SELECT nama_lengkap, jumlah_suara, foto_path FROM kandidat ORDER BY jumlah_suara DESC";
$result_results = $koneksi->query($sql_results);

$all_results = [];
if ($result_results->num_rows > 0) {
    while ($row = $result_results->fetch_assoc()) {
        $all_results[] = $row;
    }
}

// Hitung total suara yang masuk
$sql_total_votes = "SELECT COUNT(id) AS total FROM pemilih WHERE status_memilih = 1";
$result_total_votes = $koneksi->query($sql_total_votes);
$row_total_votes = $result_total_votes->fetch_assoc();
$total_votes = $row_total_votes['total'];

// Hitung total pemilih terdaftar
$sql_total_voters = "SELECT COUNT(id) AS total FROM pemilih";
$result_total_voters = $koneksi->query($sql_total_voters);
$row_total_voters = $result_total_voters->fetch_assoc();
$total_voters = $row_total_voters['total'];

$ketos_terpilih = $all_results[0] ?? null;
$waketos_terpilih = $all_results[1] ?? null;

// Ambil daftar pemilih yang sudah memilih
$sql_voted = "SELECT nama_lengkap, nis FROM pemilih WHERE status_memilih = 1 ORDER BY nama_lengkap ASC";
$result_voted = $koneksi->query($sql_voted);

$voted_list = [];
if ($result_voted->num_rows > 0) {
    while ($row = $result_voted->fetch_assoc()) {
        $voted_list[] = $row;
    }
}

// Ambil daftar pemilih yang belum memilih
$sql_not_voted = "SELECT nama_lengkap, nis FROM pemilih WHERE status_memilih = 0 ORDER BY nama_lengkap ASC";
$result_not_voted = $koneksi->query($sql_not_voted);

$not_voted_list = [];
if ($result_not_voted->num_rows > 0) {
    while ($row = $result_not_voted->fetch_assoc()) {
        $not_voted_list[] = $row;
    }
}

$koneksi->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan & Hasil - Admin BiVOTE</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="../assets/css/custom.css">
    <style>
        body { font-family: 'Poppins', sans-serif; }
        .table-container {
            max-height: 400px;
            overflow-y: auto;
        }
    </style>
</head>
<body class="bg-gray-100 min-h-screen flex">
    <?php include '../includes/sidebar_admin.php'; ?>

    <div class="flex-1 flex flex-col min-h-screen">
        <?php include '../includes/header_admin.php'; ?>

        <main class="flex-1 p-8">
            <div class="mb-8 flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800">Laporan & Hasil Voting</h1>
                    <p class="text-gray-600 mt-1">Laporan terperinci mengenai hasil pemungutan suara.</p>
                </div>
                <a href="export_pdf.php" target="_blank" class="bg-indigo-600 text-white font-medium py-2 px-4 rounded-full hover:bg-indigo-700 transition-colors">
                    Ekspor ke PDF
                </a>
            </div>

            <div class="bg-white p-6 rounded-3xl shadow-lg mb-8">
                <h2 class="text-2xl font-bold text-gray-800 mb-4">Hasil Pemilihan Sementara</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <?php if ($ketos_terpilih): ?>
                        <div class="flex flex-col items-center p-6 bg-gray-50 rounded-2xl">
                            <img src="<?php echo htmlspecialchars($ketos_terpilih['foto_path']); ?>" alt="Foto Ketua Terpilih" class="w-28 h-28 object-cover rounded-full mb-3 shadow">
                            <h3 class="text-lg font-semibold text-gray-800">Ketua Terpilih</h3>
                            <p class="text-xl font-bold text-indigo-600"><?php echo htmlspecialchars($ketos_terpilih['nama_lengkap']); ?></p>
                            <p class="text-gray-500">Suara: <?php echo htmlspecialchars($ketos_terpilih['jumlah_suara']); ?></p>
                        </div>
                    <?php endif; ?>
                    <?php if ($waketos_terpilih): ?>
                        <div class="flex flex-col items-center p-6 bg-gray-50 rounded-2xl">
                            <img src="<?php echo htmlspecialchars($waketos_terpilih['foto_path']); ?>" alt="Foto Wakil Ketua Terpilih" class="w-28 h-28 object-cover rounded-full mb-3 shadow">
                            <h3 class="text-lg font-semibold text-gray-800">Wakil Ketua Terpilih</h3>
                            <p class="text-xl font-bold text-indigo-600"><?php echo htmlspecialchars($waketos_terpilih['nama_lengkap']); ?></p>
                            <p class="text-gray-500">Suara: <?php echo htmlspecialchars($waketos_terpilih['jumlah_suara']); ?></p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="bg-white p-6 rounded-3xl shadow-lg mb-8">
    <h2 class="text-2xl font-bold text-gray-800 mb-4">Persentase Hasil Suara</h2>
    <p class="text-sm text-gray-600 mb-4">Total suara masuk: <?php echo $total_votes; ?> dari <?php echo $total_voters; ?> pemilih terdaftar</p>
    <div class="space-y-4">
        <?php
        // LANGKAH 1: Hitung total akumulasi suara dari semua kandidat
        // Ini adalah cara efisien untuk menjumlahkan kolom 'jumlah_suara' dari array $all_results
        $total_akumulasi_suara = array_sum(array_column($all_results, 'jumlah_suara'));

        foreach ($all_results as $kandidat):
            // LANGKAH 2: Hitung persentase menggunakan pembagi yang benar
            // Pastikan total akumulasi suara tidak nol untuk menghindari error pembagian dengan nol
            $percentage = ($total_akumulasi_suara > 0) ? ($kandidat['jumlah_suara'] / $total_akumulasi_suara) * 100 : 0;
        ?>
            <div class="flex items-center space-x-4">
                <div class="w-40 text-right font-medium text-gray-700"><?php echo htmlspecialchars($kandidat['nama_lengkap']); ?></div>
                <div class="flex-1 bg-gray-200 rounded-full h-6">
                    <div class="bg-indigo-600 h-6 rounded-full" style="width: <?php echo round($percentage, 2); ?>%;"></div>
                </div>
                <span class="w-16 font-bold text-gray-800"><?php echo round($percentage, 2); ?>%</span>
            </div>
        <?php endforeach; ?>
    </div>
</div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="bg-white p-6 rounded-3xl shadow-lg">
                    <h2 class="text-2xl font-bold text-gray-800 mb-4">Pemilih Sudah Memberikan Suara (<?php echo count($voted_list); ?>)</h2>
                    <div class="table-container">
                        <table class="min-w-full text-left">
                            <thead>
                                <tr class="text-gray-600 border-b">
                                    <th class="py-2 px-4">Nama Lengkap</th>
                                    <th class="py-2 px-4">NIS</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (count($voted_list) > 0): ?>
                                    <?php foreach ($voted_list as $voter): ?>
                                        <tr class="border-b">
                                            <td class="py-2 px-4"><?php echo htmlspecialchars($voter['nama_lengkap']); ?></td>
                                            <td class="py-2 px-4"><?php echo htmlspecialchars($voter['nis']); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="2" class="py-4 text-center text-gray-500">Belum ada pemilih yang memberikan suara.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-3xl shadow-lg">
                    <h2 class="text-2xl font-bold text-gray-800 mb-4">Pemilih Belum Memberikan Suara (<?php echo count($not_voted_list); ?>)</h2>
                    <div class="table-container">
                        <table class="min-w-full text-left">
                            <thead>
                                <tr class="text-gray-600 border-b">
                                    <th class="py-2 px-4">Nama Lengkap</th>
                                    <th class="py-2 px-4">NIS</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (count($not_voted_list) > 0): ?>
                                    <?php foreach ($not_voted_list as $voter): ?>
                                        <tr class="border-b">
                                            <td class="py-2 px-4"><?php echo htmlspecialchars($voter['nama_lengkap']); ?></td>
                                            <td class="py-2 px-4"><?php echo htmlspecialchars($voter['nis']); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="2" class="py-4 text-center text-gray-500">Semua pemilih sudah memberikan suara.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>