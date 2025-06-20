<?php
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'hr') {
    header("Location: ../login.php");
    exit;
}

$id_job = intval($_GET['id'] ?? 0);

// Ambil info job
$job_q = mysqli_query($conn, "SELECT * FROM jobs WHERE id_job = $id_job");
$job = mysqli_fetch_assoc($job_q);

// Ambil kriteria
$kriteria = [];
$res = mysqli_query($conn, "SELECT * FROM criteria");
while ($row = mysqli_fetch_assoc($res)) {
    $kriteria[$row['id_criteria']] = $row;
}

// Ambil data evaluasi pelamar untuk job ini
$data = [];
$res = mysqli_query($conn, "
    SELECT e.id_applicant, a.full_name, e.id_criteria, e.score
    FROM evaluations e
    JOIN applicants a ON e.id_applicant = a.id_applicant
    WHERE a.job_id = $id_job
");

while ($row = mysqli_fetch_assoc($res)) {
    $id = $row['id_applicant'];
    $cid = $row['id_criteria'];
    $data[$id]['name'] = $row['full_name'];
    $data[$id]['scores'][$cid] = $row['score'];
}

// Normalisasi dan hitung skor akhir
$normalisasi = [];
foreach ($kriteria as $cid => $k) {
    $values = array_column(array_column($data, 'scores'), $cid);
    $max = max($values);
    $min = min($values);

    foreach ($data as $id => $d) {
        $val = $d['scores'][$cid] ?? 0;
        if ($k['type'] === 'Benefit') {
            $normal = $val / ($max ?: 1);
        } else {
            $normal = ($min ?: 1) / ($val ?: 1);
        }
        $normalisasi[$id][$cid] = $normal * $k['weight'];
    }
}

$ranking = [];
foreach ($normalisasi as $id => $n_scores) {
    $ranking[$id] = array_sum($n_scores);
}

// Urutkan dari nilai terbesar
arsort($ranking);
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>SAW Result - <?= htmlspecialchars($job['title']) ?></title>
  <link href="../assets/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
  <h3>SAW Result for Job: <?= htmlspecialchars($job['title']) ?></h3>
  <div class="mb-3">
    <a href="saw_per_job.php" class="btn btn-secondary">← Back</a>
    <a href="accept_top_applicant.php?job_id=<?= $id_job ?>" class="btn btn-success">Accept Top Applicant</a>
    <a href="export_excel.php?job_id=<?= $id_job ?>" class="btn btn-outline-primary">Export to Excel</a>
  </div>

  <table class="table table-bordered">
    <thead>
      <tr>
        <th>Rank</th>
        <th>Applicant</th>
        <th>Final Score</th>
      </tr>
    </thead>
    <tbody>
      <?php
      $rank = 1;
      foreach ($ranking as $id => $score):
      ?>
        <tr>
          <td><?= $rank++ ?></td>
          <td><?= htmlspecialchars($data[$id]['name']) ?></td>
          <td><?= number_format($score, 4) ?></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
</body>
</html>
