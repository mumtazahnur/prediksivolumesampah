<?php
// Mengimpor dataset dari data.php (array berisi 120 entri data historis)
$dataset = include 'data.php';

// Mendapatkan nomor halaman aktif dari URL query string (?page=X), default-nya halaman 1
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;

// Membatasi tampilan data maksimal 10 record per halaman
$limit = 10;

// Menghitung jumlah total data dan total halaman yang dibutuhkan
$total_records = count($dataset);
$total_pages = ceil($total_records / $limit);

// Memastikan nomor halaman berada dalam batas yang valid (antara 1 s/d total_pages)
if ($page > $total_pages) $page = $total_pages;
if ($page < 1) $page = 1;

// Menghitung offset (indeks awal potong data) untuk slicing array data
$offset = ($page - 1) * $limit;

// Memotong array dataset sesuai dengan halaman saat ini untuk ditampilkan ke tabel
$page_data = array_slice($dataset, $offset, $limit);

// Mengambil tahun awal dan akhir dari dataset untuk keperluan label statistik di UI
$first_year = isset($dataset[0]['tahun']) ? $dataset[0]['tahun'] : 2016;
$last_year = isset($dataset[count($dataset)-1]['tahun']) ? $dataset[count($dataset)-1]['tahun'] : 2025;
?>
<!DOCTYPE html><html class="light" lang="en"><head>
<meta charset="utf-8">
<meta content="width=device-width, initial-scale=1.0" name="viewport">
<title>Dataset Transparency | WastePredict AI</title>
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
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #bfc9c3;
            border-radius: 10px;
        }
    </style>
</head>
<body class="bg-background text-on-surface">
<!-- SideNavBar -->
<aside class="fixed left-0 top-0 h-full w-[280px] bg-surface-container hidden md:flex flex-col p-4 gap-2 z-50">
<div class="flex items-center gap-3 px-4 py-6 mb-4">
<div class="w-10 h-10 rounded-lg bg-primary flex items-center justify-center text-on-primary">
<span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">recycling</span>
</div>
<div>
<h1 class="font-headline-sm text-[20px] font-black text-primary leading-tight">WastePredict</h1>
<p class="font-label-sm text-on-surface-variant opacity-70">Industrial Intelligence</p>
</div>
</div>
<nav class="flex-grow flex flex-col gap-1">
<a class="flex items-center gap-3 px-4 py-3 text-on-surface-variant hover:bg-surface-container-high rounded-lg transition-all" href="index.php">
<span class="material-symbols-outlined">dashboard</span>
<span class="font-label-md">Overview</span>
</a>
<!-- Active Tab: Datasets -->
<a class="flex items-center gap-3 px-4 py-3 bg-secondary-container text-on-secondary-container rounded-lg font-medium" href="datasets.php">
<span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">database</span>
<span class="font-label-md">Datasets</span>
</a>
<a class="flex items-center gap-3 px-4 py-3 text-on-surface-variant hover:bg-surface-container-high rounded-lg transition-all" href="predictionPage.php">
<span class="material-symbols-outlined">query_stats</span>
<span class="font-label-md">Predictions</span>
</a>
<a class="flex items-center gap-3 px-4 py-3 text-on-surface-variant hover:bg-surface-container-high rounded-lg transition-all" href="inputPage.php">
<span class="material-symbols-outlined">edit_note</span>
<span class="font-label-md">Input Tool</span>
</a>
</nav>
<div class="mt-auto flex flex-col gap-1 border-t border-outline-variant pt-4">
<button class="w-full bg-primary text-on-primary font-label-md py-3 rounded-lg mb-4 hover:opacity-90 transition-opacity">
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
<header class="sticky top-0 z-40 glass-header shadow-sm px-margin-desktop py-4 flex justify-between items-center w-full max-w-container-max mx-auto">
<div class="flex items-center gap-6">
<!-- Mobile Menu Trigger -->
<button class="md:hidden text-on-surface">
<span class="material-symbols-outlined">menu</span>
</button>
<div class="hidden md:flex gap-6 items-center">
<a class="font-label-md text-on-surface-variant hover:text-primary transition-colors" href="index.php">Dashboard</a>
<a class="font-label-md text-primary border-b-2 border-primary pb-1" href="datasets.php">Datasets</a>
<a class="font-label-md text-on-surface-variant hover:text-primary transition-colors" href="predictionPage.php">Predictions</a>
<a class="font-label-md text-on-surface-variant hover:text-primary transition-colors" href="inputPage.php">Input Tool</a>
</div>
</div>
<div class="flex items-center gap-4">
<div class="relative hidden sm:block">
<span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline text-[20px]">search</span>
<input class="pl-10 pr-4 py-2 bg-surface-container-low border border-outline-variant rounded-full text-label-md focus:outline-none focus:border-primary w-64" placeholder="Search data..." type="text">
</div>
<button class="p-2 text-on-surface-variant hover:bg-surface-container-high rounded-full">
<span class="material-symbols-outlined">notifications</span>
</button>
<button class="p-2 text-on-surface-variant hover:bg-surface-container-high rounded-full">
<span class="material-symbols-outlined">settings</span>
</button>
<div class="h-8 w-8 rounded-full bg-surface-variant overflow-hidden border border-outline-variant">
<img class="w-full h-full object-cover" data-alt="Profile Picture" src="https://lh3.googleusercontent.com/aida-public/AB6AXuASrfSVD_ibh7-GNPyneQYpDDUlBg6ygZjlIXdk6ed0x3q9zz_D8jswqlTTpvbbJDv9Sa5J_0SmoV_Gsgchv486_FKB7Yy1-6-ckGJC9Rg7PkGDo2cJdkeEH0ep7IY4-blYspYWyDpvZGtxAWmQr_op4Aj4ghTTFqAAq1haKpQGi0--txcfOwUzDE6QbdoDha3u_vekzkJWmWOtMmEJeGQhdJUendlq3s2y26J3TW8aY-mXUg7uRXaDOgw-qcjbKF_ZLl171QYpO-o">
</div>
<button class="hidden lg:block bg-primary text-on-primary px-4 py-2 rounded-lg font-label-sm hover:opacity-90 transition-opacity">
                    New Report
                </button>
</div>
</header>
<!-- Page Content -->
<div class="flex-grow p-margin-desktop max-w-container-max w-full mx-auto">
<!-- Header Section -->
<div class="mb-8 max-w-3xl">
<nav class="flex items-center gap-2 text-label-sm text-outline mb-4">
<span>Data Repository</span>
<span class="material-symbols-outlined text-[14px]">chevron_right</span>
<span class="text-primary">Historical Waste Records</span>
</nav>
<h2 class="font-display-lg text-display-lg text-primary mb-4">Dataset Transparency</h2>
<p class="font-body-lg text-body-lg text-on-surface-variant leading-relaxed">
                    Kami menjunjung tinggi prinsip keterbukaan data. Tabel di bawah ini menyajikan volume sampah historis selama 10 tahun terakhir yang dikumpulkan dari berbagai sektor industri. Data ini tersedia bagi publik untuk audit, riset keberlanjutan, dan peningkatan efisiensi pengelolaan limbah global.
                </p>
</div>
<!-- Dashboard Stats Row (Subtle Bento) -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-gutter mb-8">
<div class="bg-surface-container-lowest p-6 rounded-xl shadow-[0_4px_12px_rgba(0,0,0,0.05)] border border-surface-variant">
<p class="text-label-sm text-outline uppercase mb-1">Total Records</p>
<h3 class="text-headline-md font-bold text-primary"><?php echo number_format($total_records, 0, ',', '.'); ?></h3>
</div>
<div class="bg-surface-container-lowest p-6 rounded-xl shadow-[0_4px_12px_rgba(0,0,0,0.05)] border border-surface-variant">
<p class="text-label-sm text-outline uppercase mb-1">Time Range</p>
<h3 class="text-headline-md font-bold text-primary"><?php echo htmlspecialchars($first_year . ' - ' . $last_year); ?></h3>
</div>
<div class="bg-surface-container-lowest p-6 rounded-xl shadow-[0_4px_12px_rgba(0,0,0,0.05)] border border-surface-variant">
<p class="text-label-sm text-outline uppercase mb-1">Data Integrity</p>
<h3 class="text-headline-md font-bold text-primary">99.8%</h3>
</div>
<div class="bg-surface-container-lowest p-6 rounded-xl shadow-[0_4px_12px_rgba(0,0,0,0.05)] border border-surface-variant flex items-center justify-center">
<a href="export_csv.php" class="flex items-center gap-2 text-primary border border-primary px-4 py-2 rounded-lg hover:bg-secondary-container transition-colors font-label-md">
<span class="material-symbols-outlined">download</span>
                        Export CSV
                    </a>
</div>
</div>
<!-- Table Container -->
<div class="bg-surface-container-lowest rounded-xl shadow-[0_4px_20px_rgba(0,0,0,0.05)] border border-surface-variant overflow-hidden flex flex-col">
<!-- Table Controls -->
<div class="p-6 border-b border-surface-variant flex flex-col sm:flex-row justify-between gap-4 items-center">
<div class="flex items-center gap-4 w-full sm:w-auto">
<div class="relative w-full sm:w-80">
<span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline text-[20px]">filter_list</span>
<input class="w-full pl-10 pr-4 py-2.5 bg-background border border-outline-variant rounded-lg text-label-md focus:outline-none focus:ring-2 focus:ring-primary/20" placeholder="Filter by Category or Year..." type="text">
</div>
<select class="bg-background border border-outline-variant rounded-lg px-4 py-2.5 text-label-md focus:outline-none focus:ring-2 focus:ring-primary/20 cursor-pointer">
<option>All Sectors</option>
<option>Manufacturing</option>
<option>Residential</option>
<option>Healthcare</option>
</select>
</div>
<div class="flex items-center gap-2">
<span class="text-label-sm text-on-surface-variant">Showing <?php echo $offset + 1; ?>-<?php echo min($offset + $limit, $total_records); ?> of <?php echo $total_records; ?> entries</span>
</div>
</div>
<!-- Main Table -->
<div class="overflow-x-auto custom-scrollbar">
<table class="w-full text-left border-collapse">
<thead>
<tr class="bg-surface-container-low">
<th class="px-6 py-4 font-label-sm text-outline uppercase tracking-wider border-b border-surface-variant">Periode</th>
<th class="px-6 py-4 font-label-sm text-outline uppercase tracking-wider border-b border-surface-variant">Volume Sampah (Tons)</th>
<th class="px-6 py-4 font-label-sm text-outline uppercase tracking-wider border-b border-surface-variant">Jumlah Penduduk (Jiwa)</th>
<th class="px-6 py-4 font-label-sm text-outline uppercase tracking-wider border-b border-surface-variant">Kepadatan Penduduk (Jiwa/km²)</th>
<th class="px-6 py-4 font-label-sm text-outline uppercase tracking-wider border-b border-surface-variant">Wisatawan (Orang)</th>
<th class="px-6 py-4 font-label-sm text-outline uppercase tracking-wider border-b border-surface-variant">Curah Hujan (mm)</th>
</tr>
</thead>
<tbody class="divide-y divide-surface-variant">
<?php foreach ($page_data as $row): ?>
<tr class="hover:bg-surface-container-low/50 transition-colors">
<td class="px-6 py-4 font-label-md text-primary font-bold"><?php echo htmlspecialchars($row['bulan_nama'] . ' ' . $row['tahun']); ?></td>
<td class="px-6 py-4 font-body-md"><?php echo number_format($row['volume'], 2, ',', '.'); ?></td>
<td class="px-6 py-4 font-body-md"><?php echo number_format($row['penduduk'], 0, ',', '.'); ?></td>
<td class="px-6 py-4 font-body-md"><?php echo number_format($row['kepadatan'], 2, ',', '.'); ?></td>
<td class="px-6 py-4 font-body-md"><?php echo number_format($row['wisatawan'], 0, ',', '.'); ?></td>
<td class="px-6 py-4 font-body-md"><?php echo number_format($row['curah_hujan'], 1, ',', '.'); ?></td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
</div>
<!-- Pagination -->
<div class="p-6 border-t border-surface-variant flex items-center justify-between">
<a href="?page=<?php echo max(1, $page - 1); ?>" class="px-4 py-2 border border-outline-variant rounded-lg text-label-sm text-on-surface hover:bg-surface-container-high transition-colors <?php if ($page <= 1) echo 'opacity-50 pointer-events-none'; ?>">
    Previous
</a>
<div class="flex items-center gap-2">
    <?php
    $start_page = max(1, $page - 2);
    $end_page = min($total_pages, $page + 2);
    if ($start_page > 1) {
        echo '<a href="?page=1" class="w-8 h-8 rounded-lg hover:bg-surface-container-high text-label-sm flex items-center justify-center">1</a>';
        if ($start_page > 2) {
            echo '<span class="text-outline">...</span>';
        }
    }
    for ($i = $start_page; $i <= $end_page; $i++) {
        $active_class = ($i === $page) ? 'bg-primary text-on-primary' : 'hover:bg-surface-container-high';
        echo "<a href=\"?page={$i}\" class=\"w-8 h-8 rounded-lg text-label-sm flex items-center justify-center {$active_class}\">{$i}</a>";
    }
    if ($end_page < $total_pages) {
        if ($end_page < $total_pages - 1) {
            echo '<span class="text-outline">...</span>';
        }
        echo "<a href=\"?page={$total_pages}\" class=\"w-8 h-8 rounded-lg hover:bg-surface-container-high text-label-sm flex items-center justify-center\">{$total_pages}</a>";
    }
    ?>
</div>
<a href="?page=<?php echo min($total_pages, $page + 1); ?>" class="px-4 py-2 border border-outline-variant rounded-lg text-label-sm text-on-surface hover:bg-surface-container-high transition-colors <?php if ($page >= $total_pages) echo 'opacity-50 pointer-events-none'; ?>">
    Next
</a>
</div>
</div>
<!-- Transparency Disclaimer Card -->
<div class="mt-12 bg-primary-container text-on-primary-container p-8 rounded-xl flex flex-col md:flex-row gap-6 items-center">
<div class="bg-on-primary-container/20 p-4 rounded-full">
<span class="material-symbols-outlined text-[48px]">verified_user</span>
</div>
<div>
<h4 class="text-headline-md font-bold mb-2">Pernyataan Transparansi</h4>
<p class="font-body-md opacity-90 max-w-2xl">
                        Seluruh data yang ditampilkan telah melalui proses validasi oleh tim auditor WastePredict AI dan pihak ketiga independen. Kami berkomitmen untuk menyediakan akses data yang akurat guna mendorong inovasi dalam ekonomi sirkular. Data diperbarui setiap kuartal berjalan.
                    </p>
</div>
<div class="md:ml-auto">
<button class="whitespace-nowrap bg-on-primary-container text-primary-container px-6 py-3 rounded-lg font-bold hover:bg-white transition-colors">
                        Baca Kebijakan Data
                    </button>
</div>
</div>
</div>
<!-- Footer -->
<footer class="w-full py-8 mt-auto bg-surface-dim border-t border-outline-variant">
<div class="flex flex-col md:flex-row justify-between items-center px-margin-desktop w-full max-w-container-max mx-auto gap-4">
<div class="flex items-center gap-2">
<span class="font-label-md font-bold text-primary">WastePredict AI</span>
<span class="text-on-surface-variant opacity-20">|</span>
<p class="font-label-sm text-on-surface-variant">© 2024 WastePredict AI. Environmental Stewardship through Precision.</p>
</div>
<div class="flex gap-6">
<a class="font-label-sm text-on-secondary-container hover:text-primary transition-colors" href="#">Privacy Policy</a>
<a class="font-label-sm text-on-secondary-container hover:text-primary transition-colors" href="#">Terms of Service</a>
<a class="font-label-sm text-on-secondary-container hover:text-primary transition-colors" href="#">API Status</a>
</div>
</div>
</footer>
</main>
<!-- Micro-interaction Scripts -->
<script>
        // Simple hover effect for table rows
        document.querySelectorAll('tr').forEach(row => {
            row.addEventListener('mouseenter', () => {
                row.style.transform = 'translateY(-1px)';
                row.style.boxShadow = '0 4px 6px -1px rgba(0, 0, 0, 0.05)';
            });
            row.addEventListener('mouseleave', () => {
                row.style.transform = 'none';
                row.style.boxShadow = 'none';
            });
        });

        // Search highlight simulation
        const searchInput = document.querySelector('input[placeholder="Search data..."]');
        searchInput.addEventListener('input', (e) => {
            if(e.target.value.length > 2) {
                console.log('Filtering records for:', e.target.value);
                // Placeholder for real filtering logic
            }
        });
    </script>
</body></html>
