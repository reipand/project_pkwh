# SMP Bina Informatika - Sistem Penerimaan Siswa Baru

Sistem penerimaan siswa baru yang modern dan terstruktur untuk SMP Bina Informatika dengan arsitektur MVC yang bersih dan fitur-fitur yang lengkap.

## 🚀 Fitur Utama

### ✨ Frontend
- **Design Responsif**: Tampilan yang optimal di desktop, tablet, dan mobile
- **Animasi Modern**: Transisi dan animasi yang smooth menggunakan CSS3
- **Form Validasi Real-time**: Validasi input yang interaktif
- **Gallery Interaktif**: Galeri kegiatan sekolah dengan efek hover
- **Smooth Scrolling**: Navigasi yang halus antar section

### 🔧 Backend
- **Arsitektur MVC**: Struktur kode yang terorganisir dan mudah dimaintain
- **Database PDO**: Koneksi database yang aman dengan prepared statements
- **API RESTful**: Endpoint API yang terstruktur untuk registrasi dan kontak
- **Validasi Input**: Validasi server-side yang ketat
- **Error Handling**: Penanganan error yang komprehensif
- **Logging System**: Sistem log untuk monitoring aktivitas

### 📊 Database
- **Struktur Normalized**: Database yang teroptimasi dengan relasi yang baik
- **Views & Stored Procedures**: Untuk query yang kompleks dan statistik
- **Triggers**: Otomatisasi update timestamp
- **Indexing**: Optimasi performa query

## 📁 Struktur Proyek

```
SMP_BI/
├── api/                    # API Endpoints
│   ├── register.php       # Registrasi siswa
│   └── contact.php        # Form kontak
├── assets/                # Static Assets
│   ├── css/              # Stylesheets
│   │   ├── style.css     # CSS utama
│   │   └── responsive.css # CSS responsif
│   ├── js/               # JavaScript
│   │   ├── main.js       # JS utama
│   │   └── animations.js # Animasi
│   └── images/           # Gambar
├── config/               # Konfigurasi
│   └── database.php      # Koneksi database
├── controllers/          # Controllers
│   └── StudentController.php
├── models/              # Models
│   └── Student.php
├── includes/            # Reusable Components
│   ├── header.php       # Header template
│   └── footer.php       # Footer template
├── database/            # Database Schema
│   └── schema.sql       # SQL structure
├── index.php            # Halaman utama
└── README.md           # Dokumentasi
```

## 🛠️ Teknologi yang Digunakan

### Frontend
- **HTML5**: Semantic markup
- **CSS3**: Modern styling dengan Flexbox dan Grid
- **JavaScript (ES6+)**: Interaktivitas dan validasi
- **Bootstrap 5**: Framework CSS untuk responsivitas
- **Font Awesome**: Icons
- **SweetAlert2**: Notifikasi yang menarik

### Backend
- **PHP 7.4+**: Server-side scripting
- **MySQL 5.7+**: Database management
- **PDO**: Database abstraction layer
- **Apache/Nginx**: Web server

## 📋 Persyaratan Sistem

### Server Requirements
- PHP 7.4 atau lebih tinggi
- MySQL 5.7 atau lebih tinggi
- Apache/Nginx web server
- Extensions PHP: PDO, PDO_MySQL, mbstring, json

### Browser Support
- Chrome 80+
- Firefox 75+
- Safari 13+
- Edge 80+

## 🚀 Instalasi

### 1. Clone Repository
```bash
git clone https://github.com/your-username/smp-bina-informatika.git
cd smp-bina-informatika
```

### 2. Setup Database
```bash
# Import database schema
mysql -u root -p < database/schema.sql
```

### 3. Konfigurasi Database
Edit file `config/database.php`:
```php
private $host = 'localhost';
private $db_name = 'smp_bina_informatika';
private $username = 'your_username';
private $password = 'your_password';
```

### 4. Setup Web Server
#### Apache (.htaccess)
```apache
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php [QSA,L]
```

#### Nginx
```nginx
location / {
    try_files $uri $uri/ /index.php?$query_string;
}
```

### 5. Set Permissions
```bash
chmod 755 assets/
chmod 644 assets/css/*
chmod 644 assets/js/*
```

## 📖 Penggunaan

### 1. Halaman Utama
- **Hero Section**: Informasi utama penerimaan siswa baru
- **Gallery Preview**: Preview kegiatan sekolah
- **Procedure Section**: 5 langkah tata cara pendaftaran
- **Registration Form**: Form pendaftaran lengkap
- **Information Section**: Informasi detail sekolah
- **Contact Section**: Form kontak dan informasi

### 2. Form Registrasi
- Validasi real-time untuk semua field
- Generate username otomatis
- Validasi NIK 16 digit
- Validasi format nomor telepon Indonesia
- Validasi tanggal lahir dan usia
- Captcha verification

### 3. API Endpoints

#### POST /api/register.php
Registrasi siswa baru
```json
{
  "registration_type": "reguler",
  "academic_year": "2025-2026",
  "batch": "1",
  "class": "VII",
  "name": "Nama Siswa",
  "birth_place": "Jakarta",
  "birth_date": "2010-01-01",
  "parent_phone": "081234567890",
  "nik": "1234567890123456",
  "password": "password123",
  "origin_school": "SD Contoh",
  "captcha": "ABC123"
}
```

#### POST /api/contact.php
Kirim pesan kontak
```json
{
  "contact_name": "Nama Pengirim",
  "contact_email": "email@example.com",
  "contact_subject": "Pertanyaan",
  "contact_message": "Isi pesan..."
}
```

## 🔧 Konfigurasi

### Pengaturan Sistem
Semua pengaturan dapat diubah melalui tabel `settings` di database:

```sql
-- Contoh update pengaturan
UPDATE settings SET setting_value = '2026-2027' WHERE setting_key = 'current_academic_year';
UPDATE settings SET setting_value = '0' WHERE setting_key = 'registration_open';
```

### Customization
1. **Logo**: Ganti `assets/images/logo.png`
2. **Warna**: Edit variabel CSS di `assets/css/style.css`
3. **Konten**: Update teks di `index.php`
4. **Email**: Konfigurasi SMTP di `api/contact.php`

## 📊 Database Schema

### Tabel Utama
- **students**: Data siswa yang mendaftar
- **contact_messages**: Pesan dari form kontak
- **users**: User admin/staff
- **settings**: Pengaturan sistem
- **activity_logs**: Log aktivitas
- **file_uploads**: File yang diupload

### Views
- **registration_stats**: Statistik pendaftaran
- **class_stats**: Statistik per kelas
- **batch_stats**: Statistik per gelombang

## 🔒 Keamanan

### Implemented Security Features
- **SQL Injection Protection**: Menggunakan PDO prepared statements
- **XSS Protection**: Input sanitization dengan `htmlspecialchars()`
- **CSRF Protection**: Token validation untuk form submission
- **Password Hashing**: Menggunakan `password_hash()` dengan bcrypt
- **Input Validation**: Validasi ketat untuk semua input
- **File Upload Security**: Validasi tipe dan ukuran file

### Best Practices
- Semua input divalidasi di server-side
- Password di-hash menggunakan bcrypt
- Error messages tidak mengekspos informasi sensitif
- Logging untuk monitoring aktivitas mencurigakan

## 🧪 Testing

### Manual Testing Checklist
- [ ] Form registrasi berfungsi dengan semua field
- [ ] Validasi client-side dan server-side
- [ ] Responsivitas di berbagai ukuran layar
- [ ] Animasi dan transisi berjalan smooth
- [ ] API endpoints mengembalikan response yang benar
- [ ] Email notification terkirim
- [ ] Database transactions berhasil

### Automated Testing (Future)
```bash
# Unit tests (akan diimplementasikan)
php vendor/bin/phpunit tests/
```

## 📈 Performance

### Optimizations
- **CSS/JS Minification**: File assets di-minify
- **Image Optimization**: Gambar di-compress
- **Database Indexing**: Index pada kolom yang sering di-query
- **Caching**: Browser caching untuk static assets
- **Lazy Loading**: Images load saat dibutuhkan

### Monitoring
- Error logging di `error_log`
- Activity logging di database
- Performance metrics tracking

## 🤝 Contributing

1. Fork repository
2. Buat feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit changes (`git commit -m 'Add some AmazingFeature'`)
4. Push ke branch (`git push origin feature/AmazingFeature`)
5. Buat Pull Request

## 📝 Changelog

### v1.0.0 (2025-01-XX)
- ✅ Initial release
- ✅ Complete registration system
- ✅ Responsive design
- ✅ API endpoints
- ✅ Database schema
- ✅ Security features

## 📄 License

Proyek ini dilisensikan di bawah MIT License - lihat file [LICENSE](LICENSE) untuk detail.

## 📞 Support

Untuk dukungan teknis atau pertanyaan:
- Email: info.sdpjointaro@spj.sch.id
- Phone: 083896226790 (Rizam Nuruzaman)
- Address: Jl. Tegal Rotan Raya No.8 A, Sawah Baru, Kec. Ciputat, Kota Tangerang Selatan, Banten 15412

## 🙏 Acknowledgments

- Bootstrap untuk framework CSS
- Font Awesome untuk icons
- SweetAlert2 untuk notifikasi
- Poppins font dari Google Fonts
- Semua kontributor dan tester

---

**Dibuat dengan ❤️ untuk SMP Bina Informatika** 