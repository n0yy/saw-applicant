<?php
require_once '../includes/db.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'hr') {
    header("Location: ../login.php");
    exit;
}

$jobs = mysqli_query($conn, "SELECT * FROM jobs ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>SAW per Job</title>
  <link href="../assets/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
  <h2>SAW Ranking per Job</h2>
  <table class="table table-bordered">
    <thead><tr><th>Title</th><th>Location</th><th>Deadline</th><th>Action</th></tr></thead>
    <tbody>
      <?php while($job = mysqli_fetch_assoc($jobs)): ?>
        <tr>
          <td><?= $job['title'] ?></td>
          <td><?= $job['location'] ?></td>
          <td><?= $job['deadline'] ?></td>
          <td>
            <a href="saw_result_job.php?id=<?= $job['id_job'] ?>" class="btn btn-sm btn-primary">View Ranking</a>
          </td>
        </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
</div>
</body>
</html>
