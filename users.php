<?php
session_start();
require('conx_admin.php');

// Pagination logic
$limit = 50; // Number of items per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Fetch total number of user records
$sql_count = "SELECT COUNT(*) as total FROM user_acc WHERE cusType = 1";
$result_count = $conn->query($sql_count);
$total_records = $result_count->fetch_assoc()['total'];
$total_pages = ceil($total_records / $limit);

// Fetch users with cusType 1 from the database with limit and offset
$sql_users = "SELECT * FROM user_acc WHERE cusType = 1 LIMIT $limit OFFSET $offset";
$result_users = $conn->query($sql_users);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Page</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
        }
        table {
            width: 80%;
            margin: 20px auto;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #dddddd;
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
        h1 {
            margin-top: 10px;
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
        .pagination {
            display: flex;
            justify-content: center;
            margin-top: 20px;
            margin-bottom: 10px;
        }
        .pagination a {
            margin: 0 5px;
            padding: 8px 16px;
            text-decoration: none;
            background-color: #343a40;
            color: white;
            border-radius: 4px;
        }
        .pagination a:hover {
            background-color: #495057;
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
                <input type="submit" value="Logout" class="logout-btn">
            </form>
        </div>
    </div>

    <h1 class="text-center"><b>List of Users</b></h1>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Customer ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Contact Number</th>
                <th>Address</th>
                <th>Registration Date</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result_users->num_rows > 0) {
                while ($row = $result_users->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row["cusID"]) . "</td>";
                    echo "<td>" . htmlspecialchars($row["cusNam"]) . "</td>";
                    echo "<td>" . htmlspecialchars($row["cusEmail"]) . "</td>";
                    echo "<td>" . htmlspecialchars($row["cusCNum"]) . "</td>";
                    echo "<td>" . htmlspecialchars($row["cusAdd"]) . "</td>";
                    echo "<td>" . htmlspecialchars($row["cusregDate"]) . "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='6'>No users found.</td></tr>";
            }
            ?>
        </tbody>
    </table>

    <div class="pagination">
        <?php if ($page > 1): ?>
            <a href="?page=<?php echo $page - 1; ?>">&laquo; Previous</a>
        <?php endif; ?>
        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
            <a href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
        <?php endfor; ?>
        <?php if ($page < $total_pages): ?>
            <a href="?page=<?php echo $page + 1; ?>">Next &raquo;</a>
        <?php endif; ?>
    </div>

    <div class="text-center">
        <a href="admin.php" class="btn btn-primary">Back</a>
    </div>
</body>
</html>
