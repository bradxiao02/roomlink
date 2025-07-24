<?php
session_start();
require_once "../config/db.php";

// Only students can book
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'student') {
    header("Location: ../index.php");
    exit;
}

$student_id = $_SESSION['user']['id'];

if (!isset($_POST['room_id'])) {
    echo "Room not selected.";
    exit;
}

$room_id = (int) $_POST['room_id'];

// 1. Check if student already booked any room
$check = $conn->prepare("SELECT * FROM bookings WHERE student_id = ?");
$check->execute([$student_id]);
if ($check->rowCount() > 0) {
    echo "<script>alert('You have already booked a room.'); window.location.href='studentdashboard.php';</script>";
    exit;
}

// 2. Check if room exists and has space
$roomCheck = $conn->prepare("
    SELECT r.*, 
           (SELECT COUNT(*) FROM bookings WHERE room_id = r.id) AS booked_count 
    FROM rooms r 
    WHERE r.id = ?
");
$roomCheck->execute([$room_id]);
$room = $roomCheck->fetch(PDO::FETCH_ASSOC);

if (!$room) {
    echo "<script>alert('Room not found.'); window.location.href='studentdashboard.php';</script>";
    exit;
}

// Check if the room is already full
if ($room['booked_count'] >= $room['capacity']) {
    echo "<script>alert('Room is already full.'); window.location.href='studentdashboard.php';</script>";
    exit;
}

// 3. Proceed to book
try {
    $conn->beginTransaction();

    // Insert booking with status = 'pending'
    $insert = $conn->prepare("INSERT INTO bookings (student_id, room_id, status) VALUES (?, ?, 'pending')");
    $insert->execute([$student_id, $room_id]);
    $booking_id = $conn->lastInsertId();

    $conn->commit();

    // ✅ Redirect to payment page with booking ID
    echo "<script>
        alert('Room booked successfully. Proceed to payment.');
        window.location.href='payment.php?booking_id={$booking_id}';
    </script>";
    exit;
} catch (Exception $e) {
    $conn->rollBack();
    echo "Booking failed: " . $e->getMessage();
}
