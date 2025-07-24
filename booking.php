<?php
session_start();

// Initialize rooms (1–20) if not set
if (!isset($_SESSION['rooms'])) {
    $_SESSION['rooms'] = array_fill(1, 20, 'free'); // Room numbers 1-20 set to free
}

// Handle booking logic
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $room = $_POST['room'];

    if (isset($_POST['pay'])) {
        // Mark room as occupied after payment
        $_SESSION['rooms'][$room] = 'occupied';
        $message = "Room $room successfully booked!";
    } else {
        // Selecting a free room
        $selected_room = $room;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Room Booking (No DB)</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
        }
        .room {
            width: 50px;
            height: 50px;
            display: inline-block;
            margin: 5px;
            text-align: center;
            line-height: 50px;
            font-weight: bold;
            color: white;
            border-radius: 5px;
            cursor: pointer;
            border: none;
        }
        .free { background-color: green; }
        .occupied { background-color: blue; cursor: not-allowed; }
        .grid { margin-bottom: 30px; }
    </style>
</head>
<body>

<h2>Room Booking - Lenamoi Hostel</h2>

<?php if (!empty($message)): ?>
    <p style="color:green;"><strong><?= $message ?></strong></p>
<?php endif; ?>

<div class="grid">
    <?php
    foreach ($_SESSION['rooms'] as $number => $status) {
        if ($status == 'free') {
            echo "<form method='post' style='display:inline;'>
                    <input type='hidden' name='room' value='$number'>
                    <button class='room free'>$number</button>
                  </form>";
        } else {
            echo "<div class='room occupied'>$number</div>";
        }
    }
    ?>
</div>

<?php if (isset($selected_room)): ?>
    <h3>You selected Room <?= $selected_room ?></h3>
    <form method="post">
        <input type="hidden" name="room" value="<?= $selected_room ?>">
        <p>Select Payment Method:</p>
        <label><input type="radio" name="method" value="mpesa" checked> M-Pesa</label><br><br>
        <button type="submit" name="pay">Pay Now</button>
    </form>
<?php endif; ?>

</body>
</html>