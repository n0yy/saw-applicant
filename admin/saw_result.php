<?php
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'hr') {
    header("Location: ../login.php");
    exit;
}

// Ambil semua kriteria
$kriteria = [];
$res = mysqli_query($conn, "SELECT * FROM criteria");
while ($row = mysqli_fetch_assoc($res)) {
    $kriteria[$row['id_criteria']] = $row;
}

// Ambil semua nilai evaluasi
$data = [];
$res = mysqli_query($conn, "
    SELECT e.id_applicant, a.full_name, e.id_criteria, e.score
    FROM evaluations e
    JOIN applicants a ON a.id_applicant = e.id_applicant
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
  <title>SAW Result</title>
  <link href="../assets/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
  <h2>SAW Ranking Result</h2>
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
          <td><?= $data[$id]['name'] ?></td>
          <td><?= number_format($score, 4) ?></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>

</div>
</body>
</html>
