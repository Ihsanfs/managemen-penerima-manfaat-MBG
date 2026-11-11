# Aplikasi Penerima Manfaat SPPG Samarang

Aplikasi web untuk mencatat, memantau, dan mengarsipkan data penerima manfaat SPPG Samarang. Dibangun dengan **Laravel 12**, **Filament 3**, dan **MySQL**, mengikuti alur kerja spreadsheet referensi:

- Data **LIVE** dihitung langsung dari daftar penerima manfaat aktif per lembaga.
- **Simpan** memotret data LIVE ke riwayat harian, rincian per lembaga, dan log audit.
- **ARSIP** menampilkan data tanggal lampau melalui kalender.
- **Laporan mingguan** memantau status update Senin–Jumat.
- **Snapshot otomatis** setiap hari kerja pukul **01:00 WIB** (carry-forward).

## Kebutuhan

- PHP 8.2+ dengan ekstensi `pdo_mysql`, `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `json`, `zip`, `gd`, `fileinfo`
- Composer 2
- MySQL 8 / MariaDB
- Node.js 18+ (opsional, untuk aset Vite)
- Library `phpoffice/phpspreadsheet` untuk Excel dan `barryvdh/laravel-dompdf` untuk PDF (terpasang lewat Composer)

## Instalasi



composer install
copy .env.example .env
php artisan key:generate



php artisan migrate --seed
```

Seeder membuat:

- 1 profil SPPG, 13 lembaga (7 sekolah + 6 posyandu)
- 99 penerima manfaat (96 aktif + 3 non-aktif/pindah)
- Riwayat harian contoh untuk hari kerja terakhir
- 2 akun pengguna

Seeder aman dijalankan ulang: data penerima dan riwayat tidak diduplikasi.

## Menjalankan Aplikasi

```bash
php artisan serve
```

Buka <http://127.0.0.1:8000/admin>.

Untuk menjalankan penjadwal snapshot otomatis di lingkungan lokal:

```bash
php artisan schedule:work
```

Di server produksi, tambahkan cron:

```cron
* * * * * cd /path/ke/project && php artisan schedule:run >> /dev/null 2>&1
```

Jadwal: `pm:auto-snapshot` setiap Senin–Jumat pukul 01:00 `Asia/Jakarta`.

## Akun Bawaan

| Peran   | Email               | Kata Sandi |
| ------- | ------------------- | ---------- |
| Admin   | `admin@sppg.test`   | `password` |
| Petugas | `petugas@sppg.test` | `password` |

- **Admin**: seluruh menu, termasuk pengguna, log audit, dan profil SPPG.
- **Petugas**: data operasional dan laporan; menu khusus admin tidak dapat diakses.

Segera ganti kata sandi akun bawaan sebelum digunakan di produksi.

## Menu Aplikasi

| Menu | Fungsi |
| --- | --- |
| **Dasbor** | Statistik penerima, komposisi kategori, dan riwayat simpan terbaru |
| **Data Penerima Manfaat** | Tabel LIVE per sekolah/posyandu, kalender arsip, simpan hari ini atau tanggal tertentu, cetak |
| **Lembaga** | CRUD sekolah dan posyandu |
| **Kategori** | Manajemen kategori penerima manfaat dan lembaga (kode, nama, jenis, grup laporan, aktif/nonaktif) |
| **Penerima Manfaat** | CRUD data penerima, filter kategori/lembaga/status, alasan wajib saat mengubah/menghapus |
| **Impor & Ekspor** | Template Excel, impor penerima/lembaga, ekspor penerima/lembaga/riwayat harian |
| **Riwayat Harian** | Daftar snapshot harian dan rincian per lembaga |
| **Laporan Mingguan** | Status Senin–Jumat, 20 hari kerja terakhir, perubahan total |
| **Log Audit** | Riwayat simpan/update/otomatis beserta selisih total |
| **Riwayat Perubahan** | Jejak tambah/ubah/hapus penerima manfaat: alasan, tanggal, hari, pengguna, nilai sebelum/sesudah |
| **Profil SPPG** | Identitas SPPG untuk tampilan dan cetak |
| **Pengguna** | Manajemen akun dan peran (admin saja) |
| **Manual Book** | Ringkasan panduan dan unduhan PDF manual lengkap |

## Impor & Ekspor Excel

Buka **Data Master → Impor & Ekspor**.

- **Template Penerima Manfaat**: unduh template `.xlsx` berisi sheet data, petunjuk, referensi lembaga, dan daftar kategori.
- **Template Lembaga**: unduh template `.xlsx` berisi sheet data, petunjuk, dan referensi nilai.
- **Impor** menerima `.xlsx`, `.xls`, dan `.csv` (maksimal 10 MB). Hasil menampilkan jumlah dibuat, diperbarui, gagal, serta pesan error per baris.
- **Ekspor** menghasilkan file Excel untuk data penerima, lembaga, atau seluruh riwayat harian beserta rincian per lembaga.

### Kolom wajib impor penerima manfaat

`nama_lembaga`, `nama`, `kategori`, `jenis_kelamin`.

Kolom opsional: `nik`, `nisn`, `kelas`, `status`, `bulan`, `tahun`, `keterangan`.
Pencocokan data: **NIK → NISN → lembaga + nama + kategori + kelas**. Baris yang belum cocok akan dibuat.

### Kolom wajib impor lembaga

`nama`, `tipe`. Kolom opsional: `jenjang`, `alamat`, `penanggung_jawab`, `telepon`, `urutan`, `is_active`, `kategori_lembaga`.
Pencocokan berdasarkan nama lembaga tanpa membedakan huruf besar/kecil.

## Manajemen Kategori

Menu **Data Master → Kategori** digunakan untuk mengelola kategori secara manual:

- **Kategori Penerima Manfaat**: TK / PAUD, SD, SMP, SMK, Guru, Balita, Ibu Hamil, Ibu Menyusui, Kader. Digunakan pada form dan filter Penerima Manfaat, serta pengelompokan laporan harian.
- **Kategori Lembaga**: TK / PAUD, SD, SMP, SMK, Posyandu. Digunakan pada data Lembaga.
- **Jenjang Sekolah**: TK / PAUD, SD, SMP, SMA, SMK, SLB, Lainnya. Digunakan sebagai opsi Jenjang pada data Lembaga. Dapat ditambah, diedit, atau dinonaktifkan kapan saja melalui menu ini.
- Setiap kategori memiliki **kode** (unik per jenis), **nama**, **jenis** (penerima/lembaga/jenjang), **grup laporan** (opsional, untuk penerima), **urutan**, dan status **aktif**.
- Kategori yang sudah digunakan oleh penerima atau lembaga tidak dapat dihapus; nonaktifkan saja agar tidak muncul sebagai pilihan.
- Impor penerima memvalidasi kategori terhadap daftar kategori aktif di menu Kategori.
- Impor lembaga memvalidasi jenjang terhadap daftar jenis jenjang aktif di menu Kategori.

## Jejak Perubahan Penerima Manfaat

Setiap perubahan data penerima manfaat dicatat otomatis pada menu **Laporan → Riwayat Perubahan** dan tab **Riwayat Perubahan** di halaman edit penerima.

Yang dicatat: aksi (dibuat/diperbarui/dihapus), nama penerima, lembaga, kategori, tanggal, hari, waktu, pengguna, alasan, serta nilai sebelum dan sesudah per kolom.

- Saat mengedit, kolom **Alasan Perubahan** wajib diisi.
- Saat menghapus, alasan juga wajib diisi pada dialog konfirmasi.
- Impor otomatis mencatat nama file sebagai bagian alasan.
- Riwayat tidak dapat diubah/dihapus dari antarmuka agar jejak audit tetap utuh.

## Manual Book PDF

Halaman **Pengaturan → Manual Book** menyediakan ringkasan alur kerja dan tombol unduh PDF.
File PDF juga tersedia langsung di `public/manual-book-sppg-samarang.pdf`.

Buat ulang manual setelah ada perubahan besar:

```bash
php artisan manual:generate
```

## Keamanan

- Header keamanan diterapkan secara global: `X-Content-Type-Options: nosniff`, `X-Frame-Options: SAMEORIGIN`, `Referrer-Policy`, `Permissions-Policy`, dan `X-Robots-Tag: noindex, nofollow, noarchive`. Header `X-Powered-By` dihapus.
- Route unduhan (template, ekspor, manual book, cetak PDF) dilindungi throttling maksimal 30 request per menit dan memerlukan login.
- Login Filament sudah rate-limit maksimal 5 percobaan.
- NIK dan NISN opsional (boleh kosong). Jika diisi, harus 16 digit (NIK) atau 10 digit (NISN), unik, dan ditampilkan melalui pop-up modal jika gagal validasi.

## Perintah Bermanfaat

```bash
# Snapshot otomatis untuk hari ini (dilewati bila sudah ada)
php artisan pm:auto-snapshot

# Snapshot untuk tanggal tertentu
php artisan pm:auto-snapshot 2026-11-04

# Melihat jadwal
php artisan schedule:list

# Membuat ulang manual book PDF
php artisan manual:generate

# Menjalankan pengujian
php artisan test
```

## Struktur Data

- `institutions` — sekolah/posyandu, jenjang, urutan tampil
- `categories` — kategori master penerima manfaat, lembaga, dan jenjang sekolah (kode, nama, jenis, grup, urutan, aktif)
- `beneficiaries` — data penerima, kategori, kelas, status
- `daily_reports` — total per kategori untuk satu tanggal, termasuk `kategori_json`
- `daily_report_details` — rincian jumlah per lembaga untuk satu tanggal, termasuk `kategori_json`
- `audit_logs` — jejak simpan/update/otomatis laporan harian
- `beneficiary_histories` — jejak perubahan penerima manfaat beserta alasan dan nilai sebelum/sesudah
- `sppg_profiles` — identitas SPPG

Kategori laporan: `tk_paud`, `sd_1_3`, `sd_4_6`, `smk` (termasuk SMP), `guru`, `balita`, `bumil`, `busui`, `kader`.

## Catatan

- Total penerima manfaat mengikuti referensi: **96 orang** (TK/PAUD 12, SD 1–3 23, SD 4–6 6, SMK 6, Guru 8, Balita 18, Bumil 6, Busui 12, Kader 5).
- Simpan selalu memotret data LIVE saat ini, termasuk ketika menyimpan untuk tanggal lampau.
- Tanggal akhir pekan tidak dibuatkan snapshot otomatis.
- Zona waktu aplikasi: `Asia/Jakarta`.
