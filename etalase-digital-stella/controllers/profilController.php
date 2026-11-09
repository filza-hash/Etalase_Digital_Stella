<?php
class ProfilController
{
    // Halaman profil + daftar karya milik siswa tersebut
    public static function show($id): void
    {
        $stmt = db()->prepare('SELECT * FROM users WHERE id = ?');
        $stmt->execute([(int) $id]);
        $profil = $stmt->fetch();
        if (!$profil) abort(404);
        unset($profil['password']);   // jangan pernah dikirim ke tampilan

        $st = db()->prepare(ProjectController::querySelectKarya() . ' WHERE p.user_id = ? ORDER BY p.created_at DESC, p.id DESC');
        $st->execute([$profil['id']]);
        $karya = $st->fetchAll();

        $totalApresiasi = 0;
        foreach ($karya as $k) $totalApresiasi += (int) $k['jml_apresiasi'];

        view('profil/show', [
            'title' => $profil['nama'], 'profil' => $profil, 'karya' => $karya, 'totalApresiasi' => $totalApresiasi,
        ]);
    }

    // Form edit profil: hanya milik sendiri
    public static function showEdit(): void
    {
        $stmt = db()->prepare('SELECT id, nama, kelas, bio, tautan FROM users WHERE id = ?');
        $stmt->execute([auth_user()['id']]);
        view('profil/edit', ['title' => 'Edit Profil', 'profil' => $stmt->fetch()]);
    }

    public static function update(): void
    {
        $kelas = trim($_POST['kelas'] ?? '');
        $bio = trim($_POST['bio'] ?? '');
        $tautan = trim($_POST['tautan'] ?? '');

        if (mb_strlen($kelas) > 60) { flash('error', 'Kelas/jurusan maksimal 60 karakter.'); redirect('/profil/edit'); }
        if (mb_strlen($bio) > 500) { flash('error', 'Bio maksimal 500 karakter.'); redirect('/profil/edit'); }
        if ($tautan !== '' && (strlen($tautan) > 255 || safe_url($tautan) === null || !filter_var($tautan, FILTER_VALIDATE_URL))) {
            flash('error', 'Tautan harus berupa URL yang valid diawali http:// atau https://.');
            redirect('/profil/edit');
        }

        $uid = (int) auth_user()['id'];
        db()->prepare('UPDATE users SET kelas = ?, bio = ?, tautan = ? WHERE id = ?')
            ->execute([$kelas !== '' ? $kelas : null, $bio !== '' ? $bio : null, $tautan !== '' ? $tautan : null, $uid]);

        flash('success', 'Profil berhasil diperbarui.');
        redirect('/profil/' . $uid);
    }
}
