<?php
session_start();
require('conx_admin.php');

// Check if admin is logged in
if (!isset($_SESSION['cusID']) || $_SESSION['cusType'] != 2) {
    header('Location: login.php');
    exit();
}

// Get the selected date range
$startDate = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-01');
$endDate = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-t');

// Fetch total sales
$total_sales_sql = "SELECT DATE(created_at) as date, SUM(total_price) as total_sales FROM orders WHERE DATE(created_at) BETWEEN '$startDate' AND '$endDate' GROUP BY DATE(created_at)";
$total_sales_result = $conn->query($total_sales_sql);
$total_sales_data = [];
$total_sales = 0;
while ($row = $total_sales_result->fetch_assoc()) {
    $total_sales_data[] = $row;
    $total_sales += $row['total_sales'];
}

// Fetch most purchased item data
$most_purchased_items_sql = "SELECT products.product, SUM(order_items.quantity) as total_quantity
                            FROM order_items
                            JOIN products ON order_items.product_id = products.prod_ID
                            JOIN orders ON order_items.order_id = orders.order_id
                            WHERE DATE(orders.created_at) BETWEEN '$startDate' AND '$endDate'
                            GROUP BY products.product
                            ORDER BY total_quantity DESC";
$most_purchased_items_result = $conn->query($most_purchased_items_sql);
$most_purchased_items_data = [];
while ($row = $most_purchased_items_result->fetch_assoc()) {
    $most_purchased_items_data[] = $row;
}

// Fetch current most purchased item
$current_most_purchased_item_sql = "SELECT products.product, SUM(order_items.quantity) as total_quantity
                                    FROM order_items
                                    JOIN products ON order_items.product_id = products.prod_ID
                                    JOIN orders ON order_items.order_id = orders.order_id
                                    WHERE DATE(orders.created_at) = CURDATE()
                                    GROUP BY products.product
                                    ORDER BY total_quantity DESC
                                    LIMIT 1";
$current_most_purchased_item_result = $conn->query($current_most_purchased_item_sql);
$current_most_purchased_item = $current_most_purchased_item_result->fetch_assoc();

// Fetch orders data
$orders_sql = "SELECT DATE(created_at) as date, COUNT(*) as order_count
               FROM orders
               WHERE DATE(created_at) BETWEEN '$startDate' AND '$endDate'
               GROUP BY DATE(created_at)";
$orders_result = $conn->query($orders_sql);
$orders_data = [];
while ($row = $orders_result->fetch_assoc()) {
    $orders_data[] = $row;
}

// Fetch orders with customer names
$orders_sql = "SELECT orders.order_id, orders.cusID, user_acc.cusNam, orders.total_price, orders.created_at, orders.status
               FROM orders
               JOIN user_acc ON orders.cusID = user_acc.cusID
               WHERE DATE(orders.created_at) BETWEEN '$startDate' AND '$endDate'
               ORDER BY orders.created_at DESC";
$orders_result = $conn->query($orders_sql);
$orders = $orders_result->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #F8F4E1;
        }
        .navbar {
            background-color: #193925;
            padding: 10px;
        }
        .navbar a {
            color: white;
            padding: 14px 20px;
            text-decoration: none;
        }
        .navbar a:hover {
            background-color: #ddd;
            color: black;
        }
        .navbar div {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .logout-btn {
            background-color: #193925;
            color: white;
            border: none;
            padding: 10px 20px;
            cursor: pointer;
        }
        .logout-btn:hover {
            background-color: #c82333;
        }
        .container {
            margin-top: 20px;
        }
        .card {
            height: 100%;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }
        .card-header {
            background-color: #193925;
            color: white;
            font-weight: bold;
        }
        .card-body {
            padding: 20px;
        }
        table {
            width: 100%;
            margin-bottom: 20px;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        tr:hover {
            background-color: #f2f2f2;
        }
        .text-center {
            text-align: center;
        }
        .form-inline {
            margin-bottom: 20px;
        }
    </style>
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

    <div class="container">
        <h1 class="text-center"><b>Admin Dashboard</b></h1>

        <form method="get" class="form-inline justify-content-center">
            <label for="start_date" class="mr-2">Start Date:</label>
            <input type="date" id="start_date" name="start_date" class="form-control mr-2" value="<?php echo htmlspecialchars($startDate); ?>" required>
            <label for="end_date" class="mr-2">End Date:</label>
            <input type="date" id="end_date" name="end_date" class="form-control mr-2" value="<?php echo htmlspecialchars($endDate); ?>" required>
            <button type="submit" class="btn btn-primary">Filter</button>
        </form>

        <div class="card">
            <div class="card-header">Total Sales</div>
            <div class="card-body">
                <h5 class="card-title">₱<?php echo number_format($total_sales, 2); ?></h5>
                <canvas id="totalSalesChart"></canvas>
            </div>
        </div>

        <div class="card">
            <div class="card-header">Most Purchased Item Today</div>
            <div class="card-body">
                <?php if ($current_most_purchased_item): ?>
                    <h5 class="card-title"><?php echo htmlspecialchars($current_most_purchased_item['product']); ?></h5>
                    <p>Total Quantity: <?php echo htmlspecialchars($current_most_purchased_item['total_quantity']); ?></p>
                <?php else: ?>
                    <h5 class="card-title">No items purchased today</h5>
                <?php endif; ?>
            </div>
        </div>

        <div class="card">
            <div class="card-header">Most Purchased Items</div>
            <div class="card-body">
                <canvas id="mostPurchasedItemsChart"></canvas>
                <a href="admin-pdf-most-purchased.php" class="btn btn-primary">Download Most Purchased Items Report</a>
            </div>
        </div>

        <div class="card">
            <div class="card-header">Orders</div>
            <div class="card-body">
                <canvas id="ordersChart"></canvas>
                <a href="admin-pdf-orders.php" class="btn btn-primary">Download Orders Report</a>
                <table>
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Customer ID</th>
                            <th>Customer Name</th> <!-- Added Customer Name column -->
                            <th>Total Price</th>
                            <th>Order Date</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $order): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($order['order_id']); ?></td>
                                <td><?php echo htmlspecialchars($order['cusID']); ?></td>
                                <td><?php echo htmlspecialchars($order['cusNam']); ?></td> <!-- Displaying Customer Name -->
                                <td>₱<?php echo number_format($order['total_price'], 2); ?></td>
                                <td><?php echo htmlspecialchars($order['created_at']); ?></td>
                                <td><?php echo htmlspecialchars($order['status']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($orders)): ?>
                            <tr>
                                <td colspan="6" class="text-center">No orders found for this date range.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        function confirmLogout(event) {
            if (!confirm('Are you sure you want to log out?')) {
                event.preventDefault();
            }
        }

        document.addEventListener('DOMContentLoaded', function () {
            var totalSalesData = <?php echo json_encode($total_sales_data); ?>;
            var mostPurchasedItemsData = <?php echo json_encode($most_purchased_items_data); ?>;
            var ordersData = <?php echo json_encode($orders_data); ?>;

            var totalSalesLabels = totalSalesData.map(function(item) {
                return item.date;
            });
            var totalSalesValues = totalSalesData.map(function(item) {
                return item.total_sales;
            });

            var mostPurchasedItemsLabels = mostPurchasedItemsData.map(function(item) {
                return item.product;
            });
            var mostPurchasedItemsValues = mostPurchasedItemsData.map(function(item) {
                return item.total_quantity;
            });

            var ordersLabels = ordersData.map(function(item) {
                return item.date;
            });
            var ordersValues = ordersData.map(function(item) {
                return item.order_count;
            });

            var totalSalesCtx = document.getElementById('totalSalesChart').getContext('2d');
            var totalSalesChart = new Chart(totalSalesCtx, {
                type: 'bar',
                data: {
                    labels: totalSalesLabels,
                    datasets: [{
                        label: 'Total Sales',
                        data: totalSalesValues,
                        borderColor: 'rgba(75, 192, 192, 1)',
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        x: {
                            display: true,
                            title: {
                                display: true,
                                text: 'Date'
                            }
                        },
                        y: {
                            display: true,
                            title: {
                                display: true,
                                text: 'Sales Amount'
                            }
                        }
                    }
                }
            });

            var mostPurchasedItemsCtx = document.getElementById('mostPurchasedItemsChart').getContext('2d');
            var mostPurchasedItemsChart = new Chart(mostPurchasedItemsCtx, {
                type: 'bar',
                data: {
                    labels: mostPurchasedItemsLabels,
                    datasets: [{
                        label: 'Most Purchased Items',
                        data: mostPurchasedItemsValues,
                        borderColor: 'rgba(255, 99, 132, 1)',
                        backgroundColor: 'rgba(255, 99, 132, 0.2)',
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        x: {
                            display: true,
                            title: {
                                display: true,
                                text: 'Product'
                            }
                        },
                        y: {
                            display: true,
                            title: {
                                display: true,
                                text: 'Quantity'
                            }
                        }
                    }
                }
            });

            var ordersCtx = document.getElementById('ordersChart').getContext('2d');
            var ordersChart = new Chart(ordersCtx, {
                type: 'bar',
                data: {
                    labels: ordersLabels,
                    datasets: [{
                        label: 'Orders',
                        data: ordersValues,
                        borderColor: 'rgba(153, 102, 255, 1)',
                        backgroundColor: 'rgba(153, 102, 255, 0.2)',
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        x: {
                            display: true,
                            title: {
                                display: true,
                                text: 'Date'
                            }
                        },
                        y: {
                            display: true,
                            title: {
                                display: true,
                                text: 'Order Count'
                            }
                        }
                    }
                }
            });
        });
    </script>
</body>
</html>
