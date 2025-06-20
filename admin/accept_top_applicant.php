<?php
require_once '../includes/db.php';
session_start();

$job_id = intval($_GET['job_id']);

// Dapatkan top applicant
$query = "
    SELECT e.id_applicant, SUM(
        CASE
            WHEN c.type = 'Benefit' THEN e.score / (
                SELECT MAX(e2.score) FROM evaluations e2 WHERE e2.id_criteria = c.id_criteria
            )
            ELSE (
                SELECT MIN(e2.score) FROM evaluations e2 WHERE e2.id_criteria = c.id_criteria
            ) / e.score
        END * c.weight
    ) AS final_score
    FROM evaluations e
    JOIN criteria c ON c.id_criteria = e.id_criteria
    JOIN applicants a ON a.id_applicant = e.id_applicant
    WHERE a.job_id = $job_id
    GROUP BY e.id_applicant
    ORDER BY final_score DESC
    LIMIT 1";

$res = mysqli_query($conn, $query);
$row = mysqli_fetch_assoc($res);

if ($row) {
    $top_id = $row['id_applicant'];
    mysqli_query($conn, "UPDATE applicants SET status = 'accepted' WHERE id_applicant = $top_id");
}

header("Location: saw_result_job.php?id=$job_id");
