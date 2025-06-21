<?php
session_start();
require_once '../../config/db.php';

// Cek role dan status approval
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'applicant') {
    header('Location: ../../index.php');
    exit();
}

$user_id = $_SESSION['user']['id'];

$stmt = $conn->prepare("SELECT is_approved FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($is_approved);
$stmt->fetch();
$stmt->close();

if (!$is_approved) {
    ?>
    <!DOCTYPE html>
    <html lang="id">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Akun Belum Diapprove - SAW Applicant System</title>
        <link href="../../assets/css/bootstrap.min.css" rel="stylesheet">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
        <style>
            body {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                min-height: 100vh;
                font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            }
            .navbar {
                background: rgba(255, 255, 255, 0.1);
                backdrop-filter: blur(10px);
                border-bottom: 1px solid rgba(255, 255, 255, 0.2);
            }
            .approval-card {
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
        <!-- Navbar -->
        <nav class="navbar navbar-expand-lg navbar-dark">
            <div class="container">
                <a class="navbar-brand fw-bold" href="#">
                    <i class="fas fa-user-tie me-2"></i>Dashboard Pelamar
                </a>
                <div class="navbar-nav ms-auto">
                    <a href="../../controllers/LogoutController.php" class="btn btn-outline-light btn-sm">
                        <i class="fas fa-sign-out-alt me-1"></i>Logout
                    </a>
                </div>
            </div>
        </nav>

        <div class="container-fluid min-vh-100 d-flex align-items-center justify-content-center">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-6 col-md-8">
                        <div class="approval-card p-5 text-center">
                            <div class="mb-4">
                                <i class="fas fa-clock text-warning" style="font-size: 4rem;"></i>
                            </div>
                            
                            <h2 class="fw-bold text-primary mb-3">
                                Akun Belum Diapprove
                            </h2>
                            
                            <p class="lead text-muted mb-4">
                                Akun Anda belum di-approve oleh Admin. Silakan tunggu konfirmasi dari administrator.
                            </p>

                            <div class="alert alert-info" role="alert">
                                <i class="fas fa-info-circle me-2"></i>
                                <strong>Info:</strong> Proses approval biasanya memakan waktu 1-2 hari kerja.
                            </div>

                            <div class="d-grid gap-2 d-md-flex justify-content-md-center">
                                <a href="../../controllers/LogoutController.php" class="btn btn-secondary btn-custom">
                                    <i class="fas fa-sign-out-alt me-2"></i>Logout
                                </a>
                                <a href="../../index.php" class="btn btn-primary btn-custom">
                                    <i class="fas fa-home me-2"></i>Kembali ke Beranda
                                </a>
                            </div>
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

$jobs = $conn->query("SELECT * FROM jobs");

// Ambil lamaran yang diterima
$stmt = $conn->prepare("
    SELECT a.*, j.title as job_title, j.description as job_description
    FROM applications a
    JOIN jobs j ON a.job_id = j.id
    WHERE a.user_id = ? AND a.status = 'accepted'
    ORDER BY a.updated_at DESC
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$accepted_applications = $stmt->get_result();
$stmt->close();

// Ambil statistik lamaran
$stmt = $conn->prepare("
    SELECT 
        COUNT(*) as total_applications,
        SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
        SUM(CASE WHEN status = 'accepted' THEN 1 ELSE 0 END) as accepted,
        SUM(CASE WHEN status = 'rejected' THEN 1 ELSE 0 END) as rejected
    FROM applications 
    WHERE user_id = ?
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stats = $stmt->get_result()->fetch_assoc();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Pelamar - SAW Applicant System</title>
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
        .stat-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        .stat-card.success {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        }
        .stat-card.warning {
            background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%);
        }
        .stat-card.danger {
            background: linear-gradient(135deg, #dc3545 0%, #e83e8c 100%);
        }
        .accepted-card {
            background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
            border-left: 5px solid #28a745;
        }
        .job-card {
            transition: all 0.3s ease;
        }
        .job-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
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
            <a class="navbar-brand fw-bold" href="#">
                <i class="fas fa-user-tie me-2"></i>Dashboard Pelamar
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
            <!-- Welcome Section -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body text-center py-4">
                            <h2 class="card-title fw-bold text-primary mb-2">
                                <i class="fas fa-user-tie me-2"></i>Dashboard Pelamar
                            </h2>
                            <p class="card-text text-muted">
                                Selamat datang, <?= htmlspecialchars($_SESSION['user']['name']) ?>! 
                                Temukan lowongan yang sesuai dengan kemampuan Anda.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="row mb-4">
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="card stat-card">
                        <div class="card-body text-center">
                            <i class="fas fa-file-alt fa-2x mb-2"></i>
                            <h4 class="fw-bold"><?= $stats['total_applications'] ?></h4>
                            <p class="mb-0">Total Lamaran</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="card stat-card warning">
                        <div class="card-body text-center">
                            <i class="fas fa-clock fa-2x mb-2"></i>
                            <h4 class="fw-bold"><?= $stats['pending'] ?></h4>
                            <p class="mb-0">Menunggu Review</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="card stat-card success">
                        <div class="card-body text-center">
                            <i class="fas fa-check-circle fa-2x mb-2"></i>
                            <h4 class="fw-bold"><?= $stats['accepted'] ?></h4>
                            <p class="mb-0">Diterima</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="card stat-card danger">
                        <div class="card-body text-center">
                            <i class="fas fa-times-circle fa-2x mb-2"></i>
                            <h4 class="fw-bold"><?= $stats['rejected'] ?></h4>
                            <p class="mb-0">Ditolak</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Lamaran yang Diterima -->
            <?php if ($accepted_applications->num_rows > 0): ?>
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card accepted-card">
                            <div class="card-header bg-success text-white">
                                <h5 class="mb-0">
                                    <i class="fas fa-trophy me-2"></i>🎉 Lamaran yang Diterima
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <?php while ($application = $accepted_applications->fetch_assoc()): ?>
                                        <div class="col-lg-6 mb-3">
                                            <div class="card job-card h-100">
                                                <div class="card-body">
                                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                                        <div>
                                                            <h6 class="card-title fw-bold text-success mb-1">
                                                                <i class="fas fa-briefcase me-2"></i>
                                                                <?= htmlspecialchars($application['job_title']) ?>
                                                            </h6>
                                                            <p class="card-text text-muted small mb-2">
                                                                <?= htmlspecialchars($application['job_description']) ?>
                                                            </p>
                                                            <p class="text-muted small mb-0">
                                                                <i class="fas fa-calendar-check me-1"></i>
                                                                Diterima: <?= date('d/m/Y H:i', strtotime($application['updated_at'])) ?>
                                                            </p>
                                                        </div>
                                                        <span class="badge bg-success">
                                                            <i class="fas fa-check me-1"></i>Diterima
                                                        </span>
                                                    </div>
                                                    <div class="d-grid">
                                                        <a href="training_slideshow.php?job_id=<?= $application['job_id'] ?>" 
                                                           class="btn btn-success btn-custom">
                                                            <i class="fas fa-graduation-cap me-2"></i>📚 Mulai Training
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endwhile; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Daftar Lowongan -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-search me-2"></i>Daftar Lowongan Tersedia
                            </h5>
                        </div>
                        <div class="card-body">
                            <?php if ($jobs->num_rows > 0): ?>
                                <div class="row">
    <?php while ($job = $jobs->fetch_assoc()): ?>
                                        <div class="col-lg-6 col-md-12 mb-3">
                                            <div class="card job-card h-100">
                                                <div class="card-body">
                                                    <h6 class="card-title fw-bold text-primary mb-2">
                                                        <i class="fas fa-briefcase me-2"></i>
                                                        <?= htmlspecialchars($job['title']) ?>
                                                    </h6>
                                                    <p class="card-text text-muted mb-3">
                                                        <?= htmlspecialchars($job['description']) ?>
                                                    </p>
                                                    <div class="d-grid">
                                                        <a href="apply.php?job_id=<?= $job['id'] ?>" 
                                                           class="btn btn-primary btn-custom">
                                                            <i class="fas fa-paper-plane me-2"></i>Lamar Sekarang
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
    <?php endwhile; ?>
                                </div>
                            <?php else: ?>
                                <div class="text-center py-4">
                                    <i class="fas fa-search text-muted" style="font-size: 3rem;"></i>
                                    <h5 class="mt-3 text-muted">Tidak ada lowongan tersedia</h5>
                                    <p class="text-muted">Silakan cek kembali nanti</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="../../assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>
