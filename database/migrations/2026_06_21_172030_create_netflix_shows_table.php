<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('netflix_shows', function (Blueprint $table) {
            $table->id();
            $table->string('show_id')->nullable();
            $table->string('type')->nullable(); // Menampung 'Movie' atau 'TV Show'
            $table->string('title')->nullable();
            $table->text('director')->nullable(); // Pakai text karena nama sutradara bisa panjang
            $table->text('cast')->nullable();     // Pakai text karena pemainnya banyak
            $table->string('country')->nullable();
            $table->string('date_added')->nullable();
            $table->integer('release_year')->nullable(); // Tahun rilis (angka)
            $table->string('rating')->nullable();       // Rating usia (TV-MA, PG-13, dll)
            $table->string('duration')->nullable();     // Durasi (menit atau season)
            $table->text('genres')->nullable();         // Kategori / Genre
            $table->text('description')->nullable();    // Sinopsis film
            $table->timestamps(); // Membuat kolom created_at & updated_at otomatis
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('netflix_shows');
    }
};
