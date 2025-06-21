<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../models/User.php';
session_start();

class AuthController {
    /**
     * Handles user registration by creating a new user account.
     * 
     * Retrieves user details from POST request, checks if the email is 
     * already registered, and attempts to create a new user in the database.
     * 
     * Outputs success or failure message based on the registration outcome.
     */

    public static function register() {
        global $conn;
        $name = $_POST['name'];
        $email = $_POST['email'];
        $password = $_POST['password'];

        if (User::findByEmail($conn, $email)) {
            echo "Email sudah terdaftar.";
            return;
        }

        if (User::create($conn, $name, $email, $password)) {
            echo "Pendaftaran berhasil, tunggu approval admin.";
        } else {
            echo "Gagal daftar.";
        }
    }

    public static function login() {
        global $conn;
        $email = $_POST['email'];
        $password = $_POST['password'];

        $user = User::findByEmail($conn, $email);

        if ($user && password_verify($password, $user['password'])) {
            if (!$user['is_approved']) {
                echo "Akun belum di-approve admin.";
                return;
            }
            $_SESSION['user'] = [
                'id' => $user['id'],
                'name' => $user['name'],
                'role' => $user['role'],
            ];
            header("Location: ../views/{$user['role']}/dashboard.php");
        } else {
            echo "Email atau password salah.";
        }
    }
}

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if ($_POST['action'] === 'register') {
            AuthController::register();
        } elseif ($_POST['action'] === 'login') {
            AuthController::login();
        }
    }
    
?>



