<?php
session_start();
require_once "../config/db.php";

// Check admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: ../index.php");
    exit;
}

if (!isset($_GET['id'])) {
    echo "Room ID missing.";
    exit;
}

$room_id = $_GET['id'];

// Get existing room data
$stmt = $conn->prepare("SELECT * FROM rooms WHERE id = ?");
$stmt->execute([$room_id]);
$room = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$room) {
    echo "Room not found.";
    exit;
}

// Handle update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $apartment_name = $_POST['apartment_name'];
    $room_number = $_POST['room_number'];
    $gender = $_POST['gender'];
    $capacity = $_POST['capacity'];
    $price = $_POST['price'];
    $availability = $_POST['availability'];

    $image = $room['image']; // default to old image

    // If new image is uploaded
    if (!empty($_FILES['image']['name'])) {
        $img_name = time() . '_' . $_FILES['image']['name'];
        $tmp = $_FILES['image']['tmp_name'];
        move_uploaded_file($tmp, "../uploads/$img_name");
        $image = $img_name;
    }

    $update = $conn->prepare("UPDATE rooms SET apartment_name=?, room_number=?, gender=?, capacity=?, price=?, image=?, availability=? WHERE id=?");
    $update->execute([$apartment_name, $room_number, $gender, $capacity, $price, $image, $availability, $room_id]);

    header("Location: manage_rooms.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Room</title>
    <style>
        body { font-family: Arial; background: #f2f2f2; padding: 20px; }
        form {
            width: 400px;
            margin: auto;
            background: white;
            padding: 20px;
            border-radius: 10px;
        }
        input, select {
            width: 100%;
            padding: 8px;
            margin: 8px 0 15px;
        }
        label {
            font-weight: bold;
        }
        img {
            width: 100px;
            height: 100px;
            object-fit: cover;
            margin-bottom: 10px;
            border-radius: 5px;
        }
        button {
            background: #3498db;
            color: white;
            padding: 10px;
            border: none;
            width: 100%;
            cursor: pointer;
        }
    </style>
</head>
<body>

<h2 style="text-align: center;">Edit Room</h2>

<form method="POST" enctype="multipart/form-data">
    <label>Apartment Name:</label>
    <input type="text" name="apartment_name" value="<?= htmlspecialchars($room['apartment_name']) ?>" required>

    <label>Room Number:</label>
    <input type="text" name="room_number" value="<?= htmlspecialchars($room['room_number']) ?>" required>

    <label>Gender:</label>
    <select name="gender" required>
        <option value="male" <?= $room['gender'] === 'male' ? 'selected' : '' ?>>Male</option>
        <option value="female" <?= $room['gender'] === 'female' ? 'selected' : '' ?>>Female</option>
    </select>

    <label>Capacity:</label>
    <input type="number" name="capacity" value="<?= $room['capacity'] ?>" required>

    <label>Price (KES):</label>
    <input type="number" name="price" step="0.01" value="<?= $room['price'] ?>" required>

    <label>Availability:</label>
    <select name="availability" required>
        <option value="available" <?= $room['availability'] === 'available' ? 'selected' : '' ?>>Available</option>
        <option value="booked" <?= $room['availability'] === 'booked' ? 'selected' : '' ?>>Booked</option>
    </select>

    <label>Current Image:</label><br>
    <?php if (!empty($room['image'])): ?>
        <img src="../uploads/<?= htmlspecialchars($room['image']) ?>" alt="Room Image"><br>
    <?php else: ?>
        <span>No image uploaded</span><br>
    <?php endif; ?>

    <label>Upload New Image (optional):</label>
    <input type="file" name="image" accept="image/*">

    <button type="submit">Update Room</button>
</form>

<p style="text-align: center; margin-top: 20px;">
    <a href="manage_rooms.php" style="color: #3498db; text-decoration: none;"> ← back to Manage Rooms</a>
</p>


</body>
</html>
