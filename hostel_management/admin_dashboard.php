<?php
session_start();

// Prevent back button after logout
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

require 'includes/db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch admin name
$stmt = $conn->prepare("SELECT name FROM admin WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($name);
$stmt->fetch();
$stmt->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <style>
        body {
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(135deg,rgb(82, 198, 69),rgb(172, 45, 179));
        }

        .top-right-buttons {
            position: absolute;
            top: 20px;
            right: 30px;
            display: flex;
            gap: 10px;
        }

        .top-right-buttons a {
            padding: 8px 16px;
            background-color: #34495e;
            color: #fff;
            text-decoration: none;
            font-size: 14px;
            border-radius: 6px;
            font-weight: bold;
            transition: background 0.3s ease;
        }

        .top-right-buttons a:hover {
            background-color: #2c3e50;
        }

        .container {
            max-width: 600px;
            margin: 80px auto 60px;
            padding: 40px;
            background-color: rgba(0, 0, 0, 0.1);
            border-radius: 14px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        h2 {
            font-size: 28px;
            color: #2c3e50;
            margin-bottom: 30px;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 20px;
        }

        .card {
            background: linear-gradient(135deg,rgb(125, 10, 154), #3498db);
            color: #fff;
            padding: 18px;
            border-radius: 12px;
            font-weight: bold;
            text-decoration: none;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 18px rgba(0, 0, 0, 0.15);
        }
    </style>
</head>
<body>

<div class="top-right-buttons">
    <a href="change_password.php" style="background-color:rgb(43, 7, 114);">🔐 Change Password</a>
    <a href="logout.php" style="background-color:rgb(203, 13, 25);">🚪 Logout</a>
</div>

<div class="container">
    <h2>Welcome, <?= htmlspecialchars($name) ?> 👤<br><small>(Admin)</small></h2>

    <div class="grid">
        <a class="card" href="create_user.php">➕ Create User</a>
        <a class="card" href="users_list.php">👥 View Users</a>
        <a class="card" href="students.php">🎓 View Students</a>
        <a class="card" href="complaints.php">📄 View Complaints</a>
        <a class="card" href="payments.php">💳 View Payments</a>
        <a class="card" href="assign_warden_all.php">🏠 Assign All Wardens</a>
        <a class="card" href="view_wardens.php">🧑‍🏫 View Wardens</a>
        <a class="card" href="edit_profile.php">📝 Edit Profile</a>
        <a class="card" href="payments.php">💰 Payments</a>
        
    </div>
</div>

</body>
</html>
