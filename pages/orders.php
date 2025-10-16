<?php
session_start();
include('../includes/db.php');

if (!isset($_SESSION['admin_id'])) {
    header("Location: adminlogin.php");
    exit;
}

try {
    $stmt = $conn->query("
        SELECT o.*, u.username 
        FROM orders o
        LEFT JOIN users u ON o.user_id = u.id
        ORDER BY o.created_at DESC
    ");
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error fetching orders: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>All Orders</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-sans">

<div class="max-w-7xl mx-auto mt-10 p-6 bg-white rounded-lg shadow-lg">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-3xl font-bold text-gray-800">All Orders</h2>
        <a href="dashboard.php" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">⬅ Back to Dashboard</a>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full border border-gray-300 text-sm">
            <thead class="bg-gray-100 text-left">
                <tr>
                    <th class="py-3 px-4 border-b">Order ID</th>
                    <th class="py-3 px-4 border-b">Customer</th>
                    <th class="py-3 px-4 border-b">Address</th>
                    <th class="py-3 px-4 border-b">Amount</th>
                    <th class="py-3 px-4 border-b">Payment Mode</th>
                    <th class="py-3 px-4 border-b">Status</th>
                    <th class="py-3 px-4 border-b">Date</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($orders): ?>
                    <?php foreach ($orders as $order): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="py-3 px-4 border-b"><?= htmlspecialchars($order['id']) ?></td>
                            <td class="py-3 px-4 border-b"><?= htmlspecialchars($order['username'] ?? 'Unknown') ?></td>
                            <td class="py-3 px-4 border-b"><?= htmlspecialchars($order['address']) ?></td>
                            <td class="py-3 px-4 border-b">₹<?= number_format($order['total_amount'], 2) ?></td>
                            <td class="py-3 px-4 border-b"><?= ucfirst($order['payment_mode']) ?></td>
                            <td class="py-3 px-4 border-b">
                                <?php if ($order['payment_status'] === 'Paid'): ?>
                                    <span class="px-2 py-1 inline-block rounded bg-green-100 text-green-700 font-semibold">Paid</span>
                                <?php else: ?>
                                    <span class="px-2 py-1 inline-block rounded bg-red-100 text-red-700 font-semibold"><?= htmlspecialchars($order['payment_status']) ?></span>
                                <?php endif; ?>
                            </td>
                            <td class="py-3 px-4 border-b"><?= htmlspecialchars($order['created_at']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="py-6 text-center text-gray-500">No orders found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>
