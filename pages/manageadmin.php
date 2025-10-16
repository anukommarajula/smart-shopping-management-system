<?php
// orders.php
include('../includes/db.php');
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: adminlogin.php");
    exit;
}

$status = $_GET['status'] ?? '';
$query = "SELECT * FROM orders";
if ($status === 'pending') {
    $query .= " WHERE payment_status = 'Pending'";
} elseif ($status === 'completed') {
    $query .= " WHERE payment_status = 'Paid'";
}
$stmt = $conn->query($query);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<h2>Orders (<?= htmlspecialchars(ucfirst($status ?: 'All')) ?>)</h2>
<table border="1" cellpadding="10">
    <tr><th>ID</th><th>User ID</th><th>Total</th><th>Status</th><th>Date</th></tr>
    <?php foreach ($orders as $order): ?>
    <tr>
        <td><?= $order['id'] ?></td>
        <td><?= $order['user_id'] ?></td>
        <td><?= $order['total'] ?></td>
        <td><?= $order['payment_status'] ?></td>
        <td><?= $order['created_at'] ?></td>
    </tr>
    <?php endforeach; ?>
</table>
