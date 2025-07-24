<?php
session_start();
require_once "../config/db.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id = $_POST['user_id_number'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $role = $_POST['role'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $gender = $role === 'admin' ? 'N/A' : $_POST['gender'];

    try {
        // Check if user with this ID already exists
        $checkStmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
        $checkStmt->execute([$id]);

        if ($checkStmt->rowCount() > 0) {
            echo "User already exists!";
            exit();
        }

        // Insert new user
        $stmt = $conn->prepare("INSERT INTO users (id, name, email, password, gender, role, created_at)
                                VALUES (?, ?, ?, ?, ?, ?, NOW())");
        $stmt->execute([$id, $name, $email, $password, $gender, $role]);

        // Redirect to login after successful registration
        header("Location: ../index.php");
        exit();
    } catch (PDOException $e) {
        echo "Database error: " . $e->getMessage();
    }
}
?>
