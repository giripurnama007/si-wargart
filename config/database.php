<?php
/**
 * Database Configuration
 * SI-WargaRT - Sistem Informasi Warga RT
 */

// Database Configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'db_wargart');

// Get base URL automatically
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
$host = $_SERVER['HTTP_HOST'];
$scriptPath = dirname($_SERVER['SCRIPT_NAME']);
$autoBaseUrl = $protocol . $host . $scriptPath;
defined('BASE_URL') or define('BASE_URL', rtrim($autoBaseUrl, '/') . '/');
define('AUTO_BASE_URL', BASE_URL);

// Application Configuration
define('APP_NAME', 'SI-WargaRT');
define('APP_VERSION', '1.0.0');
define('APP_URL', AUTO_BASE_URL);

// Path Configuration
define('BASEPATH', dirname(__DIR__) . '/');
define('APPPATH', BASEPATH);
define('VIEWSPATH', BASEPATH . 'views/');
define('LAYOUTS', BASEPATH . 'layouts/');
define('CONTROLLERPATH', BASEPATH . 'controllers/');
define('MODELPATH', BASEPATH . 'models/');
define('HELPERPATH', BASEPATH . 'helpers/');
define('UPLOADPATH', BASEPATH . 'uploads/');
define('UPLOAD_WARGA', UPLOADPATH . 'warga/');
define('UPLOAD_PEMBAYARAN', UPLOADPATH . 'pembayaran/');
define('UPLOAD_PENGADUAN', UPLOADPATH . 'pengaduan/');

// Session Configuration
define('SESSION_NAME', 'siwargart_session');
define('SESSION_LIFETIME', 86400); // 24 hours

// Pagination
define('PER_PAGE', 10);

// File Upload Configuration
define('MAX_FILE_SIZE', 2097152); // 2MB
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'gif', 'pdf']);

// Timezone
date_default_timezone_set('Asia/Jakarta');

// Error Reporting (Development)
error_reporting(E_ALL);
ini_set('display_errors', 1);

/**
 * Database Connection using PDO
 */
class Database {
    private static $instance = null;
    private $pdo;

    private function __construct() {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];
            $this->pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            die("Connection failed: " . $e->getMessage());
        }
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->pdo;
    }
}

/**
 * Get Database Connection
 */
function getDb() {
    return Database::getInstance()->getConnection();
}
