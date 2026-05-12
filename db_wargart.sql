-- =====================================================
-- SI-WargaRT Database Schema
-- Sistem Informasi Warga RT
-- =====================================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+07:00";

-- =====================================================
-- Database Creation
-- =====================================================
CREATE DATABASE IF NOT EXISTS `db_wargart` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `db_wargart`;

-- =====================================================
-- Table: users
-- =====================================================
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `username` VARCHAR(50) NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `nama` VARCHAR(100) NOT NULL,
  `email` VARCHAR(100) DEFAULT NULL,
  `no_hp` VARCHAR(20) DEFAULT NULL,
  `role` ENUM('admin', 'ketua_rt', 'warga') NOT NULL DEFAULT 'warga',
  `id_warga` VARCHAR(20) DEFAULT NULL,
  `foto` VARCHAR(255) DEFAULT 'default.png',
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `last_login` DATETIME DEFAULT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  KEY `role` (`role`),
  KEY `id_warga` (`id_warga`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Table: warga
-- =====================================================
DROP TABLE IF EXISTS `warga`;
CREATE TABLE `warga` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `nik` VARCHAR(20) NOT NULL,
  `no_kk` VARCHAR(20) NOT NULL,
  `nama_lengkap` VARCHAR(100) NOT NULL,
  `tempat_lahir` VARCHAR(50) DEFAULT NULL,
  `tanggal_lahir` DATE DEFAULT NULL,
  `jenis_kelamin` ENUM('L', 'P') NOT NULL,
  `agama` VARCHAR(20) DEFAULT NULL,
  `status_perkawinan` ENUM('Belum Kawin', 'Kawin', 'Cerai Hidup', 'Cerai Mati') DEFAULT 'Belum Kawin',
  `pekerjaan` VARCHAR(50) DEFAULT NULL,
  `no_hp` VARCHAR(20) DEFAULT NULL,
  `email` VARCHAR(100) DEFAULT NULL,
  `alamat` TEXT DEFAULT NULL,
  `rt` VARCHAR(5) DEFAULT '01',
  `rw` VARCHAR(5) DEFAULT '01',
  `foto` VARCHAR(255) DEFAULT 'default.png',
  `foto_rumah` VARCHAR(255) DEFAULT NULL,
  `status_warga` ENUM('Aktif', 'Non-Aktif', 'Mutasi', 'Meninggal') DEFAULT 'Aktif',
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nik` (`nik`),
  KEY `status_warga` (`status_warga`),
  KEY `no_kk` (`no_kk`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Table: Kartu Keluarga
-- =====================================================
DROP TABLE IF EXISTS `kartu_keluarga`;
CREATE TABLE `kartu_keluarga` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `no_kk` VARCHAR(20) NOT NULL,
  `nik_kepala` VARCHAR(20) DEFAULT NULL,
  `nama_kepala` VARCHAR(100) NOT NULL,
  `alamat` TEXT DEFAULT NULL,
  `rt` VARCHAR(5) DEFAULT '01',
  `rw` VARCHAR(5) DEFAULT '01',
  `kode_pos` VARCHAR(10) DEFAULT NULL,
  `foto_kk` VARCHAR(255) DEFAULT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `no_kk` (`no_kk`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Table: Jenis Iuran
-- =====================================================
DROP TABLE IF EXISTS `jenis_iuran`;
CREATE TABLE `jenis_iuran` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `nama_iuran` VARCHAR(50) NOT NULL,
  `jenis` ENUM('Kebersihan', 'Keamanan', 'Kas RT', 'Dana Sosial') NOT NULL,
  `jumlah` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  `deskripsi` TEXT DEFAULT NULL,
  `is_active` TINYINT(1) DEFAULT 1,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Table: Tagihan Iuran
-- =====================================================
DROP TABLE IF EXISTS `tagihan_iuran`;
CREATE TABLE `tagihan_iuran` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `id_warga` VARCHAR(20) NOT NULL,
  `id_jenis_iuran` INT(11) NOT NULL,
  `bulan` VARCHAR(7) NOT NULL,
  `jumlah` DECIMAL(12,2) NOT NULL,
  `status` ENUM('Pending', 'Lunas', 'Menunggak') DEFAULT 'Pending',
  `tanggal_jatuh_tempo` DATE DEFAULT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `id_warga` (`id_warga`),
  KEY `id_jenis_iuran` (`id_jenis_iuran`),
  KEY `bulan` (`bulan`),
  KEY `status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Table: Pembayaran
-- =====================================================
DROP TABLE IF EXISTS `pembayaran`;
CREATE TABLE `pembayaran` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `id_tagihan` INT(11) NOT NULL,
  `id_warga` VARCHAR(20) NOT NULL,
  `jumlah_bayar` DECIMAL(12,2) NOT NULL,
  `tanggal_bayar` DATE NOT NULL,
  `metode_bayar` ENUM('Cash', 'Transfer', 'E-Wallet') DEFAULT 'Transfer',
  `bukti_transfer` VARCHAR(255) DEFAULT NULL,
  `status` ENUM('Pending', 'Verified', 'Rejected') DEFAULT 'Pending',
  `keterangan` TEXT DEFAULT NULL,
  `verified_by` INT(11) DEFAULT NULL,
  `verified_at` DATETIME DEFAULT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `id_tagihan` (`id_tagihan`),
  KEY `id_warga` (`id_warga`),
  KEY `status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Table: Kas RT
-- =====================================================
DROP TABLE IF EXISTS `kas_rt`;
CREATE TABLE `kas_rt` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `jenis` ENUM('Masuk', 'Keluar') NOT NULL,
  `kategori` VARCHAR(50) DEFAULT NULL,
  `jumlah` DECIMAL(12,2) NOT NULL,
  `keterangan` TEXT DEFAULT NULL,
  `tanggal` DATE NOT NULL,
  `bukti` VARCHAR(255) DEFAULT NULL,
  `created_by` INT(11) DEFAULT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `jenis` (`jenis`),
  KEY `tanggal` (`tanggal`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Table: Pengumuman
-- =====================================================
DROP TABLE IF EXISTS `pengumuman`;
CREATE TABLE `pengumuman` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `judul` VARCHAR(200) NOT NULL,
  `isi` TEXT NOT NULL,
  `tanggal` DATE NOT NULL,
  `banner` VARCHAR(255) DEFAULT NULL,
  `status` ENUM('Draft', 'Published', 'Archived') DEFAULT 'Published',
  `created_by` INT(11) DEFAULT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `status` (`status`),
  KEY `tanggal` (`tanggal`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Table: Kegiatan
-- =====================================================
DROP TABLE IF EXISTS `kegiatan`;
CREATE TABLE `kegiatan` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `judul` VARCHAR(200) NOT NULL,
  `isi` TEXT DEFAULT NULL,
  `tanggal` DATE NOT NULL,
  `waktu` TIME DEFAULT NULL,
  `lokasi` VARCHAR(200) DEFAULT NULL,
  `banner` VARCHAR(255) DEFAULT NULL,
  `status` ENUM('Akan Datang', 'Sedang Berlangsung', 'Selesai', 'Dibatalkan') DEFAULT 'Akan Datang',
  `created_by` INT(11) DEFAULT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `tanggal` (`tanggal`),
  KEY `status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Table: Surat Pengantar
-- =====================================================
DROP TABLE IF EXISTS `surat_pengantar`;
CREATE TABLE `surat_pengantar` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `id_warga` VARCHAR(20) NOT NULL,
  `jenis_surat` ENUM('Surat Domisili', 'Surat Keterangan', 'Surat Usaha', 'Surat Pengantar Nikah') NOT NULL,
  `keperluan` TEXT NOT NULL,
  `tanggal_pengajuan` DATE NOT NULL,
  `status` ENUM('Draft', 'Pending', 'Rejected', 'Selesai') DEFAULT 'Draft',
  `keterangan` TEXT DEFAULT NULL,
  `no_surat` VARCHAR(50) DEFAULT NULL,
  `qrcode` VARCHAR(255) DEFAULT NULL,
  `file_surat` VARCHAR(255) DEFAULT NULL,
  `approved_by_ketua` INT(11) DEFAULT NULL,
  `approved_at_ketua` DATETIME DEFAULT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `id_warga` (`id_warga`),
  KEY `jenis_surat` (`jenis_surat`),
  KEY `status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Table: Pengaduan
-- =====================================================
DROP TABLE IF EXISTS `pengaduan`;
CREATE TABLE `pengaduan` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `id_warga` VARCHAR(20) NOT NULL,
  `judul` VARCHAR(200) NOT NULL,
  `isi` TEXT NOT NULL,
  `kategori` ENUM('Kebersihan', 'Keamanan', 'Fasilitas', 'Lingkungan', 'Lainnya') DEFAULT 'Lainnya',
  `lampiran` VARCHAR(255) DEFAULT NULL,
  `status` ENUM('Dikirim', 'Diproses', 'Selesai', 'Ditolak') DEFAULT 'Dikirim',
  `tanggal_pengaduan` DATE NOT NULL,
  `tanggal_selesai` DATE DEFAULT NULL,
  `respon` TEXT DEFAULT NULL,
  `respon_by` INT(11) DEFAULT NULL,
  `respon_at` DATETIME DEFAULT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `id_warga` (`id_warga`),
  KEY `kategori` (`kategori`),
  KEY `status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Table: Notifikasi
-- =====================================================
DROP TABLE IF EXISTS `notifikasi`;
CREATE TABLE `notifikasi` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `id_user` INT(11) NOT NULL,
  `judul` VARCHAR(200) NOT NULL,
  `isi` TEXT DEFAULT NULL,
  `jenis` ENUM('Tagihan', 'Pengumuman', 'Surat', 'Pengaduan', 'System') DEFAULT 'System',
  `link` VARCHAR(255) DEFAULT NULL,
  `is_read` TINYINT(1) DEFAULT 0,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `id_user` (`id_user`),
  KEY `is_read` (`is_read`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Table: Pengaturan
-- =====================================================
DROP TABLE IF EXISTS `pengaturan`;
CREATE TABLE `pengaturan` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `nama_aplikasi` VARCHAR(100) DEFAULT 'SI-WargaRT',
  `alamat` TEXT DEFAULT NULL,
  `no_telepon` VARCHAR(20) DEFAULT NULL,
  `email` VARCHAR(100) DEFAULT NULL,
  `logo` VARCHAR(255) DEFAULT 'logo.png',
  `nama_rt` VARCHAR(50) DEFAULT 'RT 01',
  `nama_rw` VARCHAR(50) DEFAULT 'RW 01',
  `nama_ketua_rt` VARCHAR(100) DEFAULT NULL,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Table: Forum Topik
-- =====================================================
DROP TABLE IF EXISTS `forum_topik`;
CREATE TABLE `forum_topik` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `id_user` INT(11) NOT NULL,
  `judul` VARCHAR(200) NOT NULL,
  `isi` TEXT NOT NULL,
  `views` INT(11) DEFAULT 0,
  `jumlah_komentar` INT(11) DEFAULT 0,
  `status` ENUM('Published', 'Archived', 'Deleted') DEFAULT 'Published',
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `id_user` (`id_user`),
  KEY `created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Table: Forum Balasan
-- =====================================================
DROP TABLE IF EXISTS `forum_komentar`;
CREATE TABLE `forum_komentar` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `id_topik` INT(11) NOT NULL,
  `id_user` INT(11) NOT NULL,
  `isi` TEXT NOT NULL,
  `status` ENUM('Published', 'Deleted') DEFAULT 'Published',
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `id_topik` (`id_topik`),
  KEY `id_user` (`id_user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Table: Activity Log
-- =====================================================
DROP TABLE IF EXISTS `activity_log`;
CREATE TABLE `activity_log` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `id_user` INT(11) DEFAULT NULL,
  `activity` VARCHAR(255) NOT NULL,
  `modul` VARCHAR(50) DEFAULT NULL,
  `ip_address` VARCHAR(45) DEFAULT NULL,
  `user_agent` VARCHAR(255) DEFAULT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `id_user` (`id_user`),
  KEY `created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Foreign Keys
-- =====================================================
ALTER TABLE `users` ADD FOREIGN KEY (`id_warga`) REFERENCES `warga`(`nik`) ON DELETE SET NULL ON UPDATE CASCADE;
ALTER TABLE `tagihan_iuran` ADD FOREIGN KEY (`id_warga`) REFERENCES `warga`(`nik`) ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE `tagihan_iuran` ADD FOREIGN KEY (`id_jenis_iuran`) REFERENCES `jenis_iuran`(`id`) ON DELETE CASCADE;
ALTER TABLE `pembayaran` ADD FOREIGN KEY (`id_tagihan`) REFERENCES `tagihan_iuran`(`id`) ON DELETE CASCADE;
ALTER TABLE `pembayaran` ADD FOREIGN KEY (`id_warga`) REFERENCES `warga`(`nik`) ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE `pembayaran` ADD FOREIGN KEY (`verified_by`) REFERENCES `users`(`id`) ON DELETE SET NULL;
ALTER TABLE `kas_rt` ADD FOREIGN KEY (`created_by`) REFERENCES `users`(`id`) ON DELETE SET NULL;
ALTER TABLE `pengumuman` ADD FOREIGN KEY (`created_by`) REFERENCES `users`(`id`) ON DELETE SET NULL;
ALTER TABLE `kegiatan` ADD FOREIGN KEY (`created_by`) REFERENCES `users`(`id`) ON DELETE SET NULL;
ALTER TABLE `surat_pengantar` ADD FOREIGN KEY (`id_warga`) REFERENCES `warga`(`nik`) ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE `surat_pengantar` ADD FOREIGN KEY (`approved_by_ketua`) REFERENCES `users`(`id`) ON DELETE SET NULL;
ALTER TABLE `pengaduan` ADD FOREIGN KEY (`id_warga`) REFERENCES `warga`(`nik`) ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE `pengaduan` ADD FOREIGN KEY (`respon_by`) REFERENCES `users`(`id`) ON DELETE SET NULL;
ALTER TABLE `notifikasi` ADD FOREIGN KEY (`id_user`) REFERENCES `users`(`id`) ON DELETE CASCADE;
ALTER TABLE `activity_log` ADD FOREIGN KEY (`id_user`) REFERENCES `users`(`id`) ON DELETE SET NULL;
ALTER TABLE `forum_topik` ADD FOREIGN KEY (`id_user`) REFERENCES `users`(`id`) ON DELETE CASCADE;
ALTER TABLE `forum_komentar` ADD FOREIGN KEY (`id_topik`) REFERENCES `forum_topik`(`id`) ON DELETE CASCADE;
ALTER TABLE `forum_komentar` ADD FOREIGN KEY (`id_user`) REFERENCES `users`(`id`) ON DELETE CASCADE;

-- =====================================================
-- SEEDER DATA
-- =====================================================

-- Insert Pengaturan
INSERT INTO `pengaturan` (`nama_aplikasi`, `alamat`, `no_telepon`, `email`, `nama_rt`, `nama_rw`, `nama_ketua_rt`) VALUES
('SI-WargaRT', 'Jl. Merdeka No. 1, Kelurahan Sukamaju, Kecamatan Sukasari', '021-12345678', 'info@siwargart.com', 'RT 01', 'RW 01', 'Budi Santoso');

-- Insert Users Default (password: admin123, ketua123, warga123)
INSERT INTO `users` (`username`, `password`, `nama`, `email`, `no_hp`, `role`, `is_active`, `id_warga`) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrator', 'admin@siwargart.com', '081234567890', 'admin', 1, NULL),
('ketua', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Budi Santoso', 'ketua@siwargart.com', '081234567891', 'ketua_rt', 1, NULL),
('warga', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Ahmad Wijaya', 'warga@siwargart.com', '081234567892', 'warga', 1, NULL);

-- Update password dengan hash bcrypt yang benar
-- UPDATE `users` SET `password` = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi' WHERE `username` = 'admin';
-- UPDATE `users` SET `password` = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi' WHERE `username` = 'ketua';
-- UPDATE `users` SET `password` = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi' WHERE `username` = 'warga';

-- Insert Jenis Iuran
INSERT INTO `jenis_iuran` (`nama_iuran`, `jenis`, `jumlah`, `deskripsi`, `is_active`) VALUES
('Iuran Kebersihan Bulanan', 'Kebersihan', 25000.00, 'Iuran bulanan untuk kebersihan lingkungan RT', 1),
('Iuran Keamanan Bulanan', 'Keamanan', 30000.00, 'Iuran bulanan untuk keamanan lingkungan RT', 1),
('Kas RT Bulanan', 'Kas RT', 50000.00, 'Iuran bulanan untuk kas RT', 1),
('Dana Sosial', 'Dana Sosial', 10000.00, 'Dana sosial untuk kepentingan bersama', 1);

-- Insert Warga Dummy
INSERT INTO `warga` (`nik`, `no_kk`, `nama_lengkap`, `tempat_lahir`, `tanggal_lahir`, `jenis_kelamin`, `agama`, `status_perkawinan`, `pekerjaan`, `no_hp`, `email`, `alamat`, `rt`, `rw`, `status_warga`) VALUES
('3201234567890001', '3201234567890001', 'Ahmad Wijaya', 'Bandung', '1985-03-15', 'L', 'Islam', 'Kawin', 'Pedagang', '081234567891', 'ahmad@email.com', 'Jl. Merdeka No. 1', '01', '01', 'Aktif'),
('3201234567890002', '3201234567890001', 'Siti Aminah', 'Bandung', '1987-07-22', 'P', 'Islam', 'Kawin', 'Ibu Rumah Tangga', '081234567892', 'siti@email.com', 'Jl. Merdeka No. 1', '01', '01', 'Aktif'),
('3201234567890003', '3201234567890002', 'Budi Santoso', 'Jakarta', '1980-01-10', 'L', 'Islam', 'Kawin', 'Karyawan Swasta', '081234567893', 'budi@email.com', 'Jl. Merdeka No. 2', '01', '01', 'Aktif'),
('3201234567890004', '3201234567890002', 'Dewi Lestari', 'Bandung', '1982-05-18', 'P', 'Islam', 'Kawin', 'Guru', '081234567894', 'dewi@email.com', 'Jl. Merdeka No. 2', '01', '01', 'Aktif'),
('3201234567890005', '3201234567890003', 'Eko Prasetyo', 'Surabaya', '1990-09-25', 'L', 'Kristen', 'Belum Kawin', 'Wiraswasta', '081234567895', 'eko@email.com', 'Jl. Merdeka No. 3', '01', '01', 'Aktif'),
('3201234567890006', '3201234567890003', 'Fitri Handayani', 'Yogyakarta', '1992-11-30', 'P', 'Islam', 'Kawin', 'Perawat', '081234567896', 'fitri@email.com', 'Jl. Merdeka No. 3', '01', '01', 'Aktif'),
('3201234567890007', '3201234567890004', 'Gunawan Hidayat', 'Semarang', '1988-02-14', 'L', 'Islam', 'Kawin', 'PNS', '081234567897', 'gunawan@email.com', 'Jl. Merdeka No. 4', '01', '01', 'Aktif'),
('3201234567890008', '3201234567890004', 'Hesti Rahayu', 'Bandung', '1991-08-05', 'P', 'Hindu', 'Belum Kawin', 'Dosen', '081234567898', 'hesti@email.com', 'Jl. Merdeka No. 4', '01', '01', 'Aktif'),
('3201234567890009', '3201234567890005', 'Irfan Kurniawan', 'Medan', '1995-12-20', 'L', 'Islam', 'Belum Kawin', 'Mahasiswa', '081234567899', 'irfan@email.com', 'Jl. Merdeka No. 5', '01', '01', 'Aktif'),
('3201234567890010', '3201234567890005', 'Jasmine Putri', 'Jakarta', '1997-04-08', 'P', 'Islam', 'Belum Kawin', 'Pelajar', '081234567800', 'jasmine@email.com', 'Jl. Merdeka No. 5', '01', '01', 'Aktif');

-- Insert Kartu Keluarga
INSERT INTO `kartu_keluarga` (`no_kk`, `nik_kepala`, `nama_kepala`, `alamat`, `rt`, `rw`) VALUES
('3201234567890001', '3201234567890001', 'Ahmad Wijaya', 'Jl. Merdeka No. 1', '01', '01'),
('3201234567890002', '3201234567890003', 'Budi Santoso', 'Jl. Merdeka No. 2', '01', '01'),
('3201234567890003', '3201234567890005', 'Eko Prasetyo', 'Jl. Merdeka No. 3', '01', '01'),
('3201234567890004', '3201234567890007', 'Gunawan Hidayat', 'Jl. Merdeka No. 4', '01', '01'),
('3201234567890005', '3201234567890009', 'Irfan Kurniawan', 'Jl. Merdeka No. 5', '01', '01');

-- Insert Tagihan Iuran
INSERT INTO `tagihan_iuran` (`id_warga`, `id_jenis_iuran`, `bulan`, `jumlah`, `status`, `tanggal_jatuh_tempo`) VALUES
('3201234567890001', 1, '2026-01', 25000.00, 'Lunas', '2026-01-31'),
('3201234567890001', 2, '2026-01', 30000.00, 'Lunas', '2026-01-31'),
('3201234567890001', 3, '2026-01', 50000.00, 'Lunas', '2026-01-31'),
('3201234567890001', 4, '2026-01', 10000.00, 'Lunas', '2026-01-31'),
('3201234567890001', 1, '2026-02', 25000.00, 'Lunas', '2026-02-28'),
('3201234567890001', 2, '2026-02', 30000.00, 'Lunas', '2026-02-28'),
('3201234567890001', 3, '2026-02', 50000.00, 'Lunas', '2026-02-28'),
('3201234567890001', 4, '2026-02', 10000.00, 'Lunas', '2026-02-28'),
('3201234567890001', 1, '2026-03', 25000.00, 'Lunas', '2026-03-31'),
('3201234567890001', 2, '2026-03', 30000.00, 'Lunas', '2026-03-31'),
('3201234567890001', 3, '2026-03', 50000.00, 'Lunas', '2026-03-31'),
('3201234567890001', 4, '2026-03', 10000.00, 'Lunas', '2026-03-31'),
('3201234567890002', 1, '2026-01', 25000.00, 'Lunas', '2026-01-31'),
('3201234567890002', 2, '2026-01', 30000.00, 'Lunas', '2026-01-31'),
('3201234567890002', 3, '2026-01', 50000.00, 'Lunas', '2026-01-31'),
('3201234567890002', 4, '2026-01', 10000.00, 'Lunas', '2026-01-31'),
('3201234567890003', 1, '2026-04', 25000.00, 'Menunggak', '2026-04-30'),
('3201234567890003', 2, '2026-04', 30000.00, 'Menunggak', '2026-04-30'),
('3201234567890003', 3, '2026-04', 50000.00, 'Menunggak', '2026-04-30'),
('3201234567890003', 4, '2026-04', 10000.00, 'Menunggak', '2026-04-30'),
('3201234567890004', 1, '2026-04', 25000.00, 'Pending', '2026-04-30'),
('3201234567890004', 2, '2026-04', 30000.00, 'Pending', '2026-04-30'),
('3201234567890004', 3, '2026-04', 50000.00, 'Pending', '2026-04-30'),
('3201234567890004', 4, '2026-04', 10000.00, 'Pending', '2026-04-30');

-- Insert Pembayaran
INSERT INTO `pembayaran` (`id_tagihan`, `id_warga`, `jumlah_bayar`, `tanggal_bayar`, `metode_bayar`, `status`) VALUES
('1', '3201234567890001', 25000.00, '2026-01-05', 'Transfer', 'Verified'),
('2', '3201234567890001', 30000.00, '2026-01-05', 'Transfer', 'Verified'),
('3', '3201234567890001', 50000.00, '2026-01-05', 'Transfer', 'Verified'),
('4', '3201234567890001', 10000.00, '2026-01-05', 'Transfer', 'Verified'),
('5', '3201234567890001', 25000.00, '2026-02-03', 'Transfer', 'Verified'),
('6', '3201234567890001', 30000.00, '2026-02-03', 'Transfer', 'Verified'),
('7', '3201234567890001', 50000.00, '2026-02-03', 'Transfer', 'Verified'),
('8', '3201234567890001', 10000.00, '2026-02-03', 'Transfer', 'Verified'),
('9', '3201234567890001', 25000.00, '2026-03-01', 'Transfer', 'Verified'),
('10', '3201234567890001', 30000.00, '2026-03-01', 'Transfer', 'Verified'),
('11', '3201234567890001', 50000.00, '2026-03-01', 'Transfer', 'Verified'),
('12', '3201234567890001', 10000.00, '2026-03-01', 'Transfer', 'Verified'),
('13', '3201234567890002', 25000.00, '2026-01-10', 'Cash', 'Verified'),
('14', '3201234567890002', 30000.00, '2026-01-10', 'Cash', 'Verified'),
('15', '3201234567890002', 50000.00, '2026-01-10', 'Cash', 'Verified'),
('16', '3201234567890002', 10000.00, '2026-01-10', 'Cash', 'Verified');

-- Insert Kas RT
INSERT INTO `kas_rt` (`jenis`, `kategori`, `jumlah`, `keterangan`, `tanggal`) VALUES
('Masuk', 'Iuran Kebersihan', 50000.00, 'Penerimaan iuran kebersihan bulan Januari 2026', '2026-01-31'),
('Masuk', 'Iuran Keamanan', 60000.00, 'Penerimaan iuran keamanan bulan Januari 2026', '2026-01-31'),
('Masuk', 'Kas RT', 100000.00, 'Penerimaan kas RT bulan Januari 2026', '2026-01-31'),
('Masuk', 'Dana Sosial', 20000.00, 'Penerimaan dana sosial bulan Januari 2026', '2026-01-31'),
('Keluar', 'Kebersihan', 150000.00, 'Gaji petugas kebersihan bulan Januari', '2026-01-31'),
('Masuk', 'Iuran Kebersihan', 50000.00, 'Penerimaan iuran kebersihan bulan Februari 2026', '2026-02-28'),
('Masuk', 'Iuran Keamanan', 60000.00, 'Penerimaan iuran keamanan bulan Februari 2026', '2026-02-28'),
('Masuk', 'Kas RT', 100000.00, 'Penerimaan kas RT bulan Februari 2026', '2026-02-28'),
('Masuk', 'Dana Sosial', 20000.00, 'Penerimaan dana sosial bulan Februari 2026', '2026-02-28'),
('Keluar', 'Perbaikan', 200000.00, 'Perbaikan lampu jalan RT 01', '2026-02-15');

-- Insert Pengumuman
INSERT INTO `pengumuman` (`judul`, `isi`, `tanggal`, `status`) VALUES
('Jadwal Ronda Malam Bulan April 2026', 'Berikut jadwal ronda malam untuk bulan April 2026. Dimohon kepada seluruh warga untuk dapat berpartisipasi sesuai jadwal yang telah ditentukan.', '2026-04-01', 'Published'),
('Gotong Royong Bulanan', 'Akan diadakan kegiatan gotong royong bulanan pada tanggal 20 April 2026 pukul 07:00 WIB. Dimohon kehadiran seluruh warga.', '2026-04-15', 'Published'),
('Penting: Pembayaran Iuran Bulan Mei', 'Pengingat untuk seluruh warga agar dapat menyelesaikan pembayaran iuran bulan Mei paling lambat tanggal 31 Mei 2026.', '2026-05-01', 'Published');

-- Insert Kegiatan
INSERT INTO `kegiatan` (`judul`, `isi`, `tanggal`, `waktu`, `lokasi`, `status`) VALUES
('Rapat Bulanan RT', 'Rapat bulanan membahas keberlangsungan kegiatan RT', '2026-05-15', '19:00:00', 'Balai RT', 'Akan Datang'),
('Bazar Ramadhan', 'Bazar Ramadhan untuk mempersiapkan puasa Ramadhan', '2026-05-25', '08:00:00', 'Halaman RT', 'Akan Datang'),
('Pembersihan Masjid', 'Kegiatan kebersihan masjid bersama', '2026-05-20', '07:00:00', 'Masjid Al-Ikhlas', 'Akan Datang');

-- Insert Surat Pengantar
INSERT INTO `surat_pengantar` (`id_warga`, `jenis_surat`, `keperluan`, `tanggal_pengajuan`, `status`) VALUES
('3201234567890001', 'Surat Domisili', 'Untuk keperluan pembuatan KTP', '2026-04-05', 'Selesai'),
('3201234567890003', 'Surat Keterangan', 'Untuk keperluan melamar pekerjaan', '2026-04-10', 'Pending'),
('3201234567890005', 'Surat Usaha', 'Untuk keperluan membuka usaha warung', '2026-04-12', 'Pending');

-- Insert Pengaduan
INSERT INTO `pengaduan` (`id_warga`, `judul`, `isi`, `kategori`, `status`, `tanggal_pengaduan`, `respon`) VALUES
('3201234567890001', 'Lampu Jalan Mati', 'Lampu jalan di depan rumah nomor 1 sudah mati selama 3 hari', 'Keamanan', 'Selesai', '2026-04-01', 'Terima kasih atas informasinya, lampu akan segera diperbaiki oleh petugas kami.'),
('3201234567890002', 'Sampah Menumpuk', 'Tempat sampah di ujung gang sudah penuh dan berbau', 'Kebersihan', 'Diproses', '2026-04-10', 'Sedang dalam proses penanganan'),
('3201234567890005', 'Banner Rusak', 'Banner Kegiatan RT di depan gang sudah robek', 'Fasilitas', 'Dikirim', '2026-04-15', NULL);

-- Insert Notifikasi
INSERT INTO `notifikasi` (`id_user`, `judul`, `isi`, `jenis`) VALUES
(1, 'Tagihan Baru', 'Anda memiliki tagihan iuran bulan Mei 2026', 'Tagihan'),
(2, 'Pengumuman Baru', 'Jadwal Ronda Malam Bulan April 2026 telah dipublikasikan', 'Pengumuman'),
(3, 'Surat Disetujui', 'Surat Keterangan Anda telah disetujui oleh Ketua RT', 'Surat');



COMMIT;
