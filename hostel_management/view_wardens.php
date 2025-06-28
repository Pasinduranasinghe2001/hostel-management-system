<?php
session_start();
require 'includes/db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$query = "SELECT name, contact_info, assigned_hostel, user_id FROM warden ORDER BY name";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Wardens</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(to right, #eef2f3, #8e9eab);
            padding: 40px;
        }

        h2 {
            text-align: center;
            color: #34495e;
            margin-bottom: 30px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background-color: white;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
            overflow: hidden;
        }

        th, td {
            padding: 14px;
            text-align: center;
            border-bottom: 1px solid #ccc;
        }

        th {
            background-color: #2c3e50;
            color: white;
        }

        tr:hover {
            background-color: #f5f5f5;
        }

        .delete-link {
            color: red;
            font-weight: bold;
            text-decoration: none;
        }

        .delete-link:hover {
            text-decoration: underline;
        }

        .back-btn {
            display: block;
            width: fit-content;
            margin: 30px auto 0;
            padding: 10px 20px;
            background-color: #27ae60;
            color: white;
            font-weight: bold;
            text-decoration: none;
            border-radius: 8px;
            transition: background-color 0.3s ease;
        }

        .back-btn:hover {
            background-color: #1e8449;
        }
    </style>
</head>
<body>

<h2>🧑‍🏫 Warden Directory</h2>

<table>
    <tr>
        <th>Name</th>
        <th>Contact Info</th>
        <th>Assigned Hostel</th>
        <th>Action</th>
    </tr>
    <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= htmlspecialchars($row['name']) ?></td>
            <td><?= htmlspecialchars($row['contact_info']) ?></td>
            <td><?= htmlspecialchars($row['assigned_hostel'] ?: 'Not Assigned') ?></td>
            <td>
                <a href="delete_warden.php?user_id=<?= $row['user_id'] ?>"
                   class="delete-link"
                   onclick="return confirm('Delete this warden permanently?')">❌ Delete</a>
            </td>
        </tr>
    <?php endwhile; ?>
</table>

<a href="admin_dashboard.php" class="back-btn">⬅ Back to Dashboard</a>

</body>
</html>
