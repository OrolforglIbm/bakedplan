<?php require('connect.php'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Active User and Credential Report</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>

<div class="container">
    <h1 class="mt-5 mb-4">Active User and Credential</h1>
    <p>Report generated on <?php echo date('Y-m-d'); ?></p>
    <table class="table table-striped table-bordered">
        <thead class="thead-dark">
            <tr><th>Record ID</th><th>Customer Name</th><th>Email Address</th><th>Registration Date</th></tr>
        </thead>
    <tbody>
    <?php
    // Pagination
        $limit = 30; // Number of records per page
        $page = isset($_GET['page']) ? $_GET['page'] : 1; // Current page number

    // Calculate offset for pagination
        $offset = ($page - 1) * $limit;

    // Query to retrieve data from the "sam" table with pagination
        $sql = "SELECT iID, iName, iEmail, iRegDate FROM sam ORDER BY iRegDate LIMIT :limit OFFSET :offset";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

    // Fetch data
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Initialize variables
        $currentMonth = '';

    // Loop through the results
        foreach ($rows as $row) {
            $regDate = date('F Y', strtotime($row['iRegDate']));
            // Check if a new month is encountered
            if ($currentMonth != $regDate) {
                $currentMonth = $regDate;
                echo '<tr><td colspan="4"><strong>' . $currentMonth . '</strong></td></tr>';
            }
            // Add data for each record
            echo '<tr>';
            echo '<td>' . $row['iID'] . '</td>';
            echo '<td>' . $row['iName'] . '</td>';
            echo '<td>' . $row['iEmail'] . '</td>';
            echo '<td>' . $row['iRegDate'] . '</td>';
            echo '</tr>';
        }
    ?>

    </tbody>
    </table>
    <!-- Add pagination links -->
    <nav aria-label="Page navigation">
        <ul class="pagination justify-content-center">
           <li class="page-item <?php echo ($page <= 1 ? 'disabled' : ''); ?>">
              <a class="page-link" href="?page=<?php echo ($page - 1); ?>" tabindex="-1">Previous</a>
           </li>
           <li class="page-item">
              <a class="page-link" href="?page=<?php echo ($page + 1); ?>">Next</a>
           </li>
        </ul>
    </nav>

    <!-- Add buttons for PDF conversion -->
    <div class="mt-3">
       <a href="gen_pdf.php" target="_blank" class="btn btn-primary">Download PDF</a>
    </div>
</div>
</body>
</html>

<?php
// Close the database connection
$conn = null;
?>
