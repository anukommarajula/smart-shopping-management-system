<?php
// Start the session and check if the user is logged in
session_start();

// Logout Logic
if (isset($_POST['logout'])) {
    session_unset();
    session_destroy();
    header("Location: pages/login.php");
    exit();
}

if (!isset($_SESSION['user_id'])) {
    header("Location: pages/login.php");
    exit();
}

// Fetch products
include 'includes/db.php';
$stmt = $conn->query("SELECT * FROM products");
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Online Store</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        /* Add to your CSS file or inline style for demo */
        .header-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 30px;
            background-color: #f8f8f8;
        }
        .header-left h1 {
            margin: 0;
        }
        .header-right {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        .header-right a, .header-right form {
            text-decoration: none;
        }
        .cart-icon, .profile-icon {
            width: 24px;
            height: 24px;
            vertical-align: middle;
        }
        .product-image {
            max-width: 200px;
            height: auto;
        }
        .logout-button {
            background: transparent;
            border: none;
            color: #007BFF;
            cursor: pointer;
            font-size: 1em;
        }
    </style>
</head>
<body>
    <header>
        <div class="header-container">
            <!-- Left side: Logo/Site Name -->
            <div class="header-left">
                <h1>Welcome to Our Store</h1>
            </div>

            <!-- Right side: Profile, Cart, Logout -->
            <div class="header-right">
                <!-- Profile Icon -->
                <a href="pages/profile.php" title="Profile">
                    <img src="images/profile.jpg" alt="Profile" class="profile-icon">
                </a>

                <!-- Cart Icon -->
                <a href="pages/cart.php" class="cart-link" title="Cart">
                    <img src="images/cart-icon.png" alt="Cart" class="cart-icon">
                </a>

                <!-- Logout -->
                <form method="POST" style="margin: 0;">
                    <button type="submit" name="logout" class="logout-button">Logout</button>
                </form>
            </div>
        </div>
    </header>

    <div class="main-container">
        <main>
            <h2>Products</h2>
            <div class="product-list">
                <?php if (empty($products)) : ?>
                    <p>No products available.</p>
                <?php else : ?>
                    <?php foreach ($products as $product) : ?>
                        <div class="product">
                            <h3><?= htmlspecialchars($product['name']); ?></h3>
                            <p>Price: $<?= number_format($product['price'], 2); ?></p>
                            <p><?= htmlspecialchars($product['description']); ?></p>
                            <?php if (!empty($product['image'])) : ?>
                                <img src="images/<?= htmlspecialchars($product['image']); ?>" alt="<?= htmlspecialchars($product['name']); ?>" class="product-image">
                            <?php endif; ?>
                            <form method="POST" action="pages/cart.php">
                                <input type="hidden" name="product_id" value="<?= $product['id']; ?>">
                                <button type="submit" name="add_to_cart" class="add-to-cart-button">Add to Cart</button>
                            </form>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </main>
    </div>

    <footer>
        <p>&copy; <?= date('Y'); ?> Online Store. All rights reserved.</p>
    </footer>
</body>
</html>


