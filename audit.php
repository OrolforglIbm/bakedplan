<?php
session_start();

require('conx_admin.php');

// Pagination logic
$limit = 50; // Number of items per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Fetch the selected action filter
$action_filter = isset($_GET['action_filter']) ? $_GET['action_filter'] : '';

// Fetch the selected user filter
$user_filter = isset($_GET['user_filter']) ? $_GET['user_filter'] : '';

// Fetch total number of audit trail records with filters
$sql_count = "SELECT COUNT(*) as total FROM audit_trail WHERE 1=1";
if (!empty($action_filter)) {
    $sql_count .= " AND action = '" . $conn->real_escape_string($action_filter) . "'";
}
if (!empty($user_filter)) {
    $sql_count .= " AND user_id = '" . $conn->real_escape_string($user_filter) . "'";
}
$result_count = $conn->query($sql_count);
$total_records = $result_count->fetch_assoc()['total'];
$total_pages = ceil($total_records / $limit);

// Fetch audit trail data from the database with limit, offset, and filters
$sql_audit = "
    SELECT audit_trail.*, user_acc.cusNam 
    FROM audit_trail 
    LEFT JOIN user_acc ON audit_trail.user_id = user_acc.cusID
    WHERE 1=1";

if (!empty($action_filter)) {
    $sql_audit .= " AND audit_trail.action = '" . $conn->real_escape_string($action_filter) . "'";
}
if (!empty($user_filter)) {
    $sql_audit .= " AND audit_trail.user_id = '" . $conn->real_escape_string($user_filter) . "'";
}
$sql_audit .= " ORDER BY audit_trail.timestamp DESC LIMIT $limit OFFSET $offset";

$result_audit = $conn->query($sql_audit);
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
            text-align: center;
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
            text-align: center;
            margin-top: 20px;
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
            background-color: #193925;
            color: white;
            border-radius: 4px;
        }
        .pagination a:hover {
            background-color: #495057;
        }
        .text-center {
            margin-top: 20px;
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

    <h1 class="text-center"><b>Audit Trail</b></h1>

    <form method="get" class="form-inline justify-content-center mb-4">
        <label for="action_filter" class="mr-2">Filter by Action:</label>
        <select id="action_filter" name="action_filter" class="form-control mr-2">
            <option value="">All</option>
            <?php
            // Fetch distinct actions from audit_trail table for the filter dropdown
            $actions_sql = "SELECT DISTINCT action FROM audit_trail";
            $actions_result = $conn->query($actions_sql);
            while ($action = $actions_result->fetch_assoc()) {
                $selected = ($action['action'] == $action_filter) ? 'selected' : '';
                echo "<option value='" . htmlspecialchars($action['action']) . "' $selected>" . htmlspecialchars($action['action']) . "</option>";
            }
            ?>
        </select>

        <label for="user_filter" class="mr-2">Filter by User ID:</label>
        <select id="user_filter" name="user_filter" class="form-control mr-2">
            <option value="">All</option>
            <?php
            // Fetch distinct user IDs from audit_trail table for the filter dropdown
            $users_sql = "SELECT DISTINCT user_id FROM audit_trail";
            $users_result = $conn->query($users_sql);
            while ($user = $users_result->fetch_assoc()) {
                $selected = ($user['user_id'] == $user_filter) ? 'selected' : '';
                echo "<option value='" . htmlspecialchars($user['user_id']) . "' $selected>" . htmlspecialchars($user['user_id']) . "</option>";
            }
            ?>
        </select>
        <button type="submit" class="btn btn-primary">Filter</button>
    </form>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>User ID</th>
                <th>Name</th>
                <th>Action</th>
                <th>Timestamp</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result_audit->num_rows > 0) {
                while ($row = $result_audit->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row["id"]) . "</td>";
                    echo "<td>" . htmlspecialchars($row["user_id"]) . "</td>";
                    echo "<td>" . htmlspecialchars($row["cusNam"]) . "</td>"; // Display the email
                    echo "<td>" . htmlspecialchars($row["action"]) . "</td>";
                    echo "<td>" . htmlspecialchars($row["timestamp"]) . "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='5'>No audit trail found.</td></tr>"; // Updated colspan to match new column count
            }
            ?>
        </tbody>
    </table>

    <div class="pagination">
        <?php if ($page > 1): ?>
            <a href="?page=<?php echo $page - 1; ?>&action_filter=<?php echo htmlspecialchars($action_filter); ?>&user_filter=<?php echo htmlspecialchars($user_filter); ?>">&laquo; Previous</a>
        <?php endif; ?>
        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
            <a href="?page=<?php echo $i; ?>&action_filter=<?php echo htmlspecialchars($action_filter); ?>&user_filter=<?php echo htmlspecialchars($user_filter); ?>"><?php echo $i; ?></a>
        <?php endfor; ?>
        <?php if ($page < $total_pages): ?>
            <a href="?page=<?php echo $page + 1; ?>&action_filter=<?php echo htmlspecialchars($action_filter); ?>&user_filter=<?php echo htmlspecialchars($user_filter); ?>">Next &raquo;</a>
        <?php endif; ?>
    </div>

    <div class="text-center">
        <a href="admin-pdf-audit.php" class="btn btn-primary">Download Audit Trail Report</a>
        <a href="admin.php" class="btn btn-primary">Back</a>
    </div>
</body>
</html>

