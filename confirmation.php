<?php
session_start();
require('conx_user.php');

// Check if order_id is provided
if (!isset($_GET['order_id'])) {
    header('Location: cart.php');
    exit();
}

$order_id = $_GET['order_id'];

// Fetch order details
$order_sql = "SELECT * FROM orders WHERE order_id = ?";
$stmt = $conn->prepare($order_sql);
$stmt->bind_param("i", $order_id);
$stmt->execute();
$order_result = $stmt->get_result();
$order = $order_result->fetch_assoc();
if (!$order) {
    echo "<script>alert('Order not found'); window.location.href='cart.php';</script>";
    exit();
}
$stmt->close();

// Default to empty values if order details are missing
$order_id_display = $order['order_id'] ?? 'N/A';
$reference_number = $order['reference_number'] ?? 'N/A';
$total_price = isset($order['total_price']) ? number_format($order['total_price'], 2) : '0.00';
$delivery_method = $order['delivery_method'] ?? 'N/A';
$delivery_date = $order['delivery_date'] ?? 'N/A';
$payment_type = $order['payment_type'] ?? 'N/A';

// Fetch order items
$item_sql = "SELECT * FROM order_items WHERE order_id = ?";
$stmt = $conn->prepare($item_sql);
$stmt->bind_param("i", $order_id);
$stmt->execute();
$item_result = $stmt->get_result();
$order_items = $item_result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation</title>
    <style>
        /* Basic Reset */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: Arial, sans-serif;
}

body {
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 100vh;
    background-color: #f3f4f6;
}

.confirmation-container {
    width: 80%;
    max-width: 600px;
    background-color: #ffffff;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

h1, h2 {
    text-align: center;
    color: #333;
}

h1 {
    margin-bottom: 20px;
}

h2 {
    font-size: 1.5em;
    margin-bottom: 15px;
    color: #555;
}

.order-details, .order-items {
    margin-bottom: 20px;
}

.order-details p, .order-items table th, .order-items table td {
    color: #444;
    font-size: 1em;
    line-height: 1.6;
}

.order-details p {
    margin: 5px 0;
}

.order-items table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 10px;
}

.order-items table th, .order-items table td {
    padding: 10px;
    border: 1px solid #ddd;
    text-align: center;
}

.order-items table th {
    background-color: #f8f8f8;
    font-weight: bold;
}

.confirm-button {
    display: block;
    width: 100%;
    padding: 12px;
    font-size: 1em;
    font-weight: bold;
    color: #ffffff;
    background-color: #28a745;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

.confirm-button:hover {
    background-color: #218838;
}

.confirm-button:focus {
    outline: none;
}

    </style>
    <script>
        function confirmOrder() {
            var xhr = new XMLHttpRequest();
            xhr.open("POST", "record_audit.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.onreadystatechange = function() {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    alert("Order Confirmed");
                    window.location.href = "landing.php";
                }
            };
            xhr.send("order_id=<?php echo $order_id_display; ?>");
        }
    </script>
</head>
<body>
    <div class="confirmation-container">
        <div class="order-details">
            <h2>Order Details</h2>
            <p>Order ID: <?php echo htmlspecialchars($order_id_display); ?></p>
            <p>Reference Number: <?php echo htmlspecialchars($reference_number); ?></p>
            <p>Total Price: ₱<b><?php echo htmlspecialchars($total_price); ?></b></p>
            <p>Delivery Method: <?php echo htmlspecialchars($delivery_method); ?></p>
            <p>Delivery Date: <?php echo htmlspecialchars($delivery_date); ?></p>
            <p>Payment Type: <?php echo htmlspecialchars($payment_type); ?></p>
        </div>
        <div class="order-items">
            <h2>Order Items</h2>
            <table>
                <thead>
                    <tr>
                        <th>Product ID</th>
                        <th>Quantity</th>
                        <th>Service Type</th>
                        <th>Specifications</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($order_items as $item): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($item['product_id']); ?></td>
                            <td><?php echo htmlspecialchars($item['quantity']); ?></td>
                            <td><?php echo htmlspecialchars($item['service_type']); ?></td>
                            <td><?php echo htmlspecialchars($item['specifications']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <button class="confirm-button" onclick="confirmOrder()">Confirm</button>
    </div>
</body>
</html>
