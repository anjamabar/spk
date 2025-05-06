<?php
// Start session to access results
session_start();

// Check if results exist in session
if (!isset($_SESSION['topsis_results']) || empty($_SESSION['topsis_results'])) {
    // Redirect to unggah if no results are available
    header(header: "Location: unggah.php");
    exit();
}

$results = $_SESSION['topsis_results'];

// Calculation process data
// We need to recalculate some of the TOPSIS steps for display purposes
$decision_matrix = array();
$normalized_matrix = array();
$weighted_normalized_matrix = array();

// Weights (same as in process.php)
$weights = [0.25, 0.30, 0.30, 0.15];

// Extract criteria from results for matrix display
foreach ($results as $alt) {
    $decision_matrix[] = $alt['criteria'];
}

// Calculate normalized matrix
function normalize_matrix($matrix) {
    $norm = [];
    $cols = count($matrix[0]);
    $rows = count($matrix);
    
    for ($j = 0; $j < $cols; $j++) {
        $sum_squares = 0;
        for ($i = 0; $i < $rows; $i++) {
            $sum_squares += pow($matrix[$i][$j], 2);
        }
        $sqrt_sum = sqrt($sum_squares);
        
        for ($i = 0; $i < $rows; $i++) {
            if (!isset($norm[$i])) {
                $norm[$i] = array();
            }
            $norm[$i][$j] = $matrix[$i][$j] / $sqrt_sum;
        }
    }
    return $norm;
}

$normalized_matrix = normalize_matrix($decision_matrix);

// Calculate weighted normalized matrix
for ($i = 0; $i < count($normalized_matrix); $i++) {
    $weighted_normalized_matrix[$i] = array();
    for ($j = 0; $j < count($normalized_matrix[$i]); $j++) {
        $weighted_normalized_matrix[$i][$j] = $normalized_matrix[$i][$j] * $weights[$j];
    }
}

// Determine ideal solutions
$ideal_positive = array();
$ideal_negative = array();

for ($j = 0; $j < count($weights); $j++) {
    $column_values = array_column($weighted_normalized_matrix, $j);
    $ideal_positive[$j] = max($column_values);  // Max value for benefit criteria
    $ideal_negative[$j] = min($column_values);  // Min value for benefit criteria
}

// Calculate distances
$positive_distances = array();
$negative_distances = array();

for ($i = 0; $i < count($weighted_normalized_matrix); $i++) {
    $pos_dist_sum = 0;
    $neg_dist_sum = 0;
    
    for ($j = 0; $j < count($weights); $j++) {
        $pos_dist_sum += pow($weighted_normalized_matrix[$i][$j] - $ideal_positive[$j], 2);
        $neg_dist_sum += pow($weighted_normalized_matrix[$i][$j] - $ideal_negative[$j], 2);
    }
    
    $positive_distances[$i] = sqrt($pos_dist_sum);
    $negative_distances[$i] = sqrt($neg_dist_sum);
}

// Map alternatives to codes (A1, A2, etc.)
foreach ($results as $index => $alt) {
    $results[$index]['alt_code'] = 'A' . ($index + 1);
    $results[$index]['positive_distance'] = $positive_distances[$index];
    $results[$index]['negative_distance'] = $negative_distances[$index];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SPK TOPSIS - Penerima Bantuan</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#121212',
                        secondary: '#1e1e1e',
                        accent: '#8a2be2',
                        accentLight: 'rgba(138, 43, 226, 0.2)',
                        text: '#f0f0f0',
                        textMuted: '#a0a0a0',
                        border: 'rgba(255, 255, 255, 0.07)',
                        tableHover: 'rgba(138, 43, 226, 0.08)',
                        tableHeader: '#232323',
                        highlight: '#00e5ff',
                        success: '#4caf50',
                        warning: '#ff9800'
                    },
                    boxShadow: {
                        'glow': '0 0 15px rgba(138, 43, 226, 0.5)',
                        'soft': '0 4px 20px rgba(0, 0, 0, 0.3)',
                        'inner-glow': 'inset 0 0 8px rgba(138, 43, 226, 0.3)'
                    },
                    animation: {
                        'float': 'float 3s ease-in-out infinite',
                        'fadeIn': 'fadeIn 0.5s ease-out',
                        'pulse-slow': 'pulse 4s cubic-bezier(0.4, 0, 0.6, 1) infinite'
                    },
                    keyframes: {
                        float: {
                            '0%, 100%': { transform: 'translateY(0)' },
                            '50%': { transform: 'translateY(-5px)' }
                        },
                        fadeIn: {
                            '0%': { opacity: '0', transform: 'translateY(10px)' },
                            '100%': { opacity: '1', transform: 'translateY(0)' }
                        }
                    }
                }
            }
        }
    </script>
    <style>
        .gradient-text {
            background: linear-gradient(90deg, #8a2be2, #ff00ff);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        
        .glass-card {
            background: rgba(30, 30, 30, 0.7);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .table-row-hover:hover {
            background: rgba(138, 43, 226, 0.08) !important;
            transform: scale(1.005);
        }
        
        .highlight-cell {
            position: relative;
        }
        
        .highlight-cell::after {
            content: '';
            position: absolute;
            left: 0;
            bottom: 0;
            width: 100%;
            height: 1px;
            background: linear-gradient(90deg, transparent, #8a2be2, transparent);
        }
        
        .numeric-cell {
            font-family: 'Consolas', monospace;
            letter-spacing: 0.5px;
        }
        
        .top-3-badge {
            animation: float 3s ease-in-out infinite;
        }
        
        .top-1-badge {
            animation: pulse-slow 4s infinite;
        }
    </style>
</head>
<body class="bg-primary text-text min-h-screen flex flex-col items-center p-6 md:p-8">
    <!-- Floating particles background -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none">
        <div class="absolute top-1/4 left-1/4 w-2 h-2 rounded-full bg-accent opacity-20 animate-float" style="animation-delay: 0s;"></div>
        <div class="absolute top-1/3 right-1/5 w-3 h-3 rounded-full bg-purple-500 opacity-15 animate-float" style="animation-delay: 1s;"></div>
        <div class="absolute bottom-1/4 left-1/3 w-1 h-1 rounded-full bg-pink-500 opacity-15 animate-float" style="animation-delay: 2s;"></div>
        <div class="absolute top-2/5 right-1/3 w-2 h-2 rounded-full bg-accent opacity-20 animate-float" style="animation-delay: 3s;"></div>
    </div>

    <div class="relative z-10 w-full max-w-7xl">
        <!-- Header with animated gradient -->
        <header class="mb-10 text-center">
            <h1 class="text-4xl md:text-5xl font-bold gradient-text mb-3 animate-fadeIn">
                <i class="fas fa-chart-line mr-3"></i>Hasil SPK TOPSIS
            </h1>
            <p class="text-textMuted text-lg max-w-2xl mx-auto">
                Sistem Pendukung Keputusan untuk menentukan penerima bantuan sosial
            </p>
        </header>

        <!-- Main content container -->
        <div class="glass-card rounded-xl p-6 shadow-soft mb-8 animate-fadeIn" style="animation-delay: 0.2s;">
            <!-- Criteria info cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
                <div class="bg-secondary p-4 rounded-lg border-l-4 border-accent shadow-inner-glow">
                    <h3 class="text-accent font-semibold mb-1"><i class="fas fa-users mr-2"></i>C1</h3>
                    <p class="text-sm text-textMuted">Anggota Keluarga</p>
                    <p class="text-lg font-bold">25%</p>
                </div>
                <div class="bg-secondary p-4 rounded-lg border-l-4 border-blue-400 shadow-inner-glow">
                    <h3 class="text-blue-400 font-semibold mb-1"><i class="fas fa-briefcase mr-2"></i>C2</h3>
                    <p class="text-sm text-textMuted">Pekerjaan</p>
                    <p class="text-lg font-bold">30%</p>
                </div>
                <div class="bg-secondary p-4 rounded-lg border-l-4 border-green-400 shadow-inner-glow">
                    <h3 class="text-green-400 font-semibold mb-1"><i class="fas fa-money-bill-wave mr-2"></i>C3</h3>
                    <p class="text-sm text-textMuted">Penghasilan</p>
                    <p class="text-lg font-bold">30%</p>
                </div>
                <div class="bg-secondary p-4 rounded-lg border-l-4 border-yellow-400 shadow-inner-glow">
                    <h3 class="text-yellow-400 font-semibold mb-1"><i class="fas fa-home mr-2"></i>C4</h3>
                    <p class="text-sm text-textMuted">Status Rumah</p>
                    <p class="text-lg font-bold">15%</p>
                </div>
            </div>

            <!-- Navigation controls -->
            <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
                <div class="flex items-center space-x-2">
                    <button id="prevTable" class="flex items-center px-4 py-2 bg-secondary rounded-lg border border-accent text-accent hover:bg-accentLight transition-all">
                        <i class="fas fa-chevron-left mr-2"></i> Sebelumnya
                    </button>
                    <button id="nextTable" class="flex items-center px-4 py-2 bg-secondary rounded-lg border border-accent text-accent hover:bg-accentLight transition-all">
                        Selanjutnya <i class="fas fa-chevron-right ml-2"></i>
                    </button>
                </div>
                
                <div class="flex space-x-1">
                    <div class="page-dot w-3 h-3 rounded-full bg-accent opacity-100 cursor-pointer" data-page="0"></div>
                    <div class="page-dot w-3 h-3 rounded-full bg-textMuted opacity-30 cursor-pointer" data-page="1"></div>
                    <div class="page-dot w-3 h-3 rounded-full bg-textMuted opacity-30 cursor-pointer" data-page="2"></div>
                    <div class="page-dot w-3 h-3 rounded-full bg-textMuted opacity-30 cursor-pointer" data-page="3"></div>
                    <div class="page-dot w-3 h-3 rounded-full bg-textMuted opacity-30 cursor-pointer" data-page="4"></div>
                    <div class="page-dot w-3 h-3 rounded-full bg-textMuted opacity-30 cursor-pointer" data-page="5"></div>
                    <div class="page-dot w-3 h-3 rounded-full bg-textMuted opacity-30 cursor-pointer" data-page="6"></div>
                </div>
            </div>

            <!-- Table sections -->
            <!-- Table 1: Results -->
            <div class="table-section active" id="table1">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-semibold text-accent flex items-center">
                        <i class="fas fa-trophy mr-3"></i>Hasil Akhir Perhitungan
                    </h2>
                    <span class="text-textMuted text-sm"><?php echo count($results); ?> penerima</span>
                </div>
                
                <!-- Sort results by score in descending order -->
                <?php 
                usort($results, function($a, $b) {
                    return $b['score'] <=> $a['score'];
                });
                $_SESSION['sorted_results'] = $results; // Store sorted results in session
                ?>
                
                <div class="overflow-x-auto rounded-xl shadow-soft">
                    <table class="w-full">
                        <thead class="bg-tableHeader">
                            <tr>
                                <th class="py-3 px-4 text-left text-accent font-medium">Ranking</th>
                                <th class="py-3 px-4 text-left text-accent font-medium">Alternatif</th>
                                <th class="py-3 px-4 text-left text-accent font-medium">Nama</th>
                                <th class="py-3 px-4 text-left text-accent font-medium">NIK</th>
                                <th class="py-3 px-4 text-left text-accent font-medium">Skor</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border">
                            <?php foreach ($results as $index => $alt): ?>
                            <tr class="bg-secondary/50 hover:bg-tableHover transition-colors table-row-hover <?php echo ($index < 3) ? 'highlight-row' : ''; ?>">
                                <td class="py-3 px-4">
                                    <?php if($index == 0): ?>
                                        <span class="top-1-badge inline-flex items-center justify-center w-8 h-8 rounded-full bg-gradient-to-br from-accent to-purple-600 text-white font-bold">1</span>
                                    <?php elseif($index < 3): ?>
                                        <span class="top-3-badge inline-flex items-center justify-center w-8 h-8 rounded-full bg-accentLight text-accent font-bold"><?php echo $index + 1; ?></span>
                                    <?php else: ?>
                                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-secondary text-textMuted"><?php echo $index + 1; ?></span>
                                    <?php endif; ?>
                                </td>
                                <td class="py-3 px-4 font-mono text-warning"><?php echo $alt['no'] . 'F'; ?></td>
                                <td class="py-3 px-4"><?php echo $alt['nama']; ?></td>
                                <td class="py-3 px-4 font-mono"><?php echo $alt['nik']; ?></td>
                                <td class="py-3 px-4 numeric-cell text-highlight"><?php echo number_format($alt['score'], 4); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Table 2: Detail Criteria -->
            <div class="table-section hidden" id="table2">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-semibold text-accent flex items-center">
                        <i class="fas fa-list-ul mr-3"></i>Detail Kriteria Alternatif
                    </h2>
                    <span class="text-textMuted text-sm">Nilai mentah dan ternormalisasi</span>
                </div>
                
                <div class="overflow-x-auto rounded-xl shadow-soft">
                    <table class="w-full">
                        <thead class="bg-tableHeader">
                            <tr>
                                <th class="py-3 px-4 text-left text-accent font-medium">Alternatif</th>
                                <th class="py-3 px-4 text-left text-accent font-medium">Nama</th>
                                <th class="py-3 px-4 text-left text-accent font-medium">C1 (Keluarga)</th>
                                <th class="py-3 px-4 text-left text-accent font-medium">C2 (Pekerjaan)</th>
                                <th class="py-3 px-4 text-left text-accent font-medium">C3 (Penghasilan)</th>
                                <th class="py-3 px-4 text-left text-accent font-medium">C4 (Rumah)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border">
                            <?php foreach ($results as $alt): ?>
                            <tr class="bg-secondary/50 hover:bg-tableHover transition-colors table-row-hover">
                            <td class="py-3 px-4 font-mono text-warning"><?php echo $alt['no'] . 'F'; ?></td>
                                <td class="py-3 px-4"><?php echo $alt['nama']; ?></td>
                                <td class="py-3 px-4">
                                    <div class="text-sm"><?php echo $alt['raw_data']['anggota_keluarga']; ?></div>
                                    <div class="text-xs text-highlight numeric-cell"><?php echo $alt['criteria'][0]; ?></div>
                                </td>
                                <td class="py-3 px-4">
                                    <div class="text-sm"><?php echo $alt['raw_data']['pekerjaan']; ?></div>
                                    <div class="text-xs text-highlight numeric-cell"><?php echo $alt['criteria'][1]; ?></div>
                                </td>
                                <td class="py-3 px-4">
                                    <div class="text-sm"><?php echo $alt['raw_data']['penghasilan']; ?></div>
                                    <div class="text-xs text-highlight numeric-cell"><?php echo $alt['criteria'][2]; ?></div>
                                </td>
                                <td class="py-3 px-4">
                                    <div class="text-sm"><?php echo $alt['raw_data']['status_rumah']; ?></div>
                                    <div class="text-xs text-highlight numeric-cell"><?php echo $alt['criteria'][3]; ?></div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Table 3: Decision Matrix -->
            <div class="table-section hidden" id="table3">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-semibold text-accent flex items-center">
                        <i class="fas fa-table mr-3"></i>Matriks Keputusan
                    </h2>
                    <span class="text-textMuted text-sm">Nilai numerik setiap kriteria</span>
                </div>
                
                <div class="bg-secondary/50 rounded-lg p-4 mb-4 border-l-4 border-accent">
                    <h3 class="text-accent font-medium mb-2"><i class="fas fa-info-circle mr-2"></i>Keterangan:</h3>
                    <p class="text-sm">Matriks keputusan menunjukkan nilai setiap alternatif untuk masing-masing kriteria setelah dikonversi ke skala numerik.</p>
                </div>
                
                <div class="overflow-x-auto rounded-xl shadow-soft">
                    <table class="w-full">
                        <thead class="bg-tableHeader">
                            <tr>
                                <th class="py-3 px-4 text-left text-accent font-medium">Alternatif</th>
                                <th class="py-3 px-4 text-left text-accent font-medium">C1</th>
                                <th class="py-3 px-4 text-left text-accent font-medium">C2</th>
                                <th class="py-3 px-4 text-left text-accent font-medium">C3</th>
                                <th class="py-3 px-4 text-left text-accent font-medium">C4</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border">
                            <?php foreach ($results as $index => $alt): ?>
                            <tr class="bg-secondary/50 hover:bg-tableHover transition-colors table-row-hover">
                            <td class="py-3 px-4 font-mono text-warning"><?php echo $alt['no'] . 'F'; ?></td>
                                <?php for ($j = 0; $j < count($alt['criteria']); $j++): ?>
                                    <td class="py-3 px-4 numeric-cell text-highlight"><?php echo $alt['criteria'][$j]; ?></td>
                                <?php endfor; ?>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Table 4: Normalized Matrix -->
            <div class="table-section hidden" id="table4">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-semibold text-accent flex items-center">
                        <i class="fas fa-calculator mr-3"></i>Matriks Ternormalisasi
                    </h2>
                    <span class="text-textMuted text-sm">Normalisasi Euclidean</span>
                </div>
                
                <div class="bg-secondary/50 rounded-lg p-4 mb-4 border-l-4 border-accent">
                    <h3 class="text-accent font-medium mb-2"><i class="fas fa-info-circle mr-2"></i>Keterangan:</h3>
                    <p class="text-sm mb-1">Matriks ternormalisasi didapatkan dengan rumus: r<sub>ij</sub> = x<sub>ij</sub> / √(Σx<sub>ij</sub>²)</p>
                    <p class="text-sm">Dimana x<sub>ij</sub> adalah nilai kriteria j untuk alternatif i.</p>
                </div>
                
                <div class="overflow-x-auto rounded-xl shadow-soft">
                    <table class="w-full">
                        <thead class="bg-tableHeader">
                            <tr>
                                <th class="py-3 px-4 text-left text-accent font-medium">Alternatif</th>
                                <th class="py-3 px-4 text-left text-accent font-medium">C1</th>
                                <th class="py-3 px-4 text-left text-accent font-medium">C2</th>
                                <th class="py-3 px-4 text-left text-accent font-medium">C3</th>
                                <th class="py-3 px-4 text-left text-accent font-medium">C4</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border">
                            <?php foreach ($normalized_matrix as $i => $row): ?>
                            <tr class="bg-secondary/50 hover:bg-tableHover transition-colors table-row-hover">
                                <td class="py-3 px-4 font-mono text-warning"><?php echo $results[$i]['no'] . 'F'; ?></td>
                                <?php foreach ($row as $val): ?>
                                    <td class="py-3 px-4 numeric-cell text-highlight"><?php echo number_format($val, 4); ?></td>
                                <?php endforeach; ?>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Table 5: Weighted Normalized Matrix -->
            <div class="table-section hidden" id="table5">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-semibold text-accent flex items-center">
                        <i class="fas fa-weight-hanging mr-3"></i>Matriks Ternormalisasi Terbobot
                    </h2>
                    <span class="text-textMuted text-sm">Dengan bobot kriteria</span>
                </div>
                
                <div class="bg-secondary/50 rounded-lg p-4 mb-4 border-l-4 border-accent">
                    <h3 class="text-accent font-medium mb-2"><i class="fas fa-info-circle mr-2"></i>Keterangan:</h3>
                    <p class="text-sm mb-1">Matriks ternormalisasi terbobot didapatkan dengan mengalikan setiap elemen matriks ternormalisasi dengan bobot kriteria terkait.</p>
                    <p class="text-sm mb-1">y<sub>ij</sub> = w<sub>j</sub> * r<sub>ij</sub></p>
                    <p class="text-sm">Bobot (w): C1=0.25, C2=0.30, C3=0.30, C4=0.15</p>
                </div>
                
                <div class="overflow-x-auto rounded-xl shadow-soft">
                    <table class="w-full">
                        <thead class="bg-tableHeader">
                            <tr>
                                <th class="py-3 px-4 text-left text-accent font-medium">Alternatif</th>
                                <th class="py-3 px-4 text-left text-accent font-medium">C1</th>
                                <th class="py-3 px-4 text-left text-accent font-medium">C2</th>
                                <th class="py-3 px-4 text-left text-accent font-medium">C3</th>
                                <th class="py-3 px-4 text-left text-accent font-medium">C4</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border">
                            <?php foreach ($weighted_normalized_matrix as $i => $row): ?>
                            <tr class="bg-secondary/50 hover:bg-tableHover transition-colors table-row-hover">
                            <td class="py-3 px-4 font-mono text-warning"><?php echo $results[$i]['no'] . 'F'; ?></td>
                                <?php foreach ($row as $val): ?>
                                    <td class="py-3 px-4 numeric-cell text-highlight"><?php echo number_format($val, 4); ?></td>
                                <?php endforeach; ?>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Table 6: Ideal Solutions -->
            <div class="table-section hidden" id="table6">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-semibold text-accent flex items-center">
                        <i class="fas fa-bullseye mr-3"></i>Solusi Ideal
                    </h2>
                    <span class="text-textMuted text-sm">Positif dan Negatif</span>
                </div>
                
                <div class="bg-secondary/50 rounded-lg p-4 mb-4 border-l-4 border-accent">
                    <h3 class="text-accent font-medium mb-2"><i class="fas fa-info-circle mr-2"></i>Keterangan:</h3>
                    <p class="text-sm mb-1">Solusi ideal positif (A+) adalah nilai maksimal dari setiap kolom kriteria (benefit)</p>
                    <p class="text-sm">Solusi ideal negatif (A-) adalah nilai minimal dari setiap kolom kriteria (benefit)</p>
                </div>
                
                <div class="overflow-x-auto rounded-xl shadow-soft">
                    <table class="w-full">
                        <thead class="bg-tableHeader">
                            <tr>
                                <th class="py-3 px-4 text-left text-accent font-medium">Solusi Ideal</th>
                                <th class="py-3 px-4 text-left text-accent font-medium">C1</th>
                                <th class="py-3 px-4 text-left text-accent font-medium">C2</th>
                                <th class="py-3 px-4 text-left text-accent font-medium">C3</th>
                                <th class="py-3 px-4 text-left text-accent font-medium">C4</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border">
                            <tr class="bg-accentLight/30 hover:bg-tableHover transition-colors table-row-hover">
                                <td class="py-3 px-4 font-semibold text-accent"><i class="fas fa-plus-circle mr-2"></i>A+ (Positif)</td>
                                <?php foreach ($ideal_positive as $val): ?>
                                    <td class="py-3 px-4 numeric-cell text-highlight"><?php echo number_format($val, 4); ?></td>
                                <?php endforeach; ?>
                            </tr>
                            <tr class="bg-accentLight/30 hover:bg-tableHover transition-colors table-row-hover">
                                <td class="py-3 px-4 font-semibold text-accent"><i class="fas fa-minus-circle mr-2"></i>A- (Negatif)</td>
                                <?php foreach ($ideal_negative as $val): ?>
                                    <td class="py-3 px-4 numeric-cell text-highlight"><?php echo number_format($val, 4); ?></td>
                                <?php endforeach; ?>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Table 7: Distances and Final Scores -->
            <div class="table-section hidden" id="table7">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-semibold text-accent flex items-center">
                        <i class="fas fa-ruler-combined mr-3"></i>Jarak dan Preferensi
                    </h2>
                    <span class="text-textMuted text-sm">Perhitungan akhir</span>
                </div>
                
                <div class="bg-secondary/50 rounded-lg p-4 mb-4 border-l-4 border-accent">
                    <h3 class="text-accent font-medium mb-2"><i class="fas fa-info-circle mr-2"></i>Keterangan:</h3>
                    <p class="text-sm mb-1">D+ adalah jarak setiap alternatif ke solusi ideal positif</p>
                    <p class="text-sm mb-1">D- adalah jarak setiap alternatif ke solusi ideal negatif</p>
                    <p class="text-sm mb-1">Skor preferensi dihitung dengan rumus: S = D- / (D+ + D-)</p>
                    <p class="text-sm">Semakin besar nilai S, semakin tinggi peringkat alternatif</p>
                </div>
                
                <div class="overflow-x-auto rounded-xl shadow-soft">
                    <table class="w-full">
                        <thead class="bg-tableHeader">
                            <tr>
                                <th class="py-3 px-4 text-left text-accent font-medium">Ranking</th>
                                <th class="py-3 px-4 text-left text-accent font-medium">Alternatif</th>
                                <th class="py-3 px-4 text-left text-accent font-medium">Nama</th>
                                <th class="py-3 px-4 text-left text-accent font-medium">D+ (A+)</th>
                                <th class="py-3 px-4 text-left text-accent font-medium">D- (A-)</th>
                                <th class="py-3 px-4 text-left text-accent font-medium">Skor</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border">
                            <?php foreach ($results as $index => $alt): ?>
                            <tr class="bg-secondary/50 hover:bg-tableHover transition-colors table-row-hover">
                                <td class="py-3 px-4">
                                    <?php if($index == 0): ?>
                                        <span class="top-1-badge inline-flex items-center justify-center w-8 h-8 rounded-full bg-gradient-to-br from-accent to-purple-600 text-white font-bold">1</span>
                                    <?php elseif($index < 3): ?>
                                        <span class="top-3-badge inline-flex items-center justify-center w-8 h-8 rounded-full bg-accentLight text-accent font-bold"><?php echo $index + 1; ?></span>
                                    <?php else: ?>
                                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-secondary text-textMuted"><?php echo $index + 1; ?></span>
                                    <?php endif; ?>
                                </td>
                                <td class="py-3 px-4 font-mono text-warning"><?php echo $alt['no'] . 'F'; ?></td>
                                <td class="py-3 px-4"><?php echo $alt['nama']; ?></td>
                                <td class="py-3 px-4 numeric-cell text-highlight"><?php echo number_format($alt['positive_distance'], 4); ?></td>
                                <td class="py-3 px-4 numeric-cell text-highlight"><?php echo number_format($alt['negative_distance'], 4); ?></td>
                                <td class="py-3 px-4 numeric-cell text-success font-bold"><?php echo number_format($alt['score'], 4); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Back button with animation -->
        <a href="unggah.php" class="mt-8 px-6 py-3 bg-gradient-to-r from-accent to-purple-600 text-white font-semibold rounded-lg shadow-glow hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 flex items-center justify-center">
            <i class="fas fa-arrow-left mr-2"></i> Kembali ke Halaman Unggah
        </a>
    </div>

    <script>
        // Table navigation
        const tables = document.querySelectorAll('.table-section');
        const dots = document.querySelectorAll('.page-dot');
        let currentTableIndex = 0;
        
        function showTable(index) {
            // Hide all tables
            tables.forEach(table => {
                table.classList.remove('active');
                table.classList.add('hidden');
            });
            dots.forEach(dot => dot.classList.remove('bg-accent', 'opacity-100'));
            
            // Show selected table
            tables[index].classList.remove('hidden');
            tables[index].classList.add('active');
            dots[index].classList.add('bg-accent', 'opacity-100');
            currentTableIndex = index;
        }
        
        document.getElementById('nextTable').addEventListener('click', function() {
            const newIndex = (currentTableIndex + 1) % tables.length;
            showTable(newIndex);
        });
        
        document.getElementById('prevTable').addEventListener('click', function() {
            const newIndex = (currentTableIndex - 1 + tables.length) % tables.length;
            showTable(newIndex);
        });
        
        // Enable clicking on dots for direct navigation
        dots.forEach((dot, index) => {
            dot.addEventListener('click', function() {
                showTable(index);
            });
        });

        // Add animation to elements when they come into view
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animate-fadeIn');
                }
            });
        }, { threshold: 0.1 });

        document.querySelectorAll('.table-section, h1, .glass-card').forEach(el => {
            observer.observe(el);
        });
    </script>
</body>
</html>