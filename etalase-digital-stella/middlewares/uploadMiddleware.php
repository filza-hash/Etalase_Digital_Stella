<?php
// Proses upload gambar. Return [namaFile|null, pesanError|null]
function upload_gambar(array $file, bool $wajib): array
{
    $err = $file['error'] ?? UPLOAD_ERR_NO_FILE;

    if ($err === UPLOAD_ERR_NO_FILE) {
        return $wajib ? [null, 'Gambar wajib diunggah.'] : [null, null];
    }
    if ($err === UPLOAD_ERR_INI_SIZE || $err === UPLOAD_ERR_FORM_SIZE) {
        return [null, 'Ukuran gambar terlalu besar (maks. 2 MB).'];
    }
    if ($err !== UPLOAD_ERR_OK) {
        return [null, 'Upload gambar gagal. Coba lagi.'];
    }
    if ($file['size'] > 2 * 1024 * 1024) {
        return [null, 'Ukuran gambar maksimal 2 MB.'];
    }

    $mime = (new finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']);
    $ekstensi = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/gif' => 'gif', 'image/webp' => 'webp'];
    if (!isset($ekstensi[$mime])) {
        return [null, 'File harus berupa gambar (JPG, PNG, GIF, WEBP).'];
    }

    $nama = time() . '-' . bin2hex(random_bytes(4)) . '.' . $ekstensi[$mime];
    if (!move_uploaded_file($file['tmp_name'], __DIR__ . '/../uploads/' . $nama)) {
        return [null, 'Gagal menyimpan file ke folder uploads.'];
    }
    return [$nama, null];
}
