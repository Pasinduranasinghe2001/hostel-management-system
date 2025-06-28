<?php
session_start();
require 'includes/db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: login.php");
    exit();
}

$successMsg = $errorMsg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $amount = floatval($_POST['amount']);
    $user_id = $_SESSION['user_id'];

    // Get student_id
    $stmt = $conn->prepare("SELECT student_id FROM student WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($student_id);
    $stmt->fetch();
    $stmt->close();

    if ($student_id) {
        // Determine penalty if submitted after 10th of the month
        $penalty = (date('j') > 10) ? 200.00 : 0.00;

        $insert = $conn->prepare("INSERT INTO payment (student_id, amount, penalty, payment_date, status) VALUES (?, ?, ?, NOW(), 'Pending')");
        $insert->bind_param("idd", $student_id, $amount, $penalty);
        if ($insert->execute()) {
            $successMsg = "✅ Payment submitted successfully.";
        } else {
            $errorMsg = "❌ Failed to submit payment.";
        }
        $insert->close();
    } else {
        $errorMsg = "❌ Student not found.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Submit Payment</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }
        
        .container {
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
            width: 100%;
            max-width: 450px;
            padding: 40px;
            text-align: center;
        }
        
        h2 {
            color: #2c3e50;
            margin-bottom: 25px;
            font-size: 28px;
            font-weight: 600;
        }
        
        .payment-info {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 15px;
            margin: 20px 0;
            text-align: left;
        }
        
        .payment-info p {
            margin: 8px 0;
            color: #555;
            font-size: 14px;
        }
        
        .form-group {
            margin-bottom: 25px;
            text-align: left;
        }
        
        label {
            display: block;
            margin-bottom: 8px;
            color: #2c3e50;
            font-weight: 500;
        }
        
        input {
            width: 100%;
            padding: 14px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 16px;
            transition: border-color 0.3s;
        }
        
        input:focus {
            border-color: #3498db;
            outline: none;
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.2);
        }
        
        button {
            background: linear-gradient(to right, #3498db, #2c3e50);
            color: white;
            border: none;
            padding: 14px;
            width: 100%;
            border-radius: 8px;
            font-size: 17px;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
            box-shadow: 0 4px 15px rgba(52, 152, 219, 0.3);
        }
        
        button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(52, 152, 219, 0.4);
        }
        
        .back-link {
            display: inline-block;
            margin-top: 25px;
            color: #3498db;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.2s;
        }
        
        .back-link:hover {
            color: #2c3e50;
            text-decoration: underline;
        }
        
        .success {
            background: #d4edda;
            color: #155724;
            padding: 15px;
            border-radius: 8px;
            margin: 20px 0;
            font-weight: 500;
        }
        
        .error {
            background: #f8d7da;
            color: #721c24;
            padding: 15px;
            border-radius: 8px;
            margin: 20px 0;
            font-weight: 500;
        }
        
        .info-icon {
            display: inline-block;
            background: #3498db;
            color: white;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            text-align: center;
            line-height: 20px;
            margin-right: 8px;
            font-size: 14px;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>Submit Payment</h2>
    
    <?php if ($successMsg): ?>
        <p class="success"><?= $successMsg ?></p>
    <?php elseif ($errorMsg): ?>
        <p class="error"><?= $errorMsg ?></p>
    <?php endif; ?>
    
    <div class="payment-info">
        <p><span class="info-icon">i</span> After 10th of month: Rs.200 penalty applies</p>
        <p><span class="info-icon">i</span> Payments may take 1-2 business days to process</p>
    </div>
    
    <form method="POST">
        <div class="form-group">
            <label for="amount">Amount (Rs):</label>
            <input type="number" name="amount" id="amount" step="0.01" min="1" required placeholder="Enter payment amount">
        </div>
        <button type="submit">Pay Now</button>
    </form>
    
    <a href="student_dashboard.php" class="back-link">← Back to Dashboard</a>
</div>
</body>
</html>
