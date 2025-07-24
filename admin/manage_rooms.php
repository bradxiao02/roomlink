<?php
session_start();
require_once "../config/db.php";

// Ensure only admin access
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: ../index.php");
    exit;
}

// Fetch all rooms
$stmt = $conn->prepare("SELECT * FROM rooms ORDER BY apartment_name, room_number");
$stmt->execute();
$rooms = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Rooms - Admin</title>
    <style>
        body { font-family: Arial; background: #f9f9f9; }
        h2 { text-align: center; margin-top: 30px; }
        table {
            width: 95%;
            margin: 20px auto;
            border-collapse: collapse;
            background: white;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        th, td {
            padding: 10px;
            text-align: center;
            border: 1px solid #ccc;
        }
        th {
            background: #2c3e50;
            color: white;
        }
        img {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 5px;
        }
        a.btn {
            padding: 6px 12px;
            background: #3498db;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            margin: 2px;
            display: inline-block;
        }
        a.btn.delete {
            background: #e74c3c;
        }
    </style>
</head>
<body>

<h2>Manage Rooms</h2>

<table>
    <tr>
        <th>Apartment</th>
        <th>Room #</th>
        <th>Gender</th>
        <th>Capacity</th>
        <th>Price (KES)</th>
        <th>Image</th>
        <th>Availability</th>
        <th>Actions</th>
    </tr>
    <?php foreach ($rooms as $room): ?>
    <tr>
        <td><?= htmlspecialchars($room['apartment_name']) ?></td>
        <td><?= htmlspecialchars($room['room_number']) ?></td>
        <td><?= ucfirst($room['gender']) ?></td>
        <td><?= $room['capacity'] ?></td>
        <td><?= number_format($room['price'], 2) ?></td>
        <td>
            <?php if (!empty($room['image'])): ?>
                <img src="../uploads/<?= htmlspecialchars($room['image']) ?>" alt="Room Image">
            <?php else: ?>
                No image
            <?php endif; ?>
        </td>
        <td style="color: <?= $room['availability'] === 'available' ? 'green' : 'red' ?>">
            <?= ucfirst($room['availability']) ?>
        </td>
        <td>
            <a class="btn" href="edit_room.php?id=<?= $room['id'] ?>">Edit</a>
            <a class="btn delete" href="delete_room.php?id=<?= $room['id'] ?>" onclick="return confirm('Are you sure you want to delete this room?')">Delete</a>
        </td>
    </tr>
    <?php endforeach; ?>
</table>

<p style="text-align: center; margin-top: 20px;">
    <a href="admin.php" style="color: #3498db; text-decoration: none;">← Back to Admin Dashboard</a>
</p>

</body>
</html>
