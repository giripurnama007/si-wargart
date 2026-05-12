<?php
/**
 * Auth Controller
 * SI-WargaRT - Sistem Informasi Warga RT
 * Nginx Compatible Version
 */

class AuthController {

    private $db;

    public function __construct() {
        $this->db = getDb();
    }

    /**
     * Login Page
     */
    public function login() {
        if (isLoggedIn()) {
            redirect('dashboard');
        }

        $content = '<div class="login-page" style="min-height: 100vh; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
            <div class="login-box">
                <div class="card shadow-lg">
                    <div class="card-body login-card-body">
                        <div class="login-logo mb-4 text-center">
                            <i class="fas fa-home" style="font-size: 60px; color: #667eea;"></i>
                            <h3 class="mt-2 mb-0">SI-WargaRT</h3>
                            <small class="text-muted">Sistem Informasi Warga RT</small>
                        </div>
                        <form action="' . nginx_url('auth/proses_login') . '" method="POST" id="loginForm">
                            <input type="hidden" name="csrf_token" value="' . generateCSRFToken() . '">
                            <div class="input-group mb-3">
                                <input type="text" name="username" class="form-control" placeholder="Username" required autofocus>
                                <div class="input-group-append">
                                    <div class="input-group-text"><span class="fas fa-user"></span></div>
                                </div>
                            </div>
                            <div class="input-group mb-3">
                                <input type="password" name="password" class="form-control" placeholder="Password" required>
                                <div class="input-group-append">
                                    <div class="input-group-text"><span class="fas fa-lock"></span></div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary btn-block">
                                        <i class="fas fa-sign-in-alt mr-2"></i> Masuk
                                    </button>
                                </div>
                            </div>
                        </form>
                        <hr>
                        <p class="mb-0 text-center">
                            <small class="text-muted">Demo Login:</small><br>
                            <span class="text-info">Admin: admin / password</span><br>
                            <span class="text-info">Ketua RT: ketua / password</span><br>
                            <span class="text-info">Warga: warga / password</span>
                        </p>
                        <p class="mt-3 mb-1 text-center">
                            <a href="' . nginx_url('auth/register') . '">Belum punya akun? Daftar di sini</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>';

        // Simple layout without sidebar
        echo '<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - ' . APP_NAME . '</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2.0/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="' . BASE_URL . 'assets/css/style.css">
    <style>
        body { background: #f4f6f9; }
        .login-page { display: flex; align-items: center; justify-content: center; }
        .login-box { width: 380px; }
        .login-logo h3 { font-weight: 600; margin-bottom: 0; color: #333; }
    </style>
</head>
<body>' . $content . '
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</body>
</html>';
    }

    /**
     * Process Login
     */
    public function proses_login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = sanitize($_POST['username'] ?? '');
            $password = $_POST['password'] ?? '';

            // Validate CSRF
            if (!validateCSRFToken($_POST['csrf_token'] ?? '')) {
                setFlash('error', 'Token tidak valid. Silakan refresh halaman.');
                redirect('auth/login');
            }

            // Check credentials
            $stmt = $this->db->prepare("SELECT * FROM users WHERE username = ?");
            $stmt->execute([$username]);
            $user = $stmt->fetch();

            if ($user && verifyPassword($password, $user['password'])) {
                if ($user['is_active'] == 0) {
                    setFlash('error', 'Akun Anda belum diaktifkan oleh Admin. Silakan tunggu persetujuan.');
                    redirect('auth/login');
                }

                // Set session
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['nama'] = $user['nama'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['foto'] = $user['foto'];
                $_SESSION['id_warga'] = $user['id_warga'];
                $_SESSION['login_time'] = time();

                // Update last login
                $updateStmt = $this->db->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
                $updateStmt->execute([$user['id']]);

                // Log activity
                logActivity('Login ke sistem', 'Auth');

                setFlash('success', 'Selamat datang, ' . $user['nama'] . '!');
                redirect('dashboard');
            } else {
                setFlash('error', 'Username atau password salah!');
                redirect('auth/login');
            }
        }

        redirect('auth/login');
    }

    /**
     * Logout
     */
    public function logout() {
        if (isLoggedIn()) {
            logActivity('Logout dari sistem', 'Auth');
        }

        session_destroy();
        setFlash('success', 'Anda telah logout dari sistem');
        redirect('auth/login');
    }

    /**
     * Register Page
     */
    public function register() {
        if (isLoggedIn()) {
            redirect('dashboard');
        }

        $content = '<div class="register-page" style="min-height: 100vh; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
            <div class="register-box">
                <div class="card shadow-lg">
                    <div class="card-body register-card-body">
                        <div class="register-logo mb-4 text-center">
                            <i class="fas fa-user-plus" style="font-size: 50px; color: #667eea;"></i>
                            <h3 class="mt-2 mb-0">Registrasi Warga</h3>
                            <small class="text-muted">Buat akun baru untuk mengakses sistem</small>
                        </div>
                        <form action="' . nginx_url('auth/proses_register') . '" method="POST" id="registerForm">
                            <input type="hidden" name="csrf_token" value="' . generateCSRFToken() . '">
                            <div class="input-group mb-3">
                                <input type="text" name="nik" class="form-control" placeholder="NIK (Sesuai KTP)" required maxlength="20">
                                <div class="input-group-append">
                                    <div class="input-group-text"><span class="fas fa-id-card"></span></div>
                                </div>
                            </div>
                            <div class="input-group mb-3">
                                <input type="text" name="no_kk" class="form-control" placeholder="No. Kartu Keluarga" required maxlength="20">
                                <div class="input-group-append">
                                    <div class="input-group-text"><span class="fas fa-users"></span></div>
                                </div>
                            </div>
                            <div class="input-group mb-3">
                                <select name="jenis_kelamin" class="form-control" required>
                                    <option value="">-- Jenis Kelamin --</option>
                                    <option value="L">Laki-laki</option>
                                    <option value="P">Perempuan</option>
                                </select>
                            </div>
                            <div class="input-group mb-3">
                                <input type="text" name="username" class="form-control" placeholder="Username" required>
                                <div class="input-group-append">
                                    <div class="input-group-text"><span class="fas fa-user"></span></div>
                                </div>
                            </div>
                            <div class="input-group mb-3">
                                <input type="text" name="nama" class="form-control" placeholder="Nama Lengkap" required>
                                <div class="input-group-append">
                                    <div class="input-group-text"><span class="fas fa-user-tag"></span></div>
                                </div>
                            </div>
                            <div class="input-group mb-3">
                                <input type="email" name="email" class="form-control" placeholder="Email">
                                <div class="input-group-append">
                                    <div class="input-group-text"><span class="fas fa-envelope"></span></div>
                                </div>
                            </div>
                            <div class="input-group mb-3">
                                <input type="text" name="no_hp" class="form-control" placeholder="No. HP">
                                <div class="input-group-append">
                                    <div class="input-group-text"><span class="fas fa-phone"></span></div>
                                </div>
                            </div>
                            <div class="input-group mb-3">
                                <input type="password" name="password" class="form-control" placeholder="Password" required minlength="6">
                                <div class="input-group-append">
                                    <div class="input-group-text"><span class="fas fa-lock"></span></div>
                                </div>
                            </div>
                            <div class="input-group mb-3">
                                <input type="password" name="password_confirm" class="form-control" placeholder="Konfirmasi Password" required>
                                <div class="input-group-append">
                                    <div class="input-group-text"><span class="fas fa-lock"></span></div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary btn-block">
                                        <i class="fas fa-user-plus mr-2"></i> Daftar Sekarang
                                    </button>
                                </div>
                            </div>
                        </form>
                        <p class="mt-3 text-center mb-0">
                            Sudah punya akun? <a href="' . nginx_url('auth/login') . '" class="text-primary">Login di sini</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>';

        echo '<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Register - ' . APP_NAME . '</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2.0/dist/css/adminlte.min.css">
    <style>
        body { background: #f4f6f9; }
        .register-page { display: flex; align-items: center; justify-content: center; }
        .register-box { width: 400px; }
        .register-logo h3 { font-weight: 600; color: #333; }
    </style>
</head>
<body>' . $content . '
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>';
    }

    /**
     * Process Register
     */
    public function proses_register() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nik = sanitize($_POST['nik'] ?? '');
            $no_kk = sanitize($_POST['no_kk'] ?? '');
            $jenis_kelamin = sanitize($_POST['jenis_kelamin'] ?? '');
            $username = sanitize($_POST['username'] ?? '');
            $nama = sanitize($_POST['nama'] ?? '');
            $email = sanitize($_POST['email'] ?? '');
            $no_hp = sanitize($_POST['no_hp'] ?? '');
            $password = $_POST['password'] ?? '';
            $password_confirm = $_POST['password_confirm'] ?? '';

            if (!validateCSRFToken($_POST['csrf_token'] ?? '')) {
                setFlash('error', 'Token tidak valid');
                redirect('auth/register');
            }

            if (empty($nik) || empty($no_kk) || empty($jenis_kelamin) || empty($username) || empty($nama) || empty($password)) {
                setFlash('error', 'Semua field wajib diisi!');
                redirect('auth/register');
            }

            if (strlen($password) < 6) {
                setFlash('error', 'Password minimal 6 karakter!');
                redirect('auth/register');
            }

            if ($password !== $password_confirm) {
                setFlash('error', 'Password tidak cocok!');
                redirect('auth/register');
            }

            // Check username exists
            $stmt = $this->db->prepare("SELECT id FROM users WHERE username = ?");
            $stmt->execute([$username]);
            if ($stmt->fetch()) {
                setFlash('error', 'Username sudah digunakan!');
                redirect('auth/register');
            }

            // Check email exists
            if ($email) {
                $stmt = $this->db->prepare("SELECT id FROM users WHERE email = ?");
                $stmt->execute([$email]);
                if ($stmt->fetch()) {
                    setFlash('error', 'Email sudah digunakan!');
                    redirect('auth/register');
                }
            }

            // Cek NIK di database warga
            $stmt = $this->db->prepare("SELECT id FROM warga WHERE nik = ?");
            $stmt->execute([$nik]);
            $warga = $stmt->fetch();

            if (!$warga) {
                // Jika warga belum ada, tambahkan ke tabel warga dengan status Non-Aktif
                $stmt = $this->db->prepare("INSERT INTO warga (nik, no_kk, nama_lengkap, jenis_kelamin, email, no_hp, status_warga) VALUES (?, ?, ?, ?, ?, ?, 'Non-Aktif')");
                $stmt->execute([$nik, $no_kk, $nama, $jenis_kelamin, $email, $no_hp]);
                $id_warga = $this->db->lastInsertId();
            } else {
                $id_warga = $warga['id'];
                
                // Cek apakah id_warga sudah digunakan oleh akun lain
                $stmt = $this->db->prepare("SELECT id FROM users WHERE id_warga = ?");
                $stmt->execute([$id_warga]);
                if ($stmt->fetch()) {
                    setFlash('error', 'NIK ini sudah memiliki akun terdaftar!');
                    redirect('auth/register');
                }
            }

            // Insert user
            $hashedPassword = hashPassword($password);
            $stmt = $this->db->prepare("INSERT INTO users (id_warga, username, password, nama, email, no_hp, role, is_active) VALUES (?, ?, ?, ?, ?, ?, 'warga', 0)");
            $stmt->execute([$id_warga, $username, $hashedPassword, $nama, $email, $no_hp]);

            // Kirim notifikasi ke admin/ketua RT
            $adminStmt = $this->db->query("SELECT id FROM users WHERE role IN ('admin', 'ketua_rt')");
            $admins = $adminStmt->fetchAll(PDO::FETCH_COLUMN);
            foreach ($admins as $adminId) {
                $notifStmt = $this->db->prepare("INSERT INTO notifikasi (id_user, judul, isi, jenis, link) VALUES (?, 'Registrasi Warga Baru', CONCAT('Warga baru mendaftar: ', ?), 'System', '/pengaturan')");
                $notifStmt->execute([$adminId, $nama]);
            }

            logActivity('Registrasi baru: ' . $username, 'Auth');
            setFlash('success', 'Registrasi berhasil! Akun Anda sedang menunggu persetujuan Admin.');
            redirect('auth/login');
        }

        redirect('auth/register');
    }

    /**
     * Profile Page
     */
    public function profile() {
        requireLogin();

        $userId = $_SESSION['user_id'];
        $stmt = $this->db->prepare("
            SELECT u.*, w.nik, w.no_kk 
            FROM users u
            LEFT JOIN warga w ON u.id_warga = w.id
            WHERE u.id = ?
        ");
        $stmt->execute([$userId]);
        $user = $stmt->fetch();

        $pageTitle = 'Profil Saya';
        $breadcrumb = '<ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="' . nginx_url('') . '">Home</a></li>
            <li class="breadcrumb-item active">Profil</li>
        </ol>';

        ob_start();
        include VIEWSPATH . 'auth/profile.php';
        $content = ob_get_clean();

        include LAYOUTS . 'main_layout.php';
    }

    /**
     * Process Profile Update
     */
    public function proses_profile() {
        requireLogin();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!validateCSRFToken($_POST['csrf_token'] ?? '')) {
                setFlash('error', 'Token tidak valid!');
                redirect('auth/profile');
            }

            $userId = $_SESSION['user_id'];
            $nama = sanitize($_POST['nama'] ?? '');
            $email = sanitize($_POST['email'] ?? '');
            $no_hp = sanitize($_POST['no_hp'] ?? '');
            $password_lama = $_POST['password_lama'] ?? '';
            $password_baru = $_POST['password_baru'] ?? '';

            if (empty($nama)) {
                setFlash('error', 'Nama tidak boleh kosong!');
                redirect('auth/profile');
            }

            // Get current photo
            $stmt = $this->db->prepare("SELECT foto FROM users WHERE id = ?");
            $stmt->execute([$userId]);
            $currentUser = $stmt->fetch();
            $fotoName = $currentUser['foto'] ?? 'default.png';

            // Handle foto upload
            if (!empty($_FILES['foto']['name'])) {
                $upload = uploadFile($_FILES['foto'], UPLOAD_WARGA, ['jpg', 'jpeg', 'png']);
                if ($upload['success']) {
                    if ($fotoName !== 'default.png') {
                        deleteFile(UPLOAD_WARGA . $fotoName);
                    }
                    $fotoName = $upload['filename'];
                } else {
                    setFlash('error', $upload['message']);
                    redirect('auth/profile');
                }
            }

            // Update basic info
            $stmt = $this->db->prepare("UPDATE users SET nama = ?, email = ?, no_hp = ?, foto = ? WHERE id = ?");
            $stmt->execute([$nama, $email, $no_hp, $fotoName, $userId]);

            if ($_SESSION['role'] === 'warga' && !empty($_SESSION['id_warga'])) {
                $stmt = $this->db->prepare("UPDATE warga SET foto = ? WHERE id = ?");
                $stmt->execute([$fotoName, $_SESSION['id_warga']]);
            }

            // Change password if provided
            if (!empty($password_lama) && !empty($password_baru)) {
                $stmt = $this->db->prepare("SELECT password FROM users WHERE id = ?");
                $stmt->execute([$userId]);
                $user = $stmt->fetch();

                if (!verifyPassword($password_lama, $user['password'])) {
                    setFlash('error', 'Password lama salah!');
                    redirect('auth/profile');
                }

                $stmt = $this->db->prepare("UPDATE users SET password = ? WHERE id = ?");
                $stmt->execute([hashPassword($password_baru), $userId]);
                setFlash('success', 'Profil dan password berhasil diupdate!');
            } else {
                setFlash('success', 'Profil berhasil diupdate!');
            }

            // Update session
            $_SESSION['nama'] = $nama;
            $_SESSION['foto'] = $fotoName;

            logActivity('Update profil', 'Auth');
            redirect('auth/profile');
        }

        redirect('auth/profile');
    }
}