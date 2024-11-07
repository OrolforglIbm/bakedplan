<?php
session_start();
require('conx_user.php');

if (!isset($_SESSION['cusID'])) {
    header('Location: login.php');
    exit();
}

$cusID = $_SESSION['cusID'];

$order_sql = "SELECT * FROM orders WHERE cusID = ? ORDER BY created_at DESC";
$stmt = $conn->prepare($order_sql);
$stmt->bind_param("i", $cusID);
$stmt->execute();
$order_result = $stmt->get_result();
$orders = $order_result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Orders</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #F8F4E1; display: flex; flex-direction: column; align-items: center; padding: 20px; }
        h1 { margin-bottom: 20px; color: #212529; }
        .orders-container { max-width: 800px; width: 100%; background-color: #fff; padding: 20px; border-radius: 10px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); }
        .order { border-bottom: 1px solid #ccc; padding: 10px 0; }
        .order:last-child { border-bottom: none; }
        .order p { margin: 5px 0; }
        .order-status { color: #fff; padding: 5px 10px; border-radius: 5px; }
        .pending { background-color: #ffc107; }
        .approved { background-color: #28a745; }
        .rejected { background-color: #dc3545; }
        .completed { background-color: #007bff; }
        .delivering { background-color: #17a2b8; }
        .ready_for_pickup { background-color: #6f42c1; }
        .received { background-color: #6f42c1; }
        .home-button { margin-top: 20px; padding: 10px 20px; background-color: #193925; color: #fff; border: none; border-radius: 5px; cursor: pointer; text-decoration: none; }
        .home-button:hover { background-color: #145f2e; }
    </style>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script>
        function markOrderCompleted(orderId) {
            $.ajax({
                url: 'mark_order_completed.php',
                type: 'POST',
                data: { order_id: orderId },
                success: function(response) {
                    const result = JSON.parse(response);
                    if (result.success) {
                        alert('Order status updated to completed.');
                        location.reload();
                    } else {
                        alert('Error: ' + result.error);
                    }
                },
                error: function() {
                    alert('Error updating order status.');
                }
            });
        }
    </script>
</head>
<body>
    <h1>Your Orders</h1>
    <div class="orders-container">
        <?php if (count($orders) > 0): ?>
            <?php foreach ($orders as $order): ?>
                <div class="order">
                    <p><strong>Order ID:</strong> <?php echo htmlspecialchars($order['order_id']); ?></p>
                    <p><strong>Reference Number:</strong> <?php echo htmlspecialchars($order['reference_number']); ?></p>
                    <p><strong>Total Price:</strong> ₱<b><?php echo number_format($order['total_price'], 2); ?></b></p>
                    <p><strong>Delivery Date:</strong> <?php echo htmlspecialchars($order['delivery_date']); ?></p>
                    <p><strong>Payment Type:</strong> <?php echo htmlspecialchars($order['payment_type']); ?></p>
                    <p><strong>Status:</strong> 
                        <span class="order-status <?php echo strtolower(str_replace(' ', '_', htmlspecialchars($order['status']))); ?>">
                            <?php echo ucfirst(str_replace('_', ' ', htmlspecialchars($order['status']))); ?>
                        </span>
                    </p>
                    <?php if ($order['status'] == 'delivering' || $order['status'] == 'ready_for_pickup'): ?>
                        <button class="btn btn-primary" onclick="markOrderCompleted(<?php echo $order['order_id']; ?>)">Order Received</button>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>You have no orders.</p>
        <?php endif; ?>
    </div>
    <a href="landing.php" class="home-button">Home</a>
</body>
</html>
