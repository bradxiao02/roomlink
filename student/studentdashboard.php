<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once "../config/db.php";

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'student') {
    header("Location: ../index.php");
    exit;
}

$gender = $_SESSION['user']['gender'];
$name   = $_SESSION['user']['name'];
$id     = $_SESSION['user']['id'];

// ✅ Get unique apartment names
$stmt = $conn->prepare("SELECT DISTINCT apartment_name FROM rooms WHERE gender = ?");
$stmt->execute([$gender]);
$hostels = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ✅ Check for accepted booking
$approvedStmt = $conn->prepare("
    SELECT b.*, r.room_number, r.apartment_name
    FROM bookings b
    JOIN rooms r ON b.room_id = r.id
    WHERE b.student_id = ? AND b.status = 'accepted'
    ORDER BY b.created_at DESC
    LIMIT 1
");
$approvedStmt->execute([$id]);
$approvedBooking = $approvedStmt->fetch(PDO::FETCH_ASSOC);

// ✅ Show popup only once per session
$showPopup = $approvedBooking && empty($_SESSION['popup_shown']);
if ($showPopup) {
    $_SESSION['popup_shown'] = true;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Student Dashboard - RoomLink</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background: #8d6262ff;
      margin: 0;
    }
    header {
      background-color: #020a01ff;
      color: #a81c0aff;
      padding: 15px;
      text-align: center;
      position: relative;
    }
    .header-buttons {
      position: absolute;
      top: 15px;
      right: 20px;
    }
    .header-buttons button {
      margin-left: 10px;
      background-color: #007bff;
      color: white;
      padding: 8px 14px;
      border: none;
      border-radius: 5px;
      cursor: pointer;
    }
    .header-buttons button:hover {
      background-color: #0056b3;
    }
    .hostels {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 20px;
      padding: 20px;
    }
    .card {
      background: white;
      border-radius: 10px;
      box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
      padding: 20px;
      text-align: center;
    }
    .card h3 {
      margin-bottom: 10px;
      color: #333;
    }
    .card button {
      margin-top: 10px;
      background-color: #ce1919ff;
      color: white;
      border: none;
      padding: 8px 18px;
      border-radius: 5px;
      cursor: pointer;
    }
    .card button:hover {
      background-color: #218838;
    }
  </style>
</head>
<body>

<header>
  <h1>RoomLink Kabarak</h1>
  <h3>Welcome, <?php echo htmlspecialchars($name); ?> (<?php echo htmlspecialchars($id); ?>)</h3>
  <div class="header-buttons">
    <?php if ($approvedBooking): ?>
      <button onclick="document.getElementById('bookingDetails').style.display='flex'">View Booking Details</button>
    <?php endif; ?>
    <button onclick="window.location.href='../logout.php'">Logout</button>
  </div>
</header>

<div class="hostels">
  <?php if (count($hostels) > 0): ?>
    <?php foreach ($hostels as $hostel): ?>
      <div class="card">
        <h3><?php echo htmlspecialchars($hostel['apartment_name']); ?></h3>
        <a href="view_room.php?apartment=<?php echo urlencode($hostel['apartment_name']); ?>">
          <button>View Rooms</button>
        </a>
      </div>
    <?php endforeach; ?>
  <?php else: ?>
    <p style="text-align:center; color:white;">No hostels available for your gender.</p>
  <?php endif; ?>
</div>

<?php if ($approvedBooking): ?>
  <!-- Modal -->
  <div id="bookingDetails" style="
    display: none;
    position: fixed;
    top: 0; left: 0; right: 0; bottom: 0;
    background: rgba(0,0,0,0.6);
    z-index: 9999;
    align-items: center;
    justify-content: center;
  ">
    <div style="
      background: white;
      padding: 25px;
      border-radius: 10px;
      width: 90%;
      max-width: 500px;
      text-align: center;
    ">
      <h3>Booking Approved 🎉</h3>
      <p><strong>Room:</strong> <?php echo htmlspecialchars($approvedBooking['room_number']); ?></p>
      <p><strong>Apartment:</strong> <?php echo htmlspecialchars($approvedBooking['apartment_name']); ?></p>
      <?php if ($approvedBooking['check_in'] && $approvedBooking['check_out']): ?>
        <p><strong>Check-in:</strong> <?php echo htmlspecialchars($approvedBooking['check_in']); ?></p>
        <p><strong>Check-out:</strong> <?php echo htmlspecialchars($approvedBooking['check_out']); ?></p>
      <?php endif; ?>
      <p><strong>Status:</strong> <?php echo htmlspecialchars($approvedBooking['status']); ?></p>

      <button onclick="document.getElementById('bookingDetails').style.display='none'" style="
        margin-top: 20px;
        padding: 10px 18px;
        background-color: #28a745;
        color: white;
        border: none;
        border-radius: 5px;
        cursor: pointer;
      ">
        Close
      </button>
    </div>
  </div>

  <?php if ($showPopup): ?>
    <script>
      window.onload = function() {
        document.getElementById('bookingDetails').style.display = 'flex';
      };
    </script>
  <?php endif; ?>
<?php endif; ?>

</body>
</html>
