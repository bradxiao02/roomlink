<?php
session_start();
require_once "../config/db.php";

// Ensure admin access
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: ../index.php");
    exit;
}

// Fetch all rooms
$roomsStmt = $conn->prepare("SELECT * FROM rooms");
$roomsStmt->execute();
$rooms = $roomsStmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch all bookings (with student info)
$bookingsStmt = $conn->prepare("
    SELECT b.*, u.name AS student_name, r.apartment_name, r.room_number
    FROM bookings b
    JOIN users u ON b.student_id = u.id
    JOIN rooms r ON b.room_id = r.id
    ORDER BY b.created_at DESC
");
$bookingsStmt->execute();
$bookings = $bookingsStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard - RoomLink</title>
    <style>
        body {
            font-family: Arial;
            background: #f4f4f4;
            margin: 0;
        }
        header {
            background: #333;
            color: #fff;
            padding: 15px;
            text-align: center;
            position: relative;
        }
        footer {
            background: #333;
            color: #fff;
            padding: 15px;
            text-align: center;
            position: relative;
        }
        .logout-btn {
            position: absolute;
            right: 20px;
            top: 15px;
            background: #e74c3c;
            color: #fff;
            padding: 8px 15px;
            border: none;
            border-radius: 5px;
            text-decoration: none;
        }
        .btn-group {
            text-align: center;
            margin: 20px 0;
        }
        .btn-group a {
            padding: 10px 20px;
            background: #2ecc71;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 0 10px;
        }
        table {
            width: 90%;
            margin: 20px auto;
            border-collapse: collapse;
            background: white;
        }
        th, td {
            border: 1px solid #ccc;
            padding: 10px;
            text-align: center;
        }
        th {
            background: #222;
            color: white;
        }
        h2 {
            text-align: center;
        }
    </style>
</head>
<body>

<header>
    <h1>RoomLink Admin Panel</h1>
    <a href="../logout.php" class="logout-btn">Logout</a>
</header>

<div class="btn-group">
    <a href="add_room.php">Add Rooms</a>
    <a href="manage_bookings.php" style="background:#3498db;">Manage Bookings</a>
    <a href="manage_rooms.php" style="background: #f39c12;">Manage Rooms</a>

</div>

<h2>All Rooms</h2>
<table>
    <tr>
        <th>Apartment</th>
        <th>Room No</th>
        <th>Gender</th>
        <th>Capacity</th>
        <th>Booked</th>
    </tr>
    <?php foreach ($rooms as $room): ?>
    <tr>
        <td><?= htmlspecialchars($room['apartment_name']) ?></td>
        <td><?= htmlspecialchars($room['room_number']) ?></td>
        <td><?= htmlspecialchars(ucfirst($room['gender'])) ?></td>
        <td><?= htmlspecialchars($room['capacity']) ?></td>
        <td><?= ($room['availability']=== 'available')? 'No' : 'Yes' ?></td>
    </tr>
    <?php endforeach; ?>
</table>

<h2>Bookings Summary</h2>
<table>
    <tr>
        <th>Student</th>
        <th>Room</th>
        <th>Apartment</th>
        <th>Status</th>
        <th>Date</th>
    </tr>
    <?php foreach ($bookings as $booking): ?>
    <tr>
        <td><?= htmlspecialchars($booking['student_name']) ?></td>
        <td><?= htmlspecialchars($booking['room_number']) ?></td>
        <td><?= htmlspecialchars($booking['apartment_name']) ?></td>
        <td><?= ucfirst($booking['status']) ?></td>
        <td><?= htmlspecialchars($booking['created_at']) ?></td>
    </tr>
    <?php endforeach; ?>
</table>

<footer>
    <a href="../logout.php" class="logout-btn">Logout</a>

</footer>

</body>
</html>
