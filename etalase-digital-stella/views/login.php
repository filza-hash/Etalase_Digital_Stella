<?php $authPage = true; require __DIR__ . '/partials/header.php'; ?>
<div class="auth">
  <h1>Masuk</h1>
  <p class="sub">Selamat datang kembali. Masuk untuk melanjutkan.</p>
  <form action="<?= url('/login') ?>" method="POST">
    <?= csrf_field() ?>
    <label for="email">Email</label>
    <input type="email" id="email" name="email" required autofocus autocomplete="email">
    <label for="password">Password</label>
    <div class="pw">
      <input type="password" id="password" name="password" required autocomplete="current-password">
      <button type="button" class="pw-toggle" aria-label="Tampilkan password"><?= icon('eye') ?></button>
    </div>
    <button class="btn btn-primary block">Login</button>
  </form>
  <p class="muted">Belum punya akun? <a href="<?= url('/register') ?>">Daftar sebagai siswa</a></p>
  <details class="muted"><summary>Akun uji</summary>
    superadmin@stella.sch.id<br>admin@stella.sch.id<br>siswa@stella.sch.id<br>Password: <code>password123</code>
  </details>
</div>
<?php require __DIR__ . '/partials/footer.php'; ?>
