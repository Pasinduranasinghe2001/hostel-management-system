<?php
session_start();
require 'includes/db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Get student_id from user_id
$stmt = $conn->prepare("SELECT student_id FROM student WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($student_id);
$stmt->fetch();
$stmt->close();

// Handle update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['visitor_id'])) {
    $visitor_id = $_POST['visitor_id'];
    $name = trim($_POST['visitor_name']);
    $datetime = $_POST['visit_datetime'];
    $purpose = trim($_POST['purpose']);

    $update = $conn->prepare("UPDATE visitor_log SET visitor_name = ?, visit_datetime = ?, purpose = ? WHERE visitor_id = ? AND student_id = ?");
    $update->bind_param("sssii", $name, $datetime, $purpose, $visitor_id, $student_id);
    $update->execute();
    $update->close();
}

// Fetch visitor logs
$stmt = $conn->prepare("SELECT * FROM visitor_log WHERE student_id = ? ORDER BY visit_datetime DESC");
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Visitors</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(to right, #fceabb, #f8b500);
            padding: 20px;
        }

        h2 {
            text-align: center;
            color: #444;
        }

        table {
            width: 95%;
            margin: auto;
            border-collapse: collapse;
            background: white;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }

        th, td {
            padding: 10px 14px;
            text-align: center;
            border: 1px solid #ccc;
        }

        th {
            background-color: #ff9800;
            color: white;
        }

        input, textarea {
            width: 100%;
            padding: 6px;
            border-radius: 4px;
            border: 1px solid #ccc;
        }

        button {
            padding: 6px 12px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover {
            background-color: #218838;
        }

        a {
            display: block;
            text-align: center;
            margin-top: 20px;
            text-decoration: none;
            color: #333;
            font-weight: bold;
        }
    </style>
</head>
<body>

<h2>My Visitor Logs</h2>

<table>
    <tr>
        <th>Name</th>
        <th>Date & Time</th>
        <th>Purpose</th>
        <th>Action</th>
    </tr>
    <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <form method="POST">
                <td><input type="text" name="visitor_name" value="<?= htmlspecialchars($row['visitor_name']) ?>" required></td>
                <td><input type="datetime-local" name="visit_datetime" value="<?= date('Y-m-d\TH:i', strtotime($row['visit_datetime'])) ?>" required></td>
                <td><textarea name="purpose" required><?= htmlspecialchars($row['purpose']) ?></textarea></td>
                <td>
                    <input type="hidden" name="visitor_id" value="<?= $row['visitor_id'] ?>">
                    <button type="submit">Update</button>
                </td>
            </form>
        </tr>
    <?php endwhile; ?>
</table>

<a href="student_dashboard.php">← Back to Dashboard</a>

</body>
</html>
