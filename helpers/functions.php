<?php
/**
 * Helper Functions
 * SI-WargaRT - Sistem Informasi Warga RT
 */

 /**
 * Sanitasi Input
 */
function sanitize($input) {
    if (is_array($input)) {
        foreach ($input as $key => $value) {
            $input[$key] = sanitize($value);
        }
        return $input;
    }
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

/**
 * Format Tanggal Indonesia
 */
function formatTanggal($date, $format = 'd F Y') {
    if (empty($date)) {
        return '-';
    }

    $bulan = [
        'January' => 'Januari',
        'February' => 'Februari',
        'March' => 'Maret',
        'April' => 'April',
        'May' => 'Mei',
        'June' => 'Juni',
        'July' => 'Juli',
        'August' => 'Agustus',
        'September' => 'September',
        'October' => 'Oktober',
        'November' => 'November',
        'December' => 'Desember'
    ];

    $tanggal = date($format, strtotime($date));
    return strtr($tanggal, $bulan);
}

/**
 * Format Tanggal Pendek
 */
function formatTanggalPendek($date) {
    if (empty($date)) {
        return '-';
    }

    return date('d/m/Y', strtotime($date));
}

/**
 * Format Currency Indonesia
 */
function formatCurrency($amount) {
    return 'Rp ' . number_format((float)($amount ?? 0), 0, ',', '.');
}

/**
 * Generate Slug
 */
function slugify($text) {
    $text = preg_replace('/[^a-z0-9]+/i', '-', strtolower($text));
    return trim($text, '-');
}

/**
 * Generate Random String
 */
function generateRandomString($length = 10) {
    return substr(str_shuffle('0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, $length);
}

/**
 * Upload File
 */
function uploadFile($file, $destination, $allowedTypes = ['jpg', 'jpeg', 'png', 'gif', 'pdf']) {
    if ($file['error'] === UPLOAD_ERR_OK) {
        $fileInfo = pathinfo($file['name']);
        $extension = strtolower($fileInfo['extension']);

        if (!in_array($extension, $allowedTypes)) {
            return ['success' => false, 'message' => 'Tipe file tidak diizinkan'];
        }

        if ($file['size'] > MAX_FILE_SIZE) {
            return ['success' => false, 'message' => 'Ukuran file terlalu besar'];
        }

        $newFileName = time() . '_' . generateRandomString() . '.' . $extension;
        $uploadPath = $destination . $newFileName;

        if (!is_dir($destination)) {
            mkdir($destination, 0755, true);
        }

        if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
            return ['success' => true, 'filename' => $newFileName];
        }
    }

    return ['success' => false, 'message' => 'Gagal mengupload file'];
}

/**
 * Delete File
 */
function deleteFile($filepath) {
    if (file_exists($filepath) && is_file($filepath)) {
        return unlink($filepath);
    }
    return false;
}

/**
 * Get Foto Warga (dengan fallback ke default.png)
 */
function getFotoWarga($foto) {
    if (empty($foto) || $foto === 'default.png') {
        return BASE_URL . 'uploads/warga/default.png';
    }
    
    if (!file_exists(UPLOAD_WARGA . $foto)) {
        return BASE_URL . 'uploads/warga/default.png';
    }
    
    return BASE_URL . 'uploads/warga/' . $foto;
}

/**
 * Session Flash Message
 */
function setFlash($type, $message) {
    $_SESSION['flash'] = [
        'type' => $type,
        'message' => $message
    ];
}

function getFlash() {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

/**
 * Redirect - Nginx compatible
 */
function redirect($url) {
    $url = ltrim($url, '/');
    header("Location: " . nginx_url($url));
    exit;
}

/**
 * JSON Response
 */
function jsonResponse($success, $message = '', $data = []) {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => $success,
        'message' => $message,
        'data' => $data
    ]);
    exit;
}

/**
 * Get Client IP
 */
function getClientIP() {
    $ip = '';
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    return $ip;
}

/**
 * Get User Agent
 */
function getUserAgent() {
    return $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';
}

/**
 * Check Login
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

/**
 * Get Current User
 */
function getCurrentUser() {
    if (isLoggedIn()) {
        return [
            'id' => $_SESSION['user_id'],
            'username' => $_SESSION['username'],
            'nama' => $_SESSION['nama'],
            'role' => $_SESSION['role'],
            'foto' => $_SESSION['foto'] ?? 'default.png',
            'id_warga' => $_SESSION['id_warga'] ?? null
        ];
    }
    return null;
}

/**
 * Check Role
 */
function hasRole($roles) {
    if (!isLoggedIn()) return false;

    if (is_string($roles)) {
        $roles = [$roles];
    }

    return in_array($_SESSION['role'], $roles);
}

/**
 * Require Login
 */
function requireLogin() {
    if (!isLoggedIn()) {
        setFlash('error', 'Silakan login terlebih dahulu');
        redirect('/auth/login');
    }
}

/**
 * Require Role
 */
function requireRole($roles) {
    requireLogin();
    if (!hasRole($roles)) {
        setFlash('error', 'Anda tidak memiliki akses ke halaman ini');
        redirect('/dashboard');
    }
}

/**
 * Hash Password
 */
function hashPassword($password) {
    return password_hash($password, PASSWORD_BCRYPT);
}

/**
 * Verify Password
 */
function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}

/**
 * Generate CSRF Token
 */
function generateCSRFToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Validate CSRF Token
 */
function validateCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Log Activity
 */
function logActivity($activity, $modul = null) {
    $db = getDb();
    $userId = $_SESSION['user_id'] ?? null;
    $ipAddress = getClientIP();
    $userAgent = getUserAgent();

    $stmt = $db->prepare("
        INSERT INTO activity_log (id_user, activity, modul, ip_address, user_agent) 
        VALUES (?, ?, ?, ?, ?)
    ");
    $stmt->execute([$userId, $activity, $modul, $ipAddress, $userAgent]);
}

// ===========================================
// NGINX COMPATIBLE URL FUNCTIONS
// ===========================================
// Karena Nginx tidak support .htaccess, URL menggunakan format query string
// Format: /index.php?route=controller/method

/**
 * Generate URL untuk Nginx (tanpa .htaccess)
 */
function nginx_url($path = '') {
    $baseUrl = defined('BASE_URL') ? BASE_URL : '';
    $path = ltrim($path, '/');

    if (empty($path)) {
        return rtrim($baseUrl, '/') . '/';
    }

    // Handle path yang sudah memiliki query string di dalamnya
    if (strpos($path, '?') !== false) {
        $parts = explode('?', $path, 2);
        return rtrim($baseUrl, '/') . '/index.php?route=' . $parts[0] . '&' . $parts[1];
    }

    // Gunakan format query string untuk nginx
    return rtrim($baseUrl, '/') . '/index.php?route=' . $path;
}

/**
 * Generate URL dengan route parameter
 */
function route_url($path, $params = []) {
    $url = nginx_url($path);

    if (!empty($params)) {
        $query = http_build_query($params);
        $url .= '&' . $query;
    }

    return $url;
}

/**
 * Redirect yang compatible dengan Nginx
 */
function nginx_redirect($path) {
    $url = nginx_url($path);
    header("Location: " . $url);
    exit;
}

/**
 * Get Current URL (Nginx compatible)
 */
function current_url() {
    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    $uri = $_SERVER['REQUEST_URI'];

    // Parse route dari query string
    $route = isset($_GET['route']) ? '/' . $_GET['route'] : '';

    return $scheme . '://' . $host . $route;
}

/**
 * Check apakah menggunakan Nginx
 */
function is_nginx() {
    return isset($_SERVER['SERVER_SOFTWARE']) && strpos($_SERVER['SERVER_SOFTWARE'], 'nginx') !== false;
}

/**
 * Get Sidebar Menu URL (Nginx compatible)
 */
function getSidebarMenu() {
    $menus = [
        'dashboard' => [
            'icon' => 'tachometer-alt',
            'text' => 'Dashboard',
            'url' => nginx_url('dashboard'),
            'roles' => ['admin', 'ketua_rt', 'warga']
        ],
        'data_warga' => [
            'icon' => 'users',
            'text' => 'Data Warga',
            'url' => nginx_url('warga'),
            'roles' => ['admin', 'ketua_rt']
        ],
        'kk' => [
            'icon' => 'address-card',
            'text' => 'Kartu Keluarga',
            'url' => nginx_url('kk'),
            'roles' => ['admin', 'ketua_rt']
        ],
        'iuran' => [
            'icon' => 'money-bill',
            'text' => 'Iuran RT',
            'url' => nginx_url('iuran'),
            'roles' => ['admin', 'ketua_rt', 'warga']
        ],
        'kas' => [
            'icon' => 'cash-register',
            'text' => 'Kas RT',
            'url' => nginx_url('kas'),
            'roles' => ['admin', 'ketua_rt']
        ],
        'pengumuman' => [
            'icon' => 'bullhorn',
            'text' => 'Pengumuman',
            'url' => nginx_url('pengumuman'),
            'roles' => ['admin', 'ketua_rt']
        ],
        'kegiatan' => [
            'icon' => 'calendar-alt',
            'text' => 'Kegiatan',
            'url' => nginx_url('kegiatan'),
            'roles' => ['admin', 'ketua_rt', 'warga']
        ],
        'surat' => [
            'icon' => 'file-alt',
            'text' => 'Surat Pengantar',
            'url' => nginx_url('surat'),
            'roles' => ['admin', 'ketua_rt', 'warga']
        ],
        'pengaduan' => [
            'icon' => 'exclamation-circle',
            'text' => 'Pengaduan',
            'url' => nginx_url('pengaduan'),
            'roles' => ['admin', 'ketua_rt', 'warga']
        ],
        'laporan' => [
            'icon' => 'chart-bar',
            'text' => 'Laporan',
            'url' => nginx_url('laporan'),
            'roles' => ['admin', 'ketua_rt']
        ],
        'forum' => [
            'icon' => 'comments',
            'text' => 'Forum Warga',
            'url' => nginx_url('forum'),
            'roles' => ['admin', 'ketua_rt', 'warga']
        ],
        'pengaturan' => [
            'icon' => 'cog',
            'text' => 'Pengaturan',
            'url' => nginx_url('pengaturan'),
            'roles' => ['admin']
        ]
    ];

    $userRole = $_SESSION['role'] ?? '';
    $filteredMenus = [];

    foreach ($menus as $key => $menu) {
        if (in_array($userRole, $menu['roles'])) {
            $filteredMenus[$key] = $menu;
        }
    }

    return $filteredMenus;
}

/**
 * Get Breadcrumb (Nginx compatible)
 */
function getBreadcrumb($items = []) {
    $html = '<ol class="breadcrumb">';
    $html .= '<li class="breadcrumb-item"><a href="' . nginx_url('') . '"><i class="fas fa-home"></i></a></li>';

    foreach ($items as $item) {
        if (isset($item['url'])) {
            $html .= '<li class="breadcrumb-item"><a href="' . nginx_url($item['url']) . '">' . $item['text'] . '</a></li>';
        } else {
            $html .= '<li class="breadcrumb-item active">' . $item['text'] . '</li>';
        }
    }

    $html .= '</ol>';
    return $html;
}

/**
 * Active Menu Check (Nginx compatible)
 */
function isActiveMenu($menu) {
    $route = isset($_GET['route']) ? trim($_GET['route'], '/') : '';

    if ($menu === 'dashboard') {
        return $route === '' || $route === 'dashboard';
    }

    // Check if route starts with menu key
    return strpos($route, $menu) === 0;
}

/**
 * Pagination (Nginx compatible)
 */
function pagination($total, $perPage, $currentPage, $baseUrl, $extraParams = []) {
    $totalPages = ceil($total / $perPage);
    $adjacents = 2;

    $baseUrl = nginx_url($baseUrl);

    $pagination = '<ul class="pagination justify-content-center">';

    if ($currentPage > 1) {
        $params = array_merge(['page' => 1], $extraParams);
        $pagination .= '<li class="page-item"><a class="page-link" href="' . $baseUrl . '&page=1">First</a></li>';
        $pagination .= '<li class="page-item"><a class="page-link" href="' . $baseUrl . '&page=' . ($currentPage - 1) . '">Prev</a></li>';
    }

    if ($totalPages <= 5) {
        for ($i = 1; $i <= $totalPages; $i++) {
            $active = ($i == $currentPage) ? 'active' : '';
            $pagination .= '<li class="page-item ' . $active . '"><a class="page-link" href="' . $baseUrl . '&page=' . $i . '">' . $i . '</a></li>';
        }
    } else {
        if ($currentPage <= $adjacents + 1) {
            for ($i = 1; $i <= $adjacents + 3; $i++) {
                $active = ($i == $currentPage) ? 'active' : '';
                $pagination .= '<li class="page-item ' . $active . '"><a class="page-link" href="' . $baseUrl . '&page=' . $i . '">' . $i . '</a></li>';
            }
            $pagination .= '<li class="page-item disabled"><span class="page-link">...</span></li>';
            $pagination .= '<li class="page-item"><a class="page-link" href="' . $baseUrl . '&page=' . $totalPages . '">' . $totalPages . '</a></li>';
        } elseif ($currentPage > ($totalPages - $adjacents)) {
            $pagination .= '<li class="page-item"><a class="page-link" href="' . $baseUrl . '&page=1">1</a></li>';
            $pagination .= '<li class="page-item disabled"><span class="page-link">...</span></li>';
            for ($i = $totalPages - $adjacents - 1; $i <= $totalPages; $i++) {
                $active = ($i == $currentPage) ? 'active' : '';
                $pagination .= '<li class="page-item ' . $active . '"><a class="page-link" href="' . $baseUrl . '&page=' . $i . '">' . $i . '</a></li>';
            }
        } else {
            $pagination .= '<li class="page-item"><a class="page-link" href="' . $baseUrl . '&page=1">1</a></li>';
            $pagination .= '<li class="page-item disabled"><span class="page-link">...</span></li>';
            for ($i = $currentPage - $adjacents; $i <= $currentPage + $adjacents; $i++) {
                $active = ($i == $currentPage) ? 'active' : '';
                $pagination .= '<li class="page-item ' . $active . '"><a class="page-link" href="' . $baseUrl . '&page=' . $i . '">' . $i . '</a></li>';
            }
            $pagination .= '<li class="page-item disabled"><span class="page-link">...</span></li>';
            $pagination .= '<li class="page-item"><a class="page-link" href="' . $baseUrl . '&page=' . $totalPages . '">' . $totalPages . '</a></li>';
        }
    }

    if ($currentPage < $totalPages) {
        $pagination .= '<li class="page-item"><a class="page-link" href="' . $baseUrl . '&page=' . ($currentPage + 1) . '">Next</a></li>';
        $pagination .= '<li class="page-item"><a class="page-link" href="' . $baseUrl . '&page=' . $totalPages . '">Last</a></li>';
    }

    $pagination .= '</ul>';

    return $pagination;
}

/**
 * Get Bulan Indonesia
 */
function getBulanIndonesia($bulan) {
    $bulanArray = [
        '01' => 'Januari',
        '02' => 'Februari',
        '03' => 'Maret',
        '04' => 'April',
        '05' => 'Mei',
        '06' => 'Juni',
        '07' => 'Juli',
        '08' => 'Agustus',
        '09' => 'September',
        '10' => 'Oktober',
        '11' => 'November',
        '12' => 'Desember'
    ];
    return $bulanArray[$bulan] ?? $bulan;
}

/**
 * Get Unread Notification Count
 */
function getUnreadNotificationCount() {
    $db = getDb();
    $userId = $_SESSION['user_id'] ?? 0;
    $stmt = $db->prepare("SELECT COUNT(*) as total FROM notifikasi WHERE id_user = ? AND is_read = 0");
    $stmt->execute([$userId]);
    $result = $stmt->fetch();
    return $result['total'] ?? 0;
}

/**
 * Get Status Badge HTML
 */
function getStatusBadge($status) {
    $badges = [
        'Aktif' => 'success',
        'Non-Aktif' => 'secondary',
        'Pending' => 'warning',
        'Lunas' => 'success',
        'Menunggak' => 'danger',
        'Verified' => 'success',
        'Rejected' => 'danger',
        'Dikirim' => 'info',
        'Diproses' => 'warning',
        'Selesai' => 'success',
        'Ditolak' => 'danger',
        'Draft' => 'secondary',
        'Published' => 'success',
        'Archived' => 'secondary',
        'Approved_Ketua' => 'info',
        'Approved_Admin' => 'success',
        'Akan Datang' => 'primary',
        'Sedang Berlangsung' => 'success',
        'Dibatalkan' => 'danger'
    ];
    $class = $badges[$status] ?? 'secondary';
    return '<span class="badge badge-' . $class . '">' . htmlspecialchars($status) . '</span>';
}
