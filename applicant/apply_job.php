<?php
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'applicant') {
    header("Location: ../register.php");
    exit;
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $job_id         = intval($_POST['id_job']);
    $reason         = mysqli_real_escape_string($conn, $_POST['reason']);
    $teamwork       = intval($_POST['teamwork']);
    $commitment     = intval($_POST['commitment']);
    $work_exp       = intval($_POST['work_experience']);

    // Cari id_applicant
    $q = mysqli_query($conn, "SELECT id_applicant FROM applicants WHERE user_id = $user_id");
    $row = mysqli_fetch_assoc($q);
    $id_applicant = $row['id_applicant'];

    $update = "UPDATE applicants 
               SET reason = '$reason', 
                   teamwork = $teamwork, 
                   commitment = $commitment, 
                   work_experience = $work_exp 
               WHERE id_applicant = $id_applicant";
    mysqli_query($conn, $update);

    $cek = mysqli_query($conn, "SELECT * FROM job_applications WHERE id_applicant = $id_applicant AND id_job = $job_id");
    if (mysqli_num_rows($cek) === 0) {
        mysqli_query($conn, "INSERT INTO job_applications (id_job, id_applicant) VALUES ($job_id, $id_applicant)");
    }

    echo "<script>alert('Application submitted!'); window.location = 'jobs.php';</script>";
    exit;
}
