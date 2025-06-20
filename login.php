<?php
session_start();
require_once 'includes/db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];

    $query = "SELECT * FROM user WHERE username = '$username' LIMIT 1";
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) === 1) {
        $user = mysqli_fetch_assoc($result);
        
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id_user'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['username'] = $user['username'];

            if ($user['role'] === 'hr') {
                header('Location: admin');
            } else {
                header('Location: applicant');
            }
            exit;
        } else {
            $error = "Kata sandi salah.";
        }
    } else {
        $error = "Pengguna tidak ditemukan.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Masuk</title>
  <link href="assets/css/bootstrap.min.css"  rel="stylesheet">
  <style>
    body {
      background-color: #f8f9fa;
      display: flex;
      align-items: center;
      justify-content: center;
      min-height: 100vh;
    }
    .login-card {
      width: 100%;
      max-width: 450px;
      padding: 2rem;
      border-radius: 0.5rem;
      box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1);
    }
    .btn-primary {
      background-color: #0d6efd;
      border: none;
    }
  </style>
</head>
<body>

  <div class="login-card bg-white mx-auto">
    <h4 class="mb-4 text-center">Masuk ke Akun Anda</h4>

    <?php if (!empty($error)): ?>
      <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form action="" method="POST">
      
      <!-- Username -->
      <div class="mb-3">
        <label for="username" class="form-label">Username</label>
        <input type="text" name="username" id="username" class="form-control" placeholder="hennyyy0" required>
      </div>

      <!-- Password -->
      <div class="mb-3">
        <label for="password" class="form-label">Password</label>
        <input type="password" name="password" id="password" class="form-control" placeholder="********" required>
      </div>

      <!-- Submit Button -->
      <div class="d-grid mt-4">
        <button type="submit" class="btn btn-primary btn-lg">Masuk</button>
      </div>

      <!-- Register -->
      <div class="mt-2 text-center">
        <a href="register.php">Belum punya akun?</a>
      </div>
    </form>
  </div>

<script src="assets/js/bootstrap.bundle.min.js"></script> 
</body>
</html>