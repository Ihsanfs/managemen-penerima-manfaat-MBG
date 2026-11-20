-- ============================================================
-- Setup awal PostgreSQL untuk aplikasi SPPG Samarang
-- Jalankan sebagai superuser (postgres):
--   psql -U postgres -h 127.0.0.1 -p 5432 -f database/postgres/01-create-database.sql
--
-- Ganti password 'sppg_secret' sebelum dipakai di server produksi.
-- ============================================================

SELECT 'CREATE ROLE sppg LOGIN PASSWORD ''sppg_secret'''
WHERE NOT EXISTS (SELECT FROM pg_roles WHERE rolname = 'sppg')\gexec

SELECT 'CREATE DATABASE sppg_samarang OWNER sppg ENCODING ''UTF8'''
WHERE NOT EXISTS (SELECT FROM pg_database WHERE datname = 'sppg_samarang')\gexec
