<?php
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'hr') {
    header("Location: ../login.php");
    exit;
}

// Approve/Reject logic
if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $status = $_GET['action'] === 'accept' ? 'accepted' : 'rejected';
    mysqli_query($conn, "UPDATE applicants SET status = '$status' WHERE id_applicant = $id");
    exit;
}

// Handle job post
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_job'])) {
    $title = $_POST['title'];
    $desc  = $_POST['description'];
    $loc   = $_POST['location'];
    $deadline = $_POST['deadline'];
    $hr_id = $_SESSION['user_id'];

    $insert = "INSERT INTO jobs (title, description, location, deadline, created_by)
               VALUES ('$title', '$desc', '$loc', '$deadline', $hr_id)";
    mysqli_query($conn, $insert);
    $success = "Job posted successfully!";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>HR Dashboard</title>
  <link href="../assets/css/bootstrap.min.css" rel="stylesheet">
  <style>
    .sidebar { min-height: 100vh; background: #f8f9fa; }
    .nav-link.active { font-weight: bold; }
  </style>
</head>
<body>
<div class="container-fluid">
  <div class="row">
    <!-- Sidebar -->
    <div class="col-md-3 sidebar p-3">
      <h4>HR Panel</h4>
      <ul class="nav flex-column mt-4">
        <li class="nav-item">
          <a class="nav-link active" href="#applicants" data-bs-toggle="tab">Data Pelamar</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#jobs" data-bs-toggle="tab">Tambah Lowongan</a>
        </li>
        <li class="list-group-item"><a href="applicants.php">Applicants</a></li>
        <li class="list-group-item"><a href="create_job.php">Post Job</a></li>
        <li class="list-group-item"><a href="saw_result.php">SAW Result</a></li>

      </ul>
    </div>

    <!-- Content -->
    <div class="col-md-9 p-4 tab-content">
      <!-- Tab 1: Applicants -->
      <div class="tab-pane fade show active" id="applicants">
        <h4>Data Pelamar</h4>
        <table class="table table-bordered mt-3">
          <thead>
            <tr>
              <th>Name</th>
              <th>Phone</th>
              <th>Email</th>
              <th>Status</th>
              <th>CV</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            <?php
              $sql = "SELECT a.*, u.email FROM applicants a JOIN user u ON a.user_id = u.id_user";
              $res = mysqli_query($conn, $sql);
              while ($row = mysqli_fetch_assoc($res)):
            ?>
            <tr>
              <td><?= htmlspecialchars($row['full_name']) ?></td>
              <td><?= $row['phone_number'] ?></td>
              <td><?= $row['email'] ?></td>
              <td>
                <?php
                  $badge = match ($row['status']) {
                      'pending'  => 'warning',
                      'accepted' => 'success',
                      'rejected' => 'danger',
                  };
                ?>
                <span class="badge bg-<?= $badge ?>"><?= $row['status'] ?></span>
              </td>
              <td><a href="../<?= $row['cv_file'] ?>" target="_blank">View CV</a></td>
              <td>
                <?php if ($row['status'] === 'pending'): ?>
                  <a href="?action=accept&id=<?= $row['id_applicant'] ?>" class="btn btn-success btn-sm">Accept</a>
                  <a href="?action=reject&id=<?= $row['id_applicant'] ?>" class="btn btn-danger btn-sm">Reject</a>
                <?php else: ?>
                  <em>No action</em>
                <?php endif; ?>
              </td>
            </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      </div>

      <!-- Tab 2: Job Form -->
      <div class="tab-pane fade" id="jobs">
        <h4>Tambah Lowongan Kerja</h4>
        <?php if (isset($success)) echo "<div class='alert alert-success'>$success</div>"; ?>
        <form method="POST" class="mt-3">
          <input type="hidden" name="add_job" value="1">
          <div class="mb-3">
            <label>Judul</label>
            <input type="text" name="title" class="form-control" required>
          </div>
          <div class="mb-3">
            <label>Deskripsi</label>
            <textarea name="description" class="form-control" rows="4" required></textarea>
          </div>
          <div class="mb-3">
            <label>Lokasi</label>
            <input type="text" name="location" class="form-control">
          </div>
          <div class="mb-3">
            <label>Deadline</label>
            <input type="date" name="deadline" class="form-control" required>
          </div>
          <button type="submit" class="btn btn-primary">Tambah</button>
        </form>
      </div>
    </div>
  </div>
</div>

<script src="../assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>
