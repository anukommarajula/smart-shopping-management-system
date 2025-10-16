<?php
session_start();
include('../includes/db.php');

if (!isset($_SESSION['admin_id'])) {
    header("Location: adminlogin.php");
    exit;
}

try {
    $stmt = $conn->query("SELECT * FROM products ORDER BY created_at DESC");
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error fetching products: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>All Products</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-sans">

<div class="max-w-7xl mx-auto mt-10 p-6 bg-white rounded-lg shadow-lg">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-3xl font-bold text-gray-800">All Products</h2>
        <a href="add_product.php" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">➕ Add Product</a><a href="add_product.php" ...>➕ Add Product</a>

    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full border border-gray-300 text-sm">
            <thead class="bg-gray-100 text-left">
                <tr>
                    <th class="py-3 px-4 border-b">Product ID</th>
                    <th class="py-3 px-4 border-b">Name</th>
                    <th class="py-3 px-4 border-b">Description</th>
                    <th class="py-3 px-4 border-b">Price</th>
                    <th class="py-3 px-4 border-b">Image</th>
                    <th class="py-3 px-4 border-b">Created At</th>
                    <th class="py-3 px-4 border-b">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($products): ?>
                    <?php foreach ($products as $product): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="py-3 px-4 border-b"><?= $product['id'] ?></td>
                            <td class="py-3 px-4 border-b"><?= htmlspecialchars($product['name']) ?></td>
                            <td class="py-3 px-4 border-b"><?= htmlspecialchars(substr($product['description'], 0, 50)) ?>...</td>
                            <td class="py-3 px-4 border-b">₹<?= number_format($product['price'], 2) ?></td>
                            <td class="py-3 px-4 border-b">
                                <?php if (!empty($product['image'])): ?>
                                    <img src="../uploads/<?= htmlspecialchars($product['image']) ?>" alt="Product Image" class="h-12 w-12 object-cover rounded">
                                <?php else: ?>
                                    <span class="text-gray-400">No Image</span>
                                <?php endif; ?>
                            </td>
                            <td class="py-3 px-4 border-b"><?= htmlspecialchars($product['created_at']) ?></td>
                            <td class="py-3 px-4 border-b space-x-2">
                                <a href="edit_product.php?id=<?= $product['id'] ?>" class="text-blue-600 hover:underline">Edit</a>
                                <a href="delete_product.php?id=<?= $product['id'] ?>" class="text-red-600 hover:underline" onclick="return confirm('Are you sure?')">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="text-center py-6 text-gray-500">No products found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        <a href="dashboard.php" class="text-blue-600 hover:underline">⬅ Back to Dashboard</a>
    </div>
</div>

</body>
</html>
