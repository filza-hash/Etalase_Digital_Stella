<?php
class ProjectController
{
    // SELECT dasar untuk karya: + nama pemilik, jumlah apresiasi, dan apakah sudah diapresiasi pengguna ini
    public static function querySelectKarya(): string
    {
        $uid = (int) auth_user()['id'];
        return 'SELECT p.*, u.nama AS pemilik,'
            . ' (SELECT COUNT(*) FROM apresiasi a WHERE a.project_id = p.id) AS jml_apresiasi,'
            . ' (SELECT COUNT(*) FROM apresiasi b WHERE b.project_id = p.id AND b.user_id = ' . $uid . ') AS diapresiasi'
            . ' FROM projects p JOIN users u ON u.id = p.user_id';
    }

    // Kategori dari form: harus ada di daftar, jika tidak -> 'Lainnya'
    private static function kategoriDari($nilai): string
    {
        $nilai = trim((string) $nilai);
        return in_array($nilai, kategori_list(), true) ? $nilai : 'Lainnya';
    }

    // [Read] Etalase - semua role
    public static function index(): void
    {
        $q = trim($_GET['q'] ?? '');
        $kategori = trim($_GET['kategori'] ?? '');
        if (!in_array($kategori, kategori_list(), true)) $kategori = '';

        $where = [];
        $params = [];
        if ($q !== '') {
            $where[] = '(p.nama_project LIKE ? OR u.nama LIKE ?)';
            $params[] = "%$q%";
            $params[] = "%$q%";
        }
        if ($kategori !== '') {
            $where[] = 'p.kategori = ?';
            $params[] = $kategori;
        }
        $sql = self::querySelectKarya() . ($where ? ' WHERE ' . implode(' AND ', $where) : '')
            . ' ORDER BY p.created_at DESC, p.id DESC';
        $stmt = db()->prepare($sql);
        $stmt->execute($params);
        $semua = $stmt->fetchAll();

        // Karya unggulan tampil di bagian atas (maks. 6) - hanya saat tidak sedang mencari/menyaring
        $unggulan = [];
        $lainnya = [];
        if ($q === '' && $kategori === '') {
            foreach ($semua as $p) {
                if ((int) $p['unggulan'] === 1 && count($unggulan) < 6) $unggulan[] = $p;
                else $lainnya[] = $p;
            }
        } else {
            $lainnya = $semua;
        }

        $perKategori = [];
        foreach (db()->query('SELECT kategori, COUNT(*) AS jml FROM projects GROUP BY kategori')->fetchAll() as $h) {
            $perKategori[$h['kategori']] = (int) $h['jml'];
        }

        view('projects/index', [
            'title' => 'Etalase Digital', 'projects' => $semua, 'unggulan' => $unggulan, 'lainnya' => $lainnya,
            'q' => $q, 'kategori' => $kategori, 'perKategori' => $perKategori,
        ]);
    }

    // [Read] Detail satu karya - semua role
    public static function show($id): void
    {
        $stmt = db()->prepare(self::querySelectKarya() . ' WHERE p.id = ?');
        $stmt->execute([$id]);
        $project = $stmt->fetch();
        if (!$project) abort(404);

        view('projects/show', ['title' => $project['nama_project'], 'project' => $project]);
    }

    // Beri / cabut apresiasi (hati). Tidak boleh pada karya sendiri.
    public static function toggleApresiasi($id): void
    {
        $stmt = db()->prepare('SELECT id, user_id FROM projects WHERE id = ?');
        $stmt->execute([$id]);
        $p = $stmt->fetch();
        if (!$p) {
            if (is_fetch()) json_out(['error' => 'Karya tidak ditemukan.'], 404);
            abort(404);
        }

        $uid = (int) auth_user()['id'];
        if ((int) $p['user_id'] === $uid) {
            if (is_fetch()) json_out(['error' => 'Kamu tidak bisa mengapresiasi karyamu sendiri.'], 403);
            flash('error', 'Kamu tidak bisa mengapresiasi karyamu sendiri.');
            redirect_back('/projects/' . (int) $p['id']);
        }

        $pdo = db();
        $hapus = $pdo->prepare('DELETE FROM apresiasi WHERE project_id = ? AND user_id = ?');
        $hapus->execute([$p['id'], $uid]);
        if ($hapus->rowCount() > 0) {
            $liked = false;
        } else {
            $pdo->prepare('INSERT IGNORE INTO apresiasi (project_id, user_id) VALUES (?, ?)')->execute([$p['id'], $uid]);
            $liked = true;
        }

        $hitung = $pdo->prepare('SELECT COUNT(*) FROM apresiasi WHERE project_id = ?');
        $hitung->execute([$p['id']]);
        $count = (int) $hitung->fetchColumn();

        if (is_fetch()) json_out(['liked' => $liked, 'count' => $count]);
        redirect_back('/projects/' . (int) $p['id']);
    }

    // Tandai / cabut karya unggulan - Admin & Super Admin
    public static function toggleUnggulan($id): void
    {
        $stmt = db()->prepare('SELECT id, unggulan FROM projects WHERE id = ?');
        $stmt->execute([$id]);
        $p = $stmt->fetch();
        if (!$p) abort(404);

        $baru = (int) $p['unggulan'] === 1 ? 0 : 1;
        db()->prepare('UPDATE projects SET unggulan = ? WHERE id = ?')->execute([$baru, $p['id']]);
        flash('success', $baru ? 'Karya dijadikan unggulan.' : 'Karya dicabut dari unggulan.');
        redirect_back('/projects');
    }

    public static function showCreate(): void
    {
        view('projects/form', ['title' => 'Tambah Karya', 'project' => null]);
    }

    // [Create] - semua role
    public static function create(): void
    {
        $nama = trim($_POST['nama_project'] ?? '');
        $deskripsi = trim($_POST['deskripsi'] ?? '');
        $link = trim($_POST['link_project'] ?? '');
        $kategori = self::kategoriDari($_POST['kategori'] ?? '');

        if ($nama === '') { flash('error', 'Nama project wajib diisi.'); redirect('/projects/new'); }
        if ($link !== '' && !filter_var($link, FILTER_VALIDATE_URL)) {
            flash('error', 'Link project harus berupa URL yang valid.'); redirect('/projects/new');
        }

        [$gambar, $err] = upload_gambar($_FILES['gambar'] ?? [], true);
        if ($err) { flash('error', $err); redirect('/projects/new'); }

        db()->prepare('INSERT INTO projects (user_id, nama_project, deskripsi, gambar, link_project, kategori) VALUES (?,?,?,?,?,?)')
            ->execute([auth_user()['id'], $nama, $deskripsi, $gambar, $link, $kategori]);

        flash('success', 'Karya berhasil ditambahkan.');
        redirect('/projects');
    }

    // Ambil project + cek hak akses (owner / admin / super admin)
    private static function ambil($id): array
    {
        $stmt = db()->prepare('SELECT * FROM projects WHERE id = ?');
        $stmt->execute([$id]);
        $project = $stmt->fetch();
        if (!$project) abort(404);
        if (!can_modify(auth_user(), $project)) abort(403);
        return $project;
    }

    public static function showEdit($id): void
    {
        view('projects/form', ['title' => 'Edit Karya', 'project' => self::ambil($id)]);
    }

    // [Update]
    public static function update($id): void
    {
        $project = self::ambil($id);
        $nama = trim($_POST['nama_project'] ?? '');
        $deskripsi = trim($_POST['deskripsi'] ?? '');
        $link = trim($_POST['link_project'] ?? '');
        $kategori = self::kategoriDari($_POST['kategori'] ?? '');
        $back = '/projects/' . (int) $id . '/edit';

        if ($nama === '') { flash('error', 'Nama project wajib diisi.'); redirect($back); }
        if ($link !== '' && !filter_var($link, FILTER_VALIDATE_URL)) {
            flash('error', 'Link project harus berupa URL yang valid.'); redirect($back);
        }

        [$gambarBaru, $err] = upload_gambar($_FILES['gambar'] ?? [], false);
        if ($err) { flash('error', $err); redirect($back); }

        $gambar = $project['gambar'];
        if ($gambarBaru) { hapus_file($project['gambar']); $gambar = $gambarBaru; }

        db()->prepare('UPDATE projects SET nama_project=?, deskripsi=?, gambar=?, link_project=?, kategori=? WHERE id=?')
            ->execute([$nama, $deskripsi, $gambar, $link, $kategori, $project['id']]);

        flash('success', 'Karya berhasil diperbarui.');
        redirect('/projects');
    }

    // [Delete]
    public static function destroy($id): void
    {
        $project = self::ambil($id);
        db()->prepare('DELETE FROM projects WHERE id = ?')->execute([$project['id']]);
        hapus_file($project['gambar']);
        flash('success', 'Karya berhasil dihapus.');
        redirect('/projects');
    }

    // Hapus semua karya - Admin & Super Admin saja
    public static function deleteAll(): void
    {
        $rows = db()->query('SELECT gambar FROM projects')->fetchAll();
        db()->exec('DELETE FROM projects');
        foreach ($rows as $r) hapus_file($r['gambar']);
        flash('success', 'Semua karya berhasil dihapus.');
        redirect('/projects');
    }
}
