<?php
session_start();
require_once 'includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username      = $_POST['username'];
    $email         = $_POST['email'];
    $password      = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $full_name     = $_POST['full_name'];
    $phone_number  = $_POST['phone_number'];
    $birth_date    = $_POST['birth_date'];
    $address       = $_POST['address'];
    $job_id        = isset($_POST['job_id']) ? intval($_POST['job_id']) : null;

    // Upload CV
    $cv_name = $_FILES['cv']['name'];
    $cv_tmp  = $_FILES['cv']['tmp_name'];
    $cv_path = 'uploads/' . time() . '_' . $cv_name;
    move_uploaded_file($cv_tmp, $cv_path);

    $query1 = "INSERT INTO user (username, email, password, role) VALUES ('$username', '$email', '$password', 'applicant')";
    if (mysqli_query($conn, $query1)) {
        $user_id = mysqli_insert_id($conn);

        $query2 = "INSERT INTO applicants (user_id, job_id, full_name, phone_number, birth_date, address, cv_file, reason, teamwork, commitment, experience_years)
           VALUES ($user_id, $job_id, '$full_name', '$phone_number', '$birth_date', '$address', '$cv_path', '$reason', $teamwork, $commitment, $experience_years)"; 
        mysqli_query($conn, $query2);

        echo "<script>alert('Registration successful! Please log in.'); window.location='login.php';</script>";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Register</title>
  <link href="assets/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
  <h2 class="mb-4">Applicant Registration</h2>
  <form action="" method="POST" enctype="multipart/form-data" class="row g-3">
    <input type="hidden" name="job_id" value="<?= isset($_GET['job_id']) ? $_GET['job_id'] : '' ?>">

    <div class="col-md-6">
      <label>Username</label>
      <input type="text" name="username" class="form-control" required>
    </div>
    <div class="col-md-6">
      <label>Email</label>
      <input type="email" name="email" class="form-control" required>
    </div>
    <div class="col-md-6">
      <label>Password</label>
      <input type="password" name="password" class="form-control" required>
    </div>
    <div class="col-md-6">
      <label>Full Name</label>
      <input type="text" name="full_name" class="form-control" required>
    </div>
    <div class="col-md-6">
      <label>Phone Number</label>
      <input type="text" name="phone_number" class="form-control">
    </div>
    <div class="col-md-6">
      <label>Date of Birth</label>
      <input type="date" name="birth_date" class="form-control">
    </div>
    <div class="col-12">
      <label>Address</label>
      <textarea name="address" class="form-control"></textarea>
    </div>
    <div class="col-12">
      <label>Upload CV (PDF)</label>
      <input type="file" name="cv" accept=".pdf" class="form-control" required>
    </div>
    <div class="col-12">
      <button type="submit" class="btn btn-primary">Register</button>
    </div>
  </form>
</div>
</body>
</html>
