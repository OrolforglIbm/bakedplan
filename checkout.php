<?php
session_start();
require('conx_user.php');

// Check if the cart is empty
$cart_is_empty = empty($_SESSION['cart']);

// Calculate total price
$total_price = 0;
if (!$cart_is_empty) {
    foreach ($_SESSION['cart'] as $item) {
        $total_price += $item['price'] * $item['quantity'];
    }
}

// Delivery fee
$delivery_fee = 70;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Rubik:ital,wght@0,300..900;1,300..900&display=swap');
        * { box-sizing: border-box; margin: 0; margin-top: 10; padding: 0; font-family: 'Rubik', sans-serif; }
        body { background-color: #F8F4E1; display: flex; flex-direction: column; align-items: center; padding: 20px; }
        h1 {margin-top: 20px; margin-bottom: 20px; color: #212529; }
        .navbar { width: 100%; background-color: #193925; overflow: hidden; display: flex; justify-content: space-between; align-items: center; }
        .navbar a { color: #f2f2f2; text-align: center; padding: 14px 20px; text-decoration: none; }
        .navbar a:hover { background-color: #ddd; color: black; }
        .logout-btn { background: none; border: none; color: white; cursor: pointer; font-size: 16px; padding: 14px 20px; }
        .logout-btn:hover { background-color: #EE4E4E; }
        .checkout-container { max-width: 800px; width: 100%; margin-top: 20px; background-color: #fff; padding: 20px; border-radius: 10px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; color: #212529; }
        .form-group select, .form-group input[type="radio"], .form-group input[type="submit"], .form-group button { width: 100%; padding: 10px; border-radius: 5px; border: 1px solid #ddd; font-size: 16px; }
        .form-group input[type="submit"], .form-group button { background-color: #0A6847; color: white; cursor: pointer; transition: background-color 0.3s ease; }
        .form-group input[type="submit"]:hover, .form-group button:hover { background-color: #085d3e; }
        .total-price { font-size: 20px; color: #212529; text-align: right; margin-top: 20px; }
        .empty-cart-message { font-size: 20px; color: #212529; text-align: center; margin-top: 20px; }
    </style>
</head>
<body>


    <h1>Checkout</h1>

    <?php if ($cart_is_empty): ?>
        <script>
            alert("Your cart has no items.");
            window.location.href = "cart.php";
        </script>
    <?php else: ?>
        <div class="checkout-container">
            <form id="checkoutForm" action="process_checkout.php" method="post">
                <div class="form-group">
                    <label for="deliveryOption">Delivery or Pickup</label>
                    <select name="deliveryOption" id="deliveryOption" required>
                        <option value="pickup">Pickup</option>
                        <option value="delivery">Delivery (₱<?php echo number_format($delivery_fee, 2); ?> fee)</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="deliveryDate">Delivery Date:</label>
                    <input type="date" id="deliveryDate" name="deliveryDate" required>
                </div>
                
                <div class="form-group">
                    <label>Payment Method</label>
                    <input type="radio" name="paymentMethod" value="gcash" required> GCash
                    <!-- <input type="radio" name="paymentMethod" value="cash" required> Cash on Pickup/Delivery -->
                </div>

                <!-- <div id="gcashDetails" style="display: none;">
                    <div class="form-group">
                        <label for="gcashNumber">GCash Number</label>
                        <input type="text" name="gcashNumber" id="gcashNumber" placeholder="09*********">
                    </div>
                    <div class="form-group">
                        <label for="gcashReceiverNumber">Receiver GCash Number</label>
                        <input type="text" name="gcashReceiverNumber" id="gcashReceiverNumber" value="0915 720 3668" readonly>
                    </div>
                    <div class="form-group">
                        <label for="gcashReceiverName">Receiver Name</label>
                        <input type="text" name="gcashReceiverName" id="gcashReceiverName" value="T**** O." readonly>
                    </div>
                </div>
 -->
                <div class="total-price">Total Price: ₱<?php echo number_format($total_price, 2); ?></div>
                <div class="total-price" id="finalTotalPrice"></div>

                <div class="form-group">
                    <input type="submit" value="Proceed to Payment">
                    <a href="cart.php">Back</a>
                </div>
            </form>
        </div>

        <script>
            document.getElementById('checkoutForm').addEventListener('change', function() {
                const paymentMethod = document.querySelector('input[name="paymentMethod"]:checked').value;
                const gcashDetails = document.getElementById('gcashDetails');
                const deliveryOption = document.getElementById('deliveryOption').value;
                const finalTotalPrice = document.getElementById('finalTotalPrice');
                let totalPrice = <?php echo $total_price; ?>;

                if (deliveryOption === 'delivery') {
                    totalPrice += <?php echo $delivery_fee; ?>;
                }

                if (paymentMethod === 'gcash') {
                    gcashDetails.style.display = 'block';
                   document.getElementById('gcashNumber').setAttribute('required', 'required');
                } else {
                   gcashDetails.style.display = 'none';
                   document.getElementById('gcashNumber').removeAttribute('required');
                }

                finalTotalPrice.textContent = 'Final Total Price: ₱' + totalPrice.toFixed(2);
            });

            // Trigger change event to update initial state
            document.getElementById('checkoutForm').dispatchEvent(new Event('change'));
        </script>
    <?php endif; ?>
</body>
</html>
