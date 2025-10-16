<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include '../includes/db.php';
$user_id = $_SESSION['user_id'];

// ✅ Cancel order handler
if (isset($_GET['cancel']) && is_numeric($_GET['cancel'])) {
    $order_id = $_GET['cancel'];

    $check = $conn->prepare("SELECT * FROM orders WHERE id = ? AND user_id = ? AND status != 'Canceled'");
    $check->execute([$order_id, $user_id]);

    if ($check->rowCount() > 0) {
        $cancel = $conn->prepare("UPDATE orders SET status = 'Canceled' WHERE id = ?");
        $cancel->execute([$order_id]);
        header("Location: profile.php");
        exit();
    }
}

// ✅ Fetch orders
$stmt = $conn->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$user_id]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Orders</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f1f5f9;
            margin: 0;
            padding: 40px 20px;
        }

        h2 {
            text-align: center;
            color: #1e293b;
            font-size: 32px;
            margin-bottom: 30px;
        }

        .orders-container {
            max-width: 900px;
            margin: auto;
        }

        .order-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.04);
            margin-bottom: 25px;
            padding: 20px 24px;
            transition: box-shadow 0.2s ease;
        }

        .order-card:hover {
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
        }

        .order-header {
            font-size: 18px;
            font-weight: 600;
            color: #1e40af;
            margin-bottom: 10px;
        }

        .order-details p {
            margin: 6px 0;
            color: #334155;
            font-size: 15px;
        }

        .tag {
            display: inline-block;
            padding: 4px 10px;
            font-size: 13px;
            font-weight: 600;
            border-radius: 999px;
            text-transform: capitalize;
        }

        .status-pending {
            background-color: #fef3c7;
            color: #92400e;
        }

        .status-canceled {
            background-color: #fee2e2;
            color: #b91c1c;
        }

        .status-delivered {
            background-color: #dcfce7;
            color: #15803d;
        }

        .actions {
            margin-top: 12px;
        }

        .action-button {
            text-decoration: none;
            padding: 6px 12px;
            margin-right: 10px;
            border-radius: 6px;
            font-weight: 500;
            font-size: 14px;
        }

        .edit-btn {
            background-color: #e0f2fe;
            color: #0369a1;
        }

        .cancel-btn {
            background-color: #fee2e2;
            color: #b91c1c;
        }

        .no-orders {
            text-align: center;
            color: #64748b;
            font-size: 16px;
            padding: 60px 20px;
        }
    </style>
</head>
<body>

<h2>📦 My Orders</h2>

<div class="orders-container">
    <?php if (empty($orders)) : ?>
        <div class="no-orders">You haven't placed any orders yet.</div>
    <?php else : ?>
        <?php foreach ($orders as $order) : ?>
            <div class="order-card">
                <div class="order-header">Order #<?= htmlspecialchars($order['id']) ?></div>
                <div class="order-details">
                    <p><strong>Address:</strong><br><?= nl2br(htmlspecialchars($order['address'])) ?></p>

                    <?php if (isset($order['payment_method'])) : ?>
                        <p><strong>Payment Method:</strong> <?= htmlspecialchars($order['payment_method']) ?></p>
                    <?php endif; ?>

                    <p><strong>Date & Time:</strong> 
                        <?= date('d M Y, h:i A', strtotime($order['created_at'])) ?>
                    </p>

                    <p><strong>Status:</strong>
                        <span class="tag status-<?= strtolower($order['status']) ?>">
                            <?= htmlspecialchars($order['status']) ?>
                        </span>
                    </p>

                    <?php if ($order['status'] === 'Pending') : ?>
                        <div class="actions">
                            <a class="action-button edit-btn" href="edit_order.php?order_id=<?= $order['id'] ?>">✏️ Edit Address</a>
                            <a class="action-button cancel-btn" href="profile.php?cancel=<?= $order['id'] ?>"
                               onclick="return confirm('Are you sure you want to cancel this order?')">❌ Cancel</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

</body>
</html>
