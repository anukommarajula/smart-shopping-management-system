<?php
session_start();
include('../includes/db.php');
error_reporting(E_ALL);
ini_set('display_errors', 1);

$order_id = $_GET['order_id'] ?? null;

if (!$order_id) {
    echo "<h2 style='color:red;text-align:center;'>Invalid access. Order ID missing.</h2>";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $payment_mode = $_POST['payment_mode'] ?? 'Unknown';
    $upi_app = $_POST['upi_app'] ?? null;
    $phone = $_POST['cod_phone'] ?? null;
    $address = $_POST['cod_address'] ?? null;
    $entered_otp = $_POST['otp'] ?? null;
    $action = $_POST['action'] ?? 'pay_now';

    $payment_note = $payment_mode;

    if ($payment_mode === 'UPI' && $upi_app) {
        $payment_note .= " - $upi_app";
    } elseif (in_array($payment_mode, ['Credit Card', 'Debit Card'])) {
        $card_number = $_POST['card_number'] ?? '';
        $payment_note .= " - " . str_repeat('X', 12) . substr($card_number, -4);
    } elseif ($payment_mode === 'Net Banking') {
        $bank = $_POST['bank'] ?? '';
        $payment_note .= " - $bank";
    } elseif ($payment_mode === 'Cash on Delivery') {
        if ($action === 'send_otp' && $phone && $address) {
            $_SESSION['cod_otp'] = rand(1000, 9999);
            $_SESSION['cod_phone'] = $phone;
            $_SESSION['cod_address'] = $address;
            echo "<script>alert('OTP sent to $phone. (Demo OTP: {$_SESSION['cod_otp']})'); window.location.href='payment.php?order_id=$order_id&show_otp=true';</script>";
            exit();
        }

        if ($action === 'confirm_cod' && $entered_otp == ($_SESSION['cod_otp'] ?? '')) {
            $payment_note .= " | Phone: {$_SESSION['cod_phone']} | Address: {$_SESSION['cod_address']}";
            unset($_SESSION['cod_otp'], $_SESSION['cod_phone'], $_SESSION['cod_address']);
        } elseif ($action === 'confirm_cod') {
            echo "<script>alert('Invalid OTP. Please try again.'); window.history.back();</script>";
            exit();
        }
    }

    // Update order payment
    $stmt = $conn->prepare("UPDATE orders SET payment_status='Paid', payment_mode=? WHERE id=?");
    $stmt->bindParam(1, $payment_note);
    $stmt->bindParam(2, $order_id);
    if ($stmt->execute()) {
        header("Location: success.php?order_id=$order_id&payment_mode=" . urlencode($payment_note));
        exit();
    } else {
        echo "<script>alert('Failed to update order.');</script>";
    }
}

$show_otp = isset($_GET['show_otp']) && isset($_SESSION['cod_otp']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Smart Checkout</title>
    <style>
        body { margin: 0; background: #f1f3f6; font-family: 'Segoe UI', sans-serif; }
        .wrapper {
            max-width: 600px;
            background: #fff;
            margin: 40px auto;
            padding: 30px 40px;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }
        h2 { text-align: center; color: #2c3e50; }
        .form-group { margin: 20px 0; }
        label { font-weight: 500; display: block; margin-bottom: 8px; color: #333; }
        input, select, textarea {
            width: 100%; padding: 12px 14px; font-size: 1em;
            border-radius: 6px; border: 1px solid #ccc; outline: none;
        }
        .flex { display: flex; gap: 10px; }
        .btn {
            padding: 14px; background: #007bff; border: none;
            color: #fff; font-weight: bold; border-radius: 6px; cursor: pointer;
        }
        .btn:hover { background: #0056cc; }
        .hidden { display: none; }
        .send-otp-btn {
            background: #28a745; margin-top: 8px; border: none;
            color: #fff; font-weight: bold; border-radius: 6px; padding: 10px; cursor: pointer;
        }
        .send-otp-btn:hover { background: #218838; }
    </style>
</head>
<body>
    <div class="wrapper">
        <h2>Secure Payment</h2>
        <form method="post">
            <input type="hidden" name="action" id="action" value="pay_now">
            <div class="form-group">
                <label>Order ID:</label>
                <input type="text" value="<?= htmlspecialchars($order_id) ?>" readonly>
            </div>

            <div class="form-group">
                <label>Payment Mode:</label>
                <select name="payment_mode" id="payment_mode" required>
                    <option value="">-- Select --</option>
                    <option value="UPI">UPI</option>
                    <option value="Credit Card">Credit Card</option>
                    <option value="Debit Card">Debit Card</option>
                    <option value="Net Banking">Net Banking</option>
                    <option value="Cash on Delivery">Cash on Delivery</option>
                </select>
            </div>

            <!-- UPI -->
            <div class="form-group hidden" id="upi_section">
                <label>Select UPI App:</label>
                <select name="upi_app">
                    <option value="">-- Choose App --</option>
                    <option value="Google Pay">Google Pay</option>
                    <option value="PhonePe">PhonePe</option>
                    <option value="Paytm">Paytm</option>
                </select>
            </div>

            <!-- Card -->
            <div class="form-group hidden" id="card_section">
                <label>Card Number:</label>
                <input type="text" name="card_number" maxlength="16" placeholder="xxxx-xxxx-xxxx-1234">
                <div class="flex">
                    <input type="text" name="expiry" placeholder="MM/YY">
                    <input type="text" name="cvv" maxlength="4" placeholder="CVV">
                </div>
            </div>

            <!-- Net Banking -->
            <div class="form-group hidden" id="bank_section">
                <label>Select Bank:</label>
                <select name="bank">
                    <option value="">-- Choose Bank --</option>
                    <option>SBI</option>
                    <option>HDFC</option>
                    <option>ICICI</option>
                    <option>Axis</option>
                    <option>PNB</option>
                    <option>Other</option>
                </select>
            </div>

            <!-- COD -->
            <div class="form-group hidden" id="cod_section">
                <label>Phone Number:</label>
                <div class="flex">
                    <input type="text" name="cod_phone" id="cod_phone" pattern="[0-9]{10}" maxlength="10" placeholder="e.g. 9876543210">
                    <button type="submit" class="send-otp-btn" name="action" value="send_otp">Send OTP</button>
                </div>

                <label>Delivery Address:</label>
                <textarea name="cod_address" placeholder="Enter address..."></textarea>

                <?php if ($show_otp): ?>
                    <label>Enter OTP:</label>
                    <input type="text" name="otp" maxlength="4" placeholder="4-digit OTP">
                    <input type="hidden" name="action" value="confirm_cod">
                <?php endif; ?>
            </div>

            <button class="btn" type="submit">💳 Pay Now</button>
        </form>
    </div>

    <script>
        const modeSelect = document.getElementById('payment_mode');
        const upi = document.getElementById('upi_section');
        const card = document.getElementById('card_section');
        const bank = document.getElementById('bank_section');
        const cod = document.getElementById('cod_section');

        function toggleSections() {
            const mode = modeSelect.value;
            upi.classList.add('hidden');
            card.classList.add('hidden');
            bank.classList.add('hidden');
            cod.classList.add('hidden');

            if (mode === 'UPI') upi.classList.remove('hidden');
            else if (mode === 'Credit Card' || mode === 'Debit Card') card.classList.remove('hidden');
            else if (mode === 'Net Banking') bank.classList.remove('hidden');
            else if (mode === 'Cash on Delivery') cod.classList.remove('hidden');
        }

        modeSelect.addEventListener('change', toggleSections);
        document.addEventListener('DOMContentLoaded', toggleSections);
    </script>
</body>
</html>
