<?php
session_start();
include('../includes/db.php');

if (!isset($_SESSION['admin_id'])) {
    header("Location: adminlogin.php");
    exit;
}

$product_id = $_GET['id'] ?? null;

if (!$product_id) {
    die("Product ID is missing.");
}

// Fetch product info
$stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$product_id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    die("Product not found.");
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $price = floatval($_POST['price']);
    $description = trim($_POST['description']);
    $image = $product['image']; // default to existing image

    // Handle image upload if new one is selected
    if (!empty($_FILES['image']['name'])) {
        $target_dir = "../uploads/";
        $image = basename($_FILES['image']['name']);
        $target_file = $target_dir . $image;

        move_uploaded_file($_FILES['image']['tmp_name'], $target_file);
    }

    // Update query
    $stmt = $conn->prepare("UPDATE products SET name = ?, description = ?, price = ?, image = ? WHERE id = ?");
    $stmt->execute([$name, $description, $price, $image, $product_id]);

    header("Location: products.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Product</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">

<div class="max-w-xl mx-auto mt-10 p-6 bg-white rounded-lg shadow-lg">
    <h2 class="text-2xl font-bold mb-4 text-gray-700">✏️ Edit Product</h2>

    <form method="POST" enctype="multipart/form-data">
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">Product Name</label>
            <input type="text" name="name" value="<?= htmlspecialchars($product['name']) ?>" required class="mt-1 block w-full border border-gray-300 rounded-md p-2">
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">Price (₹)</label>
            <input type="number" name="price" step="0.01" value="<?= $product['price'] ?>" required class="mt-1 block w-full border border-gray-300 rounded-md p-2">
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">Description</label>
            <textarea name="description" rows="4" required class="mt-1 block w-full border border-gray-300 rounded-md p-2"><?= htmlspecialchars($product['description']) ?></textarea>
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">Image</label>
            <?php if (!empty($product['image'])): ?>
                <img src="../uploads/<?= htmlspecialchars($product['image']) ?>" alt="Current Image" class="h-20 mb-2 rounded">
            <?php endif; ?>
            <input type="file" name="image" accept="image/*" class="block w-full text-sm text-gray-500">
        </div>

        <div class="flex items-center justify-between">
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Update Product</button>
            <a href="products.php" class="text-gray-600 hover:underline">Cancel</a>
        </div>
    </form>
</div>

</body>
</html>
