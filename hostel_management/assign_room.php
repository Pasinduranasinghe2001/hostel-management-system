<?php
session_start();
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['warden', 'admin'])) {
    header("Location: login.php");
    exit();
}

require 'includes/db_connect.php';

$success = "";
$error = "";

// Assign room to student
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_id = $_POST['student_id'];
    $room_number = $_POST['room_number'];

    // Get room capacity and occupancy
    $check = $conn->prepare("SELECT capacity, occupied_count FROM room WHERE room_number = ?");
    $check->bind_param("i", $room_number);
    $check->execute();
    $check->bind_result($capacity, $occupied);
    $check->fetch();
    $check->close();

    if ($occupied < $capacity) {
        // Assign room to student
        $assign = $conn->prepare("UPDATE student SET room_number = ? WHERE user_id = ?");
        $assign->bind_param("ii", $room_number, $student_id);

        if ($assign->execute()) {
            // Increment room occupancy
            $update = $conn->prepare("UPDATE room SET occupied_count = occupied_count + 1 WHERE room_number = ?");
            $update->bind_param("i", $room_number);
            $update->execute();

            $success = "Room assigned successfully.";
        } else {
            $error = "Failed to assign room.";
        }
    } else {
        $error = "Room is already full.";
    }
}

// Fetch students (who haven't been assigned a room yet)
$student_stmt = $conn->prepare("
    SELECT s.user_id, s.name 
    FROM student s
    LEFT JOIN room r ON s.room_number = r.room_number
    WHERE s.room_number IS NULL
");
$student_stmt->execute();
$students = $student_stmt->get_result();

// Fetch available rooms
$room_stmt = $conn->prepare("SELECT room_number FROM room WHERE occupied_count < capacity ORDER BY room_number");
$room_stmt->execute();
$rooms = $room_stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Assign Room</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(to right,rgb(134, 5, 134), #d6efff);
            padding: 40px;
        }

        .container {
            max-width: 400px;
            margin: auto;
            background: rgba(0, 0, 0, 0.1);
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.1);
        }

        h2 {
            text-align: center;
            margin-bottom: 25px;
            color: #2c3e50;
        }

        label {
            display: block;
            margin-top: 15px;
            font-weight: 600;
            color: #34495e;
        }

        select, button {
            width: 100%;
            padding: 12px;
            margin-top: 5px;
            border-radius: 8px;
            border: 1px solid #ccc;
            font-size: 16px;
        }

        button {
            background: #3498db;
            color: white;
            font-weight: bold;
            cursor: pointer;
            margin-top: 25px;
            transition: background 0.3s ease;
        }

        button:hover {
            background: #2980b9;
        }

        .message {
            text-align: center;
            font-weight: bold;
            margin-top: 15px;
        }

        .success { color: green; }
        .error { color: red; }

        .back {
            display: block;
            margin-top: 25px;
            text-align: center;
        }

        .back a {
            color: #2980b9;
            text-decoration: none;
            font-weight: bold;
        }

        .back a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Assign Room to Student</h2>

    <?php if ($success): ?>
        <div class="message success"><?= htmlspecialchars($success) ?></div>
    <?php elseif ($error): ?>
        <div class="message error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="post">
        <label for="student_id">Select Student:</label>
        <select name="student_id" id="student_id" required>
            <option value="">-- Select Student --</option>
            <?php while ($row = $students->fetch_assoc()): ?>
                <option value="<?= $row['user_id'] ?>"><?= htmlspecialchars($row['name']) ?></option>
            <?php endwhile; ?>
        </select>

        <label for="room_number">Choose Room:</label>
        <select name="room_number" id="room_number" required>
            <option value="">-- Select Room --</option>
            <?php while ($room = $rooms->fetch_assoc()): ?>
                <option value="<?= $room['room_number'] ?>"><?= $room['room_number'] ?></option>
            <?php endwhile; ?>
        </select>

        <button type="submit">Assign Room</button>
    </form>

    <div class="back">
        <a href="<?= ($_SESSION['role'] === 'admin') ? 'admin_dashboard.php' : 'warden_dashboard.php' ?>">⬅ Back to Dashboard</a>
    </div>
</div>

</body>
</html>
