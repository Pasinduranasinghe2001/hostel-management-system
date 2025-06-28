<?php
session_start();
require 'includes/db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];

// Fetch payments
if ($role === 'student') {
    // Get student_id from user_id
    $stmt = $conn->prepare("SELECT student_id FROM student WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($student_id);
    $stmt->fetch();
    $stmt->close();

    if ($student_id) {
        $stmt = $conn->prepare("SELECT * FROM payment WHERE student_id = ? ORDER BY payment_date DESC");
        $stmt->bind_param("i", $student_id);
        $stmt->execute();
        $result = $stmt->get_result();
    } else {
        die("Student ID not found.");
    }
} elseif ($role === 'admin') {
    $result = $conn->query("SELECT * FROM payment ORDER BY payment_date DESC");
} elseif ($role === 'warden') {
    $stmt = $conn->prepare("
        SELECT p.* FROM payment p 
        JOIN student s ON p.student_id = s.student_id
        JOIN room r ON s.room_number = r.room_number
        WHERE r.warden_id = ?
    ");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    die("Access denied.");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Payments</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(to right, #e0f7fa, #e1f5fe);
            padding: 20px;
        }

        h2 {
            text-align: center;
            color: #00796b;
        }

        table {
            border-collapse: collapse;
            width: 90%;
            margin: 20px auto;
            background: white;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }

        th, td {
            padding: 12px 16px;
            text-align: center;
            border-bottom: 1px solid #ccc;
        }

        th {
            background-color: #00796b;
            color: white;
        }

        tr:hover {
            background-color: #f1f1f1;
        }

        a {
            display: block;
            text-align: center;
            margin-top: 30px;
            text-decoration: none;
            font-weight: bold;
            color: #00796b;
        }
    </style>
</head>
<body>

<h2>Payment Records</h2>

<?php if (isset($result) && $result->num_rows > 0): ?>
<table>
    <tr>
        <th>Payment ID</th>
        <th>Student ID</th>
        <th>Amount</th>
        <th>Penalty</th>
        <th>Status</th>
        <th>Date</th>
    </tr>
    <?php while ($p = $result->fetch_assoc()): ?>
    <tr>
        <td><?= htmlspecialchars($p['payment_id']) ?></td>
        <td><?= htmlspecialchars($p['student_id']) ?></td>
        <td>Rs. <?= number_format($p['amount'], 2) ?></td>
        <td>Rs. <?= number_format($p['penalty'], 2) ?></td>
        <td><?= htmlspecialchars($p['status']) ?></td>
        <td><?= htmlspecialchars($p['payment_date']) ?></td>
    </tr>
    <?php endwhile; ?>
</table>
<?php else: ?>
    <p style="text-align:center;">No payment records found.</p>
<?php endif; ?>

<a href="<?= ($role === 'admin') ? 'admin_dashboard.php' : (($role === 'warden') ? 'warden_dashboard.php' : 'student_dashboard.php') ?>">← Back to Dashboard</a>

</body>
</html>
