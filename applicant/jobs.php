<?php
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'applicant') {
    header("Location: ../login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Available Jobs</title>
  <link href="../assets/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
  <h2 class="mb-4">Available Job Vacancies</h2>
  <table class="table table-bordered">
    <thead>
      <tr>
        <th>Job Title</th>
        <th>Description</th>
        <th>Location</th>
        <th>Deadline</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody>
      <?php
        $sql = "SELECT * FROM jobs ORDER BY deadline ASC";
        $result = mysqli_query($conn, $sql);
        while ($job = mysqli_fetch_assoc($result)):
      ?>
      <tr>
        <td><?= htmlspecialchars($job['title']) ?></td>
        <td><?= htmlspecialchars($job['description']) ?></td>
        <td><?= htmlspecialchars($job['location']) ?></td>
        <td><?= $job['deadline'] ?></td>
        <td>
          <form method="POST" action="apply_job.php">
            <input type="hidden" name="id_job" value="<?= $job['id_job'] ?>">
            <button type="submit" class="btn btn-primary btn-sm">
            <a href="register.php?job_id=<?= $job['id_job'] ?>" class="btn btn-sm btn-primary">Apply</a>
            </button>
          </form>
        </td>
      </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
</div>
</body>
</html>
