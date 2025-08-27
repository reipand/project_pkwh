
-- Membuat tabel kandidat
CREATE TABLE kandidat (
  id INT(11) NOT NULL AUTO_INCREMENT,
  nama_lengkap VARCHAR(255) NOT NULL,
  nis VARCHAR(50) NOT NULL,
  visi_misi TEXT NOT NULL,
  video_path VARCHAR(255) DEFAULT NULL, 
  foto_path VARCHAR(255) NOT NULL,
  jumlah_suara INT(11) DEFAULT 0,
  PRIMARY KEY (id)
)


CREATE TABLE IF NOT EXISTS `kandidat` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `nama_lengkap` VARCHAR(255) NOT NULL,
  `nis` VARCHAR(50) NOT NULL UNIQUE,
  `visi` TEXT NOT NULL,
  `misi` TEXT NOT NULL,
  `video_path` VARCHAR(255) DEFAULT NULL, 
  `foto_path` VARCHAR(255) NOT NULL,
  `kejar` VARCHAR(255) NOT NULL,
  `usia` INT(11) DEFAULT NULL,
  `jumlah_suara` INT(11) DEFAULT 0,
  PRIMARY KEY (`id`)
);
-- Membuat tabel pemilih
CREATE TABLE pemilih (
  id INT(11) NOT NULL AUTO_INCREMENT,
  nis VARCHAR(50) NOT NULL,
  nama_lengkap VARCHAR(255) NOT NULL,
  tanggal_lahir VARCHAR(8) NOT NULL COMMENT 'Format: DD/MM/YY',
  status_memilih TINYINT(1) DEFAULT 0 COMMENT '0=Belum, 1=Sudah',
  PRIMARY KEY (id),
  UNIQUE KEY nis (nis)
);

ALTER TABLE `pemilih` ADD FOREIGN KEY (`id_kandidat_dipilih`) REFERENCES `kandidat`(`id`);

ALTER TABLE `pemilih` ADD `id_kandidat_dipilih` INT(11) NULL DEFAULT NULL AFTER `status_memilih`;
-- Membuat tabel admin
CREATE TABLE admin (
  id INT(11) NOT NULL AUTO_INCREMENT,
  username VARCHAR(100) NOT NULL,
  password VARCHAR(255) NOT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY username (username)
) 

-- === CONTOH DATA AWAL ===

-- Menambahkan contoh data admin
-- Passwordnya adalah 'admin123', ini adalah hash-nya
INSERT INTO admin (username, password) VALUES
('admin', '$2y$10$8glLM.iNLCebL/AOqREoW.Jnwf3BmHW9sIzGabg7SW8LBFPGPIoaa');

-- Menambahkan contoh data pemilih
INSERT INTO pemilih (nis, nama_lengkap, tanggal_lahir) VALUES
('1001', 'Budi Santoso', '15/08/06'),
('1002', 'Citra Lestari', '22/01/07'),
('1003', 'Dewi Anggraini', '30/11/06');

-- Menambahkan contoh data kandidat
INSERT INTO `kandidat` (`id`, `nama_lengkap`, `nis`, `visi`, `misi`, `video_path`, `foto_path`, `kejar`, `usia`, `jumlah_suara`) VALUES
(1, 'Qanita Malila Oufwa Pahingguan', '2001', 'Mewujudkan sekolah yang berbasis teknologi, berintegritas, dan transparan.', 'Membangun sistem voting online yang transparan dan aman.; Mengadakan workshop coding dan pengembangan aplikasi untuk siswa.; Meningkatkan literasi digital di lingkungan sekolah.', '../assets/videos/compressed_video_1.mp4', '../assets/image/qani.png', 'DKV', 17, 0),
(2, 'Aura Anastasya Putri Fiara', '2002', 'Menciptakan lingkungan sekolah yang kreatif dan inovatif.', 'Mengadakan workshop seni dan event untuk mengembangkan bakat siswa.; Membentuk komunitas kreatif di setiap kelas.; Menjalin kolaborasi dengan pihak luar untuk event sekolah.', '../assets/videos/compressed_video_2.mp4', '../assets/image/aura.png', 'RPL', 18, 0),
(3, 'Safdiza Azizi', '2003', 'Meningkatkan kesadaran lingkungan di sekolah.', 'Mengadakan program daur ulang dan penanaman pohon rutin.; Mengampanyekan penggunaan tumbler dan mengurangi sampah plastik.; Membangun taman sekolah yang produktif.', '../assets/videos/compressed_video_3.mp4', '../assets/image/diza.png', 'RPL', 17, 0),
(4, 'Ananda Dio Pratama Harahap', '2004', 'Menjadikan OSIS sebagai wadah aspirasi siswa.', 'Membentuk forum diskusi bulanan untuk semua siswa.; Menyediakan kotak saran online yang dapat diakses setiap saat.; Mengadakan pertemuan rutin antara perwakilan kelas dengan pihak sekolah.', '../assets/videos/compressed_video_4.mp4', '../assets/image/dio.png', 'RPL', 17, 0);

CREATE TABLE `kepuasan_debat` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `id_pemilih` INT(11) NOT NULL,
    `id_kandidat` INT(11) NOT NULL,
    `nilai_kepuasan` INT(1) NOT NULL COMMENT 'Skala 1-5',
    `sesi_ke` INT(1) NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `unique_vote_per_session` (`id_pemilih`, `sesi_ke`),
    FOREIGN KEY (`id_pemilih`) REFERENCES `pemilih`(`id`),
    FOREIGN KEY (`id_kandidat`) REFERENCES `kandidat`(`id`)
);