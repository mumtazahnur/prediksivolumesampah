<!DOCTYPE html><html class="light" lang="en"><head>
<meta charset="utf-8">
<meta content="width=device-width, initial-scale=1.0" name="viewport">
<title>WastePredict - Prediction Input</title>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&amp;display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet">
<!-- Tailwind Configuration -->
<script id="tailwind-config">
      tailwind.config = {
        darkMode: "class",
        theme: {
          extend: {
            "colors": {
                    "on-secondary-fixed": "#101e1a",
                    "on-secondary-container": "#566660",
                    "on-tertiary-fixed-variant": "#004395",
                    "on-primary-fixed": "#002117",
                    "secondary-container": "#d3e3dc",
                    "primary-container": "#064e3b",
                    "secondary-fixed": "#d5e6df",
                    "on-primary": "#ffffff",
                    "surface-container": "#edeeef",
                    "on-secondary-fixed-variant": "#3b4a44",
                    "inverse-surface": "#2e3132",
                    "on-tertiary-container": "#8cb1ff",
                    "tertiary": "#002c65",
                    "outline-variant": "#bfc9c3",
                    "background": "#f8f9fa",
                    "on-surface-variant": "#404944",
                    "surface-bright": "#f8f9fa",
                    "secondary-fixed-dim": "#bacac3",
                    "surface-variant": "#e1e3e4",
                    "on-tertiary": "#ffffff",
                    "on-secondary": "#ffffff",
                    "on-primary-fixed-variant": "#0b513d",
                    "on-error": "#ffffff",
                    "on-tertiary-fixed": "#001a42",
                    "surface-container-lowest": "#ffffff",
                    "primary": "#003527",
                    "inverse-on-surface": "#f0f1f2",
                    "secondary": "#52625c",
                    "surface": "#f8f9fa",
                    "tertiary-container": "#004190",
                    "surface-container-low": "#f3f4f5",
                    "surface-tint": "#2b6954",
                    "tertiary-fixed-dim": "#adc6ff",
                    "inverse-primary": "#95d3ba",
                    "on-error-container": "#93000a",
                    "surface-container-high": "#e7e8e9",
                    "surface-dim": "#d9dadb",
                    "error": "#ba1a1a",
                    "primary-fixed-dim": "#95d3ba",
                    "outline": "#707974",
                    "tertiary-fixed": "#d8e2ff",
                    "on-primary-container": "#80bea6",
                    "on-background": "#191c1d",
                    "error-container": "#ffdad6",
                    "surface-container-highest": "#e1e3e4",
                    "on-surface": "#191c1d",
                    "primary-fixed": "#b0f0d6"
            },
            "borderRadius": {
                    "DEFAULT": "0.125rem",
                    "lg": "0.25rem",
                    "xl": "0.5rem",
                    "full": "0.75rem"
            },
            "spacing": {
                    "margin-desktop": "32px",
                    "margin-mobile": "16px",
                    "gutter": "24px",
                    "container-max": "1440px",
                    "unit": "8px"
            },
            "fontFamily": {
                    "headline-lg-mobile": ["Inter"],
                    "label-md": ["Inter"],
                    "body-lg": ["Inter"],
                    "headline-md": ["Inter"],
                    "body-md": ["Inter"],
                    "label-sm": ["Inter"],
                    "display-lg": ["Inter"],
                    "headline-lg": ["Inter"]
            },
            "fontSize": {
                    "headline-lg-mobile": ["24px", {"lineHeight": "32px", "fontWeight": "600"}],
                    "label-md": ["14px", {"lineHeight": "20px", "letterSpacing": "0.01em", "fontWeight": "500"}],
                    "body-lg": ["18px", {"lineHeight": "28px", "fontWeight": "400"}],
                    "headline-md": ["24px", {"lineHeight": "32px", "fontWeight": "600"}],
                    "body-md": ["16px", {"lineHeight": "24px", "fontWeight": "400"}],
                    "label-sm": ["12px", {"lineHeight": "16px", "letterSpacing": "0.05em", "fontWeight": "600"}],
                    "display-lg": ["48px", {"lineHeight": "56px", "letterSpacing": "-0.02em", "fontWeight": "700"}],
                    "headline-lg": ["32px", {"lineHeight": "40px", "letterSpacing": "-0.01em", "fontWeight": "600"}]
            }
          },
        },
      }
    </script>
<style>
        body { font-family: 'Inter', sans-serif; background-color: #f8f9fa; }
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }
        .glass-card {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }
        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
            100% { transform: translateY(0px); }
        }
        .float-animation { animation: float 4s ease-in-out infinite; }
    </style>
</head>
<body class="text-on-surface">
<!-- Sidebar Navigation -->
<aside class="fixed left-0 top-0 h-full w-[280px] bg-surface-container flex flex-col p-4 gap-2 hidden md:flex">
<div class="flex items-center gap-3 px-2 mb-8">
<div class="w-10 h-10 bg-primary-container rounded-lg flex items-center justify-center text-on-primary-container">
<span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">recycling</span>
</div>
<div>
<h1 class="font-headline-sm text-headline-sm font-black text-primary">WastePredict</h1>
<p class="text-xs text-on-surface-variant uppercase tracking-wider font-bold">Industrial Intelligence</p>
</div>
</div>
<nav class="flex-1 flex flex-col gap-1">
<a class="flex items-center gap-3 p-3 text-on-surface-variant hover:bg-surface-container-high rounded-lg transition-all font-label-md" href="index.php">
<span class="material-symbols-outlined">dashboard</span> Overview
            </a>
<a class="flex items-center gap-3 p-3 text-on-surface-variant hover:bg-surface-container-high rounded-lg transition-all font-label-md" href="datasets.php">
<span class="material-symbols-outlined">database</span> Datasets
            </a>
<a class="flex items-center gap-3 p-3 text-on-surface-variant hover:bg-surface-container-high rounded-lg transition-all font-label-md" href="predictionPage.php">
<span class="material-symbols-outlined">query_stats</span> Predictions
            </a>
<a class="flex items-center gap-3 p-3 bg-secondary-container text-on-secondary-container rounded-lg font-label-md" href="inputPage.php">
<span class="material-symbols-outlined">edit_note</span> Input Tool
            </a>
</nav>
<button class="mt-4 mb-8 bg-primary text-on-primary py-3 px-4 rounded-xl font-label-md flex items-center justify-center gap-2 hover:opacity-90 active:scale-95 hover:shadow-lg hover:-translate-y-1 transition-all duration-300">
<span class="material-symbols-outlined text-sm">auto_awesome</span> Generate Insights
        </button>
<div class="pt-4 border-t border-outline-variant flex flex-col gap-1">
<a class="flex items-center gap-3 p-3 text-on-surface-variant hover:bg-surface-container-high rounded-lg transition-all font-label-md" href="#">
<span class="material-symbols-outlined">description</span> Documentation
            </a>
<a class="flex items-center gap-3 p-3 text-on-surface-variant hover:bg-surface-container-high rounded-lg transition-all font-label-md" href="#">
<span class="material-symbols-outlined">help</span> Support
            </a>
</div>
</aside>
<!-- Main Content Area -->
<main class="md:ml-[280px] min-h-screen flex flex-col">
<!-- Top App Bar -->
<header class="bg-surface shadow-sm sticky top-0 z-10">
<div class="flex justify-between items-center w-full px-margin-desktop py-4 max-w-container-max mx-auto">
<div class="flex items-center gap-8">
<span class="font-headline-md text-headline-md font-bold text-primary md:hidden">WP</span>
<nav class="hidden lg:flex items-center gap-6">
<a class="font-label-md text-on-surface-variant hover:text-primary transition-colors" href="index.php">Dashboard</a>
<a class="font-label-md text-on-surface-variant hover:text-primary transition-colors" href="datasets.php">Datasets</a>
<a class="font-label-md text-on-surface-variant hover:text-primary transition-colors" href="predictionPage.php">Predictions</a>
<a class="font-label-md text-primary border-b-2 border-primary pb-1" href="inputPage.php">Input Tool</a>
</nav>
</div>
<div class="flex items-center gap-4">
<div class="relative hidden sm:block">
<input class="pl-10 pr-4 py-2 bg-surface-container-low border-none rounded-full text-sm focus:ring-2 focus:ring-primary w-64" placeholder="Search data..." type="text">
<span class="material-symbols-outlined absolute left-3 top-2 text-on-surface-variant text-xl">search</span>
</div>
<button class="text-on-surface-variant hover:text-primary"><span class="material-symbols-outlined">notifications</span></button>
<button class="text-on-surface-variant hover:text-primary"><span class="material-symbols-outlined">settings</span></button>
<div class="w-8 h-8 rounded-full overflow-hidden border border-outline-variant">
<img class="w-full h-full object-cover" data-alt="A professional headshot of a corporate environmental analyst in high-key studio lighting. The user has a confident smile and is wearing modern, minimal business casual attire. The image is clean and bright, matching a light-mode modern UI aesthetic with a soft gray background." src="https://lh3.googleusercontent.com/aida-public/AB6AXuCKQzMaVpAY_JlMm5jJooPh8l0T3KCddVcU4RyOQCAeI8Qwuye_XRSzuK1O-sdEpT7qjX18sdCVGD_4RDToXttuydY4nDqQ1CkM0gsAcJ17fi97SYT_y7CQCsEHUYwN3Nthpmq-Ny-v9IEtFLByZuJB7InJ8_WzI2K9Tc0raajamGe4tWE6Bj0oiqyg89P4DqskHAlXGwGRBMGs2kG8EvE3sMHGQfsZGWRsMIuencl_dqCwL9jk12JW5OeKuf-7drHPcjz6he1o2Hw">
</div>
</div>
</div>
</header>
<!-- Canvas -->
<div class="flex-1 p-8 max-w-6xl mx-auto w-full">
<div class="grid grid-cols-1 lg:grid-cols-12 gap-gutter">
<!-- Prediction Form Section -->
<div class="lg:col-span-4 space-y-6">
<div>
<h2 class="font-headline-lg text-headline-lg text-primary">Predictive Engine</h2>
<p class="font-body-md text-on-surface-variant mt-2">Enter a future year to estimate total waste generation based on current industrial growth trends.</p>
</div>
<div class="bg-surface-container-lowest p-6 rounded-xl shadow-sm border border-outline-variant">
<form class="space-y-4" id="prediction-form">
<div>
<label class="font-label-sm text-on-surface-variant uppercase mb-2 block" for="year-input">Target Year</label>
<input class="w-full p-4 bg-surface-container-low border-2 border-transparent focus:border-primary focus:ring-0 rounded-lg text-lg font-bold transition-all" id="year-input" max="2100" min="2024" placeholder="e.g., 2030" required="" type="number">
</div>
<button class="w-full bg-primary text-on-primary py-4 rounded-lg font-label-md flex items-center justify-center gap-2 hover:opacity-90 active:scale-95 hover:shadow-lg hover:-translate-y-1 transition-all duration-300" type="submit">
<span class="material-symbols-outlined">analytics</span> Run Simulation
                            </button>
</form>
<div class="mt-6 p-4 bg-secondary-fixed text-on-secondary-fixed rounded-lg text-xs flex gap-3">
<span class="material-symbols-outlined">info</span>
<p>Prediction is based on the <b>2023 baseline dataset</b> including population density and industrial output metrics.</p>
</div>
</div>
</div>
<!-- Results Display Area -->
<div class="lg:col-span-8">
<div class="h-full flex flex-col items-center justify-center border-2 border-dashed border-outline-variant rounded-2xl p-12 text-center opacity-50" id="results-placeholder">
<span class="material-symbols-outlined text-6xl mb-4 text-on-surface-variant">monitoring</span>
<p class="font-headline-md text-on-surface-variant">Ready for Input</p>
<p class="font-body-md">Fill in the target year to generate the predictive report.</p>
</div>
<div class="hidden h-full" id="results-card">
<div class="glass-card rounded-2xl p-8 h-full shadow-lg relative overflow-hidden flex flex-col border-2 border-primary-container/20">
<!-- Background Decoration -->
<div class="absolute -top-12 -right-12 w-48 h-48 bg-primary-container/10 rounded-full blur-3xl"></div>
<div class="flex justify-between items-start relative z-10">
<div>
<span class="inline-block px-3 py-1 bg-primary text-on-primary rounded-full text-[10px] font-bold uppercase tracking-widest mb-4">Simulated Report</span>
<h3 class="font-headline-lg text-headline-lg text-primary">Projection for <span id="display-year">2030</span></h3>
</div>
<div class="bg-surface-container-high p-3 rounded-xl float-animation">
<span class="material-symbols-outlined text-primary text-4xl" style="font-variation-settings: 'FILL' 1;">delete_sweep</span>
</div>
</div>
<div class="mt-12 mb-auto grid grid-cols-1 md:grid-cols-2 gap-8 relative z-10">
<div>
<p class="font-label-sm text-on-surface-variant uppercase tracking-widest">Estimated Volume</p>
<div class="flex items-baseline gap-2 mt-1">
<span class="font-display-lg text-display-lg text-primary" id="display-volume">1,240</span>
<span class="font-headline-md text-on-surface-variant">Tons</span>
</div>
</div>
<div class="flex flex-col justify-center">
<div class="flex items-center gap-2 text-error" id="trend-container">
<span class="material-symbols-outlined" id="trend-icon">trending_up</span>
<span class="font-headline-md" id="display-percentage">+12.5%</span>
</div>
<p class="font-label-md text-on-surface-variant">vs baseline year (2025)</p>
</div>
</div>
<!-- Mini Chart Representation -->
<div class="mt-12 bg-surface-container-low p-6 rounded-xl relative z-10">
<div class="flex justify-between items-end h-24 gap-2">
<div class="bg-outline-variant w-full rounded-t" style="height: 40%"></div>
<div class="bg-outline-variant w-full rounded-t" style="height: 45%"></div>
<div class="bg-outline-variant w-full rounded-t" style="height: 55%"></div>
<div class="bg-outline-variant w-full rounded-t" style="height: 65%"></div>
<div class="bg-primary w-full rounded-t transition-all duration-1000" id="current-bar" style="height: 0%"></div>
</div>
<div class="flex justify-between mt-2 text-[10px] font-bold text-on-surface-variant uppercase">
<span>2016</span>
<span>2019</span>
<span>2022</span>
<span>2025</span>
<span class="text-primary" id="display-year-label">2026</span>
</div>
</div>
<div class="mt-8 flex gap-4 relative z-10">
<button class="flex-1 border border-primary text-primary py-3 rounded-lg font-label-md hover:bg-secondary-container transition-colors">Export PDF</button>
<button class="flex-1 bg-primary text-on-primary py-3 rounded-lg font-label-md hover:opacity-90 transition-opacity">Save Scenarios</button>
</div>
</div>
</div>
</div>
</div>
<!-- Visual Decorative Element -->
<div class="mt-12 grid grid-cols-1 md:grid-cols-3 gap-6">
<div class="p-6 bg-surface-container-low rounded-xl border border-outline-variant/30 flex items-center gap-4">
<div class="w-12 h-12 bg-white rounded-full flex items-center justify-center shadow-sm">
<span class="material-symbols-outlined text-primary">recycling</span>
</div>
<div>
<p class="font-label-sm text-on-surface-variant">Target Recycling</p>
<p class="font-headline-md text-primary">64%</p>
</div>
</div>
<div class="p-6 bg-surface-container-low rounded-xl border border-outline-variant/30 flex items-center gap-4">
<div class="w-12 h-12 bg-white rounded-full flex items-center justify-center shadow-sm">
<span class="material-symbols-outlined text-primary">factory</span>
</div>
<div>
<p class="font-label-sm text-on-surface-variant">Industrial Impact</p>
<p class="font-headline-md text-primary">High</p>
</div>
</div>
<div class="p-6 bg-surface-container-low rounded-xl border border-outline-variant/30 flex items-center gap-4">
<div class="w-12 h-12 bg-white rounded-full flex items-center justify-center shadow-sm">
<span class="material-symbols-outlined text-primary">precision_manufacturing</span>
</div>
<div>
<p class="font-label-sm text-on-surface-variant">Accuracy Score</p>
<p class="font-headline-md text-primary">98.2%</p>
</div>
</div>
</div>
</div>
<!-- Footer -->
<footer class="bg-surface-dim border-t border-outline-variant mt-auto">
<div class="flex flex-col md:flex-row justify-between items-center px-margin-desktop py-8 max-w-container-max mx-auto w-full">
<p class="font-label-sm text-on-surface-variant">© 2024 WastePredict AI. Environmental Stewardship through Precision.</p>
<div class="flex gap-6 mt-4 md:mt-0">
<a class="font-label-sm text-on-secondary-container hover:text-primary" href="#">Privacy Policy</a>
<a class="font-label-sm text-on-secondary-container hover:text-primary" href="#">Terms of Service</a>
<a class="font-label-sm text-on-secondary-container hover:text-primary" href="#">API Status</a>
</div>
</div>
</footer>
</main>
<script>
        // Mendengarkan event 'submit' pada form input tahun prediksi
        document.getElementById('prediction-form').addEventListener('submit', function(e) {
            e.preventDefault(); // Mencegah reload halaman secara default
            
            // Mengambil nilai tahun target yang diinput oleh user
            const year = document.getElementById('year-input').value;
            
            // Mengaktifkan efek loading pada tombol submit form
            const runBtn = e.target.querySelector('button[type="submit"]');
            const originalBtnHtml = runBtn.innerHTML;
            runBtn.disabled = true; // Menonaktifkan tombol sementara
            runBtn.innerHTML = '<span class="material-symbols-outlined animate-spin" style="font-variation-settings: \'opsz\' 20;">sync</span> Running...';

            // Memanggil API predict.php secara asynchronous menggunakan fetch
            fetch('predict.php?year=' + year)
                .then(response => response.json()) // Mengonversi respon server menjadi object JSON
                .then(data => {
                    // Mengembalikan tombol submit ke keadaan semula setelah respon diterima
                    runBtn.disabled = false;
                    runBtn.innerHTML = originalBtnHtml;

                    if (data.status === 'success') {
                        // Menyembunyikan card placeholder kosong dan menampilkan card hasil simulasi
                        document.getElementById('results-placeholder').classList.add('hidden');
                        const resultsCard = document.getElementById('results-card');
                        resultsCard.classList.remove('hidden');
                        
                        // Merender hasil prediksi ke elemen HTML (Tahun Target dan Total Volume Tahunan)
                        document.getElementById('display-year').innerText = data.target_year;
                        document.getElementById('display-year-label').innerText = data.target_year;
                        document.getElementById('display-volume').innerText = Math.round(data.total_annual).toLocaleString('id-ID');
                        
                        const trendIcon = document.getElementById('trend-icon');
                        const trendContainer = document.getElementById('trend-container');
                        const percentageText = document.getElementById('display-percentage');

                        // Menghitung & merender trend persentase kenaikan/penurunan dibanding tahun 2025
                        const growth = data.growth_rate_vs_2025;
                        if (growth >= 0) {
                            percentageText.innerText = `+${growth}%`;
                            trendContainer.className = "flex items-center gap-2 text-error"; // Warna merah jika naik
                            trendIcon.innerText = "trending_up";
                        } else {
                            percentageText.innerText = `${growth}%`;
                            trendContainer.className = "flex items-center gap-2 text-primary"; // Warna hijau jika turun
                            trendIcon.innerText = "trending_down";
                        }

                        // Menjalankan animasi tinggi grafik batang (bar chart) berdasarkan rasio volume dibanding tahun 2025
                        setTimeout(() => {
                            const ratio = Math.min(Math.max(30, (data.total_annual / data.total_2025_actual) * 65), 100);
                            document.getElementById('current-bar').style.height = ratio + '%';
                        }, 100);
                    } else {
                        // Menampilkan alert jika API predict.php mengembalikan error status
                        alert('Error: ' + data.message);
                    }
                })
                .catch(err => {
                    // Penanganan jika koneksi ke server gagal
                    runBtn.disabled = false;
                    runBtn.innerHTML = originalBtnHtml;
                    alert('Gagal menghubungi engine prediksi: ' + err.message);
                });
        });

        // Initialize mobile menu or other micro-interactions if needed
        window.addEventListener('scroll', () => {
            const header = document.querySelector('header');
            if (window.scrollY > 20) {
                header.classList.add('shadow-md');
            } else {
                header.classList.remove('shadow-md');
            }
        });
    </script>
</body></html>