<?php
session_start();
require_once '../../config/db.php';

if ($_SESSION['user']['role'] !== 'admin') {
    header('Location: ../../index.php');
    exit();
}

$result = $conn->query("SELECT * FROM jobs");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Lowongan - SAW Applicant System</title>
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
            padding: 8px 20px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .btn-custom:hover {
            transform: translateY(-2px);
        }
        .training-input {
            border: 2px dashed #dee2e6;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 15px;
            background-color: #f8f9fa;
            transition: all 0.3s ease;
        }
        .training-input:hover {
            border-color: #667eea;
            background-color: #f8f9ff;
        }
        .table {
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        }
        .job-card {
            transition: all 0.3s ease;
        }
        .job-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
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
            <!-- Header -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body text-center py-4">
                            <h2 class="card-title fw-bold text-primary mb-2">
                                <i class="fas fa-briefcase me-2"></i>Kelola Lowongan
                            </h2>
                            <p class="card-text text-muted">
                                Tambah lowongan baru dan kelola lowongan yang sudah ada
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Success Messages -->
            <?php if (isset($_GET['success'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    <?php if ($_GET['success'] === 'winner_selected'): ?>
                        <strong>Berhasil!</strong> Pemenang telah berhasil dipilih!
                    <?php elseif ($_GET['success'] === 'job_added'): ?>
                        <strong>Berhasil!</strong> Lowongan berhasil ditambahkan!
                    <?php endif; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <!-- Add Job Form -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-plus me-2"></i>Tambah Lowongan Baru
                            </h5>
                        </div>
                        <div class="card-body p-4">
                            <form action="../../controllers/AdminController.php" method="POST" enctype="multipart/form-data">
    <input type="hidden" name="action" value="add_job">
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="title" class="form-label fw-semibold">
                                            <i class="fas fa-heading me-2"></i>Judul Lowongan
                                        </label>
                                        <input type="text" 
                                               class="form-control" 
                                               id="title" 
                                               name="title" 
                                               placeholder="Contoh: Software Developer" 
                                               required>
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label for="description" class="form-label fw-semibold">
                                            <i class="fas fa-align-left me-2"></i>Deskripsi
                                        </label>
                                        <textarea class="form-control" 
                                                  id="description" 
                                                  name="description" 
                                                  rows="3" 
                                                  placeholder="Jelaskan deskripsi lowongan..."
                                                  required></textarea>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="requirements" class="form-label fw-semibold">
                                        <i class="fas fa-check-circle me-2"></i>Persyaratan (pisahkan dengan koma)
                                    </label>
                                    <textarea class="form-control" 
                                              id="requirements" 
                                              name="requirements" 
                                              rows="2" 
                                              placeholder="Contoh: PHP, JavaScript, MySQL"></textarea>
                                    <div class="form-text">
                                        <i class="fas fa-info-circle me-1"></i>
                                        Masukkan persyaratan yang dibutuhkan, pisahkan dengan koma.
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <label class="form-label fw-semibold">
                                        <i class="fas fa-images me-2"></i>Gambar Training (5-10 gambar)
                                    </label>
                                    <div id="training-images-container">
                                        <div class="training-input">
                                            <div class="row align-items-center">
                                                <div class="col-md-5">
                                                    <input type="file" 
                                                           class="form-control" 
                                                           name="training_images[]" 
                                                           accept="image/*" 
                                                           required>
                                                </div>
                                                <div class="col-md-6">
                                                    <input type="text" 
                                                           class="form-control" 
                                                           name="training_descriptions[]" 
                                                           placeholder="Deskripsi gambar training">
                                                </div>
                                                <div class="col-md-1">
                                                    <button type="button" class="btn btn-danger btn-sm remove-image">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <button type="button" id="add-image" class="btn btn-success btn-custom">
                                        <i class="fas fa-plus me-2"></i>Tambah Gambar
                                    </button>
                                    <div class="form-text">
                                        <i class="fas fa-info-circle me-1"></i>
                                        Format: JPG, PNG, GIF. Maksimal 5MB per gambar.
                                    </div>
                                </div>

                                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                    <button type="submit" class="btn btn-primary btn-custom">
                                        <i class="fas fa-save me-2"></i>Tambah Lowongan
                                    </button>
                                </div>
</form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Job List -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header bg-info text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-list me-2"></i>Daftar Lowongan
                            </h5>
                        </div>
                        <div class="card-body">
                            <?php if ($result->num_rows > 0): ?>
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead class="table-light">
                                            <tr>
                                                <th><i class="fas fa-heading me-1"></i>Judul</th>
                                                <th><i class="fas fa-align-left me-1"></i>Deskripsi</th>
                                                <th><i class="fas fa-calendar me-1"></i>Tanggal Dibuat</th>
                                                <th class="text-center"><i class="fas fa-cogs me-1"></i>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
    <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
                                                    <td>
                                                        <i class="fas fa-briefcase me-2 text-primary"></i>
                                                        <strong><?= htmlspecialchars($row['title']) ?></strong>
                                                    </td>
                                                    <td>
                                                        <span class="text-muted">
                                                            <?= htmlspecialchars(substr($row['description'], 0, 100)) ?>
                                                            <?= strlen($row['description']) > 100 ? '...' : '' ?>
                                                        </span>
            </td>
            <td>
                                                        <i class="fas fa-calendar me-2 text-muted"></i>
                                                        <?= date('d/m/Y H:i', strtotime($row['created_at'])) ?>
                                                    </td>
                                                    <td class="text-center">
                                                        <div class="btn-group" role="group">
                                                            <a href="applicants_per_job.php?job_id=<?= $row['id'] ?>" 
                                                               class="btn btn-info btn-sm btn-custom">
                                                                <i class="fas fa-users me-1"></i>Pelamar
                                                            </a>
                                                            <a href="../../controllers/SAWController.php?job_id=<?= $row['id'] ?>" 
                                                               class="btn btn-warning btn-sm btn-custom">
                                                                <i class="fas fa-chart-line me-1"></i>SAW
                                                            </a>
                                                        </div>
            </td>
        </tr>
    <?php endwhile; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php else: ?>
                                <div class="text-center py-4">
                                    <i class="fas fa-briefcase text-muted" style="font-size: 3rem;"></i>
                                    <h5 class="mt-3 text-muted">Tidak ada lowongan tersedia</h5>
                                    <p class="text-muted">Silakan tambah lowongan baru di atas</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="../../assets/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const container = document.getElementById('training-images-container');
            const addButton = document.getElementById('add-image');
            let imageCount = 1;

            addButton.addEventListener('click', function() {
                if (imageCount < 10) {
                    const newInput = document.createElement('div');
                    newInput.className = 'training-input';
                    
                    newInput.innerHTML = `
                        <div class="row align-items-center">
                            <div class="col-md-5">
                                <input type="file" class="form-control" name="training_images[]" accept="image/*" required>
                            </div>
                            <div class="col-md-6">
                                <input type="text" class="form-control" name="training_descriptions[]" placeholder="Deskripsi gambar training">
                            </div>
                            <div class="col-md-1">
                                <button type="button" class="btn btn-danger btn-sm remove-image">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    `;
                    
                    container.appendChild(newInput);
                    imageCount++;
                    
                    if (imageCount >= 10) {
                        addButton.disabled = true;
                        addButton.innerHTML = '<i class="fas fa-ban me-2"></i>Maksimal 10 gambar';
                        addButton.className = 'btn btn-secondary btn-custom';
                    }
                }
            });

            container.addEventListener('click', function(e) {
                if (e.target.closest('.remove-image')) {
                    e.target.closest('.training-input').remove();
                    imageCount--;
                    addButton.disabled = false;
                    addButton.innerHTML = '<i class="fas fa-plus me-2"></i>Tambah Gambar';
                    addButton.className = 'btn btn-success btn-custom';
                }
            });
        });
    </script>
</body>
</html>
