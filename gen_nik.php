<?php

/**
 * Menghasilkan nomor 16 digit dengan format NIK (Nomor Induk Kependudukan).
 * PENTING: NIK yang dihasilkan adalah FIKTIF dan TIDAK VALID untuk penggunaan resmi.
 *
 * @param string $kodeWilayah 6 digit kode provinsi, kota/kab, dan kecamatan (e.g., '327301' untuk Bandung Kidul).
 * @param string $tanggalLahir Format 'Y-m-d' (e.g., '1995-08-17').
 * @param string $gender 'pria' atau 'wanita'.
 * @return string Nomor 16 digit fiktif.
 */
function generateNikFiktif($kodeWilayah, $tanggalLahir, $gender) {
    // Validasi dasar
    if (strlen($kodeWilayah) !== 6 || !ctype_digit($kodeWilayah)) {
        return "Error: Kode wilayah harus 6 digit angka.";
    }

    try {
        $tanggal = new DateTime($tanggalLahir);
    } catch (Exception $e) {
        return "Error: Format tanggal lahir tidak valid.";
    }

    // Ambil komponen tanggal, bulan, dan tahun
    $hari = (int)$tanggal->format('d');
    $bulan = $tanggal->format('m');
    $tahun = $tanggal->format('y');

    // Terapkan aturan gender: tambah 40 pada hari jika wanita
    if (strtolower($gender) === 'wanita') {
        $hari += 40;
    }

    // Pastikan hari menjadi 2 digit (misal: 5 menjadi '05')
    $hariFormatted = str_pad($hari, 2, '0', STR_PAD_LEFT);

    // Hasilkan 4 digit nomor urut acak
    $nomorUrut = str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);

    // Gabungkan semua komponen menjadi NIK fiktif
    $nik = $kodeWilayah . $hariFormatted . $bulan . $tahun . $nomorUrut;

    return $nik;
}

// --- Contoh Penggunaan ---

// Kode wilayah untuk Kecamatan Menteng, Jakarta Pusat (Provinsi DKI Jakarta)
$kodeWilayahContoh = '317106'; 

// Contoh 1: Laki-laki lahir pada Hari Kemerdekaan Indonesia
$tanggalLahirPria = '1995-08-17';
$nikPria = generateNikFiktif($kodeWilayahContoh, $tanggalLahirPria, 'pria');
echo "NIK Fiktif (Pria):   " . $nikPria . "<br>";

// Contoh 2: Perempuan lahir pada Hari Kartini
$tanggalLahirWanita = '2001-04-21';
$nikWanita = generateNikFiktif($kodeWilayahContoh, $tanggalLahirWanita, 'wanita');
echo "NIK Fiktif (Wanita): " . $nikWanita . "<br>";

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generator NIK Fiktif</title>
    <style>
        body { font-family: sans-serif; max-width: 600px; margin: 40px auto; padding: 20px; border: 1px solid #ddd; border-radius: 8px; }
        h1, p { text-align: center; }
        .warning { color: #d93025; font-weight: bold; text-align: center; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; }
        input, select, button { width: 100%; padding: 10px; font-size: 16px; border-radius: 5px; border: 1px solid #ccc; box-sizing: border-box; }
        button { background-color: #007bff; color: white; cursor: pointer; border: none; }
        button:hover { background-color: #0056b3; }
        #hasil { margin-top: 20px; padding: 15px; background-color: #e9f7ef; border: 1px solid #5cb85c; border-radius: 5px; text-align: center; font-size: 1.2em; font-weight: bold; word-wrap: break-word; }
    </style>
</head>
<body>

    <h1>Generator NIK Fiktif</h1>
    <p class="warning">PENTING: Nomor ini acak, fiktif, dan tidak untuk penggunaan resmi!</p>

    <div class="form-group">
        <label for="kodeWilayah">Kode Wilayah (6 Digit):</label>
        <input type="text" id="kodeWilayah" value="327301" maxlength="6">
    </div>

    <div class="form-group">
        <label for="tanggalLahir">Tanggal Lahir:</label>
        <input type="date" id="tanggalLahir" value="1998-10-28">
    </div>

    <div class="form-group">
        <label for="gender">Jenis Kelamin:</label>
        <select id="gender">
            <option value="pria">Pria</option>
            <option value="wanita">Wanita</option>
        </select>
    </div>

    <button onclick="generateNik()">Buat NIK</button>

    <div id="hasil" style="display:none;"></div>

    <script>
        function generateNik() {
            const kodeWilayah = document.getElementById('kodeWilayah').value;
            const tanggalLahirValue = document.getElementById('tanggalLahir').value;
            const gender = document.getElementById('gender').value;
            const hasilDiv = document.getElementById('hasil');

            if (kodeWilayah.length !== 6 || !/^\d+$/.test(kodeWilayah)) {
                hasilDiv.textContent = 'Error: Kode Wilayah harus 6 digit angka.';
                hasilDiv.style.borderColor = '#d93025';
                hasilDiv.style.backgroundColor = '#fce8e6';
                hasilDiv.style.display = 'block';
                return;
            }
            
            if (!tanggalLahirValue) {
                hasilDiv.textContent = 'Error: Silakan pilih tanggal lahir.';
                hasilDiv.style.borderColor = '#d93025';
                hasilDiv.style.backgroundColor = '#fce8e6';
                hasilDiv.style.display = 'block';
                return;
            }

            const tanggal = new Date(tanggalLahirValue);
            
            let hari = tanggal.getDate();
            const bulan = (tanggal.getMonth() + 1).toString().padStart(2, '0');
            const tahun = tanggal.getFullYear().toString().slice(-2);

            if (gender === 'wanita') {
                hari += 40;
            }
            
            const hariFormatted = hari.toString().padStart(2, '0');
            const nomorUrut = Math.floor(1000 + Math.random() * 9000).toString();

            const nikFiktif = kodeWilayah + hariFormatted + bulan + tahun + nomorUrut;

            hasilDiv.textContent = nikFiktif;
            hasilDiv.style.borderColor = '#5cb85c';
            hasilDiv.style.backgroundColor = '#e9f7ef';
            hasilDiv.style.display = 'block';
        }
    </script>

</body>
</html>