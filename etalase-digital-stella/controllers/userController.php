<?php
// Manajemen User - Super Admin only
class UserController
{
    private const ROLES = ['super_admin', 'admin', 'siswa'];

    public static function index(): void
    {
        $users = db()->query('SELECT id, nama, email, role FROM users ORDER BY id')->fetchAll();
        view('users/index', ['title' => 'Kelola User', 'users' => $users]);
    }

    public static function showCreate(): void
    {
        view('users/form', ['title' => 'Tambah User', 'target' => null, 'roles' => self::ROLES]);
    }

    public static function create(): void
    {
        $nama = trim($_POST['nama'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $role = $_POST['role'] ?? '';

        if ($nama === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($password) < 6 || !in_array($role, self::ROLES, true)) {
            flash('error', 'Data tidak valid (password minimal 6 karakter).');
            redirect('/users/new');
        }
        $cek = db()->prepare('SELECT id FROM users WHERE email = ?');
        $cek->execute([$email]);
        if ($cek->fetch()) { flash('error', 'Email sudah terdaftar.'); redirect('/users/new'); }

        db()->prepare('INSERT INTO users (nama, email, password, role) VALUES (?,?,?,?)')
            ->execute([$nama, $email, password_hash($password, PASSWORD_DEFAULT), $role]);

        flash('success', 'User berhasil ditambahkan.');
        redirect('/users');
    }

    private static function ambil($id): array
    {
        $stmt = db()->prepare('SELECT id, nama, email, role FROM users WHERE id = ?');
        $stmt->execute([$id]);
        $u = $stmt->fetch();
        if (!$u) abort(404);
        return $u;
    }

    public static function showEdit($id): void
    {
        view('users/form', ['title' => 'Edit User', 'target' => self::ambil($id), 'roles' => self::ROLES]);
    }

    public static function update($id): void
    {
        $target = self::ambil($id);
        $nama = trim($_POST['nama'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $role = $_POST['role'] ?? '';
        $back = '/users/' . (int) $id . '/edit';

        if ($nama === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || !in_array($role, self::ROLES, true)) {
            flash('error', 'Data tidak valid.'); redirect($back);
        }
        if ((int) $id === auth_user()['id'] && $role !== auth_user()['role']) {
            flash('error', 'Anda tidak dapat mengubah role akun Anda sendiri.'); redirect($back);
        }
        $dup = db()->prepare('SELECT id FROM users WHERE email = ? AND id <> ?');
        $dup->execute([$email, $id]);
        if ($dup->fetch()) { flash('error', 'Email sudah dipakai user lain.'); redirect($back); }

        if ($password !== '') {
            if (strlen($password) < 6) { flash('error', 'Password minimal 6 karakter.'); redirect($back); }
            db()->prepare('UPDATE users SET nama=?, email=?, role=?, password=? WHERE id=?')
                ->execute([$nama, $email, $role, password_hash($password, PASSWORD_DEFAULT), $id]);
        } else {
            db()->prepare('UPDATE users SET nama=?, email=?, role=? WHERE id=?')
                ->execute([$nama, $email, $role, $id]);
        }

        // Jika yang diedit adalah akun sendiri, segarkan data session
        if ((int) $id === auth_user()['id']) {
            $_SESSION['user']['nama'] = $nama;
            $_SESSION['user']['email'] = $email;
        }
        flash('success', 'User berhasil diperbarui.');
        redirect('/users');
    }

    public static function destroy($id): void
    {
        $target = self::ambil($id);
        if ((int) $id === auth_user()['id']) {
            flash('error', 'Anda tidak dapat menghapus akun sendiri.'); redirect('/users');
        }
        $files = db()->prepare('SELECT gambar FROM projects WHERE user_id = ?');
        $files->execute([$id]);
        $rows = $files->fetchAll();

        db()->prepare('DELETE FROM users WHERE id = ?')->execute([$target['id']]); // projects ikut terhapus (CASCADE)
        foreach ($rows as $r) hapus_file($r['gambar']);

        flash('success', 'User berhasil dihapus.');
        redirect('/users');
    }
}
