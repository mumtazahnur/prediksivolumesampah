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
        <!-- Active Tab: Datasets -->
        <a class="flex items-center gap-3 px-4 py-3 bg-secondary-container text-on-secondary-container rounded-lg font-medium" href="datasets.php">
            <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">database</span>
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
<!-- Header Section -->
<div class="mb-8 max-w-3xl">
    <nav class="flex items-center gap-2 text-label-sm text-outline mb-4">
        <span>Data Repository</span>
        <span class="material-symbols-outlined text-[14px]">chevron_right</span>
        <span class="text-primary">Historical Waste Records</span>
    </nav>
    <h2 class="font-display-lg text-display-lg text-primary mb-4">Dataset Transparency</h2>
    <p class="font-body-lg text-body-lg text-on-surface-variant leading-relaxed">
        Kami menjunjung tinggi prinsip keterbukaan data. Tabel di bawah ini menyajikan volume sampah historis Kota Surakarta selama 10 tahun yang dikumpulkan dari berbagai sektor. Data tersedia untuk audit, riset keberlanjutan, dan perencanaan pengelolaan limbah.
    </p>
</div>
<!-- Dashboard Stats Row -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-gutter mb-8">
    <div class="bg-surface-container-lowest p-6 rounded-xl shadow-[0_4px_12px_rgba(0,0,0,0.05)] border border-surface-variant">
        <p class="text-label-sm text-outline uppercase mb-1">Total Records</p>
        <h3 class="text-headline-md font-bold text-primary"><?php echo number_format($total_records, 0, ',', '.'); ?></h3>
        <p class="text-sm text-on-surface-variant mt-1">Entri data historis bulanan</p>
    </div>
    <div class="bg-surface-container-lowest p-6 rounded-xl shadow-[0_4px_12px_rgba(0,0,0,0.05)] border border-surface-variant">
        <p class="text-label-sm text-outline uppercase mb-1">Time Range</p>
        <h3 class="text-headline-md font-bold text-primary"><?php echo htmlspecialchars($first_year . ' – ' . $last_year); ?></h3>
        <p class="text-sm text-on-surface-variant mt-1">Rentang tahun dataset aktual</p>
    </div>
    <div class="bg-surface-container-lowest p-6 rounded-xl shadow-[0_4px_12px_rgba(0,0,0,0.05)] border border-surface-variant flex items-center justify-center">
        <a href="export_csv.php" class="flex items-center gap-2 text-primary border border-primary px-4 py-2 rounded-lg hover:bg-secondary-container transition-colors font-label-md">
            <span class="material-symbols-outlined">download</span>
            Download CSV
        </a>
    </div>
</div>
<!-- Table Container -->
<div class="bg-surface-container-lowest rounded-xl shadow-[0_4px_20px_rgba(0,0,0,0.05)] border border-surface-variant overflow-hidden flex flex-col">
    <!-- Table Controls -->
    <div class="p-6 border-b border-surface-variant flex flex-col sm:flex-row justify-between gap-4 items-center">
        <span class="text-label-sm text-on-surface-variant">
            Menampilkan <?php echo $offset + 1; ?>–<?php echo min($offset + $limit, $total_records); ?> dari <?php echo $total_records; ?> entri
        </span>
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
</div>
<!-- Footer -->
<footer class="w-full py-6 mt-auto bg-surface-dim border-t border-outline-variant">
    <div class="px-margin-desktop w-full max-w-container-max mx-auto">
        <p class="font-label-sm text-on-surface-variant text-center">&copy; <?php echo date('Y'); ?> WastePredict AI &mdash; Data Historis Kota Surakarta. Environmental Stewardship through Precision.</p>
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
document.querySelectorAll('tr').forEach(row => {
    row.addEventListener('mouseenter', () => { row.style.transform = 'translateY(-1px)'; });
    row.addEventListener('mouseleave', () => { row.style.transform = 'none'; });
});
</script>
</body></html>
