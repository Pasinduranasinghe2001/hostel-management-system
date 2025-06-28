<?php
session_start();
require 'includes/db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'warden') {
    header("Location: login.php");
    exit();
}

// Use correct table name: student (not students)
$sql = "
    SELECT 
        r.room_number,
        r.room_type,
        r.capacity,
        r.ac_nonac,
        COUNT(s.student_id) AS occupied_count
    FROM room r
    LEFT JOIN student s ON s.room_number = r.room_number
    GROUP BY r.room_number, r.room_type, r.capacity, r.ac_nonac
    ORDER BY r.room_number;
";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Rooms</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(to bottom right, #e3f2fd, #ffffff);
            padding: 40px;
        }

        h2 {
            text-align: center;
            color: #2c3e50;
            margin-bottom: 30px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 20px rgba(0,0,0,0.08);
            overflow: hidden;
        }

        th, td {
            padding: 14px;
            border-bottom: 1px solid #ddd;
            text-align: center;
        }

        th {
            background-color: #2c3e50;
            color: white;
        }

        tr:hover {
            background-color: #f9f9f9;
        }

        .edit-btn {
            background-color: #3498db;
            color: white;
            padding: 8px 12px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: bold;
        }

        .edit-btn:hover {
            background-color: #2c80b4;
        }

        .back-btn {
            display: block;
            margin: 30px auto 0;
            width: max-content;
            background-color: #2ecc71;
            padding: 10px 22px;
            border-radius: 10px;
            color: white;
            font-weight: bold;
            text-decoration: none;
        }

        .back-btn:hover {
            background-color: #27ae60;
        }
    </style>
</head>
<body>

<h2>🛠️ Manage Hostel Rooms</h2>

<table>
    <tr>
        <th>Room Number</th>
        <th>Room Type</th>
        <th>Capacity</th>
        <th>Occupied</th>
        <th>AC / Non-AC</th>
        <th>Action</th>
    </tr>
    <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= htmlspecialchars($row['room_number']) ?></td>
            <td><?= htmlspecialchars($row['room_type']) ?></td>
            <td><?= htmlspecialchars($row['capacity']) ?></td>
            <td><?= htmlspecialchars($row['occupied_count']) ?></td>
            <td><?= htmlspecialchars($row['ac_nonac']) ?></td>
            <td>
                <a href="room_edit_form.php?room_number=<?= urlencode($row['room_number']) ?>" class="edit-btn">✏️ Edit</a>
            </td>
        </tr>
    <?php endwhile; ?>
</table>

<a href="warden_dashboard.php" class="back-btn">⬅ Back to Dashboard</a>

</body>
</html>
