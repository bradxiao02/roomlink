<?php
session_start();
$hostel = isset($_GET['hostel']) ? htmlspecialchars($_GET['hostel']) : 'Unknown Hostel';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title><?php echo $hostel; ?> - Rooms | RoomLink</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background: #f4f4f4;
      margin: 0;
      padding: 0;
    }

    header {
      background-color: #020a01ff;
      color: #fff;
      padding: 15px;
      text-align: center;
    }

    .container {
      padding: 20px;
    }

    h2 {
      color: #333;
      margin-bottom: 10px;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 15px;
    }

    th, td {
      padding: 12px;
      border: 1px solid #ccc;
      text-align: center;
    }

    th {
      background-color: #8d6262;
      color: white;
    }

    tr:nth-child(even) {
      background-color: #f9f9f9;
    }

    input[type="file"] {
      padding: 5px;
    }

    .upload-btn {
      background-color: #ce1919ff;
      color: white;
      padding: 6px 12px;
      border: none;
      border-radius: 5px;
      cursor: pointer;
    }

    .upload-btn:hover {
      background-color: #218838;
    }

    .back-link {
      display: inline-block;
      margin-top: 15px;
      background: #333;
      color: white;
      padding: 8px 16px;
      border-radius: 5px;
      text-decoration: none;
    }

    .back-link:hover {
      background: #555;
    }
  </style>
</head>
<body>

<header>
  <h1>RoomLink Kabarak</h1>
  <h2>Rooms in <?php echo $hostel; ?></h2>
</header>

<div class="container">
  <table>
    <thead>
      <tr>
        <th>Room Code</th>
        <th>Type</th>
        <th>Status</th>
        <th>Booking Start</th>
        <th>Booking End</th>
        <th>Payment/Sem</th>
        <th>Upload Payment Proof</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td>R101</td>
        <td>Single</td>
        <td>Available</td>
        <td>2025-08-01</td>
        <td>2025-12-01</td>
        <td>18,000 KES</td>
        <td>
          <form method="post" enctype="multipart/form-data">
            <input type="file" name="proof">
            <button type="submit" class="uploadbtn">Upload</button>
          </form>
        </td>
      </tr>
      <tr>
        <td>R102</td>
        <td>Double</td>
        <td>Booked</td>
        <td>2025-08-01</td>
        <td>2025-12-01</td>
        <td>15,000 KES</td>
        <td>
          <form method="post" enctype="multipart/form-data">
            <input type="file" name="proof">
            <button type="submit" class="upload-btn">Upload</button>
          </form>
        </td>
      </tr>
      <!-- Add more rooms as needed -->
    </tbody>
  </table>

  <a href="studentdashboard.php" class="back-link">← Back to Dashboard</a>
</div>

</body>
</html>
