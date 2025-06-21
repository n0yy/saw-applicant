<?php
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'approve_user') {
    $user_id = $_POST['user_id'];
    $stmt = $conn->prepare("UPDATE users SET is_approved = 1 WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    header('Location: ../views/admin/dashboard.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'add_job') {
    session_start();
    $title = $_POST['title'];
    $desc = $_POST['description'];
    $requirements = $_POST['requirements'] ?? null;
    $admin_id = $_SESSION['user']['id'];

    // Mulai transaksi
    $conn->begin_transaction();

    try {
        // Insert job
        $stmt = $conn->prepare("INSERT INTO jobs (title, description, requirements) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $title, $desc, $requirements);
        $stmt->execute();
        $job_id = $conn->insert_id;
        $stmt->close();

        // Buat direktori upload training jika belum ada
        $upload_dir = '../uploads/training/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        // Tangani upload gambar training
        if (isset($_FILES['training_images']) && !empty($_FILES['training_images']['name'][0])) {
            $total_files = count($_FILES['training_images']['name']);
            
            for ($i = 0; $i < $total_files; $i++) {
                if ($_FILES['training_images']['error'][$i] === UPLOAD_ERR_OK) {
                    $file_tmp = $_FILES['training_images']['tmp_name'][$i];
                    $file_name = basename($_FILES['training_images']['name'][$i]);
                    $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
                    
                    // Validasi format file
                    $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
                    if (!in_array($file_ext, $allowed_extensions)) {
                        throw new Exception("Format file tidak didukung. Gunakan: " . implode(', ', $allowed_extensions));
                    }
                    
                    // Validasi ukuran file (maksimal 5MB)
                    if ($_FILES['training_images']['size'][$i] > 5 * 1024 * 1024) {
                        throw new Exception("Ukuran file terlalu besar (maksimal 5MB)");
                    }
                    
                    // Generate nama file unik
                    $new_file_name = 'training_' . $job_id . '_' . time() . '_' . $i . '.' . $file_ext;
                    $destination = $upload_dir . $new_file_name;
                    
                    // Upload file
                    if (!move_uploaded_file($file_tmp, $destination)) {
                        throw new Exception("Gagal mengupload file training");
                    }
                    
                    // Simpan ke database
                    $image_path = 'uploads/training/' . $new_file_name;
                    $description = $_POST['training_descriptions'][$i] ?? '';
                    
                    $stmt = $conn->prepare("INSERT INTO training (job_id, image_path, description) VALUES (?, ?, ?)");
                    $stmt->bind_param("iss", $job_id, $image_path, $description);
                    $stmt->execute();
                    $stmt->close();
                }
            }
        }

        // Commit transaksi
        $conn->commit();
        header('Location: ../views/admin/job_list.php?success=job_added');
        
    } catch (Exception $e) {
        // Rollback jika terjadi error
        $conn->rollback();
        die("Gagal menambah lowongan: " . $e->getMessage());
    }
}

