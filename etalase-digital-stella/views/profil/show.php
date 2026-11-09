<?php require __DIR__ . '/../partials/header.php'; ?>
<?php
$milikSendiri = (int) $profil['id'] === (int) $user['id'];
$tautan = safe_url($profil['tautan'] ?? '');
$jumlahKarya = count($karya);
?>
<section class="card profile">
  <div class="profile-cover" aria-hidden="true"></div>
  <div class="profile-main">
    <?= avatar($profil['nama'], 'avatar-xl') ?>
    <div class="profile-info">
      <h1><?= e($profil['nama']) ?></h1>
      <div class="profile-tags">
        <em class="badge badge-<?= e($profil['role']) ?>"><?= e(role_label($profil['role'])) ?></em>
        <?php if (!empty($profil['kelas'])): ?><span class="chip"><?= icon('users') ?><?= e($profil['kelas']) ?></span><?php endif; ?>
      </div>
      <?php if (!empty($profil['bio'])): ?>
        <p class="profile-bio"><?= nl2br(e($profil['bio'])) ?></p>
      <?php elseif ($milikSendiri): ?>
        <p class="profile-bio muted">Belum ada bio. Ceritakan sedikit tentang dirimu agar profilmu lebih menarik.</p>
      <?php endif; ?>
      <div class="profile-links">
        <?php if ($tautan): ?><a class="btn btn-sm" href="<?= e($tautan) ?>" target="_blank" rel="noopener">Portofolio <?= icon('link') ?></a><?php endif; ?>
        <?php if ($milikSendiri): ?><a class="btn btn-sm btn-primary" href="<?= url('/profil/edit') ?>"><?= icon('edit') ?>Edit Profil</a><?php endif; ?>
      </div>
    </div>
  </div>
  <div class="profile-stats">
    <div class="pstat"><strong data-count="<?= $jumlahKarya ?>"><?= $jumlahKarya ?></strong><span>Karya</span></div>
    <div class="pstat"><strong data-count="<?= (int) $totalApresiasi ?>"><?= (int) $totalApresiasi ?></strong><span>Apresiasi diterima</span></div>
    <?php if (!empty($profil['created_at'])): ?>
      <div class="pstat"><strong class="pstat-text"><?= e(tgl_id($profil['created_at'])) ?></strong><span>Bergabung</span></div>
    <?php endif; ?>
  </div>
</section>

<div class="section-head">
  <div>
    <h2>Karya <?= e($profil['nama']) ?></h2>
    <p class="count" id="count" data-total="<?= $jumlahKarya ?>"><?= $jumlahKarya ?> karya</p>
  </div>
  <?php if ($milikSendiri): ?>
    <div class="toolbar-actions"><a href="<?= url('/projects/new') ?>" class="btn btn-primary"><?= icon('plus') ?>Tambah Karya</a></div>
  <?php endif; ?>
</div>

<?php if (!$jumlahKarya): ?>
  <div class="card empty">
    <h2><?= $milikSendiri ? 'Kamu belum punya karya' : 'Belum ada karya' ?></h2>
    <p><?= $milikSendiri ? 'Tambahkan karya pertamamu dan tampilkan di etalase.' : 'Siswa ini belum menambahkan karya.' ?></p>
    <?php if ($milikSendiri): ?><a href="<?= url('/projects/new') ?>" class="btn btn-primary"><?= icon('plus') ?>Tambah karya pertama</a><?php endif; ?>
  </div>
<?php else: ?>
  <div class="grid" id="grid">
  <?php foreach ($karya as $i => $p): ?>
    <?php include __DIR__ . '/../partials/project_card.php'; ?>
  <?php endforeach; ?>
  </div>
<?php endif; ?>
<?php require __DIR__ . '/../partials/footer.php'; ?>
