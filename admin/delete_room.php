<?php
session_start();
require_once "../config/db.php";

// Admin check
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: ../index.php");
    exit;
}

// Ensure room ID is provided
if (!isset($_GET['id'])) {
    echo "Room ID is missing.";
    exit;
}

$room_id = $_GET['id'];

// Optional: delete the image from the server
$stmt = $conn->prepare("SELECT image FROM rooms WHERE id = ?");
$stmt->execute([$room_id]);
$room = $stmt->fetch(PDO::FETCH_ASSOC);

if ($room && !empty($room['image'])) {
    $image_path = "../uploads/" . $room['image'];
    if (file_exists($image_path)) {
        unlink($image_path);
    }
}

// Delete the room
$delete = $conn->prepare("DELETE FROM rooms WHERE id = ?");
$delete->execute([$room_id]);

// Redirect back to manage_rooms.php
header("Location: manage_rooms.php");
exit;
?>
