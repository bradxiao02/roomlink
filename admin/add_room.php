<?php
session_start();
require_once "../config/db.php";

// Ensure admin access
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: ../index.php");
    exit;
}
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $apartment = trim($_POST['apartment_name']);
    $room_number = trim($_POST['room_number']);
    $gender = $_POST['gender'];
    $capacity = (int)$_POST['capacity'];
    $price = (float)$_POST['price'];

    $imagePath = null;

    // === Image Upload ===
    if (!empty($_FILES['image']['name'])) {
        $projectRoot = realpath(__DIR__ . '/../');  // Go up to RoomLink root
        $targetDir = $projectRoot . '/uploads/';
        $webPath = 'uploads/';

        if (!file_exists($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        $imageName = time() . '_' . basename($_FILES["image"]["name"]);
        $targetFile = $targetDir . $imageName;

        if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile)) {
            $imagePath = $webPath . $imageName;
        } else {
            echo "Image upload failed!";
        }
    }

    // === Insert into DB ===
    try {
        $stmt = $conn->prepare("INSERT INTO rooms (apartment_name, room_number, gender, capacity, price, image) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$apartment, $room_number, $gender, $capacity, $price, $imagePath]);
        header("Location: admin.php?success=1");
        exit();
    } catch (PDOException $e) {
        echo "Database error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Add Room - Admin</title>
  <style>
    body {
      background-color: #8d6262;
      font-family: Arial, sans-serif;
      color: #fff;
      padding: 20px;
    }
    h2 {
      color: #fff;
    }
    form {
      background: #2c2c2c;
      padding: 20px;
      border-radius: 10px;
      max-width: 500px;
      margin: auto;
    }
    label {
      display: block;
      margin-top: 15px;
    }
    input, select {
      width: 100%;
      padding: 8px;
      margin-top: 5px;
      border-radius: 5px;
      border: none;
    }
    button {
      margin-top: 20px;
      background-color: #a81c0a;
      color: white;
      border: none;
      padding: 10px 20px;
      border-radius: 5px;
      cursor: pointer;
    }
    button:hover {
      background-color: #218838;
    }
  </style>
</head>
<body>

  <h2>Add New Room</h2>

  <form action="add_room.php" method="POST" enctype="multipart/form-data">
    <label>Apartment Name:</label>
    <input type="text" name="apartment_name" required>

    <label>Room Number:</label>
    <input type="text" name="room_number" required>

    <label>Gender:</label>
    <select name="gender" required>
      <option value="male">Male</option>
      <option value="female">Female</option>
    </select>

    <label>Capacity:</label>
    <input type="number" name="capacity" min="1" required>

    <label>Price (KES):</label>
    <input type="number" name="price" step="0.01" required>

    <label>Room Image:</label>
    <input type="file" name="image" accept="image/*">

    <button type="submit">Add Room</button>
  </form>

</body>
</html>
