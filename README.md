# SI-WargaRT (Sistem Informasi Warga RT)

Aplikasi web administrasi pemerintahan tingkat RT/RW yang dibangun menggunakan PHP Native (tanpa framework), MySQL, AdminLTE v3, Bootstrap 5, jQuery, dan AJAX. Aplikasi ini dirancang untuk memudahkan pengelolaan data warga, iuran, pengumuman, kegiatan, surat pengantar, pengaduan, dan laporan di lingkungan RT/RW.

## Fitur Aplikasi

1.  **Authentication**:
    *   Login multi-role (Admin, Ketua RT, Warga).
    *   Session login dan logout.
    *   Middleware role access.
    *   Registrasi warga baru dengan validasi Admin.

2.  **Dashboard**:
    *   Menampilkan total warga, total KK, total iuran masuk, total tunggakan.
    *   Statistik pembayaran dan grafik kas RT (menggunakan Chart.js).
    *   Pengumuman dan kegiatan terbaru.

3.  **Data Warga**:
    *   CRUD lengkap (Tambah, Edit, Hapus, Detail).
    *   Field: NIK, No KK, Nama lengkap, Tempat/Tanggal lahir, Jenis kelamin, Agama, Status perkawinan, Pekerjaan, No HP, Email, Alamat, RT/RW, Foto warga, Foto rumah, Status warga.
    *   Upload foto warga.
    *   QRCode profil warga.
    *   Import/Export data warga via Excel.
    *   Export data warga ke PDF.

4.  **Kartu Keluarga (KK)**:
    *   CRUD lengkap (Tambah, Edit, Hapus, Detail).
    *   Menampilkan daftar anggota keluarga yang terhubung.

5.  **Iuran RT**:
    *   Generate tagihan bulanan (Kebersihan, Keamanan, Kas RT, Dana Sosial).
    *   Input pembayaran (oleh Admin/Ketua RT) atau pengajuan pembayaran (oleh Warga).
    *   Upload bukti transfer.
    *   Verifikasi pembayaran oleh Admin/Ketua RT.
    *   Status tagihan: Lunas, Pending, Menunggak.
    *   Rekap bulanan dan riwayat pembayaran.
    *   Cetak PDF riwayat iuran.

6.  **Kas RT**:
    *   Pencatatan kas masuk dan kas keluar.
    *   Filter berdasarkan bulan/tahun.
    *   Laporan rekapitulasi kas.

7.  **Pengumuman & Kegiatan**:
    *   CRUD lengkap untuk Pengumuman dan Kegiatan RT.
    *   Field: Judul, Isi, Tanggal, Lokasi, Banner.
    *   Detail kegiatan.

8.  **Surat Pengantar**:
    *   Pengajuan surat online (Domisili, Usaha, Pengantar Nikah, Keterangan).
    *   Approval bertingkat (Ketua RT, Admin).
    *   Generate PDF otomatis dengan QR Code validasi surat.

9.  **Pengaduan Warga**:
    *   Input pengaduan dengan upload foto.
    *   Status pengaduan: Dikirim, Diproses, Selesai.
    *   Balasan/respon dari Admin/Ketua RT.

10. **Forum Warga**:
    *   Warga dapat membuat topik diskusi baru.
    *   Warga lain dapat mengirim pesan/komentar untuk berdiskusi.
    *   Admin/Ketua RT dapat menghapus topik.

11. **Laporan**:
    *   Generate laporan kas masuk/keluar, rekap iuran, tunggakan warga.
    *   Export laporan ke PDF.
    *   Filter laporan berdasarkan tanggal/bulan.

12. **Notifikasi**:
    *   Sistem notifikasi untuk tagihan, pengumuman baru, surat selesai, pengaduan dibalas.
    *   Toast notification dan badge notification.

13. **Keamanan**:
    *   PDO / Prepared Statement untuk mencegah SQL Injection.
    *   Validasi form dan sanitasi input.
    *   Validasi upload file.
    *   Session security.
    *   CSRF Token sederhana.

## Teknologi yang Digunakan

*   **Backend**: PHP Native (tanpa framework), MySQL
*   **Frontend**: HTML5, CSS3, JavaScript
*   **UI Framework**: AdminLTE v3, Bootstrap 5
*   **Interaktivitas**: jQuery, AJAX
*   **Tabel Data**: DataTables
*   **Notifikasi**: SweetAlert2
*   **PDF Generation**: DomPDF
*   **QR Code**: QRCode.js
*   **Charts**: Chart.js

## Instalasi (Menggunakan Laragon)

Ikuti langkah-langkah berikut untuk menjalankan aplikasi di lingkungan Laragon:

1.  **Download Proyek**:
    *   Unduh seluruh folder proyek `si-wargart` ke direktori `G:\laragon\www\`.
    *   Pastikan struktur folder menjadi `G:\laragon\www\si-wargart`.

2.  **Konfigurasi Database**:
    *   Buka Laragon, pastikan Apache dan MySQL berjalan.
    *   Buka phpMyAdmin (biasanya melalui menu Laragon -> `Database -> phpMyAdmin`).
    *   Buat database baru dengan nama `db_wargart`.
    *   Import file `db_wargart.sql` yang ada di root folder proyek (`G:\laragon\www\si-wargart\db_wargart.sql`) ke database `db_wargart` yang baru Anda buat.
    *   Pastikan user database adalah `root` dengan password kosong (default Laragon). Jika tidak, sesuaikan di `config/database.php`.

3.  **Konfigurasi Aplikasi**:
    *   Buka file `config/database.php`.
    *   Pastikan konfigurasi database sudah sesuai:
        ```php
        define('DB_HOST', 'localhost');
        define('DB_USER', 'root');
        define('DB_PASS', ''); // Kosongkan jika tidak ada password
        define('DB_NAME', 'db_wargart');
        ```
    *   Pastikan `BASE_URL` terdeteksi otomatis. Jika ada masalah, Anda bisa mengaturnya secara manual:
        ```php
        // Contoh manual jika Laragon membuat domain lokal:
        // define('BASE_URL', 'http://si-wargart.test/');
        // Atau jika diakses via localhost/folder:
        // define('BASE_URL', 'http://localhost/si-wargart/');
        ```

4.  **Akses Aplikasi**:
    *   Buka browser Anda dan akses URL proyek. Jika Anda menggunakan Laragon, biasanya akan otomatis terdaftar sebagai `http://si-wargart.test` atau `http://localhost/si-wargart`.

## Akun Default

Setelah instalasi database, Anda dapat login menggunakan akun-akun berikut:

*   **Admin**:
    *   Username: `admin`
    *   Password: `admin123`
*   **Ketua RT**:
    *   Username: `ketua`
    *   Password: `ketua123`
*   **Warga**:
    *   Username: `warga`
    *   Password: `warga123`

*(Catatan: Untuk akun warga, Anda mungkin perlu membuat akun baru melalui halaman registrasi dengan NIK yang valid dari data dummy, lalu mengaktifkannya melalui menu Pengaturan Admin, karena akun 'warga' default mungkin tidak tertaut dengan `id_warga`.)*

## Troubleshooting

*   **"404 Not Found"**: Pastikan konfigurasi Nginx Anda sudah benar untuk *rewrite rules* (jika menggunakan Nginx secara manual, bukan Laragon). Laragon biasanya sudah mengaturnya secara otomatis. Atau periksa kembali `BASE_URL` di `config/database.php`.
*   **"Database Connection Failed"**: Periksa `DB_HOST`, `DB_USER`, `DB_PASS`, dan `DB_NAME` di `config/database.php`. Pastikan MySQL di Laragon sudah berjalan.
*   **"Incorrect Column Count" pada DataTables**: Ini biasanya terjadi jika ada ketidaksesuaian jumlah `<th>` di `<thead>` dan `<td>` di `<tbody>` pada tabel HTML. Pastikan jumlahnya sama.
*   **"Deprecated: strtotime(): Passing null..."**: Pastikan semua fungsi yang memproses tanggal memiliki validasi `empty()` atau `null` sebelum memanggil `strtotime()`.
*   **Link tidak berfungsi**: Pastikan semua URL di *views* menggunakan `nginx_url()` atau `route_url()` untuk navigasi antar halaman, dan `BASE_URL` hanya untuk aset statis (gambar, CSS, JS).

---

Selamat menggunakan SI-WargaRT! Jika ada pertanyaan atau masalah, silakan hubungi pengembang.

---

**Generated by Gemini Code Assist**