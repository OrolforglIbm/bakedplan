<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Service Selection</title>
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
            background-color: #f8f9fa;
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

        .service-list-container {
            padding: 60px 20px;
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
        }

        .service-list {
            list-style-type: none;
            padding: 0;
        }

        .service-list-item {
            background: #fff;
            border: 1px solid #ddd;
            margin: 10px 0;
            padding: 10px;
            border-radius: 5px;
            box-shadow: 0px 5px 10px rgba(0, 0, 0, 0.1);
            display: flex;
            align-items: center;
            flex-direction: column;
        }

        .service-form {
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="navbar">
        <a href="landing.php">Home</a>
        <a href="products.php">Product List</a>
        <a href="order.php">Order</a>
        <a href="cart.php">Cart</a>
        <a href="profile.php">Profile</a>
        <div style="float: right;">
            <form id="logoutForm" action="logout.php" method="post" style="margin: 0;">
                <input type="hidden" name="logout" value="1">
                <input type="submit" value="Logout" class="logout-btn" onclick="confirmLogout(event)">
            </form>
        </div>
    </div>

    <div class="service-list-container">
        <ul class="service-list">
            <?php
            session_start();
            require 'conx_user.php';

            if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
                echo "<li>Your cart is empty.</li>";
            } else {
                foreach ($_SESSION['cart'] as $product_id => $details) {
                    $sqlFetch = "SELECT * FROM `products` WHERE `prod_ID` = " . intval($product_id);
                    $result = mysqli_query($conn, $sqlFetch);
                    $row = mysqli_fetch_assoc($result);
                    
                    $imageSrc = !empty($row['prod_image']) ? htmlspecialchars($row['prod_image']) : 'path/to/default-image.jpg';
                    echo "<li class='service-list-item'>";
                    echo "<div class='product-info'>";
                    echo "<strong>Product:</strong> " . htmlspecialchars($row['product']) . "<br>";
                    echo "<strong>Price:</strong> &#8369;" . number_format($row['prod_price'], 2) . "<br>";
                    echo "</div>";
                    echo "<img src='$imageSrc' alt='Product Image'>";
                    echo "<form class='service-form' action='add_service.php' method='post'>";
                    echo "<input type='hidden' name='product_id' value='" . htmlspecialchars($product_id) . "'>";
                    echo "<label for='service_type'>Choose Service Type:</label>";
                    echo "<select name='service_type' id='service_type'>";
                    echo "<option value='normal'>Normal</option>";
                    echo "<option value='customized'>Customized</option>";
                    echo "</select><br>";

                    if (!empty($row['custom_options'])) {
                        echo "<div id='custom-options' style='display:none;'>";
                        echo "<label for='flavor'>Choose Flavor:</label>";
                        echo "<select name='specifications[flavor]' id='flavor'>";
                        echo "<option value='vanilla'>Vanilla</option>";
                        echo "<option value='chocolate'>Chocolate</option>";
                        echo "<option value='red_velvet'>Red Velvet</option>";
                        echo "</select><br>";
                        echo "<label for='size'>Choose Size:</label>";
                        echo "<select name='specifications[size]' id='size'>";
                        echo "<option value='small'>Small</option>";
                        echo "<option value='large'>Large</option>";
                        echo "</select><br>";
                        echo "<label for='message'>Message on Cake:</label>";
                        echo "<input type='text' name='specifications[message]' id='message'><br>";
                        echo "</div>";
                    }

                    echo "<button type='submit'>Add Service</button>";
                    echo "</form>";
                    echo "</li>";
                }
            }

            mysqli_close($conn);
            ?>
        </ul>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const serviceTypeSelects = document.querySelectorAll('#service_type');
            serviceTypeSelects.forEach(select => {
                select.addEventListener('change', function() {
                    const customOptions = this.closest('form').querySelector('#custom-options');
                    if (this.value === 'customized' && customOptions) {
                        customOptions.style.display = 'block';
                    } else if (customOptions) {
                        customOptions.style.display = 'none';
                    }
                });
            });
        });
    </script>
</body>
</html>
