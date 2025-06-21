<?php
require_once '../config/db.php';
session_start();

// Validasi session admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: ../index.php');
    exit();
}

// Validasi input
if (!isset($_POST['application_id']) || !isset($_POST['job_id'])) {
    die("Data tidak lengkap");
}

$application_id = (int)$_POST['application_id'];
$job_id = (int)$_POST['job_id'];

// Validasi application_id ada dan valid
$stmt = $conn->prepare("SELECT id, job_id FROM applications WHERE id = ? AND status = 'submitted'");
$stmt->bind_param("i", $application_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Aplikasi tidak ditemukan atau status tidak valid");
}

$application = $result->fetch_assoc();
$stmt->close();

// Validasi job_id sesuai
if ($application['job_id'] != $job_id) {
    die("Job ID tidak sesuai");
}

// Mulai transaksi
$conn->begin_transaction();

try {
    // Update status aplikasi yang dipilih menjadi accepted
    $stmt = $conn->prepare("UPDATE applications SET status = 'accepted' WHERE id = ?");
    $stmt->bind_param("i", $application_id);
    $stmt->execute();
    $stmt->close();

    // Update status aplikasi lain untuk job yang sama menjadi rejected
    $stmt = $conn->prepare("UPDATE applications SET status = 'rejected' WHERE job_id = ? AND id != ?");
    $stmt->bind_param("ii", $job_id, $application_id);
    $stmt->execute();
    $stmt->close();

    // Commit transaksi
    $conn->commit();

    // Redirect ke halaman training dengan data pemenang
    header("Location: ../views/admin/training_view.php?job_id=$job_id&winner_id=$application_id");
    exit();

} catch (Exception $e) {
    // Rollback jika terjadi error
    $conn->rollback();
    die("Terjadi kesalahan: " . $e->getMessage());
}
