<?php
require_once '../config/db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'apply_job') {
    $user_id = $_SESSION['user']['id'];
    $job_id = $_POST['job_id'];

    $stmt = $conn->prepare("INSERT INTO applications (user_id, job_id) VALUES (?, ?)");
    $stmt->bind_param("ii", $user_id, $job_id);
    $stmt->execute();

    header('Location: ../views/applicant/dashboard.php');
}

if ($_POST['action'] === 'submit_application') {
    $user_id = $_SESSION['user']['id'];
    $job_id = $_POST['job_id'];
    $exp = $_POST['experience_years'];
    $motiv = $_POST['motivation'];
    $edu = $_POST['education_level'];

    // Cek apakah sudah ada aplikasi untuk user dan job ini
    $stmt = $conn->prepare("SELECT id FROM applications WHERE user_id = ? AND job_id = ?");
    $stmt->bind_param("ii", $user_id, $job_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        // Buat record baru jika belum ada
        $stmt = $conn->prepare("INSERT INTO applications (user_id, job_id, status) VALUES (?, ?, 'draft')");
        $stmt->bind_param("ii", $user_id, $job_id);
        if (!$stmt->execute()) {
            die("Gagal membuat aplikasi: " . $stmt->error);
        }
    }
    $stmt->close();

    // Tangani upload file CV
    $upload_dir = '../uploads/cv/';
    
    // Buat direktori jika belum ada
    if (!is_dir($upload_dir)) {
        if (!mkdir($upload_dir, 0777, true)) {
            die("Gagal membuat direktori upload: " . $upload_dir);
        }
    }
    
    // Pastikan direktori memiliki permission yang benar
    if (!is_writable($upload_dir)) {
        chmod($upload_dir, 0777);
    }

    // Validasi file upload
    if (!isset($_FILES['cv']) || $_FILES['cv']['error'] !== UPLOAD_ERR_OK) {
        $error_message = "Error upload file: ";
        switch ($_FILES['cv']['error']) {
            case UPLOAD_ERR_INI_SIZE:
                $error_message .= "File terlalu besar (melebihi upload_max_filesize)";
                break;
            case UPLOAD_ERR_FORM_SIZE:
                $error_message .= "File terlalu besar (melebihi MAX_FILE_SIZE)";
                break;
            case UPLOAD_ERR_PARTIAL:
                $error_message .= "File hanya terupload sebagian";
                break;
            case UPLOAD_ERR_NO_FILE:
                $error_message .= "Tidak ada file yang diupload";
                break;
            default:
                $error_message .= "Error tidak diketahui";
        }
        die($error_message);
    }

    $file_tmp = $_FILES['cv']['tmp_name'];
    $file_name = basename($_FILES['cv']['name']);
    $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

    // Validasi file
    if ($file_ext !== 'pdf') {
        die("Format CV harus PDF");
    }

    // Validasi ukuran file (maksimal 5MB)
    if ($_FILES['cv']['size'] > 5 * 1024 * 1024) {
        die("Ukuran file terlalu besar (maksimal 5MB)");
    }

    $new_file_name = 'cv_' . $user_id . '_' . $job_id . '.' . $file_ext;
    $destination = $upload_dir . $new_file_name;

    // Coba upload file
    if (!move_uploaded_file($file_tmp, $destination)) {
        $error = error_get_last();
        die("Gagal mengupload file. Error: " . ($error['message'] ?? 'Unknown error'));
    }

    // Simpan path relatif ke database
    $cv_path = 'uploads/cv/' . $new_file_name;

    $stmt = $conn->prepare("
        UPDATE applications
        SET experience_years = ?, motivation = ?, education_level = ?, cv = ?, status = 'submitted'
        WHERE user_id = ? AND job_id = ?
    ");
    $stmt->bind_param("isssii", $exp, $motiv, $edu, $cv_path, $user_id, $job_id);
    
    if (!$stmt->execute()) {
        // Jika update gagal, hapus file yang sudah diupload
        unlink($destination);
        die("Gagal menyimpan data aplikasi: " . $stmt->error);
    }

    header("Location: ../views/applicant/dashboard.php");
}
