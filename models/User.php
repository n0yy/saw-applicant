<?php
class User {
    /**
     * Creates a new user in the database with the given name, email, and password.
     *
     * @param mysqli $conn
     * @param string $name
     * @param string $email
     * @param string $password
     * @return boolean
     */
    public static function create($conn, $name, $email, $password) {
        $stmt = $conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
        $hashed = password_hash($password, PASSWORD_BCRYPT);
        $stmt->bind_param("sss", $name, $email, $hashed);
        return $stmt->execute();
    }

    public static function findByEmail($conn, $email) {
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
}
?>
