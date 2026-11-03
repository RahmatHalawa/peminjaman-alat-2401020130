<?php

if (!function_exists('statusJumlah')) {
    /**
     * Menampilkan status berdasarkan jumlah alat
     * Banyak jika >= 3, Sedikit jika < 3
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
