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

<!-- Sidebar Navigation -->
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
        <a class="flex items-center gap-3 px-4 py-3 text-on-surface-variant hover:bg-surface-container-high hover:translate-x-1 rounded-lg transition-all duration-200 active:scale-95" href="predictionPage.php">
            <span class="material-symbols-outlined">query_stats</span>
            <span class="font-label-md">Predictions</span>
        </a>
        <!-- Active Tab: Input Tool -->
        <a class="flex items-center gap-3 px-4 py-3 bg-secondary-container text-on-secondary-container rounded-lg font-medium" href="inputPage.php">
            <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">edit_note</span>
            <span class="font-label-md">Input Tool</span>
        </a>
    </nav>
</aside>
<!-- Main Content Area -->
<main class="md:ml-[280px] min-h-screen flex flex-col">
<!-- Canvas -->
<div class="flex-1 p-8 pt-20 md:pt-8 max-w-6xl mx-auto w-full">
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
    <button id="export-pdf-btn" onclick="exportPDF()" class="flex-grow border border-primary text-primary py-3 rounded-lg font-label-md hover:bg-secondary-container transition-colors flex items-center justify-center gap-2 opacity-50 cursor-not-allowed" disabled title="Jalankan simulasi terlebih dahulu">
        <span class="material-symbols-outlined text-[18px]">picture_as_pdf</span> Export PDF
    </button>
</div>
</div>
</div>
</div>
</div>
</div>
<!-- Footer -->
<footer class="bg-surface-dim border-t border-outline-variant mt-auto">
    <div class="px-margin-desktop py-6 max-w-container-max mx-auto w-full">
        <p class="font-label-sm text-on-surface-variant text-center">&copy; <?php echo date('Y'); ?> WastePredict AI &mdash; Predictive Engine powered by Random Forest. Environmental Stewardship through Precision.</p>
    </div>
</footer>
</main>

<script>
// ====== DATA STORE ======
// Menyimpan data JSON terakhir dari model AI untuk digunakan saat export PDF
let predictionData = null;

// ====== FORM SUBMISSION ======
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
                // Simpan data prediksi ke variable global untuk digunakan saat export PDF
                predictionData = data;

                // Aktifkan tombol Export PDF setelah hasil tersedia
                const pdfBtn = document.getElementById('export-pdf-btn');
                pdfBtn.disabled = false;
                pdfBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                pdfBtn.title = 'Download laporan PDF prediksi';

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

// ====== EXPORT PDF ======
// Mengirim data prediksi ke generate_pdf.php dan membuka laporan di tab baru
function exportPDF() {
    if (!predictionData) {
        alert('Jalankan simulasi terlebih dahulu sebelum export PDF.');
        return;
    }
    // Kirim JSON via fetch POST ke generate_pdf.php
    fetch('generate_pdf.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(predictionData)
    })
    .then(response => response.text())
    .then(html => {
        // Buka hasil HTML laporan di tab baru
        const tab = window.open('', '_blank');
        tab.document.write(html);
        tab.document.close();
    })
    .catch(err => {
        alert('Gagal membuka laporan PDF: ' + err.message);
    });
}

// ====== HAMBURGER SIDEBAR ======
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