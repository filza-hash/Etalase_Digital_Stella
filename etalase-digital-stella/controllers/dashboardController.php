<?php
class DashboardController
{
    // Statistik ringkas untuk Admin & Super Admin
    public static function index(): void
    {
        $pdo = db();
        $angka = function (string $sql) use ($pdo): int {
            return (int) $pdo->query($sql)->fetchColumn();
        };

        $stat = [
            'karya'     => $angka('SELECT COUNT(*) FROM projects'),
            'pengguna'  => $angka('SELECT COUNT(*) FROM users'),
            'siswa'     => $angka("SELECT COUNT(*) FROM users WHERE role = 'siswa'"),
            'apresiasi' => $angka('SELECT COUNT(*) FROM apresiasi'),
            'unggulan'  => $angka('SELECT COUNT(*) FROM projects WHERE unggulan = 1'),
        ];

        $kategori = $pdo->query('SELECT kategori, COUNT(*) AS jml FROM projects GROUP BY kategori ORDER BY jml DESC, kategori ASC')->fetchAll();

        $terbaru = $pdo->query(
            'SELECT p.id, p.nama_project, p.gambar, p.created_at, p.user_id, u.nama AS pemilik'
            . ' FROM projects p JOIN users u ON u.id = p.user_id ORDER BY p.created_at DESC, p.id DESC LIMIT 5'
        )->fetchAll();

        $populer = $pdo->query(
            'SELECT p.id, p.nama_project, p.gambar, p.user_id, u.nama AS pemilik, COUNT(a.id) AS jml'
            . ' FROM projects p JOIN users u ON u.id = p.user_id JOIN apresiasi a ON a.project_id = p.id'
            . ' GROUP BY p.id, p.nama_project, p.gambar, p.user_id, u.nama ORDER BY jml DESC, p.id DESC LIMIT 5'
        )->fetchAll();

        $aktif = $pdo->query(
            'SELECT u.id, u.nama, u.role, COUNT(p.id) AS jml'
            . ' FROM users u JOIN projects p ON p.user_id = u.id GROUP BY u.id, u.nama, u.role ORDER BY jml DESC, u.nama ASC LIMIT 5'
        )->fetchAll();

        view('dashboard/index', [
            'title' => 'Dasbor', 'stat' => $stat, 'kategori' => $kategori,
            'terbaru' => $terbaru, 'populer' => $populer, 'aktif' => $aktif,
        ]);
    }
}
