<?php
session_start();
require 'includes/db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];
$success = "";
$error = "";

// Fetch user info
switch ($role) {
    case 'student':
        $stmt = $conn->prepare("SELECT * FROM student WHERE user_id = ?");
        break;
    case 'warden':
        $stmt = $conn->prepare("SELECT * FROM warden WHERE user_id = ?");
        break;
    case 'admin':
        $stmt = $conn->prepare("SELECT * FROM admin WHERE user_id = ?");
        break;
    default:
        die("Invalid role.");
}
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$userData = $result->fetch_assoc();
$stmt->close();

if (!$userData) {
    die("⚠️ Profile not found.");
}

// Handle update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $contact = trim($_POST['contact']);

    if ($role === 'student') {
        $duration = intval($_POST['duration']);
        $update = $conn->prepare("UPDATE student SET name = ?, contact_info = ?, duration_of_stay = ? WHERE user_id = ?");
        $update->bind_param("ssii", $name, $contact, $duration, $user_id);
    } elseif ($role === 'warden') {
        $update = $conn->prepare("UPDATE warden SET name = ?, contact_info = ? WHERE user_id = ?");
        $update->bind_param("ssi", $name, $contact, $user_id);
    } elseif ($role === 'admin') {
        $update = $conn->prepare("UPDATE admin SET name = ?, contact = ? WHERE user_id = ?");
        $update->bind_param("ssi", $name, $contact, $user_id);
    }

    if ($update->execute()) {
        $success = "✅ Profile updated successfully.";
    } else {
        $error = "❌ Failed to update profile.";
    }
    $update->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Edit Profile</title>
    <style>
        * { box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', sans-serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(135deg, #00c6ff, #0072ff);
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
        }

        .container {
            background: rgba(255, 255, 255, 0.15);
            border: 1px solid rgba(255, 255, 255, 0.3);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            padding: 40px;
            border-radius: 20px;
            max-width: 500px;
            width: 90%;
            color: #2c3e50;
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.2);
        }

        h2 {
            text-align: center;
            color: #fff;
            margin-bottom: 25px;
        }

        label {
            font-weight: bold;
            display: block;
            margin-bottom: 6px;
            color: #f5f5f5;
        }

        input {
            width: 100%;
            padding: 12px;
            margin-bottom: 18px;
            border-radius: 8px;
            border: none;
            font-size: 15px;
            background: rgba(255, 255, 255, 0.8);
        }

        button {
            width: 100%;
            padding: 12px;
            background-color: #16a085;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        button:hover {
            background-color: #138d75;
        }

        .message {
            text-align: center;
            font-weight: bold;
            margin-bottom: 15px;
            padding: 10px;
            border-radius: 10px;
        }

        .success {
            background-color: #d4edda;
            color: #155724;
        }

        .error {
            background-color: #f8d7da;
            color: #721c24;
        }

        .back-link {
            display: block;
            text-align: center;
            margin-top: 25px;
            color: #f5f5f5;
            text-decoration: none;
        }

        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Edit Your Profile</h2>

    <?php if ($success): ?>
        <div class="message success"><?= htmlspecialchars($success) ?></div>
    <?php elseif ($error): ?>
        <div class="message error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="post">
        <label for="name">Full Name:</label>
        <input type="text" name="name" id="name" value="<?= htmlspecialchars($userData['name']) ?>" required>

        <label for="contact">Contact Info:</label>
        <input type="text" name="contact" id="contact"
               value="<?= htmlspecialchars($userData['contact_info'] ?? $userData['contact'] ?? '') ?>" required>

        <?php if ($role === 'student'): ?>
            <label for="duration">Duration of Stay (months):</label>
            <input type="number" name="duration" id="duration"
                   value="<?= htmlspecialchars($userData['duration_of_stay']) ?>" required>
        <?php endif; ?>

        <button type="submit">Update Profile</button>
    </form>

    <?php
    $dashboardLink = $role . "_dashboard.php";
    ?>
    <a href="<?= $dashboardLink ?>" class="back-link">← Back to Dashboard</a>
</div>

</body>
</html>
