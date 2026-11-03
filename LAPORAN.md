# Laporan Pengujian Sistem Peminjaman Alat

## Identitas
- **Nama**: [Nama Mahasiswa]
- **NIM**: 2401020130
- **Mata Kuliah**: Praktikum Pemrograman Web (INF11197)
- **Paket Soal**: 2 - Tipe B (Sistem Peminjaman Alat)
- **Tanggal**: 3 November 2026

---

## 1. Rancangan Relasi Database

### Entity Relationship Diagram (ERD)
```
┌─────────────┐         ┌───────────────────┐
│    alat     │         │  peminjaman_alat  │
├─────────────┤         ├───────────────────┤
│ id (PK)     │────┐    │ id (PK)           │
│ nama_alat   │    └───<│ alat_id (FK)      │
└─────────────┘         │ kode_peminjaman   │
                        │ nama_peminjam     │
                        │ nomor_identitas   │
                        │ jumlah_alat       │
                        │ created_at        │
                        │ updated_at        │
                        └───────────────────┘
```

### Relasi
- **Tipe**: One-to-Many (1:N)
- **Tabel Master**: `alat`
- **Tabel Detail**: `peminjaman_alat`
- **Deskripsi**: Satu alat dapat dipinjam oleh banyak peminjam (dalam record berbeda)

### Constraints
1. **Primary Key**: `id` (AUTO_INCREMENT) pada kedua tabel
2. **Foreign Key**: `alat_id` di tabel `peminjaman_alat` → `id` di tabel `alat`
   - ON UPDATE CASCADE: Jika id alat berubah, alat_id ikut berubah
   - ON DELETE RESTRICT: Alat tidak bisa dihapus jika masih ada peminjaman
3. **UNIQUE**: 
   - `nama_alat` di tabel `alat`
   - `kode_peminjaman` di tabel `peminjaman_alat`
   - `nomor_identitas` di tabel `peminjaman_alat`
4. **NOT NULL**: Semua kolom kecuali timestamp
5. **CHECK**: `jumlah_alat BETWEEN 1 AND 10`

---

## 2. Daftar Route

| Method | URI | Controller@Action | Deskripsi |
|--------|-----|-------------------|-----------|
| GET | `/peminjaman` | PeminjamanController@index | Menampilkan daftar peminjaman dengan form tambah, pencarian, dan filter |
| POST | `/peminjaman` | PeminjamanController@store | Menyimpan data peminjaman baru |
| GET | `/peminjaman/{id}/edit` | PeminjamanController@edit | Menampilkan form edit peminjaman |
| PUT | `/peminjaman/{id}` | PeminjamanController@update | Mengupdate data peminjaman |
| DELETE | `/peminjaman/{id}` | PeminjamanController@destroy | Menghapus data peminjaman |

---

## 3. Prepared Statement dan Keamanan

### Implementasi Prepared Statement
Semua query yang menerima input pengguna menggunakan **prepared statement** dengan parameter binding untuk mencegah SQL Injection.

**Contoh pada method `store()`:**
```php
// Cek duplikat kode_peminjaman
$stmt = $pdo->prepare("SELECT COUNT(*) as count FROM peminjaman_alat WHERE kode_peminjaman = ?");
$stmt->execute([$kode_peminjaman]);

// Insert data
$query = "INSERT INTO peminjaman_alat (kode_peminjaman, nama_peminjam, nomor_identitas, jumlah_alat, alat_id, created_at, updated_at) 
          VALUES (?, ?, ?, ?, ?, NOW(), NOW())";
$stmt = $pdo->prepare($query);
$stmt->execute([$kode_peminjaman, $nama_peminjam, $nomor_identitas, $jumlah_alat, $alat_id]);
```

**Contoh pada method `index()` dengan pencarian:**
```php
$query = "SELECT peminjaman_alat.*, alat.nama_alat 
          FROM peminjaman_alat 
          INNER JOIN alat ON peminjaman_alat.alat_id = alat.id 
          WHERE 1=1";

if ($search) {
    $query .= " AND (peminjaman_alat.kode_peminjaman LIKE ? 
                OR peminjaman_alat.nama_peminjam LIKE ? 
                OR peminjaman_alat.nomor_identitas LIKE ?)";
    $searchParam = '%' . $search . '%';
    $params[] = $searchParam;
    $params[] = $searchParam;
    $params[] = $searchParam;
}

$stmt = $pdo->prepare($query);
$stmt->execute($params);
```

### Keuntungan Prepared Statement:
1. **Mencegah SQL Injection**: Parameter tidak pernah digabung langsung ke string SQL
2. **Performance**: Query di-compile sekali, bisa dieksekusi berkali-kali
3. **Type Safety**: PDO otomatis meng-escape parameter sesuai tipe data

---

## 4. Error Handling

### Implementasi Try-Catch
Semua method controller dibungkus dengan try-catch untuk menangani error database.

**Contoh:**
```php
try {
    $pdo = DB::connection()->getPdo();
    
    // Query operations...
    
    return redirect('/peminjaman')->with('success', 'Data berhasil disimpan');
    
} catch (Exception $e) {
    report($e);  // Log error detail ke storage/logs/laravel.log
    return back()->withInput()->with('error', 'Terjadi kesalahan saat menyimpan data');
}
```

### Cara Kerja:
1. **`try` block**: Eksekusi kode normal
2. **`catch` block**: Tangkap exception jika terjadi error
3. **`report($e)`**: Log detail error (stack trace, query, dll) untuk developer
4. **User message**: Tampilkan pesan aman dan singkat ke user tanpa expose detail teknis

### Keuntungan:
- User tidak melihat error teknis yang membingungkan
- Developer bisa debug dari log file
- Aplikasi tidak crash saat ada error database

---

## 5. Hasil Pengujian (10 Skenario Wajib)

### Pengujian 1: Read - Menampilkan Data Awal
**Langkah:**
1. Akses URL `http://localhost:8000/peminjaman`
2. Pastikan database sudah diimport

**Hasil yang Diharapkan:**
- Tampil 3 data peminjaman awal (PM00000001, PM00000002, PM00000003)
- Kolom menampilkan: No, Kode Peminjaman, Nama Peminjam, Nomor Identitas, Alat, Jumlah Alat, Status, Aksi

**Status:** ✅ BERHASIL
- Data awal tampil lengkap
- Status "Sedikit" untuk jumlah < 3, "Banyak" untuk jumlah >= 3

---

### Pengujian 2: Create Valid - Tambah Data Baru
**Langkah:**
1. Isi form tambah dengan data valid:
   - Kode Peminjaman: `PM00000004`
   - Nama Peminjam: `Budi Santoso`
   - Nomor Identitas: `MHS-2201004`
   - Jumlah Alat: `5`
   - Alat: `Mikroskop`
2. Klik tombol "Tambah Peminjaman"

**Hasil yang Diharapkan:**
- Data tersimpan ke database
- Muncul pesan sukses "Data peminjaman berhasil ditambahkan"
- Data baru tampil di tabel

**Status:** ✅ BERHASIL
- Data tersimpan dengan benar
- Feedback success tampil

---

### Pengujian 3: Create Invalid - Validasi Input
**Langkah:**
1. Isi form dengan data tidak valid:
   - Kode Peminjaman: `PM1` (kurang dari 10 karakter)
   - Nama Peminjam: kosong
   - Nomor Identitas: `AB` (kurang dari 5 karakter)
   - Jumlah Alat: `15` (melebihi maksimal 10)
2. Klik tombol "Tambah Peminjaman"

**Hasil yang Diharapkan:**
- Data tidak tersimpan
- Muncul pesan error untuk setiap field yang invalid
- Input sebelumnya dipertahankan dengan `old()`

**Status:** ✅ BERHASIL
- Validasi berjalan dengan baik
- Pesan error spesifik untuk setiap field
- Old input dipertahankan

---

### Pengujian 4: Duplicate - Tolak Kode/Identitas Sama
**Langkah 4a - Duplikat Kode Peminjaman:**
1. Input data dengan `kode_peminjaman` yang sudah ada: `PM00000001`
2. Isi field lain dengan data valid
3. Submit form

**Hasil yang Diharapkan:**
- Data ditolak
- Muncul error "Kode peminjaman sudah digunakan"

**Status:** ✅ BERHASIL

**Langkah 4b - Duplikat Nomor Identitas:**
1. Input data dengan `nomor_identitas` yang sudah ada: `MHS-2201001`
2. Isi field lain dengan data valid (termasuk kode unik)
3. Submit form

**Hasil yang Diharapkan:**
- Data ditolak
- Muncul error "Nomor identitas sudah digunakan"

**Status:** ✅ BERHASIL

---

### Pengujian 5: Update - Mengubah Data Existing
**Langkah:**
1. Klik tombol "Edit" pada salah satu data (misal: PM00000001)
2. Ubah nama peminjam menjadi `Rina Kurnia Sari`
3. Ubah jumlah alat menjadi `4`
4. Klik tombol "Update"

**Hasil yang Diharapkan:**
- Data lama tampil di form edit
- Perubahan tersimpan
- Muncul pesan "Data peminjaman berhasil diupdate"
- Redirect ke halaman index dengan data terupdate

**Status:** ✅ BERHASIL
- Data lama terisi dengan `old()` fallback
- Update berhasil

---

### Pengujian 6: Konflik Update - Nilai Unik Milik Lain
**Langkah:**
1. Edit data PM00000002
2. Ubah `kode_peminjaman` menjadi `PM00000001` (milik data lain)
3. Klik Update

**Hasil yang Diharapkan:**
- Update ditolak
- Muncul error "Kode peminjaman sudah digunakan"
- Data tetap aman (tidak ada yang rusak)

**Status:** ✅ BERHASIL
- Validasi unik mengecualikan record sendiri dengan `WHERE id != ?`

---

### Pengujian 7: Delete - Hapus Data
**Langkah:**
1. Klik tombol "Hapus" pada salah satu data
2. Konfirmasi dialog JavaScript
3. Form DELETE dengan CSRF token tersubmit

**Hasil yang Diharapkan:**
- Data terhapus dari database
- Muncul pesan "Data peminjaman berhasil dihapus"
- Data tidak tampil lagi di tabel

**Status:** ✅ BERHASIL
- Method spoofing `@method('DELETE')` berfungsi
- CSRF token tervalidasi

---

### Pengujian 8: Pencarian - 3 Kolom
**Langkah:**
1. Input kata kunci di form pencarian: `Rina`
2. Klik tombol "Cari"

**Hasil yang Diharapkan:**
- Tampil data yang cocok di kolom `kode_peminjaman`, `nama_peminjam`, atau `nomor_identitas`
- Data dengan nama "Rina Kurnia" tampil
- Data lain tidak tampil

**Status:** ✅ BERHASIL
- Query LIKE dengan 3 kondisi OR berfungsi
- Prepared statement dengan parameter yang sama digunakan 3 kali

---

### Pengujian 9: Filter - Berdasarkan Alat
**Langkah:**
1. Pilih "Kamera" di dropdown Filter Alat
2. Klik tombol "Cari"

**Hasil yang Diharapkan:**
- Hanya tampil peminjaman dengan alat = Kamera
- Data dengan alat Proyektor/Mikroskop tidak tampil

**Status:** ✅ BERHASIL
- Filter `WHERE alat_id = ?` berfungsi
- Kombinasi pencarian + filter juga berfungsi

---

### Pengujian 10: Error Handling - Koneksi Database
**Langkah:**
1. Matikan MySQL server
2. Akses `http://localhost:8000/peminjaman`

**Hasil yang Diharapkan:**
- Tidak crash dengan error PHP
- Muncul pesan error user-friendly: "Terjadi kesalahan saat mengambil data"
- Detail error tercatat di `storage/logs/laravel.log`

**Status:** ✅ BERHASIL
- Try-catch menangkap exception
- `report($e)` mencatat detail error
- User menerima pesan aman

---

## 6. Kendala dan Solusi

### Kendala 1: Laravel membutuhkan PHP 8.3 untuk versi 13
**Solusi:** Menggunakan Laravel 12 yang kompatibel dengan PHP 8.2

### Kendala 2: Session driver memerlukan tabel tambahan
**Solusi:** Tidak ada kendala, aplikasi berjalan normal tanpa migrasi session

### Kendala 3: Validasi duplikat pada update
**Solusi:** Menambahkan kondisi `WHERE id != ?` pada query pengecekan duplikat untuk mengecualikan record yang sedang diedit

---

## 7. Fitur Keamanan yang Diterapkan

1. **Prepared Statement**: Semua query menggunakan parameter binding
2. **Input Sanitization**: `strip_tags()` dan `trim()` pada semua input teks
3. **Server-side Validation**: Validasi lengkap di controller
4. **CSRF Protection**: Token `@csrf` di semua form
5. **Method Spoofing**: `@method('PUT')` dan `@method('DELETE')` untuk REST compliance
6. **Error Handling**: Try-catch dengan `report()` untuk logging
7. **.env Protection**: File .env tidak masuk repository (di .gitignore)
8. **Database Constraints**: UNIQUE, CHECK, FK untuk integritas data
9. **Old Input**: Mempertahankan input user saat validasi gagal
10. **Safe Error Messages**: User tidak melihat detail teknis error

---

## 8. Kesimpulan

Aplikasi Sistem Peminjaman Alat Laboratorium telah berhasil dibangun dengan fitur CRUD lengkap menggunakan Laravel dan PDO. Semua 10 skenario pengujian berhasil dengan baik. 

**Poin Penting:**
- ✅ Prepared statement diterapkan di semua query untuk keamanan
- ✅ Error handling dengan try-catch dan report() berfungsi optimal
- ✅ Validasi server-side mencegah data invalid dan duplikat
- ✅ Pencarian 3 kolom dan filter berdasarkan alat berfungsi
- ✅ CSRF protection dan method spoofing diterapkan
- ✅ Repository Git memiliki minimal 6 commit terstruktur
- ✅ File .env tidak masuk repository

Aplikasi siap untuk digunakan dan memenuhi semua kriteria yang diminta pada soal Paket 2 Tipe B.

---

**Dibuat oleh:** [Nama Mahasiswa] - NIM 2401020130  
**Tanggal:** 3 November 2026
