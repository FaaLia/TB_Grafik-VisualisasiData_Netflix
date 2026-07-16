<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;

Route::get('/', [DashboardController::class, 'index']);
// Route API untuk upload akan kita tambah di bawah nanti
Route::post('/api/upload', [DashboardController::class, 'upload']);