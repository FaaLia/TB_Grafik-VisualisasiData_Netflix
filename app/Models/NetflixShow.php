<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NetflixShow extends Model
{
    // Ditambahkan agar Laravel mengizinkan semua kolom diisi otomatis dari CSV
    protected $guarded = []; 
}