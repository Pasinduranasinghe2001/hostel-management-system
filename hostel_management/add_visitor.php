<?php
session_start();
require 'includes/db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch student_id
$stmt = $conn->prepare("SELECT student_id FROM student WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($student_id);
$stmt->fetch();
$stmt->close();

$successMsg = $errorMsg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $visitor_name = trim($_POST['visitor_name']);
    $visit_datetime = $_POST['visit_datetime'];
    $purpose = trim($_POST['purpose']);

    if ($visitor_name && $visit_datetime && $purpose && $student_id) {
        $stmt = $conn->prepare("INSERT INTO visitor_log (visitor_name, visit_datetime, purpose, student_id) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("sssi", $visitor_name, $visit_datetime, $purpose, $student_id);
        if ($stmt->execute()) {
            $successMsg = "Visitor added successfully.";
        } else {
            $errorMsg = "Failed to add visitor.";
        }
        $stmt->close();
    } else {
        $errorMsg = "Please fill in all fields.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Visitor</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(to right, #f0f9ff, #d9f2e6);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .form-box {
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 6px 20px rgba(0,0,0,0.1);
            width: 400px;
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #2c3e50;
        }
        label {
            font-weight: bold;
        }
        input, textarea {
            width: 100%;
            padding: 10px;
            margin-top: 6px;
            margin-bottom: 16px;
            border: 1px solid #ccc;
            border-radius: 6px;
        }
        button {
            width: 100%;
            padding: 10px;
            background-color: #27ae60;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 16px;
        }
        button:hover {
            background-color: #219150;
        }
        .message {
            text-align: center;
            margin-bottom: 15px;
            font-weight: bold;
        }
        .success {
            color: green;
        }
        .error {
            color: red;
        }
        a {
            display: block;
            text-align: center;
            margin-top: 20px;
            text-decoration: none;
            color: #3498db;
        }
    </style>
</head>
<body>

<div class="form-box">
    <h2>Add Visitor Details</h2>

    <?php if ($successMsg): ?>
        <div class="message success"><?= $successMsg ?></div>
    <?php elseif ($errorMsg): ?>
        <div class="message error"><?= $errorMsg ?></div>
    <?php endif; ?>

    <form method="POST">
        <label>Visitor Name:</label>
        <input type="text" name="visitor_name" required>

        <label>Visit Date & Time:</label>
        <input type="datetime-local" name="visit_datetime" required>

        <label>Purpose of Visit:</label>
        <textarea name="purpose" rows="3" required></textarea>

        <button type="submit">Submit</button>
    </form>

    <a href="student_dashboard.php">← Back to Dashboard</a>
</div>

</body>
</html>
