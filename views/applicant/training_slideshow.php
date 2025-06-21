<?php
session_start();
require_once '../../config/db.php';

// Cek role dan status approval
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'applicant') {
    header('Location: ../../index.php');
    exit();
}

$user_id = $_SESSION['user']['id'];
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
                            <a href="dashboard.php" class="btn btn-primary btn-custom">
                                <i class="fas fa-arrow-left me-2"></i>Kembali ke Dashboard
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

// Validasi bahwa user ini benar-benar diterima untuk lowongan ini
$stmt = $conn->prepare("
    SELECT a.*, j.title as job_title 
    FROM applications a 
    JOIN jobs j ON a.job_id = j.id 
    WHERE a.user_id = ? AND a.job_id = ? AND a.status = 'accepted'
");
$stmt->bind_param("ii", $user_id, $job_id);
$stmt->execute();
$application = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$application) {
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
                                <i class="fas fa-lock text-warning" style="font-size: 4rem;"></i>
                            </div>
                            <h2 class="fw-bold text-warning mb-3">Akses Ditolak</h2>
                            <p class="lead text-muted mb-4">Anda tidak memiliki akses ke training ini. Pastikan Anda telah diterima untuk lowongan ini.</p>
                            <a href="dashboard.php" class="btn btn-primary btn-custom">
                                <i class="fas fa-arrow-left me-2"></i>Kembali ke Dashboard
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

// Konversi ke array untuk navigasi
$images_array = [];
while ($image = $training_images->fetch_assoc()) {
    $images_array[] = $image;
}

$total_images = count($images_array);
$current_slide = isset($_GET['slide']) ? (int)$_GET['slide'] : 1;

// Validasi slide number
if ($current_slide < 1) $current_slide = 1;
if ($current_slide > $total_images) $current_slide = $total_images;

$current_image = $images_array[$current_slide - 1] ?? null;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Training Material - <?= htmlspecialchars($application['job_title']) ?></title>
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
        .slide-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            overflow: hidden;
        }
        .slide-image {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            min-height: 400px;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }
        .slide-image img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }
        .slide-content {
            padding: 30px;
        }
        .slide-title {
            font-size: 24px;
            font-weight: bold;
            color: #333;
            margin-bottom: 15px;
        }
        .slide-description {
            font-size: 16px;
            line-height: 1.6;
            color: #666;
            margin-bottom: 20px;
        }
        .slide-counter {
            text-align: center;
            font-size: 14px;
            color: #888;
            margin-bottom: 20px;
        }
        .navigation {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-top: 1px solid #dee2e6;
            padding: 20px 30px;
        }
        .nav-btn {
            border-radius: 50px;
            padding: 12px 30px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        .nav-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }
        .nav-btn:disabled {
            background-color: #6c757d !important;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }
        .progress-bar {
            height: 8px;
            background-color: #e9ecef;
            border-radius: 10px;
            overflow: hidden;
            margin: 20px 0;
        }
        .progress-fill {
            height: 100%;
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            transition: width 0.5s ease;
            border-radius: 10px;
        }
        .job-info {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        .job-title {
            font-size: 20px;
            font-weight: bold;
            color: #28a745;
            margin-bottom: 5px;
        }
        .completion-message {
            background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
            border-radius: 15px;
            padding: 40px;
            text-align: center;
            margin: 20px 0;
            border: 1px solid #c3e6cb;
        }
        .slide-number {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: bold;
            font-size: 14px;
        }
        .btn-custom {
            border-radius: 10px;
            padding: 10px 20px;
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
            <a class="navbar-brand fw-bold" href="dashboard.php">
                <i class="fas fa-arrow-left me-2"></i>Kembali ke Dashboard
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
            <!-- Job Information -->
            <div class="job-info">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <div class="job-title">
                            <i class="fas fa-graduation-cap me-2"></i>
                            <?= htmlspecialchars($application['job_title']) ?>
                        </div>
                        <p class="mb-0 text-muted">
                            <i class="fas fa-calendar-check me-1"></i>
                            Status: Diterima pada <?= date('d/m/Y H:i', strtotime($application['updated_at'])) ?>
                        </p>
                    </div>
                    <div class="col-md-4 text-md-end">
                        <span class="slide-number">
                            <i class="fas fa-book me-1"></i>
                            Training Material
                        </span>
                    </div>
                </div>
            </div>

            <?php if ($total_images > 0): ?>
                <div class="slide-container">
                    <div class="slide-image">
                        <img src="../../<?= htmlspecialchars($current_image['image_path']) ?>" 
                             alt="Training Material <?= $current_slide ?>" 
                             class="img-fluid">
                    </div>
                    
                    <div class="slide-content">
                        <div class="slide-counter">
                            <i class="fas fa-layer-group me-1"></i>
                            Slide <?= $current_slide ?> dari <?= $total_images ?>
                        </div>
                        
                        <div class="progress-bar">
                            <div class="progress-fill" style="width: <?= ($current_slide / $total_images) * 100 ?>%"></div>
                        </div>
                        
                        <div class="slide-title">
                            <i class="fas fa-lightbulb me-2 text-warning"></i>
                            Materi Training #<?= $current_slide ?>
                        </div>
                        
                        <div class="slide-description">
                            <?php if (!empty($current_image['description'])): ?>
                                <?= nl2br(htmlspecialchars($current_image['description'])) ?>
                            <?php else: ?>
                                <em class="text-muted">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Tidak ada deskripsi untuk materi ini.
                                </em>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="navigation">
                        <div class="row align-items-center">
                            <div class="col-md-4">
                                <?php if ($current_slide > 1): ?>
                                    <a href="?job_id=<?= $job_id ?>&slide=<?= $current_slide - 1 ?>" 
                                       class="nav-btn btn btn-outline-primary">
                                        <i class="fas fa-chevron-left"></i>
                                        Sebelumnya
                                    </a>
                                <?php else: ?>
                                    <button class="nav-btn btn btn-outline-secondary" disabled>
                                        <i class="fas fa-chevron-left"></i>
                                        Sebelumnya
                                    </button>
                                <?php endif; ?>
                            </div>
                            
                            <div class="col-md-4 text-center">
                                <div class="slide-number">
                                    <i class="fas fa-image me-1"></i>
                                    <?= $current_slide ?> / <?= $total_images ?>
                                </div>
                            </div>
                            
                            <div class="col-md-4 text-end">
                                <?php if ($current_slide < $total_images): ?>
                                    <a href="?job_id=<?= $job_id ?>&slide=<?= $current_slide + 1 ?>" 
                                       class="nav-btn btn btn-primary">
                                        Selanjutnya
                                        <i class="fas fa-chevron-right"></i>
                                    </a>
                                <?php else: ?>
                                    <button class="nav-btn btn btn-secondary" disabled>
                                        Selanjutnya
                                        <i class="fas fa-chevron-right"></i>
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                
                <?php if ($current_slide == $total_images): ?>
                    <div class="completion-message">
                        <i class="fas fa-trophy fa-3x text-success mb-3"></i>
                        <h3 class="text-success fw-bold">🎉 Selamat!</h3>
                        <p class="lead mb-2">Anda telah menyelesaikan semua materi training untuk lowongan ini.</p>
                        <p class="text-muted">Silakan hubungi admin untuk informasi selanjutnya.</p>
                    </div>
                <?php endif; ?>
                
            <?php else: ?>
                <div class="slide-container">
                    <div class="slide-content text-center py-5">
                        <i class="fas fa-images fa-4x text-muted mb-4"></i>
                        <h3 class="text-muted">📷 Tidak ada materi training</h3>
                        <p class="text-muted">Materi training belum tersedia untuk lowongan ini.</p>
                        <p class="text-muted small">Silakan hubungi admin untuk informasi lebih lanjut.</p>
                    </div>
                </div>
            <?php endif; ?>
            
            <div class="text-center mt-4">
                <div class="d-flex flex-wrap gap-2 justify-content-center">
                    <a href="dashboard.php" class="btn btn-secondary btn-custom">
                        <i class="fas fa-home me-2"></i>Kembali ke Dashboard
                    </a>
                    <?php if ($current_image): ?>
                        <a href="../../<?= htmlspecialchars($current_image['image_path']) ?>" 
                           target="_blank" 
                           class="btn btn-info btn-custom">
                            <i class="fas fa-external-link-alt me-2"></i>Lihat Gambar Lengkap
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script src="../../assets/js/bootstrap.bundle.min.js"></script>
    <script>
        // Keyboard navigation
        document.addEventListener('keydown', function(e) {
            const currentSlide = <?= $current_slide ?>;
            const totalImages = <?= $total_images ?>;
            
            if (e.key === 'ArrowLeft' && currentSlide > 1) {
                window.location.href = '?job_id=<?= $job_id ?>&slide=' + (currentSlide - 1);
            } else if (e.key === 'ArrowRight' && currentSlide < totalImages) {
                window.location.href = '?job_id=<?= $job_id ?>&slide=' + (currentSlide + 1);
            }
        });
        
        // Auto-save progress
        localStorage.setItem('training_progress_<?= $job_id ?>', <?= $current_slide ?>);
        
        // Show keyboard hint
        if (<?= $total_images ?> > 1) {
            console.log('💡 Tip: Gunakan tombol Arrow Left/Right untuk navigasi keyboard');
        }
    </script>
</body>
</html> 