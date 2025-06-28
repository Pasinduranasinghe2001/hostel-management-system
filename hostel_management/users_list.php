<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

require 'includes/db_connect.php';

// Use mysqli connection ($conn)
$stmt = $conn->prepare("SELECT user_id, username, role FROM user");
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Registered Users</title>
    <link rel="stylesheet" href="css/users_list.css">
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(to bottom right, #d6f0ff, #f0faff);
            padding: 30px;
        }

        h2 {
            text-align: center;
            color: #2c3e50;
            margin-bottom: 20px;
        }

        table {
            border-collapse: collapse;
            width: 100%;
            background-color: #fff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 6px 10px rgba(0, 0, 0, 0.1);
        }

        th, td {
            padding: 12px 16px;
            text-align: center;
            border-bottom: 1px solid #ccc;
        }

        th {
            background-color: #3498db;
            color: white;
        }

        tr:hover {
            background-color: #f4faff;
        }

        a {
            display: block;
            width: fit-content;
            margin: 20px auto;
            padding: 10px 20px;
            background-color: #3498db;
            color: white;
            border-radius: 8px;
            text-decoration: none;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }

        a:hover {
            background-color: #217dbb;
        }
    </style>
</head>
<body>

<h2>Registered Users</h2>

<table>
    <tr>
        <th>User ID</th>
        <th>Username</th>
        <th>Role</th>
    </tr>
    <?php while ($user = $result->fetch_assoc()): ?>
        <tr>
            <td><?= htmlspecialchars($user['user_id']) ?></td>
            <td><?= htmlspecialchars($user['username']) ?></td>
            <td><?= htmlspecialchars($user['role']) ?></td>
        </tr>
    <?php endwhile; ?>
</table>

<a href="admin_dashboard.php">← Back to Dashboard</a>

<script src="js/users_list.js"></script>
</body>
</html>
