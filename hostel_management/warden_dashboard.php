<?php
session_start();

// Prevent back button access after logout
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

require 'includes/db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'warden') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch Warden name
$stmt = $conn->prepare("SELECT name FROM Warden WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($name);
$stmt->fetch();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Warden Dashboard</title>
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', sans-serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(135deg, rgb(218, 9, 9), rgb(71, 172, 27));
            min-height: 100vh;
            position: relative;
        }

        /* 🔴 Logout button top-right */
        .logout-btn {
            position: absolute;
            top: -60px;
            right: 30px;
            background: linear-gradient(135deg, #e74c3c, #c0392b);
            color: white;
            padding: 10px 18px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: bold;
            box-shadow: 0 4px 10px rgba(0,0,0,0.2);
            transition: background 0.3s ease;
            z-index: 1000;
        }

        .logout-btn:hover {
            background: linear-gradient(135deg, #c0392b, #a93226);
        }

        /* 🔒 Change Password button bottom-left */
     
 .edit-Profile-btn {
    position: absolute;
    top: 50px;
    right: 1050px;
    width: 100px;
    height: 100px;
    background: linear-gradient(135deg, rgba(243, 18, 18, 0), rgb(195, 19, 235));
    color: white;
    border-radius: 50%;
    text-decoration: none;
    font-weight: bold;
    box-shadow: 0 4px 10px rgba(0,0,0,0.2);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
    transition: background 0.3s ease;
    border: none;
    cursor: pointer;
}

.edit-Profile-btn:hover {
    background: linear-gradient(135deg, #e67e22, #d35400);
}

 .change-password-btn {
    position: absolute;
    top: 50px;
    right: 370px;
    width: 100px;
    height: 100px;
    background: linear-gradient(135deg,rgba(243, 18, 18, 0), #e67e22);
    color: white;
    border-radius: 50%;
    text-decoration: none;
    font-weight: bold;
    box-shadow: 0 4px 10px rgba(0,0,0,0.2);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 12.1px;
    transition: background 0.3s ease;
    border: none;
    cursor: pointer;
}

.change-password-btn:hover {
    background: linear-gradient(135deg, #e67e22, #d35400);
}

        .container {
            max-width: 900px;
            width: 80%;
            margin: 100px auto;
            background: rgba(28, 26, 26, 0.92);
            border-radius: 16px;
            padding: 40px 30px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        h2 {
            font-size: 28px;
            color: #ecf0f1;
            margin-bottom: 35px;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 20px;
            margin-top: 10px;
        }

        .card {
            background: linear-gradient(135deg, #3498db, #2980b9);
            color: #fff;
            padding: 20px;
            border-radius: 12px;
            text-decoration: none;
            font-weight: 600;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 18px rgba(0, 0, 0, 0.15);
        }

        @media (max-width: 600px) {
            .container {
                padding: 30px 20px;
                width: 95%;
            }
        }
    </style>
</head>
<body>

<!-- 🔴 Logout button top-right -->
<a href="logout.php" class="logout-btn">🚪 Logout</a>

<!-- 🔒 Change password button bottom-left -->
<a href="change_password.php" class="change-password-btn"> Change Password</a>

<!-- 🔒 Edit profile button top-right -->
<a href="edit_profile.php" class="edit-Profile-btn"> Edit Profile</a>

<div class="container">
    <h2>Welcome <?= htmlspecialchars($name) ?> 👋 <br><small>(Warden)</small></h2>

    <div class="grid">
        <a href="students.php" class="card">📘 View Students</a>
        <a href="complaints.php" class="card">📄 View Complaints</a>
        <a href="rooms.php" class="card">🛏️ View Rooms</a>
        <a href="assign_room.php" class="card">🏠 Assign Rooms</a>
        <a href="edit_rooms.php" class="card">🛠️ Edit Rooms</a>
        <a href="leave_room.php" class="card">🚪 Leave Room</a>
        <a href="change_room.php" class="card">🔄 Change Room</a>
        <a class="card" href="visitor_log.php">📒 Visitor Logs</a>
    </div>
</div>

</body>
</html>

