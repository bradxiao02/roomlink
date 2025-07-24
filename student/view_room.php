<?php
session_start();
require_once "../config/db.php";

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'student') {
    header("Location: ../index.php");
    exit;
}

if (!isset($_GET['apartment'])) {
    echo "Apartment not specified.";
    exit;
}

$apartment = $_GET['apartment'];
$gender = $_SESSION['user']['gender'];
$student_id = $_SESSION['user']['id'];

// Check if student already has a booking
$hasBooking = false;
try {
    $stmt = $conn->prepare("SELECT * FROM bookings WHERE student_id = ? AND status = 'accepted'");
    $stmt->execute([$student_id]);
    if ($stmt->rowCount() > 0) {
        $hasBooking = true;
    }
} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage();
    exit;
}

try {
    $stmt = $conn->prepare("SELECT * FROM rooms WHERE apartment_name = ? AND gender = ? AND availability = 'available'");
    $stmt->execute([$apartment, $gender]);
    $rooms = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage();
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title><?php echo htmlspecialchars($apartment); ?> - Rooms</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #f7f7f7;
      padding: 20px;
    }
    h2 {
      text-align: center;
      color: #333;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 30px;
    }
    th, td {
      padding: 12px 15px;
      text-align: center;
      border-bottom: 1px solid #ddd;
    }
    th {
      background-color: #333;
      color: white;
    }
    tr:hover {
      background-color: #f1f1f1;
    }
    .btn {
      background-color: #ce1919ff;
      color: white;
      border: none;
      padding: 8px 15px;
      border-radius: 4px;
      cursor: pointer;
    }
    .btn:hover {
      background-color: #218838;
    }
    .btn[disabled] {
      background-color: gray;
      cursor: not-allowed;
    }
    .tooltip {
      position: relative;
      display: inline-block;
    }
    .tooltip .tooltiptext {
      visibility: hidden;
      width: 180px;
      background-color: black;
      color: #fff;
      text-align: center;
      border-radius: 6px;
      padding: 6px;
      position: absolute;
      z-index: 1;
      bottom: 125%;
      left: 50%;
      margin-left: -90px;
      opacity: 0;
      transition: opacity 0.3s;
      font-size: 13px;
    }
    .tooltip:hover .tooltiptext {
      visibility: visible;
      opacity: 1;
    }
  </style>
</head>
<body>

<h2><?php echo htmlspecialchars($apartment); ?> - Available Rooms</h2>


<?php if (count($rooms) > 0): ?>
  <table>
    <thead>
      <tr>
        <th>Image</th>
        <th>Room Number</th>
        <th>Gender</th>
        <th>Capacity</th>
        <th>Rent (KES)</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($rooms as $room): ?>
        <tr>
          <td>
            <?php if (!empty($room['image'])): ?>
              <img src="../uploads/<?php echo htmlspecialchars($room['image']); ?>" alt="Room Image" style="width: 80px; height: 60px; object-fit: cover; border-radius: 5px;">
            <?php else: ?>
              <span style="color: #999;">No image</span>
            <?php endif; ?>
          </td>
          <td><?php echo htmlspecialchars($room['room_number']); ?></td>
          <td><?php echo htmlspecialchars($room['gender']); ?></td>
          <td><?php echo htmlspecialchars($room['capacity']); ?></td>
          <td><?php echo number_format($room['price']); ?></td>
          <td>
            <?php if ($hasBooking): ?>
              <div class="tooltip">
                <button class="btn" disabled>Already Booked</button>
                <span class="tooltiptext">You have already booked a room</span>
              </div>
            <?php else: ?>
              <form action="payment.php" method="GET">
                <input type="hidden" name="room_id" value="<?= $room['id'] ?>">
                <button type="submit" class="btn">Book Now</button>
              </form>
            <?php endif; ?>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
<?php else: ?>
  <p style="text-align: center; color: #888;">No available rooms in this apartment at the moment.</p>
<?php endif; ?>

<p style="text-align: center; margin-top: 20px;">
    <a href="studentdashboard.php" style="color: #3498db; text-decoration: none;"> ← back to student Dashboard</a>
</p>


</body>
</html>
