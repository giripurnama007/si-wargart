<?php
/**
 * Dashboard Controller
 * SI-WargaRT - Sistem Informasi Warga RT
 */

class DashboardController {

    private $db;

    public function __construct() {
        requireLogin();
        $this->db = getDb();
    }

    /**
     * Dashboard Index
     */
    public function index() {
        $user = getCurrentUser();

        // Stats
        $stats = $this->getStats();

        // Forum stats
        //$forumStats = $this->getForumStats();

        // Recent activity
        $recentWarga = $this->getRecentWarga();
        $recentPayments = $this->getRecentPayments();
        $recentPengaduan = $this->getRecentPengaduan();
        $pengumumanTerbaru = $this->getPengumumanTerbaru();
        $kegiatanAkanDatang = $this->getKegiatanAkanDatang();
       // $recentForum = $this->getRecentForum();

        // Chart data
        $chartData = $this->getChartData();

        $pageTitle = 'Dashboard';
        $breadcrumb = '<ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="' . nginx_url('dashboard') . '">Home</a></li>
            <li class="breadcrumb-item active">Dashboard</li>
        </ol>';

        ob_start();
        include VIEWSPATH . 'dashboard/index.php';
        $content = ob_get_clean();

        include LAYOUTS . 'main_layout.php';
    }

    private function getStats() {
        $stats = [];

        // Total Warga
        $stmt = $this->db->query("SELECT COUNT(*) as total FROM warga WHERE status_warga = 'Aktif'");
        $stats['total_warga'] = $stmt->fetch()['total'] ?? 0;

        // Total KK
        $stmt = $this->db->query("SELECT COUNT(*) as total FROM kartu_keluarga");
        $stats['total_kk'] = $stmt->fetch()['total'] ?? 0;

        // Total Iuran Masuk (bulan ini)
        $currentMonth = date('Y-m');
        $stmt = $this->db->prepare("
            SELECT COALESCE(SUM(p.jumlah_bayar), 0) as total
            FROM pembayaran p
            INNER JOIN tagihan_iuran t ON p.id_tagihan = t.id
            WHERE t.bulan = ? AND p.status = 'Verified'
        ");
        $stmt->execute([$currentMonth]);
        $stats['iuran_bulan_ini'] = $stmt->fetch()['total'] ?? 0;

        // Total Tunggakan
        $stmt = $this->db->query("SELECT COALESCE(SUM(jumlah), 0) as total FROM tagihan_iuran WHERE status = 'Menunggak'");
        $stats['total_tunggakan'] = $stmt->fetch()['total'] ?? 0;

        // Total Forum Topik
        $stmt = $this->db->query("SELECT COUNT(*) as total FROM forum_topik WHERE status = 'Published'");
        $stats['total_forum'] = $stmt->fetch()['total'] ?? 0;

        // Total Komentar Forum
        $stmt = $this->db->query("SELECT COUNT(*) as total FROM forum_komentar");
        $stats['total_komentar'] = $stmt->fetch()['total'] ?? 0;

        // Tagihan Pending
        $stmt = $this->db->query("SELECT COUNT(*) as total FROM tagihan_iuran WHERE status = 'Pending'");
        $stats['tagihan_pending'] = $stmt->fetch()['total'] ?? 0;

        // Pengaduan Aktif
        $stmt = $this->db->query("SELECT COUNT(*) as total FROM pengaduan WHERE status IN ('Dikirim', 'Diproses')");
        $stats['pengaduan_aktif'] = $stmt->fetch()['total'] ?? 0;

        return $stats;
    }

    private function getRecentWarga($limit = 5) {
        $stmt = $this->db->query("SELECT * FROM warga ORDER BY created_at DESC LIMIT $limit");
        return $stmt->fetchAll();
    }

    private function getRecentPayments($limit = 5) {
        $stmt = $this->db->query("
            SELECT p.*, w.nama_lengkap, t.bulan, j.nama_iuran
            FROM pembayaran p
            INNER JOIN warga w ON p.id_warga = w.id
            INNER JOIN tagihan_iuran t ON p.id_tagihan = t.id
            INNER JOIN jenis_iuran j ON t.id_jenis_iuran = j.id
            ORDER BY p.created_at DESC
            LIMIT $limit
        ");
        return $stmt->fetchAll();
    }

    private function getRecentPengaduan($limit = 5) {
        $stmt = $this->db->query("
            SELECT p.*, w.nama_lengkap
            FROM pengaduan p
            INNER JOIN warga w ON p.id_warga = w.id
            ORDER BY p.created_at DESC
            LIMIT $limit
        ");
        return $stmt->fetchAll();
    }

    private function getPengumumanTerbaru($limit = 3) {
        $stmt = $this->db->query("
            SELECT * FROM pengumuman
            WHERE status = 'Published'
            ORDER BY tanggal DESC
            LIMIT $limit
        ");
        return $stmt->fetchAll();
    }

    private function getKegiatanAkanDatang($limit = 3) {
        $stmt = $this->db->query("
            SELECT * FROM kegiatan
            WHERE status = 'Akan Datang' AND tanggal >= CURDATE()
            ORDER BY tanggal ASC
            LIMIT $limit
        ");
        return $stmt->fetchAll();
    }

    private function getChartData() {
        // Chart kas 6 bulan terakhir
        $kasMasuk = [];
        $kasKeluar = [];
        $bulanLabels = [];

        for ($i = 5; $i >= 0; $i--) {
            $month = date('Y-m', strtotime("-$i months"));
            $bulanLabels[] = getBulanIndonesia(substr($month, 5, 2)) . ' ' . substr($month, 0, 4);

            $stmt = $this->db->prepare("SELECT COALESCE(SUM(jumlah), 0) as total FROM kas_rt WHERE jenis = 'Masuk' AND DATE_FORMAT(tanggal, '%Y-%m') = ?");
            $stmt->execute([$month]);
            $kasMasuk[] = (float)$stmt->fetch()['total'];

            $stmt = $this->db->prepare("SELECT COALESCE(SUM(jumlah), 0) as total FROM kas_rt WHERE jenis = 'Keluar' AND DATE_FORMAT(tanggal, '%Y-%m') = ?");
            $stmt->execute([$month]);
            $kasKeluar[] = (float)$stmt->fetch()['total'];
        }

        // Chart pie status iuran
        $stmt = $this->db->query("
            SELECT status, COUNT(*) as total
            FROM tagihan_iuran
            GROUP BY status
        ");
        $statusIuran = $stmt->fetchAll();

        // Stats by jenis iuran
        $stmt = $this->db->query("
            SELECT j.jenis, COALESCE(SUM(p.jumlah_bayar), 0) as total
            FROM jenis_iuran j
            LEFT JOIN tagihan_iuran t ON j.id = t.id_jenis_iuran
            LEFT JOIN pembayaran p ON t.id = p.id_tagihan AND p.status = 'Verified'
            GROUP BY j.jenis
        ");
        $iuranByJenis = $stmt->fetchAll();

        return [
            'kasMasuk' => $kasMasuk,
            'kasKeluar' => $kasKeluar,
            'bulanLabels' => $bulanLabels,
            'statusIuran' => $statusIuran,
            'iuranByJenis' => $iuranByJenis
        ];
    }
}