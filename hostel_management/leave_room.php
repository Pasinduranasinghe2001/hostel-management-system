<?php
session_start();
require 'includes/db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'warden') {
    header("Location: login.php");
    exit();
}

$success = $error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_id = $_POST['student_id'];

    // Get student's current room
    $result = $conn->query("SELECT room_number FROM student WHERE user_id = $student_id");
    $row = $result->fetch_assoc();

    if ($row && $row['room_number']) {
        $room = $row['room_number'];

        // Remove student's room assignment
        $conn->query("UPDATE student SET room_number = NULL WHERE user_id = $student_id");
        // Decrement occupied count in 'room' table (✅ fixed table name)
        $conn->query("UPDATE room SET occupied_count = occupied_count - 1 WHERE room_number = $room");

        $success = "✅ Room assignment removed for student.";
    } else {
        $error = "⚠️ Student is not currently assigned to any room.";
    }
}

// Fetch students with assigned rooms
$students = $conn->query("SELECT user_id, name, room_number FROM student WHERE room_number IS NOT NULL");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Remove Room Assignment</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #f0f8ff, #d4edda);
            padding: 40px;
            color: #2c3e50;
        }

        .container {
            max-width: 500px;
            margin: auto;
            background: white;
            padding: 30px 40px;
            border-radius: 14px;
            box-shadow: 0 8px 16px rgba(0,0,0,0.1);
        }

        h2 {
            text-align: center;
            color: #27ae60;
            margin-bottom: 25px;
        }

        label {
            font-weight: bold;
            display: block;
            margin-bottom: 8px;
        }

        select, button {
            width: 100%;
            padding: 12px;
            font-size: 15px;
            border-radius: 8px;
            border: 1px solid #ccc;
            margin-bottom: 20px;
        }

        button {
            background-color: #e74c3c;
            color: white;
            font-weight: bold;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: #c0392b;
        }

        .message {
            text-align: center;
            font-weight: bold;
            margin-bottom: 15px;
        }

        .success {
            color: green;
        }

        .error {
            color: red;
        }

        .back-link {
            display: block;
            text-align: center;
            margin-top: 15px;
            text-decoration: none;
            color: #2980b9;
        }

        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>🚪 Remove Room Assignment</h2>

        <?php if ($success): ?>
            <div class="message success"><?= $success ?></div>
        <?php elseif ($error): ?>
            <div class="message error"><?= $error ?></div>
        <?php endif; ?>

        <form method="post">
            <label>Select Student:</label>
            <select name="student_id" required>
                <option value="">-- Select Student --</option>
                <?php while ($s = $students->fetch_assoc()): ?>
                    <option value="<?= $s['user_id'] ?>">
                        <?= htmlspecialchars($s['name']) ?> (Room <?= $s['room_number'] ?>)
                    </option>
                <?php endwhile; ?>
            </select>

            <button type="submit">🗑️ Remove Room</button>
        </form>

        <a href="warden_dashboard.php" class="back-link">⬅ Back to Dashboard</a>
    </div>
</body>
</html>
