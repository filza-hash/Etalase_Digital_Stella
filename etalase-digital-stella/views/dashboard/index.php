<?php
$stat = $stat ?? [
    'karya' => 0,
    'pengguna' => 0,
    'siswa' => 0,
    'apresiasi' => 0,
    'unggulan' => 0,
];

$kategori = $kategori ?? [];
$terbaru = $terbaru ?? [];
$populer = $populer ?? [];
$aktif = $aktif ?? [];

$maks = 0;
foreach ($kategori as $k) {
    $maks = max($maks, (int) ($k['jml'] ?? 0));
}

require __DIR__ . '/../partials/header.php';
?>
<div class="dash-head">
  <h1>Dasbor</h1>
  <p class="muted">Ringkasan aktivitas Etalase Stella.</p>
</div>

<div class="stat-grid">
  <div class="card stat stat-main">
    <span class="stat-ic"><?= icon('folder') ?></span>
    <div><strong data-count="<?= (int) $stat['karya'] ?>"><?= (int) $stat['karya'] ?></strong><span>Total karya</span></div>
  </div>
  <div class="card stat">
    <span class="stat-ic"><?= icon('users') ?></span>
    <div><strong data-count="<?= (int) $stat['pengguna'] ?>"><?= (int) $stat['pengguna'] ?></strong><span>Pengguna (<?= (int) $stat['siswa'] ?> siswa)</span></div>
  </div>
  <div class="card stat">
    <span class="stat-ic"><?= icon('heart') ?></span>
    <div><strong data-count="<?= (int) $stat['apresiasi'] ?>"><?= (int) $stat['apresiasi'] ?></strong><span>Total apresiasi</span></div>
  </div>
  <div class="card stat">
    <span class="stat-ic"><?= icon('star') ?></span>
    <div><strong data-count="<?= (int) $stat['unggulan'] ?>"><?= (int) $stat['unggulan'] ?></strong><span>Karya unggulan</span></div>
  </div>
</div>

<div class="dash-grid">
  <section class="card panel">
    <h2>Kategori terpopuler</h2>
    <?php if (!$kategori): ?>
      <p class="muted">Belum ada data.</p>
    <?php else: ?>
      <?php foreach ($kategori as $k): ?>
        <a class="bar-row" href="<?= url('/projects?' . http_build_query(['kategori' => $k['kategori']])) ?>">
          <span class="bar-label"><?= e($k['kategori']) ?></span>
          <span class="bar"><span style="--w:<?= $maks ? round(((int) $k['jml'] / $maks) * 100) : 0 ?>%"></span></span>
          <strong><?= (int) $k['jml'] ?></strong>
        </a>
      <?php endforeach; ?>
    <?php endif; ?>
  </section>

  <section class="card panel">
    <h2>Karya terbaru</h2>
    <?php if (!$terbaru): ?>
      <p class="muted">Belum ada karya.</p>
    <?php else: ?>
      <ul class="mini-list">
      <?php foreach ($terbaru as $t): ?>
        <li class="mini-item">
          <img class="mini-thumb" src="<?= url('uploads/' . rawurlencode($t['gambar'])) ?>" alt="">
          <div>
            <a class="t" href="<?= url('/projects/' . (int) $t['id']) ?>"><?= e($t['nama_project']) ?></a>
            <small><?= e($t['pemilik']) ?></small>
          </div>
          <span class="n n-muted"><?= e(tgl_id($t['created_at'])) ?></span>
        </li>
      <?php endforeach; ?>
      </ul>
    <?php endif; ?>
  </section>

  <section class="card panel">
    <h2>Karya paling diapresiasi</h2>
    <?php if (!$populer): ?>
      <p class="muted">Belum ada apresiasi.</p>
    <?php else: ?>
      <ul class="mini-list">
      <?php foreach ($populer as $t): ?>
        <li class="mini-item">
          <img class="mini-thumb" src="<?= url('uploads/' . rawurlencode($t['gambar'])) ?>" alt="">
          <div>
            <a class="t" href="<?= url('/projects/' . (int) $t['id']) ?>"><?= e($t['nama_project']) ?></a>
            <small><?= e($t['pemilik']) ?></small>
          </div>
          <span class="n"><?= icon('heart') ?><?= (int) $t['jml'] ?></span>
        </li>
      <?php endforeach; ?>
      </ul>
    <?php endif; ?>
  </section>

  <section class="card panel">
    <h2>Siswa paling aktif</h2>
    <?php if (!$aktif): ?>
      <p class="muted">Belum ada data.</p>
    <?php else: ?>
      <ul class="mini-list">
      <?php foreach ($aktif as $a): ?>
        <li class="mini-item">
          <?= avatar($a['nama']) ?>
          <div>
            <a class="t" href="<?= url('/profil/' . (int) $a['id']) ?>"><?= e($a['nama']) ?></a>
            <small><?= e(role_label($a['role'])) ?></small>
          </div>
          <span class="n"><?= (int) $a['jml'] ?> karya</span>
        </li>
      <?php endforeach; ?>
      </ul>
    <?php endif; ?>
  </section>
</div>
<?php require __DIR__ . '/../partials/footer.php'; ?>
