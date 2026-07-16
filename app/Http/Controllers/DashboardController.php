<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\NetflixShow;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        return view('dashboard');
    }

    // FUNGSI API UNTUK MEMPROSES CSV DAN MENGEMBALIKAN DATA GRAFIK (JSON)
    public function upload(Request $request)
    {
        // 1. Validasi apakah file benar-benar di-upload dan berformat csv
        $request->validate([
            'csv_file' => 'required|mimes:csv,txt'
        ]);

        // 2. Kosongkan database lama agar tidak menumpuk saat di-upload ulang
        NetflixShow::truncate();

        // 3. Baca File CSV baris demi baris
        $file = $request->file('csv_file');
        $handle = fopen($file->getRealPath(), 'r');
        
        // Lewati baris pertama (karena baris pertama adalah judul kolom / header)
        $header = fgetcsv($handle, 0, ','); 

        // Looping untuk memasukkan data CSV ke Database MySQL
        while (($row = fgetcsv($handle, 0, ',')) !== false) {
            // Hindari error jika barisnya kosong
            if (!isset($row[1])) continue; 

            NetflixShow::create([
                'show_id'      => $row[0] ?? null,
                'type'         => $row[1] ?? null,
                'title'        => $row[2] ?? null,
                'director'     => $row[3] ?? null,
                'cast'         => $row[4] ?? null,
                'country'      => $row[5] ?? null,
                'date_added'   => $row[6] ?? null,
                'release_year' => isset($row[7]) ? (int)$row[7] : null,
                'rating'       => $row[8] ?? null,
                'duration'     => $row[9] ?? null,
                'genres'       => $row[10] ?? null,
                'description'  => $row[11] ?? null,
            ]);
        }
        fclose($handle);

        // 4. HITUNG DATA UNTUK GRAFIK (PROSES API)
        // Grafik 1: Hitung jumlah Movie vs TV Show
        $typeData = NetflixShow::select('type', DB::raw('count(*) as total'))
            ->groupBy('type')->get();

        // Grafik 2: Hitung tren rilis 10 tahun terakhir
        $yearData = NetflixShow::select('release_year', DB::raw('count(*) as total'))
            ->where('release_year', '>', 2012)
            ->groupBy('release_year')->orderBy('release_year', 'asc')->get();

        // Grafik 3: Hitung Distribusi Rating Usia (Top 5)
        $ratingData = NetflixShow::select('rating', DB::raw('count(*) as total'))
            ->whereNotNull('rating')
            ->groupBy('rating')->orderBy('total', 'desc')->take(5)->get();

        // Grafik 4: Hitung Genre Terbanyak (Top 5)
        // Karena satu film bisa punya banyak genre (dipisah koma), kita hitung pakai bantuan PHP array
        $allGenres = NetflixShow::pluck('genres')->toArray();
        $genreCounts = [];
        foreach ($allGenres as $genres) {
            $exploded = explode(',', $genres);
            foreach ($exploded as $genre) {
                $genreName = trim($genre);
                if($genreName != "") {
                    $genreCounts[$genreName] = ($genreCounts[$genreName] ?? 0) + 1;
                }
            }
        }
        arsort($genreCounts); // Urutkan dari yang terbesar
        $topGenres = array_slice($genreCounts, 0, 5); // Ambil 5 teratas

        // Grafik 5: Hitung Top 5 Negara Asal Produsen Konten
        // Diolah pakai PHP array karena satu film bisa diproduksi oleh kolaborasi beberapa negara (dipisah koma)
        $allCountries = NetflixShow::pluck('country')->toArray();
        $countryCounts = [];
        foreach ($allCountries as $countries) {
            if (!$countries) continue;
            $exploded = explode(',', $countries);
            foreach ($exploded as $country) {
                $countryName = trim($country);
                if($countryName != "") {
                    $countryCounts[$countryName] = ($countryCounts[$countryName] ?? 0) + 1;
                }
            }
        }
        arsort($countryCounts); // Urutkan dari jumlah terbanyak
        $topCountries = array_slice($countryCounts, 0, 5); // Ambil 5 teratas

        // Grafik 6: Hitung Top 5 Sutradara Terproduktif
        // Diolah pakai PHP array karena ada film yang disutradarai lebih dari 1 orang
        $allDirectors = NetflixShow::pluck('director')->toArray();
        $directorCounts = [];
        foreach ($allDirectors as $directors) {
            if (!$directors) continue;
            $exploded = explode(',', $directors);
            foreach ($exploded as $director) {
                $directorName = trim($director);
                if($directorName != "") {
                    $directorCounts[$directorName] = ($directorCounts[$directorName] ?? 0) + 1;
                }
            }
        }
        arsort($directorCounts); // Urutkan dari sutradara dengan karya terbanyak
        $topDirectors = array_slice($directorCounts, 0, 5); // Ambil 5 teratas


        // 5. Bungkus semua data hasil hitungan ke dalam format JSON untuk dikirim ke Javascript
        return response()->json([
            'status' => 'success',
            'data' => [
                'type' => [
                    'labels' => $typeData->pluck('type'),
                    'values' => $typeData->pluck('total')
                ],
                'year' => [
                    'labels' => $yearData->pluck('release_year'),
                    'values' => $yearData->pluck('total')
                ],
                'rating' => [
                    'labels' => $ratingData->pluck('rating'),
                    'values' => $ratingData->pluck('total')
                ],
                'genre' => [
                    'labels' => array_keys($topGenres),
                    'values' => array_values($topGenres)
                ],
                'country' => [
                    'labels' => array_keys($topCountries),
                    'values' => array_values($topCountries)
                ],
                'director' => [
                    'labels' => array_keys($topDirectors),
                    'values' => array_values($topDirectors)
                ]
            ]
        ]);
    }
}