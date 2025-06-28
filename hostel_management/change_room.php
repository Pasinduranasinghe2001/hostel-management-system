<?php
session_start();
require 'includes/db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'warden') {
    header("Location: login.php");
    exit();
}

$success = $error = "";

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_id = $_POST['student_id'];
    $new_room = $_POST['new_room'];

    // Fetch current room of student
    $result = $conn->query("SELECT room_number FROM student WHERE user_id = $student_id");
    $row = $result->fetch_assoc();
    $old_room = $row['room_number'];

    // Update student's room
    $conn->query("UPDATE student SET room_number = $new_room WHERE user_id = $student_id");

    // Decrease old room count if exists
    if ($old_room) {
        $conn->query("UPDATE room SET occupied_count = occupied_count - 1 WHERE room_number = $old_room");
    }

    // Increase new room count
    $conn->query("UPDATE room SET occupied_count = occupied_count + 1 WHERE room_number = $new_room");

    $success = "✅ Room changed successfully.";
}

// Fetch students
$students = $conn->query("SELECT user_id, name, room_number FROM student");

// Fetch only available rooms
$rooms = $conn->query("SELECT room_number FROM room WHERE capacity > occupied_count");
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Change Student Room</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #f5f5f5, #e0f7fa);
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
            color: #00796b;
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
            background-color: #00796b;
            color: white;
            font-weight: bold;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: #004d40;
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
        <h2>🏠 Change Room Assignment</h2>

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
                        <?= htmlspecialchars($s['name']) ?> (Current Room: <?= $s['room_number'] ?? 'None' ?>)
                    </option>
                <?php endwhile; ?>
            </select>

            <label>Select New Room:</label>
            <select name="new_room" required>
                <option value="">-- Select Available Room --</option>
                <?php while ($r = $rooms->fetch_assoc()): ?>
                    <option value="<?= $r['room_number'] ?>">Room <?= $r['room_number'] ?></option>
                <?php endwhile; ?>
            </select>

            <button type="submit">🔁 Change Room</button>
        </form>

        <a href="warden_dashboard.php" class="back-link">⬅ Back to Dashboard</a>
    </div>
</body>
</html>
