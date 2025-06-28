<?php
session_start();

require 'includes/db_connect.php';

// Allow only admin or warden to delete student
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['admin', 'warden'])) {
    header("Location: login.php");
    exit();
}

$student_id = $_GET['student_id'] ?? null;

if ($student_id) {
    // Get user_id of the student
    $stmt = $conn->prepare("SELECT user_id FROM student WHERE student_id = ?");
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
    $stmt->bind_result($user_id);
    $stmt->fetch();
    $stmt->close();

    if ($user_id) {
        // Delete related records first
        $conn->query("DELETE FROM payment WHERE student_id = $student_id");
        $conn->query("DELETE FROM visitor_log WHERE student_id = $student_id");
        $conn->query("DELETE FROM student WHERE student_id = $student_id");
        $conn->query("DELETE FROM user WHERE user_id = $user_id");  // Corrected table name here
    }
}

// Redirect based on role
$redirect = ($_SESSION['role'] === 'admin') ? 'students.php' : 'students.php';
header("Location: $redirect");
exit;
?>
