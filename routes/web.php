<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PeminjamanController;

Route::get('/', function () {
    return redirect('/peminjaman');
});

// Route untuk peminjaman alat
Route::get('/peminjaman', [PeminjamanController::class, 'index']);
Route::post('/peminjaman', [PeminjamanController::class, 'store']);
Route::get('/peminjaman/{id}/edit', [PeminjamanController::class, 'edit']);
Route::put('/peminjaman/{id}', [PeminjamanController::class, 'update']);
Route::delete('/peminjaman/{id}', [PeminjamanController::class, 'destroy']);
