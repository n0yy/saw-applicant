<?php
session_start();
require_once '../../config/db.php';

if ($_SESSION['user']['role'] !== 'admin') {
    header('Location: ../../index.php');
    exit();
}

$result = $conn->query("SELECT * FROM users WHERE is_approved = 0 AND role = 'applicant'");

$total_applicants = $conn->query("SELECT COUNT(*) as count FROM users WHERE role = 'applicant'")->fetch_assoc()['count'];
$pending_approvals = $conn->query("SELECT COUNT(*) as count FROM users WHERE is_approved = 0 AND role = 'applicant'")->fetch_assoc()['count'];
$total_jobs = $conn->query("SELECT COUNT(*) as count FROM jobs")->fetch_assoc()['count'];
$total_applications = $conn->query("SELECT COUNT(*) as count FROM applications")->fetch_assoc()['count'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - SAW Applicant System</title>
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
        .stat-card.info {
            background: linear-gradient(135deg, #17a2b8 0%, #6f42c1 100%);
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
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand fw-bold" href="#">
                <i class="fas fa-shield-alt me-2"></i>Admin Dashboard
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
                                <i class="fas fa-tachometer-alt me-2"></i>Dashboard Admin
                            </h2>
                            <p class="card-text text-muted">
                                Selamat datang di panel administrasi SAW Applicant System
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
                            <i class="fas fa-users fa-2x mb-2"></i>
                            <h4 class="fw-bold"><?= $total_applicants ?></h4>
                            <p class="mb-0">Total Pelamar</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="card stat-card warning">
                        <div class="card-body text-center">
                            <i class="fas fa-clock fa-2x mb-2"></i>
                            <h4 class="fw-bold"><?= $pending_approvals ?></h4>
                            <p class="mb-0">Menunggu Approval</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="card stat-card info">
                        <div class="card-body text-center">
                            <i class="fas fa-briefcase fa-2x mb-2"></i>
                            <h4 class="fw-bold"><?= $total_jobs ?></h4>
                            <p class="mb-0">Total Lowongan</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="card stat-card success">
                        <div class="card-body text-center">
                            <i class="fas fa-file-alt fa-2x mb-2"></i>
                            <h4 class="fw-bold"><?= $total_applications ?></h4>
                            <p class="mb-0">Total Lamaran</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-bolt me-2"></i>Aksi Cepat
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="d-flex flex-wrap gap-2">
                                <a href="job_list.php" class="btn btn-primary btn-custom">
                                    <i class="fas fa-briefcase me-2"></i>Kelola Lowongan
                                </a>
                                <a href="applicants_per_job.php" class="btn btn-info btn-custom">
                                    <i class="fas fa-users me-2"></i>Lihat Pelamar
                                </a>
                                <a href="training_view.php" class="btn btn-success btn-custom">
                                    <i class="fas fa-graduation-cap me-2"></i>Training Material
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pending Approvals -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header bg-warning text-dark">
                            <h5 class="mb-0">
                                <i class="fas fa-clock me-2"></i>Pelamar Menunggu Approval
                            </h5>
                        </div>
                        <div class="card-body">
                            <?php if ($result->num_rows > 0): ?>
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead class="table-light">
                                            <tr>
                                                <th><i class="fas fa-user me-1"></i>Nama</th>
                                                <th><i class="fas fa-envelope me-1"></i>Email</th>
                                                <th><i class="fas fa-calendar me-1"></i>Tanggal Daftar</th>
                                                <th class="text-center"><i class="fas fa-cogs me-1"></i>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
    <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
                                                    <td>
                                                        <i class="fas fa-user-circle me-2 text-primary"></i>
                                                        <?= htmlspecialchars($row['name']) ?>
                                                    </td>
                                                    <td>
                                                        <i class="fas fa-envelope me-2 text-muted"></i>
                                                        <?= htmlspecialchars($row['email']) ?>
                                                    </td>
                                                    <td>
                                                        <i class="fas fa-calendar me-2 text-muted"></i>
                                                        <?= date('d/m/Y H:i', strtotime($row['created_at'])) ?>
                                                    </td>
                                                    <td class="text-center">
                                                        <form action="../../controllers/AdminController.php" method="POST" class="d-inline">
                    <input type="hidden" name="action" value="approve_user">
                    <input type="hidden" name="user_id" value="<?= $row['id'] ?>">
                                                            <button type="submit" class="btn btn-success btn-sm btn-custom">
                                                                <i class="fas fa-check me-1"></i>Approve
                                                            </button>
                </form>
            </td>
        </tr>
    <?php endwhile; ?>
                                        </tbody>
</table>
                                </div>
                            <?php else: ?>
                                <div class="text-center py-4">
                                    <i class="fas fa-check-circle text-success" style="font-size: 3rem;"></i>
                                    <h5 class="mt-3 text-muted">Tidak ada pelamar yang menunggu approval</h5>
                                    <p class="text-muted">Semua pelamar telah diapprove</p>
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
