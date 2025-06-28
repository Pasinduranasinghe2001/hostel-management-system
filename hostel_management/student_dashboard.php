<?php
session_start();
require 'includes/db_connect.php';

header("Expires: Tue, 01 Jan 2000 00:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("
    SELECT s.name, s.contact_info, s.duration_of_stay, r.room_number, r.room_type, r.ac_nonac
    FROM Student s
    LEFT JOIN Room r ON s.room_number = r.room_number
    WHERE s.user_id = ?
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "Student details not found.";
    exit();
}

$student = $result->fetch_assoc();

$stmt2 = $conn->prepare("
    SELECT 
        COALESCE(SUM(amount), 0) AS total_paid,
        COALESCE(SUM(penalty), 0) AS total_penalty
    FROM Payment
    WHERE student_id = (SELECT student_id FROM Student WHERE user_id = ?)
");
$stmt2->bind_param("i", $user_id);
$stmt2->execute();
$paymentResult = $stmt2->get_result();
$payment = $paymentResult->fetch_assoc();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Student Dashboard</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(to right, #1f1c2c, #928dab);
            overflow-x: hidden;
        }

        .container {
            max-width: 1000px;
            margin: auto;
            padding: 40px 20px;
        }

        h2 {
            color: #ffffff;
            text-align: center;
            margin-bottom: 30px;
            font-size: 2.5rem;
            text-shadow: 2px 2px 10px rgba(0,0,0,0.4);
        }

        .logout-btn {
            position: absolute;
            top: 20px;
            right: 20px;
            background: #e74c3c;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 8px;
            font-weight: bold;
            box-shadow: 0 4px 12px rgba(231, 76, 60, 0.6);
            transition: background 0.3s ease;
        }

        .logout-btn:hover {
            background: #c0392b;
        }

 /* 🔒 Change Password button bottom-left */
        .change-password-btn {
            position: absolute;
            top: 20px;
            right: 120px;
            width: 159px;
            background: linear-gradient(135deg,rgba(243, 18, 18, 0), #e67e22);
            color: white;
            padding: 10px 18px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: bold;
            box-shadow: 0 4px 10px rgba(0,0,0,0.2);
            transition: background 0.3s ease;
        }

        .change-password-btn:hover {
            background: linear-gradient(135deg, #e67e22, #d35400);
        }

       .edit-Profile-btn {
           position: absolute;
            top: 20px;
            right: 1300px;
            width: 159px;
            background: linear-gradient(135deg, rgba(243, 18, 18, 0), rgb(195, 19, 235));
            color: white;
            padding: 10px 18px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: bold;
            box-shadow: 0 4px 10px rgba(0,0,0,0.2);
            transition: background 0.3s ease;
        }

        .edit-Profile-btn:hover {
            background: linear-gradient(135deg, #e67e22, #d35400);
        }

        .action-buttons {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 16px;
            margin-bottom: 40px;
        }

        .action-buttons a {
            flex: 1 1 240px;
            max-width: 300px;
            padding: 18px 20px;
            background: linear-gradient(135deg, #00feba, #5b86e5);
            color: white;
            text-align: center;
            border-radius: 12px;
            font-weight: bold;
            font-size: 1.1rem;
            text-decoration: none;
            box-shadow: 0 0 20px #00feba, 0 0 30px #5b86e5;
            transition: all 0.3s ease;
        }

        .action-buttons a:hover {
            transform: translateY(-5px) scale(1.02);
            background: linear-gradient(135deg, #ff9a9e, #fad0c4);
            box-shadow: 0 0 25px #ff9a9e, 0 0 35px #fad0c4;
        }

        .dashboard-box {
            background: rgba(255, 255, 255, 0.15);
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 25px;
            box-shadow: 0 6px 18px rgba(0,0,0,0.2);
            backdrop-filter: blur(12px);
        }

        h3 {
            color: #ffffff;
            margin-bottom: 15px;
            font-size: 1.4rem;
        }

        p {
            font-size: 16px;
            margin: 8px 0;
            color: #f0f0f0;
        }

        strong {
            color: #ffd700;
        }

        @media (max-width: 600px) {
            .action-buttons {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
<!-- 🔒 Change password button bottom-left -->
<a href="change_password.php" class="change-password-btn">🔒 Change Password</a>
<a href="edit_profile.php" class="edit-Profile-btn"> Edit Profile </a>
<a class="logout-btn" href="logout.php">Logout</a>

<div class="container">
    <h2>Welcome, <?= htmlspecialchars($student['name']) ?></h2>

    <div class="action-buttons">
        <a href="my_complaints.php">📄 View Complaints</a>
        <a href="submit_complaint.php">📝 Submit Complaint</a>
        <a href="my_payments.php">💰 Payment History</a>
        <a href="submit_payment.php">💳 Make Payment</a>
        <a href="add_visitor.php">👥 Add Visitor</a>
        <a href="my_visitors.php">👀 My Visitors</a>
    </div>

    <div class="dashboard-box">
        <h3>Your Details</h3>
        <p><strong>Contact Info:</strong> <?= htmlspecialchars($student['contact_info']) ?></p>
        <p><strong>Duration of Stay:</strong> <?= htmlspecialchars($student['duration_of_stay']) ?> months</p>
    </div>

    <div class="dashboard-box">
        <h3>Room Details</h3>
        <?php if ($student['room_number']): ?>
            <p><strong>Room Number:</strong> <?= htmlspecialchars($student['room_number']) ?></p>
            <p><strong>Room Type:</strong> <?= htmlspecialchars($student['room_type']) ?></p>
            <p><strong>AC / Non-AC:</strong> <?= $student['ac_nonac'] ? 'AC' : 'Non-AC' ?></p>
        <?php else: ?>
            <p><strong>You have not been assigned a room yet.</strong></p>
        <?php endif; ?>
    </div>

    <div class="dashboard-box">
        <h3>Payment Summary</h3>
        <p><strong>Total Paid:</strong> Rs. <?= number_format($payment['total_paid'], 2) ?></p>
        <p><strong>Total Penalties:</strong> Rs. <?= number_format($payment['total_penalty'], 2) ?></p>
    </div>
</div>

</body>
</html>











   