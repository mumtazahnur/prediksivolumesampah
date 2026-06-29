<!DOCTYPE html><html class="light" lang="id"><head>
<meta charset="utf-8">
<meta content="width=device-width, initial-scale=1.0" name="viewport">
<title>WastePredict - Prediksi &amp; Metodologi</title>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;900&amp;display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet">
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
                        "headline-lg": ["Inter"],
                        "headline-md": ["Inter"],
                        "body-lg": ["Inter"],
                        "body-md": ["Inter"],
                        "label-md": ["Inter"],
                        "label-sm": ["Inter"],
                        "display-lg": ["Inter"]
                    }
                }
            }
        }
    </script>
<style>
        body { font-family: 'Inter', sans-serif; background-color: #f8f9fa; }
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
            vertical-align: middle;
        }
        .chart-container {
            position: relative;
            height: 300px;
            width: 100%;
        }
        .bento-grid {
            display: grid;
            grid-template-columns: repeat(12, 1fr);
            gap: 24px;
        }
    </style>
</head>
<body class="text-on-surface">
<!-- SideNavBar -->
<aside class="fixed left-0 top-0 h-full w-[280px] bg-surface-container dark:bg-surface-container-low hidden md:flex flex-col p-4 gap-2 z-50">
<div class="flex items-center gap-3 px-4 py-6">
<div class="w-10 h-10 bg-primary rounded-lg flex items-center justify-center text-on-primary">
<span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">recycling</span>
</div>
<div>
<h1 class="font-headline-sm text-[20px] font-black text-primary">WastePredict</h1>
<p class="font-label-sm text-on-surface-variant opacity-70">Industrial Intelligence</p>
</div>
</div>
<nav class="flex-1 mt-4 space-y-1">
<a class="flex items-center gap-3 px-4 py-3 text-on-surface-variant hover:bg-surface-container-high rounded-lg transition-all" href="#">
<span class="material-symbols-outlined">dashboard</span>
<span class="font-label-md">Overview</span>
</a>
<a class="flex items-center gap-3 px-4 py-3 text-on-surface-variant hover:bg-surface-container-high rounded-lg transition-all" href="#">
<span class="material-symbols-outlined">database</span>
<span class="font-label-md">Datasets</span>
</a>
<a class="flex items-center gap-3 px-4 py-3 bg-secondary-container text-on-secondary-container rounded-lg font-medium" href="predictionPage.php">
<span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">query_stats</span>
<span class="font-label-md">Predictions</span>
</a>
<a class="flex items-center gap-3 px-4 py-3 text-on-surface-variant hover:bg-surface-container-high rounded-lg transition-all" href="inputPage.php">
<span class="material-symbols-outlined">edit_note</span>
<span class="font-label-md">Input Tool</span>
</a>
</nav>
<div class="mt-auto pt-4 border-t border-outline-variant space-y-1">
<button class="w-full bg-primary text-on-primary py-3 rounded-xl font-label-md flex items-center justify-center gap-2 hover:opacity-90 hover:shadow-lg hover:-translate-y-1 transition-all duration-300 mb-4">
<span class="material-symbols-outlined">auto_awesome</span>
                Generate Insights
            </button>
<a class="flex items-center gap-3 px-4 py-2 text-on-surface-variant hover:bg-surface-container-high rounded-lg transition-all" href="#">
<span class="material-symbols-outlined">description</span>
<span class="font-label-md">Documentation</span>
</a>
<a class="flex items-center gap-3 px-4 py-2 text-on-surface-variant hover:bg-surface-container-high rounded-lg transition-all" href="#">
<span class="material-symbols-outlined">help</span>
<span class="font-label-md">Support</span>
</a>
</div>
</aside>
<!-- Main Content Wrapper -->
<main class="md:ml-[280px] min-h-screen flex flex-col">
<!-- TopAppBar -->
<header class="sticky top-0 z-40 bg-surface shadow-sm w-full">
<div class="flex justify-between items-center w-full px-margin-desktop py-4 max-w-container-max mx-auto">
<div class="flex items-center gap-8">
<span class="font-headline-md text-headline-md font-bold text-primary block md:hidden">WastePredict</span>
<nav class="hidden lg:flex items-center gap-6">
<a class="text-on-surface-variant hover:text-primary transition-colors font-label-md" href="#">Dashboard</a>
<a class="text-on-surface-variant hover:text-primary transition-colors font-label-md" href="#">Datasets</a>
<a class="text-primary border-b-2 border-primary pb-1 font-label-md" href="predictionPage.php">Predictions</a>
<a class="text-on-surface-variant hover:text-primary transition-colors font-label-md" href="inputPage.php">Input Tool</a>
</nav>
</div>
<div class="flex items-center gap-4">
<div class="hidden sm:flex items-center bg-surface-container-high rounded-full px-4 py-1.5 gap-2 border border-outline-variant">
<span class="material-symbols-outlined text-on-surface-variant text-[20px]">search</span>
<input class="bg-transparent border-none focus:ring-0 text-label-md w-40" placeholder="Search predictions..." type="text">
</div>
<button class="p-2 text-on-surface-variant hover:bg-surface-container-high rounded-full transition-all">
<span class="material-symbols-outlined">notifications</span>
</button>
<button class="p-2 text-on-surface-variant hover:bg-surface-container-high rounded-full transition-all">
<span class="material-symbols-outlined">settings</span>
</button>
<div class="w-8 h-8 rounded-full bg-primary-container overflow-hidden border border-outline-variant">
<img class="w-full h-full object-cover" data-alt="A professional headshot of a senior waste management consultant looking confidently at the camera, wearing professional business attire. The background is a softly blurred modern office environment with warm morning light streaming through windows, creating a clean and authoritative corporate aesthetic." src="https://lh3.googleusercontent.com/aida-public/AB6AXuCzzhn6Foft7rtiIdh9qj1KVmAHsUhKNg0cJHwdPxR_l4fbdDJfBFf9N8odVUnwXKVndxfizBaTytidzpS1PB4M-zDWKCDE8cvOC5FXE5JF9KB6SKOonE8De97DZLFP4TpcjX0cpp8_QD71I1smCh1tne_qlaltqq_5rMSOxjAoE4lChYHBrEKmjZcVOlT6XbO0V6vYI4SrV8uqpi38D8UCjj7PJnTExsVr_6OpvvTJdyd3iNqTDp5LbwHdzf62SeY4TaX9Qiahffg">
</div>
</div>
</div>
</header>
<!-- Page Canvas -->
<div class="p-margin-desktop space-y-gutter max-w-container-max mx-auto w-full">
<!-- Header Section -->
<div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
<div>
<h2 class="font-headline-lg text-headline-lg text-on-surface">Prediksi &amp; Metodologi</h2>
<p class="font-body-md text-on-surface-variant max-w-2xl mt-2">Analisis mendalam mengenai proyeksi volume limbah industri berdasarkan tren historis dan pemodelan prediktif untuk strategi pengelolaan yang lebih cerdas.</p>
</div>
<div class="flex gap-3">
<button class="px-4 py-2 border border-primary text-primary font-label-md rounded-lg hover:bg-secondary-container transition-colors flex items-center gap-2">
<span class="material-symbols-outlined text-[18px]">download</span> Export Report
                    </button>
<button class="px-4 py-2 bg-primary text-on-primary font-label-md rounded-lg hover:opacity-90 transition-all duration-300 flex items-center gap-2 hover:shadow-lg hover:-translate-y-1">
<span class="material-symbols-outlined text-[18px]">add</span> New Report
                    </button>
</div>
</div>
<!-- Bento Grid Layout -->
<div class="bento-grid">
<!-- Main Chart Card (Column 8) -->
<div class="col-span-12 lg:col-span-8 bg-surface-container-lowest rounded-xl p-6 shadow-sm border border-outline-variant/30 flex flex-col">
<div class="flex justify-between items-center mb-6">
<div>
<h3 class="font-headline-md text-headline-md">Tren Volume Limbah</h3>
<p class="font-label-sm text-on-surface-variant">Historis (2019-2023) vs Prediksi (2024-2028)</p>
</div>
<div class="flex items-center gap-4">
<div class="flex items-center gap-2">
<span class="w-3 h-3 rounded-full bg-tertiary"></span>
<span class="font-label-sm">Historis</span>
</div>
<div class="flex items-center gap-2">
<span class="w-3 h-3 rounded-full bg-primary-container"></span>
<span class="font-label-sm">Prediksi</span>
</div>
</div>
</div>
<div class="chart-container flex items-end justify-between px-2 pt-4">
<!-- Custom SVG Chart Illustration -->
<svg class="w-full h-full overflow-visible" viewBox="0 0 800 300">
<!-- Grid Lines -->
<line stroke="#e1e3e4" stroke-width="1" x1="0" x2="800" y1="250" y2="250"></line>
<line stroke="#e1e3e4" stroke-width="1" x1="0" x2="800" y1="200" y2="200"></line>
<line stroke="#e1e3e4" stroke-width="1" x1="0" x2="800" y1="150" y2="150"></line>
<line stroke="#e1e3e4" stroke-width="1" x1="0" x2="800" y1="100" y2="100"></line>
<line stroke="#e1e3e4" stroke-width="1" x1="0" x2="800" y1="50" y2="50"></line>
<!-- Historical Line (Forest Green variant) -->
<path d="M0,230 L100,210 L200,215 L300,180 L400,165" fill="none" stroke="#002c65" stroke-linecap="round" stroke-width="3"></path>
<circle cx="400" cy="165" fill="#002c65" r="5"></circle>
<!-- Prediction Line (Dashed / Lighter Green) -->
<path d="M400,165 L500,140 L600,120 L700,95 L800,80" fill="none" stroke="#2b6954" stroke-dasharray="8,4" stroke-linecap="round" stroke-width="3"></path>
<!-- Labels -->
<text fill="#707974" font-size="12" x="0" y="270">2019</text>
<text fill="#707974" font-size="12" x="100" y="270">2020</text>
<text fill="#707974" font-size="12" x="200" y="270">2021</text>
<text fill="#707974" font-size="12" x="300" y="270">2022</text>
<text fill="#191c1d" font-size="12" font-weight="bold" x="400" y="270">2023</text>
<text fill="#707974" font-size="12" x="500" y="270">2024 (P)</text>
<text fill="#707974" font-size="12" x="600" y="270">2025 (P)</text>
<text fill="#707974" font-size="12" x="700" y="270">2026 (P)</text>
<text fill="#707974" font-size="12" x="770" y="270">2027</text>
</svg>
</div>
<div class="mt-6 flex items-center justify-between p-4 bg-surface-container-low rounded-lg">
<div class="flex items-center gap-3">
<span class="material-symbols-outlined text-primary">trending_up</span>
<span class="font-label-md">Prediksi pertumbuhan volume tahunan rata-rata sebesar 4.2%</span>
</div>
<span class="text-label-sm text-on-surface-variant">Terakhir diperbarui: 12 Okt 2023</span>
</div>
</div>
<!-- Methodology Panel (Column 4) -->
<div class="col-span-12 lg:col-span-4 space-y-6">
<div class="bg-primary text-on-primary rounded-xl p-6 shadow-md relative overflow-hidden h-full">
<!-- Abstract pattern background -->
<div class="absolute top-0 right-0 opacity-10 pointer-events-none transform translate-x-1/4 -translate-y-1/4">
<span class="material-symbols-outlined text-[200px]" style="font-variation-settings: 'FILL' 1;">insights</span>
</div>
<h3 class="font-headline-md text-headline-md mb-4 relative z-10">Metodologi</h3>
<div class="space-y-4 relative z-10">
<div>
<h4 class="font-label-md font-bold text-primary-fixed mb-1">Time Series Analysis</h4>
<p class="font-body-md opacity-90 leading-relaxed">Kami menggunakan model ARIMA (AutoRegressive Integrated Moving Average) untuk menganalisis data berurutan waktu dan mengidentifikasi pola musiman limbah industri.</p>
</div>
<div class="h-px bg-on-primary opacity-20"></div>
<div>
<h4 class="font-label-md font-bold text-primary-fixed mb-1">Linear Regression</h4>
<p class="font-body-md opacity-90 leading-relaxed">Model regresi digunakan untuk memahami kaitan antara kapasitas produksi pabrik dengan volume limbah yang dihasilkan setiap bulannya.</p>
</div>
<div class="mt-6 pt-4 border-t border-on-primary/20">
<button class="flex items-center gap-2 font-label-md hover:underline">
                                    Lihat Detail Teknis <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
</button>
</div>
</div>
</div>
</div>
<!-- Summary Table Card (Column 12) -->
<div class="col-span-12 bg-white rounded-xl shadow-sm border border-outline-variant/30 overflow-hidden">
<div class="px-6 py-4 border-b border-outline-variant flex justify-between items-center">
<h3 class="font-headline-md text-headline-md">Ringkasan Hasil Prediksi 5 Tahun</h3>
<div class="flex gap-2">
<span class="px-3 py-1 bg-secondary-container text-on-secondary-container rounded-full text-label-sm">Confidence Level: 94%</span>
</div>
</div>
<div class="overflow-x-auto">
<table class="w-full text-left border-collapse">
<thead>
<tr class="bg-surface-container-low">
<th class="px-6 py-4 font-label-sm text-on-surface-variant uppercase tracking-wider">Tahun</th>
<th class="px-6 py-4 font-label-sm text-on-surface-variant uppercase tracking-wider">Prediksi Volume (Ton)</th>
<th class="px-6 py-4 font-label-sm text-on-surface-variant uppercase tracking-wider">Pertumbuhan (%)</th>
<th class="px-6 py-4 font-label-sm text-on-surface-variant uppercase tracking-wider">Margin Error (+/-)</th>
<th class="px-6 py-4 font-label-sm text-on-surface-variant uppercase tracking-wider">Rekomendasi Aksi</th>
</tr>
</thead>
<tbody class="divide-y divide-outline-variant/30">
<tr class="hover:bg-surface-container-lowest transition-colors cursor-pointer group hover:shadow-[0_0_15px_rgba(0,0,0,0.1)] hover:z-10 relative">
<td class="px-6 py-4 font-label-md font-bold">2024</td>
<td class="px-6 py-4 font-body-md">12,450</td>
<td class="px-6 py-4 font-body-md text-error">+3.2%</td>
<td class="px-6 py-4 font-body-md">1.5%</td>
<td class="px-6 py-4">
<span class="px-2 py-1 bg-surface-container-high rounded text-label-sm">Optimalisasi Rute</span>
</td>
</tr>
<tr class="bg-surface-container-lowest/50 hover:bg-surface-container-lowest transition-colors cursor-pointer group hover:shadow-[0_0_15px_rgba(0,0,0,0.1)] hover:z-10 relative">
<td class="px-6 py-4 font-label-md font-bold">2025</td>
<td class="px-6 py-4 font-body-md">13,100</td>
<td class="px-6 py-4 font-body-md text-error">+5.2%</td>
<td class="px-6 py-4 font-body-md">2.1%</td>
<td class="px-6 py-4">
<span class="px-2 py-1 bg-secondary-container text-on-secondary-container rounded text-label-sm">Ekspansi Fasilitas</span>
</td>
</tr>
<tr class="hover:bg-surface-container-lowest transition-colors cursor-pointer group hover:shadow-[0_0_15px_rgba(0,0,0,0.1)] hover:z-10 relative">
<td class="px-6 py-4 font-label-md font-bold">2026</td>
<td class="px-6 py-4 font-body-md">13,550</td>
<td class="px-6 py-4 font-body-md text-error">+3.4%</td>
<td class="px-6 py-4 font-body-md">2.8%</td>
<td class="px-6 py-4">
<span class="px-2 py-1 bg-surface-container-high rounded text-label-sm">Pemeliharaan Rutin</span>
</td>
</tr>
<tr class="bg-surface-container-lowest/50 hover:bg-surface-container-lowest transition-colors cursor-pointer group hover:shadow-[0_0_15px_rgba(0,0,0,0.1)] hover:z-10 relative">
<td class="px-6 py-4 font-label-md font-bold">2027</td>
<td class="px-6 py-4 font-body-md">14,200</td>
<td class="px-6 py-4 font-body-md text-error">+4.8%</td>
<td class="px-6 py-4 font-body-md">3.4%</td>
<td class="px-6 py-4">
<span class="px-2 py-1 bg-secondary-container text-on-secondary-container rounded text-label-sm">Audit Limbah</span>
</td>
</tr>
<tr class="hover:bg-surface-container-lowest transition-colors cursor-pointer group hover:shadow-[0_0_15px_rgba(0,0,0,0.1)] hover:z-10 relative">
<td class="px-6 py-4 font-label-md font-bold">2028</td>
<td class="px-6 py-4 font-body-md">15,050</td>
<td class="px-6 py-4 font-body-md text-error">+6.0%</td>
<td class="px-6 py-4 font-body-md">4.2%</td>
<td class="px-6 py-4">
<span class="px-2 py-1 bg-tertiary text-on-tertiary rounded text-label-sm">Upgrade Sistem</span>
</td>
</tr>
</tbody>
</table>
</div>
<div class="px-6 py-4 bg-surface-container-low border-t border-outline-variant">
<p class="font-label-sm text-on-surface-variant italic">*Data berdasarkan model peramalan prediktif versi 2.4. Hasil aktual dapat bervariasi tergantung pada regulasi industri baru.</p>
</div>
</div>
<!-- Insight Cards (Bottom Row) -->
<div class="col-span-12 lg:col-span-6 bg-surface-container-highest rounded-xl p-6 flex items-start gap-4">
<div class="w-12 h-12 rounded-lg bg-tertiary-container text-on-tertiary-container flex items-center justify-center shrink-0">
<span class="material-symbols-outlined">psychology</span>
</div>
<div>
<h4 class="font-label-md font-bold mb-1 text-on-surface">Interpretasi Model</h4>
<p class="font-body-md text-on-surface-variant">Peningkatan volume di tahun 2025 dipicu oleh rencana pembukaan Unit Produksi C. Model merekomendasikan penambahan 2 unit armada pengangkut sebelum Q3 2025.</p>
</div>
</div>
<div class="col-span-12 lg:col-span-6 bg-primary-container/10 rounded-xl p-6 border border-primary/20 flex items-start gap-4">
<div class="w-12 h-12 rounded-lg bg-primary text-on-primary flex items-center justify-center shrink-0">
<span class="material-symbols-outlined">verified</span>
</div>
<div>
<h4 class="font-label-md font-bold mb-1 text-on-surface">Validitas Data</h4>
<p class="font-body-md text-on-surface-variant">98.4% data historis telah divalidasi melalui sensor IoT lapangan. Ini memberikan dasar yang sangat kuat untuk akurasi prediksi jangka pendek (12-24 bulan ke depan).</p>
</div>
</div>
</div>
</div>
<!-- Footer -->
<footer class="w-full py-8 mt-auto bg-surface-dim border-t border-outline-variant">
<div class="flex flex-col md:flex-row justify-between items-center px-margin-desktop w-full max-w-container-max mx-auto gap-4">
<div class="flex flex-col items-center md:items-start gap-1">
<span class="font-label-md text-label-md font-bold text-primary">WastePredict AI</span>
<p class="font-label-sm text-label-sm text-on-surface-variant opacity-70">© 2024 WastePredict AI. Environmental Stewardship through Precision.</p>
</div>
<div class="flex gap-6">
<a class="font-label-sm text-on-secondary-container hover:text-primary transition-colors" href="#">Privacy Policy</a>
<a class="font-label-sm text-on-secondary-container hover:text-primary transition-colors" href="#">Terms of Service</a>
<a class="font-label-sm text-on-secondary-container hover:text-primary transition-colors flex items-center gap-1" href="#">
<span class="w-2 h-2 rounded-full bg-emerald-500"></span> API Status
                    </a>
</div>
</div>
</footer>
</main>
<script>
        // Micro-interactions for the table rows
        document.querySelectorAll('tbody tr').forEach(row => {
            row.addEventListener('click', () => {
                row.classList.add('scale-[0.99]');
                setTimeout(() => row.classList.remove('scale-[0.99]'), 100);
            });
        });
    </script>
</body></html>
