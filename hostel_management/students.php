<?php
session_start();
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['admin', 'warden'])) {
    header("Location: login.php");
    exit;
}

require 'includes/db_connect.php';

$stmt = $conn->prepare("
    SELECT u.user_id, s.student_id, u.username, s.name, s.contact_info, s.duration_of_stay, s.room_number,
           w.name AS warden_name
    FROM user u 
    JOIN student s ON u.user_id = s.user_id
    LEFT JOIN room r ON s.room_number = r.room_number
    LEFT JOIN warden w ON r.warden_id = w.warden_id
    WHERE u.role = 'student'
");
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Students List</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(to bottom right, #d9f1ff, #ffffff);
            margin: 0;
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
            background-color: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 6px 16px rgba(0,0,0,0.1);
        }

        th, td {
            padding: 14px;
            border-bottom: 1px solid #ddd;
            text-align: center;
        }

        th {
            background-color: #2980b9;
            color: white;
        }

        tr:hover {
            background-color: #f4faff;
        }

        a.back {
            display: block;
            width: fit-content;
            margin: 25px auto 0;
            padding: 10px 20px;
            background: #2980b9;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: bold;
        }

        a.back:hover {
            background-color: #216aa4;
        }

        .del-btn {
            color: red;
            font-weight: bold;
            text-decoration: none;
        }

        .del-btn:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<h2>📘 Registered Students</h2>

<table>
    <tr>
        <th>User ID</th>
        <th>Username</th>
        <th>Full Name</th>
        <th>Contact</th>
        <th>Duration (months)</th>
        <th>Room</th>
        <?php if ($_SESSION['role'] === 'admin'): ?>
            <th>Warden</th>
        <?php endif; ?>
        <th>Action</th>
    </tr>
    <?php while ($student = $result->fetch_assoc()): ?>
        <tr>
            <td><?= htmlspecialchars($student['user_id']) ?></td>
            <td><?= htmlspecialchars($student['username']) ?></td>
            <td><?= htmlspecialchars($student['name']) ?></td>
            <td><?= htmlspecialchars($student['contact_info']) ?></td>
            <td><?= htmlspecialchars($student['duration_of_stay']) ?></td>
            <td><?= htmlspecialchars($student['room_number']) ?></td>
            <?php if ($_SESSION['role'] === 'admin'): ?>
                <td><?= htmlspecialchars($student['warden_name'] ?? 'Not Assigned') ?></td>
            <?php endif; ?>
            <td>
                <a class="del-btn"
                   href="delete_student.php?student_id=<?= $student['student_id'] ?>"
                   onclick="return confirm('Are you sure you want to delete this student permanently?');">❌ Delete</a>
            </td>
        </tr>
    <?php endwhile; ?>
</table>

<a class="back" href="<?= $_SESSION['role'] === 'admin' ? 'admin_dashboard.php' : 'warden_dashboard.php' ?>">⬅ Back to Dashboard</a>

</body>
</html>
