<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

require 'includes/db_connect.php';

$errors = [];
$success = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username  = trim($_POST['username']);
    $password  = $_POST['password'];
    $role      = $_POST['role'];
    $name      = trim($_POST['name']);
    $contact   = trim($_POST['contact']);
    $extra2    = $_POST['extra2'] ?? null; // Duration (student)
    $extra1    = $_POST['extra1'] ?? null; // Hostel (warden)

    $check = $conn->prepare("SELECT * FROM user WHERE username = ?");
    $check->bind_param("s", $username);
    $check->execute();
    $res = $check->get_result();
    if ($res->num_rows > 0) {
        $errors[] = "Username already exists.";
    } else {
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $stmt1 = $conn->prepare("INSERT INTO user (username, password, role) VALUES (?, ?, ?)");
        $stmt1->bind_param("sss", $username, $hashed, $role);
        if ($stmt1->execute()) {
            $user_id = $stmt1->insert_id;

            if ($role === 'student') {
                // Insert without room_number (set NULL or handle separately)
                $stmt2 = $conn->prepare("INSERT INTO student (name, contact_info, duration_of_stay, room_number, user_id) VALUES (?, ?, ?, NULL, ?)");
                $stmt2->bind_param("ssii", $name, $contact, $extra2, $user_id);
            } elseif ($role === 'warden') {
                $stmt2 = $conn->prepare("INSERT INTO warden (name, contact_info, assigned_hostel, user_id) VALUES (?, ?, ?, ?)");
                $stmt2->bind_param("sssi", $name, $contact, $extra1, $user_id);
            } elseif ($role === 'admin') {
                $stmt2 = $conn->prepare("INSERT INTO admin (name, contact, user_id) VALUES (?, ?, ?)");
                $stmt2->bind_param("ssi", $name, $contact, $user_id);
            }

            if ($stmt2 && $stmt2->execute()) {
                $success = "User registered successfully.";
            } else {
                $errors[] = "User created but failed to insert role-specific data.";
            }
        } else {
            $errors[] = "Failed to create user.";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Create User (Admin)</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(to bottom right,rgb(58, 106, 141),rgb(108, 172, 221));
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 400px;
            background: rgba(20, 27, 35, 0.96);
            margin: 60px auto;
            padding: 30px 40px;
            border-radius: 12px;
            box-shadow: 0 12px 24px rgba(0,0,0,0.1);
            color: #ecf0f1;
        }

        h2 {
            text-align: center;
            margin-bottom: 30px;
        }

        label {
            display: block;
            margin: 10px 0 5px;
            font-weight: bold;
        }

        input, select {
            width: 100%;
            padding: 10px;
            border-radius: 8px;
            border: 1px solid #ccc;
            margin-bottom: 15px;
            font-size: 14px;
        }

        button {
            background-color: #3498db;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: bold;
            width: 100%;
            cursor: pointer;
        }

        button:hover {
            background-color: #2c80b4;
        }

        .message {
            text-align: center;
            margin-bottom: 15px;
            font-weight: bold;
        }

        .error {
            color: #ff6b6b;
        }

        .success {
            color: #2ecc71;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Create New User</h2>

    <?php foreach ($errors as $error): ?>
        <div class="message error"><?= htmlspecialchars($error) ?></div>
    <?php endforeach; ?>

    <?php if ($success): ?>
        <div class="message success"><?= $success ?></div>
    <?php endif; ?>

    <form method="post">
        <label>Username:</label>
        <input type="text" name="username" required>

        <label>Password:</label>
        <input type="password" name="password" required>

        <label>Role:</label>
        <select name="role" id="role" required onchange="showRoleFields()">
            <option value="">--Select--</option>
            <option value="student">Student</option>
            <option value="warden">Warden</option>
            <option value="admin">Admin</option>
        </select>

        <label>Full Name:</label>
        <input type="text" name="name" required>

        <label>Contact Info:</label>
        <input type="text" name="contact">

        <div id="studentFields" style="display:none;">
            <label>Duration of Stay (months):</label>
            <input type="number" name="extra2">
        </div>

        <div id="wardenFields" style="display:none;">
            <label>Assigned Hostel:</label>
            <input type="text" name="extra1">
        </div>

        <div id="adminFields" style="display:none;">
            <label>Note: No extra fields for admin.</label>
        </div>

        <button type="submit">Create User</button>
        <a href="admin_dashboard.php" style="display:block; text-align:center; margin-top:15px; color:#3498db; text-decoration:none; font-weight:bold;">&larr; Back to Dashboard</a>
    </form>
</div>

<script>
    function showRoleFields() {
        const role = document.getElementById('role').value;
        document.getElementById('studentFields').style.display = role === 'student' ? 'block' : 'none';
        document.getElementById('wardenFields').style.display = role === 'warden' ? 'block' : 'none';
        document.getElementById('adminFields').style.display  = role === 'admin'  ? 'block' : 'none';
    }
</script>

</body>
</html>
