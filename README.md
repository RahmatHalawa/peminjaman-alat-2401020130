# Sistem Peminjaman Alat Laboratorium

## Identitas
- **Nama**: [Nama Mahasiswa]
- **NIM**: 2401020130
- **Mata Kuliah**: Praktikum Pemrograman Web (INF11197)
- **Paket Soal**: 2 - Tipe B

## Deskripsi
Aplikasi web untuk mencatat peminjaman alat laboratorium menggunakan Laravel. Aplikasi ini menyediakan fitur CRUD lengkap, pencarian, dan filter berdasarkan alat.

## Teknologi
- Laravel 12
- PHP 8.2
- MySQL/MariaDB
- PDO (PHP Data Objects)
- Blade Template Engine

## Fitur
1. **Create**: Menambah data peminjaman baru
2. **Read**: Menampilkan daftar peminjaman
3. **Update**: Mengubah data peminjaman
4. **Delete**: Menghapus data peminjaman
5. **Pencarian**: Mencari berdasarkan kode peminjaman, nama peminjam, atau nomor identitas
6. **Filter**: Memfilter data berdasarkan alat
7. **Validasi**: Validasi input server-side
8. **Error Handling**: Penanganan error dengan try-catch dan report()
9. **Security**: Prepared statement, CSRF token, sanitasi input

## Struktur Database

### Tabel: alat
| Kolom | Tipe | Constraint |
|-------|------|------------|
| id | BIGINT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT |
| nama_alat | VARCHAR(100) | NOT NULL, UNIQUE |

### Tabel: peminjaman_alat
| Kolom | Tipe | Constraint |
|-------|------|------------|
| id | BIGINT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT |
| kode_peminjaman | CHAR(10) | NOT NULL, UNIQUE |
| nama_peminjam | VARCHAR(100) | NOT NULL |
| nomor_identitas | VARCHAR(30) | NOT NULL, UNIQUE |
| jumlah_alat | TINYINT UNSIGNED | NOT NULL, CHECK (1-10) |
| alat_id | BIGINT UNSIGNED | FOREIGN KEY to alat(id), ON UPDATE CASCADE, ON DELETE RESTRICT |
| created_at | TIMESTAMP | NULL |
| updated_at | TIMESTAMP | NULL |

## Instalasi

1. Clone repository
```bash
git clone [URL_REPOSITORY]
cd peminjaman-alat-2401020130
```

2. Install dependencies
```bash
composer install
```

3. Konfigurasi environment
```bash
cp .env.example .env
php artisan key:generate
```

4. Sesuaikan konfigurasi database di file .env
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=peminjaman_alat_2401020130
DB_USERNAME=root
DB_PASSWORD=
```

5. Import database
```bash
mysql -u root -p < database.sql
```
Atau import manual melalui phpMyAdmin/MySQL Workbench

6. Jalankan aplikasi
```bash
php artisan serve
```

7. Akses aplikasi di browser
```
http://localhost:8000/peminjaman
```

## Route List
| Method | URI | Action | Deskripsi |
|--------|-----|--------|-----------|
| GET | /peminjaman | index | Menampilkan daftar peminjaman |
| POST | /peminjaman | store | Menyimpan peminjaman baru |
| GET | /peminjaman/{id}/edit | edit | Menampilkan form edit |
| PUT | /peminjaman/{id} | update | Mengupdate data peminjaman |
| DELETE | /peminjaman/{id} | destroy | Menghapus data peminjaman |

## Keamanan
1. **Prepared Statement**: Semua query menggunakan prepared statement dengan parameter binding
2. **CSRF Protection**: Semua form menggunakan token @csrf
3. **Input Sanitization**: Menggunakan strip_tags() dan trim()
4. **Server-side Validation**: Validasi lengkap di controller
5. **Error Handling**: Try-catch dengan report() untuk logging, pesan aman ke user
6. **.env Protection**: File .env tidak masuk ke repository (ada di .gitignore)

## Testing
Aplikasi telah diuji dengan 10 skenario:
1. Read - Menampilkan data awal
2. Create Valid - Menambah data baru
3. Create Invalid - Validasi input kosong/salah
4. Duplicate - Menolak kode/identitas duplikat
5. Update - Mengubah data existing
6. Conflict Update - Menolak update dengan nilai duplikat
7. Delete - Menghapus data
8. Search - Pencarian 3 kolom
9. Filter - Filter berdasarkan alat
10. Error Handling - Penanganan error database

## Fungsi Helper
**statusJumlah($jumlah)**: Mengembalikan 'Banyak' jika jumlah >= 3, 'Sedikit' jika < 3

## Catatan
- Aplikasi menggunakan PDO murni untuk akses database (bukan Eloquent ORM)
- Tidak menggunakan CSS/JavaScript/framework frontend sesuai instruksi
- Semua query prepared untuk mencegah SQL injection
