<?php
class AuthController
{
    public static function showLogin(): void    { view('login', ['title' => 'Login']); }
    public static function showRegister(): void { view('register', ['title' => 'Daftar Siswa']); }

    public static function login(): void
    {
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        $stmt = db()->prepare('SELECT * FROM users WHERE email = ?');
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if (!$user || !password_verify($password, $user['password'])) {
            flash('error', 'Email atau password salah.');
            redirect('/login');
        }

        session_regenerate_id(true);
        $_SESSION['user'] = [
            'id' => (int) $user['id'], 'nama' => $user['nama'],
            'email' => $user['email'], 'role' => $user['role'],
        ];
        flash('success', 'Selamat datang, ' . $user['nama'] . '!');
        redirect('/projects');
    }

    // Register khusus role Siswa (role dipaksa 'siswa' di server)
    public static function register(): void
    {
        $nama = trim($_POST['nama'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if ($nama === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($password) < 6) {
            flash('error', 'Isi semua field dengan benar. Password minimal 6 karakter.');
            redirect('/register');
        }
        $cek = db()->prepare('SELECT id FROM users WHERE email = ?');
        $cek->execute([$email]);
        if ($cek->fetch()) {
            flash('error', 'Email sudah terdaftar.');
            redirect('/register');
        }

        db()->prepare('INSERT INTO users (nama, email, password, role) VALUES (?,?,?,?)')
            ->execute([$nama, $email, password_hash($password, PASSWORD_DEFAULT), 'siswa']);

        flash('success', 'Pendaftaran berhasil. Silakan login.');
        redirect('/login');
    }

    public static function logout(): void
    {
        $_SESSION = [];
        session_destroy();
        session_start();
        flash('success', 'Anda telah keluar.');
        redirect('/login');
    }
}
