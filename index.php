<?php
/**
 * SI-WargaRT - Sistem Informasi Warga RT
 * Main Entry Point
 * Compatible with Nginx (no .htaccess required)
 */

// Start session
session_name('siwargart_session');
session_start();

// Base URL untuk server nginx
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
$host = $_SERVER['HTTP_HOST'];
$scriptPath = dirname($_SERVER['SCRIPT_NAME']);
$baseUrl = $protocol . $host . $scriptPath;
define('BASE_URL', rtrim($baseUrl, '/') . '/');

// Load configuration
require_once __DIR__ . '/config/database.php';

// Fallback definisi path jika tidak sengaja terhapus di config/database.php
defined('BASEPATH') or define('BASEPATH', __DIR__ . '/');
defined('VIEWSPATH') or define('VIEWSPATH', BASEPATH . 'views/');
defined('LAYOUTS') or define('LAYOUTS', BASEPATH . 'layouts/');
defined('HELPERPATH') or define('HELPERPATH', BASEPATH . 'helpers/');
defined('CONTROLLERPATH') or define('CONTROLLERPATH', BASEPATH . 'controllers/');

// Load helpers
require_once HELPERPATH . 'functions.php';

// Generate CSRF Token
generateCSRFToken();

// ===========================================
// ROUTING UNTUK NGINX (tanpa .htaccess)
// ===========================================
// Untuk Nginx, gunakan format: index.php?route=path/to/page
// Atau buat rewrite di nginx.conf:
//   location / {
//       try_files $uri $uri/ /index.php?route=$request_uri;
//   }

// Get route dari query string
$route = isset($_GET['route']) ? trim($_GET['route'], '/') : '';
$route = $route ? explode('/', $route) : [];

// Controller dan Method default
$controllerName = 'Dashboard';
$methodName = 'index';

// Routing mapping
$routes = [
    // Auth
    ''                      => ['controller' => 'Dashboard', 'method' => 'index'],
    'dashboard'             => ['controller' => 'Dashboard', 'method' => 'index'],
    'auth/login'            => ['controller' => 'Auth', 'method' => 'login'],
    'auth/logout'           => ['controller' => 'Auth', 'method' => 'logout'],
    'auth/register'         => ['controller' => 'Auth', 'method' => 'register'],
    'auth/profile'          => ['controller' => 'Auth', 'method' => 'profile'],
    'auth/proses_login'     => ['controller' => 'Auth', 'method' => 'proses_login'],
    'auth/proses_register' => ['controller' => 'Auth', 'method' => 'proses_register'],
    'auth/proses_profile'   => ['controller' => 'Auth', 'method' => 'proses_profile'],

    // Warga
    'warga'                 => ['controller' => 'Warga', 'method' => 'index'],
    'warga/tambah'          => ['controller' => 'Warga', 'method' => 'tambah'],
    'warga/proses_tambah'   => ['controller' => 'Warga', 'method' => 'proses_tambah'],
    'warga/edit'            => ['controller' => 'Warga', 'method' => 'edit'],
    'warga/proses_edit'     => ['controller' => 'Warga', 'method' => 'proses_edit'],
    'warga/hapus'           => ['controller' => 'Warga', 'method' => 'hapus'],
    'warga/detail'          => ['controller' => 'Warga', 'method' => 'detail'],
    'warga/import'          => ['controller' => 'Warga', 'method' => 'import'],
    'warga/proses_import'   => ['controller' => 'Warga', 'method' => 'proses_import'],
    'warga/export'          => ['controller' => 'Warga', 'method' => 'export'],
    'warga/export_pdf'      => ['controller' => 'Warga', 'method' => 'export_pdf'],

    // KK
    'kk'                    => ['controller' => 'KK', 'method' => 'index'],
    'kk/tambah'             => ['controller' => 'KK', 'method' => 'tambah'],
    'kk/proses_tambah'      => ['controller' => 'KK', 'method' => 'proses_tambah'],
    'kk/edit'               => ['controller' => 'KK', 'method' => 'edit'],
    'kk/proses_edit'        => ['controller' => 'KK', 'method' => 'proses_edit'],
    'kk/hapus'              => ['controller' => 'KK', 'method' => 'hapus'],
    'kk/detail'             => ['controller' => 'KK', 'method' => 'detail'],

    // Iuran
    'iuran'                 => ['controller' => 'Iuran', 'method' => 'index'],
    'iuran/jenis'           => ['controller' => 'Iuran', 'method' => 'jenis'],
    'iuran/tambah_jenis'    => ['controller' => 'Iuran', 'method' => 'tambah_jenis'],
    'iuran/proses_jenis'    => ['controller' => 'Iuran', 'method' => 'proses_jenis'],
    'iuran/hapus_jenis'     => ['controller' => 'Iuran', 'method' => 'hapus_jenis'],
    'iuran/generate'        => ['controller' => 'Iuran', 'method' => 'generate'],
    'iuran/proses_generate' => ['controller' => 'Iuran', 'method' => 'proses_generate'],
    'iuran/pembayaran'      => ['controller' => 'Iuran', 'method' => 'pembayaran'],
    'iuran/proses_pembayaran' => ['controller' => 'Iuran', 'method' => 'proses_pembayaran'],
    'iuran/get_tagihan'     => ['controller' => 'Iuran', 'method' => 'get_tagihan'],
    'iuran/verifikasi'      => ['controller' => 'Iuran', 'method' => 'verifikasi'],
    'iuran/proses_verifikasi' => ['controller' => 'Iuran', 'method' => 'proses_verifikasi'],
    'iuran/hapus_tagihan'   => ['controller' => 'Iuran', 'method' => 'hapus_tagihan'],

    // Kas RT
    'kas'                   => ['controller' => 'Kas', 'method' => 'index'],
    'kas/masuk'             => ['controller' => 'Kas', 'method' => 'masuk'],
    'kas/proses_masuk'      => ['controller' => 'Kas', 'method' => 'proses_masuk'],
    'kas/keluar'            => ['controller' => 'Kas', 'method' => 'keluar'],
    'kas/proses_keluar'     => ['controller' => 'Kas', 'method' => 'proses_keluar'],

    // Pengumuman
    'pengumuman'            => ['controller' => 'Pengumuman', 'method' => 'index'],
    'pengumuman/tambah'     => ['controller' => 'Pengumuman', 'method' => 'tambah'],
    'pengumuman/proses_tambah' => ['controller' => 'Pengumuman', 'method' => 'proses_tambah'],
    'pengumuman/edit'       => ['controller' => 'Pengumuman', 'method' => 'edit'],
    'pengumuman/proses_edit' => ['controller' => 'Pengumuman', 'method' => 'proses_edit'],
    'pengumuman/hapus'      => ['controller' => 'Pengumuman', 'method' => 'hapus'],

    // Kegiatan
    'kegiatan'              => ['controller' => 'Kegiatan', 'method' => 'index'],
    'kegiatan/tambah'       => ['controller' => 'Kegiatan', 'method' => 'tambah'],
    'kegiatan/detail'       => ['controller' => 'Kegiatan', 'method' => 'detail'],
    'kegiatan/proses_tambah' => ['controller' => 'Kegiatan', 'method' => 'proses_tambah'],
    'kegiatan/edit'         => ['controller' => 'Kegiatan', 'method' => 'edit'],
    'kegiatan/proses_edit'  => ['controller' => 'Kegiatan', 'method' => 'proses_edit'],
    'kegiatan/hapus'        => ['controller' => 'Kegiatan', 'method' => 'hapus'],

    // Surat Pengantar
    'surat'                 => ['controller' => 'Surat', 'method' => 'index'],
    'surat/aju'             => ['controller' => 'Surat', 'method' => 'aju'],
    'surat/proses_aju'      => ['controller' => 'Surat', 'method' => 'proses_aju'],
    'surat/detail'          => ['controller' => 'Surat', 'method' => 'detail'],
    'surat/cetak'           => ['controller' => 'Surat', 'method' => 'cetak'],
    'surat/approve'         => ['controller' => 'Surat', 'method' => 'approve'],
    'surat/reject'          => ['controller' => 'Surat', 'method' => 'reject'],

    // Pengaduan
    'pengaduan'             => ['controller' => 'Pengaduan', 'method' => 'index'],
    'pengaduan/tambah'      => ['controller' => 'Pengaduan', 'method' => 'tambah'],
    'pengaduan/proses_tambah' => ['controller' => 'Pengaduan', 'method' => 'proses_tambah'],
    'pengaduan/respon'      => ['controller' => 'Pengaduan', 'method' => 'respon'],
    'pengaduan/proses_respon' => ['controller' => 'Pengaduan', 'method' => 'proses_respon'],
    'pengaduan/hapus'       => ['controller' => 'Pengaduan', 'method' => 'hapus'],

    // Laporan
    'laporan'               => ['controller' => 'Laporan', 'method' => 'index'],
    'laporan/kas'           => ['controller' => 'Laporan', 'method' => 'kas'],
    'laporan/iuran'         => ['controller' => 'Laporan', 'method' => 'iuran'],
    'laporan/tunggakan'     => ['controller' => 'Laporan', 'method' => 'tunggakan'],
    'laporan/export_kas_pdf' => ['controller' => 'Laporan', 'method' => 'export_kas_pdf'],
    'laporan/export_iuran_pdf' => ['controller' => 'Laporan', 'method' => 'export_iuran_pdf'],
    'laporan/export_tunggakan_pdf' => ['controller' => 'Laporan', 'method' => 'export_tunggakan_pdf'],
    'laporan/export_pdf'    => ['controller' => 'Laporan', 'method' => 'export_all_pdf'],

    // Forum Warga
    'forum'                 => ['controller' => 'Forum', 'method' => 'index'],
    'forum/tambah'          => ['controller' => 'Forum', 'method' => 'tambah'],
    'forum/proses_tambah'   => ['controller' => 'Forum', 'method' => 'proses_tambah'],
    'forum/detail'          => ['controller' => 'Forum', 'method' => 'detail'],
    'forum/proses_komentar' => ['controller' => 'Forum', 'method' => 'proses_komentar'],
    'forum/hapus'           => ['controller' => 'Forum', 'method' => 'hapus'],

    // Pengaturan
    'pengaturan'            => ['controller' => 'Pengaturan', 'method' => 'index'],
    'pengaturan/update'     => ['controller' => 'Pengaturan', 'method' => 'update'],
    'pengaturan/toggle_user'=> ['controller' => 'Pengaturan', 'method' => 'toggle_user'],
    'pengaturan/tambah_user'=> ['controller' => 'Pengaturan', 'method' => 'tambah_user'],
    'pengaturan/edit_user'  => ['controller' => 'Pengaturan', 'method' => 'edit_user'],
    'pengaturan/hapus_user' => ['controller' => 'Pengaturan', 'method' => 'hapus_user'],
    'pengaturan/tambah_iuran'=> ['controller' => 'Pengaturan', 'method' => 'tambah_iuran'],
    'pengaturan/edit_iuran' => ['controller' => 'Pengaturan', 'method' => 'edit_iuran'],
    'pengaturan/hapus_iuran'=> ['controller' => 'Pengaturan', 'method' => 'hapus_iuran'],

    // Notifikasi
    'notifikasi'            => ['controller' => 'Notifikasi', 'method' => 'index'],
    'notifikasi/mark_read'  => ['controller' => 'Notifikasi', 'method' => 'mark_read'],
];

// Build route key
$routeKey = implode('/', $route);
$is404 = false;

// Match route
if (empty($routeKey)) {
    // Gunakan default controller & method (Dashboard/index)
} elseif (isset($routes[$routeKey])) {
    $controllerName = $routes[$routeKey]['controller'];
    $methodName = $routes[$routeKey]['method'];
} elseif (!empty($route[0]) && isset($routes[$route[0]])) {
    $controllerName = $routes[$route[0]]['controller'];
    $methodName = $routes[$route[0]]['method'];
} else {
    $is404 = true;
}

if (!$is404) {
    // Load controller
    $controllerFile = CONTROLLERPATH . $controllerName . 'Controller.php';
    
    if (file_exists($controllerFile)) {
        require_once $controllerFile;
        $controllerClass = $controllerName . 'Controller';
    
        if (class_exists($controllerClass) && method_exists($controllerClass, $methodName)) {
            $controllerInstance = new $controllerClass();
            $controllerInstance->$methodName();
            exit;
        }
    }
}

// Output 404 global jika rute/file/class/method tidak valid
http_response_code(404);
echo '<h1>404 - Page Not Found</h1>';
echo '<p>Halaman yang Anda tuju tidak ditemukan.</p>';
echo '<a href="' . nginx_url('') . '">Kembali ke Dashboard</a>';