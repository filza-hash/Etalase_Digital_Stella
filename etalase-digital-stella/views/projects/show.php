<?php require __DIR__ . '/../partials/header.php'; ?>
<?php
$pid = (int) $project['id'];
$gambarUrl = url('uploads/' . rawurlencode($project['gambar']));
$tautan = safe_url($project['link_project'] ?? '');
$unggul = (int) ($project['unggulan'] ?? 0) === 1;
?>
<div class="detail">
  <a class="back" href="<?= url('/projects') ?>"><?= icon('back') ?>Kembali ke Etalase</a>

  <section class="detail-media" style="--bg:url('<?= e($gambarUrl) ?>')">
    <?php if ($unggul): ?><span class="ribbon ribbon-lg"><?= icon('star') ?>Unggulan</span><?php endif; ?>
    <button type="button" class="detail-img" data-lightbox="<?= e($gambarUrl) ?>" data-caption="<?= e($project['nama_project']) ?>" aria-label="Perbesar gambar <?= e($project['nama_project']) ?>">
      <img src="<?= e($gambarUrl) ?>" alt="<?= e($project['nama_project']) ?>">
    </button>
    <span class="detail-hint">Klik gambar untuk memperbesar</span>
  </section>

  <article class="card detail-body">
    <span class="chip"><?= icon('tag') ?><?= e($project['kategori'] ?? 'Lainnya') ?></span>
    <h1><?= e($project['nama_project']) ?></h1>

    <div class="meta">
      <a class="meta-item meta-link" href="<?= url('/profil/' . (int) $project['user_id']) ?>">
        <?= avatar($project['pemilik']) ?>
        <div><span class="lbl">Dibuat oleh</span><span class="val"><?= e($project['pemilik']) ?></span></div>
      </a>
      <div class="meta-item">
        <span class="meta-ic"><?= icon('calendar') ?></span>
        <div><span class="lbl">Dibuat pada</span><time class="val" datetime="<?= e(date('c', strtotime($project['created_at']))) ?>"><?= e(tgl_lengkap($project['created_at'])) ?></time></div>
      </div>
      <div class="meta-item">
        <span class="meta-ic"><?= icon('heart') ?></span>
        <div><span class="lbl">Apresiasi</span><span class="val"><span data-like-count="<?= $pid ?>"><?= (int) $project['jml_apresiasi'] ?></span> orang</span></div>
      </div>
    </div>

    <h2>Deskripsi</h2>
    <?php if (trim((string) $project['deskripsi']) !== ''): ?>
      <p class="detail-desc"><?= nl2br(e($project['deskripsi'])) ?></p>
    <?php else: ?>
      <p class="detail-desc muted">Belum ada deskripsi untuk karya ini.</p>
    <?php endif; ?>

    <div class="actions">
      <?php if ($tautan): ?>
        <a class="btn btn-primary" href="<?= e($tautan) ?>" target="_blank" rel="noopener">Buka Link <?= icon('link') ?></a>
      <?php endif; ?>
      <?= like_button($project, $user, 'like-lg') ?>
      <?php if (is_admin($user)): ?>
        <form action="<?= url('/admin/projects/' . $pid . '/unggulan') ?>" method="POST" class="inline">
          <?= csrf_field() ?>
          <button class="btn"><?= icon($unggul ? 'star' : 'star-o') ?><?= $unggul ? 'Cabut Unggulan' : 'Jadikan Unggulan' ?></button>
        </form>
      <?php endif; ?>
      <?php if (can_modify($user, $project)): ?>
        <a class="btn" href="<?= url('/projects/' . $pid . '/edit') ?>"><?= icon('edit') ?>Edit</a>
        <form action="<?= url('/projects/' . $pid) ?>" method="POST" class="inline"
              onsubmit="return confirm('Hapus karya ini?')"
              data-confirm="“<?= e($project['nama_project']) ?>” akan dihapus permanen." data-confirm-title="Hapus karya ini?" data-confirm-yes="Ya, hapus">
          <?= csrf_field() ?><?= method_field('DELETE') ?>
          <button class="btn btn-danger"><?= icon('trash') ?>Hapus</button>
        </form>
      <?php endif; ?>
    </div>
  </article>
</div>
<?php require __DIR__ . '/../partials/footer.php'; ?>
