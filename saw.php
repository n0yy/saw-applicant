<?php
require_once 'includes/db.php';

$kriteria = [];
$queryK = mysqli_query($conn, "SELECT * FROM criteria");
while ($row = mysqli_fetch_assoc($queryK)) {
    $kriteria[] = $row;
}

$alternatif = [];
$queryA = mysqli_query($conn, "SELECT * FROM applicants WHERE status = 'accepted'");
while ($a = mysqli_fetch_assoc($queryA)) {
    $alternatif[$a['id_applicant']] = [
        'name' => $a['full_name'],
        'scores' => []
    ];
}

foreach ($kriteria as $k) {
    $id_k = $k['id_criteria'];
    $max = 0;

    $queryN = mysqli_query($conn, "SELECT MAX(score) AS max_score FROM evaluations WHERE id_criteria = $id_k");
    $max = mysqli_fetch_assoc($queryN)['max_score'];

    $queryScores = mysqli_query($conn, "SELECT * FROM evaluations WHERE id_criteria = $id_k");
    while ($s = mysqli_fetch_assoc($queryScores)) {
        $id_app = $s['id_applicant'];
        $normalized = $s['score'] / $max;
        $alternatif[$id_app]['scores'][] = $normalized * $k['weight'];
    }
}

foreach ($alternatif as $id => $data) {
    $alternatif[$id]['total'] = array_sum($data['scores']);
}

uasort($alternatif, function ($a, $b) {
    return $b['total'] <=> $a['total'];
});
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>SAW Results</title>
  <link href="assets/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
  <h2>SAW Final Ranking</h2>
  <table class="table table-bordered mt-3">
    <thead>
      <tr>
        <th>Rank</th>
        <th>Name</th>
        <th>Score</th>
      </tr>
    </thead>
    <tbody>
    <?php $rank = 1; foreach ($alternatif as $alt): ?>
      <tr>
        <td><?= $rank++ ?></td>
        <td><?= $alt['name'] ?></td>
        <td><?= round($alt['total'], 4) ?></td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
</div>
</body>
</html>
