<?php
session_start();
require 'includes/db_connect.php';

// Only admin can delete warden
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$user_id = $_GET['user_id'] ?? null;

if ($user_id) {
    // First, set any rooms managed by this warden to NULL
    $conn->query("UPDATE room SET warden_id = NULL WHERE warden_id = (SELECT warden_id FROM warden WHERE user_id = $user_id)");

    // Delete from warden table
    $conn->query("DELETE FROM warden WHERE user_id = $user_id");

    // Delete from user table (correct name, not 'users')
    $conn->query("DELETE FROM user WHERE user_id = $user_id");
}

header("Location: view_wardens.php");
exit;
