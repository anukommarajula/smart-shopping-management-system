<?php
$order_id = $_GET['order_id'] ?? 'N/A';
$payment_mode = $_GET['payment_mode'] ?? 'Unknown';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Order Success</title>
    <style>
        body {
            background-color: #e8f5e9;
            font-family: Arial, sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
        }
        .success-box {
            background: white;
            padding: 40px;
            border-radius: 12px;
            text-align: center;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }
        .success-box h2 {
            color: #2e7d32;
        }
        .success-box .info {
            margin: 15px 0;
            font-size: 18px;
            color: #333;
        }
        .success-box .info strong {
            color: #000;
        }
    </style>
</head>
<body>
    <div class="success-box">
        <h2>✅ Order Successful!</h2>
        <p class="info"><strong>Order ID:</strong> <?= htmlspecialchars($order_id) ?></p>
        <p class="info"><strong>Payment Mode:</strong> <?= htmlspecialchars($payment_mode) ?></p>
        <p>Thank you for shopping with us!</p>
    </div>
</body>
</html>
