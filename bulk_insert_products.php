<?php
include 'includes/db.php'; // Connect to the database

// Function to generate random products
function generateRandomProduct($index) {
    $name = "Product $index";
    $price = rand(100, 10000) / 100;
    $description = "This is a description for Product $index.";
    $image = "product$index.jpg"; // You can update this later with actual image paths
    return [$name, $price, $description, $image];
}

try {
    $conn->beginTransaction(); // Start transaction for speed

    $stmt = $conn->prepare("INSERT INTO products (name, price, description, image) VALUES (?, ?, ?, ?)");

    for ($i = 1; $i <= 2500; $i++) {
        list($name, $price, $description, $image) = generateRandomProduct($i);
        $stmt->execute([$name, $price, $description, $image]);
    }

    $conn->commit(); // Commit all inserts
    echo "2500 products inserted successfully!";
} catch (PDOException $e) {
    $conn->rollBack(); // Rollback on error
    echo "Error inserting products: " . $e->getMessage();
}
?>
