<?php
session_start();
require 'includes/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $role = trim($_POST['role']);

    if (empty($username) || empty($password) || empty($role)) {
        $_SESSION['error'] = "All fields are required.";
        header("Location: login.php");
        exit();
    }

    $stmt = $conn->prepare("SELECT user_id, password, role FROM User WHERE username = ? AND role = ?");
    $stmt->bind_param("ss", $username, $role);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows === 1) {
        $row = $res->fetch_assoc();

        if (password_verify($password, $row['password'])) {
            $_SESSION['user_id'] = $row['user_id'];
            $_SESSION['role'] = $row['role'];
            $_SESSION['username'] = $username;

            switch ($row['role']) {
                case 'admin':
                    header("Location: admin_dashboard.php");
                    break;
                case 'warden':
                    header("Location: warden_dashboard.php");
                    break;
                case 'student':
                    header("Location: student_dashboard.php");
                    break;
                default:
                    $_SESSION['error'] = "Invalid user role.";
                    header("Location: login.php");
                    break;
            }
            exit();
        } else {
            $_SESSION['error'] = "Invalid password.";
            header("Location: login.php");
            exit();
        }
    } else {
        $_SESSION['error'] = "User not found or role mismatch.";
        header("Location: login.php");
        exit();
    }
} else {
    $_SESSION['error'] = "Invalid request.";
    header("Location: login.php");
    exit();
}
