<?php
session_start();
if (isset($_SESSION['user_id'])) {
    $role = $_SESSION['role'];
    if ($role === 'admin') {
        header("Location: admin_dashboard.php");
    } elseif ($role === 'warden') {
        header("Location: warden_dashboard.php");
    } elseif ($role === 'student') {
        header("Location: student_dashboard.php");
    }
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Hostel Management System</title>

    <!-- Google Fonts: Montserrat -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@500;700&display=swap" rel="stylesheet">

    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />

    <style>
        body {
            font-family: 'Montserrat', Arial, sans-serif;
            background: linear-gradient(135deg,rgb(29, 59, 159) 0%,rgb(31, 113, 31) 100%);
            min-height: 100vh;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 420px;
            margin: 70px auto 0 auto;
            background: rgba(210, 211, 199, 0.93);
            border-radius: 22px;
            box-shadow: 0 8px 32px rgba(60,60,120,0.12);
            padding: 38px 32px 32px 32px;
            text-align: center;
        }
        .main-title i {
            background: linear-gradient(135deg,rgb(116, 179, 0),rgb(4, 24, 106) 80%);
            color: white;
            border-radius: 50%;
            padding: 26px;
            margin-bottom: 10px;
            box-shadow: 0 4px 18px rgba(0,179,179,0.18);
        }
        .main-title h2 {
            font-size: 2.2rem;
            color: #253053;
            margin-bottom: 8px;
            font-weight: 700;
            letter-spacing: 1px;
        }
        .main-title p {
            font-size: 1.05rem;
            color: #3b7fa7;
            margin-bottom: 24px;
        }
        .actions {
            display: flex;
            flex-direction: column;
            gap: 18px;
            margin-top: 25px;
        }
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            padding: 15px 0;
            width: 100%;
            font-size: 1.05rem;
            font-weight: 600;
            border: none;
            border-radius: 12px;
            cursor: pointer;
            background: linear-gradient(90deg, #00b3b3 0%, #5f72bd 100%);
            color: #fff;
            box-shadow: 0 2px 12px rgba(0,179,179,0.10);
            transition: background 0.2s, transform 0.15s, box-shadow 0.2s;
            text-decoration: none;
            letter-spacing: 0.5px;
        }
        .btn.login {
            background: linear-gradient(90deg, #00b3b3 0%, #6a82fb 100%);
        }
        .btn.register {
            background: linear-gradient(90deg,rgb(170, 8, 8) 0%,rgb(114, 4, 118) 100%);
        }
        .btn:hover {
            transform: translateY(-2px) scale(1.03);
            box-shadow: 0 4px 18px rgba(95,114,189,0.16);
            filter: brightness(1.08);
        }
        @media (max-width: 500px) {
            .container {
                padding: 20px 8px 18px 8px;
            }
            .main-title h2 {
                font-size: 1.4rem;
            }
        }
    </style>
</head>
<body>

<?php include 'includes/header.php'; ?>

<div class="container">
    <div class="main-title">
        <i class="fas fa-hotel" style="font-size: 3rem;"></i>
        <h2>Welcome to Your Hostel Portal</h2>
        <p>Safe, Secure &amp; Smart Living Experience</p>
    </div>

    <div class="actions">
        <a href="login.php" class="btn login">
            <i class="fas fa-sign-in-alt"></i> Login
        </a>
        <a href="register.php" class="btn register">
            <i class="fas fa-user-plus"></i> Student Register
        </a>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
</body>
</html>
