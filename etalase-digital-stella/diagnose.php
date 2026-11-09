<?php
// Halaman diagnosa SEMENTARA. Buka: http://localhost/etalase-digital-stella/diagnose.php
// HAPUS file ini setelah masalah selesai.
if (!in_array($_SERVER['REMOTE_ADDR'] ?? '', ['127.0.0.1', '::1'], true)) { http_response_code(403); exit('Hanya bisa dibuka dari localhost.'); }
error_reporting(E_ALL);
ini_set('display_errors', '1');

$rows = [];
function cek($judul, $ok, $detail = '') {
    global $rows;
    $rows[] = [$ok, $judul, $detail];
}
function rapi($e) {
    return get_class($e) . ': ' . $e->getMessage() . "\n" . str_replace('\\', '/', $e->getFile()) . ' (baris ' . $e->getLine() . ')';
}

$root = __DIR__;

// 1. Lingkungan PHP
cek('Versi PHP', version_compare(PHP_VERSION, '7.4', '>='), PHP_VERSION);
foreach (['pdo_mysql', 'mbstring'] as $ext) cek("Ekstensi $ext aktif", extension_loaded($ext));

// 2. File penting ada semua
$wajib = [
    'index.php', 'config/database.php', 'config/helpers.php', 'config/Router.php',
    'controllers/projectController.php', 'routes/projectRoutes.php',
    'views/partials/header.php', 'views/partials/footer.php',
    'views/projects/index.php', 'views/projects/show.php', 'views/projects/form.php',
    'views/login.php', 'views/register.php', 'views/users/index.php', 'views/users/form.php',
    'assets/css/base.css', 'assets/css/layout.css', 'assets/css/components.css', 'assets/css/pages.css',
    'assets/js/app.js', 'assets/img/logo.png',
];
$hilang = array();
foreach ($wajib as $f) { if (!is_file($root . '/' . $f)) $hilang[] = $f; }
cek('Semua file proyek lengkap', !$hilang, $hilang ? "Belum ada:\n" . implode("\n", $hilang) : count($wajib) . ' file diperiksa');

// 3. Cek sintaks seluruh file .php (menangkap salah ketik)
$salah = [];
$it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root, FilesystemIterator::SKIP_DOTS));
foreach ($it as $f) {
    $p = str_replace('\\', '/', $f->getPathname());
    if (substr($p, -4) !== '.php' || strpos($p, '/uploads/') !== false || basename($p) === 'diagnose.php') continue;
    try { token_get_all(file_get_contents($p), TOKEN_PARSE); }
    catch (ParseError $e) { $salah[] = substr($p, strlen(str_replace('\\', '/', $root)) + 1) . ' baris ' . $e->getLine() . ': ' . $e->getMessage(); }
}
cek('Tidak ada salah sintaks di file PHP', !$salah, implode("\n", $salah));

// 4. Fungsi bantu versi terbaru ada
try {
    require_once "$root/config/database.php";
    require_once "$root/config/helpers.php";
    $fn = ['asset', 'logo_url', 'brand_mark', 'icon', 'avatar', 'initials', 'tgl_id', 'tgl_lengkap', 'request_path', 'constellation'];
    $kurang = array();
    foreach ($fn as $n) { if (!function_exists($n)) $kurang[] = $n; }
    cek('config/helpers.php versi terbaru', !$kurang, $kurang ? "Fungsi belum ada: " . implode(', ', $kurang) . "\n=> helpers.php masih versi lama, timpa dengan yang baru." : 'semua fungsi ada');
} catch (Throwable $e) {
    cek('Memuat config/helpers.php', false, rapi($e));
}

// 5. Database
$pdo = null;
try {
    $pdo = db();
    cek('Koneksi database "' . DB_NAME . '"', true, 'tersambung');
} catch (Throwable $e) {
    cek('Koneksi database "' . (defined('DB_NAME') ? DB_NAME : '?') . '"', false, rapi($e) . "\n=> Pastikan MySQL di XAMPP menyala dan database sudah dibuat (database/setup.php).");
}
$projects = [];
if ($pdo) {
    foreach (['users' => ['id', 'nama', 'email', 'role'], 'projects' => ['id', 'user_id', 'nama_project', 'deskripsi', 'gambar', 'link_project', 'created_at']] as $tb => $cols) {
        try {
            $ada = array_column($pdo->query("SHOW COLUMNS FROM `$tb`")->fetchAll(), 'Field');
            $kurang = array_diff($cols, $ada);
            cek("Tabel $tb", !$kurang, $kurang ? 'Kolom belum ada: ' . implode(', ', $kurang) : 'kolom: ' . implode(', ', $ada));
        } catch (Throwable $e) { cek("Tabel $tb", false, rapi($e)); }
    }
    try {
        $projects = $pdo->query('SELECT p.*, u.nama AS pemilik FROM projects p JOIN users u ON u.id = p.user_id ORDER BY p.created_at DESC, p.id DESC')->fetchAll();
        cek('Query etalase', true, count($projects) . ' karya');
    } catch (Throwable $e) { cek('Query etalase', false, rapi($e)); }
}

// 6. Render halaman sungguhan (tanpa menyentuh sesi atau data)
if (function_exists('view')) {
    $_SESSION = ['user' => ['id' => 0, 'nama' => 'Diagnosa', 'role' => 'super_admin']];
    $uji = [
        'Halaman etalase' => ['projects/index', ['title' => 'Etalase Digital', 'projects' => $projects, 'q' => '']],
        'Halaman login'   => ['login', ['title' => 'Login']],
        'Halaman daftar'  => ['register', ['title' => 'Daftar']],
        'Form tambah'     => ['projects/form', ['title' => 'Tambah Karya', 'project' => null]],
    ];
    if ($projects) $uji['Halaman detail karya'] = ['projects/show', ['title' => 'Detail', 'project' => $projects[0]]];
    foreach ($uji as $nama => [$v, $data]) {
        $level = ob_get_level();
        ob_start();
        try { view($v, $data); ob_end_clean(); cek("Render: $nama", true); }
        catch (Throwable $e) { while (ob_get_level() > $level) ob_end_clean(); cek("Render: $nama", false, rapi($e)); }
    }
}

$gagal = 0;
foreach ($rows as $r) { if (!$r[0]) $gagal++; }
?><!DOCTYPE html>
<html lang="id"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Diagnosa Etalase Stella</title>
<style>
body{font:15px/1.5 system-ui,Segoe UI,sans-serif;max-width:860px;margin:2rem auto;padding:0 1rem;color:#202124;background:#fff6f6}
h1{margin:0 0 .3rem}.sum{padding:.8rem 1rem;border-radius:10px;font-weight:700;margin:1rem 0}
.sum.ok{background:#e6f6ec;color:#168a45}.sum.bad{background:#fdecee;color:#b5122b}
.r{display:flex;gap:.7rem;padding:.7rem 1rem;background:#fff;border:1px solid #e5e7eb;border-radius:10px;margin-bottom:.5rem}
.r b{flex:none;width:1.4rem;text-align:center}.ok b{color:#168a45}.bad b{color:#b5122b}.bad{border-color:#f3b5b5}
pre{margin:.3rem 0 0;white-space:pre-wrap;word-break:break-word;font:13px/1.5 Consolas,monospace;color:#4b5563}
small{color:#6b7280}
</style></head><body>
<h1>Diagnosa Etalase Stella</h1>
<small>Halaman sementara. Hapus <code>diagnose.php</code> setelah selesai.</small>
<div class="sum <?= $gagal ? 'bad' : 'ok' ?>"><?= $gagal ? "$gagal pemeriksaan gagal. Lihat yang bertanda ✗ di bawah." : 'Semua pemeriksaan lolos. Coba buka /projects lagi.' ?></div>
<?php foreach ($rows as [$ok, $judul, $detail]): ?>
<div class="r <?= $ok ? 'ok' : 'bad' ?>"><b><?= $ok ? '✓' : '✗' ?></b><div><?= htmlspecialchars($judul) ?><?php if ($detail !== ''): ?><pre><?= htmlspecialchars($detail) ?></pre><?php endif; ?></div></div>
<?php endforeach; ?>
</body></html>
