-- Buat database
CREATE DATABASE IF NOT EXISTS peminjaman_alat_2401020130;
USE peminjaman_alat_2401020130;

-- Tabel alat (master)
CREATE TABLE alat (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nama_alat VARCHAR(100) NOT NULL UNIQUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabel peminjaman_alat (utama)
CREATE TABLE peminjaman_alat (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    kode_peminjaman CHAR(10) NOT NULL UNIQUE,
    nama_peminjam VARCHAR(100) NOT NULL,
    nomor_identitas VARCHAR(30) NOT NULL UNIQUE,
    jumlah_alat TINYINT UNSIGNED NOT NULL CHECK (jumlah_alat BETWEEN 1 AND 10),
    alat_id BIGINT UNSIGNED NOT NULL,
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL,
    FOREIGN KEY (alat_id) REFERENCES alat(id) ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data awal untuk tabel alat
INSERT INTO alat (nama_alat) VALUES 
('Kamera'),
('Proyektor'),
('Mikroskop');

-- Data awal untuk tabel peminjaman_alat
INSERT INTO peminjaman_alat (kode_peminjaman, nama_peminjam, nomor_identitas, jumlah_alat, alat_id, created_at, updated_at) VALUES 
('PM00000001', 'Rina Kurnia', 'MHS-2201001', 2, 1, NOW(), NOW()),
('PM00000002', 'Dedi Pranata', 'MHS-2201002', 1, 2, NOW(), NOW()),
('PM00000003', 'Mira Ananda', 'MHS-2201003', 3, 1, NOW(), NOW());
