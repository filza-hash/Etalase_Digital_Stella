<?php
// Fungsi-fungsi bantu global

function base_path(): string
{
    $dir = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])), '/');
    return $dir === '.' ? '' : $dir;
}

function url(string $path = ''): string
{
    return base_path() . '/' . ltrim($path, '/');
}

function redirect(string $path): void
{
    header('Location: ' . url($path));
    exit;
}

function e($v): string
{
    return htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8');
}

function flash(string $type, string $message): void
{
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function get_flash(): ?array
{
    $f = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    return $f;
}

function view(string $name, array $data = []): void
{
    extract($data);
    require __DIR__ . '/../views/' . $name . '.php';
}

function abort(int $code): void
{
    http_response_code($code);
    view((string) $code, ['title' => $code === 403 ? 'Akses Ditolak' : 'Tidak Ditemukan']);
    exit;
}

function auth_user(): ?array
{
    return $_SESSION['user'] ?? null;
}

function is_admin(array $user): bool
{
    return in_array($user['role'], ['admin', 'super_admin'], true);
}

// Siswa hanya milik sendiri; Admin & Super Admin semua
function can_modify(array $user, array $project): bool
{
    return is_admin($user) || (int) $project['user_id'] === (int) $user['id'];
}

function role_label(string $role): string
{
    return str_replace('_', ' ', $role);
}

// ---- CSRF ----
function csrf_token(): string
{
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="_csrf" value="' . e(csrf_token()) . '">';
}

function method_field(string $method): string
{
    return '<input type="hidden" name="_method" value="' . e($method) . '">';
}

function verify_csrf(): void
{
    $ok = isset($_POST['_csrf'], $_SESSION['csrf']) && hash_equals($_SESSION['csrf'], $_POST['_csrf']);
    if (!$ok) {
        http_response_code(419);
        exit('Sesi form tidak valid atau kedaluwarsa. Kembali dan muat ulang halaman. (Pastikan ukuran file upload tidak melebihi batas post_max_size PHP.)');
    }
}

function hapus_file(?string $nama): void
{
    if (!$nama) return;
    $p = __DIR__ . '/../uploads/' . basename($nama);
    if (is_file($p)) @unlink($p);
}

// ---- Pembantu tampilan (hanya untuk views; tidak menyentuh logika data) ----

// Inisial dari nama, mis. "Super Admin Stella" -> "SA"
function initials(string $nama): string
{
    $parts = preg_split('/\s+/', trim($nama)) ?: [];
    $out = '';
    foreach (array_slice($parts, 0, 2) as $p) {
        $out .= mb_strtoupper(mb_substr($p, 0, 1));
    }
    return $out !== '' ? $out : '?';
}

// Warna avatar yang konsisten untuk tiap nama (0-359)
function avatar_hue(string $nama): int
{
    return abs(crc32(mb_strtolower(trim($nama)))) % 360;
}

function avatar(string $nama, string $extra = ''): string
{
    // pasangan warna dari palet merek (merah & abu-abu)
    $pal = [['#E32636', '#B5122B'], ['#4B5563', '#202124'], ['#F26B76', '#E32636'], ['#9CA3AF', '#4B5563']];
    [$a, $b] = $pal[avatar_hue($nama) % count($pal)];
    return '<span class="avatar ' . e($extra) . '" style="--a:' . $a . ';--b:' . $b . '" aria-hidden="true">' . e(initials($nama)) . '</span>';
}

// Tanggal berbahasa Indonesia, mis. "7 Okt 2026"
function tgl_id(string $datetime): string
{
    $bln = ['', 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
    $t = strtotime($datetime);
    return (int) date('j', $t) . ' ' . $bln[(int) date('n', $t)] . ' ' . date('Y', $t);
}

// Tanggal & jam lengkap, mis. "19 Oktober 2026, 13.31"
function tgl_lengkap(string $datetime): string
{
    $bln = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
    $t = strtotime($datetime);
    return (int) date('j', $t) . ' ' . $bln[(int) date('n', $t)] . ' ' . date('Y', $t) . ', ' . date('H.i', $t);
}

// Rute aktif untuk navbar
function request_path(): string
{
    $p = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
    $base = base_path();
    if ($base !== '' && strpos($p, $base) === 0) $p = substr($p, strlen($base));
    return '/' . trim($p, '/');
}

// Ikon SVG inline (stroke, mengikuti warna teks)
function icon(string $name): string
{
    $paths = [
        'star'   => '<path d="M12 2l2.9 6.6 7.1.6-5.4 4.7 1.6 7-6.2-3.7-6.2 3.7 1.6-7L2 9.2l7.1-.6z" fill="currentColor" stroke="none"/>',
        'search' => '<circle cx="11" cy="11" r="7"/><path d="m21 21-4.3-4.3"/>',
        'plus'   => '<path d="M12 5v14M5 12h14"/>',
        'link'   => '<path d="M7 17 17 7M8 7h9v9"/>',
        'trash'  => '<path d="M3 6h18M8 6V4h8v2M6 6l1 14h10l1-14M10 11v6M14 11v6"/>',
        'check'  => '<path d="m5 12 5 5 9-10"/>',
        'alert'  => '<path d="M12 8v5M12 16.5v.5"/><circle cx="12" cy="12" r="9"/>',
        'eye'    => '<path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7S2 12 2 12z"/><circle cx="12" cy="12" r="3"/>',
        'upload' => '<path d="M12 16V4M7 9l5-5 5 5M4 20h16"/>',
        'back'   => '<path d="M19 12H5M12 19l-7-7 7-7"/>',
        'calendar' => '<rect x="3" y="4" width="18" height="18" rx="3"/><path d="M16 2v4M8 2v4M3 10h18"/>',
        'user'   => '<circle cx="12" cy="8" r="4"/><path d="M4 21c0-4 3.6-7 8-7s8 3 8 7"/>',
        'edit'   => '<path d="M4 20h4L19 9l-4-4L4 16zM13.5 6.5l4 4"/>',
        'star-o' => '<path d="M12 2.8l2.8 6.1 6.7.7-5 4.5 1.4 6.6L12 17.4l-5.9 3.3 1.4-6.6-5-4.5 6.7-.7z"/>',
        'heart'  => '<path d="M20.8 4.6a5.5 5.5 0 0 0-7.8 0L12 5.7l-1-1.1a5.5 5.5 0 0 0-7.8 7.8l1 1L12 21l7.8-7.6 1-1a5.5 5.5 0 0 0 0-7.8z"/>',
        'chart'  => '<path d="M4 20V10M10 20V4M16 20v-7M22 20H2"/>',
        'users'  => '<path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.9M16 3.1a4 4 0 0 1 0 7.8"/>',
        'folder' => '<path d="M3 7a2 2 0 0 1 2-2h4l2 2h8a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>',
        'tag'    => '<path d="M20.6 13.4l-7.2 7.2a2 2 0 0 1-2.8 0L3 13V3h10l7.6 7.6a2 2 0 0 1 0 2.8z"/><circle cx="7.5" cy="7.5" r="1.2"/>',
    ];
    $fill = $name === 'star';
    return '<svg viewBox="0 0 24 24" aria-hidden="true" ' . ($fill ? '' : 'fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"') . '>' . ($paths[$name] ?? '') . '</svg>';
}

// Rasi bintang dekoratif
function constellation(): string
{
    return '<svg class="constellation" viewBox="0 0 300 240" aria-hidden="true" focusable="false">'
        . '<path pathLength="1" d="M30 170 L95 110"/><path pathLength="1" d="M95 110 L165 150"/>'
        . '<path pathLength="1" d="M165 150 L220 70"/><path pathLength="1" d="M220 70 L270 125"/>'
        . '<circle cx="30" cy="170" r="3.2"/><circle cx="95" cy="110" r="4"/><circle cx="165" cy="150" r="3.2"/>'
        . '<circle class="big" cx="220" cy="70" r="6"/><circle cx="270" cy="125" r="3.6"/></svg>';
}

// URL aset dengan penanda versi (otomatis berubah saat file diubah, menghindari cache lama)
function asset(string $path): string
{
    $file = __DIR__ . '/../' . ltrim($path, '/');
    $v = is_file($file) ? filemtime($file) : time();
    return url($path) . '?v=' . $v;
}

// URL logo (assets/img/logo.svg|png|webp|jpg|jpeg) atau null jika belum ada
function logo_url(): ?string
{
    foreach (['svg', 'png', 'webp', 'jpg', 'jpeg'] as $ext) {
        if (is_file(__DIR__ . '/../assets/img/logo.' . $ext)) return asset('assets/img/logo.' . $ext);
    }
    return null;
}

// Tanda merek: logo bila ada, ikon bintang bila belum. $gelap = di atas latar gelap (logo diberi alas putih)
function brand_mark(bool $gelap = false): string
{
    $logo = logo_url();
    if (!$logo) return '<span class="brand-mark">' . icon('star') . '</span>';
    $img = '<img class="brand-logo" src="' . e($logo) . '" alt="Telkom Schools">';
    return $gelap ? '<span class="logo-chip">' . $img . '</span>' : $img . '<span class="brand-sep" aria-hidden="true"></span>';
}

// ================= Fitur: unggulan, apresiasi, profil, dasbor =================

// Daftar kategori karya (ubah di sini bila ingin menambah kategori)
function kategori_list(): array
{
    return ['Website', 'Aplikasi', 'IoT & Robotik', 'Desain Grafis', 'Multimedia', 'Game', 'Lainnya'];
}

// Hanya izinkan tautan http/https (mencegah javascript: dsb.)
function safe_url(?string $u): ?string
{
    $u = trim((string) $u);
    return preg_match('#^https?://#i', $u) ? $u : null;
}

function json_out(array $data, int $code = 200): void
{
    http_response_code($code);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data);
    exit;
}

function is_fetch(): bool
{
    return ($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'fetch';
}

// Kembali ke halaman sebelumnya (hanya di situs ini), atau ke $fallback
function redirect_back(string $fallback): void
{
    $p = parse_url($_SERVER['HTTP_REFERER'] ?? '');
    $host = explode(':', $_SERVER['HTTP_HOST'] ?? '')[0];
    if ($p && isset($p['host']) && strcasecmp($p['host'], $host) === 0) {
        header('Location: ' . ($p['path'] ?? '/') . (isset($p['query']) ? '?' . $p['query'] : ''));
        exit;
    }
    redirect($fallback);
}

// Tombol apresiasi (hati). Milik sendiri: hanya menampilkan jumlah.
function like_button(array $p, array $user, string $extra = ''): string
{
    $n = (int) ($p['jml_apresiasi'] ?? 0);
    $id = (int) $p['id'];
    if ((int) $p['user_id'] === (int) $user['id']) {
        return '<span class="like is-own ' . e($extra) . '" title="Jumlah apresiasi untuk karyamu">' . icon('heart')
            . '<span class="like-n" data-like-total="' . $id . '">' . $n . '</span></span>';
    }
    $on = !empty($p['diapresiasi']);
    return '<form class="inline like-form ' . e($extra) . '" action="' . url('/projects/' . $id . '/apresiasi') . '" method="POST" data-like data-project="' . $id . '">'
        . csrf_field()
        . '<button type="submit" class="like' . ($on ? ' is-on' : '') . '" aria-pressed="' . ($on ? 'true' : 'false') . '" aria-label="Beri apresiasi">'
        . icon('heart') . '<span class="like-n" data-like-total="' . $id . '">' . $n . '</span></button></form>';
}

// Pembaruan skema otomatis (aman dijalankan berulang): kolom & tabel untuk fitur baru.
// Tipe kolom id dibaca dari database yang sudah ada agar foreign key selalu cocok.
function ensure_schema(): void
{
    if (($_SESSION['schema_v'] ?? 0) >= 2) return;
    $pdo = db();

    $tipe = function (string $tabel, string $kolom) use ($pdo) {
        $st = $pdo->prepare('SELECT COLUMN_TYPE FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ?');
        $st->execute([$tabel, $kolom]);
        $r = $st->fetchColumn();
        return $r === false ? null : (string) $r;
    };

    $tambah = [
        ['projects', 'unggulan', 'TINYINT(1) NOT NULL DEFAULT 0'],
        ['projects', 'kategori', "VARCHAR(40) NOT NULL DEFAULT 'Lainnya'"],
        ['users', 'kelas', 'VARCHAR(60) NULL'],
        ['users', 'bio', 'TEXT NULL'],
        ['users', 'tautan', 'VARCHAR(255) NULL'],
    ];
    foreach ($tambah as [$tabel, $kolom, $def]) {
        if ($tipe($tabel, $kolom) === null) {
            $pdo->exec('ALTER TABLE `' . $tabel . '` ADD COLUMN `' . $kolom . '` ' . $def);
        }
    }

    $pid = $tipe('projects', 'id');
    $uid = $tipe('users', 'id');
    if ($pid !== null && $uid !== null) {
        $kolomTabel = 'id INT UNSIGNED NOT NULL AUTO_INCREMENT, project_id ' . $pid . ' NOT NULL, user_id ' . $uid . ' NOT NULL, '
            . 'created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, PRIMARY KEY (id), '
            . 'UNIQUE KEY uq_apresiasi (project_id, user_id), KEY idx_apresiasi_user (user_id)';
        $fk = ', CONSTRAINT fk_apresiasi_project FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE'
            . ', CONSTRAINT fk_apresiasi_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE';
        try {
            $pdo->exec('CREATE TABLE IF NOT EXISTS apresiasi (' . $kolomTabel . $fk . ') ENGINE=InnoDB DEFAULT CHARSET=utf8mb4');
        } catch (PDOException $e) {
            // Cadangan: tanpa foreign key (mis. tabel induk bukan InnoDB)
            $pdo->exec('CREATE TABLE IF NOT EXISTS apresiasi (' . $kolomTabel . ') DEFAULT CHARSET=utf8mb4');
        }
    }
    $_SESSION['schema_v'] = 2;
}
