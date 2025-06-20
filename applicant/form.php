<?php
    session_start();
    require_once '../includes/db.php';

    if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'applicant') {
        header("Location: ../register.php");
        exit;
    }

    $id_job = intval($_GET['id_job'] ?? 0);

    // Ambil detail job
    $job_query = mysqli_query($conn, "SELECT * FROM jobs WHERE id_job = $id_job");
    $job = mysqli_fetch_assoc($job_query);
    if (!$job) {
        echo "<script>alert('Job not found.'); window.location='applicant';</script>";
        exit;
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Job Application</title>
  <link href="../assets/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
  <h2>Job Application Evaluation Form</h2>
    
    <div class="card mb-4">
    <div class="card-body">
        <h4><?= htmlspecialchars($job['title']) ?></h4>
        <p><strong>Description:</strong> <?= nl2br(htmlspecialchars($job['description'])) ?></p>
        <p><strong>Location:</strong> <?= htmlspecialchars($job['location']) ?></p>
        <p><strong>Deadline:</strong> <?= htmlspecialchars($job['deadline']) ?></p>
    </div>
    </div>

  <form method="POST" action="apply_job.php" class="mt-4">
    <input type="hidden" name="id_job" value="<?= $id_job ?>">

    <div class="mb-3">
      <label>Why do you want to apply for this job?</label>
      <textarea name="reason" class="form-control" required></textarea>
    </div>

    <div class="row">
      <div class="col-md-4">
        <label>Teamwork (1–5)</label>
        <input type="number" name="teamwork" min="1" max="5" class="form-control" required>
      </div>
      <div class="col-md-4">
        <label>Commitment to Learning (1–5)</label>
        <input type="number" name="commitment" min="1" max="5" class="form-control" required>
      </div>
      <div class="col-md-4">
        <label>Work Experience (in years)</label>
        <input type="number" name="work_experience" min="0" max="50" class="form-control" required>
      </div>
    </div>

    <div class="mt-4">
      <button type="submit" class="btn btn-success">Submit Application</button>
    </div>
  </form>
</div>
</body>
</html>
