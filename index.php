<?php
session_start();

if (isset($_SESSION['user'])) {
    $role = $_SESSION['user']['role'];
    header("Location: views/$role/dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SAW Applicant System - Portal Pelamaran Kerja</title>
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .hero-section {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        .hero-title {
            background: linear-gradient(135deg, #667eea, #764ba2);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            font-weight: 700;
        }
        .btn-custom {
            border-radius: 50px;
            padding: 12px 30px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }
        .btn-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }
        .feature-icon {
            font-size: 3rem;
            margin-bottom: 1rem;
            background: linear-gradient(135deg, #667eea, #764ba2);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
    </style>
</head>
<body>
    <div class="container-fluid min-vh-100 d-flex align-items-center justify-content-center">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8 col-md-10">
                    <div class="hero-section p-5 text-center">
                        <div class="mb-4">
                            <i class="fas fa-briefcase feature-icon"></i>
                        </div>
                        
                        <h1 class="hero-title display-4 fw-bold mb-4">
                            SAW Applicant System
                        </h1>
                        
                        <p class="lead text-muted mb-5">
                            Portal pelamaran kerja modern dengan sistem seleksi SAW (Simple Additive Weighting) 
                            untuk menemukan kandidat terbaik
                        </p>

                        <?php if (isset($_GET['logout']) && $_GET['logout'] === 'success'): ?>
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="fas fa-check-circle me-2"></i>
                                <strong>Berhasil!</strong> Anda telah berhasil logout dari sistem.
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <div class="row g-4 mb-5">
                            <div class="col-md-4">
                                <div class="text-center">
                                    <i class="fas fa-search feature-icon"></i>
                                    <h5 class="fw-bold">Cari Lowongan</h5>
                                    <p class="text-muted small">Temukan lowongan yang sesuai dengan kemampuan Anda</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="text-center">
                                    <i class="fas fa-chart-line feature-icon"></i>
                                    <h5 class="fw-bold">Seleksi SAW</h5>
                                    <p class="text-muted small">Sistem seleksi otomatis menggunakan metode SAW</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="text-center">
                                    <i class="fas fa-graduation-cap feature-icon"></i>
                                    <h5 class="fw-bold">Training Material</h5>
                                    <p class="text-muted small">Akses materi training setelah diterima</p>
                                </div>
                            </div>
                        </div>

                        <div class="d-grid gap-3 d-md-flex justify-content-md-center">
                            <a href="views/auth/login.php" class="btn btn-primary btn-custom">
                                <i class="fas fa-sign-in-alt me-2"></i>Login
                            </a>
                            <a href="views/auth/register.php" class="btn btn-outline-primary btn-custom">
                                <i class="fas fa-user-plus me-2"></i>Register
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>
