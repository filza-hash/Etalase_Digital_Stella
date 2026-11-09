# Etalase Digital Stella (PHP Native)

CRUD karya digital siswa dengan Role-Based Access Control (Super Admin / Admin / Siswa).
Stack: PHP 8+ Native, PDO MySQL, session, upload file. Tanpa framework.

## Cara Menjalankan (XAMPP / Laragon)
1. Letakkan folder `etalase-digital-stella` di `htdocs` (XAMPP) atau `www` (Laragon). Aktifkan Apache + MySQL.
2. Cek `config/database.php` (default: user `root`, password kosong).
3. Buat database & akun uji, pilih salah satu:
   - Terminal: `php database/setup.php`
   - atau impor `database/schema.sql` lewat phpMyAdmin, lalu buat akun lewat setup.php.
4. Buka http://localhost/etalase-digital-stella/

Tanpa Apache (PHP built-in server): `php -S localhost:8000 index.php` lalu buka http://localhost:8000

Pastikan ekstensi `pdo_mysql` dan `fileinfo` aktif di php.ini (default aktif di XAMPP).

## Akun Uji (password: `password123`)
| Role | Email |
|---|---|
| Super Admin | superadmin@stella.sch.id |
| Admin | admin@stella.sch.id |
| Siswa | siswa@stella.sch.id |

## Route
| Method | Endpoint | Akses |
|---|---|---|
| POST | /login, /register | Public |
| GET | /projects | Semua yang login |
| POST | /projects | Semua yang login |
| PUT | /projects/{id} | Pemilik / Admin / Super Admin |
| DELETE | /projects/{id} | Pemilik / Admin / Super Admin |
| POST | /admin/projects/delete-all | Admin / Super Admin |
| GET | /users, /admin/users | Super Admin |
| POST | /users | Super Admin |
| PUT/DELETE | /users/{id} | Super Admin |

PUT/DELETE dikirim dari form HTML lewat field tersembunyi `_method`.

## Fitur Tambahan
- **Karya unggulan**: Admin/Super Admin menandai karya dengan ikon bintang di foto kartu (atau tombol di halaman detail). Maksimal 6 karya unggulan tampil di bagian atas etalase.
- **Apresiasi**: siswa dan admin dapat memberi/mencabut apresiasi (hati) pada karya orang lain. Karya sendiri tidak bisa diapresiasi.
- **Kategori**: tiap karya punya kategori (daftar di `kategori_list()` pada `config/helpers.php`). Etalase bisa disaring per kategori.
- **Profil siswa**: `/profil/{id}` menampilkan identitas (kelas/jurusan, bio, tautan portofolio) dan daftar karya. Edit di `/profil/edit`.
- **Dasbor**: `/dashboard` (Admin/Super Admin) berisi jumlah karya, pengguna, apresiasi, kategori terpopuler, karya terbaru, karya paling diapresiasi, dan siswa paling aktif.

**Database:** kolom dan tabel baru (`projects.unggulan`, `projects.kategori`, `users.kelas/bio/tautan`, tabel `apresiasi`) dibuat **otomatis** saat halaman pertama dibuka setelah pembaruan. Tidak perlu impor SQL.

| Method | Endpoint | Akses |
|---|---|---|
| GET | /projects/{id} | Semua yang login |
| POST | /projects/{id}/apresiasi | Semua yang login (bukan pemilik karya) |
| POST | /admin/projects/{id}/unggulan | Admin / Super Admin |
| GET | /profil/{id}, /profil/edit | Semua yang login |
| POST | /profil | Pemilik profil |
| GET | /dashboard | Admin / Super Admin |
