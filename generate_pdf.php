<?php
/**
 * generate_pdf.php
 * Endpoint untuk menghasilkan laporan PDF prediksi volume sampah.
 * Menerima data JSON hasil prediksi model AI dari inputPage.php via POST,
 * kemudian merender halaman HTML laporan yang siap dicetak sebagai PDF
 * menggunakan dialog print bawaan browser (tanpa library eksternal).
 */

// Ambil data JSON dari body POST request
$raw = file_get_contents('php://input');
$data = json_decode($raw, true);

// Validasi: data harus ada dan status harus success
if (!$data || ($data['status'] ?? '') !== 'success') {
    http_response_code(400);
    echo "Data prediksi tidak valid atau tidak ditemukan.";
    exit;
}

// Ekstrak variabel dari data JSON
$target_year     = $data['target_year'];
$total_annual    = number_format($data['total_annual'], 2, ',', '.');
$avg_monthly     = number_format($data['average_monthly'], 2, ',', '.');
$growth_rate     = $data['growth_rate_vs_2025'];
$total_2025      = number_format($data['total_2025_actual'], 2, ',', '.');
$predictions     = $data['predictions'];       // Array 12 bulan prediksi
$feature_imp     = $data['feature_importance']; // Array feature importance
$metrics         = $data['metrics'];            // Train/Test metrics
$generated_at    = date('d F Y, H:i') . ' WIB';

// Tentukan warna trend (merah = naik = lebih banyak sampah, hijau = turun)
$growth_color    = $growth_rate >= 0 ? '#ba1a1a' : '#003527';
$growth_symbol   = $growth_rate >= 0 ? '▲' : '▼';
$growth_text     = ($growth_rate >= 0 ? '+' : '') . number_format($growth_rate, 2, ',', '.') . '%';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Prediksi Volume Sampah <?= $target_year ?> — WastePredict AI</title>
    <style>
        /* ===== GLOBAL ===== */
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            font-size: 11pt;
            color: #191c1d;
            background: #fff;
            line-height: 1.5;
        }

        /* ===== PRINT SETTINGS ===== */
        @page {
            size: A4;
            margin: 15mm 15mm 15mm 15mm;
        }
        @media print {
            .no-print { display: none !important; }
            .page-break { page-break-before: always; }
            body { font-size: 10pt; }
        }

        /* ===== TOMBOL AKSI (tidak dicetak) ===== */
        .no-print {
            position: fixed;
            top: 0; left: 0; right: 0;
            background: #003527;
            color: white;
            padding: 12px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            z-index: 1000;
            box-shadow: 0 2px 8px rgba(0,0,0,0.3);
        }
        .no-print .info { font-size: 13px; opacity: 0.8; }
        .no-print .actions { display: flex; gap: 12px; }
        .btn-print {
            background: #95d3ba;
            color: #003527;
            border: none;
            padding: 8px 20px;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
        }
        .btn-close {
            background: rgba(255,255,255,0.15);
            color: white;
            border: 1px solid rgba(255,255,255,0.3);
            padding: 8px 20px;
            border-radius: 6px;
            font-size: 13px;
            cursor: pointer;
        }

        /* ===== LAPORAN ===== */
        .report-wrapper {
            max-width: 800px;
            margin: 70px auto 40px;
            padding: 0 20px;
        }
        @media print {
            .report-wrapper { margin: 0; padding: 0; max-width: 100%; }
        }

        /* ===== HEADER LAPORAN ===== */
        .report-header {
            border-bottom: 3px solid #003527;
            padding-bottom: 16px;
            margin-bottom: 24px;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }
        .report-header .brand {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .brand-icon {
            width: 40px; height: 40px;
            background: #003527;
            border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            color: white;
            font-size: 22px;
        }
        .brand-name { font-size: 18px; font-weight: 900; color: #003527; }
        .brand-sub  { font-size: 10px; color: #707974; text-transform: uppercase; letter-spacing: 0.05em; }
        .report-header .meta { text-align: right; font-size: 10px; color: #707974; }
        .report-header .meta .doc-title { font-size: 13px; font-weight: 700; color: #191c1d; margin-bottom: 4px; }

        /* ===== SECTION TITLE ===== */
        .section-title {
            font-size: 13px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #003527;
            border-left: 4px solid #003527;
            padding-left: 10px;
            margin: 24px 0 12px;
        }

        /* ===== SUMMARY CARDS ===== */
        .summary-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 12px;
            margin-bottom: 8px;
        }
        .summary-card {
            background: #f3f4f5;
            border-radius: 8px;
            padding: 14px 16px;
            border: 1px solid #e7e8e9;
        }
        .summary-card .label {
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: 0.07em;
            color: #707974;
            margin-bottom: 4px;
        }
        .summary-card .value {
            font-size: 20px;
            font-weight: 800;
            color: #003527;
            line-height: 1.1;
        }
        .summary-card .unit {
            font-size: 11px;
            font-weight: 400;
            color: #707974;
            margin-left: 2px;
        }
        .summary-card .sub {
            font-size: 10px;
            color: #707974;
            margin-top: 2px;
        }

        /* ===== TABEL ===== */
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10pt;
        }
        thead tr {
            background: #003527;
            color: white;
        }
        thead th {
            padding: 9px 12px;
            text-align: left;
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            font-weight: 600;
        }
        tbody tr:nth-child(even) { background: #f3f4f5; }
        tbody tr:nth-child(odd)  { background: #ffffff; }
        tbody td {
            padding: 8px 12px;
            border-bottom: 1px solid #e7e8e9;
            font-size: 10pt;
        }
        tbody td.bold { font-weight: 700; }
        tbody td.right { text-align: right; }

        /* ===== METRICS MODEL ===== */
        .metrics-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }
        .metric-box {
            background: #f3f4f5;
            border-radius: 8px;
            padding: 14px 16px;
            border: 1px solid #e7e8e9;
        }
        .metric-box .box-title {
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: #707974;
            margin-bottom: 8px;
            border-bottom: 1px solid #e7e8e9;
            padding-bottom: 6px;
        }
        .metric-row {
            display: flex;
            justify-content: space-between;
            font-size: 10pt;
            padding: 3px 0;
        }
        .metric-row .metric-label { color: #404944; }
        .metric-row .metric-value { font-weight: 700; color: #003527; }

        /* ===== FEATURE IMPORTANCE BAR ===== */
        .fi-row {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 6px;
        }
        .fi-label {
            width: 160px;
            font-size: 9.5pt;
            color: #404944;
            flex-shrink: 0;
        }
        .fi-bar-wrap {
            flex: 1;
            background: #e7e8e9;
            border-radius: 4px;
            height: 10px;
            overflow: hidden;
        }
        .fi-bar-fill {
            background: #003527;
            height: 100%;
            border-radius: 4px;
        }
        .fi-pct {
            width: 38px;
            font-size: 9pt;
            font-weight: 700;
            color: #003527;
            text-align: right;
            flex-shrink: 0;
        }

        /* ===== FOOTER LAPORAN ===== */
        .report-footer {
            margin-top: 32px;
            padding-top: 12px;
            border-top: 1px solid #e7e8e9;
            font-size: 9px;
            color: #707974;
            display: flex;
            justify-content: space-between;
        }
    </style>
</head>
<body>

<!-- Tombol aksi (tidak tercetak di PDF) -->
<div class="no-print">
    <div>
        <div style="font-size:15px; font-weight:700;">WastePredict AI — Laporan Prediksi</div>
        <div class="info">Pilih "Simpan sebagai PDF" di dialog cetak, atau klik tombol di bawah</div>
    </div>
    <div class="actions">
        <button class="btn-print" onclick="window.print()">🖨️ Cetak / Simpan PDF</button>
        <button class="btn-close" onclick="window.close()">✕ Tutup</button>
    </div>
</div>

<!-- Konten Laporan -->
<div class="report-wrapper">

    <!-- Header Laporan -->
    <div class="report-header">
        <div class="brand">
            <div class="brand-icon">♻</div>
            <div>
                <div class="brand-name">WastePredict AI</div>
                <div class="brand-sub">Industrial Intelligence</div>
            </div>
        </div>
        <div class="meta">
            <div class="doc-title">Laporan Prediksi Volume Sampah</div>
            <div>Kota Surakarta — Tahun Target: <strong><?= $target_year ?></strong></div>
            <div>Digenerate: <?= $generated_at ?></div>
            <div>Model: Random Forest Regression (v2.4)</div>
        </div>
    </div>

    <!-- Ringkasan Eksekutif -->
    <div class="section-title">Ringkasan Eksekutif</div>
    <div class="summary-grid">
        <div class="summary-card">
            <div class="label">Total Volume Tahunan</div>
            <div class="value"><?= $total_annual ?><span class="unit">Ton</span></div>
            <div class="sub">Prediksi kumulatif 12 bulan</div>
        </div>
        <div class="summary-card">
            <div class="label">Rata-rata Bulanan</div>
            <div class="value"><?= $avg_monthly ?><span class="unit">Ton</span></div>
            <div class="sub">Per bulan dalam <?= $target_year ?></div>
        </div>
        <div class="summary-card">
            <div class="label">Pertumbuhan vs 2025</div>
            <div class="value" style="color: <?= $growth_color ?>;">
                <?= $growth_symbol ?> <?= $growth_text ?>
            </div>
            <div class="sub">Baseline 2025: <?= $total_2025 ?> Ton</div>
        </div>
    </div>

    <!-- Tabel Prediksi 12 Bulan -->
    <div class="section-title">Prediksi Bulanan <?= $target_year ?></div>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Bulan</th>
                <th>Volume Sampah (Ton)</th>
                <th>Jumlah Wisatawan</th>
                <th>Curah Hujan (mm)</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $total_check = 0;
            foreach ($predictions as $i => $p):
                $total_check += $p['volume'];
            ?>
            <tr>
                <td><?= $i + 1 ?></td>
                <td class="bold"><?= htmlspecialchars($p['bulan_nama']) ?></td>
                <td class="right"><?= number_format($p['volume'], 2, ',', '.') ?></td>
                <td class="right"><?= number_format($p['wisatawan'], 0, ',', '.') ?></td>
                <td class="right"><?= number_format($p['curah_hujan'], 1, ',', '.') ?></td>
            </tr>
            <?php endforeach; ?>
            <tr style="background:#003527; color:white; font-weight:700;">
                <td colspan="2" style="padding:9px 12px;">TOTAL TAHUNAN</td>
                <td class="right" style="padding:9px 12px;"><?= $total_annual ?></td>
                <td colspan="2"></td>
            </tr>
        </tbody>
    </table>

    <!-- Feature Importance -->
    <div class="section-title page-break" style="page-break-before: avoid;">Kontribusi Fitur Model (Feature Importance)</div>
    <p style="font-size:10pt; color:#404944; margin-bottom:12px;">
        Grafik di bawah menunjukkan seberapa besar kontribusi setiap variabel input terhadap akurasi prediksi model Random Forest.
        Semakin panjang bar, semakin besar pengaruh fitur tersebut.
    </p>
    <?php foreach ($feature_imp as $fi): ?>
    <div class="fi-row">
        <div class="fi-label"><?= htmlspecialchars($fi['label']) ?></div>
        <div class="fi-bar-wrap">
            <div class="fi-bar-fill" style="width: <?= round($fi['importance'] * 100, 1) ?>%;"></div>
        </div>
        <div class="fi-pct"><?= round($fi['importance'] * 100, 1) ?>%</div>
    </div>
    <?php endforeach; ?>

    <!-- Metrik Akurasi Model -->
    <div class="section-title">Metrik Akurasi Model</div>
    <div class="metrics-grid">
        <div class="metric-box">
            <div class="box-title">📊 Data Latih (Train — 2017–2021)</div>
            <div class="metric-row">
                <span class="metric-label">MAE (Mean Absolute Error)</span>
                <span class="metric-value"><?= number_format($metrics['train']['mae'], 2, ',', '.') ?> Ton</span>
            </div>
            <div class="metric-row">
                <span class="metric-label">RMSE (Root Mean Square Error)</span>
                <span class="metric-value"><?= number_format($metrics['train']['rmse'], 2, ',', '.') ?> Ton</span>
            </div>
            <div class="metric-row">
                <span class="metric-label">R² Score (Koefisien Determinasi)</span>
                <span class="metric-value"><?= number_format($metrics['train']['r2'], 4, ',', '.') ?></span>
            </div>
        </div>
        <div class="metric-box">
            <div class="box-title">🧪 Data Uji (Test — 2022–2025)</div>
            <div class="metric-row">
                <span class="metric-label">MAE (Mean Absolute Error)</span>
                <span class="metric-value"><?= number_format($metrics['test']['mae'], 2, ',', '.') ?> Ton</span>
            </div>
            <div class="metric-row">
                <span class="metric-label">RMSE (Root Mean Square Error)</span>
                <span class="metric-value"><?= number_format($metrics['test']['rmse'], 2, ',', '.') ?> Ton</span>
            </div>
            <div class="metric-row">
                <span class="metric-label">R² Score (Koefisien Determinasi)</span>
                <span class="metric-value"><?= number_format($metrics['test']['r2'], 4, ',', '.') ?></span>
            </div>
        </div>
    </div>

    <!-- Catatan -->
    <p style="margin-top:20px; font-size:9.5pt; color:#707974; font-style:italic;">
        * Laporan ini dihasilkan secara otomatis oleh model Random Forest Regression WastePredict AI
        berdasarkan data historis Kota Surakarta (2016–2025). Hasil aktual dapat bervariasi tergantung
        kondisi sosial-ekonomi dan regulasi yang berlaku. Digunakan untuk keperluan perencanaan dan
        manajemen limbah internal.
    </p>

    <!-- Footer -->
    <div class="report-footer">
        <div>WastePredict AI — Industrial Intelligence © <?= date('Y') ?></div>
        <div>Laporan digenerate: <?= $generated_at ?></div>
    </div>

</div>

<script>
    // Auto-buka dialog print saat halaman dimuat
    window.onload = function() {
        // Delay sedikit agar halaman ter-render sempurna dahulu
        setTimeout(function() {
            window.print();
        }, 800);
    };
</script>
</body>
</html>
