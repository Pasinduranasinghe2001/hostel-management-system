<?php
session_start();
require 'includes/db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$success = "";

// --- Handle hostel assignment ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['assign_hostel'])) {
    $warden_id = (int)$_POST['warden_id'];
    $hostel = trim($_POST['hostel']);

    $stmt = $conn->prepare("UPDATE warden SET assigned_hostel = ? WHERE user_id = ?");
    $stmt->bind_param("si", $hostel, $warden_id);
    $stmt->execute();
    $success = "✅ Warden assigned to $hostel successfully.";
}

// --- Handle room assignment ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['assign_room'])) {
    $room_number = $_POST['room_number'];
    $warden_id = (int)$_POST['room_warden_id'];

    $stmt = $conn->prepare("UPDATE room SET warden_id = ? WHERE room_number = ?");
    $stmt->bind_param("is", $warden_id, $room_number);
    $stmt->execute();
    $success = "✅ Room $room_number assigned to Warden ID $warden_id.";
}

// Get all wardens
$wardens = $conn->query("SELECT user_id, name, assigned_hostel, warden_id FROM warden");

// Get ALL rooms (not just unassigned)
$all_rooms = $conn->query("
    SELECT r.room_number, w.name AS warden_name 
    FROM room r 
    LEFT JOIN warden w ON r.warden_id = w.warden_id
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Assign Wardens & Rooms</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(to right,rgb(137, 36, 110),rgb(47, 45, 155));
            padding: 40px;
        }

        h2 {
            text-align: center;
            color: #fff;
        }

        .container {
            max-width: 650px;
            margin: auto;
            background: rgba(0,0,0,0.5);
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
        }

        form {
            margin-bottom: 40px;
        }

        label, select, input {
            display: block;
            margin: 12px 0;
            width: 100%;
            padding: 10px;
        }

        button {
            padding: 12px;
            background: #3498db;
            color: white;
            border: none;
            font-weight: bold;
            border-radius: 6px;
            cursor: pointer;
        }

        button:hover {
            background: #2980b9;
        }

        .success {
            background-color: #d4edda;
            color: #155724;
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 8px;
        }

        .section-title {
            font-size: 20px;
            color: #2c3e50;
            margin-top: 20px;
        }

        a {
            display: block;
            margin-top: 30px;
            text-align: center;
            color: #2980b9;
        }

        option {
            padding: 5px;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>👩‍💼 Assign Wardens to Hostel & Rooms</h2>

    <?php if ($success): ?>
        <div class="success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <!-- Assign Hostel to Warden -->
    <form method="POST">
        <div class="section-title">🏢 Assign Warden to Hostel</div>

        <label for="warden_id">Select Warden:</label>
        <select name="warden_id" id="warden_id" required>
            <option value="">-- Select Warden --</option>
            <?php
            $wardens->data_seek(0);
            while ($w = $wardens->fetch_assoc()): ?>
                <option value="<?= $w['user_id'] ?>">
                    <?= htmlspecialchars($w['name']) ?> (<?= $w['assigned_hostel'] ?: 'No Hostel' ?>)
                </option>
            <?php endwhile; ?>
        </select>

        <label for="hostel">Hostel Name:</label>
        <input type="text" name="hostel" id="hostel" required>

        <button type="submit" name="assign_hostel">Assign Hostel</button>
    </form>

    <!-- Assign ANY Room to Warden -->
    <form method="POST">
        <div class="section-title">🛏️ Assign Any Room to Warden</div>

        <label for="room_number">Room Number:</label>
        <select name="room_number" id="room_number" required>
            <option value="">-- Select Room --</option>
            <?php while ($r = $all_rooms->fetch_assoc()): ?>
                <option value="<?= $r['room_number'] ?>">
                    <?= $r['room_number'] ?> <?= $r['warden_name'] ? "(Assigned to: {$r['warden_name']})" : "(Unassigned)" ?>
                </option>
            <?php endwhile; ?>
        </select>

        <label for="room_warden_id">Assign to Warden:</label>
        <select name="room_warden_id" id="room_warden_id" required>
            <option value="">-- Select Warden --</option>
            <?php
            $wardens->data_seek(0);
            while ($w = $wardens->fetch_assoc()): ?>
                <option value="<?= $w['warden_id'] ?>"><?= htmlspecialchars($w['name']) ?></option>
            <?php endwhile; ?>
        </select>

        <button type="submit" name="assign_room">Assign Room</button>
    </form>

    <a href="admin_dashboard.php">⬅ Back to Dashboard</a>
</div>

</body>
</html>
