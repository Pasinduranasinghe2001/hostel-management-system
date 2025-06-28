<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
require 'includes/db_connect.php';

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];

if ($role === 'student') {
    $stmt = $pdo->prepare("SELECT * FROM payment WHERE student_id = ? ORDER BY payment_date DESC");
    $stmt->execute([$user_id]);
} elseif ($role === 'admin') {
    $stmt = $pdo->query("SELECT * FROM payment ORDER BY payment_date DESC");
} else {
    echo "Access denied.";
    exit;
}

$payments = $stmt->fetchAll();
?>

<h2>Payment History</h2>
<table border="1">
    <tr>
        <th>Payment ID</th><th>Student ID</th><th>Amount</th><th>Date</th><th>Status</th>
    </tr>
    <?php foreach ($payments as $p): ?>
    <tr>
        <td><?= htmlspecialchars($p['payment_id']) ?></td>
        <td><?= htmlspecialchars($p['student_id']) ?></td>
        <td><?= htmlspecialchars($p['amount']) ?></td>
        <td><?= htmlspecialchars($p['payment_date']) ?></td>
        <td><?= htmlspecialchars($p['status']) ?></td>
    </tr>
    <?php endforeach; ?>
</table>

<a href="<?= ($role === 'admin') ? 'admin_dashboard.php' : 'student_dashboard.php' ?>">Back to Dashboard</a>
