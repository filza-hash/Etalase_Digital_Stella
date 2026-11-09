<?php
// Jalankan sekali:  php database/setup.php
// (atau lewat browser: http://localhost/etalase-digital-stella/database/setup.php)
// Membuat database, tabel, dan 3 akun uji (password: password123)
//
// CATATAN: folder database/ diblokir oleh .htaccess. Kalau ingin lewat browser,
// hapus sementara file database/.htaccess, lalu kembalikan setelah selesai.
require __DIR__ . '/../config/database.php';
header('Content-Type: text/plain; charset=utf-8');

try {
    $pdo = new PDO('mysql:host=' . DB_HOST . ';port=' . DB_PORT . ';charset=utf8mb4', DB_USER, DB_PASS,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

    $sql = str_replace('etalase_stella', DB_NAME, file_get_contents(__DIR__ . '/schema.sql'));
    $pdo->exec($sql);

    $hash = password_hash('password123', PASSWORD_DEFAULT);
    $stmt = $pdo->prepare('INSERT IGNORE INTO users (nama, email, password, role) VALUES (?,?,?,?)');
    foreach ([
        ['Super Admin', 'superadmin@stella.sch.id', 'super_admin'],
        ['Admin Stella', 'admin@stella.sch.id', 'admin'],
        ['Siswa Stella', 'siswa@stella.sch.id', 'siswa'],
    ] as [$nama, $email, $role]) {
        $stmt->execute([$nama, $email, $hash, $role]);
    }
    echo "OK: Database & akun uji siap. Password semua akun: password123\n";
} catch (PDOException $e) {
    http_response_code(500);
    echo 'GAGAL: ' . $e->getMessage() . "\n";
    exit(1);
}
