<?php
// Menentukan output halaman berupa JSON
header('Content-Type: application/json');

// Menonaktifkan penayangan error langsung di output (HTML) agar tidak merusak format JSON
ini_set('display_errors', 0);
error_reporting(E_ALL);

// Mengambil input tahun dari parameter GET atau POST, default-nya 2026 jika tidak diisi
$year = isset($_GET['year']) ? intval($_GET['year']) : (isset($_POST['year']) ? intval($_POST['year']) : 2026);

// Validasi input tahun untuk pembatasan jangkauan model (tahun 2026 s/d 2100)
if ($year < 2026 || $year > 2100) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Tahun target tidak valid. Input harus di antara 2026 dan 2100.'
    ]);
    exit;
}

// Menentukan interpreter Python secara dinamis
$python_path = 'python3'; // Default fallback untuk environment cloud / Railway

// Deteksi environment lokal (PC) atau kontainer Docker
if (file_exists('/home/alfin/ETL-Project/venv/bin/python3')) {
    // Menggunakan virtual environment python lokal jika terdeteksi
    $python_path = '/home/alfin/ETL-Project/venv/bin/python3';
} elseif (file_exists('/opt/venv/bin/python3')) {
    // Menggunakan virtual environment kontainer Docker saat deploy di Railway
    $python_path = '/opt/venv/bin/python3';
}

// Menentukan path absolut dari script predict_engine.py dan mensterilkan parameter input dari celah command injection
$engine_path = __DIR__ . '/predict_engine.py';
$escaped_year = escapeshellarg($year);

// Merakit command untuk menjalankan python: "python3 /path/to/predict_engine.py <tahun_target> 2>&1"
// Bagian 2>&1 digunakan untuk menangkap error Python ke output string PHP agar mudah didebug
$command = "{$python_path} " . escapeshellarg($engine_path) . " {$escaped_year} 2>&1";

// Menjalankan command python di server melalui shell_exec
$output = shell_exec($command);

// Jika output kosong (null), kirim respon error
if ($output === null) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Gagal menjalankan engine prediksi (output kosong).'
    ]);
    exit;
}

// Memverifikasi apakah output dari Python adalah string JSON yang valid
$json_data = json_decode($output, true);
if (json_last_error() === JSON_ERROR_NONE) {
    // Jika valid, teruskan langsung output JSON dari Python ke browser / frontend AJAX
    echo $output;
} else {
    // Jika ada error/tidak valid, kembalikan respon error berikut output mentah dari CLI Python
    echo json_encode([
        'status' => 'error',
        'message' => 'Gagal memparsing output engine prediksi.',
        'raw_output' => $output
    ]);
}
