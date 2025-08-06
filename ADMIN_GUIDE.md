# Panduan Admin - Sistem Penerimaan Siswa Baru SMP Bina Informatika

## Informasi Login Admin

### Akun Default Admin
- **Username**: `admin`
- **Password**: `password`
- **Email**: `admin@smpbinainformatika.sch.id`

### Cara Login Admin
1. Buka halaman login: `http://localhost/SMP_BI/login.php`
2. Pilih tab "Admin"
3. Masukkan username dan password admin
4. Klik "Login"

## Fitur Admin Dashboard

### 1. Statistik Pendaftaran
Dashboard menampilkan statistik real-time:
- Total siswa yang mendaftar
- Jumlah siswa menunggu verifikasi
- Jumlah siswa diterima
- Jumlah siswa ditolak
- Jumlah siswa yang sudah bayar

### 2. Daftar Pendaftaran Terbaru
- Menampilkan 10 pendaftaran terbaru
- Informasi: Nama, Username, Kelas, Email Orang Tua, Status, Pembayaran, Tanggal Daftar
- Fitur pencarian berdasarkan nama
- Filter berdasarkan status (Menunggu/Diterima/Ditolak)

### 3. Verifikasi Siswa
Admin dapat melakukan verifikasi siswa dengan status:
- **Terima**: Mengubah status dari "Menunggu" menjadi "Diterima"
- **Tolak**: Mengubah status dari "Menunggu" menjadi "Ditolak"
- **Detail**: Melihat informasi lengkap siswa

## Cara Menambahkan Admin Baru

### Menggunakan Script Otomatis
1. Akses file: `http://localhost/SMP_BI/create_admin.php`
2. Isi form dengan data admin baru:
   - Username (unik)
   - Password
   - Email (unik)
   - Nama Lengkap
   - Role (Admin/Staff/Teacher)
3. Klik "Tambah Admin"
4. **PENTING**: Hapus file `create_admin.php` setelah selesai

### Menggunakan Database Langsung
```sql
INSERT INTO users (username, password, email, full_name, role, is_active) 
VALUES ('admin2', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin2@smpbinainformatika.sch.id', 'Admin Kedua', 'admin', 1);
```
*Note: Password di atas adalah hash untuk kata "password"*

## Struktur File Admin

```
admin/
├── dashboard.php          # Dashboard utama admin
├── view_student.php       # Halaman detail siswa
└── logout.php            # Halaman logout admin

api/
└── verify_student.php     # API untuk verifikasi siswa
```

## Status Siswa

### Status Pendaftaran
- **pending**: Menunggu verifikasi admin
- **accepted**: Diterima oleh admin
- **rejected**: Ditolak oleh admin

### Status Pembayaran
- **unpaid**: Belum melakukan pembayaran
- **paid**: Sudah melakukan pembayaran
- **verified**: Pembayaran sudah diverifikasi

## Log Aktivitas

Sistem mencatat semua aktivitas admin:
- Login admin
- Verifikasi siswa (terima/tolak)
- Logout admin

Log tersimpan di tabel `activity_logs` dengan informasi:
- User ID admin
- Jenis aksi
- Deskripsi aksi
- IP Address
- User Agent
- Timestamp

## Keamanan

### Fitur Keamanan yang Diterapkan
1. **Session Management**: Validasi session untuk setiap halaman admin
2. **Password Hashing**: Password di-hash menggunakan `password_hash()`
3. **SQL Injection Protection**: Menggunakan prepared statements
4. **XSS Protection**: Output di-escape menggunakan `htmlspecialchars()`
5. **CSRF Protection**: Validasi token untuk form submission

### Rekomendasi Keamanan
1. Ganti password default admin setelah login pertama
2. Hapus file `create_admin.php` setelah menambah admin
3. Gunakan HTTPS di production
4. Batasi akses IP jika diperlukan
5. Backup database secara berkala

## Troubleshooting

### Masalah Umum

#### 1. Tidak bisa login admin
- Pastikan username dan password benar
- Cek apakah akun admin aktif (`is_active = 1`)
- Pastikan database terhubung dengan benar

#### 2. Gambar tidak muncul
- Pastikan path gambar benar: `assets/image/` (bukan `assets/images/`)
- Cek apakah file gambar ada di folder yang benar
- Pastikan permission file dan folder sudah benar

#### 3. Error database
- Cek konfigurasi database di `config/database.php`
- Pastikan tabel `users` dan `students` sudah dibuat
- Cek log error PHP untuk detail masalah

#### 4. Verifikasi siswa gagal
- Pastikan siswa masih berstatus "pending"
- Cek apakah ada error di log sistem
- Pastikan API endpoint `verify_student.php` dapat diakses

## Kontak Support

Jika mengalami masalah teknis, hubungi:
- Email: info@smpbinainformatika.sch.id
- Telepon: 083896226790 (Rizam Nuruzaman)

---

**Catatan**: Sistem ini dirancang khusus untuk verifikasi registrasi siswa SMP Bina Informatika. Pastikan untuk selalu backup data sebelum melakukan perubahan besar. 