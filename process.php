<?php
// Load library PHPSpreadsheet
require 'vendor/autoload.php'; // pastikan sudah install phpoffice/phpspreadsheet

use PhpOffice\PhpSpreadsheet\IOFactory;

// Check if a file was provided via GET parameter
if (!isset($_GET['file']) || empty($_GET['file'])) {
    // Redirect to unggah.php if no file is specified
    header("Location: unggah.php");
    exit();
}

$file = $_GET['file'];

// Ensure the file exists
if (!file_exists($file)) {
    die("File tidak ditemukan. <a href='unggah.php'>Kembali</a>");
}

// Konversi nilai jumlah anggota keluarga (C1) berdasarkan tabel
// Benefit: Semakin banyak anggota keluarga, semakin tinggi nilai (kebutuhan bantuan lebih tinggi)
function convert_anggota_keluarga($value) {
    $value = (int)$value;
    if ($value >= 6 && $value <= 8) return 3; // Nilai tertinggi untuk keluarga besar
    if ($value >= 3 && $value <= 5) return 2;
    if ($value >= 1 && $value <= 2) return 1;
    return 1; // default jika di luar range
}

// Konversi nilai pekerjaan (C2) berdasarkan tabel
// Benefit: Semakin tidak stabil pekerjaan, semakin tinggi nilai (kebutuhan bantuan lebih tinggi)
function convert_pekerjaan($value) {
    $value = strtoupper(trim($value));
    if ($value == "TIDAK BEKERJA") return 5;
    if ($value == "PETANI") return 4;
    if ($value == "PNS") return 3 ; // Lebih stabil, skor rendah
    if ($value == "KARYAWAN SWASTA") return 2;
    if ($value == "WIRASWASTA") return 2;
    return 3; // default untuk pekerjaan lainnya
}

// Konversi nilai penghasilan (C3) berdasarkan tabel
// Benefit: Semakin rendah penghasilan, semakin tinggi nilai (kebutuhan bantuan lebih tinggi)
// Modified function
// Konversi nilai penghasilan (C3) berdasarkan tabel yang ditunjukkan
function convert_penghasilan($value) {
    $original_value = $value; // Simpan nilai asli untuk pengecekan format
    $value = str_replace(['.', ' ', 'Rp', 'RP', 'rp'], '', $value);
    
    // Cek format spesifik dengan tanda ">"
    if (strpos($original_value, '>3.000.000') !== false || 
        strpos($original_value, '>3000000') !== false) {
        return 3;
    }
    
    if (strpos($original_value, '>2.000.000') !== false || 
        strpos($original_value, '>2000000') !== false) {
        return 4;
    }
    
    // Cek khusus untuk range 500-800
    if (strpos($value, '500-800') !== false || 
        strpos($value, '500000-800000') !== false) {
        return 5;
    }
    
    // Cek berdasarkan angka
    $value = (int)filter_var($value, FILTER_SANITIZE_NUMBER_INT);
    if ($value >= 500000 && $value <= 800000) return 5;
    if ($value > 2000000 && $value <= 3000000) return 4;
    if ($value > 3000000) return 3;
    
    // Untuk nilai yang tidak masuk dalam range di atas
    return 5; // Default, asumsikan penghasilan rendah jika tidak terdeteksi
}

// Konversi status rumah (C4) berdasarkan tabel
// Benefit: Semakin tidak memiliki rumah sendiri, semakin tinggi nilai (kebutuhan bantuan lebih tinggi)
function convert_status($value) {
    $value = strtoupper(trim($value));
    if ($value == "MENUMPANG") return 2; // Tidak punya rumah sendiri
    if ($value == "MILIK SENDIRI") return 1;
    return 3; // Default untuk status lainnya
}

// Normalisasi matriks keputusan
function normalize($matrix) {
    $norm = [];
    $cols = count($matrix[0]);
    for ($j = 0; $j < $cols; $j++) {
        $sum = 0;
        foreach ($matrix as $row) {
            $sum += pow($row[$j], 2); // kuadratkan setiap elemen kolom
        }
        $sqrt = sqrt($sum); // ambil akar dari total kuadrat
        foreach ($matrix as $i => $row) {
            $norm[$i][$j] = $row[$j] / $sqrt; // normalisasi
        }
    }
    return $norm;
}

// Fungsi utama metode TOPSIS
function topsis($alternatives, $weights) {
    // Normalisasi dan pembobotan
    $normalized = normalize(array_column($alternatives, 'criteria'));
    $weighted = [];
    foreach ($normalized as $i => $row) {
        foreach ($row as $j => $val) {
            $weighted[$i][$j] = $val * $weights[$j]; // bobot * nilai normalisasi
        }
    }

    // Menentukan solusi ideal positif dan negatif
    // Semua kriteria adalah benefit, maka solusi ideal positif adalah nilai maksimum
    $ideal_pos = array_map('max', array_map(null, ...$weighted));
    $ideal_neg = array_map('min', array_map(null, ...$weighted));

    $scores = [];
    foreach ($weighted as $i => $row) {
        $d_pos = 0;
        $d_neg = 0;
        foreach ($row as $j => $val) {
            $d_pos += pow($val - $ideal_pos[$j], 2); // jarak ke solusi ideal positif
            $d_neg += pow($val - $ideal_neg[$j], 2); // jarak ke solusi ideal negatif
        }
        $d_pos = sqrt($d_pos);
        $d_neg = sqrt($d_neg);
        $scores[$i] = $d_neg / ($d_pos + $d_neg); // skor preferensi
    }

    // Tambahkan skor ke data alternatif
    foreach ($alternatives as $i => &$alt) {
        $alt['score'] = $scores[$i];
    }

    // Urutkan berdasarkan skor tertinggi
    usort($alternatives, function ($a, $b) {
        return $b['score'] <=> $a['score'];
    });

    return $alternatives;
}

try {
    // Load Excel file
    $spreadsheet = IOFactory::load($file);
    $sheet = $spreadsheet->getActiveSheet();
    $data = $sheet->toArray(); // konversi sheet ke array


$alternatives = [];
for ($i = 1; $i < count($data); $i++) { // mulai dari baris ke-2 (menghindari header)
    $row = $data[$i];
    
    // Ubah bagian ini
    $alternatives[] = [
        'no' => $row[0],       // kolom A (No)
        'nik' => $row[1],      // kolom B (NIK)
        'nama' => $row[2],     // kolom C (Nama)
        'alt_code' => 'A' . $i, // Gunakan indeks iterasi, bukan nilai dari kolom No
        // atau alternatif lain: 'alt_code' => 'A' . ($i),
        'criteria' => [
            convert_anggota_keluarga($row[3]), // jumlah anggota keluarga (C1)
            convert_pekerjaan($row[4]),        // pekerjaan (C2)
            convert_penghasilan($row[5]),      // penghasilan (C3)
            convert_status($row[6])            // status rumah (C4)
        ],
        'raw_data' => [
            'anggota_keluarga' => $row[3],
            'pekerjaan' => $row[4],
            'penghasilan' => $row[5],
            'status_rumah' => $row[6]
        ]
    ];
}

    // Bobot masing-masing kriteria sesuai dengan kebutuhan sistem
    // C1 = Jumlah anggota keluarga, C2 = Pekerjaan, C3 = Penghasilan, C4 = Status rumah
    // Anda bisa atur bobot di sini (contoh: 25%, 30%, 30%, 15%)
    $weights = [0.25, 0.30, 0.30, 0.15];
    
    $results = topsis($alternatives, $weights); // jalankan perhitungan topsis

    // Store results in session
    session_start();
    $_SESSION['topsis_results'] = $results;
    
    // Redirect to results page
    header("Location: results.php");
    exit();
    
} catch (Exception $e) {
    die("Error: " . $e->getMessage() . " <a href='unggah.php'>Kembali</a>");
}   