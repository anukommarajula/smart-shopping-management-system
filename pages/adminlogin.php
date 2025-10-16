<?php
session_start();
include('../includes/db.php'); // adjust if needed

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"] ?? '');
    $password = $_POST["password"] ?? '';

    if (empty($email) || empty($password)) {
        $error = "Please enter email and password.";
    } else {
        // Ensure the 'admins' table exists and contains your admin accounts
        $stmt = $conn->prepare("SELECT * FROM admins WHERE email = :email");
        $stmt->execute(['email' => $email]);
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($admin && password_verify($password, $admin['password'])) {
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_username'] = $admin['username'];
            $_SESSION['role'] = 'admin';

            // Redirect to dashboard
            header("Location: dashboard.php");  // make sure this file exists
            exit;
        } else {
            $error = "Invalid credentials";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Login</title>
    <style>
        body { font-family: Arial; padding: 20px; background-color: #f1f1f1; }
        .form-box {
            max-width: 400px;
            margin: 100px auto;
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .error { color: red; margin-bottom: 10px; }
        label { display: block; margin-top: 10px; }
        input[type="email"], input[type="password"] {
            width: 100%;
            padding: 10px;
            margin-top: 4px;
        }
        button {
            margin-top: 20px;
            padding: 10px;
            width: 100%;
            background: #007bff;
            color: white;
            border: none;
            font-weight: bold;
            cursor: pointer;
        }
        button:hover { background: #0056cc; }
    </style>
</head>
<body>
<div class="form-box">
    <h2>Admin Login</h2>

    <?php if ($error): ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" action="">
        <label>Email:</label>
        <input type="email" name="email" required>

        <label>Password:</label>
        <input type="password" name="password" required>

        <button type="submit">Login</button>
    </form>
</div>
</body>
</html>
