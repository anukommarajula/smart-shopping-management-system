<?php
session_start();
include('../includes/db.php'); // Adjust path as needed

// Display errors for debugging (disable in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

$errors = [];
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize input
    $username = trim($_POST["username"] ?? '');
    $email = trim($_POST["email"] ?? '');
    $password = $_POST["password"] ?? '';
    $confirm_password = $_POST["confirm_password"] ?? '';

    // Validate input
    if (empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
        $errors[] = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    } elseif ($password !== $confirm_password) {
        $errors[] = "Passwords do not match.";
    } else {
        // Check if email already exists
        $stmt = $conn->prepare("SELECT id FROM admins WHERE email = :email");
        $stmt->execute(['email' => $email]);
        if ($stmt->fetch()) {
            $errors[] = "Email is already registered.";
        } else {
            // Hash the password
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

            // Insert into database
            $stmt = $conn->prepare("INSERT INTO admins (username, email, password) VALUES (:username, :email, :password)");
            $result = $stmt->execute([
                'username' => $username,
                'email' => $email,
                'password' => $hashedPassword
            ]);

            if ($result) {
                $success = "Admin registered successfully.";
            } else {
                $errors[] = "Something went wrong. Please try again.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Registration</title>
    <style>
        body { font-family: Arial; padding: 20px; }
        .form-box { max-width: 400px; margin: auto; }
        .error { color: red; }
        .success { color: green; }
    </style>
</head>
<body>
<div class="form-box">
    <h2>Register Admin</h2>

    <?php if ($errors): ?>
        <div class="error">
            <?php foreach ($errors as $error) echo "<p>$error</p>"; ?>
        </div>
    <?php elseif ($success): ?>
        <div class="success"><?php echo $success; ?></div>
    <?php endif; ?>

    <form method="POST" action="">
        <label>Username:</label><br>
        <input type="text" name="username" required><br><br>

        <label>Email:</label><br>
        <input type="email" name="email" required><br><br>

        <label>Password:</label><br>
        <input type="password" name="password" required><br><br>

        <label>Confirm Password:</label><br>
        <input type="password" name="confirm_password" required><br><br>

        <button type="submit">Register</button>
    </form>
</div>
</body>
</html>
