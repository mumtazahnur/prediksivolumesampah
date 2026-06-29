<?php
// Mengimpor data historis dari data.php (Source of Truth)
$dataset = include 'data.php';

// Mengatur Header HTTP agar browser mengenali output sebagai file CSV untuk diunduh
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=historical_waste_data.csv');

// Membuka output stream untuk menulis file CSV langsung ke browser
$output = fopen('php://output', 'w');

// Menuliskan baris pertama berupa nama-nama kolom (Header CSV)
fputcsv($output, ['Tahun', 'Bulan', 'Bulan Nama', 'Volume (Tons)', 'Penduduk', 'Kepadatan', 'Wisatawan', 'Curah Hujan']);

// Melakukan perulangan untuk setiap baris data di dataset dan memasukkannya ke file CSV
foreach ($dataset as $row) {
    fputcsv($output, [
        $row['tahun'],
        $row['bulan'],
        $row['bulan_nama'],
        $row['volume'],
        $row['penduduk'],
        $row['kepadatan'],
        $row['wisatawan'],
        $row['curah_hujan']
    ]);
}

// Menutup output stream dan menghentikan eksekusi script
fclose($output);
exit;
