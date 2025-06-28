<?php
// temp_add_user.php
// Temporary script to add users to 'user' table with hashed password

require 'includes/db_connect.php';  // your $conn is MySQLi connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $role = $_POST['role']; // should be 'student', 'warden', or 'admin'

    // Validate inputs (simple)
    if (empty($username) || empty($password) || !in_array($role, ['student','warden','admin'])) {
        echo "Invalid input.";
        exit;
    }

    // Hash password
    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    // Prepare and execute insert
    $stmt = $conn->prepare("INSERT INTO user (username, password, role) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $username, $password_hash, $role);

    if ($stmt->execute()) {
        echo "User added successfully: $username ($role)";
    } else {
        echo "Error adding user: " . $stmt->error;
    }

} else {
    // Show simple form
    ?>
    <h2>Add User to user table</h2>
    <form method="post">
        Username: <input type="text" name="username" maxlength="10" required><br><br>
        Password: <input type="password" name="password" required><br><br>
        Role:
        <select name="role" required>
            <option value="">Select Role</option>
            <option value="student">Student</option>
            <option value="warden">Warden</option>
            <option value="admin">Admin</option>
        </select><br><br>
        <button type="submit">Add User</button>
    </form>
    <?php
}
?>
