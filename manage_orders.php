<?php
session_start();
require('conx_user.php');

// Check if admin is logged in
if (!isset($_SESSION['cusID']) || $_SESSION['cusType'] != 2) {
    header('Location: login.php');
    exit();
}

// Fetch all orders
$order_sql = "SELECT * FROM orders ORDER BY created_at DESC";
$result = $conn->query($order_sql);
$orders = $result->fetch_all(MYSQLI_ASSOC);


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Manage Orders</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        h1 { text-align: center; margin-top: 10px; }
        .navbar { background-color: #193925; padding: 10px; }
        .navbar a { color: white; padding: 14px 20px; text-decoration: none; }
        .navbar a:hover { background-color: #ddd; color: black; }
        .navbar div { display: flex; align-items: center; }
        .logout-btn { background-color: #193925; color: white; border: none; padding: 10px 20px; cursor: pointer; }
        .logout-btn:hover { background-color: #c82333; }
        table { width: 80%; margin: 20px auto; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        tr:nth-child(even) { background-color: #f9f9f9; }
        tr:hover { background-color: #f2f2f2; }
        .btn-action { margin: 5px; }
    </style>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script>
        function confirmLogout(event) {
            event.preventDefault();
            if (confirm('Are you sure you want to log out?')) {
                alert('User Logged Out!');
                document.getElementById('logoutForm').submit();
            }
        }

        function updateOrderStatus(orderId, status) {
            $.ajax({
                url: 'update_order_status.php',
                type: 'POST',
                data: { order_id: orderId, status: status },
                success: function(response) {
                    alert('Order status updated to ' + status);
                    location.reload();
                },
                error: function() {
                    alert('Error updating order status.');
                }
            });
        }
    </script>
</head>
<body>
    <div class="navbar">
        <div>
            <a href="admin.php">Dashboard</a>
            <a href="users.php">Users</a>
            <a href="audit.php">Audit Trail</a>
            <a href="manage_orders.php">Manage Orders</a>
        </div>
        <div>
            <form id="logoutForm" action="logout.php" method="post" style="margin: 0;">
                <input type="hidden" name="logout" value="1">
                <input type="submit" value="Logout" class="logout-btn" onclick="confirmLogout(event)">
            </form>
        </div>
    </div>

    <h1 class="text-center"><b>Manage Orders</b></h1>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Order ID</th>
                <th>Customer ID</th>
                <th>Reference Number</th>
                <th>Total Price</th>
                <th>Delivery Method</th>
                <th>Delivery Date</th>
                <th>Payment Type</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($orders as $order): ?>
                <tr>
                    <td><?php echo htmlspecialchars($order['order_id']); ?></td>
                    <td><?php echo htmlspecialchars($order['cusID']); ?></td>
                    <td><?php echo htmlspecialchars($order['reference_number']); ?></td>
                    <td>₱<?php echo number_format($order['total_price'], 2); ?></td>
                    <td><?php echo htmlspecialchars($order['delivery_method']); ?></td>
                    <td><?php echo htmlspecialchars($order['delivery_date']); ?></td>
                    <td><?php echo htmlspecialchars($order['payment_type']); ?></td>
                    <td><?php echo htmlspecialchars($order['status']); ?></td>
                    <td>
                        <?php if ($order['status'] == 'pending'): ?>
                            <button class="btn btn-success btn-action" onclick="updateOrderStatus(<?php echo $order['order_id']; ?>, 'approved')">Approve</button>
                        <?php elseif ($order['status'] == 'approved'): ?>
                            <?php if ($order['delivery_method'] == 'pickup'): ?>
                                <button class="btn btn-primary btn-action" onclick="updateOrderStatus(<?php echo $order['order_id']; ?>, 'ready_for_pickup')">Ready for Pickup</button>
                            <?php else: ?>
                                <button class="btn btn-primary btn-action" onclick="updateOrderStatus(<?php echo $order['order_id']; ?>, 'delivering')">Deliver</button>
                            <?php endif; ?>
                        <?php elseif ($order['status'] == 'ready_for_pickup'): ?>
                            <button class="btn btn-secondary btn-action" disabled>Ready for Pickup</button>
                        <?php elseif ($order['status'] == 'delivering'): ?>
                            <button class="btn btn-secondary btn-action" disabled>Delivering</button>
                        <?php elseif ($order['status'] == 'completed'): ?>
                            <button class="btn btn-secondary btn-action" disabled>Completed</button>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>
