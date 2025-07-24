<?php
session_start();
require_once "../config/db.php";

// Ensure admin access
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: ../index.php");
    exit;
}

// Handle approval or rejection
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['booking_id'], $_POST['action'])) {
    $booking_id = $_POST['booking_id'];
    $action = $_POST['action'];

    $newStatus = $action === "approve" ? "accepted" : "rejected"; // match DB enum
    $stmt = $conn->prepare("UPDATE bookings SET status = ? WHERE id = ?");
    $stmt->execute([$newStatus, $booking_id]);

    // Update room availability if accepted
    if ($action === "approve") {
        $roomUpdate = $conn->prepare("UPDATE rooms SET availability = 'booked' WHERE id = (
            SELECT room_id FROM bookings WHERE id = ?
        )");
        $roomUpdate->execute([$booking_id]);
    }
}

// Get pending bookings
$stmt = $conn->query("
    SELECT 
        b.id, u.name AS student_name, b.student_id, r.room_number, 
        r.apartment_name, r.gender, b.status, b.payment_method, b.receipt_file AS payment_proof
    FROM bookings b
    JOIN users u ON b.student_id = u.id
    JOIN rooms r ON b.room_id = r.id
    WHERE b.status = 'pending'
");
$bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
  <title>Manage Bookings</title>
  <style>
    body { font-family: Arial; background: #f4f4f4; padding: 20px; }
    h2 { text-align: center; color: #333; }
    table { width: 95%; margin: auto; border-collapse: collapse; background: #fff; }
    th, td { border: 1px solid #ccc; padding: 10px; text-align: center; font-size: 14px; }
    th { background-color: #34495e; color: #fff; }
    img.receipt {
      max-width: 150px;
      height: auto;
      border-radius: 5px;
    }
    form button {
      padding: 6px 14px;
      border: none;
      border-radius: 5px;
      cursor: pointer;
    }
    .approve { background-color: #27ae60; color: white; }
    .reject { background-color: #c0392b; color: white; }
  </style>
</head>
<body>

<h2>Pending Bookings</h2>

<table>
  <tr>
    <th>Booking ID</th>
    <th>Student Name</th>
    <th>Student ID</th>
    <th>Apartment</th>
    <th>Room No</th>
    <th>Gender</th>
    <th>Payment Method</th>
    <th>Receipt</th>
    <th>Actions</th>
  </tr>
  <?php if ($bookings): ?>
    <?php foreach ($bookings as $booking): ?>
      <tr>
        <td><?= htmlspecialchars($booking['id']) ?></td>
        <td><?= htmlspecialchars($booking['student_name']) ?></td>
        <td><?= htmlspecialchars($booking['student_id']) ?></td>
        <td><?= htmlspecialchars($booking['apartment_name']) ?></td>
        <td><?= htmlspecialchars($booking['room_number']) ?></td>
        <td><?= htmlspecialchars($booking['gender']) ?></td>
        <td><?= htmlspecialchars($booking['payment_method']) ?></td>
        <td>
          <?php if ($booking['payment_proof']): ?>
            <a href="../uploads/<?= htmlspecialchars($booking['payment_proof']) ?>" target="_blank">
              <img src="../uploads/<?= htmlspecialchars($booking['payment_proof']) ?>" alt="Receipt" class="receipt">
            </a>
          <?php else: ?>
            <em>No receipt uploaded</em>
          <?php endif; ?>
        </td>
        <td>
          <form method="POST" style="display:inline;">
            <input type="hidden" name="booking_id" value="<?= $booking['id'] ?>">
            <button type="submit" name="action" value="approve" class="approve">Approve</button>
          </form>
          <form method="POST" style="display:inline;">
            <input type="hidden" name="booking_id" value="<?= $booking['id'] ?>">
            <button type="submit" name="action" value="reject" class="reject">Reject</button>
          </form>
        </td>
      </tr>
    <?php endforeach; ?>
  <?php else: ?>
    <tr><td colspan="9">No pending bookings.</td></tr>
  <?php endif; ?>
</table>

<p style="text-align:center; margin-top: 20px;">
  <a href="admin.php" style="text-decoration:none; color:#2980b9;">← Back to Admin Dashboard</a>
</p>

</body>
</html>
