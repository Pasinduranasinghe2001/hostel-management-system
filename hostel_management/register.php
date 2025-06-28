<?php
session_start();
require 'includes/db_connect.php';

$errors = [];
$success = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $name     = trim($_POST['name']);
    $contact  = trim($_POST['contact_info']);
    $duration = intval($_POST['duration_of_stay']);
    // Removed room_number

    // Check if username exists
    $check = $conn->prepare("SELECT * FROM User WHERE username = ?");
    $check->bind_param("s", $username);
    $check->execute();
    $result = $check->get_result();
    if ($result->num_rows > 0) {
        $errors[] = "Username already taken.";
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt1 = $conn->prepare("INSERT INTO User (username, password, role) VALUES (?, ?, 'student')");
        $stmt1->bind_param("ss", $username, $hashed_password);
        if ($stmt1->execute()) {
            $user_id = $stmt1->insert_id;
            // Insert into Student without room_number
            $stmt2 = $conn->prepare("INSERT INTO Student (name, contact_info, duration_of_stay, user_id) VALUES (?, ?, ?, ?)");
            $stmt2->bind_param("ssii", $name, $contact, $duration, $user_id);
            if ($stmt2->execute()) {
                $success = "Registration successful. You can now <a href='login.php'>login</a>.";
            } else {
                $errors[] = "Failed to save student details.";
            }
        } else {
            $errors[] = "Failed to create user.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Student Registration</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body, html {
            height: 100%;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: url('images/bg_register.jpg') no-repeat center center fixed;
            background-size: cover;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .register-container {
            background-color: rgba(20, 30, 90, 0.5);
            padding: 30px 40px;
            border-radius: 20px;
            width: 500px;
            max-width: 90%;
            color: blue;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.4);
            text-align: center;
        }

        .register-container img {
            width: 90px;
            margin-bottom: 15px;
            border-radius: 10px;
        }

        h2 {
            font-size: 1.9rem;
            margin-bottom: 20px;
        }

        form {
            display: grid;
            gap: 12px;
            text-align: left;
        }

        label {
            font-size: 0.95rem;
            font-weight: bold;
        }

        input[type="text"],
        input[type="password"],
        input[type="number"] {
            padding: 10px;
            border: none;
            border-radius: 8px;
            font-size: 0.95rem;
            background-color: #ecf0f1;
            color: #333;
            width: 100%;
        }

        button {
            background-color: #00b894;
            color: white;
            padding: 12px;
            border: none;
            border-radius: 30px;
            font-size: 1.1rem;
            font-weight: bold;
            cursor: pointer;
            margin-top: 10px;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #009f7c;
        }

        .message {
            text-align: center;
            font-weight: bold;
            padding: 10px;
            margin-bottom: 12px;
            border-radius: 8px;
        }

        .error-message {
            background-color: #e74c3c;
            color: white;
        }

        .success-message {
            background-color: #2ecc71;
            color: white;
        }

        .link {
            margin-top: 15px;
        }

        .link a {
            color: #ccefff;
            font-weight: bold;
            text-decoration: none;
        }

        .link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="register-container">
        <h2>Student Registration</h2>

        <?php
        foreach ($errors as $error) {
            echo "<div class='message error-message'>" . htmlspecialchars($error) . "</div>";
        }
        if ($success) {
            echo "<div class='message success-message'>$success</div>";
        }
        ?>

        <form method="post">
            <label for="username">Username</label>
            <input type="text" name="username" id="username" required>

            <label for="password">Password</label>
            <input type="password" name="password" id="password" required>

            <label for="name">Full Name</label>
            <input type="text" name="name" id="name" required>

            <label for="contact_info">Contact Info</label>
            <input type="text" name="contact_info" id="contact_info">

            <label for="duration_of_stay">Duration of Stay (months)</label>
            <input type="number" name="duration_of_stay" id="duration_of_stay" min="1" required>

            <button type="submit">Register</button>
        </form>

        <div class="link">
            Already registered? <a href="login.php">Login here</a>
        </div>
    </div>
</body>
</html>
