<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Manual Book SPPG Samarang</title>
    <style>
        @page {
            margin: 23mm 15mm 20mm 15mm;
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: "DejaVu Sans", sans-serif;
            font-size: 9.3pt;
            line-height: 1.55;
            color: #1f2937;
            margin: 0;
        }

        h1 {
            font-size: 17pt;
            color: #065f46;
            margin: 0 0 9pt;
            padding-bottom: 4pt;
            border-bottom: 2pt solid #10b981;
        }

        h2 {
            font-size: 12.2pt;
            color: #065f46;
            margin: 14pt 0 5pt;
        }

        h3 {
            font-size: 10.2pt;
            color: #111827;
            margin: 10pt 0 4pt;
        }

        p {
            margin: 0 0 6pt;
            text-align: justify;
        }

        ul, ol {
            margin: 0 0 8pt 15pt;
            padding: 0;
        }

        li {
            margin-bottom: 3pt;
        }

        .cover {
            height: 250mm;
            page-break-after: always;
        }

        .cover-band {
            background: #065f46;
            color: #ffffff;
            padding: 22pt 24pt;
            border-radius: 6pt;
        }

        .cover-eyebrow {
            font-size: 8pt;
            letter-spacing: 2pt;
            text-transform: uppercase;
            color: #a7f3d0;
            font-weight: bold;
        }

        .cover-title {
            margin-top: 10pt;
            font-size: 27pt;
            line-height: 1.12;
            font-weight: bold;
        }

        .cover-subtitle {
            margin-top: 8pt;
            font-size: 11.5pt;
            line-height: 1.45;
            color: #d1fae5;
        }

        .cover-strip {
            height: 7pt;
            background: #34d399;
            border-radius: 4pt;
            margin-top: 5pt;
        }

        .cover-meta {
            margin-top: 24pt;
            width: 100%;
            border-collapse: collapse;
        }

        .cover-meta td {
            border: 0;
            padding: 4pt 0;
            font-size: 9.3pt;
            vertical-align: top;
        }

        .cover-meta .label {
            width: 34mm;
            color: #6b7280;
            font-weight: bold;
        }

        .cover-note {
            margin-top: 28pt;
            border-left: 4pt solid #10b981;
            background: #ecfdf5;
            padding: 9pt 11pt;
            font-size: 8.8pt;
            color: #065f46;
            line-height: 1.55;
        }

        .cover-footer {
            margin-top: 30pt;
            font-size: 8pt;
            color: #6b7280;
            text-align: center;
        }

        .toc {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8pt;
        }

        .toc td {
            border: 0;
            border-bottom: 1pt dashed #d1d5db;
            padding: 7pt 4pt;
            vertical-align: top;
            font-size: 9pt;
        }

        .toc .num {
            width: 12mm;
            color: #059669;
            font-weight: bold;
            font-size: 10pt;
        }

        .toc .desc {
            color: #6b7280;
            font-size: 8.3pt;
            margin-top: 1pt;
        }

        .page-break {
            page-break-before: always;
        }

        table.data {
            width: 100%;
            border-collapse: collapse;
            margin: 5pt 0 11pt;
        }

        table.data th {
            background: #ecfdf5;
            color: #065f46;
            border: 1pt solid #d1d5db;
            padding: 4pt 5pt;
            text-align: left;
            font-size: 8.2pt;
        }

        table.data td {
            border: 1pt solid #e5e7eb;
            padding: 4pt 5pt;
            font-size: 8.3pt;
            vertical-align: top;
        }

        .note {
            border-left: 4pt solid #10b981;
            background: #ecfdf5;
            padding: 7pt 9pt;
            margin: 6pt 0 10pt;
            font-size: 8.6pt;
            color: #065f46;
            line-height: 1.55;
        }

        .warn {
            border-left-color: #f59e0b;
            background: #fffbeb;
            color: #92400e;
        }

        .danger {
            border-left-color: #ef4444;
            background: #fef2f2;
            color: #991b1b;
        }

        .flow {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0 4pt;
            margin: 6pt 0 10pt;
        }

        .flow td {
            border: 1pt solid #d1fae5;
            background: #f0fdf4;
            padding: 6pt 8pt;
            font-size: 8.6pt;
            vertical-align: middle;
        }

        .flow .no {
            width: 10mm;
            text-align: center;
            font-weight: bold;
            color: #047857;
            font-size: 10pt;
            border-right: 0;
        }

        .flow .title {
            font-weight: bold;
            color: #065f46;
        }

        .flow .arrow {
            border: 0;
            background: transparent;
            text-align: center;
            color: #10b981;
            font-size: 11pt;
            padding: 0;
            width: 8mm;
        }

        .tag {
            display: inline-block;
            padding: 1pt 5pt;
            border-radius: 8pt;
            background: #ecfdf5;
            color: #047857;
            font-size: 7.3pt;
            font-weight: bold;
        }

        .tag-warn {
            background: #fffbeb;
            color: #b45309;
        }

        .tag-danger {
            background: #fef2f2;
            color: #b91c1c;
        }

        .two-col {
            width: 100%;
            border-collapse: collapse;
        }

        .two-col td {
            border: 0;
            width: 50%;
            vertical-align: top;
            padding: 0 5pt 0 0;
        }

        .two-col td + td {
            padding: 0 0 0 5pt;
        }

        .small {
            font-size: 8pt;
            color: #6b7280;
        }

        .step-list {
            margin: 0 0 10pt 0;
            padding: 0;
            list-style: none;
        }

        .step-list li {
            margin-bottom: 6pt;
            padding-left: 18pt;
            position: relative;
        }

        .step-list .num {
            position: absolute;
            left: 0;
            top: 0.5pt;
            display: inline-block;
            width: 12pt;
            height: 12pt;
            line-height: 12pt;
            text-align: center;
            border-radius: 6pt;
            background: #059669;
            color: #ffffff;
            font-size: 7pt;
            font-weight: bold;
        }

        .footer {
            margin-top: 16pt;
            border-top: 1pt solid #d1d5db;
            padding-top: 5pt;
            font-size: 7.5pt;
            color: #9ca3af;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="cover">
        <div class="cover-band">
            <div class="cover-eyebrow">Manual Book - Versi 1.0</div>
            <div class="cover-title">Aplikasi Penerima Manfaat<br>SPPG Samarang</div>
            <div class="cover-subtitle">
                Panduan penggunaan, alur kerja harian, laporan, impor &amp; ekspor Excel,
                serta jejak perubahan data penerima manfaat.
            </div>
        </div>
        <div class="cover-strip"></div>

        <table class="cover-meta">
            <tr>
                <td class="label">Instansi</td>
                <td>{{ $profile->nama }}</td>
            </tr>
            <tr>
                <td class="label">Wilayah</td>
                <td>
                    {{ $profile->kecamatan ?: '-' }}
                    @if ($profile->kabupaten), {{ $profile->kabupaten }}@endif
                    @if ($profile->provinsi), {{ $profile->provinsi }}@endif
                </td>
            </tr>
            <tr>
                <td class="label">Alamat</td>
                <td>{{ $profile->alamat ?: '-' }}</td>
            </tr>
            <tr>
                <td class="label">Kontak</td>
                <td>
                    {{ $profile->telepon ?: '-' }}
                    @if ($profile->email) - {{ $profile->email }}@endif
                </td>
            </tr>
            <tr>
                <td class="label">Penanggung Jawab</td>
                <td>{{ $profile->kepala_sppg ?: '-' }}</td>
            </tr>
            <tr>
                <td class="label">Tanggal Cetak</td>
                <td>{{ $dicetak }}</td>
            </tr>
        </table>

        <div class="cover-note">
            <strong>Ringkasan data saat manual dibuat:</strong>
            {{ number_format($jumlahPenerima, 0, ',', '.') }} penerima manfaat
            ({{ number_format($jumlahPenerimaAktif, 0, ',', '.') }} aktif),
            {{ number_format($jumlahLembaga, 0, ',', '.') }} lembaga, dan
            {{ number_format($jumlahLaporan, 0, ',', '.') }} laporan harian tersimpan.
        </div>

        <div class="cover-footer">
            Dokumen ini dihasilkan otomatis oleh aplikasi. Perbarui manual secara berkala
            agar sesuai dengan kondisi data terbaru.
        </div>
    </div>

    <h1>Daftar Isi</h1>
    <table class="toc">
        <tr>
            <td class="num">01</td>
            <td>
                <strong>Mengenal aplikasi dan peran pengguna</strong>
                <div class="desc">Tujuan aplikasi, fitur utama, hak akses admin dan petugas.</div>
            </td>
        </tr>
        <tr>
            <td class="num">02</td>
            <td>
                <strong>Login, dashboard, dan navigasi</strong>
                <div class="desc">Cara masuk, membaca statistik, dan berpindah menu.</div>
            </td>
        </tr>
        <tr>
            <td class="num">03</td>
            <td>
                <strong>Alur operasional harian</strong>
                <div class="desc">Data LIVE, penyimpanan snapshot, arsip kalender, dan carry-forward otomatis.</div>
            </td>
        </tr>
        <tr>
            <td class="num">04</td>
            <td>
                <strong>Data master lembaga dan penerima manfaat</strong>
                <div class="desc">Menambah, mengubah, menghapus, dan aturan alasan perubahan.</div>
            </td>
        </tr>
        <tr>
            <td class="num">05</td>
            <td>
                <strong>Laporan mingguan, arsip, dan log audit</strong>
                <div class="desc">Rekap Senin-Jumat, 20 hari kerja terakhir, perbedaan log audit dan riwayat perubahan.</div>
            </td>
        </tr>
        <tr>
            <td class="num">06</td>
            <td>
                <strong>Impor dan ekspor Excel</strong>
                <div class="desc">Template, kolom wajib, pencocokan data, hasil impor, dan daftar ekspor.</div>
            </td>
        </tr>
        <tr>
            <td class="num">07</td>
            <td>
                <strong>Jejak perubahan penerima manfaat</strong>
                <div class="desc">Alasan, tanggal, hari, pengguna, nilai sebelum dan sesudah, serta cara menelusuri.</div>
            </td>
        </tr>
        <tr>
            <td class="num">08</td>
            <td>
                <strong>Penjadwalan otomatis dan pemeliharaan</strong>
                <div class="desc">Scheduler 01.00 WIB, perintah manual, pencadangan, dan pemeriksaan rutin.</div>
            </td>
        </tr>
        <tr>
            <td class="num">09</td>
            <td>
                <strong>Penanganan masalah dan glosarium</strong>
                <div class="desc">Kendala umum saat impor, laporan, dan scheduler, beserta solusinya.</div>
            </td>
        </tr>
    </table>

    <div class="note">
        <strong>Cara membaca manual ini:</strong> ikuti bab secara berurutan untuk memahami aplikasi
        dari awal. Jika hanya membutuhkan tugas tertentu, gunakan daftar isi di atas atau buka
        bagian yang relevan. Istilah teknis dijelaskan pada bab terakhir.
    </div>

    <div class="page-break"></div>

    <h1>01. Mengenal Aplikasi dan Peran Pengguna</h1>

    <p>
        Aplikasi Penerima Manfaat SPPG Samarang digunakan untuk mencatat data penerima manfaat
        dari sekolah dan posyandu, menghitung jumlah harian per kategori, menyimpan arsip harian,
        serta menyusun laporan mingguan. Aplikasi menggantikan pencatatan manual pada spreadsheet
        agar data lebih konsisten, dapat ditelusuri, dan siap diekspor.
    </p>

    <h2>Fitur utama</h2>
    <table class="data">
        <tr>
            <th style="width:34%">Fitur</th>
            <th>Kegunaan</th>
        </tr>
        <tr>
            <td>Data Penerima Manfaat (LIVE)</td>
            <td>Menampilkan data penerima aktif hari ini per sekolah dan posyandu, lengkap dengan total per kategori.</td>
        </tr>
        <tr>
            <td>Snapshot harian</td>
            <td>Menyimpan kondisi data pada tanggal tertentu sebagai arsip yang tidak berubah walaupun data master diperbarui.</td>
        </tr>
        <tr>
            <td>Arsip kalender</td>
            <td>Membuka data tanggal lampau melalui kalender. Jika tanggal belum tersimpan, sistem menampilkan data terakhir sebagai fallback.</td>
        </tr>
        <tr>
            <td>Laporan mingguan</td>
            <td>Rekap Senin-Jumat, status manual/otomatis, rata-rata total, dan 20 hari kerja terakhir.</td>
        </tr>
        <tr>
            <td>Data master</td>
            <td>Mengelola lembaga, penerima manfaat, serta impor dan ekspor Excel.</td>
        </tr>
        <tr>
            <td>Jejak perubahan</td>
            <td>Mencatat penambahan, perubahan, dan penghapusan penerima manfaat beserta alasan, tanggal, hari, dan pengguna.</td>
        </tr>
        <tr>
            <td>Log audit laporan</td>
            <td>Mencatat riwayat penyimpanan snapshot harian, termasuk selisih total sebelum dan sesudah.</td>
        </tr>
        <tr>
            <td>Snapshot otomatis</td>
            <td>Membuat carry-forward pada hari kerja pukul 01.00 WIB jika data hari tersebut belum ada.</td>
        </tr>
    </table>

    <h2>Peran pengguna</h2>
    <table class="data">
        <tr>
            <th style="width:22%">Peran</th>
            <th>Akses</th>
        </tr>
        <tr>
            <td><strong>Admin</strong></td>
            <td>
                Seluruh fitur: data operasional, data master, impor/ekspor, laporan,
                log audit, riwayat perubahan, pengelolaan pengguna, dan profil SPPG.
            </td>
        </tr>
        <tr>
            <td><strong>Petugas</strong></td>
            <td>
                Data operasional, data master, impor/ekspor, laporan, dan riwayat perubahan.
                Menu pengguna, log audit, dan profil SPPG hanya untuk admin.
            </td>
        </tr>
    </table>

    <div class="note">
        Akun dibuat dan diatur oleh admin melalui menu <strong>Pengaturan -&gt; Pengguna</strong>.
        Gunakan satu akun per petugas agar jejak perubahan dapat dipertanggungjawabkan.
    </div>

    <div class="page-break"></div>

    <h1>02. Login, Dashboard, dan Navigasi</h1>

    <h2>Masuk ke aplikasi</h2>
    <ol class="step-list">
        <li><span class="num">1</span>Buka alamat aplikasi, misalnya <strong>http://127.0.0.1:8000/admin</strong> pada server lokal.</li>
        <li><span class="num">2</span>Masukkan email dan kata sandi yang diberikan admin.</li>
        <li><span class="num">3</span>Klik tombol masuk. Setelah berhasil, dashboard akan terbuka.</li>
    </ol>

    <div class="warn note">
        Jangan membagikan kata sandi. Jika lupa kata sandi, hubungi admin untuk mengatur ulang
        melalui menu <strong>Pengaturan -&gt; Pengguna</strong>.
    </div>

    <h2>Membaca dashboard</h2>
    <table class="data">
        <tr>
            <th style="width:30%">Bagian</th>
            <th>Informasi yang ditampilkan</th>
        </tr>
        <tr>
            <td>Kartu statistik</td>
            <td>Jumlah penerima aktif, jumlah lembaga, total laporan harian, dan informasi ringkas lainnya.</td>
        </tr>
        <tr>
            <td>Grafik kategori</td>
            <td>Sebaran penerima manfaat per kategori: TK/PAUD, SD, SMK, guru, balita, bumil, busui, dan kader.</td>
        </tr>
        <tr>
            <td>Riwayat terbaru</td>
            <td>Daftar laporan harian terakhir yang sudah disimpan.</td>
        </tr>
    </table>

    <h2>Struktur menu</h2>
    <table class="data">
        <tr>
            <th style="width:24%">Grup menu</th>
            <th>Menu</th>
        </tr>
        <tr>
            <td>Operasional</td>
            <td>Data Penerima Manfaat (halaman LIVE dan arsip kalender).</td>
        </tr>
        <tr>
            <td>Data Master</td>
            <td>Lembaga, Penerima Manfaat, dan Impor &amp; Ekspor.</td>
        </tr>
        <tr>
            <td>Laporan</td>
            <td>Laporan Mingguan, Riwayat Harian, Log Audit, dan Riwayat Perubahan.</td>
        </tr>
        <tr>
            <td>Pengaturan</td>
            <td>Profil SPPG, Pengguna, dan Manual Book.</td>
        </tr>
    </table>

    <div class="page-break"></div>

    <h1>03. Alur Operasional Harian</h1>

    <h2>Alur lengkap dari data master sampai laporan</h2>
    <table class="flow">
        <tr>
            <td class="no">1</td>
            <td>
                <span class="title">Data master</span><br>
                Pastikan lembaga dan penerima manfaat sudah benar. Perubahan data wajib disertai alasan.
            </td>
        </tr>
        <tr>
            <td colspan="2" class="arrow">v</td>
        </tr>
        <tr>
            <td class="no">2</td>
            <td>
                <span class="title">Halaman LIVE</span><br>
                Sistem menghitung otomatis data penerima aktif hari ini per lembaga dan kategori.
            </td>
        </tr>
        <tr>
            <td colspan="2" class="arrow">v</td>
        </tr>
        <tr>
            <td class="no">3</td>
            <td>
                <span class="title">Simpan snapshot</span><br>
                Klik Simpan Data Hari Ini untuk mengunci data tanggal tersebut sebagai arsip.
            </td>
        </tr>
        <tr>
            <td colspan="2" class="arrow">v</td>
        </tr>
        <tr>
            <td class="no">4</td>
            <td>
                <span class="title">Arsip kalender</span><br>
                Buka tanggal lampau melalui kalender. Data yang belum tersimpan memakai data terakhir sebagai fallback.
            </td>
        </tr>
        <tr>
            <td colspan="2" class="arrow">v</td>
        </tr>
        <tr>
            <td class="no">5</td>
            <td>
                <span class="title">Laporan mingguan</span><br>
                Rekap Senin-Jumat, status manual/otomatis, perubahan total, dan 20 hari kerja terakhir.
            </td>
        </tr>
        <tr>
            <td colspan="2" class="arrow">v</td>
        </tr>
        <tr>
            <td class="no">6</td>
            <td>
                <span class="title">Ekspor dan arsip</span><br>
                Unduh Excel data penerima, lembaga, atau riwayat harian untuk pelaporan dan pencadangan.
            </td>
        </tr>
    </table>

    <h2>Langkah menyimpan data harian</h2>
    <ol class="step-list">
        <li><span class="num">1</span>Buka <strong>Operasional -&gt; Data Penerima Manfaat</strong>.</li>
        <li><span class="num">2</span>Pastikan tanggal yang tampil adalah hari ini dan status halaman menunjukkan <strong>LIVE</strong>.</li>
        <li><span class="num">3</span>Periksa total per lembaga dan total keseluruhan pada panel ringkasan.</li>
        <li><span class="num">4</span>Klik <strong>Simpan Data Hari Ini</strong>, lalu konfirmasi.</li>
        <li><span class="num">5</span>Setelah berhasil, data masuk ke arsip dan muncul pada laporan mingguan serta riwayat harian.</li>
    </ol>

    <div class="note">
        <strong>Snapshot berbeda dengan data LIVE.</strong> Data LIVE selalu mengikuti data master terbaru.
        Snapshot adalah salinan permanen pada tanggal penyimpanan. Karena itu, perubahan data master
        setelah tanggal tersebut tidak mengubah arsip yang sudah tersimpan.
    </div>

    <h2>Arsip dan fallback</h2>
    <ul>
        <li>Pilih tanggal pada kalender. Tanggal dengan titik hijau menandakan laporan sudah tersimpan.</li>
        <li>Jika tanggal belum memiliki laporan, sistem menampilkan data terakhir sebelum tanggal tersebut sebagai fallback.</li>
        <li>Tanggal yang lebih besar dari hari ini tidak dapat dipilih.</li>
        <li>Untuk menyimpan data pada tanggal lampau, gunakan tombol simpan tanggal dan pilih tanggal yang diinginkan.</li>
    </ul>

    <h2>Carry-forward otomatis</h2>
    <p>
        Pada hari kerja, pukul 01.00 WIB, scheduler memeriksa apakah laporan hari tersebut sudah ada.
        Jika belum ada, sistem menyalin snapshot terakhir sebagai data carry-forward dengan sumber
        <span class="tag tag-warn">Otomatis</span>. Sabtu dan Minggu tidak diproses.
    </p>

    <div class="page-break"></div>

    <h1>04. Data Master Lembaga dan Penerima Manfaat</h1>

    <h2>Data lembaga</h2>
    <table class="data">
        <tr>
            <th style="width:26%">Kolom</th>
            <th>Keterangan</th>
        </tr>
        <tr><td>Nama</td><td>Nama sekolah atau posyandu. Harus unik.</td></tr>
        <tr><td>Tipe</td><td><strong>sekolah</strong> atau <strong>posyandu</strong>.</td></tr>
        <tr><td>Jenjang</td><td>tk_paud, sd, atau smk. Kosongkan untuk posyandu.</td></tr>
        <tr><td>Alamat</td><td>Alamat lembaga (opsional).</td></tr>
        <tr><td>Penanggung jawab</td><td>Nama kepala sekolah atau penanggung jawab posyandu (opsional).</td></tr>
        <tr><td>Telepon</td><td>Nomor kontak (opsional).</td></tr>
        <tr><td>Urutan</td><td>Menentukan urutan tampilan pada halaman LIVE dan laporan.</td></tr>
        <tr><td>Status aktif</td><td>Lembaga nonaktif tidak dihitung pada laporan harian.</td></tr>
    </table>

    <h2>Kategori penerima manfaat</h2>
    <table class="data">
        <tr>
            <th style="width:20%">Kode</th>
            <th style="width:26%">Label</th>
            <th>Kelas yang wajib diisi</th>
        </tr>
        <tr><td>tk_paud</td><td>TK / PAUD</td><td>Tidak ada.</td></tr>
        <tr><td>sd</td><td>SD</td><td>Kelas 1 sampai 6.</td></tr>
        <tr><td>smk</td><td>SMK</td><td>Kelas 1-3 atau X, XI, XII.</td></tr>
        <tr><td>guru</td><td>Guru</td><td>Tidak ada.</td></tr>
        <tr><td>balita</td><td>Balita</td><td>Tidak ada.</td></tr>
        <tr><td>bumil</td><td>Ibu Hamil</td><td>Tidak ada.</td></tr>
        <tr><td>busui</td><td>Ibu Menyusui</td><td>Tidak ada.</td></tr>
        <tr><td>kader</td><td>Kader Posyandu</td><td>Tidak ada.</td></tr>
    </table>

    <h2>Status penerima manfaat</h2>
    <table class="data">
        <tr>
            <th style="width:22%">Status</th>
            <th>Arti dan pengaruh</th>
        </tr>
        <tr><td><span class="tag">aktif</span></td><td>Dihitung pada snapshot dan laporan harian.</td></tr>
        <tr><td><span class="tag tag-warn">non_aktif</span></td><td>Tidak dihitung, tetapi data tetap tersimpan.</td></tr>
        <tr><td><span class="tag tag-danger">pindah</span></td><td>Penerima pindah atau keluar. Tidak dihitung pada laporan.</td></tr>
    </table>

    <h2>Menambah dan mengubah penerima manfaat</h2>
    <ol class="step-list">
        <li><span class="num">1</span>Buka <strong>Data Master -&gt; Penerima Manfaat</strong>.</li>
        <li><span class="num">2</span>Klik <strong>Tambah</strong>, lalu isi lembaga, nama, jenis kelamin, kategori, dan kelas bila diperlukan.</li>
        <li><span class="num">3</span>Untuk mengubah data, klik <strong>Edit</strong> pada baris yang dipilih.</li>
        <li><span class="num">4</span>Isi <strong>Alasan Perubahan</strong> dengan jelas, misalnya: perbaikan NIK, perubahan status karena lulus, atau koreksi nama.</li>
        <li><span class="num">5</span>Klik <strong>Simpan</strong>. Perubahan langsung tercatat di Riwayat Perubahan.</li>
    </ol>

    <div class="warn note">
        Alasan perubahan wajib diisi saat mengedit. Untuk penghapusan, alasan juga wajib diisi
        pada dialog konfirmasi. Data yang dihapus tetap meninggalkan jejak pada riwayat.
    </div>

    <h2>Menghapus data</h2>
    <p>
        Hapus hanya data yang benar-benar tidak diperlukan. Untuk penerima yang sudah tidak aktif,
        sebaiknya ubah status menjadi <strong>non_aktif</strong> atau <strong>pindah</strong> agar
        riwayat dan laporan lama tetap utuh. Penghapusan hanya tersedia untuk admin.
    </p>

    <div class="page-break"></div>

    <h1>05. Laporan Mingguan, Arsip, dan Log Audit</h1>

    <h2>Laporan mingguan</h2>
    <p>
        Halaman <strong>Laporan -&gt; Laporan Mingguan</strong> menampilkan rekap Senin sampai Jumat.
        Gunakan tombol minggu lalu, minggu ini, dan minggu depan untuk berpindah periode. Tombol
        cetak dapat digunakan untuk mencetak atau menyimpan halaman sebagai PDF dari browser.
    </p>
    <table class="data">
        <tr>
            <th style="width:30%">Bagian</th>
            <th>Isi</th>
        </tr>
        <tr><td>Status Senin-Jumat</td><td>Total per hari, status manual/otomatis/belum ada, dan tanggal.</td></tr>
        <tr><td>20 hari kerja terakhir</td><td>Total harian dan perubahan dibanding hari kerja sebelumnya.</td></tr>
        <tr><td>Statistik mingguan</td><td>Jumlah hari sudah diupdate, jumlah hari otomatis, dan rata-rata total.</td></tr>
    </table>

    <h2>Riwayat harian</h2>
    <p>
        Menu <strong>Laporan -&gt; Riwayat Harian</strong> berisi daftar snapshot yang sudah tersimpan.
        Buka detail untuk melihat rincian per lembaga. Gunakan tombol ekspor untuk mengunduh seluruh
        riwayat dalam satu file Excel.
    </p>

    <h2>Log audit laporan</h2>
    <p>
        Log audit mencatat aktivitas penyimpanan snapshot harian: siapa yang menyimpan, kapan,
        tanggal data, total sebelum dan sesudah, selisih, serta catatan. Log audit berbeda dengan
        riwayat perubahan penerima manfaat:
    </p>
    <table class="data">
        <tr>
            <th style="width:31%">Aspek</th>
            <th>Log Audit</th>
            <th>Riwayat Perubahan</th>
        </tr>
        <tr>
            <td>Objek</td>
            <td>Snapshot/laporan harian</td>
            <td>Data penerima manfaat</td>
        </tr>
        <tr>
            <td>Yang dicatat</td>
            <td>Total sebelum/sesudah dan selisih</td>
            <td>Nilai kolom sebelum/sesudah dan alasan</td>
        </tr>
        <tr>
            <td>Dibuat oleh</td>
            <td>Simpan manual dan carry-forward otomatis</td>
            <td>Tambah, edit, hapus, dan impor penerima</td>
        </tr>
        <tr>
            <td>Akses</td>
            <td>Admin</td>
            <td>Admin dan petugas</td>
        </tr>
    </table>

    <div class="page-break"></div>

    <h1>06. Impor dan Ekspor Excel</h1>

    <p>
        Menu <strong>Data Master -&gt; Impor &amp; Ekspor</strong> menyediakan template dan ekspor
        untuk data penerima manfaat, lembaga, serta riwayat harian. Format file yang diterima untuk
        impor adalah <strong>.xlsx</strong>, <strong>.xls</strong>, dan <strong>.csv</strong>.
    </p>

    <h2>Alur impor</h2>
    <ol class="step-list">
        <li><span class="num">1</span>Buka halaman Impor &amp; Ekspor.</li>
        <li><span class="num">2</span>Klik <strong>Template Impor</strong> pada kartu Penerima Manfaat atau Lembaga.</li>
        <li><span class="num">3</span>Isi sheet data sesuai petunjuk. Jangan mengubah nama kolom.</li>
        <li><span class="num">4</span>Simpan file, lalu unggah pada form impor yang sesuai.</li>
        <li><span class="num">5</span>Klik tombol impor dan tunggu proses selesai.</li>
        <li><span class="num">6</span>Periksa hasil: jumlah dibuat, diperbarui, gagal, dan pesan error per baris.</li>
    </ol>

    <h2>Kolom wajib impor penerima manfaat</h2>
    <table class="data">
        <tr>
            <th style="width:20%">Kolom</th>
            <th style="width:12%">Wajib</th>
            <th>Keterangan</th>
        </tr>
        <tr><td>nama_lembaga</td><td>Ya</td><td>Harus sama dengan nama lembaga yang sudah ada.</td></tr>
        <tr><td>nama</td><td>Ya</td><td>Nama lengkap penerima.</td></tr>
        <tr><td>kategori</td><td>Ya</td><td>tk_paud, sd, smk, guru, balita, bumil, busui, atau kader.</td></tr>
        <tr><td>jenis_kelamin</td><td>Ya</td><td>L atau P.</td></tr>
        <tr><td>nik</td><td>Tidak</td><td>16 digit. Format kolom sebagai Teks.</td></tr>
        <tr><td>nisn</td><td>Tidak</td><td>10 digit. Format kolom sebagai Teks.</td></tr>
        <tr><td>kelas</td><td>Untuk SD/SMK</td><td>SD 1-6; SMK 1-3 atau X, XI, XII.</td></tr>
        <tr><td>status</td><td>Tidak</td><td>aktif, non_aktif, atau pindah. Default aktif.</td></tr>
        <tr><td>bulan / tahun</td><td>Tidak</td><td>Default bulan dan tahun berjalan.</td></tr>
        <tr><td>keterangan</td><td>Tidak</td><td>Catatan tambahan.</td></tr>
    </table>

    <h2>Cara pencocokan data penerima</h2>
    <table class="flow">
        <tr>
            <td class="no">1</td>
            <td><span class="title">Cocokkan NIK</span><br>Jika NIK ditemukan, data tersebut diperbarui.</td>
        </tr>
        <tr>
            <td colspan="2" class="arrow">v</td>
        </tr>
        <tr>
            <td class="no">2</td>
            <td><span class="title">Cocokkan NISN</span><br>Jika NIK tidak ditemukan, sistem mencari berdasarkan NISN.</td>
        </tr>
        <tr>
            <td colspan="2" class="arrow">v</td>
        </tr>
        <tr>
            <td class="no">3</td>
            <td><span class="title">Cocokkan lembaga + nama + kategori + kelas</span><br>Jika belum ada yang cocok, baris dianggap data baru.</td>
        </tr>
        <tr>
            <td colspan="2" class="arrow">v</td>
        </tr>
        <tr>
            <td class="no">4</td>
            <td><span class="title">Simpan</span><br>Data baru dibuat, data lama diperbarui, dan setiap perubahan dicatat pada riwayat.</td>
        </tr>
    </table>

    <h2>Ikon dan tombol</h2>
    <table class="data">
        <tr>
            <th style="width:30%">Tombol</th>
            <th>Fungsi</th>
        </tr>
        <tr><td>Template Impor</td><td>Mengunduh file Excel kosong beserta sheet petunjuk dan referensi.</td></tr>
        <tr><td>Ekspor Excel</td><td>Mengunduh seluruh data saat ini dalam format Excel.</td></tr>
        <tr><td>Impor Penerima Manfaat / Lembaga</td><td>Memproses file yang diunggah dan menampilkan hasil per baris.</td></tr>
        <tr><td>Ekspor Riwayat Harian</td><td>Mengunduh seluruh snapshot harian dan rincian per lembaga.</td></tr>
    </table>

    <h2>Ekspor yang tersedia</h2>
    <table class="data">
        <tr>
            <th style="width:30%">Ekspor</th>
            <th>Isi file</th>
        </tr>
        <tr><td>Data Penerima Manfaat</td><td>Sheet data lengkap dan ringkasan per lembaga (aktif, nonaktif, pindah, total).</td></tr>
        <tr><td>Data Lembaga</td><td>Sheet data lembaga dan ringkasan per tipe.</td></tr>
        <tr><td>Riwayat Harian</td><td>Sheet riwayat total per tanggal dan sheet rincian per lembaga.</td></tr>
        <tr><td>Manual Book</td><td>Dokumen PDF ini.</td></tr>
    </table>

    <div class="warn note">
        Jika impor gagal seluruhnya, periksa header kolom, ekstensi file, dan ukuran file (maksimal 10 MB).
        Jika hanya sebagian baris gagal, pesan error menunjukkan nomor baris dan penyebabnya.
        Perbaiki baris tersebut, lalu unggah ulang file.
    </div>

    <div class="page-break"></div>

    <h1>07. Jejak Perubahan Penerima Manfaat</h1>

    <p>
        Setiap perubahan data penerima manfaat dicatat pada menu <strong>Laporan -&gt; Riwayat Perubahan</strong>.
        Tujuan fitur ini adalah memastikan setiap perubahan dapat dipertanggungjawabkan: apa yang berubah,
        mengapa berubah, kapan, hari apa, dan siapa yang mengubah.
    </p>

    <h2>Informasi yang dicatat</h2>
    <table class="data">
        <tr>
            <th style="width:28%">Informasi</th>
            <th>Contoh / keterangan</th>
        </tr>
        <tr><td>Jenis aksi</td><td>Dibuat, Diperbarui, atau Dihapus.</td></tr>
        <tr><td>Nama penerima</td><td>Nama pada saat perubahan terjadi, tetap tersimpan walaupun data kemudian dihapus.</td></tr>
        <tr><td>Lembaga dan kategori</td><td>Snapshot lembaga dan kategori saat perubahan.</td></tr>
        <tr><td>Tanggal dan hari</td><td>Contoh: 04 November 2026 (Rabu).</td></tr>
        <tr><td>Waktu</td><td>Jam dan menit pencatatan.</td></tr>
        <tr><td>Pengguna</td><td>Nama akun yang melakukan perubahan. Jika dari sistem, tertulis Sistem.</td></tr>
        <tr><td>Alasan</td><td>Alasan yang diisi pada form edit/hapus, atau keterangan impor.</td></tr>
        <tr><td>Nilai sebelum dan sesudah</td><td>Rincian per kolom yang berubah, misalnya status aktif menjadi pindah.</td></tr>
    </table>

    <h2>Contoh catatan perubahan</h2>
    <table class="data">
        <tr>
            <th style="width:20%">Kolom</th>
            <th style="width:34%">Sebelum</th>
            <th>Sesudah</th>
        </tr>
        <tr><td>Status</td><td>Aktif</td><td>Pindah / Keluar</td></tr>
        <tr><td>Kelas</td><td>5</td><td>6</td></tr>
        <tr><td>Keterangan</td><td>-</td><td>Naik kelas tahun ajaran baru</td></tr>
    </table>

    <h2>Cara menelusuri</h2>
    <ol class="step-list">
        <li><span class="num">1</span>Buka <strong>Laporan -&gt; Riwayat Perubahan</strong>.</li>
        <li><span class="num">2</span>Gunakan pencarian nama penerima atau lembaga.</li>
        <li><span class="num">3</span>Filter berdasarkan aksi, kategori, pengguna, atau rentang tanggal.</li>
        <li><span class="num">4</span>Klik <strong>Detail</strong> untuk melihat alasan lengkap dan rincian nilai sebelum/sesudah.</li>
        <li><span class="num">5</span>Untuk riwayat satu orang, buka edit penerima manfaat lalu pilih tab <strong>Riwayat Perubahan</strong>.</li>
    </ol>

    <h2>Aturan pengisian alasan</h2>
    <ul>
        <li>Gunakan alasan yang spesifik dan dapat dipahami orang lain.</li>
        <li>Sebutkan penyebab perubahan, bukan hanya menulis "update" atau "perbaikan".</li>
        <li>Contoh alasan yang baik: "Perbaikan NIK sesuai KTP", "Penerima pindah domisili ke luar kecamatan", "Koreksi nama sesuai akta".</li>
        <li>Untuk impor, sistem otomatis mencatat nama file sebagai bagian dari alasan.</li>
        <li>Jangan menghapus data hanya untuk merapikan tampilan. Gunakan status nonaktif atau pindah.</li>
    </ul>

    <div class="note">
        Riwayat perubahan tidak dapat diedit atau dihapus dari antarmuka. Ini menjaga integritas
        jejak audit. Jika ada kekeliruan, lakukan perubahan baru dengan alasan koreksi agar
        keduanya tetap tercatat.
    </div>

    <div class="page-break"></div>

    <h1>08. Penjadwalan Otomatis dan Pemeliharaan</h1>

    <h2>Snapshot otomatis</h2>
    <table class="data">
        <tr>
            <th style="width:28%">Pengaturan</th>
            <th>Nilai</th>
        </tr>
        <tr><td>Hari</td><td>Senin sampai Jumat</td></tr>
        <tr><td>Jam</td><td>01.00 WIB</td></tr>
        <tr><td>Zona waktu</td><td>Asia/Jakarta</td></tr>
        <tr><td>Perilaku</td><td>Menyalin snapshot terakhir jika data hari tersebut belum ada.</td></tr>
        <tr><td>Status sumber</td><td>Otomatis</td></tr>
        <tr><td>Sabtu/Minggu</td><td>Dilewati</td></tr>
    </table>

    <p>
        Pada server produksi, scheduler harus dijalankan oleh cron atau Task Scheduler.
        Perintah yang digunakan adalah <strong>php artisan schedule:run</strong> setiap menit.
        Untuk pengujian di komputer lokal, jalankan <strong>php artisan schedule:work</strong>
        pada terminal yang terpisah.
    </p>

    <h2>Perintah pemeliharaan</h2>
    <table class="data">
        <tr>
            <th style="width:40%">Perintah</th>
            <th>Kegunaan</th>
        </tr>
        <tr><td>php artisan schedule:list</td><td>Melihat daftar jadwal yang terdaftar.</td></tr>
        <tr><td>php artisan schedule:work</td><td>Menjalankan scheduler untuk pengembangan lokal.</td></tr>
        <tr><td>php artisan pm:auto-snapshot</td><td>Menjalankan snapshot carry-forward secara manual.</td></tr>
        <tr><td>php artisan manual:generate</td><td>Membuat ulang file PDF manual book.</td></tr>
        <tr><td>php artisan optimize:clear</td><td>Membersihkan cache konfigurasi, route, dan view.</td></tr>
    </table>

    <h2>Pencadangan data</h2>
    <ul>
        <li>Lakukan pencadangan database secara berkala, terutama sebelum impor data besar.</li>
        <li>Ekspor data penerima, lembaga, dan riwayat harian sebagai cadangan tambahan.</li>
        <li>Simpan file ekspor di tempat yang aman dan beri nama dengan tanggal.</li>
        <li>Periksa log audit dan riwayat perubahan secara berkala untuk memastikan tidak ada aktivitas tidak wajar.</li>
    </ul>

    <h2>Pemeriksaan rutin</h2>
    <table class="data">
        <tr>
            <th style="width:24%">Frekuensi</th>
            <th>Yang diperiksa</th>
        </tr>
        <tr><td>Harian</td><td>Total LIVE, kelengkapan data per lembaga, dan penyimpanan snapshot hari ini.</td></tr>
        <tr><td>Mingguan</td><td>Laporan mingguan, status hari otomatis, dan perubahan total yang tidak wajar.</td></tr>
        <tr><td>Bulanan</td><td>Data master, akun pengguna, ekspor cadangan, dan ukuran database.</td></tr>
    </table>

    <div class="page-break"></div>

    <h1>09. Penanganan Masalah dan Glosarium</h1>

    <h2>Masalah umum</h2>
    <table class="data">
        <tr>
            <th style="width:33%">Gejala</th>
            <th>Kemungkinan penyebab dan solusi</th>
        </tr>
        <tr>
            <td>Impor gagal: kolom wajib tidak ditemukan</td>
            <td>Nama header berubah atau terhapus. Unduh template baru dan gunakan header asli.</td>
        </tr>
        <tr>
            <td>NIK/NISN berubah menjadi notasi ilmiah</td>
            <td>Format kolom menjadi Teks sebelum mengisi angka, lalu impor ulang.</td>
        </tr>
        <tr>
            <td>Lembaga tidak ditemukan saat impor</td>
            <td>Tambahkan lembaga terlebih dahulu atau gunakan nama yang sama dengan sheet Referensi Lembaga.</td>
        </tr>
        <tr>
            <td>Kelas wajib diisi</td>
            <td>Kategori SD/SMK memerlukan kelas. Isi 1-6 untuk SD, atau 1-3/X-XII untuk SMK.</td>
        </tr>
        <tr>
            <td>Data tidak muncul di laporan</td>
            <td>Periksa status penerima (harus aktif) dan status lembaga (harus aktif).</td>
        </tr>
        <tr>
            <td>Snapshot otomatis tidak berjalan</td>
            <td>Pastikan scheduler aktif dan zona waktu server Asia/Jakarta. Cek dengan schedule:list.</td>
        </tr>
        <tr>
            <td>Tanggal tidak bisa dipilih</td>
            <td>Tanggal setelah hari ini memang tidak diizinkan.</td>
        </tr>
        <tr>
            <td>Perubahan tidak tercatat</td>
            <td>Pastikan perubahan dilakukan melalui aplikasi dan alasan diisi saat mengedit.</td>
        </tr>
    </table>

    <h2>Glosarium</h2>
    <table class="data">
        <tr>
            <th style="width:26%">Istilah</th>
            <th>Arti</th>
        </tr>
        <tr><td>LIVE</td><td>Data yang dihitung langsung dari data master penerima aktif pada hari ini.</td></tr>
        <tr><td>Snapshot</td><td>Salinan permanen data pada tanggal tertentu yang disimpan sebagai arsip.</td></tr>
        <tr><td>Carry-forward</td><td>Penyalinan snapshot terakhir ke tanggal berikutnya saat data belum tersedia.</td></tr>
        <tr><td>Fallback</td><td>Data terakhir yang ditampilkan untuk tanggal yang belum memiliki laporan.</td></tr>
        <tr><td>Log audit</td><td>Catatan aktivitas penyimpanan laporan harian.</td></tr>
        <tr><td>Riwayat perubahan</td><td>Catatan perubahan data penerima manfaat beserta alasan dan nilai sebelum/sesudah.</td></tr>
        <tr><td>Template</td><td>File Excel acuan yang dapat digunakan berulang kali untuk impor.</td></tr>
        <tr><td>Scheduler</td><td>Penjadwal otomatis yang menjalankan perintah pada waktu tertentu.</td></tr>
    </table>

    <h2>Checklist harian petugas</h2>
    <table class="data">
        <tr>
            <th style="width:12%">No</th>
            <th>Aktivitas</th>
            <th style="width:16%">Status</th>
        </tr>
        <tr><td>1</td><td>Periksa data penerima dan lembaga.</td><td>[ ]</td></tr>
        <tr><td>2</td><td>Buka halaman LIVE dan cocokkan total.</td><td>[ ]</td></tr>
        <tr><td>3</td><td>Simpan snapshot hari ini.</td><td>[ ]</td></tr>
        <tr><td>4</td><td>Periksa laporan mingguan.</td><td>[ ]</td></tr>
        <tr><td>5</td><td>Catat alasan setiap perubahan data.</td><td>[ ]</td></tr>
    </table>

    <div class="note">
        <strong>Dukungan:</strong> hubungi admin SPPG Samarang untuk masalah akun, hak akses,
        atau data yang tidak sesuai. Sertakan tangkapan layar, tanggal kejadian, dan nama pengguna
        agar penelusuran lebih cepat.
    </div>

    <div class="footer">
        Manual Book Aplikasi Penerima Manfaat SPPG Samarang - dibuat {{ $dicetak }}
    </div>
</body>
</html>
