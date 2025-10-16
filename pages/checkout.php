<?php
session_start();
include('../includes/db.php'); // uses PDO
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Simulated user_id and cart total
$user_id = $_SESSION['user_id'] ?? 1;
$total_amount = 500.00;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $address = $_POST['address'];
    $city = $_POST['city'];
    $state = $_POST['state'];
    $pincode = $_POST['pincode'];

    // Combine full address
    $full_address = "$address, $city, $state - $pincode";

    $stmt = $conn->prepare("INSERT INTO orders (user_id, address, total_amount) VALUES (?, ?, ?)");
    $stmt->execute([$user_id, $full_address, $total_amount]);

    $order_id = $conn->lastInsertId();
    header("Location: payment.php?order_id=$order_id");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Checkout</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f3f4f6;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 550px;
            margin: 60px auto;
            padding: 40px;
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }

        h2 {
            text-align: center;
            color: #333333;
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        label {
            font-weight: 500;
            color: #444;
            margin-bottom: 6px;
        }

        textarea,
        input,
        select {
            width: 100%;
            padding: 12px;
            font-size: 14px;
            border: 1px solid #ccc;
            border-radius: 8px;
            box-sizing: border-box;
        }

        .row {
            display: flex;
            gap: 12px;
        }

        .row > div {
            flex: 1;
        }

        .amount {
            font-size: 18px;
            color: #007b5e;
            font-weight: bold;
            margin-top: 10px;
        }

        button {
            margin-top: 20px;
            padding: 14px;
            background-color: #007b5e;
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        button:hover {
            background-color: #005a45;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Checkout</h2>
        <form method="post">
            <div>
                <label for="address">Delivery Address:</label>
                <textarea name="address" id="address" required></textarea>
            </div>

            <div class="row">
                <div>
                    <label for="city">City:</label>
                    <input type="text" name="city" id="city" required>
                </div>
                <div>
                    <label for="state">State:</label>
                    <input type="text" name="state" id="state" required>
                </div>
            </div>

            <div>
                <label for="pincode">Pin Code:</label>
                <input type="text" name="pincode" id="pincode" pattern="\d{6}" title="Enter 6-digit pincode" required>
            </div>

            <div class="amount">
                Total Amount: ₹<?= number_format($total_amount, 2) ?>
            </div>

            <button type="submit">Proceed to Payment</button>
        </form>
    </div>
</body>
</html>

