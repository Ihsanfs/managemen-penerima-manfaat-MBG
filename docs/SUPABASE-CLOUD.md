# Supabase Cloud sebagai Database Laravel

Aplikasi SPPG Samarang memakai PostgreSQL. Supabase adalah layanan PostgreSQL
terkelola, jadi aplikasi tetap memakai driver `pgsql` Laravel seperti biasa —
tanpa mengganti Eloquent, migration, atau query.

## Informasi project

| Item | Nilai |
|---|---|
| Project ref | `mxuxyvqkowabydsscgth` |
| Dashboard | `https://supabase.com/dashboard/project/mxuxyvqkowabydsscgth` |
| API URL | `https://mxuxyvqkowabydsscgth.supabase.co` |
| DB host | `db.mxuxyvqkowabydsscgth.supabase.co` |
| DB port (direct) | `5432` |
| DB port (pooler) | `6543` |
| DB name | `postgres` |
| DB user | `postgres` |
| Publishable key | `sb_publishable_56VMqvEpnbo5lJu5YnPrLw_PU8jhcLB` |

> Publishable key **bukan** password database. Koneksi Eloquent memakai
> database password, bukan kunci API.

## 1. Dapatkan database password

Di Supabase Dashboard:

```
Settings -> Database -> Database password -> Reset database password
```

Simpan password tersebut. Bisa juga pakai connection string yang sudah
disalin Supabase:

```
postgresql://postgres:<password>@db.mxuxyvqkowabydsscgth.supabase.co:5432/postgres
```

## 2. Isi `.env`

Salin dari `.env.supabase.example`, lalu isi password aslinya:

```dotenv
DB_CONNECTION=pgsql
DB_HOST=db.mxuxyvqkowabydsscgth.supabase.co
DB_PORT=5432
DB_DATABASE=postgres
DB_USERNAME=postgres
DB_PASSWORD=<database password>
DB_SSLMODE=require
```

`DB_SSLMODE=require` wajib karena Supabase memaksa TLS.

## 3. Terapkan

```powershell
php artisan config:clear
php artisan migrate --force
php artisan db:seed --force          # hanya jika ingin data demo
php artisan storage:link
php artisan optimize
```

Verifikasi:

```powershell
php artisan tinker --execute="DB::connection()->getPdo(); echo 'connected';"
```

Lalu buka `/` dan `/admin/login`.

## 4. Connection Pooler (opsional)

Supabase menyediakan pooler di port `6543` (transaction mode). Gunakan ini
bila aplikasi di-deploy dengan banyak worker / traffic tinggi:

```dotenv
DB_PORT=6543
```

Catatan:
- Tetap pakai port `5432` saat menjalankan `php artisan migrate`.
- Transaction mode tidak mendukung prepared statement lintas transaksi;
  Laravel bawaan aman karena `PDO::ATTR_EMULATE_PREPARES` tidak dipakai
  untuk query biasa.

## 5. Cadangan & restore

Supabase melakukan backup otomatis. Backup manual via CLI:

```powershell
$env:SUPABASE_ACCESS_TOKEN="<token>"
supabase db dump --project-ref mxuxyvqkowabydsscgth -f backup.sql
```

Atau lewat `pg_dump`:

```powershell
pg_dump "postgresql://postgres:<password>@db.mxuxyvqkowabydsscgth.supabase.co:5432/postgres" -Fc -f backup.dump
```

## 6. Keamanan

- Jangan pernah commit `.env` (sudah masuk `.gitignore`).
- Simpan database password di secret manager / environment variable server.
- Publishable key aman dipakai di browser, tapi jangan taruh service role key
  di sisi klien.
- Batasi akses IP di Supabase → Settings → Database → Network bila perlu.
