<?php

if (!function_exists('statusJumlah')) {
    /**
     * Menampilkan status berdasarkan jumlah alat yang dipinjam
     * 
     * Fungsi ini menggunakan percabangan untuk menentukan status:
     * - Jika jumlah >= 3, return 'Banyak'
     * - Jika jumlah < 3, return 'Sedikit'
     * 
     * @param int $jumlah Jumlah alat yang dipinjam (1-10)
     * @return string Status 'Banyak' atau 'Sedikit'
     */
    function statusJumlah($jumlah)
    {
        if ($jumlah >= 3) {
            return 'Banyak';
        } else {
            return 'Sedikit';
        }
    }
}
