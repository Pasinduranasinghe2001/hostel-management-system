<?php
session_start();
require 'includes/db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];

// Handle update if admin submits changes
if ($role === 'admin' && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['payment_id'])) {
    $payment_id = $_POST['payment_id'];
    $new_status = $_POST['status'];
    $new_penalty = floatval($_POST['penalty']);

    $stmt = $conn->prepare("UPDATE payment SET status = ?, penalty = ? WHERE payment_id = ?");
    $stmt->bind_param("sdi", $new_status, $new_penalty, $payment_id);
    $stmt->execute();
    $stmt->close();
}

// Fetch payments
if ($role === 'student') {
    $stmt = $conn->prepare("SELECT student_id FROM student WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($student_id);
    $stmt->fetch();
    $stmt->close();

    $stmt = $conn->prepare("SELECT * FROM payment WHERE student_id = ? ORDER BY payment_date DESC");
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
    $result = $stmt->get_result();
} elseif ($role === 'admin') {
    $result = $conn->query("SELECT * FROM payment ORDER BY payment_date DESC");
} else {
    echo "Access Denied.";
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Payments</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(to right, #f0f9ff, #e0f7fa);
            padding: 20px;
        }

        h2 {
            text-align: center;
            color: #00796b;
        }

        table {
            border-collapse: collapse;
            width: 95%;
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

        input[type="number"], select {
            padding: 6px;
            width: 100px;
            border-radius: 5px;
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
            margin-top: 30px;
            text-decoration: none;
            font-weight: bold;
            color: #00796b;
        }
    </style>
</head>
<body>

<h2>Payment Records</h2>

<table>
    <tr>
        <th>Payment ID</th>
        <th>Student ID</th>
        <th>Amount (Rs.)</th>
        <th>Penalty (Rs.)</th>
        <th>Status</th>
        <th>Payment Date</th>
        <?php if ($role === 'admin') echo "<th>Action</th>"; ?>
    </tr>

    <?php while ($p = $result->fetch_assoc()): ?>
        <tr>
            <?php if ($role === 'admin'): ?>
            <form method="POST">
                <td><?= htmlspecialchars($p['payment_id']) ?></td>
                <td><?= htmlspecialchars($p['student_id']) ?></td>
                <td><?= number_format($p['amount'], 2) ?></td>
                <td>
                    <input type="number" step="0.01" name="penalty" value="<?= htmlspecialchars($p['penalty']) ?>">
                </td>
                <td>
                    <select name="status">
                        <option value="Pending" <?= $p['status'] === 'Pending' ? 'selected' : '' ?>>Pending</option>
                        <option value="Approved" <?= $p['status'] === 'Approved' ? 'selected' : '' ?>>Approved</option>
                        <option value="Rejected" <?= $p['status'] === 'Rejected' ? 'selected' : '' ?>>Rejected</option>
                    </select>
                </td>
                <td><?= htmlspecialchars($p['payment_date']) ?></td>
                <td>
                    <input type="hidden" name="payment_id" value="<?= $p['payment_id'] ?>">
                    <button type="submit">Update</button>
                </td>
            </form>
            <?php else: ?>
                <td><?= htmlspecialchars($p['payment_id']) ?></td>
                <td><?= htmlspecialchars($p['student_id']) ?></td>
                <td><?= number_format($p['amount'], 2) ?></td>
                <td><?= number_format($p['penalty'], 2) ?></td>
                <td><?= htmlspecialchars($p['status']) ?></td>
                <td><?= htmlspecialchars($p['payment_date']) ?></td>
            <?php endif; ?>
        </tr>
    <?php endwhile; ?>
</table>

<a href="<?= $role === 'admin' ? 'admin_dashboard.php' : 'student_dashboard.php' ?>">← Back to Dashboard</a>

</body>
</html>
