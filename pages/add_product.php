<?php
session_start();
include('../includes/db.php');

// Restrict to admin login
if (!isset($_SESSION['admin_id'])) {
    header("Location: adminlogin.php");
    exit;
}

$product_id = $name = $description = $price = "";
$success = $error = "";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
   
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $price = floatval($_POST['price']);
    $image = "";

    if (empty($product_id) || empty($name) || empty($description) || $price <= 0) {
        $error = "⚠️ Please fill in all fields correctly.";
    } else {
        // Handle image upload
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $maxSize = 2 * 1024 * 1024;

        if (!empty($_FILES['image']['name'])) {
            $filename = $_FILES['image']['name'];
            $tmpPath = $_FILES['image']['tmp_name'];
            $fileSize = $_FILES['image']['size'];
            $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

            if (!in_array($ext, $allowed)) {
                $error = "⚠️ Only JPG, JPEG, PNG, and GIF allowed.";
            } elseif ($fileSize > $maxSize) {
                $error = "⚠️ Max image size is 2MB.";
            } else {
                $newName = uniqid("prod_") . "." . $ext;
                $uploadDir = "../uploads/";
                $uploadPath = $uploadDir . $newName;

                if (!move_uploaded_file($tmpPath, $uploadPath)) {
                    $error = "❌ Image upload failed.";
                } else {
                    $image = $newName;
                }
            }
        }

        // Insert product
        if (empty($error)) {
            try {
                $stmt = $conn->prepare("INSERT INTO products (id, name, description, price, image) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$product_id, $name, $description, $price, $image]);
                $success = "✅ Product added successfully!";
                $product_id = $name = $description = $price = "";
            } catch (PDOException $e) {
                $error = "❌ Error: " . $e->getMessage();
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Product</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-sans">
    <div class="max-w-3xl mx-auto mt-10 p-8 bg-white shadow rounded-lg">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">➕ Add Product</h1>
            <a href="product.php" class="text-blue-600 hover:underline">← Back to Products</a>
        </div>

        <?php if ($error): ?>
            <div class="mb-4 p-3 bg-red-100 text-red-700 rounded"><?= $error ?></div>
        <?php elseif ($success): ?>
            <div class="mb-4 p-3 bg-green-100 text-green-700 rounded"><?= $success ?></div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data" class="space-y-5">
         

            <div>
                <label class="block font-medium">Name:</label>
                <input type="text" name="name" value="<?= htmlspecialchars($name) ?>" required class="w-full px-4 py-2 border rounded">
            </div>

            <div>
                <label class="block font-medium">Description:</label>
                <textarea name="description" required class="w-full px-4 py-2 border rounded"><?= htmlspecialchars($description) ?></textarea>
            </div>

            <div>
                <label class="block font-medium">Price:</label>
                <input type="number" name="price" value="<?= htmlspecialchars($price) ?>" step="0.01" required class="w-full px-4 py-2 border rounded">
            </div>

            <div>
                <label class="block font-medium">Product Image:</label>
                <input type="file" name="image" accept="image/*" class="w-full px-2 py-2 border rounded">
            </div>

            <button type="submit" class="bg-green-600 text-white px-5 py-2 rounded hover:bg-green-700">Add Product</button>
        </form>
    </div>
</body>
</html>
