<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'warden') {
    header("Location: login.php");
    exit;
}
require 'includes/db_connect.php';

$stmt = $conn->prepare("
    SELECT v.*, s.name AS student_name 
    FROM visitor_log v
    LEFT JOIN student s ON v.student_id = s.student_id
    ORDER BY v.visit_datetime DESC
");
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Visitor Log</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(to bottom right, #b3e5fc, #e1f5fe);
            padding: 30px;
        }

        h2 {
            text-align: center;
            color: #01579b;
            margin-bottom: 30px;
        }

        table {
            width: 90%;
            margin: auto;
            border-collapse: collapse;
            background-color: white;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
        }

        th, td {
            padding: 12px 16px;
            text-align: center;
            border-bottom: 1px solid #ccc;
        }

        th {
            background-color: #0288d1;
            color: white;
        }

        tr:hover {
            background-color: #f1f1f1;
        }

        a {
            display: block;
            width: fit-content;
            margin: 40px auto 0;
            text-decoration: none;
            color: white;
            background-color: #0288d1;
            padding: 10px 20px;
            border-radius: 6px;
            font-weight: bold;
        }

        a:hover {
            background-color: #0277bd;
        }
    </style>
</head>
<body>

<h2>Visitor Log</h2>

<table>
    <tr>
        <th>Visitor ID</th>
        <th>Visitor Name</th>
        <th>Visit Date & Time</th>
        <th>Student Name</th>
        <th>Purpose</th>
    </tr>
    <?php while ($visitor = $result->fetch_assoc()): ?>
    <tr>
        <td><?= htmlspecialchars($visitor['visitor_id']) ?></td>
        <td><?= htmlspecialchars($visitor['visitor_name']) ?></td>
        <td><?= htmlspecialchars($visitor['visit_datetime']) ?></td>
        <td><?= htmlspecialchars($visitor['student_name']) ?></td>
        <td><?= htmlspecialchars($visitor['purpose']) ?></td>
    </tr>
    <?php endwhile; ?>
</table>

<a href="admin_dashboard.php">← Back to Dashboard</a>

</body>
</html>
