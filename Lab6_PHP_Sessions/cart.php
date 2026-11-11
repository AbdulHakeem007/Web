<?php
// LAB 6: PHP Shopping Cart - cart.php
// Start session at the very beginning
session_start();

// Initialize cart if not exists
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = array();
}

// Handle Remove Item
if (isset($_POST['remove_item'])) {
    $index = $_POST['item_index'];
    if (isset($_SESSION['cart'][$index])) {
        unset($_SESSION['cart'][$index]);
        // Re-index array
        $_SESSION['cart'] = array_values($_SESSION['cart']);
        $message = "Item removed successfully!";
    }
}

// Handle Clear Cart
if (isset($_POST['clear_cart'])) {
    $_SESSION['cart'] = array();
    $message = "Cart cleared!";
}

// Calculate total
$total = 0;
foreach ($_SESSION['cart'] as $item) {
    $total += $item['price'];
}

$cart_count = count($_SESSION['cart']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Lab 6 - Cart (PHP Sessions)</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; max-width: 800px; margin: 0 auto; padding: 20px; background: #f5f5f5; }
        .container { background: white; padding: 30px; border-radius: 15px; box-shadow: 0 5px 20px rgba(0,0,0,0.1); }
        h1 { color: #2c3e50; text-align: center; }
        .shop-link { text-align: left; margin-bottom: 20px; }
        .shop-link a { background: #3498db; color: white; padding: 10px 20px; text-decoration: none; border-radius: 8px; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th, td { padding: 15px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #3498db; color: white; }
        tr:hover { background: #f5f5f5; }
        .remove-btn { background: #e74c3c; color: white; border: none; padding: 8px 15px; border-radius: 5px; cursor: pointer; }
        .remove-btn:hover { background: #c0392b; }
        .total-row { font-size: 1.3rem; font-weight: bold; background: #e8f5e9; }
        .clear-btn { background: #f39c12; color: white; border: none; padding: 12px 25px; border-radius: 8px; cursor: pointer; font-weight: 600; margin-top: 20px; }
        .clear-btn:hover { background: #e67e22; }
        .empty-cart { text-align: center; padding: 40px; color: #7f8c8d; }
        .message { background: #d4edda; color: #155724; padding: 15px; border-radius: 8px; margin-bottom: 20px; }
        .expected-output { background: #fff3cd; padding: 15px; border-radius: 8px; margin-top: 20px; color: #856404; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🛍️ Your Shopping Cart</h1>

        <div class="shop-link">
            <a href="shop.php">← Continue Shopping</a>
        </div>

        <?php if (isset($message)): ?>
        <div class="message"><?php echo $message; ?></div>
        <?php endif; ?>

        <?php if ($cart_count > 0): ?>

        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Product</th>
                    <th>Price</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($_SESSION['cart'] as $index => $item): ?>
                <tr>
                    <td><?php echo $index + 1; ?></td>
                    <td><?php echo $item['image'] . ' ' . $item['name']; ?></td>
                    <td>Rs. <?php echo number_format($item['price']); ?></td>
                    <td>
                        <form method="POST" action="" style="display: inline;">
                            <input type="hidden" name="item_index" value="<?php echo $index; ?>">
                            <button type="submit" name="remove_item" class="remove-btn">Remove</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
                <tr class="total-row">
                    <td colspan="2"><strong>Total Price:</strong></td>
                    <td colspan="2"><strong>Rs. <?php echo number_format($total); ?></strong></td>
                </tr>
            </tbody>
        </table>

        <form method="POST" action="">
            <button type="submit" name="clear_cart" class="clear-btn">🗑️ Clear Cart</button>
        </form>

        <?php else: ?>
        <div class="empty-cart">
            <h2>Your cart is empty!</h2>
            <p>Add some products from the shop.</p>
        </div>
        <?php endif; ?>

        <div class="expected-output">
            <h3>Expected Output Example:</h3>
            <p><strong>Your Cart:</strong></p>
            <p>1. Laptop – 50000</p>
            <p>2. Mouse – 1000</p>
            <p><strong>Total Price: 51000</strong></p>
            <p><em>After removing item: Total Price: 1000</em></p>
        </div>

        <hr style="margin-top: 30px;">
        <h3>PHP Session Data:</h3>
        <pre style="background: #f8f9fa; padding: 15px; border-radius: 8px; overflow-x: auto;">
<?php print_r($_SESSION); ?>
        </pre>
    </div>
</body>
</html>