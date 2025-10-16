<?php
session_start();
include('../includes/db.php'); // Adjust if needed

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Fetch orders
$query = "SELECT id, user_id, payment_status, payment_mode, created_at FROM orders ORDER BY created_at DESC";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>All Orders</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8fafc;
            margin: 0;
            padding: 40px 20px;
        }

        h2 {
            font-size: 32px;
            text-align: center;
            color: #1e293b;
            margin-bottom: 30px;
        }

        .container {
            max-width: 1100px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 16px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.06);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 15px;
            color: #334155;
        }

        th, td {
            padding: 14px 16px;
            border-bottom: 1px solid #e2e8f0;
        }

        th {
            background-color: #1e40af;
            color: white;
            text-align: left;
            font-weight: 600;
        }

        tr:hover {
            background-color: #f1f5f9;
            transition: background-color 0.2s ease;
        }

        .badge {
            padding: 5px 10px;
            border-radius: 999px;
            font-weight: 600;
            font-size: 13px;
            display: inline-block;
        }

        .paid {
            background-color: #dcfce7;
            color: #15803d;
        }

        .unpaid {
            background-color: #fee2e2;
            color: #b91c1c;
        }

        .mode {
            background-color: #e0f2fe;
            color: #0369a1;
        }

        .footer-note {
            text-align: center;
            font-size: 14px;
            color: #64748b;
            margin-top: 30px;
        }
    </style>
</head>
<body>
    <h2>📦 All Orders Overview</h2>

    <div class="container">
        <table>
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>User ID</th>
                    <th>Payment Status</th>
                    <th>Payment Mode</th>
                    <th>Order Date</th>
                </tr>
            </thead>
            <tbody>
                <?php if (mysqli_num_rows($result) > 0): ?>
                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td>#<?= htmlspecialchars($row['id']) ?></td>
                            <td><?= htmlspecialchars($row['user_id']) ?></td>
                            <td>
                                <span class="badge <?= $row['payment_status'] === 'Paid' ? 'paid' : 'unpaid' ?>">
                                    <?= htmlspecialchars($row['payment_status']) ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge mode">
                                    <?= htmlspecialchars($row['payment_mode']) ?>
                                </span>
                            </td>
                            <td><?= date('d M Y, h:i A', strtotime($row['created_at'])) ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="5" style="text-align:center; color:#64748b;">No orders found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="footer-note">
        &copy; <?= date("Y") ?> Smart Shopping Admin Panel
    </div>
</body>
</html>
