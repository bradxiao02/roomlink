<?php
session_start();
require_once "../config/db.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $user_id_number = trim($_POST['user_id_number']);
    $password = $_POST['password'];
    $role = $_POST['role'];

    try {
        $stmt = $conn->prepare("SELECT * FROM users WHERE id = ? AND role = ?");
        $stmt->execute([$user_id_number, $role]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            // Set all relevant session values
            $_SESSION['user'] = [
                'id' => $user['id'],
                'name' => $user['name'],
                'role' => $user['role'],
                'gender' => $user['gender'] ?? null
            ];

            // Redirect based on role
            if ($user['role'] === 'admin') {
                header("Location: ../admin/admin.php");
            } else {
                header("Location: ../student/studentdashboard.php");
            }
            exit();
        } else {
            echo "Invalid ID number or password.";
        }

    } catch (PDOException $e) {
        echo "Database error: " . $e->getMessage();
    }
}
?>
