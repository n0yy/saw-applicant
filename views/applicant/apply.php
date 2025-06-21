<?php
session_start();
require_once '../../config/db.php';

// Cek role dan status approval
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'applicant') {
    header('Location: ../../index.php');
    exit();
}

// Ambil job_id dari parameter URL
if (!isset($_GET['job_id']) || empty($_GET['job_id'])) {
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

$job_id = (int)$_GET['job_id'];

// Validasi job_id ada di database
$stmt = $conn->prepare("SELECT id, title, description, requirements FROM jobs WHERE id = ?");
$stmt->bind_param("i", $job_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
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

$job = $result->fetch_assoc();
$stmt->close();

// Cek apakah sudah pernah melamar
$user_id = $_SESSION['user']['id'];
$stmt = $conn->prepare("SELECT id FROM applications WHERE user_id = ? AND job_id = ?");
$stmt->bind_param("ii", $user_id, $job_id);
$stmt->execute();
$stmt->bind_result($application_id);
$stmt->fetch();
$stmt->close();

if ($application_id) {
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
                                <i class="fas fa-file-alt text-info" style="font-size: 4rem;"></i>
                            </div>
                            <h2 class="fw-bold text-info mb-3">Sudah Melamar</h2>
                            <p class="lead text-muted mb-4">Anda sudah melamar untuk lowongan ini sebelumnya.</p>
                            <div class="d-grid gap-2 d-md-flex justify-content-md-center">
                                <a href="dashboard.php" class="btn btn-primary btn-custom">
                                    <i class="fas fa-arrow-left me-2"></i>Kembali ke Dashboard
                                </a>
                                <a href="../../index.php" class="btn btn-secondary btn-custom">
                                    <i class="fas fa-home me-2"></i>Beranda
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
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lamar Lowongan - SAW Applicant System</title>
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
        .form-control, .form-select {
            border-radius: 10px;
            border: 2px solid #e9ecef;
            padding: 12px 15px;
            transition: all 0.3s ease;
        }
        .form-control:focus, .form-select:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
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
        .file-upload {
            border: 2px dashed #dee2e6;
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            transition: all 0.3s ease;
        }
        .file-upload:hover {
            border-color: #667eea;
            background-color: #f8f9ff;
        }
        .job-info {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px;
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
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <!-- Job Information -->
                    <div class="card job-info mb-4">
                        <div class="card-body text-center">
                            <h2 class="card-title fw-bold mb-3">
                                <i class="fas fa-briefcase me-2"></i>Lamar Lowongan
                            </h2>
                            <h4 class="mb-2"><?= htmlspecialchars($job['title']) ?></h4>
                            <p class="mb-0 opacity-75"><?= htmlspecialchars($job['description']) ?></p>
                            <div class="mt-4">
                                <h5 class="text-white" style="opacity: .85;">Persyaratan</h5>
                                <div>
                                    <?php if (!empty($job['requirements'])): ?>
                                        <?php
                                        $requirements = explode(',', $job['requirements']);
                                        foreach ($requirements as $req): ?>
                                            <span class="badge bg-light text-dark me-1 mb-1 p-2">
                                                <i class="fas fa-check me-1"></i>
                                                <?= htmlspecialchars(trim($req)) ?>
                                            </span>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <p class="mb-0 opacity-75">Tidak ada persyaratan khusus.</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Application Form -->
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-edit me-2"></i>Form Lamaran
                            </h5>
                        </div>
                        <div class="card-body p-4">
                            <form action="../../controllers/ApplicantController.php" method="POST" enctype="multipart/form-data">
                                <input type="hidden" name="action" value="submit_application">
                                <input type="hidden" name="job_id" value="<?= $job_id ?>">

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="experience_years" class="form-label fw-semibold">
                                            <i class="fas fa-clock me-2"></i>Lama Pengalaman (tahun)
                                        </label>
                                        <input type="number" 
                                               class="form-control" 
                                               id="experience_years" 
                                               name="experience_years" 
                                               min="0" 
                                               max="50"
                                               placeholder="Contoh: 3"
                                               required>
                                        <div class="form-text">
                                            <i class="fas fa-info-circle me-1"></i>
                                            Masukkan jumlah tahun pengalaman kerja
                                        </div>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="education_level" class="form-label fw-semibold">
                                            <i class="fas fa-graduation-cap me-2"></i>Pendidikan Terakhir
                                        </label>
                                        <select class="form-select" id="education_level" name="education_level" required>
                                            <option value="">Pilih Pendidikan</option>
                                            <option value="SMA">SMA/SMK</option>
                                            <option value="Diploma">Diploma (D1-D4)</option>
                                            <option value="S1">Sarjana (S1)</option>
                                            <option value="S2">Magister (S2)</option>
                                            <option value="S3">Doktor (S3)</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="motivation" class="form-label fw-semibold">
                                        <i class="fas fa-heart me-2"></i>Alasan Melamar
                                    </label>
                                    <textarea class="form-control" 
                                              id="motivation" 
                                              name="motivation" 
                                              rows="5" 
                                              placeholder="Jelaskan mengapa Anda tertarik dengan lowongan ini dan apa yang membuat Anda cocok untuk posisi ini..."
                                              required></textarea>
                                    <div class="form-text">
                                        <i class="fas fa-info-circle me-1"></i>
                                        Minimal 100 karakter, jelaskan motivasi dan keunggulan Anda
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <label for="cv" class="form-label fw-semibold">
                                        <i class="fas fa-file-pdf me-2"></i>Upload CV (PDF)
                                    </label>
                                    <div class="file-upload">
                                        <i class="fas fa-cloud-upload-alt fa-3x text-muted mb-3"></i>
                                        <h6 class="text-muted">Pilih file CV Anda</h6>
                                        <p class="text-muted small mb-3">Drag & drop file PDF atau klik untuk memilih</p>
                                        <input type="file" 
                                               class="form-control" 
                                               id="cv" 
                                               name="cv" 
                                               accept=".pdf"
                                               required>
                                        <div class="form-text mt-2">
                                            <i class="fas fa-info-circle me-1"></i>
                                            Format PDF, maksimal 5MB
                                        </div>
                                    </div>
                                </div>

                                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                    <a href="dashboard.php" class="btn btn-secondary btn-custom">
                                        <i class="fas fa-times me-2"></i>Batal
                                    </a>
                                    <button type="submit" class="btn btn-primary btn-custom">
                                        <i class="fas fa-paper-plane me-2"></i>Kirim Lamaran
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Tips Section -->
                    <div class="card mt-4">
                        <div class="card-header bg-info text-white">
                            <h6 class="mb-0">
                                <i class="fas fa-lightbulb me-2"></i>Tips Melamar
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <ul class="list-unstyled">
                                        <li class="mb-2">
                                            <i class="fas fa-check-circle text-success me-2"></i>
                                            Pastikan CV Anda up-to-date
                                        </li>
                                        <li class="mb-2">
                                            <i class="fas fa-check-circle text-success me-2"></i>
                                            Tulis alasan melamar yang menarik
                                        </li>
                                    </ul>
                                </div>
                                <div class="col-md-6">
                                    <ul class="list-unstyled">
                                        <li class="mb-2">
                                            <i class="fas fa-check-circle text-success me-2"></i>
                                            Isi data dengan jujur dan akurat
                                        </li>
                                        <li class="mb-2">
                                            <i class="fas fa-check-circle text-success me-2"></i>
                                            Periksa kembali sebelum submit
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="../../assets/js/bootstrap.bundle.min.js"></script>
    <script>
        // File upload preview
        document.getElementById('cv').addEventListener('change', function(e) {
            const file = e.target.files[0];
            const fileUpload = document.querySelector('.file-upload');
            
            if (file) {
                if (file.type === 'application/pdf') {
                    fileUpload.innerHTML = `
                        <i class="fas fa-file-pdf fa-3x text-success mb-3"></i>
                        <h6 class="text-success">${file.name}</h6>
                        <p class="text-muted small">File berhasil dipilih</p>
                        <input type="file" class="form-control" id="cv" name="cv" accept=".pdf" required>
                    `;
                } else {
                    alert('Hanya file PDF yang diperbolehkan!');
                    e.target.value = '';
                }
            }
        });
    </script>
</body>
</html>
