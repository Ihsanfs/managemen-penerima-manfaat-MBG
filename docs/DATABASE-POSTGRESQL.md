# Panduan Database PostgreSQL

Dokumen ini menjelaskan cara memakai PostgreSQL untuk aplikasi SPPG Samarang
(Laravel 12 + Filament 3).

## Status di mesin ini

- PostgreSQL **18.3** (Laragon) berjalan di `127.0.0.1:5432`.
- Role: **sppg**, database: **sppg_samarang** (owner `sppg`).
- Skema sudah dimigrasikan dan data demo sudah di-seed:
  - 99 penerima manfaat (96 aktif), 13 lembaga, 21 kategori, 37 laporan harian, 2 user.
- Seluruh test suite **33 passed** saat dijalankan di PostgreSQL.
- `.env` utama **masih SQLite** — belum diubah otomatis, jadi aplikasi yang
  sedang berjalan tidak terganggu.

## Berkas terkait

| Berkas | Fungsi |
|---|---|
| `database/postgres/01-create-database.sql` | Membuat role `sppg` + database `sppg_samarang` (idempoten) |
| `database/migrations/2026_11_20_000000_fix_postgres_enum_constraints.php` | Menghapus CHECK constraint enum lama yang tertinggal saat kolom diubah menjadi string (khusus PostgreSQL) |
| `.env.pgsql.example` | Contoh konfigurasi `.env` untuk PostgreSQL |

## 1. Beralih ke PostgreSQL di lokal (Laragon)

1. Pastikan service **PostgreSQL** menyala (Laragon → Start All, atau
   `Get-Service postgresql-x64-18`).
2. Buat role + database (cukup sekali):
   ```powershell
   & "C:\laragon\bin\postgresql\pgsql\bin\psql.exe" -U postgres -h 127.0.0.1 -f database/postgres/01-create-database.sql
   ```
3. Ubah `.env`:
   ```dotenv
   DB_CONNECTION=pgsql
   DB_HOST=127.0.0.1
   DB_PORT=5432
   DB_DATABASE=sppg_samarang
   DB_USERNAME=sppg
   DB_PASSWORD=sppg_secret
   DB_SSLMODE=prefer
   ```
4. Bersihkan cache konfigurasi:
   ```powershell
   php artisan config:clear
   ```
5. Migrasi + seed (gunakan `--seed` hanya bila ingin data demo):
   ```powershell
   php artisan migrate --seed
   ```
   Untuk memulai dari nol: `php artisan migrate:fresh --seed`
6. Pastikan symlink storage untuk logo:
   ```powershell
   php artisan storage:link
   ```
7. Jalankan aplikasi seperti biasa: `php artisan serve`

> Ganti password `sppg_secret` sebelum dipakai di server. Di server, autentikasi
> PostgreSQL sebaiknya `scram-sha-256` (bukan `trust` seperti konfigurasi lokal Laragon).

## 2. Deploy PostgreSQL di server produksi

1. Install PostgreSQL 15+ dan ekstensi PHP `pdo_pgsql` (`php8.2-pgsql` /
   `php8.3-pgsql` pada Ubuntu).
2. Buat role + database dengan password kuat:
   ```sql
   CREATE ROLE sppg LOGIN PASSWORD 'password-kuat';
   CREATE DATABASE sppg_samarang OWNER sppg ENCODING 'UTF8' TEMPLATE template0;
   ```
3. Isi `.env` produksi:
   ```dotenv
   APP_ENV=production
   APP_DEBUG=false
   APP_URL=https://domain-anda.id
   DB_CONNECTION=pgsql
   DB_HOST=127.0.0.1
   DB_PORT=5432
   DB_DATABASE=sppg_samarang
   DB_USERNAME=sppg
   DB_PASSWORD=password-kuat
   DB_SSLMODE=prefer
   ```
4. Jalankan:
   ```bash
   php artisan migrate --force
   php artisan storage:link
   php artisan optimize
   ```
5. Backup harian (cron):
   ```bash
   pg_dump -U sppg -h 127.0.0.1 -Fc sppg_samarang > /backup/sppg-$(date +\%F).dump
   ```
   Restore: `pg_restore -U sppg -d sppg_samarang --clean /backup/file.dump`

## 3. Catatan kompatibilitas

- **Enum**: PostgreSQL menyimpan `enum` sebagai `VARCHAR` + CHECK constraint.
  Migration `change_*_to_string` tidak otomatis menghapus constraint tersebut,
  sehingga nilai seperti `jenjang`/`smp` ditolak. Migration
  `2026_11_20_000000_fix_postgres_enum_constraints` memperbaikinya.
- **Pencarian**: `LIKE` di PostgreSQL bersifat **case-sensitive** (MySQL tidak).
  Filament 3 memakai `whereLike` sehingga umumnya sudah case-insensitive
  (`ILIKE`), tetapi bila ada pencarian manual dengan `LIKE`, pertimbangkan
  `whereLike($col, $value, caseSensitive: false)`.
- **JSON**: kolom `kategori_json` dan `perubahan` bertipe `json`; cast Eloquent
  tetap berfungsi normal.
- **Timezone**: aplikasi memakai `Asia/Jakarta`; biarkan server database di UTC.
- **Unique `daily_reports.tanggal`** tetap berlaku; service sudah memakai pola
  check-and-update sehingga aman.

## 4. Memindahkan data lama ke PostgreSQL

- **Dari MySQL**: paling mudah pakai `pgloader` (schema dibuat oleh migration,
  lalu data di-copy), atau ekspor CSV per tabel.
- **Dari SQLite**: buat koneksi sementara ke file SQLite lalu salin tabel
  berurutan sesuai foreign key (categories → institutions → beneficiaries →
  daily_reports → daily_report_details → beneficiary_histories → audit_logs).

Beri tahu bila ingin dibuatkan perintah artisan khusus untuk menyalin data lama
secara otomatis.

## 5. Kembali ke SQLite/MySQL

Ubah kembali `.env` (`DB_CONNECTION=sqlite` atau `mysql`), lalu:

```powershell
php artisan config:clear
php artisan migrate
```
