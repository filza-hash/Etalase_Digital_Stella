<?php require __DIR__ . '/../partials/header.php'; ?>
<div class="card form-card">
  <h1><?= $target ? 'Edit User' : 'Tambah User' ?></h1>
  <form action="<?= $target ? url('/users/' . (int) $target['id']) : url('/users') ?>" method="POST">
    <?= csrf_field() ?>
    <?php if ($target) echo method_field('PUT'); ?>
    <label for="nama">Nama</label>
    <input type="text" id="nama" name="nama" value="<?= e($target['nama'] ?? '') ?>" required>
    <label for="email">Email</label>
    <input type="email" id="email" name="email" value="<?= e($target['email'] ?? '') ?>" required>
    <label for="password">Password <?= $target ? '(kosongkan jika tidak diganti)' : '(min. 6 karakter)' ?></label>
    <input type="password" id="password" name="password" minlength="6" <?= $target ? '' : 'required' ?>>
    <label for="role">Role</label>
    <select id="role" name="role">
      <?php foreach ($roles as $r): ?>
        <option value="<?= e($r) ?>" <?= ($target && $target['role'] === $r) ? 'selected' : '' ?>><?= e(role_label($r)) ?></option>
      <?php endforeach; ?>
    </select>
    <div class="actions">
      <button class="btn btn-primary">Simpan</button>
      <a href="<?= url('/users') ?>" class="btn">Batal</a>
    </div>
  </form>
</div>
<?php require __DIR__ . '/../partials/footer.php'; ?>
