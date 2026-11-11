<?php
// LAB 6: PHP Shopping Cart - shop.php
// Start session at the very beginning (before any output)
session_start();

// Initialize cart if not exists
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = array();
}

// Product data (array of associative arrays)
$products = array(
    array('id' => 1, 'name' => 'Laptop', 'price' => 50000, 'image' => '💻'),
    array('id' => 2, 'name' => 'Mouse', 'price' => 1000, 'image' => '🖱️'),
    array('id' => 3, 'name' => 'Keyboard', 'price' => 2500, 'image' => '⌨️'),
    array('id' => 4, 'name' => 'Monitor', 'price' => 15000, 'image' => '🖥️')
);

// Handle Add to Cart
if (isset($_POST['add_to_cart'])) {
    $product_id = $_POST['product_id'];

    // Find product
    foreach ($products as $product) {
        if ($product['id'] == $product_id) {
            // Add to session cart array
            $_SESSION['cart'][] = $product;
            $message = $product['name'] . " added to cart!";
            break;
        }
    }
}

// Count items in cart
$cart_count = count($_SESSION['cart']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Lab 6 - Shop (PHP Sessions)</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; max-width: 900px; margin: 0 auto; padding: 20px; background: #f5f5f5; }
        .container { background: white; padding: 30px; border-radius: 15px; box-shadow: 0 5px 20px rgba(0,0,0,0.1); }
        h1 { color: #2c3e50; text-align: center; }
        .cart-link { text-align: right; margin-bottom: 20px; }
        .cart-link a { background: #e74c3c; color: white; padding: 10px 20px; text-decoration: none; border-radius: 8px; }
        .products { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-top: 20px; }
        .product { border: 2px solid #ecf0f1; padding: 20px; border-radius: 10px; text-align: center; transition: all 0.3s; }
        .product:hover { border-color: #3498db; transform: translateY(-5px); box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
        .product-icon { font-size: 4rem; margin-bottom: 10px; }
        .product-name { font-size: 1.2rem; font-weight: bold; color: #2c3e50; }
        .product-price { color: #e74c3c; font-size: 1.3rem; font-weight: bold; margin: 10px 0; }
        .add-btn { background: #2ecc71; color: white; border: none; padding: 10px 20px; border-radius: 6px; cursor: pointer; font-weight: 600; }
        .add-btn:hover { background: #27ae60; }
        .message { background: #d4edda; color: #155724; padding: 15px; border-radius: 8px; margin-bottom: 20px; }
        .info { background: #e3f2fd; padding: 15px; border-radius: 8px; margin-bottom: 20px; color: #1565c0; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🛒 PHP Shopping Cart - Shop</h1>

        <div class="cart-link">
            <a href="cart.php">🛍️ View Cart (<?php echo $cart_count; ?> items)</a>
        </div>

        <div class="info">
            <strong>Lab 6 Task:</strong> Shopping Cart using PHP Sessions and Arrays
        </div>

        <?php if (isset($message)): ?>
        <div class="message"><?php echo $message; ?></div>
        <?php endif; ?>

        <h2>Available Products</h2>
        <div class="products">
            <?php foreach ($products as $product): ?>
            <div class="product">
                <div class="product-icon"><?php echo $product['image']; ?></div>
                <div class="product-name"><?php echo $product['name']; ?></div>
                <div class="product-price">Rs. <?php echo number_format($product['price']); ?></div>
                <form method="POST" action="">
                    <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                    <button type="submit" name="add_to_cart" class="add-btn">Add to Cart</button>
                </form>
            </div>
            <?php endforeach; ?>
        </div>

        <hr style="margin-top: 30px;">
        <h3>PHP Session Data Debug:</h3>
        <pre style="background: #f8f9fa; padding: 15px; border-radius: 8px; overflow-x: auto;">
<?php
// Display session variables
print_r($_SESSION);
?>
        </pre>
    </div>
</body>
</html>