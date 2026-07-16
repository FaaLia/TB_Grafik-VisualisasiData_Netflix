<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Netflix Analytics Dashboard</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .chart-wrapper {
            position: relative;
            height: 280px;
            width: 100%;
        }
    </style>
</head>

<body class="bg-light">

    <div class="container my-5">
        <h1 class="text-center mb-4">🎬 Netflix Data Analytics Dashboard</h1>

        <div class="card mb-4 shadow-sm">
            <div class="card-body">
                <h5 class="card-title">Upload Dataset Baru</h5>
                <form id="uploadForm">
                    <div class="input-group">
                        <input type="file" class="form-control" id="csvFile" accept=".csv" required>
                        <button class="btn btn-primary" type="submit" id="btnUpload">Upload CSV</button>
                    </div>
                </form>
                <div id="uploadStatus" class="mt-2 text-muted" style="display:none;">Sedang memproses file, mohon tunggu...</div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-md-6">
                <div class="card shadow-sm h-100">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title text-center mb-3">Tipe Konten (Movie vs TV Show)</h5>
                        <div class="chart-wrapper mt-auto">
                            <canvas id="chartType"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card shadow-sm h-100">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title text-center mb-3">Tren Rilis Konten</h5>
                        <div class="chart-wrapper mt-auto">
                            <canvas id="chartYear"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card shadow-sm h-100">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title text-center mb-3">Top 5 Genre Terbanyak</h5>
                        <div class="chart-wrapper mt-auto">
                            <canvas id="chartGenre"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card shadow-sm h-100">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title text-center mb-3">Distribusi Rating Usia</h5>
                        <div class="chart-wrapper mt-auto">
                            <canvas id="chartRating"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card shadow-sm h-100">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title text-center mb-3">Top 5 Negara Produsen Konten</h5>
                        <div class="chart-wrapper mt-auto">
                            <canvas id="chartCountry"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card shadow-sm h-100">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title text-center mb-3">Top 5 Sutradara Terproduktif</h5>
                        <div class="chart-wrapper mt-auto">
                            <canvas id="chartDirector"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Opsi global untuk grafik lingkaran & garis
        const chartOptions = {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        };

        // 1. Chart Type (Pie)
        const chartType = new Chart(document.getElementById('chartType'), {
            type: 'pie',
            data: {
                labels: ['Movie', 'TV Show'],
                datasets: [{
                    data: [70, 30],
                    backgroundColor: ['#E50914', '#B81D24']
                }]
            },
            options: { // <-- DIUBAH MENJADI SEPERTI INI
                responsive: true,
                maintainAspectRatio: false
            }
        });
        
        // 2. Chart Year (Line)
        const chartYear = new Chart(document.getElementById('chartYear'), {
            type: 'line',
            data: {
                labels: ['2018', '2019', '2020', '2021', '2022'],
                datasets: [{
                    label: 'Jumlah Konten',
                    data: [100, 150, 130, 180, 210],
                    borderColor: '#E50914',
                    tension: 0.1
                }]
            },
            options: chartOptions
        });

        // 3. Chart Genre (Bar - Vertical)
        const chartGenre = new Chart(document.getElementById('chartGenre'), {
            type: 'bar',
            data: {
                labels: ['Dramas', 'Comedies', 'Action', 'Documentaries', 'International'],
                datasets: [{
                    label: 'Total',
                    data: [45, 38, 30, 25, 20],
                    backgroundColor: '#E50914',
                    barThickness: 28,
                    borderRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false
                        }
                    },
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)'
                        }
                    }
                }
            }
        });

        // 4. Chart Rating (Doughnut)
        const chartRating = new Chart(document.getElementById('chartRating'), {
            type: 'doughnut',
            data: {
                labels: ['TV-MA', 'TV-14', 'TV-PG', 'R', 'PG-13'],
                datasets: [{
                    data: [40, 25, 15, 12, 8],
                    backgroundColor: ['#E50914', '#221F1F', '#F5F5F1', '#4A4A4A', '#8C8C8C']
                }]
            },
            options: { // <-- DIUBAH MENJADI SEPERTI INI
                responsive: true,
                maintainAspectRatio: false
            }
        });

        // 5. Chart Country (Bar - Horizontal)
        const chartCountry = new Chart(document.getElementById('chartCountry'), {
            type: 'bar',
            data: {
                labels: ['United States', 'India', 'United Kingdom', 'Canada', 'France'],
                datasets: [{
                    label: 'Total Konten',
                    data: [55, 35, 25, 18, 12],
                    backgroundColor: '#B81D24', // Warna Merah gelap variasi Netflix
                    barThickness: 18,
                    borderRadius: 4
                }]
            },
            options: {
                indexAxis: 'y', // Mengubah chart batang menjadi horizontal
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)'
                        }
                    },
                    y: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });

        // 6. Chart Director (Bar - Horizontal)
        const chartDirector = new Chart(document.getElementById('chartDirector'), {
            type: 'bar',
            data: {
                labels: ['Sutradara A', 'Sutradara B', 'Sutradara C', 'Sutradara D', 'Sutradara E'],
                datasets: [{
                    label: 'Total Karya',
                    data: [18, 14, 12, 10, 8],
                    backgroundColor: '#4A4A4A', // Warna abu gelap kontras estetik
                    barThickness: 18,
                    borderRadius: 4
                }]
            },
            options: {
                indexAxis: 'y', // Mengubah chart batang menjadi horizontal
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)'
                        }
                    },
                    y: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });

        // === LOGIKA API AJAX UPLOAD CSV ===
        document.getElementById('uploadForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const fileInput = document.getElementById('csvFile');
            if (fileInput.files.length === 0) return;

            const formData = new FormData();
            formData.append('csv_file', fileInput.files[0]);

            document.getElementById('uploadStatus').style.display = 'block';
            document.getElementById('btnUpload').disabled = true;

            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            fetch('/api/upload', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    }
                })
                .then(response => response.json())
                .then(res => {
                    document.getElementById('uploadStatus').style.display = 'none';
                    document.getElementById('btnUpload').disabled = false;

                    if (res.status === 'success') {
                        // Update Grafik 1 (Type)
                        chartType.data.labels = res.data.type.labels;
                        chartType.data.datasets[0].data = res.data.type.values;
                        chartType.update();

                        // Update Grafik 2 (Year)
                        chartYear.data.labels = res.data.year.labels;
                        chartYear.data.datasets[0].data = res.data.year.values;
                        chartYear.update();

                        // Update Grafik 3 (Genre)
                        chartGenre.data.labels = res.data.genre.labels;
                        chartGenre.data.datasets[0].data = res.data.genre.values;
                        chartGenre.update();

                        // Update Grafik 4 (Rating)
                        chartRating.data.labels = res.data.rating.labels;
                        chartRating.data.datasets[0].data = res.data.rating.values;
                        chartRating.update();

                        // Update Grafik 5 (Country) - TAMBAHAN
                        chartCountry.data.labels = res.data.country.labels;
                        chartCountry.data.datasets[0].data = res.data.country.values;
                        chartCountry.update();

                        // Update Grafik 6 (Director) - TAMBAHAN
                        chartDirector.data.labels = res.data.director.labels;
                        chartDirector.data.datasets[0].data = res.data.director.values;
                        chartDirector.update();
                    } else {
                        alert('Gagal memproses file: ' + res.message);
                    }
                })
                .catch(error => {
                    console.error(error);
                    document.getElementById('uploadStatus').style.display = 'none';
                    document.getElementById('btnUpload').disabled = false;
                    alert('Terjadi kesalahan sistem.');
                });
        });
    </script>
</body>

</html>