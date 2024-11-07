<?php
session_start();
require('conx_admin.php');
require_once('pdf/tcpdf.php');

// Check if admin is logged in
if (!isset($_SESSION['cusID']) || $_SESSION['cusType'] != 2) {
    header('Location: login.php');
    exit();
}

// Fetch the selected action filter
$action_filter = isset($_GET['action_filter']) ? $_GET['action_filter'] : '';

// Fetch the selected user filter
$user_filter = isset($_GET['user_filter']) ? $_GET['user_filter'] : '';

// Fetch audit trail data from the database with filters
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
$sql_audit .= " ORDER BY audit_trail.timestamp DESC";

$result_audit = $conn->query($sql_audit);
$audit_trail_data = [];
while ($row = $result_audit->fetch_assoc()) {
    $audit_trail_data[] = $row;
}

// Initialize TCPDF
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// Set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Admin');
$pdf->SetTitle('Audit Trail Report');
$pdf->SetSubject('Audit Trail Report');
$pdf->SetKeywords('TCPDF, PDF, audit, report');

// Remove default header/footer
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);

// Add a page
$pdf->AddPage();

// Set font
$pdf->SetFont('helvetica', '', 10);

// Header
$pdf->writeHTML('<h1 class="text-center mb-4">Audit Trail Report</h1>', true, false, true, false, '');
$pdf->writeHTML('<p class="text-center">Report generated on ' . date('Y-m-d') . '</p>', true, false, true, false, '');

// Filter information
$pdf->writeHTML('<p><strong>Filters:</strong> Action: ' . ($action_filter ?: 'All') . ', User ID: ' . ($user_filter ?: 'All') . '</p>', true, false, true, false, '');

// Detailed Audit Trail Table
$pdf->writeHTML('<h3>Audit Trail Details</h3>', true, false, true, false, '');
$html = '<table border="1" cellspacing="0" cellpadding="5">';
$html .= '<thead>';
$html .= '<tr>
            <th>ID</th>
            <th>User ID</th>
            <th>Customer Name</th>
            <th>Action</th>
            <th>Timestamp</th>
        </tr>';
$html .= '</thead>';
$html .= '<tbody>';

// Loop through the results
if (!empty($audit_trail_data)) {
    foreach ($audit_trail_data as $row) {
        $html .= '<tr>';
        $html .= '<td>' . htmlspecialchars($row['id']) . '</td>';
        $html .= '<td>' . htmlspecialchars($row['user_id']) . '</td>';
        $html .= '<td>' . htmlspecialchars($row['cusNam']) . '</td>';
        $html .= '<td>' . htmlspecialchars($row['action']) . '</td>';
        $html .= '<td>' . htmlspecialchars($row['timestamp']) . '</td>';
        $html .= '</tr>';
    }
} else {
    $html .= '<tr><td colspan="5">No audit trail records found.</td></tr>';
}

$html .= '</tbody>';
$html .= '</table>';

// Output the HTML content
$pdf->writeHTML($html, true, false, true, false, '');

// Close and output PDF document
$pdf->Output('audit_trail_report_' . date('Y-m-d') . '.pdf', 'D');

// Close the database connection
$conn->close();
?>
