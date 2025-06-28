<?php
session_start();

if (isset($_SESSION['user_id'])) {
    $role = $_SESSION['role'];
    if ($role === 'admin') {
        header("Location: admin_dashboard.php");
    } elseif ($role === 'warden') {
        header("Location: warden_dashboard.php");
    } else {
        header("Location: student_dashboard.php");
    }
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Login - Hostel Management System</title>
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }

    body, html {
        height: 100%;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background: url('images/login_icon.png') no-repeat center center fixed;
        background-size: cover;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .login-container {
        background-color: rgba(24, 131, 161, 0.1);
        padding: 50px 60px;
        border-radius: 20px;
        box-shadow: 0 14px 30px rgba(0, 0, 0, 0.5);
        width: 480px;
        max-width: 90%;
        text-align: center;
        color: #f0f4f8;
    }

    h2 {
        font-size: 2.5rem;
        margin-bottom: 30px;
        color: #e3f6ff;
        text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.4);
    }

    .error-message {
        background-color: #c0392b;
        padding: 12px;
        margin-bottom: 20px;
        border-radius: 10px;
        color: #fff;
        font-weight: bold;
    }

    form input[type="text"],
    form input[type="password"],
    form select {
        width: 100%;
        padding: 14px;
        margin-top: 10px;
        border: 2px solid #5dade2;
        border-radius: 10px;
        font-size: 1rem;
        background: #f1f7fc;
        color: #333;
    }

    input:focus, select:focus {
        border-color: #3498db;
        background-color: #fff;
        box-shadow: 0 0 10px #3498db;
    }

    button {
        width: 100%;
        padding: 14px;
        margin-top: 20px;
        border: none;
        border-radius: 25px;
        background: linear-gradient(to right, #3dbec3, #1f618d);
        color: #fff;
        font-size: 1.3rem;
        font-weight: bold;
        cursor: pointer;
        transition: background 0.3s ease;
        box-shadow: 0 8px 20px #3dbec3;
    }

    button:hover {
        background: linear-gradient(to right, #1f618d, #2980b9);
    }

    p {
        margin-top: 20px;
        font-size: 1rem;
        color: rgb(185, 16, 16);
    }

    p a {
        color: #ffffff;
        font-weight: bold;
        text-decoration: none;
    }

    p a:hover {
        text-decoration: underline;
        color: #d0eaff;
    }

    .error-text {
        color: #ff4d4d;
        font-size: 0.9rem;
        margin-top: 5px;
        margin-bottom: 15px;
        display: none;
        text-align: left;
    }
</style>
</head>
<body>
    <div class="login-container">
        <h2>Login</h2>

        <?php
        if (isset($_SESSION['error'])) {
            echo "<div class='error-message'>" . htmlspecialchars($_SESSION['error']) . "</div>";
            unset($_SESSION['error']);
        }
        ?>

        <form id="loginForm" action="login_process.php" method="post" novalidate>
            <input type="text" name="username" id="username" placeholder="Username" autocomplete="username" autofocus>
            <small id="username-error" class="error-text">Please fill out this field</small>

            <input type="password" name="password" id="password" placeholder="Password" autocomplete="current-password">
            <small id="password-error" class="error-text">Please fill out this field</small>

            <select name="role" id="role">
                <option value="">-- Select Role --</option>
                <option value="student">Student</option>
                <option value="warden">Warden</option>
                <option value="admin">Admin</option>
            </select>
            <small id="role-error" class="error-text">Please select a role</small>

            <button type="submit">Login</button>
        </form>

        <p>Not registered? Students can <a href="register.php">Register here</a></p>
    </div>

<script>
const form = document.getElementById('loginForm');
const usernameInput = document.getElementById('username');
const passwordInput = document.getElementById('password');
const roleSelect = document.getElementById('role');
const usernameError = document.getElementById('username-error');
const passwordError = document.getElementById('password-error');
const roleError = document.getElementById('role-error');

form.addEventListener('submit', function(e) {
    let valid = true;

    if (usernameInput.value.trim() === '') {
        usernameError.style.display = 'block';
        valid = false;
    } else {
        usernameError.style.display = 'none';
    }

    if (passwordInput.value.trim() === '') {
        passwordError.style.display = 'block';
        valid = false;
    } else {
        passwordError.style.display = 'none';
    }

    if (roleSelect.value === '') {
        roleError.style.display = 'block';
        valid = false;
    } else {
        roleError.style.display = 'none';
    }

    if (!valid) {
        e.preventDefault();
    }
});
</script>
</body>
</html>
