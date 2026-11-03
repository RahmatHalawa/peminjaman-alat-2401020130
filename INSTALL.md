# Panduan Instalasi Cepat

## Prasyarat
- PHP 8.2 atau lebih tinggi
- MySQL 8.0 atau MariaDB 10.x
- Composer
- XAMPP/WAMP/LAMP (atau server lokal lainnya)

## Langkah Instalasi

### 1. Clone atau Download Repository
```bash
# Clone repository (jika sudah di GitHub)
git clone [URL_REPOSITORY]
cd peminjaman-alat-2401020130

# Atau extract file ZIP jika download manual
```

### 2. Install Dependencies
```bash
composer install
```

### 3. Konfigurasi Environment
```bash
# Copy file .env.example ke .env
copy .env.example .env

# Generate application key
php artisan key:generate
```

### 4. Sesuaikan Konfigurasi Database
Edit file `.env` dan sesuaikan dengan konfigurasi MySQL Anda:
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=peminjaman_alat_2401020130
DB_USERNAME=root
DB_PASSWORD=
```

### 5. Import Database
**Cara 1 - Via phpMyAdmin:**
1. Buka phpMyAdmin di browser (http://localhost/phpmyadmin)
2. Buat database baru dengan nama `peminjaman_alat_2401020130`
3. Pilih database tersebut
4. Klik tab "Import"
5. Pilih file `database.sql`
6. Klik "Go"

**Cara 2 - Via Command Line:**
```bash
# Pastikan MySQL service sudah running
mysql -u root -p < database.sql
# Masukkan password MySQL jika diminta
```

**Cara 3 - Via MySQL Workbench:**
1. Buka MySQL Workbench
2. Connect ke MySQL server
3. File > Run SQL Script
4. Pilih file `database.sql`
5. Execute

### 6. Jalankan Aplikasi
```bash
php artisan serve
```

### 7. Akses Aplikasi
Buka browser dan akses:
```
http://localhost:8000/peminjaman
```

## Verifikasi Instalasi

Jika instalasi berhasil, Anda akan melihat:
- ✅ Halaman dengan judul "Sistem Peminjaman Alat Laboratorium"
- ✅ NIM: 2401020130
- ✅ Form tambah peminjaman
- ✅ Form pencarian dan filter
- ✅ Tabel dengan 3 data awal (PM00000001, PM00000002, PM00000003)

## Troubleshooting

### Error: "SQLSTATE[HY000] [2002] Connection refused"
**Penyebab:** MySQL service belum berjalan
**Solusi:** 
- Di XAMPP: Start Apache dan MySQL dari XAMPP Control Panel
- Di WAMP: Start All Services
- Di Command Line: `net start MySQL80` (Windows) atau `sudo service mysql start` (Linux)

### Error: "SQLSTATE[HY000] [1049] Unknown database"
**Penyebab:** Database belum dibuat
**Solusi:** Import file `database.sql` terlebih dahulu

### Error: "Class 'Composer\InstalledVersions' not found"
**Penyebab:** Autoload belum di-generate
**Solusi:** 
```bash
composer dump-autoload
```

### Error: "The stream or file could not be opened"
**Penyebab:** Permission pada folder storage/logs
**Solusi:**
```bash
# Windows (di PowerShell sebagai Administrator)
icacls "storage" /grant Everyone:F /T
icacls "bootstrap/cache" /grant Everyone:F /T

# Linux/Mac
chmod -R 777 storage bootstrap/cache
```

### Error: "No application encryption key has been set"
**Penyebab:** APP_KEY belum di-generate
**Solusi:**
```bash
php artisan key:generate
```

## Pengujian Fitur

### Test Create
1. Isi form tambah dengan data valid
2. Klik "Tambah Peminjaman"
3. Data harus tersimpan dan muncul pesan sukses

### Test Validation
1. Isi form dengan kode peminjaman kurang dari 10 karakter
2. Submit form
3. Harus muncul pesan error validasi

### Test Duplicate
1. Isi form dengan kode peminjaman yang sudah ada (misal: PM00000001)
2. Submit form
3. Harus muncul error "Kode peminjaman sudah digunakan"

### Test Search
1. Ketik "Rina" di kolom pencarian
2. Klik "Cari"
3. Harus tampil data dengan nama Rina Kurnia

### Test Filter
1. Pilih "Kamera" di dropdown Filter Alat
2. Klik "Cari"
3. Harus tampil hanya peminjaman alat Kamera

### Test Update
1. Klik tombol "Edit" pada salah satu data
2. Ubah nama peminjam
3. Klik "Update"
4. Data harus terupdate dan muncul pesan sukses

### Test Delete
1. Klik tombol "Hapus" pada salah satu data
2. Konfirmasi dialog
3. Data harus terhapus dan muncul pesan sukses

## Catatan Penting

- ⚠️ Jangan commit file `.env` ke repository (sudah ada di .gitignore)
- ⚠️ Pastikan MySQL sudah running sebelum mengakses aplikasi
- ⚠️ Gunakan PHP 8.2 atau lebih tinggi
- ⚠️ Pastikan ekstensi PHP yang diperlukan sudah aktif: pdo_mysql, mbstring, xml, json

## Kontak

Jika ada pertanyaan atau kendala, hubungi:
- **Nama**: [Nama Mahasiswa]
- **NIM**: 2401020130
- **Email**: [email@domain.com]
