<?php
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'hr') {
    header("Location: ../login.php");
    exit;
}

$id_applicant = intval($_GET['id'] ?? 0);

// Ambil pelamar
$applicant_q = mysqli_query($conn, "SELECT full_name FROM applicants WHERE id_applicant = $id_applicant");
$applicant = mysqli_fetch_assoc($applicant_q);

// Ambil kriteria
$criteria_q = mysqli_query($conn, "SELECT * FROM criteria");
$criteria = [];
while ($row = mysqli_fetch_assoc($criteria_q)) {
    $criteria[] = $row;
}

// Handle submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($_POST['score'] as $id_criteria => $value) {
        $id_criteria = intval($id_criteria);
        $value = floatval($value);

        $cek = mysqli_query($conn, "SELECT * FROM evaluations WHERE id_applicant = $id_applicant AND id_criteria = $id_criteria");
        if (mysqli_num_rows($cek) > 0) {
            mysqli_query($conn, "UPDATE evaluations SET score = $value WHERE id_applicant = $id_applicant AND id_criteria = $id_criteria");
        } else {
            mysqli_query($conn, "INSERT INTO evaluations (id_applicant, id_criteria, score) VALUES ($id_applicant, $id_criteria, $value)");
        }
    }

    echo "<script>alert('Evaluation saved successfully'); window.location='applicants.php';</script>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Evaluate Applicant</title>
  <link href="../assets/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
  <h3>Evaluation for: <?= htmlspecialchars($applicant['full_name']) ?></h3>
  <form method="POST" class="mt-4">
    <?php foreach ($criteria as $k): ?>
      <div class="mb-3">
        <label><?= $k['name'] ?> (<?= $k['type'] ?>)</label>
        <input type="number" step="any" name="score[<?= $k['id_criteria'] ?>]" class="form-control" required>
      </div>
    <?php endforeach; ?>
    <button type="submit" class="btn btn-success">Save Evaluation</button>
  </form>
</div>
</body>
</html>
