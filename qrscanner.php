<?php
session_start();
include('../includes/db.php');

$order_id = $_SESSION['order_id'] ?? null;
$payment_note = $_SESSION['payment_note'] ?? null;

// Replace this with your actual UPI ID
$upi_id = "yourname@upi";

if (!$order_id || !$payment_note) {
    echo "Invalid access.";
    exit;
}

// Simulate UPI scan by showing QR code and a "Mark as Paid" button
?>

<!DOCTYPE html>
<html>
<head>
    <title>Scan UPI QR</title>
    <style>
        body {
            font-family: Arial;
            text-align: center;
            background: #f9f9f9;
            padding: 50px;
        }
        .qr-box {
            background: #fff;
            display: inline-block;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
        }
        img {
            width: 250px;
            height: 250px;
        }
        button {
            margin-top: 25px;
            padding: 15px 30px;
            font-size: 16px;
            background: green;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
        }
        button:hover {
            background: darkgreen;
        }
    </style>
</head>
<body>

<div class="qr-box">
    <h2>Scan & Pay via GPay/PhonePe/Paytm</h2>
    <p>Scan this UPI QR to pay:</p>

    <!-- Replace with your generated QR image (use Google Charts API) -->
    <img src="https://chart.googleapis.com/chart?cht=qr&chs=250x250&chl=upi://pay?pa=<?= urlencode($upi_id) ?>&pn=SmartShop&cu=INR&am=10" alt="UPI QR">

    <form method="post">
        <input type="hidden" name="mark_paid" value="yes">
        <button type="submit">✅ I've Paid</button>
    </form>
</div>

</body>
</html>

<?php
// On "Mark as Paid", update DB
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['mark_paid'])) {
    $stmt = $conn->prepare("UPDATE orders SET payment_status = 'Paid', payment_mode = :payment_mode WHERE id = :order_id");
    $stmt->execute([
        'payment_mode' => $payment_note,
        'order_id' => $order_id
    ]);

    // Cleanup
    unset($_SESSION['order_id'], $_SESSION['payment_note']);

    header("Location: success.php?order_id=$order_id&payment_mode=" . urlencode($payment_note));
    exit;
}
?>
