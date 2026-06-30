<?php
// Hitung metrik aktual dari dataset historis
$dataset = include 'data.php';
$data_2024 = array_filter($dataset, fn($r) => $r['tahun'] == 2024);
$data_2025 = array_filter($dataset, fn($r) => $r['tahun'] == 2025);
$total_2024 = array_sum(array_column($data_2024, 'volume'));
$total_2025 = array_sum(array_column($data_2025, 'volume'));
$avg_monthly_2025 = count($data_2025) > 0 ? $total_2025 / count($data_2025) : 0;
$growth_pct = $total_2024 > 0 ? (($total_2025 - $total_2024) / $total_2024) * 100 : 0;
$growth_sign = $growth_pct >= 0 ? '+' : '';
?>
<!DOCTYPE html><html class="light" lang="en"><head>
<meta charset="utf-8">
<meta content="width=device-width, initial-scale=1.0" name="viewport">
<title>Overview | WastePredict AI</title>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
<!-- Tailwind Config -->
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
        body { font-family: 'Inter', sans-serif; }
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }
        .glass-header {
            backdrop-filter: blur(12px);
            background-color: rgba(248, 249, 250, 0.8);
        }
    </style>
</head>
<body class="bg-background text-on-surface">

<!-- Overlay mobile sidebar -->
<div id="sidebar-overlay" class="fixed inset-0 bg-black/50 z-40 hidden md:hidden transition-opacity duration-300" onclick="closeSidebar()"></div>

<!-- Tombol hamburger (hanya mobile) -->
<button id="hamburger-btn" onclick="openSidebar()" class="md:hidden fixed top-4 left-4 z-50 bg-surface-container-high p-2.5 rounded-xl shadow-md hover:bg-surface-container border border-outline-variant transition-all duration-200 text-on-surface">
    <span class="material-symbols-outlined">menu</span>
</button>

<!-- Sidebar -->
<aside id="sidebar" class="fixed left-0 top-0 h-full w-[280px] bg-surface-container flex flex-col p-4 gap-2 z-50 -translate-x-full md:translate-x-0 transition-transform duration-300 ease-out">
    <!-- Tombol tutup (hanya mobile) -->
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
        <!-- Active Tab: Overview -->
        <a class="flex items-center gap-3 px-4 py-3 bg-secondary-container text-on-secondary-container rounded-lg font-medium" href="index.php">
            <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">dashboard</span>
            <span class="font-label-md">Overview</span>
        </a>
        <a class="flex items-center gap-3 px-4 py-3 text-on-surface-variant hover:bg-surface-container-high hover:translate-x-1 rounded-lg transition-all duration-200 active:scale-95" href="datasets.php">
            <span class="material-symbols-outlined">database</span>
            <span class="font-label-md">Datasets</span>
        </a>
        <a class="flex items-center gap-3 px-4 py-3 text-on-surface-variant hover:bg-surface-container-high hover:translate-x-1 rounded-lg transition-all duration-200 active:scale-95" href="predictionPage.php">
            <span class="material-symbols-outlined">query_stats</span>
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
<!-- Page Content -->
<div class="flex-grow p-margin-desktop pt-20 md:pt-margin-desktop max-w-container-max w-full mx-auto">
<div class="mb-8 max-w-3xl">
    <nav class="flex items-center gap-2 text-label-sm text-outline mb-4">
        <span>Dashboard</span>
        <span class="material-symbols-outlined text-[14px]">chevron_right</span>
        <span class="text-primary">Overview</span>
    </nav>
    <h2 class="font-display-lg text-display-lg text-primary mb-4">Dashboard Overview</h2>
    <p class="font-body-lg text-body-lg text-on-surface-variant leading-relaxed">
        Selamat datang di WastePredict AI. Pantau volume sampah historis Kota Surakarta, analisis tren, dan gunakan engine prediksi berbasis Random Forest untuk proyeksi ke depan.
    </p>
</div>

<!-- Key Metrics dari Data Aktual -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-gutter mb-8">
    <!-- Total Volume Sampah 2025 -->
    <div class="bg-surface-container-lowest p-6 rounded-xl shadow-[0_4px_12px_rgba(0,0,0,0.05)] border border-surface-variant border-l-4 border-l-primary">
        <div class="flex justify-between items-start mb-4">
            <div class="bg-primary-container/20 p-3 rounded-lg text-primary">
                <span class="material-symbols-outlined">monitoring</span>
            </div>
            <span class="flex items-center text-sm font-medium <?= $growth_pct >= 0 ? 'text-error bg-error-container/20' : 'text-primary bg-primary-container/20' ?> px-2 py-1 rounded-full">
                <span class="material-symbols-outlined text-[16px] mr-1"><?= $growth_pct >= 0 ? 'arrow_upward' : 'arrow_downward' ?></span>
                <?= $growth_sign . number_format(abs($growth_pct), 1) ?>%
            </span>
        </div>
        <p class="text-label-sm text-outline uppercase mb-1">Total Sampah 2025 (Aktual)</p>
        <h3 class="text-headline-lg font-bold text-on-surface"><?= number_format($total_2025 / 1000, 1, ',', '.') ?><span class="text-body-md text-outline ml-1">Ribu Ton</span></h3>
        <p class="text-sm text-on-surface-variant mt-2">Pertumbuhan vs tahun 2024</p>
    </div>

    <!-- Rata-rata Bulanan 2025 -->
    <div class="bg-surface-container-lowest p-6 rounded-xl shadow-[0_4px_12px_rgba(0,0,0,0.05)] border border-surface-variant border-l-4 border-l-secondary">
        <div class="flex justify-between items-start mb-4">
            <div class="bg-secondary-container/50 p-3 rounded-lg text-secondary">
                <span class="material-symbols-outlined">calendar_month</span>
            </div>
            <span class="px-2 py-1 bg-secondary-container text-on-secondary-container rounded-full text-xs font-semibold">2025</span>
        </div>
        <p class="text-label-sm text-outline uppercase mb-1">Rata-rata Bulanan 2025</p>
        <h3 class="text-headline-lg font-bold text-on-surface"><?= number_format($avg_monthly_2025, 0, ',', '.') ?><span class="text-body-md text-outline ml-1">Ton</span></h3>
        <p class="text-sm text-on-surface-variant mt-2">Per bulan, data historis aktual</p>
    </div>

    <!-- Jumlah Record Dataset -->
    <div class="bg-surface-container-lowest p-6 rounded-xl shadow-[0_4px_12px_rgba(0,0,0,0.05)] border border-surface-variant border-l-4 border-l-tertiary">
        <div class="flex justify-between items-start mb-4">
            <div class="bg-tertiary-container/30 p-3 rounded-lg text-tertiary">
                <span class="material-symbols-outlined">database</span>
            </div>
            <span class="px-2 py-1 bg-tertiary-container/30 text-tertiary rounded-full text-xs font-semibold">2016–2025</span>
        </div>
        <p class="text-label-sm text-outline uppercase mb-1">Total Data Historis</p>
        <h3 class="text-headline-lg font-bold text-on-surface"><?= count($dataset) ?><span class="text-body-md text-outline ml-1">Records</span></h3>
        <p class="text-sm text-on-surface-variant mt-2">Data bulanan Kota Surakarta</p>
    </div>
</div>

<div class="bg-surface-container-lowest rounded-xl shadow-[0_4px_20px_rgba(0,0,0,0.05)] border border-surface-variant p-8 flex flex-col items-center justify-center min-h-[300px]">
    <span class="material-symbols-outlined text-[64px] text-outline mb-4">insights</span>
    <h3 class="text-headline-md font-bold text-on-surface mb-2">Predictive Models Active</h3>
    <p class="text-body-md text-on-surface-variant text-center max-w-lg mb-6">Your data pipelines are flowing perfectly. Our AI is currently analyzing production volumes to refine the next quarter's forecast.</p>
    <a href="predictionPage.php" class="bg-primary text-on-primary px-6 py-3 rounded-lg font-label-md hover:opacity-90 transition-opacity">View Forecasts</a>
</div>

</div>
<!-- Footer -->
<footer class="w-full py-6 mt-auto bg-surface-dim border-t border-outline-variant">
    <div class="px-margin-desktop w-full max-w-container-max mx-auto">
        <p class="font-label-sm text-on-surface-variant text-center">© <?= date('Y') ?> WastePredict AI — Data Historis Kota Surakarta. Environmental Stewardship through Precision.</p>
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
</script>
</body></html>
