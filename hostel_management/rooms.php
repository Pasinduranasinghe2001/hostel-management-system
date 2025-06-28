<?php
session_start();
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['admin', 'warden'])) {
    header("Location: login.php");
    exit();
}

require 'includes/db_connect.php';

// Fetch room info along with assigned warden name
$stmt = $conn->prepare("
    SELECT r.room_number, r.room_type, r.ac_nonac, r.capacity, r.occupied_count, w.name AS warden_name
    FROM room r
    LEFT JOIN warden w ON r.warden_id = w.warden_id
    ORDER BY r.room_number
");
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Hostel Rooms</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(to bottom right, #ffffff, #e0f7fa);
            margin: 0;
            padding: 40px;
        }

        h2 {
            text-align: center;
            color: #2c3e50;
            font-size: 28px;
            margin-bottom: 30px;
        }

        .table-wrapper {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background-color: white;
            border-radius: 12px;
            box-shadow: 0 10px 24px rgba(0,0,0,0.08);
            overflow: hidden;
            min-width: 900px;
        }

        th, td {
            padding: 14px 16px;
            text-align: center;
            border-bottom: 1px solid #e0e0e0;
        }

        th {
            background-color: #00796b;
            color: white;
            font-weight: bold;
        }

        tr:hover {
            background-color: #f1f8e9;
        }

        .back-btn {
            display: block;
            width: fit-content;
            margin: 30px auto 0;
            background: linear-gradient(135deg, #388e3c, #2e7d32);
            color: white;
            padding: 12px 24px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: bold;
            transition: 0.3s ease;
        }

        .back-btn:hover {
            background: linear-gradient(135deg, #2e7d32, #1b5e20);
        }

        @media (max-width: 768px) {
            table {
                font-size: 14px;
                min-width: unset;
            }

            th, td {
                padding: 10px 12px;
            }

            .back-btn {
                font-size: 14px;
                padding: 10px 20px;
            }
        }
    </style>
</head>
<body>

<h2>🛏️ Hostel Room Details</h2>

<div class="table-wrapper">
    <table>
        <thead>
            <tr>
                <th>Room Number</th>
                <th>Room Type</th>
                <th>AC / Non-AC</th>
                <th>Capacity</th>
                <th>Occupied</th>
                <th>Warden</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['room_number']) ?></td>
                        <td><?= htmlspecialchars($row['room_type']) ?></td>
                        <td><?= htmlspecialchars($row['ac_nonac']) ?></td>
                        <td><?= htmlspecialchars($row['capacity']) ?></td>
                        <td><?= htmlspecialchars($row['occupied_count']) ?></td>
                        <td><?= htmlspecialchars($row['warden_name'] ?? 'Unassigned') ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="6">No rooms found.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<a href="<?= $_SESSION['role'] === 'admin' ? 'admin_dashboard.php' : 'warden_dashboard.php' ?>" class="back-btn">⬅ Back to Dashboard</a>

</body>
</html>
