<?php
session_start();
require 'includes/db_connect.php';

// Prevent back button cache
header("Expires: Tue, 01 Jan 2000 00:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$message = "";
$success = false;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $current_pass = $_POST['current_password'];
    $new_pass     = $_POST['new_password'];
    $confirm_pass = $_POST['confirm_password'];

    if ($new_pass !== $confirm_pass) {
        $message = "New passwords do not match.";
    } else {
        $stmt = $conn->prepare("SELECT password FROM User WHERE user_id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stmt->bind_result($hashed_password);
        $stmt->fetch();
        $stmt->close();

        if (password_verify($current_pass, $hashed_password)) {
            $new_hashed = password_hash($new_pass, PASSWORD_DEFAULT);
            $update = $conn->prepare("UPDATE User SET password = ? WHERE user_id = ?");
            $update->bind_param("si", $new_hashed, $user_id);

            if ($update->execute()) {
                $message = "Password updated successfully.";
                $success = true;
            } else {
                $message = "Failed to update password.";
            }
        } else {
            $message = "Current password is incorrect.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Change Password</title>
    <style>
        body {
            background: linear-gradient(135deg, #667eea, #764ba2);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .password-container {
            background-color: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(12px);
            padding: 40px 30px;
            border-radius: 15px;
            width: 400px;
            max-width: 90%;
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
            color: #fff;
        }

        h2 {
            text-align: center;
            margin-bottom: 25px;
            font-size: 2rem;
        }

        label {
            font-weight: bold;
        }

        input[type="password"] {
            width: 100%;
            padding: 12px;
            margin-top: 6px;
            margin-bottom: 20px;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
        }

        button {
            width: 100%;
            padding: 12px;
            background: linear-gradient(to right, #00c6ff, #0072ff);
            color: white;
            border: none;
            border-radius: 25px;
            font-size: 1.1rem;
            font-weight: bold;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        button:hover {
            background: linear-gradient(to right, #0072ff, #00c6ff);
        }

        .message {
            text-align: center;
            padding: 12px;
            margin-bottom: 20px;
            border-radius: 10px;
        }

        .error {
            background-color: rgba(220, 53, 69, 0.85);
            color: white;
        }

        .success {
            background-color: rgba(40, 167, 69, 0.85);
            color: white;
        }
    </style>
</head>
<body>
    <div class="password-container">
        <h2>Change Password</h2>

        <?php if ($message): ?>
            <div class="message <?= $success ? 'success' : 'error' ?>">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <form method="post">
            <label>Current Password</label>
            <input type="password" name="current_password" required>

            <label>New Password</label>
            <input type="password" name="new_password" required>

            <label>Confirm New Password</label>
            <input type="password" name="confirm_password" required>

            <button type="submit">Update Password</button>
        </form>
    </div>
</body>
</html>
