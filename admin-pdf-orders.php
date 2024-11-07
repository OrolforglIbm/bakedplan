<?php
session_start();
require('conx_admin.php');
require_once('pdf/tcpdf.php');

// Check if admin is logged in
if (!isset($_SESSION['cusID']) || $_SESSION['cusType'] != 2) {
    header('Location: login.php');
    exit();
}

// Get the selected date range for the current year
$startDate = date('Y-01-01');
$endDate = date('Y-12-31');

// Fetch total sales for the entire year
$total_sales_sql = "SELECT DATE(created_at) as date, SUM(total_price) as total_sales 
                    FROM orders 
                    WHERE DATE(created_at) BETWEEN '$startDate' AND '$endDate' 
                    GROUP BY DATE(created_at)";
$total_sales_result = $conn->query($total_sales_sql);
$total_sales_data = [];
while ($row = $total_sales_result->fetch_assoc()) {
    $total_sales_data[] = $row;
}

// Fetch total orders for the entire year
$total_orders_sql = "SELECT DATE(created_at) as date, COUNT(*) as order_count 
                     FROM orders 
                     WHERE DATE(created_at) BETWEEN '$startDate' AND '$endDate' 
                     GROUP BY DATE(created_at)";
$total_orders_result = $conn->query($total_orders_sql);
$total_orders_data = [];
while ($row = $total_orders_result->fetch_assoc()) {
    $total_orders_data[] = $row;
}

// Fetch detailed orders for the entire year
$detailed_orders_sql = "SELECT orders.order_id, orders.cusID, user_acc.cusNam, orders.total_price, orders.created_at, orders.status 
                        FROM orders 
                        JOIN user_acc ON orders.cusID = user_acc.cusID 
                        WHERE DATE(orders.created_at) BETWEEN '$startDate' AND '$endDate' 
                        ORDER BY orders.created_at DESC";
$detailed_orders_result = $conn->query($detailed_orders_sql);
$detailed_orders_data = [];
while ($row = $detailed_orders_result->fetch_assoc()) {
    $detailed_orders_data[] = $row;
}

// Initialize TCPDF
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// Set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Admin');
$pdf->SetTitle('Orders Report for the Year');
$pdf->SetSubject('Orders Report');
$pdf->SetKeywords('TCPDF, PDF, report');

// Remove default header/footer
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);

// Add a page
$pdf->AddPage();

// Set font
$pdf->SetFont('helvetica', '', 10);

// Header
$pdf->writeHTML('<h1 class="text-center mb-4">Orders Report for the Year</h1>', true, false, true, false, '');
$pdf->writeHTML('<p class="text-center">Report generated on ' . date('Y-m-d') . '</p>', true, false, true, false, '');

// Total Sales
$pdf->writeHTML('<h3>Total Sales for the Year</h3>', true, false, true, false, '');
if (!empty($total_sales_data)) {
    $total_sales_amount = array_reduce($total_sales_data, function ($carry, $item) {
        return $carry + $item['total_sales'];
    }, 0);
    $pdf->writeHTML('<p>Total Sales: Php ' . number_format($total_sales_amount, 2) . '</p>', true, false, true, false, '');
} else {
    $pdf->writeHTML('<p>No sales recorded this year</p>', true, false, true, false, '');
}

// Total Orders
$pdf->writeHTML('<h3>Total Orders for the Year</h3>', true, false, true, false, '');
if (!empty($total_orders_data)) {
    $total_orders_count = array_reduce($total_orders_data, function ($carry, $item) {
        return $carry + $item['order_count'];
    }, 0);
    $pdf->writeHTML('<p>Total Orders: ' . $total_orders_count . '</p>', true, false, true, false, '');
} else {
    $pdf->writeHTML('<p>No orders recorded this year</p>', true, false, true, false, '');
}

// Detailed Orders Table
$pdf->writeHTML('<h3>Order Details</h3>', true, false, true, false, '');
$html = '<table border="1" cellspacing="0" cellpadding="5">';
$html .= '<thead>';
$html .= '<tr>
            <th>Order ID</th>
            <th>Customer ID</th>
            <th>Customer Name</th>
            <th>Total Price</th>
            <th>Order Date</th>
            <th>Status</th>
        </tr>';
$html .= '</thead>';
$html .= '<tbody>';

// Loop through the results
foreach ($detailed_orders_data as $row) {
    $html .= '<tr>';
    $html .= '<td>' . htmlspecialchars($row['order_id']) . '</td>';
    $html .= '<td>' . htmlspecialchars($row['cusID']) . '</td>';
    $html .= '<td>' . htmlspecialchars($row['cusNam']) . '</td>';
    $html .= '<td>Php ' . number_format($row['total_price'], 2) . '</td>';
    $html .= '<td>' . htmlspecialchars($row['created_at']) . '</td>';
    $html .= '<td>' . htmlspecialchars($row['status']) . '</td>';
    $html .= '</tr>';
}

$html .= '</tbody>';
$html .= '</table>';

// Output the HTML content
$pdf->writeHTML($html, true, false, true, false, '');

// Close and output PDF document
$pdf->Output('orders_report_' . date('Y') . '.pdf', 'D');

// Close the database connection
$conn->close();
?>
