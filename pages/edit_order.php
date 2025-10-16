<?php
session_start();
include '../includes/db.php';

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$order_id = $_GET['order_id'] ?? null;

if (!$order_id) {
    echo "Invalid order ID.";
    exit();
}

// Fetch order to ensure ownership
$stmt = $conn->prepare("SELECT * FROM orders WHERE id = ? AND user_id = ?");
$stmt->execute([$order_id, $_SESSION['user_id']]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    echo "Order not found or access denied.";
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $address = trim($_POST['address']);
    $city = trim($_POST['city']);
    $state = trim($_POST['state']);
    $pincode = trim($_POST['pincode']);

    if ($address && $city && $state && $pincode) {
        $update = $conn->prepare("UPDATE orders SET address = ?, city = ?, state = ?, pincode = ? WHERE id = ? AND user_id = ?");
        $update->execute([$address, $city, $state, $pincode, $order_id, $_SESSION['user_id']]);

        header("Location: profile.php");
        exit();
    } else {
        $error = "All fields are required.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Delivery Address</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f1f5f9;
            padding: 40px 20px;
        }
        .container {
            max-width: 600px;
            background: white;
            margin: auto;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 6px 15px rgba(0,0,0,0.08);
        }
        h2 {
            font-size: 24px;
            color: #1e293b;
            margin-bottom: 20px;
        }
        label {
            font-weight: 600;
            margin-top: 15px;
            display: block;
            color: #334155;
        }
        input, textarea {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            margin-top: 6px;
            font-size: 15px;
        }
        textarea {
            resize: vertical;
            min-height: 80px;
        }
        .btn {
            margin-top: 25px;
            background-color: #2563eb;
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 6px;
            font-weight: 600;
            font-size: 16px;
            cursor: pointer;
        }
        .btn:hover {
            background-color: #1e40af;
        }
        .error {
            color: red;
            margin-top: 10px;
        }
        .back-link {
            display: inline-block;
            margin-bottom: 20px;
            text-decoration: none;
            color: #2563eb;
        }
    </style>
</head>
<body>

<a href="profile.php" class="back-link">← Back to My Orders</a>

<div class="container">
    <h2>Edit Address for Order #<?= htmlspecialchars($order['id']) ?></h2>

    <?php if (isset($error)) : ?>
        <p class="error"><?= $error ?></p>
    <?php endif; ?>

    <form method="POST">
        <label for="address">Delivery Address</label>
        <textarea name="address" id="address" required><?= htmlspecialchars($order['address']) ?></textarea>

        <label for="city">City</label>
        <input type="text" name="city" id="city" value="<?= htmlspecialchars($order['city'] ?? '') ?>" required>

        <label for="state">State</label>
        <input type="text" name="state" id="state" value="<?= htmlspecialchars($order['state'] ?? '') ?>" required>

        <label for="pincode">Pincode</label>
        <input type="text" name="pincode" id="pincode" value="<?= htmlspecialchars($order['pincode'] ?? '') ?>" required>

        <button type="submit" class="btn">Update Address</button>
    </form>
</div>

</body>
</html>
