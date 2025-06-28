<?php
session_start();
require 'includes/db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: login.php");
    exit;
}

$successMsg = $errorMsg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $type = $_POST['type'];
    $description = trim($_POST['description']);
    $user_id = $_SESSION['user_id'];

    $stmt = $conn->prepare("SELECT student_id FROM student WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($student_id);
    $stmt->fetch();
    $stmt->close();

    if ($student_id) {
        $insert = $conn->prepare("INSERT INTO complaint (date, status, remarks, description, type, student_id) VALUES (NOW(), 'Pending', '', ?, ?, ?)");
        $insert->bind_param("ssi", $description, $type, $student_id);
        if ($insert->execute()) {
            $successMsg = "✅ Complaint submitted successfully.";
        } else {
            $errorMsg = "❌ Failed to submit complaint.";
        }
        $insert->close();
    } else {
        $errorMsg = "⚠️ Student ID not found.";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Submit Complaint</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(to right,rgb(18, 62, 182),rgb(193, 11, 11));
            margin: 0;
            padding: 40px;
        }

        .container {
            max-width: 400px;
            margin: auto;
            background: rgba(129, 181, 39, 0.7);
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 6px 18px rgba(201, 97, 17, 1);
        }

        h2 {
            text-align: center;
            color: #2c3e50;
        }

        form {
            margin-top: 20px;
        }

        label {
            font-weight: bold;
            display: block;
            margin-top: 15px;
        }

        select, textarea {
            width: 100%;
            padding: 10px;
            margin-top: 6px;
            border-radius: 6px;
            border: 1px solid #ccc;
            font-size: 14px;
        }

        textarea {
            height: 100px;
            resize: vertical;
        }

        button {
            margin-top: 20px;
            background-color: #3498db;
            color: white;
            border: none;
            padding: 12px 18px;
            border-radius: 6px;
            font-size: 16px;
            cursor: pointer;
            width: 100%;
        }

        button:hover {
            background-color: #2980b9;
        }

        .message {
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 6px;
            text-align: center;
            font-weight: bold;
        }

        .success {
            background-color: #e0f8e9;
            color: #2e7d32;
        }

        .error {
            background-color: #fce4e4;
            color: #c0392b;
        }

        .back {
            text-align: center;
            margin-top: 20px;
        }

        .back a {
            text-decoration: none;
            background-color: #3498db;
            padding: 10px 18px;
            color: white;
            border-radius: 6px;
        }

        .back a:hover {
            background-color: #2980b9;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Submit a Complaint 📝</h2>

        <?php if ($successMsg): ?>
            <div class="message success"><?= htmlspecialchars($successMsg) ?></div>
        <?php endif; ?>

        <?php if ($errorMsg): ?>
            <div class="message error"><?= htmlspecialchars($errorMsg) ?></div>
        <?php endif; ?>

        <form method="POST">
            <label>Type of Complaint:</label>
            <select name="type" required>
                <option value="">-- Select Type --</option>
                <option value="Plumbing">Plumbing</option>
                <option value="Electricity">Electricity</option>
                <option value="Cleaning">Cleaning</option>
                <option value="Other">Other</option>
            </select>

            <label>Description:</label>
            <textarea name="description" required></textarea>

            <button type="submit">Submit Complaint</button>
        </form>

        <div class="back">
            <a href="student_dashboard.php">⬅ Back to Dashboard</a>
        </div>
    </div>
</body>
</html>
