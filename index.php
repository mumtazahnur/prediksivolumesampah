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
<!-- Active Tab: Overview -->
<a class="flex items-center gap-3 px-4 py-3 bg-secondary-container text-on-secondary-container rounded-lg font-medium" href="index.php">
<span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">dashboard</span>
<span class="font-label-md">Overview</span>
</a>
<a class="flex items-center gap-3 px-4 py-3 text-on-surface-variant hover:bg-surface-container-high rounded-lg transition-all" href="datasets.php">
<span class="material-symbols-outlined">database</span>
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
<a class="font-label-md text-primary border-b-2 border-primary pb-1" href="index.php">Dashboard</a>
<a class="font-label-md text-on-surface-variant hover:text-primary transition-colors" href="datasets.php">Datasets</a>
<a class="font-label-md text-on-surface-variant hover:text-primary transition-colors" href="predictionPage.php">Predictions</a>
<a class="font-label-md text-on-surface-variant hover:text-primary transition-colors" href="inputPage.php">Input Tool</a>
</div>
</div>
<div class="flex items-center gap-4">
<div class="relative hidden sm:block">
<span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline text-[20px]">search</span>
<input class="pl-10 pr-4 py-2 bg-surface-container-low border border-outline-variant rounded-full text-label-md focus:outline-none focus:border-primary w-64" placeholder="Search insights..." type="text">
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
</div>
</header>
<!-- Page Content -->
<div class="flex-grow p-margin-desktop max-w-container-max w-full mx-auto">
<div class="mb-8 max-w-3xl">
<nav class="flex items-center gap-2 text-label-sm text-outline mb-4">
<span>Dashboard</span>
<span class="material-symbols-outlined text-[14px]">chevron_right</span>
<span class="text-primary">Overview</span>
</nav>
<h2 class="font-display-lg text-display-lg text-primary mb-4">Dashboard Overview</h2>
<p class="font-body-lg text-body-lg text-on-surface-variant leading-relaxed">
    Welcome back to WastePredict AI. Monitor real-time industrial waste metrics, track sustainability performance, and review AI-generated insights across your facilities.
</p>
</div>

<!-- Key Metrics -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-gutter mb-8">
<div class="bg-surface-container-lowest p-6 rounded-xl shadow-[0_4px_12px_rgba(0,0,0,0.05)] border border-surface-variant border-l-4 border-l-primary">
<div class="flex justify-between items-start mb-4">
<div class="bg-primary-container/20 p-3 rounded-lg text-primary">
<span class="material-symbols-outlined">monitoring</span>
</div>
<span class="flex items-center text-sm font-medium text-primary bg-primary-container/20 px-2 py-1 rounded-full"><span class="material-symbols-outlined text-[16px] mr-1">arrow_upward</span>12.5%</span>
</div>
<p class="text-label-sm text-outline uppercase mb-1">Total Waste Processed</p>
<h3 class="text-headline-lg font-bold text-on-surface">1,245.8<span class="text-body-md text-outline ml-1">Tons</span></h3>
<p class="text-sm text-on-surface-variant mt-2">This month vs last month</p>
</div>

<div class="bg-surface-container-lowest p-6 rounded-xl shadow-[0_4px_12px_rgba(0,0,0,0.05)] border border-surface-variant border-l-4 border-l-secondary">
<div class="flex justify-between items-start mb-4">
<div class="bg-secondary-container/50 p-3 rounded-lg text-secondary">
<span class="material-symbols-outlined">recycling</span>
</div>
<span class="flex items-center text-sm font-medium text-primary bg-primary-container/20 px-2 py-1 rounded-full"><span class="material-symbols-outlined text-[16px] mr-1">arrow_upward</span>4.2%</span>
</div>
<p class="text-label-sm text-outline uppercase mb-1">Recycling Rate</p>
<h3 class="text-headline-lg font-bold text-on-surface">68.5<span class="text-body-md text-outline ml-1">%</span></h3>
<p class="text-sm text-on-surface-variant mt-2">Overall efficiency target: 75%</p>
</div>

<div class="bg-surface-container-lowest p-6 rounded-xl shadow-[0_4px_12px_rgba(0,0,0,0.05)] border border-surface-variant border-l-4 border-l-error">
<div class="flex justify-between items-start mb-4">
<div class="bg-error-container/50 p-3 rounded-lg text-error">
<span class="material-symbols-outlined">warning</span>
</div>
<span class="flex items-center text-sm font-medium text-error bg-error-container/20 px-2 py-1 rounded-full"><span class="material-symbols-outlined text-[16px] mr-1">arrow_downward</span>1.1%</span>
</div>
<p class="text-label-sm text-outline uppercase mb-1">Hazardous Incidents</p>
<h3 class="text-headline-lg font-bold text-on-surface">3<span class="text-body-md text-outline ml-1">Events</span></h3>
<p class="text-sm text-on-surface-variant mt-2">Requires immediate attention</p>
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
<footer class="w-full py-8 mt-auto bg-surface-dim border-t border-outline-variant">
<div class="flex flex-col md:flex-row justify-between items-center px-margin-desktop w-full max-w-container-max mx-auto gap-4">
<div class="flex items-center gap-2">
<span class="font-label-md font-bold text-primary">WastePredict AI</span>
<span class="text-on-surface-variant opacity-20">|</span>
<p class="font-label-sm text-on-surface-variant">© 2024 WastePredict AI. Environmental Stewardship through Precision.</p>
</div>
</div>
</footer>
</main>
</body></html>
