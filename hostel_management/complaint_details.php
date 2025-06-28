<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

require 'includes/db_connect.php';

if (!isset($_GET['id'])) {
    echo "Complaint ID missing.";
    exit;
}

$complaint_id = (int)$_GET['id'];
$role = $_SESSION['role'];
$user_id = $_SESSION['user_id'];

// Fetch complaint
$stmt = $conn->prepare("
    SELECT c.*, s.name AS student_name 
    FROM complaint c 
    JOIN student s ON c.student_id = s.student_id 
    WHERE c.complaint_id = ?
");
$stmt->bind_param("i", $complaint_id);
$stmt->execute();
$result = $stmt->get_result();
$complaint = $result->fetch_assoc();

if (!$complaint) {
    echo "Complaint not found.";
    exit;
}

// Authorization
if ($role === 'student' && $complaint['student_id'] != $user_id) {
    echo "Access denied.";
    exit;
}

if ($role === 'warden' && $complaint['warden_id'] != $user_id) {
    echo "Access denied.";
    exit;
}

// Update handling
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $role !== 'student') {
    $new_status = $_POST['status'] ?? $complaint['status'];
    $remarks = $_POST['remarks'] ?? '';

    $update = $conn->prepare("UPDATE complaint SET status = ?, remarks = ? WHERE complaint_id = ?");
    $update->bind_param("ssi", $new_status, $remarks, $complaint_id);
    if ($update->execute()) {
        header("Location: complaints.php");
        exit;
    } else {
        echo "<p style='color:red;'>Failed to update complaint.</p>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Complaint Details</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg,rgb(24, 38, 145),rgb(227, 235, 255));
            padding: 30px;
        }

        .box {
            background-color: rgba(84, 104, 133, 0.5);
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            max-width: 500px;
            margin: auto;
        }

        h2 {
            color: #2c3e50;
            text-align: center;
        }

        p {
            font-size: 16px;
            margin: 10px 0;
        }

        label {
            font-weight: bold;
        }

        textarea, select {
            width: 100%;
            padding: 8px;
            margin-top: 8px;
            margin-bottom: 20px;
        }

        button {
            background-color: #3498db;
            color: white;
            padding: 10px 18px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
        }

        button:hover {
            background-color: #2980b9;
        }

        .back-link {
            display: block;
            margin-top: 30px;
            text-align: center;
            font-weight: bold;
        }
    </style>
</head>
<body>

<div class="box">
    <h2>Complaint Details</h2>

    <p><strong>Date:</strong> <?= htmlspecialchars($complaint['date']) ?></p>
    <p><strong>Student:</strong> <?= htmlspecialchars($complaint['student_name']) ?></p>
    <p><strong>Type:</strong> <?= htmlspecialchars($complaint['type']) ?></p>
    <p><strong>Description:</strong> <?= htmlspecialchars($complaint['description']) ?></p>
    <p><strong>Status:</strong> <?= htmlspecialchars($complaint['status']) ?></p>
    <p><strong>Remarks:</strong> <?= nl2br(htmlspecialchars($complaint['remarks'] ?? '')) ?></p>

    <?php if ($role !== 'student'): ?>
    <form method="POST">
        <label>Status:</label>
        <select name="status">
            <option value="Pending" <?= $complaint['status'] === 'Pending' ? 'selected' : '' ?>>Pending</option>
            <option value="In Progress" <?= $complaint['status'] === 'In Progress' ? 'selected' : '' ?>>In Progress</option>
            <option value="Resolved" <?= $complaint['status'] === 'Resolved' ? 'selected' : '' ?>>Resolved</option>
        </select>

        <label>Remarks:</label>
        <textarea name="remarks" rows="4"><?= htmlspecialchars($complaint['remarks'] ?? '') ?></textarea>

        <button type="submit">Update Complaint</button>
    </form>
    <?php endif; ?>

    <a class="back-link" href="<?= ($role === 'admin' || $role === 'warden') ? 'complaints.php' : 'my_complaints.php' ?>">⬅ Back</a>
</div>

</body>
</html>
