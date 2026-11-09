<?php require __DIR__ . '/../partials/header.php'; ?>
<div class="toolbar">
  <h1 style="margin:0">Kelola User</h1>
  <a href="<?= url('/users/new') ?>" class="btn btn-primary"><?= icon('plus') ?>Tambah User</a>
</div>
<div class="card table-wrap">
<table>
  <thead><tr><th>#</th><th>Nama</th><th>Email</th><th>Role</th><th>Aksi</th></tr></thead>
  <tbody>
  <?php foreach ($users as $u): ?>
    <tr>
      <td><?= (int) $u['id'] ?></td>
      <td><a class="cell-user" href="<?= url('/profil/' . (int) $u['id']) ?>"><?= avatar($u['nama']) ?><?= e($u['nama']) ?></a></td>
      <td><?= e($u['email']) ?></td>
      <td><span class="badge badge-<?= e($u['role']) ?>"><?= e(role_label($u['role'])) ?></span></td>
      <td class="actions">
        <a class="btn btn-sm" href="<?= url('/users/' . (int) $u['id'] . '/edit') ?>">Edit</a>
        <?php if ((int) $u['id'] !== $user['id']): ?>
        <form action="<?= url('/users/' . (int) $u['id']) ?>" method="POST" class="inline"
              onsubmit="return confirm('Hapus user ini beserta seluruh karyanya?')"
              data-confirm="<?= e($u['nama']) ?> beserta seluruh karyanya akan dihapus permanen." data-confirm-title="Hapus user ini?" data-confirm-yes="Ya, hapus">
          <?= csrf_field() ?><?= method_field('DELETE') ?>
          <button class="btn btn-sm btn-danger">Hapus</button>
        </form>
        <?php endif; ?>
      </td>
    </tr>
  <?php endforeach; ?>
  </tbody>
</table>
</div>
<?php require __DIR__ . '/../partials/footer.php'; ?>
