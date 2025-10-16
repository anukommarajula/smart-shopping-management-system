<?php
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: adminlogin.php");
    exit;
}

$adminName = $_SESSION['admin_username'] ?? 'Admin';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f6f9;
            margin: 0;
            padding: 0;
        }
        .header {
            background: #007bff;
            color: #fff;
            padding: 20px;
            text-align: center;
            position: relative;
        }
        .logout-btn {
            position: absolute;
            top: 20px;
            right: 20px;
            background: #dc3545;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 6px;
            cursor: pointer;
            font-weight: bold;
        }
        .logout-btn:hover {
            background: #c82333;
        }
        .container {
            max-width: 1000px;
            margin: 40px auto;
            background: #fff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        .section {
            margin: 20px 0;
        }
        h2 {
            color: #333;
        }
        ul {
            padding-left: 20px;
        }
        a {
            color: #007bff;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="header">
    <h1>Welcome, <?= htmlspecialchars($adminName) ?></h1>
    <form method="post">
        <button class="logout-btn" name="logout" type="submit">Logout</button>
    </form>
</div>

<div class="container">
    <div class="section">
        <h2>📦 Orders</h2>
        <ul>
            <li><a href="orders.php">View All Orders</a></li>
            <li><a href="orders.php?status=pending">Pending Orders</a></li>
            <li><a href="orders.php?status=completed">Completed Orders</a></li>
        </ul>
    </div>

    <div class="section">
        <h2>🛍 Products</h2>
        <ul>
            <li><a href="add_product.php">Add New Product</a></li>
            <li><a href="products.php">Manage Products</a></li>
        </ul>
    </div>

    <div class="section">
        <h2>👥 Users</h2>
        <ul>
            <li><a href="users.php">View Customers</a></li>
            <li><a href="admins.php">Manage Admins</a></li>
        </ul>
    </div>
</div>

<?php
if (isset($_POST['logout'])) {
    session_destroy();
    header("Location: adminlogin.php");
    exit;
}
?>

</body>
</html>

