<?php
session_start();
require 'includes/db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'warden') {
    header("Location: login.php");
    exit();
}

$room_number = $_GET['room_number'] ?? '';
$error = '';
$success = '';

if (!$room_number) {
    $error = "❌ Room number not provided.";
} else {
    $stmt = $conn->prepare("SELECT * FROM room WHERE room_number = ?");
    $stmt->bind_param("s", $room_number);
    $stmt->execute();
    $result = $stmt->get_result();
    $room = $result->fetch_assoc();
    $stmt->close();

    if (!$room) {
        $error = "❌ Room not found.";
    }
}

// Update on POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($room)) {
    $room_type = $_POST['room_type'];
    $capacity = (int)$_POST['capacity'];
    $ac_nonac = $_POST['ac_nonac'];

    $stmt = $conn->prepare("UPDATE room SET room_type = ?, capacity = ?, ac_nonac = ? WHERE room_number = ?");
    $stmt->bind_param("siss", $room_type, $capacity, $ac_nonac, $room_number);
    if ($stmt->execute()) {
        $success = "✅ Room updated successfully!";
        $room['room_type'] = $room_type;
        $room['capacity'] = $capacity;
        $room['ac_nonac'] = $ac_nonac;
    } else {
        $error = "❌ Update failed.";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Room - <?= htmlspecialchars($room_number) ?></title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(to right, #f5f7fa, #c3cfe2);
            padding: 40px;
        }

        .container {
            max-width: 500px;
            background: white;
            padding: 30px;
            margin: auto;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }

        h2 {
            text-align: center;
            color: #2c3e50;
            margin-bottom: 25px;
        }

        label {
            margin-top: 15px;
            display: block;
            font-weight: bold;
        }

        select, input[type="number"] {
            width: 100%;
            padding: 10px;
            margin-top: 8px;
            border-radius: 6px;
            border: 1px solid #ccc;
        }

        .btn {
            margin-top: 25px;
            width: 100%;
            background-color: #3498db;
            color: white;
            padding: 12px;
            font-size: 16px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
        }

        .btn:hover {
            background-color: #2980b9;
        }

        .message {
            text-align: center;
            margin-top: 10px;
            font-weight: bold;
        }

        .success {
            color: green;
        }

        .error {
            color: red;
        }

        .back {
            display: block;
            margin-top: 25px;
            text-align: center;
            color: #555;
        }

        .back:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>✏️ Edit Room <?= htmlspecialchars($room_number) ?></h2>

    <?php if ($error): ?>
        <p class="message error"><?= htmlspecialchars($error) ?></p>
    <?php elseif ($success): ?>
        <p class="message success"><?= htmlspecialchars($success) ?></p>
    <?php endif; ?>

    <?php if (isset($room)): ?>
        <form method="post">
            <label for="room_type">Room Type:</label>
            <select name="room_type" id="room_type" required>
                <option value="Single" <?= $room['room_type'] === 'Single' ? 'selected' : '' ?>>Single</option>
                <option value="Double" <?= $room['room_type'] === 'Double' ? 'selected' : '' ?>>Double</option>
                <option value="Multiple" <?= $room['room_type'] === 'Multiple' ? 'selected' : '' ?>>Multiple</option>
            </select>

            <label for="capacity">Capacity:</label>
            <input type="number" name="capacity" id="capacity" value="<?= htmlspecialchars($room['capacity']) ?>" required>

            <label for="ac_nonac">AC / Non-AC:</label>
            <select name="ac_nonac" id="ac_nonac" required>
                <option value="AC" <?= $room['ac_nonac'] === 'AC' ? 'selected' : '' ?>>AC</option>
                <option value="Non-AC" <?= $room['ac_nonac'] === 'Non-AC' ? 'selected' : '' ?>>Non-AC</option>
            </select>

            <button type="submit" class="btn">💾 Update Room</button>
        </form>
    <?php endif; ?>

    <a href="edit_rooms.php" class="back">⬅ Back to Edit Rooms</a>
</div>

</body>
</html>
