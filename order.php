<?php 
require('conx_user.php');
session_start();

// Fetch products from the database
$sqlFetchProducts = "SELECT * FROM `products`";
$resultProducts = mysqli_query($conn, $sqlFetchProducts);

// Check if products are fetched successfully
if(!$resultProducts) {
    echo "Error fetching products: " . mysqli_error($conn);
    exit; // Exit if there's an error fetching products
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Page</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Rubik:ital,wght@0,300..900;1,300..900&display=swap');
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Rubik', sans-serif; }
        body { background-color: #F8F4E1; display: flex; flex-direction: column; align-items: center; padding: 20px; }
        h1 { margin-bottom: 20px; color: #212529; }
        .navbar { width: 100%; background-color: #193925; overflow: hidden; display: flex; justify-content: space-between; align-items: center; padding: 10px 20px; }
        .navbar a { color: #f2f2f2; text-align: center; padding: 14px 20px; text-decoration: none; }
        .navbar a:hover { background-color: #ddd; color: black; }
        .logout-btn { background: none; border: none; color: white; cursor: pointer; font-size: 16px; padding: 14px 20px; }
        .logout-btn:hover { background-color: #EE4E4E; }
        .products { display: flex; flex-wrap: wrap; gap: 20px; justify-content: center; max-width: 1200px; width: 100%; }
        .product { background-color: #fff; border-radius: 10px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); overflow: hidden; width: 300px; text-align: center; padding: 20px; }
        .product img { max-width: 100%; height: auto; border-bottom: 1px solid #e8e8e8; margin-bottom: 15px; }
        .product h2 { font-size: 20px; color: #212529; margin-bottom: 10px; }
        .product p { font-size: 16px; color: #666; margin-bottom: 15px; }
        .product .price { font-size: 18px; color: #0A6847; margin-bottom: 15px; }
        .product form { display: inline-block; }
        .product button { padding: 10px 20px; color: white; background-color: #017aff; border-radius: 5px; border: none; font-size: 15px; cursor: pointer; transition: background-color 0.3s ease; }
        .product button:hover { background-color: #0061cc; }
        .customization-form { margin-top: 20px; padding: 20px; background-color: #fff; border-radius: 10px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); width: 100%; max-width: 600px; }
        .customization-form label { display: block; margin-bottom: 10px; font-weight: bold; color: #212529; }
        .customization-form input[type="text"], .customization-form textarea { width: 100%; padding: 10px; margin-bottom: 20px; border: 1px solid #ccc; border-radius: 5px; font-size: 16px; }
        .customization-form select { width: 100%; padding: 10px; margin-bottom: 20px; border: 1px solid #ccc; border-radius: 5px; font-size: 16px; }
        .customization-form button { padding: 10px 20px; color: white; background-color: #0A6847; border-radius: 5px; border: none; font-size: 16px; cursor: pointer; transition: background-color 0.3s ease; }
        .customization-form button:hover { background-color: #085d3e; }
    </style>
</head>
<body>
    <div class="navbar">
        <div style="display: flex; gap: 20px;">
            <a href="landing.php">Home</a>
            <a href="products.php">Product List</a>
            <a href="order.php">Order</a>
            <a href="cart.php">Cart</a>
            <a href="profile.php">Profile</a>
        </div>
        <div>
            <form id="logoutForm" action="logout.php" method="post" style="margin: 0;">
                <input type="hidden" name="logout" value="1">
                <input type="submit" value="Logout" class="logout-btn" onclick="confirmLogout(event)">
            </form>
        </div>
    </div>

    <h1>Order Products</h1>
    <div class="products">
        <?php while($product = mysqli_fetch_assoc($resultProducts)): ?>
            <div class="product">
                <img src="<?php echo htmlspecialchars($product['prod_image'] ?? 'default.png'); ?>" alt="<?php echo htmlspecialchars($product['product'] ?? 'Product'); ?>">
                <h2><?php echo htmlspecialchars($product['product'] ?? 'Unknown'); ?></h2>
                <p class="price">₱<?php echo htmlspecialchars($product['prod_price'] ?? '0.00'); ?></p>
                <button onclick="showCustomizationForm('<?php echo $product['prod_ID']; ?>')">Order Now</button>
            </div>
        <?php endwhile; ?>
    </div>

    <div id="customizationFormContainer" class="customization-form" style="display: none;">
        <h2>Customize Your Cake</h2>
        <form id="customizationForm" action="cart.php" method="post">
            <input type="hidden" name="product_id" id="product_id">
            <label for="service_type">Service Type:</label>
            <select name="service_type" id="service_type" required>
                <option value="default">Default</option>
                <option value="customized">Customized (+150)</option>
            </select>
            <div id="specificationsContainer" style="display: none;">
                <label for="specifications">Specifications:</label>
                <textarea name="specifications" id="specifications" rows="4"></textarea>
            </div>
            <button type="submit">Add to Cart</button>
        </form>
    </div>

    <script>
        function showCustomizationForm(productId) {
            document.getElementById('product_id').value = productId;
            document.getElementById('customizationFormContainer').style.display = 'block';
        }

        document.getElementById('service_type').addEventListener('change', function() {
            if (this.value === 'customized') {
                document.getElementById('specificationsContainer').style.display = 'block';
            } else {
                document.getElementById('specificationsContainer').style.display = 'none';
            }
        });

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
