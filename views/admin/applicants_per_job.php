<?php
session_start();
require_once '../../config/db.php';

if ($_SESSION['user']['role'] !== 'admin') {
    header('Location: ../../index.php');
    exit();
}

$job_id = $_GET['job_id'] ?? 0;
if (!$job_id) {
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
                            <h2 class="fw-bold text-danger mb-3">Job ID tidak ditemukan</h2>
                            <p class="lead text-muted mb-4">Parameter lowongan tidak valid atau tidak ditemukan.</p>
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

$job = $conn->query("SELECT title FROM jobs WHERE id = $job_id")->fetch_assoc();
$pelamar = $conn->query("
    SELECT a.*, u.name, u.email
    FROM applications a
    JOIN users u ON a.user_id = u.id
    WHERE a.job_id = $job_id AND a.status = 'submitted'
");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Pelamar - SAW Applicant System</title>
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
        .applicant-card {
            transition: all 0.3s ease;
            border-left: 4px solid #007bff;
        }
        .applicant-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        }
        .motivation-text {
            max-width: 300px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        .motivation-full {
            white-space: pre-wrap;
            word-wrap: break-word;
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
                                <i class="fas fa-users me-2"></i>Daftar Pelamar
                            </h2>
                            <p class="card-text text-muted">
                                Lowongan: <strong><?= htmlspecialchars($job['title']) ?></strong>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Applicants List -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header bg-info text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-user-tie me-2"></i>Pelamar yang Telah Submit
                            </h5>
                        </div>
                        <div class="card-body">
                            <?php if ($pelamar->num_rows > 0): ?>
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead class="table-light">
                                            <tr>
                                                <th><i class="fas fa-user me-1"></i>Nama</th>
                                                <th><i class="fas fa-envelope me-1"></i>Email</th>
                                                <th><i class="fas fa-clock me-1"></i>Pengalaman</th>
                                                <th><i class="fas fa-graduation-cap me-1"></i>Pendidikan</th>
                                                <th><i class="fas fa-heart me-1"></i>Motivasi</th>
                                                <th><i class="fas fa-file-pdf me-1"></i>CV</th>
                                                <th><i class="fas fa-calendar me-1"></i>Tanggal Lamar</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php while ($row = $pelamar->fetch_assoc()): ?>
                                            <tr class="applicant-card">
                                                <td>
                                                    <i class="fas fa-user-circle me-2 text-primary"></i>
                                                    <strong><?= htmlspecialchars($row['name']) ?></strong>
                                                </td>
                                                <td>
                                                    <i class="fas fa-envelope me-2 text-muted"></i>
                                                    <?= htmlspecialchars($row['email']) ?>
                                                </td>
                                                <td>
                                                    <span class="badge bg-info">
                                                        <i class="fas fa-clock me-1"></i>
                                                        <?= $row['experience_years'] ?> tahun
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="badge bg-secondary">
                                                        <i class="fas fa-graduation-cap me-1"></i>
                                                        <?= htmlspecialchars($row['education_level']) ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="motivation-text" 
                                                         data-bs-toggle="tooltip" 
                                                         data-bs-placement="top" 
                                                         title="<?= htmlspecialchars($row['motivation']) ?>">
                                                        <?= htmlspecialchars(substr($row['motivation'], 0, 50)) ?>
                                                        <?= strlen($row['motivation']) > 50 ? '...' : '' ?>
                                                    </div>
                                                </td>
                                                <td>
                                                    <?php if ($row['cv']): ?>
                                                        <a href="../../<?= $row['cv'] ?>" 
                                                           target="_blank" 
                                                           class="btn btn-primary btn-sm btn-custom">
                                                            <i class="fas fa-download me-1"></i>Download CV
                                                        </a>
                                                    <?php else: ?>
                                                        <span class="text-muted">
                                                            <i class="fas fa-times-circle me-1"></i>
                                                            Tidak ada CV
                                                        </span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <small class="text-muted">
                                                        <i class="fas fa-calendar me-1"></i>
                                                        <?= date('d/m/Y H:i', strtotime($row['created_at'])) ?>
                                                    </small>
                                                </td>
                                            </tr>
                                            <?php endwhile; ?>
                                        </tbody>
                                    </table>
                                </div>

                                <!-- Action Buttons -->
                                <div class="d-flex flex-wrap gap-2 justify-content-center mt-4">
                                    <a href="job_list.php" class="btn btn-secondary btn-custom">
                                        <i class="fas fa-arrow-left me-2"></i>Kembali ke Daftar Lowongan
                                    </a>
                                    <a href="../../controllers/SAWController.php?job_id=<?= $job_id ?>" 
                                       class="btn btn-warning btn-custom">
                                        <i class="fas fa-chart-line me-2"></i>Seleksi SAW
                                    </a>
                                </div>

                            <?php else: ?>
                                <div class="text-center py-5">
                                    <i class="fas fa-users text-muted" style="font-size: 4rem;"></i>
                                    <h4 class="mt-3 text-muted">Belum ada pelamar</h4>
                                    <p class="text-muted">Tidak ada pelamar yang telah submit aplikasi untuk lowongan ini.</p>
                                    <div class="mt-3">
                                        <a href="job_list.php" class="btn btn-primary btn-custom">
                                            <i class="fas fa-arrow-left me-2"></i>Kembali ke Daftar Lowongan
                                        </a>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Statistics Card -->
            <?php if ($pelamar->num_rows > 0): ?>
                <div class="row mt-4">
                    <div class="col-md-4">
                        <div class="card text-center">
                            <div class="card-body">
                                <i class="fas fa-users fa-2x text-primary mb-2"></i>
                                <h4 class="fw-bold"><?= $pelamar->num_rows ?></h4>
                                <p class="text-muted mb-0">Total Pelamar</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card text-center">
                            <div class="card-body">
                                <i class="fas fa-clock fa-2x text-warning mb-2"></i>
                                <h4 class="fw-bold">Menunggu</h4>
                                <p class="text-muted mb-0">Status Review</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card text-center">
                            <div class="card-body">
                                <i class="fas fa-chart-line fa-2x text-success mb-2"></i>
                                <h4 class="fw-bold">SAW Ready</h4>
                                <p class="text-muted mb-0">Siap Seleksi</p>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="../../assets/js/bootstrap.bundle.min.js"></script>
    <script>
        // Initialize tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });
    </script>
</body>
</html>
