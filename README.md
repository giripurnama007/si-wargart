# SI-WargaRT

**Sistem Informasi Warga RT** - Aplikasi administrasi pemerintahan tingkat RT/RW berbasis web.

## Fitur Utama

- **Authentication** - Login multi role (Admin, Ketua RT, Warga)
- **Dashboard** - Statistik dan grafik kas RT
- **Data Warga** - CRUD lengkap dengan fitur import/export
- **Iuran RT** - Generate tagihan, pembayaran, verifikasi
- **Kas RT** - Pencatatan kas masuk dan keluar
- **Pengumuman** - Manajemen pengumuman dan kegiatan
- **Surat Pengantar** - Pengajuan dan approval surat online
- **Pengaduan** - Sistem pengaduan warga
- **Laporan** - Laporan kas, iuran, dan tunggakan

## Teknologi

- PHP Native ( tanpa framework )
- MySQL Database
- AdminLTE v3
- Bootstrap 5
- JQuery + AJAX
- DataTables
- SweetAlert2
- Chart.js
- QRCode Generator

## Instalasi

### 1. Persyaratan
- PHP 7.4+ atau PHP 8.x
- MySQL 5.7+ atau MariaDB 10.3+
- Web Server (Apache/Nginx) atau Laragon/XAMPP

### 2. Langkah Instalasi

1. **Clone/Download** project ke folder web server
   ```
   G:\laragon\www\si-wargart
   ```

2. **Import Database**
   - Buka phpMyAdmin
   - Buat database baru: `db_wargart`
   - Import file `db_wargart.sql`

3. **Konfigurasi Database**
   - Edit file `config/database.php`
   - Sesuaikan username, password, dan nama database

4. **Download Vendor Libraries**
   - Download libraries berikut ke folder `assets/vendor/`:

   ```
   assets/vendor/
   ├── adminlte/
   │   └── css/
   │       └── adminlte.min.css
   │   └── js/
   │       └── adminlte.min.js
   ├── bootstrap/
   │   └── js/
   │       └── bootstrap.bundle.min.js
   ├── fontawesome-free/
   │   └── css/
   │       └── all.min.css
   ├── jquery/
   │   └── jquery.min.js
   ├── datatables/
   │   ├── jquery.dataTables.min.js
   │   ├── dataTables.bootstrap4.min.css
   │   ├── dataTables.bootstrap4.min.js
   │   ├── dataTables.responsive.min.js
   │   ├── responsive.bootstrap4.min.css
   │   └── etc...
   ├── sweetalert2/
   │   └── sweetalert2.all.min.js
   ├── chart.js/
   │   └── Chart.min.js
   ├── qrcodejs/
   │   └── qrcode.min.js
   └── etc...
   ```

   Atau gunakan CDN di file `layouts/main_layout.php`.

5. **Buat Folder Uploads**
   ```
   mkdir uploads/warga
   mkdir uploads/pembayaran
   mkdir uploads/pengaduan
   ```

6. **Selesai!** Buka browser dan akses:
   ```
   http://localhost/si-wargart
   ```

## Default Login

| Role | Username | Password |
|------|----------|----------|
| Admin | admin | password |
| Ketua RT | ketua | password  |
| Warga | warga | password  |

## Struktur Folder

```
si-wargart/
├── assets/
│   ├── css/
│   │   └── style.css
│   ├── js/
│   │   └── app.js
│   ├── img/
│   │   └── logo.png
│   └── vendor/          # Third-party libraries
├── config/
│   └── database.php
├── controllers/
│   ├── AuthController.php
│   ├── DashboardController.php
│   ├── WargaController.php
│   ├── IuranController.php
│   ├── KasController.php
│   ├── PengumumanController.php
│   ├── KegiatanController.php
│   ├── SuratController.php
│   ├── PengaduanController.php
│   ├── LaporanController.php
│   ├── PengaturanController.php
│   ├── NotifikasiController.php
│   └── KKController.php
├── helpers/
│   └── functions.php
├── layouts/
│   └── main_layout.php
├── models/
├── modules/
├── uploads/
│   ├── warga/
│   ├── pembayaran/
│   └── pengaduan/
├── views/
│   ├── auth/
│   ├── dashboard/
│   ├── warga/
│   ├── iuran/
│   ├── kas/
│   ├── pengumuman/
│   ├── kegiatan/
│   ├── surat/
│   ├── pengaduan/
│   ├── laporan/
│   ├── pengaturan/
│   ├── notifikasi/
│   └── kk/
├── vendor/
├── db_wargart.sql
├── .htaccess
├── index.php
└── README.md
```

## Routing

| URL | Controller | Method |
|-----|------------|--------|
| /dashboard | DashboardController | index |
| /warga | WargaController | index |
| /warga/tambah | WargaController | tambah |
| /warga/edit | WargaController | edit |
| /warga/hapus | WargaController | hapus |
| /warga/detail | WargaController | detail |
| /iuran | IuranController | index |
| /iuran/generate | IuranController | generate |
| /iuran/pembayaran | IuranController | pembayaran |
| /iuran/verifikasi | IuranController | verifikasi |
| /kas | KasController | index |
| /kas/masuk | KasController | masuk |
| /kas/keluar | KasController | keluar |
| /pengumuman | PengumumanController | index |
| /kegiatan | KegiatanController | index |
| /surat | SuratController | index |
| /surat/aju | SuratController | aju |
| /surat/detail | SuratController | detail |
| /surat/cetak | SuratController | cetak |
| /pengaduan | PengaduanController | index |
| /laporan | LaporanController | index |
| /laporan/kas | LaporanController | kas |
| /laporan/iuran | LaporanController | iuran |
| /laporan/tunggakan | LaporanController | tunggakan |
| /pengaturan | PengaturanController | index |
| /kk | KKController | index |

## Database Schema

### Tabel Utama

- `users` - Data user/login
- `warga` - Data warga
- `kartu_keluarga` - Data KK
- `jenis_iuran` - Jenis iuran
- `tagihan_iuran` - Tagihan bulanan
- `pembayaran` - Pembayaran iuran
- `kas_rt` - Kas masuk/keluar
- `pengumuman` - Pengumuman
- `kegiatan` - Kegiatan RT
- `surat_pengantar` - Surat pengantar
- `pengaduan` - Pengaduan warga
- `notifikasi` - Notifikasi sistem
- `pengaturan` - Pengaturan aplikasi
- `activity_log` - Log aktivitas

## Fitur Tambahan

### Import/Export
- Import data warga dari CSV
- Export data warga ke CSV
- Export laporan ke PDF

### QRCode
- QRCode profil warga
- QRCode validasi surat

### Notifikasi
- Notifikasi tagihan
- Notifikasi pengumuman
- Toast notification

### Keamanan
- Prepared statements (PDO)
- Input sanitization
- CSRF token
- Session management

## Lisensi

MIT License - Bebas digunakan untuk keperluan apapun.

## Kontak

Untuk pertanyaan atau bantuan, silakan hubungi developer.