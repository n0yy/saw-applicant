<?php
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'applicant') {
    header("Location: ../login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Ambil data pelamar
$query = "SELECT * FROM applicants WHERE user_id = $user_id LIMIT 1";
$result = mysqli_query($conn, $query);
$applicant = mysqli_fetch_assoc($result);

// Ambil training jika sudah diterima
$training = null;
if ($applicant['status'] === 'accepted') {
    $queryTraining = "SELECT * FROM training WHERE id_applicant = " . $applicant['id_applicant'];
    $resultTraining = mysqli_query($conn, $queryTraining);
    $training = mysqli_fetch_assoc($resultTraining);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Applicant Dashboard</title>
  <link href="../assets/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
  <h2>Welcome, <?= $_SESSION['username'] ?></h2>

  <div class="card mt-4">
    <div class="card-header">Application Status</div>
    <div class="card-body">
      <p><strong>Name:</strong> <?= $applicant['full_name'] ?></p>
      <p><strong>Status:</strong> 
        <?php
          if ($applicant['status'] === 'pending') {
              echo '<span class="badge bg-warning">Pending</span>';
          } elseif ($applicant['status'] === 'accepted') {
              echo '<span class="badge bg-success">Accepted</span>';
          } else {
              echo '<span class="badge bg-danger">Rejected</span>';
          }
        ?>
      </p>
    </div>
  </div>

  <?php if ($training): ?>
    <div class="card mt-4">
      <div class="card-header">Training Content</div>
      <div class="card-body">
        <h5><?= $training['title'] ?></h5>
        <img src="../<?= $training['image_path'] ?>" class="img-fluid" alt="Training">
      </div>
    </div>
  <?php endif; ?>

  <div class="mt-4">
    <a href="../logout.php" class="btn btn-danger">Logout</a>
  </div>
</div>
</body>
</html>
