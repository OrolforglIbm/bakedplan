<?php
require('conx_user.php');
session_start();

// Initialize the cart if it doesn't exist
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Handle adding to cart
if (isset($_POST['product_id'])) {
    $product_id = $_POST['product_id'];
    $specifications = $_POST['specifications'] ?? '';
    $size = $_POST['size'] ?? '';
    $price = 1000; // Default price for the product

    // Adjust price based on size
    if ($size) {
        switch ($size) {
            case 'small':
                $price = 300;
                break;
            case 'medium':
                $price = 700;
                break;
            case 'large':
                $price = 1200;
                break;
        }
    } else {
        // Fetch product details for non-customized products
        $productDetails = fetchProductDetails($conn, $product_id);
        $price = $productDetails['prod_price'] ?? 1000;
    }

    // Create a unique key for each cart item
    $cart_key = $product_id . '_' . $size . '_' . md5($specifications);

    if (!isset($_SESSION['cart'][$cart_key])) {
        $_SESSION['cart'][$cart_key] = [
            'product_id' => $product_id,
            'size' => $size,
            'specifications' => $specifications,
            'price' => $price,
            'quantity' => 1
        ];
    } else {
        // Increase quantity if already in cart
        $_SESSION['cart'][$cart_key]['quantity']++;
    }
}

// Handle removing from cart
if (isset($_POST['remove_cart_key'])) {
    $cart_key = $_POST['remove_cart_key'];
    unset($_SESSION['cart'][$cart_key]);
}

// Fetch product details from the database
function fetchProductDetails($conn, $product_id) {
    $sql = "SELECT * FROM products WHERE prod_ID = '$product_id'";
    $result = mysqli_query($conn, $sql);

    if ($result && mysqli_num_rows($result) > 0) {
        return mysqli_fetch_assoc($result);
    }
    return null;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cart</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Rubik:ital,wght@0,300..900;1,300..900&display=swap');
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Rubik', sans-serif; }
        body { display: flex; flex-direction: column; align-items: center; min-height: 100vh; background-color: #F8F4E1; color: #212529; }
        h1 { margin-bottom: 20px; color: #212529; text-align: center; margin-top: 20px; }
        .navbar { width: 100%; background-color: #193925; overflow: hidden; display: flex; justify-content: space-between; align-items: center; padding: 10px 20px; }
        .navbar a { color: #f2f2f2; text-align: center; padding: 14px 20px; text-decoration: none; }
        .navbar a:hover { background-color: #ddd; color: black; }
        .logout-btn { background: none; border: none; color: white; cursor: pointer; font-size: 16px; padding: 14px 20px; }
        .logout-btn:hover { background-color: #EE4E4E; }
        .cart { align-items: center; max-width: 800px; width: 100%; margin-top: 20px; background-color: #fff; padding: 20px; border-radius: 10px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); }
        .cart-item { display: flex; justify-content: space-between; align-items: center; padding: 15px 0; border-bottom: 1px solid #e8e8e8; }
        .cart-item:last-child { border-bottom: none; }
        .cart-item h2 { font-size: 18px; color: #212529; }
        .cart-item p { font-size: 16px; color: #666; }
        .cart-item .price { font-size: 18px; color: #0A6847; }
        .cart-item form { margin: 10px; }
        .cart-item button { padding: 5px 10px; color: white; background-color: #EE4E4E; border-radius: 5px; border: none; font-size: 14px; cursor: pointer; transition: background-color 0.3s ease; }
        .cart-item button:hover { background-color: #d43d3d; }
        .total-price { font-size: 20px; color: #212529; text-align: right; margin-top: 20px; }
        .checkout-button { margin-top: 20px; text-align: center; }
        .checkout-button button { padding: 10px 20px; color: white; background-color: #193925; border-radius: 5px; border: none; font-size: 16px; cursor: pointer; transition: background-color 0.3s ease; }
        .checkout-button button:hover { background-color: #085d3e; }
    </style>
</head>
<body>
    <div class="navbar">
        <div>
            <a href="landing.php">Home</a>
            <a href="products.php">Product List</a>
            <a href="cart.php">Cart</a>
            <a href="profile.php">Profile</a>
            <!-- <a href="user_orders.php">My Orders</a> -->
        </div>
        <div style="float: right;">
            <form id="logoutForm" action="logout.php" method="post" style="margin: 0;">
                <input type="hidden" name="logout" value="1">
                <input type="submit" value="Logout" class="logout-btn" onclick="confirmLogout(event)">
            </form>
        </div>
    </div>

    <h1>Your Cart</h1>
    <div class="cart">
        <?php 
        $total_price = 0;

        foreach ($_SESSION['cart'] as $cart_key => $item): 
            $productDetails = fetchProductDetails($conn, $item['product_id']);
            if (!$productDetails) {
                echo "<p>Product details not found for ID: {$item['product_id']}</p>";
                continue;
            }

            $item_total_price = $item['price'] * $item['quantity'];
            $total_price += $item_total_price;
        ?>
            <div class="cart-item">
                <div>
                    <h2><?php echo htmlspecialchars($productDetails['product']); ?></h2>
                    <?php if (!empty($item['size'])): ?>
                        <p>Size: <?php echo htmlspecialchars($item['size']); ?></p>
                    <?php endif; ?>
                    <p>Specifications: <?php echo htmlspecialchars($item['specifications']); ?></p>
                    <p>Quantity: <?php echo htmlspecialchars($item['quantity']); ?></p>
                </div>
                <div class="price">₱<?php echo number_format($item_total_price, 2); ?></div>
                <form action="cart.php" method="post">
                    <input type="hidden" name="remove_cart_key" value="<?php echo htmlspecialchars($cart_key); ?>">
                    <button type="submit">Remove</button>
                </form>
            </div>
        <?php endforeach; ?>
        <div class="total-price">Total Price: ₱<?php echo number_format($total_price, 2); ?></div>
        <div class="checkout-button">
            <form action="checkout.php" method="post">
                <button type="submit">Proceed to Checkout</button>
            </form>
        </div>
    </div>

    <script>
        function confirmLogout(event) {
            event.preventDefault();
            if (confirm('Are you sure you want to log out?')) {
                alert('User Logged Out!');
                document.getElementById('logoutForm').submit();
            }
        }
    </script>
</body>
</html>
