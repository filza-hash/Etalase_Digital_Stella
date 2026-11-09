<?php
// Kartu karya. Dipakai di etalase dan profil.
// Variabel: $p (data karya), $user (pengguna login), $i (urutan, untuk jeda animasi)
$pid = (int) $p['id'];
$tautan = safe_url($p['link_project'] ?? '');
$unggul = (int) ($p['unggulan'] ?? 0) === 1;
?>
<article class="card project reveal<?= $unggul ? ' is-unggulan' : '' ?>" style="--d:<?= min((int) ($i ?? 0), 8) * 70 ?>ms" data-search="<?= e(mb_strtolower($p['nama_project'] . ' ' . $p['pemilik'] . ' ' . $p['deskripsi'] . ' ' . ($p['kategori'] ?? ''))) ?>">
  <div class="thumb">
    <img src="<?= url('uploads/' . rawurlencode($p['gambar'])) ?>" alt="<?= e($p['nama_project']) ?>" loading="lazy">
    <?php if ($unggul): ?><span class="ribbon"><?= icon('star') ?>Unggulan</span><?php endif; ?>
    <?= like_button($p, $user) ?>
    <?php if (is_admin($user)): ?>
      <form class="star-form" action="<?= url('/admin/projects/' . $pid . '/unggulan') ?>" method="POST">
        <?= csrf_field() ?>
        <button type="submit" class="star-btn<?= $unggul ? ' is-on' : '' ?>" title="<?= $unggul ? 'Cabut dari unggulan' : 'Jadikan unggulan' ?>" aria-label="<?= $unggul ? 'Cabut dari unggulan' : 'Jadikan unggulan' ?>"><?= icon($unggul ? 'star' : 'star-o') ?></button>
      </form>
    <?php endif; ?>
  </div>
  <div class="body">
    <span class="chip"><?= icon('tag') ?><?= e($p['kategori'] ?? 'Lainnya') ?></span>
    <h3><a href="<?= url('/projects/' . $pid) ?>"><?= e($p['nama_project']) ?></a></h3>
    <div class="owner">
      <a class="owner-who" href="<?= url('/profil/' . (int) $p['user_id']) ?>"><?= avatar($p['pemilik'], 'avatar-sm') ?><span>oleh <strong><?= e($p['pemilik']) ?></strong></span></a>
      <time datetime="<?= e(date('Y-m-d', strtotime($p['created_at']))) ?>"><?= e(tgl_id($p['created_at'])) ?></time>
    </div>
    <p class="desc"><?= nl2br(e($p['deskripsi'])) ?></p>
    <div class="actions">
      <?php if ($tautan): ?>
        <a class="btn btn-sm" href="<?= e($tautan) ?>" target="_blank" rel="noopener">Buka Link <?= icon('link') ?></a>
      <?php endif; ?>
      <?php if (can_modify($user, $p)): ?>
        <a class="btn btn-sm" href="<?= url('/projects/' . $pid . '/edit') ?>">Edit</a>
        <form action="<?= url('/projects/' . $pid) ?>" method="POST" class="inline"
              onsubmit="return confirm('Hapus karya ini?')"
              data-confirm="“<?= e($p['nama_project']) ?>” akan dihapus permanen." data-confirm-title="Hapus karya ini?" data-confirm-yes="Ya, hapus">
          <?= csrf_field() ?><?= method_field('DELETE') ?>
          <button class="btn btn-sm btn-danger">Hapus</button>
        </form>
      <?php endif; ?>
    </div>
  </div>
</article>
