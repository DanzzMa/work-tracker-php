# Work Tracker PHP (Login, Start/Stop, Rekap Harian & Mingguan)

Aplikasi web pencatat waktu kerja dengan fitur:
- Login/Logout per pengguna (role **admin** & **worker**)
- Tombol **Masuk (Start)** dan **Berhenti (Stop)**
- Hitung durasi otomatis (menit) dan totalisasi per hari & minggu
- Rekap harian, mingguan, dan rentang tanggal + **Export CSV**
- Admin bisa **menambah pengguna** dan melihat semua laporan
- UI modern dengan Tailwind + dark mode + timer real-time
- Proteksi **CSRF** dan session hardening

## Persyaratan
- PHP 8+ dan ekstensi `mysqli`
- MySQL/MariaDB
- Web server (Apache/Nginx). Untuk Apache, aktifkan `mod_php` & `mod_rewrite` (opsional).

## Instalasi Cepat
1. Buat database (misal: `work_tracker`).
2. Import `init.sql` ke database.
3. Salin folder `public/` ke document root web server Anda (atau jadikan root).
4. Salin `config.sample.php` menjadi `config.php`, lalu isi kredensial DB Anda.
5. Buka aplikasi di browser: `http://localhost/`

### Akun awal
- **Admin**: username `admin`, password `admin123`
- Bisa diganti di halaman Admin → Pengguna.

## Struktur
```
public/
  index.php           # Login
  dashboard.php       # Dashboard pekerja
  admin.php           # Panel admin
  logout.php          # Logout
  actions/
    login.php
    start.php
    stop.php
    add_user.php
    export_csv.php
    utils.php
  assets/
    app.js
    styles.css
config.sample.php
db.php
init.sql
README.md
```

## Catatan Keamanan
- Ubah `admin` password segera setelah instalasi.
- Pastikan `config.php` **tidak** dapat diakses publik (biarkan di root proyek, bukan dalam `public`).

## Lisensi
MIT
