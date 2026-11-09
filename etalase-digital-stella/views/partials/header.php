<?php
$title = $title ?? 'Etalase Digital Stella';
$user = auth_user();
$flash = get_flash();
$authPage = $authPage ?? false;
$path = request_path();
$isEtalase = $path === '/projects' || preg_match('#^/projects/\d+$#', $path) === 1;
$isTambah  = $path === '/projects/new';
$isUsers   = strpos($path, '/users') === 0 || strpos($path, '/admin/users') === 0;
$isDash    = $path === '/dashboard';
$isProfil  = strpos($path, '/profil') === 0;
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <?php if ($logo = logo_url()): ?><link rel="icon" href="<?= e($logo) ?>"><?php endif; ?>
  <meta name="theme-color" content="#E32636">
  <title><?= e($title) ?> · Etalase Digital Stella</title>
  <script>document.documentElement.classList.add('js');setTimeout(function(){if(!window.__stella)document.documentElement.classList.add('js-failed')},3000)</script>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?= asset('assets/css/base.css') ?>">
  <link rel="stylesheet" href="<?= asset('assets/css/layout.css') ?>">
  <link rel="stylesheet" href="<?= asset('assets/css/components.css') ?>">
  <link rel="stylesheet" href="<?= asset('assets/css/pages.css') ?>">
  <script defer src="<?= asset('assets/js/app.js') ?>"></script>
</head>
<body class="<?= $authPage ? 'auth-body' : '' ?>">
<?php if ($flash): ?>
<div class="toast-stack" aria-live="polite">
  <div class="toast toast-<?= e($flash['type']) ?>" role="status">
    <span class="toast-icon"><?= icon($flash['type'] === 'success' ? 'check' : 'alert') ?></span>
    <span class="toast-msg"><?= e($flash['message']) ?></span>
    <button type="button" class="toast-close" aria-label="Tutup pesan">&times;</button>
  </div>
</div>
<?php endif; ?>

<?php if ($authPage): ?>
<div class="auth-shell">
  <aside class="auth-aside">
    <a href="<?= url('/projects') ?>" class="brand"><?= brand_mark(true) ?>Etalase Stella</a>
    <div>
      <?= constellation() ?>
      <h2>Karya siswa Stella, dalam satu etalase.</h2>
      <p>Masuk untuk menambahkan karyamu atau melihat karya teman-temanmu.</p>
    </div>
  </aside>
  <main class="auth-main">
<?php else: ?>
<nav class="nav" id="nav">
  <a href="<?= url('/projects') ?>" class="brand"><?= brand_mark() ?>Etalase Stella</a>
  <button class="nav-toggle" type="button" aria-label="Buka menu" aria-expanded="false" aria-controls="nav-links"><span></span></button>
  <div class="nav-links" id="nav-links">
    <?php if ($user): ?>
      <a class="nav-link <?= $isEtalase ? 'is-active' : '' ?>" href="<?= url('/projects') ?>">Etalase</a>
      <a class="nav-link <?= $isTambah ? 'is-active' : '' ?>" href="<?= url('/projects/new') ?>">+ Tambah Karya</a>
      <?php if (is_admin($user)): ?><a class="nav-link <?= $isDash ? 'is-active' : '' ?>" href="<?= url('/dashboard') ?>">Dasbor</a><?php endif; ?>
      <?php if ($user['role'] === 'super_admin'): ?><a class="nav-link <?= $isUsers ? 'is-active' : '' ?>" href="<?= url('/users') ?>">Kelola User</a><?php endif; ?>
      <div class="nav-user">
        <a class="nav-me<?= $isProfil ? ' is-active' : '' ?>" href="<?= url('/profil/' . (int) $user['id']) ?>" title="Profil saya">
          <?= avatar($user['nama']) ?>
          <span class="who"><?= e($user['nama']) ?> <em class="badge badge-<?= e($user['role']) ?>"><?= e(role_label($user['role'])) ?></em></span>
        </a>
        <form action="<?= url('/logout') ?>" method="POST" class="inline">
          <?= csrf_field() ?>
          <button class="btn btn-ghost btn-sm">Keluar</button>
        </form>
      </div>
    <?php else: ?>
      <a class="nav-link" href="<?= url('/login') ?>">Login</a>
      <a class="nav-link" href="<?= url('/register') ?>">Daftar</a>
    <?php endif; ?>
  </div>
</nav>
<main class="container">
<?php endif; ?>
