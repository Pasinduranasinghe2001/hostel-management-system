<?php
session_start();
require 'includes/db_connect.php';

if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['admin', 'warden'])) {
    header("Location: login.php");
    exit;
}

$role = $_SESSION['role'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $complaint_id = $_POST['complaint_id'];
    $new_status = $_POST['status'];
    $remarks = $_POST['remarks'];

    $stmt = $conn->prepare("UPDATE complaint SET status = ?, remarks = ? WHERE complaint_id = ?");
    $stmt->bind_param("ssi", $new_status, $remarks, $complaint_id);
    $stmt->execute();
    $stmt->close();
}

$stmt = $conn->prepare("
    SELECT c.*, s.name AS student_name
    FROM complaint c
    JOIN student s ON c.student_id = s.student_id
    ORDER BY c.date DESC
");
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>All Complaints</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(to right, #e0f7fa, #ffffff);
            padding: 40px;
            margin: 0;
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
            box-shadow: 0 8px 16px rgba(0,0,0,0.1);
            border-radius: 10px;
            overflow: hidden;
        }

        th, td {
            padding: 14px;
            text-align: center;
            border-bottom: 1px solid #e0e0e0;
        }

        th {
            background-color: #3498db;
            color: white;
        }

        tr:hover {
            background-color: #f9f9f9;
        }

        select, textarea {
            padding: 8px;
            font-size: 14px;
            width: 100%;
            border: 1px solid #ccc;
            border-radius: 6px;
            box-sizing: border-box;
        }

        textarea {
            resize: vertical;
        }

        button {
            background-color: #27ae60;
            color: white;
            padding: 8px 14px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: bold;
        }

        button:hover {
            background-color: #1e8449;
        }

        .back {
            margin-top: 30px;
            text-align: center;
        }

        .back a {
            display: inline-block;
            text-decoration: none;
            background-color: #3498db;
            color: white;
            padding: 10px 18px;
            border-radius: 6px;
            font-weight: bold;
        }

        .back a:hover {
            background-color: #2980b9;
        }
    </style>
</head>
<body>

<h2>All Student Complaints</h2>

<table>
    <tr>
        <th>Date</th>
        <th>Student</th>
        <th>Type</th>
        <th>Description</th>
        <th>Status</th>
        <th>Remarks</th>
        <th>Action</th>
    </tr>

    <?php while ($row = $result->fetch_assoc()): ?>
    <tr>
        <form method="POST">
            <td><?= htmlspecialchars($row['date']) ?></td>
            <td><?= htmlspecialchars($row['student_name']) ?></td>
            <td><?= htmlspecialchars($row['type']) ?></td>
            <td><?= htmlspecialchars($row['description']) ?></td>
            <td>
                <select name="status" required>
                    <option value="Pending" <?= $row['status'] === 'Pending' ? 'selected' : '' ?>>Pending</option>
                    <option value="In Progress" <?= $row['status'] === 'In Progress' ? 'selected' : '' ?>>In Progress</option>
                    <option value="Resolved" <?= $row['status'] === 'Resolved' ? 'selected' : '' ?>>Resolved</option>
                </select>
            </td>
            <td>
                <textarea name="remarks" rows="2"><?= htmlspecialchars($row['remarks']) ?></textarea>
            </td>
            <td>
                <input type="hidden" name="complaint_id" value="<?= $row['complaint_id'] ?>">
                <button type="submit">Update</button>
            </td>
        </form>
    </tr>
    <?php endwhile; ?>
</table>

<div class="back">
    <a href="<?= $role === 'admin' ? 'admin_dashboard.php' : 'warden_dashboard.php' ?>">⬅ Back to Dashboard</a>
</div>

</body>
</html>
