<?php require __DIR__ . '/../partials/header.php'; ?>
<?php
$total = count($projects);
$pemilik = count(array_unique(array_column($projects, 'pemilik')));
$difilter = $q !== '' || $kategori !== '';
if ($q !== '') $judul = 'Hasil untuk “' . $q . '”';
elseif ($kategori !== '') $judul = 'Kategori ' . $kategori;
else $judul = 'Semua karya';
?>
<section class="hero" id="hero">
  <?= constellation() ?>
  <div class="hero-copy">
    <h1>Etalase Digital</h1>
    <p class="lead">Kumpulan karya digital siswa Stella. Cari, lihat, dan buka langsung proyeknya.</p>
    <form action="<?= url('/projects') ?>" method="GET" class="hero-search" role="search">
      <?= icon('search') ?>
      <label for="q" class="sr-only">Cari karya atau pemilik</label>
      <input type="text" id="q" name="q" value="<?= e($q) ?>" placeholder="Cari karya / pemilik..." autocomplete="off" data-live-filter>
      <?php if ($kategori !== ''): ?><input type="hidden" name="kategori" value="<?= e($kategori) ?>"><?php endif; ?>
      <button class="btn">Cari</button>
    </form>
  </div>
</section>

<nav class="chips" aria-label="Filter kategori">
  <a class="chip-link<?= $kategori === '' ? ' is-active' : '' ?>" href="<?= url('/projects' . ($q !== '' ? '?' . http_build_query(['q' => $q]) : '')) ?>">Semua <span><?= array_sum($perKategori) ?></span></a>
  <?php foreach (kategori_list() as $k): ?>
    <?php if (empty($perKategori[$k]) && $kategori !== $k) continue; ?>
    <?php $qs = ['kategori' => $k]; if ($q !== '') $qs['q'] = $q; ?>
    <a class="chip-link<?= $kategori === $k ? ' is-active' : '' ?>" href="<?= url('/projects?' . http_build_query($qs)) ?>"><?= e($k) ?> <span><?= (int) ($perKategori[$k] ?? 0) ?></span></a>
  <?php endforeach; ?>
</nav>

<?php if ($unggulan): ?>
<section class="unggulan" id="unggulan" aria-labelledby="judul-unggulan">
  <div class="section-head">
    <div>
      <h2 id="judul-unggulan"><?= icon('star') ?>Karya Unggulan</h2>
      <p class="count">Pilihan terbaik dari Etalase Stella</p>
    </div>
  </div>
  <div class="grid grid-featured">
    <?php foreach ($unggulan as $i => $p): ?>
      <?php include __DIR__ . '/../partials/project_card.php'; ?>
    <?php endforeach; ?>
  </div>
</section>
<?php endif; ?>

<div class="section-head">
  <div>
    <h2><?= e($judul) ?></h2>
    <p class="count" id="count" data-total="<?= $total ?>"><?= $total ?> karya dari <?= $pemilik ?> pemilik</p>
  </div>
  <div class="toolbar-actions">
    <a href="<?= url('/projects/new') ?>" class="btn btn-primary"><?= icon('plus') ?>Tambah Karya</a>
    <?php if ($user['role'] !== 'siswa' && !$difilter && $total): ?>
      <form action="<?= url('/admin/projects/delete-all') ?>" method="POST" class="inline"
            onsubmit="return confirm('Hapus SEMUA karya? Tindakan ini tidak bisa dibatalkan!')"
            data-confirm="Semua karya akan dihapus permanen dan tidak bisa dikembalikan."
            data-confirm-title="Hapus semua karya?" data-confirm-yes="Ya, hapus semua">
        <?= csrf_field() ?>
        <button class="btn btn-danger"><?= icon('trash') ?>Hapus Semua</button>
      </form>
    <?php endif; ?>
  </div>
</div>

<?php if (!$total): ?>
  <div class="card empty">
    <svg class="empty-art" viewBox="0 0 132 100" fill="none" aria-hidden="true">
      <path d="M18 74 46 44l30 22 38-40" stroke="currentColor" stroke-opacity=".35" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" stroke-dasharray="4 6"/>
      <circle cx="18" cy="74" r="5" fill="currentColor" fill-opacity=".5"/><circle cx="46" cy="44" r="6" fill="currentColor" fill-opacity=".7"/>
      <circle cx="76" cy="66" r="5" fill="currentColor" fill-opacity=".5"/><circle cx="114" cy="26" r="9" fill="#E32636"/>
    </svg>
    <?php if ($difilter): ?>
      <h2>Tidak ada karya yang cocok</h2>
      <p>Coba kata kunci atau kategori lain, atau tampilkan semua karya.</p>
      <a href="<?= url('/projects') ?>" class="btn">Tampilkan semua karya</a>
    <?php else: ?>
      <h2>Belum ada karya digital</h2>
      <p>Jadilah yang pertama menambahkan!</p>
      <a href="<?= url('/projects/new') ?>" class="btn btn-primary"><?= icon('plus') ?>Tambah karya pertama</a>
    <?php endif; ?>
  </div>
<?php endif; ?>

<div class="card empty no-result" id="no-result">
  <h2>Tidak ada karya yang cocok</h2>
  <p>Coba kata kunci lain.</p>
</div>

<div class="grid" id="grid">
<?php foreach ($lainnya as $i => $p): ?>
  <?php include __DIR__ . '/../partials/project_card.php'; ?>
<?php endforeach; ?>
</div>
<?php require __DIR__ . '/../partials/footer.php'; ?>
