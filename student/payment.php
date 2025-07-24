<?php
// payment.php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();
require_once "../config/db.php";

// Redirect if not logged in as student
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'student') {
    header("Location: ../index.php");
    exit;
}

// Ensure room_id is passed correctly
if (!isset($_GET['room_id'])) {
    echo "Room ID missing.";
    exit;
}

$room_id = (int) $_GET['room_id'];
$student_id = $_SESSION['user']['id'];

// Check if student already has a pending or accepted booking
$stmt = $conn->prepare("SELECT * FROM bookings WHERE student_id = ? AND status IN ('pending', 'accepted')");
$stmt->execute([$student_id]);

if ($stmt->rowCount() > 0) {
    echo "<script>alert('You have already booked. Waiting for admin approval.'); window.location.href='studentdashboard.php';</script>";
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $payment_method = $_POST['payment_method'];
    $check_in = $_POST['check_in'];
    $check_out = $_POST['check_out'];

    // Validate check-in and check-out
    if (!$check_in || !$check_out || $check_out <= $check_in) {
        echo "<p style='color:red;'>❌ Check-out date must be after check-in date.</p>";
    } else {
        // Validate and upload receipt
        if (isset($_FILES['receipt']) && $_FILES['receipt']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = "../uploads/";
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            $originalName = basename($_FILES['receipt']['name']);
            $fileName = time() . "_" . $originalName;
            $targetPath = $uploadDir . $fileName;

            if (move_uploaded_file($_FILES['receipt']['tmp_name'], $targetPath)) {
                // Insert booking with dates
                $insert = $conn->prepare("INSERT INTO bookings 
                    (student_id, room_id, status, payment_method, receipt_file, payment_status, check_in, check_out) 
                    VALUES (?, ?, 'pending', ?, ?, 'paid', ?, ?)");
                $insert->execute([$student_id, $room_id, $payment_method, $fileName, $check_in, $check_out]);

                echo "<script>alert('Payment submitted successfully. You have already booked. Waiting for admin approval.'); window.location.href='studentdashboard.php';</script>";
                exit;
            } else {
                echo "<p style='color:red;'>❌ Error uploading the receipt file.</p>";
            }
        } else {
            echo "<p style='color:red;'>❌ Please upload a valid receipt image.</p>";
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Payment Page</title>
    <style>
        body { font-family: Arial; background: #f4f4f4; padding: 20px; }
        form { background: #fff; padding: 20px; border-radius: 10px; width: 400px; margin: auto; }
        input, select { width: 100%; padding: 10px; margin: 10px 0; }
        button { background: green; color: white; padding: 10px; border: none; width: 100%; }
    </style>
</head>
<body>

<h2 style="text-align:center;">Make Payment</h2>
<form action="" method="POST" enctype="multipart/form-data">
    <label>Check-in Date:</label>
    <input type="date" name="check_in" required><br>

    <label>Check-out Date:</label>
    <input type="date" name="check_out" required><br>

    <label>Payment Method:</label>
    <select name="payment_method" required>
        <option value="">Select method</option>
        <option value="Mpesa">Mpesa</option>
        <option value="Bank">Bank</option>
    </select><br>

    <label>Upload Payment Receipt (screenshot):</label>
    <input type="file" name="receipt" accept="image/*" required><br>

    <button type="submit" name="submit">Confirm</button>
</form>

</body>
</html>
