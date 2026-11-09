<?php require __DIR__ . '/../partials/header.php'; ?>
<div class="card form-card">
  <h1>Edit Profil</h1>
  <p class="sub">Informasi ini tampil di halaman profilmu dan dapat dilihat siswa lain.</p>
  <form action="<?= url('/profil') ?>" method="POST">
    <?= csrf_field() ?>
    <label for="kelas">Kelas / Jurusan</label>
    <input type="text" id="kelas" name="kelas" maxlength="60" value="<?= e($profil['kelas'] ?? '') ?>" placeholder="Contoh: XI RPL 2">
    <label for="bio">Bio</label>
    <textarea id="bio" name="bio" rows="4" maxlength="500" data-maxlen="500" placeholder="Ceritakan singkat tentang dirimu dan minatmu."><?= e($profil['bio'] ?? '') ?></textarea>
    <div class="counter" data-counter-for="bio"><span>0</span>/500</div>
    <label for="tautan">Tautan portofolio (opsional)</label>
    <input type="url" id="tautan" name="tautan" maxlength="255" value="<?= e($profil['tautan'] ?? '') ?>" placeholder="https://...">
    <div class="actions">
      <button class="btn btn-primary">Simpan</button>
      <a href="<?= url('/profil/' . (int) $profil['id']) ?>" class="btn">Batal</a>
    </div>
  </form>
</div>
<?php require __DIR__ . '/../partials/footer.php'; ?>
