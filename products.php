<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product List</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Rubik:ital,wght@0,300..900;1,300..900&display=swap');

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Rubik', sans-serif;
        }

        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            background-color: #F8F4E1;
            color: #212529;
        }

        .navbar {
            background-color: #193925;
            overflow: hidden;
        }

        .navbar a {
            float: left;
            display: block;
            color: #f2f2f2;
            text-align: center;
            padding: 14px 20px;
            text-decoration: none;
        }

        .navbar a:hover {
            background-color: #ddd;
            color: black;
        }

        .logout-btn {
            background: none;
            border: none;
            color: white;
            cursor: pointer;
            font-size: 16px;
            padding: 14px 20px;
        }

        .logout-btn:hover {
            background-color: #EE4E4E;
        }

        .product-list-container {
            padding: 60px 20px;
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
        }

        .product-list {
            list-style-type: none;
            padding: 0;
            width: 100%;
            max-width: 1200px;
        }

        .product-list-item {
            background: #fff;
            border: 1px solid #ddd;
            margin: 10px 0;
            padding: 10px;
            border-radius: 5px;
            box-shadow: 0px 5px 10px rgba(0, 0, 0, 0.1);
            display: flex;
            align-items: center;
        }

        .product-list-item img {
            max-width: 100px;
            max-height: 100px;
            margin-right: 20px;
            border-radius: 5px;
        }

        .product-info {
            display: flex;
            flex-direction: column;
            flex-grow: 1;
        }

        .product-info button {
            padding: 8px 16px;
            background-color: #193925;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
            align-self: flex-start;
        }

        .product-info button:hover {
            background-color: #14531f;
        }

        .product-info select,
        .product-info textarea {
            margin-top: 10px;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 5px;
            width: 100%;
        }

        .product-info textarea {
            resize: vertical;
        }
    </style>
    <script>
        function toggleCustomizationForm(productId, productName) {
            if (productName === "Customized Cake") {
                document.getElementById('customization-form-' + productId).style.display = 'block';
            } else {
                document.getElementById('default-form-' + productId).submit();
            }
        }

        function updatePrice(productId) {
            var size = document.getElementById('size-' + productId).value;
            var priceField = document.getElementById('price-' + productId);
            var price;

            if (size === 'small') {
                price = 300;
            } else if (size === 'medium') {
                price = 700;
            } else if (size === 'large') {
                price = 1200;
            }

            priceField.value = price;
        }
    </script>
</head>
<body>
    <div class="navbar">
        <a href="landing.php">Home</a>
        <a href="products.php">Product List</a>
        <a href="cart.php">Cart</a>
        <a href="profile.php">Profile</a>
        <!-- <a href="user_orders.php">My Orders</a> -->
        <div style="float: right;">
            <form id="logoutForm" action="logout.php" method="post" style="margin: 0;">
                <input type="hidden" name="logout" value="1">
                <input type="submit" value="Logout" class="logout-btn" onclick="confirmLogout(event)">
            </form>
        </div>
    </div>

    <div class="product-list-container">
        <ul class="product-list">
            <?php
            require 'conx_user.php';
            $sqlFetch = "SELECT * FROM `products`";
            $result = mysqli_query($conn, $sqlFetch);

            while ($row = mysqli_fetch_assoc($result)) {
                $productId = htmlspecialchars($row['prod_ID']);
                $productName = htmlspecialchars($row['product']);
                $productPrice = number_format($row['prod_price'], 2);
                $imageSrc = !empty($row['prod_image']) ? htmlspecialchars($row['prod_image']) : 'path/to/default-image.jpg';
                echo "<li class='product-list-item'>";
                echo "<div class='product-info'>";
                echo "<strong>$productName</strong><br>";
                echo "<strong>Price:</strong> &#8369;$productPrice<br>";
                echo "<form id='default-form-$productId' action='cart.php' method='post' style='display: none;'>";
                echo "<input type='hidden' name='product_id' value='$productId'>";
                echo "</form>";
                echo "<button type='button' onclick=\"toggleCustomizationForm('$productId', '$productName')\">Order Now</button>";
                if ($productName === "Customized Cake") {
                    echo "Apperance and Price may vary based on the specifications<br>";
                    echo "<form id='customization-form-$productId' action='cart.php' method='post' style='display: none;'>";
                    echo "<input type='hidden' name='product_id' value='$productId'>";
                    echo "<input type='hidden' name='price' id='price-$productId' value='1000'>"; // Default price
                    echo "<label for='flavor-$productId'>Flavor:</label>";
                    echo "<select name='flavor' id='flavor-$productId' required>";
                    echo "<option value='chocolate'>Chocolate</option>";
                    echo "<option value='strawberry'>Strawberry</option>";
                    echo "<option value='vanilla'>Vanilla</option>";
                    echo "<option value='ube'>Ube</option>";
                    echo "</select><br>";
                    echo "<label for='size-$productId'>Size:</label>";
                    echo "<select name='size' id='size-$productId' required onchange=\"updatePrice('$productId')\">";
                    echo "<option value='small'>Small - &#8369;300</option>";
                    echo "<option value='medium'>Medium - &#8369;700</option>";
                    echo "<option value='large'>Large - &#8369;1200</option>";
                    echo "</select><br>";
                    echo "<label for='specifications-$productId'>Specifications:</label><br>";
                    echo "<textarea name='specifications' id='specifications-$productId' rows='4' cols='50'></textarea><br>";
                    echo "<button type='submit'>Add to Cart</button>";
                    echo "</form>";
                }
                echo "</div>";
                echo "<img src='$imageSrc' alt='Product Image'>";
                echo "</li>";
            }

            mysqli_close($conn);
            ?>
        </ul>
    </div>
</body>
</html>
