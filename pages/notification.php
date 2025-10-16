<?php
// Error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Simulated admin email (change as needed)
$admin_email = "admin@example.com";

// Flags
$registration_success = false;
$admin_notified = false;

// Handle form submit
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $email = $_POST['email'] ?? '';

    if (!empty($username) && !empty($password) && !empty($email)) {
        // TODO: Save to database here (skipped for this example)

        // Set flag to show user success message
        $registration_success = true;

        // -------- Notify Admin via Email --------
        $subject = "New User Registration";
        $message = "A new user has registered:\n\nUsername: $username\nEmail: $email";
        $headers = "From: no-reply@example.com";

        // Simulate sending email (real email sending depends on mail server)
        if (mail($admin_email, $subject, $message, $headers)) {
            $admin_notified = true;
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>User Registration</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container p-4">

    <h2>Register</h2>

    <?php if ($registration_success): ?>
        <div class="alert alert-success">
            ✅ You have registered successfully!
        </div>
    <?php endif; ?>

    <?php if ($registration_success && $admin_notified): ?>
        <div class="alert alert-info">
            📧 Admin has been notified.
        </div>
    <?php elseif ($registration_success && !$admin_notified): ?>
        <div class="alert alert-warning">
            ⚠️ Registration successful, but admin notification failed.
        </div>
    <?php endif; ?>

    <form method="POST" action="">
        <div class="mb-3">
            <label>Username</label>
            <input type="text" name="username" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Email</label>
            <input type="email" name="email" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Password</label>
            <input type="password" name="password" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-primary">Register</button>
    </form>

</body>
</html>

