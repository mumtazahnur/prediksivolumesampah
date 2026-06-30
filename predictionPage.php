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
        .glass-header {
            backdrop-filter: blur(12px);
            background-color: rgba(248, 249, 250, 0.8);
        }
    </style>
</head>
<body class="text-on-surface">

<!-- Overlay mobile sidebar -->
<div id="sidebar-overlay" class="fixed inset-0 bg-black/50 z-40 hidden md:hidden transition-opacity duration-300" onclick="closeSidebar()"></div>

<!-- Tombol hamburger (hanya mobile) -->
<button id="hamburger-btn" onclick="openSidebar()" class="md:hidden fixed top-4 left-4 z-50 bg-surface-container-high p-2.5 rounded-xl shadow-md hover:bg-surface-container border border-outline-variant transition-all duration-200 text-on-surface">
    <span class="material-symbols-outlined">menu</span>
</button>

<!-- Sidebar -->
<aside id="sidebar" class="fixed left-0 top-0 h-full w-[280px] bg-surface-container flex flex-col p-4 gap-2 z-50 -translate-x-full md:translate-x-0 transition-transform duration-300 ease-out">
    <button onclick="closeSidebar()" class="md:hidden self-end p-1.5 rounded-lg text-on-surface-variant hover:bg-surface-container-high transition-all duration-200 mb-1">
        <span class="material-symbols-outlined">close</span>
    </button>
    <div class="flex items-center gap-3 px-4 py-4 mb-4">
        <div class="w-10 h-10 rounded-lg bg-primary flex items-center justify-center text-on-primary">
            <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">recycling</span>
        </div>
        <div>
            <h1 class="font-headline-sm text-[20px] font-black text-primary leading-tight">WastePredict</h1>
            <p class="font-label-sm text-on-surface-variant opacity-70">Industrial Intelligence</p>
        </div>
    </div>
    <nav class="flex-grow flex flex-col gap-1">
        <a class="flex items-center gap-3 px-4 py-3 text-on-surface-variant hover:bg-surface-container-high hover:translate-x-1 rounded-lg transition-all duration-200 active:scale-95" href="index.php">
            <span class="material-symbols-outlined">dashboard</span>
            <span class="font-label-md">Overview</span>
        </a>
        <a class="flex items-center gap-3 px-4 py-3 text-on-surface-variant hover:bg-surface-container-high hover:translate-x-1 rounded-lg transition-all duration-200 active:scale-95" href="datasets.php">
            <span class="material-symbols-outlined">database</span>
            <span class="font-label-md">Datasets</span>
        </a>
        <!-- Active Tab: Predictions -->
        <a class="flex items-center gap-3 px-4 py-3 bg-secondary-container text-on-secondary-container rounded-lg font-medium" href="predictionPage.php">
            <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">query_stats</span>
            <span class="font-label-md">Predictions</span>
        </a>
        <a class="flex items-center gap-3 px-4 py-3 text-on-surface-variant hover:bg-surface-container-high hover:translate-x-1 rounded-lg transition-all duration-200 active:scale-95" href="inputPage.php">
            <span class="material-symbols-outlined">edit_note</span>
            <span class="font-label-md">Input Tool</span>
        </a>
    </nav>
</aside>
<!-- Main Content Wrapper -->
<main class="md:ml-[280px] min-h-screen flex flex-col">
<!-- Page Canvas -->
<div class="p-margin-desktop pt-20 md:pt-margin-desktop space-y-gutter max-w-container-max mx-auto w-full">
<!-- Header Section -->
<div>
    <h2 class="font-headline-lg text-headline-lg text-on-surface">Prediksi &amp; Metodologi</h2>
    <p class="font-body-md text-on-surface-variant max-w-2xl mt-2">Analisis mendalam mengenai proyeksi volume limbah industri berdasarkan tren historis dan pemodelan prediktif Random Forest untuk strategi pengelolaan yang lebih cerdas. Gunakan <a href="inputPage.php" class="text-primary underline hover:opacity-80">Input Tool</a> untuk menjalankan simulasi prediksi.</p>
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
        <div class="absolute top-0 right-0 opacity-10 pointer-events-none transform translate-x-1/4 -translate-y-1/4">
            <span class="material-symbols-outlined text-[200px]" style="font-variation-settings: 'FILL' 1;">insights</span>
        </div>
        <h3 class="font-headline-md text-headline-md mb-4 relative z-10">Metodologi Model AI</h3>
        <div class="space-y-4 relative z-10">
            <div>
                <h4 class="font-label-md font-bold text-primary-fixed mb-1">Random Forest Regression</h4>
                <p class="font-body-md opacity-90 leading-relaxed">Model utama yang digunakan adalah Random Forest dengan 200 pohon keputusan (<em>n_estimators=200</em>). Model dilatih secara kronologis menggunakan data 2017–2021 dan divalidasi pada data 2022–2025.</p>
            </div>
            <div class="h-px bg-on-primary opacity-20"></div>
            <div>
                <h4 class="font-label-md font-bold text-primary-fixed mb-1">Feature Engineering</h4>
                <p class="font-body-md opacity-90 leading-relaxed">11 fitur input digunakan: populasi, kepadatan, rumah tangga, rumah makan, tingkat hunian hotel, wisatawan, curah hujan, hari besar, siklus bulan (sin/cos), dan <em>lag-12</em> (volume tahun sebelumnya).</p>
            </div>
            <div class="h-px bg-on-primary opacity-20"></div>
            <div>
                <h4 class="font-label-md font-bold text-primary-fixed mb-1">Recursive Forecasting</h4>
                <p class="font-body-md opacity-90 leading-relaxed">Untuk memprediksi tahun di masa depan, model menggunakan teknik <em>recursive forecasting</em> — hasil prediksi tahun sebelumnya digunakan sebagai input lag-12 untuk tahun berikutnya.</p>
            </div>
        </div>
    </div>
</div>
<!-- Summary Table Card (Column 12) -->
<div class="col-span-12 bg-white rounded-xl shadow-sm border border-outline-variant/30 overflow-hidden">
    <div class="px-6 py-4 border-b border-outline-variant flex justify-between items-center">
        <h3 class="font-headline-md text-headline-md">Ilustrasi Tren Prediksi 5 Tahun</h3>
        <div class="flex gap-2">
            <span class="px-3 py-1 bg-secondary-container text-on-secondary-container rounded-full text-label-sm">Ilustrasi — gunakan Input Tool untuk data akurat</span>
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
<footer class="w-full py-6 mt-auto bg-surface-dim border-t border-outline-variant">
    <div class="px-margin-desktop w-full max-w-container-max mx-auto">
        <p class="font-label-sm text-on-surface-variant text-center">&copy; <?php echo date('Y'); ?> WastePredict AI &mdash; Model Random Forest Regression. Environmental Stewardship through Precision.</p>
    </div>
</footer>
</main>

<script>
function openSidebar() {
    document.getElementById('sidebar').classList.remove('-translate-x-full');
    document.getElementById('sidebar-overlay').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}
function closeSidebar() {
    document.getElementById('sidebar').classList.add('-translate-x-full');
    document.getElementById('sidebar-overlay').classList.add('hidden');
    document.body.style.overflow = '';
}
document.querySelectorAll('tbody tr').forEach(row => {
    row.addEventListener('click', () => {
        row.classList.add('scale-[0.99]');
        setTimeout(() => row.classList.remove('scale-[0.99]'), 100);
    });
});
</script>
</body></html>
