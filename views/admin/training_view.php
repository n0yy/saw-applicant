<?php
session_start();
require_once '../../config/db.php';

if ($_SESSION['user']['role'] !== 'admin') {
    header('Location: ../../index.php');
    exit();
}

$job_id = $_GET['job_id'] ?? 0;
$winner_id = $_GET['winner_id'] ?? 0;

if (!$job_id || !$winner_id) {
    ?>
    <!DOCTYPE html>
    <html lang="id">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Error - SAW Applicant System</title>
        <link href="../../assets/css/bootstrap.min.css" rel="stylesheet">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
        <style>
            body {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                min-height: 100vh;
                font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            }
            .error-card {
                background: rgba(255, 255, 255, 0.95);
                backdrop-filter: blur(10px);
                border-radius: 20px;
                box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
                border: 1px solid rgba(255, 255, 255, 0.2);
            }
            .btn-custom {
                border-radius: 10px;
                padding: 12px 30px;
                font-weight: 600;
                transition: all 0.3s ease;
            }
            .btn-custom:hover {
                transform: translateY(-2px);
            }
        </style>
    </head>
    <body>
        <div class="container-fluid min-vh-100 d-flex align-items-center justify-content-center">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-6 col-md-8">
                        <div class="error-card p-5 text-center">
                            <div class="mb-4">
                                <i class="fas fa-exclamation-triangle text-danger" style="font-size: 4rem;"></i>
                            </div>
                            <h2 class="fw-bold text-danger mb-3">Parameter tidak lengkap</h2>
                            <p class="lead text-muted mb-4">Job ID atau Winner ID tidak ditemukan dalam parameter URL.</p>
                            <a href="job_list.php" class="btn btn-primary btn-custom">
                                <i class="fas fa-arrow-left me-2"></i>Kembali ke Daftar Lowongan
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script src="../../assets/js/bootstrap.bundle.min.js"></script>
    </body>
    </html>
    <?php
    exit();
}

// Ambil data lowongan
$stmt = $conn->prepare("SELECT title FROM jobs WHERE id = ?");
$stmt->bind_param("i", $job_id);
$stmt->execute();
$job = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$job) {
    ?>
    <!DOCTYPE html>
    <html lang="id">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Error - SAW Applicant System</title>
        <link href="../../assets/css/bootstrap.min.css" rel="stylesheet">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
        <style>
            body {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                min-height: 100vh;
                font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            }
            .error-card {
                background: rgba(255, 255, 255, 0.95);
                backdrop-filter: blur(10px);
                border-radius: 20px;
                box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
                border: 1px solid rgba(255, 255, 255, 0.2);
            }
            .btn-custom {
                border-radius: 10px;
                padding: 12px 30px;
                font-weight: 600;
                transition: all 0.3s ease;
            }
            .btn-custom:hover {
                transform: translateY(-2px);
            }
        </style>
    </head>
    <body>
        <div class="container-fluid min-vh-100 d-flex align-items-center justify-content-center">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-6 col-md-8">
                        <div class="error-card p-5 text-center">
                            <div class="mb-4">
                                <i class="fas fa-search text-warning" style="font-size: 4rem;"></i>
                            </div>
                            <h2 class="fw-bold text-warning mb-3">Lowongan tidak ditemukan</h2>
                            <p class="lead text-muted mb-4">Lowongan yang Anda cari tidak tersedia atau telah dihapus.</p>
                            <a href="job_list.php" class="btn btn-primary btn-custom">
                                <i class="fas fa-arrow-left me-2"></i>Kembali ke Daftar Lowongan
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script src="../../assets/js/bootstrap.bundle.min.js"></script>
    </body>
    </html>
    <?php
    exit();
}

// Ambil data pemenang
$stmt = $conn->prepare("
    SELECT a.*, u.name, u.email 
    FROM applications a 
    JOIN users u ON a.user_id = u.id 
    WHERE a.id = ? AND a.job_id = ?
");
$stmt->bind_param("ii", $winner_id, $job_id);
$stmt->execute();
$winner = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$winner) {
    ?>
    <!DOCTYPE html>
    <html lang="id">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Error - SAW Applicant System</title>
        <link href="../../assets/css/bootstrap.min.css" rel="stylesheet">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
        <style>
            body {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                min-height: 100vh;
                font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            }
            .error-card {
                background: rgba(255, 255, 255, 0.95);
                backdrop-filter: blur(10px);
                border-radius: 20px;
                box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
                border: 1px solid rgba(255, 255, 255, 0.2);
            }
            .btn-custom {
                border-radius: 10px;
                padding: 12px 30px;
                font-weight: 600;
                transition: all 0.3s ease;
            }
            .btn-custom:hover {
                transform: translateY(-2px);
            }
        </style>
    </head>
    <body>
        <div class="container-fluid min-vh-100 d-flex align-items-center justify-content-center">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-6 col-md-8">
                        <div class="error-card p-5 text-center">
                            <div class="mb-4">
                                <i class="fas fa-user-times text-danger" style="font-size: 4rem;"></i>
                            </div>
                            <h2 class="fw-bold text-danger mb-3">Data pemenang tidak ditemukan</h2>
                            <p class="lead text-muted mb-4">Data pemenang untuk lowongan ini tidak tersedia atau tidak valid.</p>
                            <a href="job_list.php" class="btn btn-primary btn-custom">
                                <i class="fas fa-arrow-left me-2"></i>Kembali ke Daftar Lowongan
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script src="../../assets/js/bootstrap.bundle.min.js"></script>
    </body>
    </html>
    <?php
    exit();
}

// Ambil gambar training
$stmt = $conn->prepare("SELECT * FROM training WHERE job_id = ? ORDER BY created_at ASC");
$stmt->bind_param("i", $job_id);
$stmt->execute();
$training_images = $stmt->get_result();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Training Material - SAW Applicant System</title>
    <link href="../../assets/css/bootstrap.min.css" rel="stylesheet">
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
            transition: transform 0.3s ease;
        }
        .card:hover {
            transform: translateY(-5px);
        }
        .winner-card {
            background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
            border-left: 5px solid #28a745;
        }
        .training-card {
            transition: all 0.3s ease;
        }
        .training-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        }
        .training-image {
            height: 250px;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            border-radius: 10px 10px 0 0;
        }
        .training-image img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
            border-radius: 8px;
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
        .winner-badge {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: bold;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand fw-bold" href="job_list.php">
                <i class="fas fa-arrow-left me-2"></i>Kembali ke Daftar Lowongan
            </a>
            <div class="navbar-nav ms-auto">
                <a href="../../controllers/LogoutController.php" class="btn btn-outline-light btn-sm">
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
                                <i class="fas fa-graduation-cap me-2"></i>Training Material
                            </h2>
                            <p class="card-text text-muted">
                                Lowongan: <strong><?= htmlspecialchars($job['title']) ?></strong>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Winner Information -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card winner-card">
                        <div class="card-header bg-success text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-trophy me-2"></i>🎉 Pemenang Seleksi SAW
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="d-flex align-items-center mb-3">
                                        <i class="fas fa-user-circle fa-2x text-success me-3"></i>
                                        <div>
                                            <h6 class="fw-bold mb-1"><?= htmlspecialchars($winner['name']) ?></h6>
                                            <p class="text-muted mb-0">
                                                <i class="fas fa-envelope me-1"></i>
                                                <?= htmlspecialchars($winner['email']) ?>
                                            </p>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-6">
                                            <div class="mb-2">
                                                <i class="fas fa-clock text-info me-2"></i>
                                                <strong>Pengalaman:</strong>
                                                <span class="badge bg-info ms-1"><?= $winner['experience_years'] ?> tahun</span>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="mb-2">
                                                <i class="fas fa-graduation-cap text-warning me-2"></i>
                                                <strong>Pendidikan:</strong>
                                                <span class="badge bg-secondary ms-1"><?= htmlspecialchars($winner['education_level']) ?></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <h6 class="fw-bold">
                                            <i class="fas fa-heart text-danger me-2"></i>Motivasi Melamar:
                                        </h6>
                                        <div class="bg-light p-3 rounded">
                                            <p class="mb-0 text-muted" style="font-style: italic;">
                                                "<?= htmlspecialchars($winner['motivation']) ?>"
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Training Materials -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-book me-2"></i>📚 Materi Training
                            </h5>
                        </div>
                        <div class="card-body">
                            <?php if ($training_images->num_rows > 0): ?>
                                <div class="row">
                                    <?php 
                                    $counter = 1;
                                    while ($image = $training_images->fetch_assoc()): 
                                    ?>
                                        <div class="col-lg-4 col-md-6 mb-4">
                                            <div class="card training-card h-100">
                                                <div class="training-image">
                                                    <img src="../../<?= htmlspecialchars($image['image_path']) ?>" 
                                                         alt="Training Material <?= $counter ?>">
                                                </div>
                                                <div class="card-body">
                                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                                        <span class="badge bg-primary">
                                                            <i class="fas fa-image me-1"></i>Materi #<?= $counter ?>
                                                        </span>
                                                        <small class="text-muted">
                                                            <i class="fas fa-calendar me-1"></i>
                                                            <?= date('d/m/Y', strtotime($image['created_at'])) ?>
                                                        </small>
                                                    </div>
                                                    
                                                    <?php if (!empty($image['description'])): ?>
                                                        <p class="card-text text-muted">
                                                            <?= htmlspecialchars($image['description']) ?>
                                                        </p>
                                                    <?php else: ?>
                                                        <p class="card-text text-muted fst-italic">
                                                            <i class="fas fa-info-circle me-1"></i>
                                                            Tidak ada deskripsi
                                                        </p>
                                                    <?php endif; ?>
                                                    
                                                    <div class="d-grid">
                                                        <a href="../../<?= htmlspecialchars($image['image_path']) ?>" 
                                                           target="_blank" 
                                                           class="btn btn-outline-primary btn-sm btn-custom">
                                                            <i class="fas fa-external-link-alt me-1"></i>Lihat Gambar Lengkap
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php 
                                    $counter++;
                                    endwhile; 
                                    ?>
                                </div>
                            <?php else: ?>
                                <div class="text-center py-5">
                                    <i class="fas fa-images text-muted" style="font-size: 4rem;"></i>
                                    <h4 class="mt-3 text-muted">📷 Tidak ada materi training</h4>
                                    <p class="text-muted">Tidak ada materi training yang tersedia untuk lowongan ini.</p>
                                    <p class="text-muted small">Silakan tambahkan gambar training saat membuat lowongan.</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="d-flex flex-wrap gap-2 justify-content-center">
                        <a href="job_list.php" class="btn btn-secondary btn-custom">
                            <i class="fas fa-arrow-left me-2"></i>Kembali ke Daftar Lowongan
                        </a>
                        <a href="../../controllers/SAWController.php?job_id=<?= $job_id ?>" 
                           class="btn btn-warning btn-custom">
                            <i class="fas fa-chart-line me-2"></i>Lihat Hasil SAW
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="../../assets/js/bootstrap.bundle.min.js"></script>
</body>
</html> 