<?php require __DIR__ . '/../partials/header.php'; ?>
<div class="card form-card">
  <h1><?= $project ? 'Edit Karya' : 'Tambah Karya Digital' ?></h1>
  <p class="sub"><?= $project ? 'Perbarui informasi karyamu.' : 'Isi detail karyamu, lalu unggah gambar sampulnya.' ?></p>
  <form action="<?= $project ? url('/projects/' . (int) $project['id']) : url('/projects') ?>" method="POST" enctype="multipart/form-data">
    <?= csrf_field() ?>
    <?php if ($project) echo method_field('PUT'); ?>
    <label for="nama_project">Nama Project</label>
    <input type="text" id="nama_project" name="nama_project" value="<?= e($project['nama_project'] ?? '') ?>" required>
    <label for="kategori">Kategori</label>
    <select id="kategori" name="kategori">
      <?php foreach (kategori_list() as $k): ?>
        <option value="<?= e($k) ?>" <?= ($project['kategori'] ?? 'Lainnya') === $k ? 'selected' : '' ?>><?= e($k) ?></option>
      <?php endforeach; ?>
    </select>
    <label for="deskripsi">Deskripsi</label>
    <textarea id="deskripsi" name="deskripsi" rows="4"><?= e($project['deskripsi'] ?? '') ?></textarea>
    <label for="gambar">Gambar <?= $project ? '(kosongkan jika tidak diganti)' : '' ?></label>
    <?php if ($project && $project['gambar']): ?>
      <img class="preview" src="<?= url('uploads/' . rawurlencode($project['gambar'])) ?>" alt="Gambar saat ini">
    <?php endif; ?>
    <div class="dropzone" data-dropzone>
      <?= icon('upload') ?>
      <span class="dz-ph"><strong>Klik untuk memilih gambar</strong><br>atau seret file ke sini</span>
      <img class="dz-preview" alt="Pratinjau gambar baru">
      <input type="file" id="gambar" name="gambar" accept="image/*" <?= $project ? '' : 'required' ?>>
    </div>
    <label for="link_project">Link Project (URL)</label>
    <input type="url" id="link_project" name="link_project" value="<?= e($project['link_project'] ?? '') ?>" placeholder="https://...">
    <div class="actions">
      <button class="btn btn-primary">Simpan</button>
      <a href="<?= url('/projects') ?>" class="btn">Batal</a>
    </div>
  </form>
</div>
<?php require __DIR__ . '/../partials/footer.php'; ?>
