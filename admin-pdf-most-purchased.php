<?php
session_start();
require('conx_admin.php');
require_once('pdf/tcpdf.php');

// Check if admin is logged in
if (!isset($_SESSION['cusID']) || $_SESSION['cusType'] != 2) {
    header('Location: login.php');
    exit();
}

// Get the selected date range for the whole year
$startDate = date('Y-01-01');
$endDate = date('Y-12-31');

// Fetch most purchased item data for the entire year
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

// Fetch current most purchased item for the entire year
$current_most_purchased_item_sql = "SELECT products.product, SUM(order_items.quantity) as total_quantity
                                    FROM order_items
                                    JOIN products ON order_items.product_id = products.prod_ID
                                    JOIN orders ON order_items.order_id = orders.order_id
                                    WHERE DATE(orders.created_at) BETWEEN '$startDate' AND '$endDate'
                                    GROUP BY products.product
                                    ORDER BY total_quantity DESC
                                    LIMIT 1";
$current_most_purchased_item_result = $conn->query($current_most_purchased_item_sql);
$current_most_purchased_item = $current_most_purchased_item_result->fetch_assoc();

// Initialize TCPDF
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// Set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Admin');
$pdf->SetTitle('Most Purchased Items Report for the Year');
$pdf->SetSubject('Most Purchased Items Report');
$pdf->SetKeywords('TCPDF, PDF, report');

// Remove default header/footer
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);

// Add a page
$pdf->AddPage();

// Set font
$pdf->SetFont('helvetica', '', 10);

// Header
$pdf->writeHTML('<h1 class="text-center mb-4">Most Purchased Items Report for the Year</h1>', true, false, true, false, '');
$pdf->writeHTML('<p class="text-center">Report generated on ' . date('Y-m-d') . '</p>', true, false, true, false, '');

// Most Purchased Item for the Year
$pdf->writeHTML('<h3>Most Purchased Item for the Year</h3>', true, false, true, false, '');
if ($current_most_purchased_item) {
    $pdf->writeHTML('<p>Product: ' . htmlspecialchars($current_most_purchased_item['product']) . '</p>', true, false, true, false, '');
    $pdf->writeHTML('<p>Total Quantity: ' . htmlspecialchars($current_most_purchased_item['total_quantity']) . '</p>', true, false, true, false, '');
} else {
    $pdf->writeHTML('<p>No items purchased this year</p>', true, false, true, false, '');
}

// Table
$pdf->writeHTML('<h3>Most Purchased Items</h3>', true, false, true, false, '');
$html = '<table border="1" cellspacing="0" cellpadding="5">';
$html .= '<thead>';
$html .= '<tr>
            <th>Rank</th>
            <th>Product</th>
            <th>Total Quantity</th>
        </tr>';
$html .= '</thead>';
$html .= '<tbody>';
$rank = 0;

// Loop through the results
foreach ($most_purchased_items_data as $row) {
    $rank++;
    // Add data for each record
    $html .= '<tr>';
    $html .= '<td>' . $rank . '</td>';
    $html .= '<td>' . htmlspecialchars($row['product']) . '</td>';
    $html .= '<td>' . htmlspecialchars($row['total_quantity']) . '</td>';
    $html .= '</tr>';
}

$html .= '</tbody>';
$html .= '</table>';

// Output the HTML content
$pdf->writeHTML($html, true, false, true, false, '');

// Close and output PDF document
$pdf->Output('most_purchased_items_' . date('Y') . '.pdf', 'D');

// Close the database connection
$conn->close();
?>
