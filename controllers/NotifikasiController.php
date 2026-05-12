<?php
/**
 * Notifikasi Controller
 * SI-WargaRT - Sistem Informasi Warga RT
 */

class NotifikasiController {

    private $db;

    public function __construct() {
        requireLogin();
        $this->db = getDb();
    }

    /**
     * Index - List Notifikasi
     */
    public function index() {
        $user = getCurrentUser();
        $userId = $user['id'];

        $pageTitle = 'Notifikasi';
        $breadcrumb = '<ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="' . nginx_url('dashboard') . '">Home</a></li>
            <li class="breadcrumb-item active">Notifikasi</li>
        </ol>';

        $stmt = $this->db->prepare("
            SELECT * FROM notifikasi
            WHERE id_user = ?
            ORDER BY created_at DESC
            LIMIT 100
        ");
        $stmt->execute([$userId]);
        $notifikasi = $stmt->fetchAll();

        // Mark as read
        $stmt = $this->db->prepare("UPDATE notifikasi SET is_read = 1 WHERE id_user = ?");
        $stmt->execute([$userId]);

        ob_start();
        include VIEWSPATH . 'notifikasi/index.php';
        $content = ob_get_clean();

        include LAYOUTS . 'main_layout.php';
    }

    /**
     * Mark as Read (AJAX)
     */
    public function read() {
        $id = (int)($_POST['id'] ?? 0);
        $userId = $_SESSION['user_id'];

        $stmt = $this->db->prepare("UPDATE notifikasi SET is_read = 1 WHERE id = ? AND id_user = ?");
        $stmt->execute([$id, $userId]);

        jsonResponse(true, 'Notifikasi ditandai sudah dibaca');
    }

    /**
     * Delete All
     */
    public function clear() {
        $userId = $_SESSION['user_id'];

        $stmt = $this->db->prepare("DELETE FROM notifikasi WHERE id_user = ?");
        $stmt->execute([$userId]);

        setFlash('success', 'Semua notifikasi berhasil dihapus!');
        redirect('/notifikasi');
    }
}