<?php
// Entry Point Aplikasi (setara app.js)

// Dukungan `php -S localhost:8000 index.php`: file statis dilayani langsung
if (php_sapi_name() === 'cli-server') {
    $f = __DIR__ . parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    if (is_file($f) && basename($f) !== 'index.php') return false;
}

session_start();

require __DIR__ . '/config/database.php';
require __DIR__ . '/config/helpers.php';
require __DIR__ . '/config/Router.php';
require __DIR__ . '/middlewares/authMiddleware.php';
require __DIR__ . '/middlewares/roleMiddleware.php';
require __DIR__ . '/middlewares/uploadMiddleware.php';
require __DIR__ . '/controllers/authController.php';
require __DIR__ . '/controllers/projectController.php';
require __DIR__ . '/controllers/userController.php';
require __DIR__ . '/controllers/profilController.php';
require __DIR__ . '/controllers/dashboardController.php';
require __DIR__ . '/routes/authRoutes.php';
require __DIR__ . '/routes/projectRoutes.php';
require __DIR__ . '/routes/userRoutes.php';
require __DIR__ . '/routes/profilRoutes.php';
require __DIR__ . '/routes/dashboardRoutes.php';

try {
    ensure_schema();   // menambah kolom/tabel fitur baru bila belum ada
    Router::dispatch();
} catch (Throwable $e) {
    error_log($e);
    http_response_code(500);
    echo 'Terjadi kesalahan server. Pastikan database sudah dibuat (jalankan database/setup.php) dan konfigurasi config/database.php benar.';
    // Hanya saat dibuka dari komputer sendiri (localhost): tampilkan penyebab aslinya
    if (in_array($_SERVER['REMOTE_ADDR'] ?? '', ['127.0.0.1', '::1'], true)) {
        echo '<pre style="margin-top:1rem;padding:1rem;background:#fff1f1;border:1px solid #f3b5b5;border-radius:8px;white-space:pre-wrap">'
            . htmlspecialchars(get_class($e) . ': ' . $e->getMessage() . "\n\n" . $e->getFile() . ' (baris ' . $e->getLine() . ')')
            . '</pre>';
    }
}
