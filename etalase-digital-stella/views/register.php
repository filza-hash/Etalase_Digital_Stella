<?php $authPage = true; require __DIR__ . '/partials/header.php'; ?>
<div class="auth">
  <h1>Daftar Siswa</h1>
  <p class="sub">Buat akun untuk mulai memamerkan karyamu.</p>
  <form action="<?= url('/register') ?>" method="POST">
    <?= csrf_field() ?>
    <label for="nama">Nama Lengkap</label>
    <input type="text" id="nama" name="nama" required autofocus autocomplete="name">
    <label for="email">Email</label>
    <input type="email" id="email" name="email" required autocomplete="email">
    <label for="password">Password (min. 6 karakter)</label>
    <div class="pw">
      <input type="password" id="password" name="password" minlength="6" required autocomplete="new-password">
      <button type="button" class="pw-toggle" aria-label="Tampilkan password"><?= icon('eye') ?></button>
    </div>
    <button class="btn btn-primary block">Daftar</button>
  </form>
  <p class="muted">Sudah punya akun? <a href="<?= url('/login') ?>">Login</a></p>
</div>
<?php require __DIR__ . '/partials/footer.php'; ?>
