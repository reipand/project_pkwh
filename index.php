<?php
/**
 * Halaman Utama - SMP Bina Informatika
 * Penerimaan Siswa Baru Tahun Ajaran 2025-2026
 */

$page_title = "Beranda";
include 'includes/header.php';
?>

<!-- Hero Section -->
<section class="hero-section">
    <div class="container">
        <div class="hero-content fade-in">
            <h1 class="hero-title">
                PENERIMAAN SISWA BARU
                <span class="text-primary-custom">TAHUN AJARAN 2025-2026</span>
            </h1>
            <p class="hero-description">
                Kami adalah sekolah humanis yang mengedepankan kompetensi anak untuk berkembang sesuai dengan bakat dan minat masing-masing individu
            </p>
            <div class="hero-buttons">
                <a href="#pendaftaran" class="btn btn-primary-custom">Daftar Sekarang</a>
                <a href="#informasi" class="btn btn-secondary-custom">Pelajari Lebih Lanjut</a>
            </div>
        </div>
    </div>
</section>

<!-- Gallery Preview Section -->
<section class="section bg-light">
    <div class="container">
        <div class="row">
            <div class="col-12 text-center mb-4">
                <h3 class="section-title">Galeri Kegiatan Sekolah</h3>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4 mb-4">
                <div class="gallery-item animate-fade-in-up">
                    <img src="assets/images/activity1.jpg" alt="LOKPD 2025" class="img-fluid">
                    <div class="gallery-overlay">
                        <h5>LOKPD 2025</h5>
                        <p>Kegiatan orientasi siswa baru</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="gallery-item animate-fade-in-up">
                    <img src="assets/images/activity2.jpg" alt="ProgsiT 2024" class="img-fluid">
                    <div class="gallery-overlay">
                        <h5>ProgsiT 2024</h5>
                        <p>Program studi teknologi informasi</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="gallery-item animate-fade-in-up">
                    <img src="assets/images/activity3.jpg" alt="Kegiatan Sekolah" class="img-fluid">
                    <div class="gallery-overlay">
                        <h5>Kegiatan Belajar</h5>
                        <p>Suasana belajar yang nyaman</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Procedure Section -->
<section id="prosedur" class="section procedure-section">
    <div class="container">
        <h2 class="section-title">Tata Cara Pendaftaran</h2>
        <div class="row">
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="procedure-card animate-slide-in-left">
                    <div class="procedure-icon">
                        <i class="fas fa-user-plus"></i>
                    </div>
                    <h3>Langkah 1</h3>
                    <p>Calon Siswa mendaftar dengan cara klik tombol 'Daftar Sekarang' kemudian mengisi data diri pada Form Registrasi untuk mendapatkan akun (Username dan Password)</p>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="procedure-card animate-slide-in-left">
                    <div class="procedure-icon">
                        <i class="fas fa-file-invoice"></i>
                    </div>
                    <h3>Langkah 2</h3>
                    <p>Jika selesai pendaftaran silakan login dengan username dan password. Kemudian lakukan pembayaran pendaftaran melalui bank BRI no rekening: 70148022526</p>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="procedure-card animate-slide-in-left">
                    <div class="procedure-icon">
                        <i class="fas fa-upload"></i>
                    </div>
                    <h3>Langkah 3</h3>
                    <p>Upload bukti transfer di menu Keuangan (Pembayaran Pendaftaran). Lengkapi Formulir pendaftaran secara lengkap</p>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="procedure-card animate-slide-in-right">
                    <div class="procedure-icon">
                        <i class="fas fa-clipboard-check"></i>
                    </div>
                    <h3>Langkah 4</h3>
                    <p>Mengikuti Tes Seleksi atau Observasi sesuai dengan jadwal yang telah ditentukan. Melakukan daftar ulang jika dinyatakan 'Diterima' dengan melakukan pembayaran SP melalui Virtual Account</p>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="procedure-card animate-slide-in-right">
                    <div class="procedure-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <h3>Langkah 5</h3>
                    <p>Lengkapi dokumen administrasi sebagai siswa SMP Bina Informatika</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Registration Section -->
<section id="pendaftaran" class="section registration-section">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <div class="text-center mb-5">
                    <h2 class="section-title">Form Registrasi</h2>
                    <p class="lead">Silakan isi data untuk mendapatkan Username dan Password</p>
                </div>
                
                <div class="registration-form">
                    <form id="registrationForm" method="POST" action="api/register.php">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="registration_type" class="form-label">Jenis Pendaftaran</label>
                                    <select name="registration_type" id="registration_type" class="form-control" required>
                                        <option value="">Pilih Jenis Pendaftaran</option>
                                        <option value="reguler">Reguler</option>
                                        <option value="prestasi">Prestasi</option>
                                        <option value="beasiswa">Beasiswa</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="academic_year" class="form-label">Tahun Pelajaran</label>
                                    <select name="academic_year" id="academic_year" class="form-control" required>
                                        <option value="">Pilih Tahun Pelajaran</option>
                                        <option value="2025-2026">2025-2026</option>
                                        <option value="2026-2027">2026-2027</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="parent_email" class="form-label">Email Orang Tua/Wali</label>
                                    <input type="email" name="parent_email" id="parent_email" class="form-control" placeholder="email@example.com" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="class" class="form-label">Kelas</label>
                                    <select name="class" id="class" class="form-control" required>
                                        <option value="">Pilih Kelas</option>
                                        <option value="VII">Kelas VII</option>
                                        <option value="VIII">Kelas VIII</option>
                                        <option value="IX">Kelas IX</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="name" class="form-label">Nama Lengkap</label>
                            <input type="text" name="name" id="name" class="form-control" required>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="birth_place" class="form-label">Tempat Lahir</label>
                                    <input type="text" name="birth_place" id="birth_place" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="birth_date" class="form-label">Tanggal Lahir</label>
                                    <input type="date" name="birth_date" id="birth_date" class="form-control" required>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="parent_phone" class="form-label">No HP Orang Tua/Wali</label>
                            <input type="tel" name="parent_phone" id="parent_phone" class="form-control" placeholder="08xxxxxxxxxx" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="nik" class="form-label">NIK (No Induk Kependudukan)</label>
                            <input type="text" name="nik" id="nik" class="form-control" placeholder="16 digit angka" maxlength="16" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="password" class="form-label">Password (Mohon Diingat)</label>
                            <input type="password" name="password" id="password" class="form-control" minlength="6" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="origin_school" class="form-label">Asal Sekolah</label>
                            <input type="text" name="origin_school" id="origin_school" class="form-control" required>
                        </div>
                        
                        <button type="submit" class="btn btn-submit">Daftar Sekarang</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Information Section -->
<section id="informasi" class="section">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <h2 class="section-title">Informasi Penerimaan Siswa Baru</h2>
                <div class="card shadow-lg">
                    <div class="card-body p-4">
                        <div class="row">
                            <div class="col-md-8">
                                <h4 class="text-primary-custom mb-3">Kepada Yth, Bapak/Ibu Calon Orang Tua Siswa</h4>
                                <p class="mb-3">
                                    Kami informasikan bahwa Penerimaan Siswa Baru Sekolah SMP Bina Informatika Ajaran 2026/2027 melalui sistem aplikasi pendaftaran akan dibuka pada tanggal <strong>11 Oktober 2025</strong>.
                                </p>
                                <p class="mb-3">
                                    Kami mengundang Bapak/Ibu calon orangtua murid untuk melakukan survei ke sekolah terlebih dahulu guna mendapatkan informasi yang lengkap terkait dengan kurikulum, kegiatan siswa, fasilitas, dan lain-lain.
                                </p>
                                <p class="mb-4">
                                    Untuk informasi lebih lanjut silahkan menghubungi kami melalui nomor WA Humas:
                                </p>
                                <div class="row">
                                    <div class="col-md-6">
                                        <p><strong>0813-1938-2002</strong> (TK)</p>
                                        <p><strong>0813-2218-7471</strong> (SD)</p>
                                    </div>
                                    <div class="col-md-6">
                                        <p><strong>0813-1938-2004</strong> (SMP)</p>
                                        <p><strong>0813-1938-2005</strong> (SMA)</p>
                                    </div>
                                </div>
                                <p class="text-end"><em>Terima kasih.</em></p>
                            </div>
                            <div class="col-md-4 text-center">
                                <div class="info-icon mb-3">
                                    <i class="fas fa-lightbulb fa-3x text-primary-custom"></i>
                                </div>
                                <p class="text-muted">Tanggal: 2025-06-17 00:00:00</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Gallery Section -->
<section id="gallery" class="section gallery-section">
    <div class="container">
        <h2 class="section-title">Galeri Kegiatan Sekolah</h2>
        <div class="gallery-grid">
            <div class="gallery-item">
                <img src="assets/images/gallery1.jpg" alt="Kegiatan Sekolah">
                <div class="gallery-overlay">
                    <h5>Upacara Bendera</h5>
                    <p>Kegiatan rutin setiap Senin</p>
                </div>
            </div>
            <div class="gallery-item">
                <img src="assets/images/gallery2.jpg" alt="Kegiatan Belajar">
                <div class="gallery-overlay">
                    <h5>Kelas Komputer</h5>
                    <p>Belajar teknologi informasi</p>
                </div>
            </div>
            <div class="gallery-item">
                <img src="assets/images/gallery3.jpg" alt="Kegiatan Mengajar">
                <div class="gallery-overlay">
                    <h5>Kegiatan Mengajar</h5>
                    <p>Suasana belajar yang interaktif</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Contact Section -->
<section id="kontak" class="section contact-section">
    <div class="container">
        <h2 class="section-title text-white">Hubungi Kami</h2>
        <div class="row">
            <div class="col-lg-4 mb-4">
                <div class="contact-info text-center">
                    <i class="fas fa-map-marker-alt"></i>
                    <h5>Lokasi</h5>
                    <p>Jl. Tegal Rotan Raya No.8 A, Sawah Baru, Kec. Ciputat, Kota Tangerang Selatan, Banten 15412</p>
                </div>
            </div>
            <div class="col-lg-4 mb-4">
                <div class="contact-info text-center">
                    <i class="fas fa-envelope"></i>
                    <h5>Email</h5>
                    <p>info@smpbinainformatika.sch.id</p>
                </div>
            </div>
            <div class="col-lg-4 mb-4">
                <div class="contact-info text-center">
                    <i class="fas fa-phone"></i>
                    <h5>Telepon</h5>
                    <p>083896226790<br>(Rizam Nuruzaman)</p>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <div class="contact-form">
                    <h4 class="text-center mb-4 text-white">Kirim Pesan</h4>
                    <form id="contactForm" method="POST" action="api/contact.php">
                        <div class="form-group">
                            <label for="contact_name" class="form-label text-white">Nama Lengkap</label>
                            <input type="text" name="contact_name" id="contact_name" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="contact_email" class="form-label text-white">Email</label>
                            <input type="email" name="contact_email" id="contact_email" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="contact_subject" class="form-label text-white">Subjek</label>
                            <input type="text" name="contact_subject" id="contact_subject" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="contact_message" class="form-label text-white">Pesan</label>
                            <textarea name="contact_message" id="contact_message" class="form-control" rows="5" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary-custom w-100">Kirim Pesan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
// Additional custom scripts for this page
document.addEventListener('DOMContentLoaded', function() {
    // Initialize any page-specific functionality
    console.log('SMP Bina Informatika - Homepage loaded');
});
</script>

<?php include 'includes/footer.php'; ?>
