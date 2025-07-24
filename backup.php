<?php
session_start();

// Initialize room statuses
if (!isset($_SESSION['rooms'])) {
    $_SESSION['rooms'] = [];
    for ($i = 1; $i <= 20; $i++) {
        if ($i <= 10) {
            $_SESSION['rooms'][$i] = false; // not booked yet
        } else {
            $_SESSION['rooms'][$i] = ['student1' => false, 'student2' => false];
        }
    }
}

$messages = [];
$selected_room = null;

// Handle logout
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: " . strtok($_SERVER["REQUEST_URI"], '?')); // reload without params
    exit;
}

// Handle uploads
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $room = (int)$_POST['room'];
    $selected_room = $room;

    // Single room proof
    if ($room <= 10 && isset($_FILES['proof']) && $_FILES['proof']['error'] === UPLOAD_ERR_OK) {
        $_SESSION['rooms'][$room] = true;
        $messages[] = "Payment uploaded for Room " . str_pad($room, 3, '0', STR_PAD_LEFT) . ".";
    }

    // Double room proofs
    if ($room > 10) {
        if (isset($_FILES['proof1']) && $_FILES['proof1']['error'] === UPLOAD_ERR_OK) {
            $_SESSION['rooms'][$room]['student1'] = true;
            $messages[] = "Payment uploaded for Student 1 in Room " . str_pad($room, 3, '0', STR_PAD_LEFT) . ".";
        }
        if (isset($_FILES['proof2']) && $_FILES['proof2']['error'] === UPLOAD_ERR_OK) {
            $_SESSION['rooms'][$room]['student2'] = true;
            $messages[] = "Payment uploaded for Student 2 in Room " . str_pad($room, 3, '0', STR_PAD_LEFT) . ".";
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Room Booking Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f4f4;
            padding: 0;
            margin: 0;
        }

        header {
            background: #020a01ff;
            color: white;
            padding: 10px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        
        }

        header .logo {
            display: flex;
            align-items: center;
        }

        header img {
            height: 40px;
            margin-right: 10px;
        }

        header h1 {
            margin: 0;
            font-size: 20px;
        }

        header a.logout-btn {
            color: white;
            text-decoration: none;
            background: #70cc1aff;
            padding: 6px 12px;
            border-radius: 5px;
            font-weight: bold;
        }

        .content {
            padding: 20px;
        }

        h2 {
            color: #222;
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

        .free { background-color: red; }
        .occupied { background-color: blue; }
        .partially { background-color: orange; }

        .grid { margin-bottom: 30px; }

        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            margin-top: 20px;
        }

        th, td {
            padding: 12px;
            border: 1px solid #ccc;
            text-align: center;
        }

        th {
            background-color: #333;
            color: white;
        }

        .upload-btn {
            background-color: #ce1919ff;
            color: white;
            padding: 6px 12px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
    </style>
</head>
<body>

<!-- Header with Logo and Logout -->
<header>
    <div class="logo">
        <img src="logo.png" alt="RoomLink Logo" />
        <h1>RoomLink Kabarak Dashboard</h1>
    </div>
    <a href="?logout=1" class="logout-btn">Log out</a>
</header>

<div class="content">
    <h2>Lenamoi Hostel Room Booking</h2>

    <?php if (!empty($messages)): ?>
        <div style="color:green;">
            <?php foreach ($messages as $msg): ?>
                <p><strong><?= $msg ?></strong></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <!-- ROOM SELECTION -->
    <div class="grid">
        <?php
        foreach ($_SESSION['rooms'] as $number => $status) {
            $btnClass = 'free';
            if ($number <= 10 && $status === true) $btnClass = 'occupied';
            if ($number > 10) {
                $booked = $status['student1'] + $status['student2'];
                if ($booked === 1) $btnClass = 'partially';
                if ($booked === 2) $btnClass = 'occupied';
            }

            echo "<form method='post' style='display:inline;'>
                    <input type='hidden' name='room' value='$number'>
                    <button class='room $btnClass'>".str_pad($number, 2, '0', STR_PAD_LEFT)."</button>
                  </form>";
        }
        ?>
    </div>

    <!-- DASHBOARDS -->
    <?php if ($selected_room): ?>
        <?php if ($selected_room <= 10): ?>
            <!-- SINGLE ROOM -->
            <h3>Single Room Dashboard - Room <?= str_pad($selected_room, 3, '0', STR_PAD_LEFT) ?></h3>
            <table>
                <thead>
                    <tr>
                        <th>Room</th>
                        <th>Type</th>
                        <th>Status</th>
                        <th>Booking Start</th>
                        <th>Booking End</th>
                        <th>Payment</th>
                        <th>Upload Proof</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>R<?= str_pad($selected_room, 3, '0', STR_PAD_LEFT) ?></td>
                        <td>Single</td>
                        <td><?= $_SESSION['rooms'][$selected_room] ? 'Booked' : 'Available' ?></td>
                        <td>2025-08-01</td>
                        <td>2025-12-01</td>
                        <td>18,000 KES</td>
                        <td>
                            <?php if (!$_SESSION['rooms'][$selected_room]): ?>
                                <form method="post" enctype="multipart/form-data">
                                    <input type="hidden" name="room" value="<?= $selected_room ?>">
                                    <input type="file" name="proof" required>
                                    <button type="submit" class="upload-btn">Upload</button>
                                </form>
                            <?php else: ?>
                                ✔ Payment Uploaded
                            <?php endif; ?>
                        </td>
                    </tr>
                </tbody>
            </table>
        <?php else: ?>
            <!-- DOUBLE ROOM -->
            <h3>Double Room Dashboard - Room <?= str_pad($selected_room, 3, '0', STR_PAD_LEFT) ?></h3>
            <table>
                <thead>
                    <tr>
                        <th>Room</th>
                        <th>Type</th>
                        <th>Status</th>
                        <th>Booking Start</th>
                        <th>Booking End</th>
                        <th>Payment</th>
                        <th>Student</th>
                        <th>Upload Proof</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $booking = $_SESSION['rooms'][$selected_room];
                    $bookedCount = ($booking['student1'] ? 1 : 0) + ($booking['student2'] ? 1 : 0);
                    $status = $bookedCount === 0 ? 'Available' : ($bookedCount === 1 ? 'Partially Booked' : 'Booked');
                    ?>
                    <tr>
                        <td rowspan="2">R<?= str_pad($selected_room, 3, '0', STR_PAD_LEFT) ?></td>
                        <td rowspan="2">Double</td>
                        <td rowspan="2"><?= $status ?></td>
                        <td rowspan="2">2025-08-01</td>
                        <td rowspan="2">2025-12-01</td>
                        <td rowspan="2">15,000 KES</td>
                        <td>Student 1</td>
                        <td>
                            <?php if (!$booking['student1']): ?>
                                <form method="post" enctype="multipart/form-data">
                                    <input type="hidden" name="room" value="<?= $selected_room ?>">
                                    <input type="file" name="proof1" required>
                                    <button type="submit" class="upload-btn">Upload</button>
                                </form>
                            <?php else: ?>✔ Uploaded<?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <td>Student 2</td>
                        <td>
                            <?php if (!$booking['student2']): ?>
                                <form method="post" enctype="multipart/form-data">
                                    <input type="hidden" name="room" value="<?= $selected_room ?>">
                                    <input type="file" name="proof2" required>
                                    <button type="submit" class="upload-btn">Upload</button>
                                </form>
                            <?php else: ?>✔ Uploaded<?php endif; ?>
                        </td>
                    </tr>
                </tbody>
            </table>
        <?php endif; ?>
    <?php endif; ?>
</div>

</body>
</html>
