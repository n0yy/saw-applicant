<?php
require_once '../config/db.php';
session_start();

// Validasi session admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: ../index.php');
    exit();
}

function edu_score($edu) {
    return match($edu) {
        'SMA' => 1,
        'Diploma' => 2,
        'S1' => 3,
        'S2' => 4,
        default => 0
    };
}

$job_id = $_GET['job_id'] ?? 0;
if (!$job_id) die("Job ID tidak ditemukan.");

// Validasi job_id ada di database
$stmt = $conn->prepare("SELECT id, title FROM jobs WHERE id = ?");
$stmt->bind_param("i", $job_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Lowongan tidak ditemukan");
}

$job = $result->fetch_assoc();
$stmt->close();

// Ambil pelamar dengan prepared statement
$stmt = $conn->prepare("
    SELECT a.id, u.name, a.experience_years, a.education_level
    FROM applications a
    JOIN users u ON a.user_id = u.id
    WHERE a.job_id = ? AND a.status = 'submitted'
");
$stmt->bind_param("i", $job_id);
$stmt->execute();
$result = $stmt->get_result();

$applicants = [];
$max_exp = 0;
$max_edu = 0;

// Normalisasi: cari nilai maksimum tiap kriteria
while ($row = $result->fetch_assoc()) {
    $edu = edu_score($row['education_level']);
    $exp = $row['experience_years'];
    $applicants[] = [
        'id' => $row['id'],
        'name' => $row['name'],
        'experience' => $exp,
        'education' => $edu,
    ];
    if ($exp > $max_exp) $max_exp = $exp;
    if ($edu > $max_edu) $max_edu = $edu;
}
$stmt->close();

// Cek apakah ada pelamar
if (empty($applicants)) {
    ?>
    <!DOCTYPE html>
    <html lang="id">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Hasil SAW - SAW Applicant System</title>
        <link href="../assets/css/bootstrap.min.css" rel="stylesheet">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
        <style>
            body {
                background-color: #f8f9fa;
                font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            }
            .navbar {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            }
            .card {
                border: none;
                border-radius: 15px;
                box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            }
            .btn-custom {
                border-radius: 10px;
                padding: 8px 20px;
                font-weight: 600;
                transition: all 0.3s ease;
            }
            .btn-custom:hover {
                transform: translateY(-2px);
            }
        </style>
    </head>
    <body>
        <!-- Navbar -->
        <nav class="navbar navbar-expand-lg navbar-dark">
            <div class="container">
                <a class="navbar-brand fw-bold" href="../views/admin/job_list.php">
                    <i class="fas fa-arrow-left me-2"></i>Kembali ke Daftar Lowongan
                </a>
                <div class="navbar-nav ms-auto">
                    <span class="navbar-text me-3">
                        <i class="fas fa-user me-1"></i>
                        <?= htmlspecialchars($_SESSION['user']['name']) ?>
                    </span>
                    <a href="LogoutController.php" class="btn btn-outline-light btn-sm">
                        <i class="fas fa-sign-out-alt me-1"></i>Logout
                    </a>
                </div>
            </div>
        </nav>

        <div class="container-fluid py-4">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-8">
                        <div class="card">
                            <div class="card-body text-center py-5">
                                <i class="fas fa-users text-muted" style="font-size: 4rem;"></i>
                                <h3 class="mt-3 text-muted">Tidak ada pelamar</h3>
                                <p class="text-muted">Tidak ada pelamar yang telah submit aplikasi untuk lowongan ini.</p>
                                <a href="../views/admin/job_list.php" class="btn btn-primary btn-custom">
                                    <i class="fas fa-arrow-left me-2"></i>Kembali ke Daftar Lowongan
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script src="../assets/js/bootstrap.bundle.min.js"></script>
    </body>
    </html>
    <?php
    exit();
}

// Konfigurasi bobot kriteria (bisa diubah sesuai kebutuhan)
$weight_experience = 0.6;
$weight_education = 0.4;

// Hitung skor SAW dengan penanganan division by zero
foreach ($applicants as &$a) {
    $exp_score = ($max_exp > 0) ? ($a['experience'] / $max_exp) : 0;
    $edu_score = ($max_edu > 0) ? ($a['education'] / $max_edu) : 0;
    $a['score'] = ($weight_experience * $exp_score) + ($weight_education * $edu_score);
}
unset($a);

// Urutkan berdasarkan score
usort($applicants, fn($a, $b) => $b['score'] <=> $a['score']);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hasil Seleksi SAW - SAW Applicant System</title>
    <link href="../assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .navbar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        }
        .table {
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        }
        .btn-custom {
            border-radius: 10px;
            padding: 8px 20px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .btn-custom:hover {
            transform: translateY(-2px);
        }
        .ranking-badge {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: bold;
            font-size: 14px;
        }
        .score-highlight {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            padding: 8px 16px;
            border-radius: 10px;
            font-weight: bold;
        }
        .weight-card {
            background: linear-gradient(135deg, #17a2b8 0%, #6f42c1 100%);
            color: white;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand fw-bold" href="../views/admin/job_list.php">
                <i class="fas fa-arrow-left me-2"></i>Kembali ke Daftar Lowongan
            </a>
            <div class="navbar-nav ms-auto">
                <span class="navbar-text me-3">
                    <i class="fas fa-user me-1"></i>
                    <?= htmlspecialchars($_SESSION['user']['name']) ?>
                </span>
                <a href="LogoutController.php" class="btn btn-outline-light btn-sm">
                    <i class="fas fa-sign-out-alt me-1"></i>Logout
                </a>
            </div>
        </div>
    </nav>

    <div class="container-fluid py-4">
        <div class="container">
            <!-- Header -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body text-center py-4">
                            <h2 class="card-title fw-bold text-primary mb-2">
                                <i class="fas fa-chart-line me-2"></i>Hasil Seleksi SAW
                            </h2>
                            <p class="card-text text-muted">
                                Lowongan: <strong><?= htmlspecialchars($job['title']) ?></strong>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Weight Configuration -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card weight-card">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-cogs me-2"></i>Konfigurasi Bobot Kriteria
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row text-center">
                                <div class="col-md-6">
                                    <div class="mb-2">
                                        <i class="fas fa-clock fa-2x mb-2"></i>
                                        <h4 class="fw-bold"><?= ($weight_experience * 100) ?>%</h4>
                                        <p class="mb-0">Bobot Pengalaman</p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-2">
                                        <i class="fas fa-graduation-cap fa-2x mb-2"></i>
                                        <h4 class="fw-bold"><?= ($weight_education * 100) ?>%</h4>
                                        <p class="mb-0">Bobot Pendidikan</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Results Table -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header bg-success text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-trophy me-2"></i>Ranking Hasil Seleksi SAW
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="text-center"><i class="fas fa-medal me-1"></i>Ranking</th>
                                            <th><i class="fas fa-user me-1"></i>Nama</th>
                                            <th><i class="fas fa-clock me-1"></i>Pengalaman</th>
                                            <th><i class="fas fa-graduation-cap me-1"></i>Pendidikan</th>
                                            <th><i class="fas fa-chart-bar me-1"></i>Skor Normalisasi</th>
                                            <th><i class="fas fa-star me-1"></i>Skor Akhir</th>
                                            <th class="text-center"><i class="fas fa-crown me-1"></i>Aksi</th>
    </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($applicants as $index => $a): ?>
                                        <tr>
                                            <td class="text-center">
                                                <?php if ($index === 0): ?>
                                                    <span class="ranking-badge">
                                                        <i class="fas fa-trophy me-1"></i>1st
                                                    </span>
                                                <?php elseif ($index === 1): ?>
                                                    <span class="ranking-badge" style="background: linear-gradient(135deg, #6c757d 0%, #495057 100%);">
                                                        <i class="fas fa-medal me-1"></i>2nd
                                                    </span>
                                                <?php elseif ($index === 2): ?>
                                                    <span class="ranking-badge" style="background: linear-gradient(135deg, #fd7e14 0%, #e55a00 100%);">
                                                        <i class="fas fa-award me-1"></i>3rd
                                                    </span>
                                                <?php else: ?>
                                                    <span class="badge bg-secondary"><?= $index + 1 ?></span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <i class="fas fa-user-circle me-2 text-primary"></i>
                                                <strong><?= htmlspecialchars($a['name']) ?></strong>
                                            </td>
                                            <td>
                                                <span class="badge bg-info">
                                                    <i class="fas fa-clock me-1"></i>
                                                    <?= $a['experience'] ?> tahun
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge bg-secondary">
                                                    <i class="fas fa-graduation-cap me-1"></i>
                                                    <?= $a['education'] ?>
                                                </span>
                                            </td>
                                            <td>
                                                <small class="text-muted">
                                                    <div>Exp: <?= round(($max_exp > 0) ? ($a['experience'] / $max_exp) : 0, 4) ?></div>
                                                    <div>Edu: <?= round(($max_edu > 0) ? ($a['education'] / $max_edu) : 0, 4) ?></div>
                                                </small>
                                            </td>
                                            <td>
                                                <span class="score-highlight">
                                                    <?= round($a['score'], 4) ?>
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <form action="SetWinnerController.php" method="POST" class="d-inline">
                <input type="hidden" name="application_id" value="<?= $a['id'] ?>">
                                                    <input type="hidden" name="job_id" value="<?= $job_id ?>">
                                                    <button type="submit" class="btn btn-success btn-sm btn-custom">
                                                        <i class="fas fa-crown me-1"></i>Pilih Pemenang
                                                    </button>
            </form>
        </td>
    </tr>
    <?php endforeach; ?>
                                    </tbody>
</table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="d-flex flex-wrap gap-2 justify-content-center">
                        <a href="../views/admin/job_list.php" class="btn btn-secondary btn-custom">
                            <i class="fas fa-arrow-left me-2"></i>Kembali ke Daftar Lowongan
                        </a>
                        <a href="../views/admin/applicants_per_job.php?job_id=<?= $job_id ?>" class="btn btn-info btn-custom">
                            <i class="fas fa-users me-2"></i>Lihat Detail Pelamar
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="../assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>
