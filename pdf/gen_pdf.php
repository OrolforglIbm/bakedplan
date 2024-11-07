<?php
// Include TCPDF library
require_once('tcpdf/tcpdf.php');
require('connect.php');

    // Query to retrieve data from the "sam" table
    $sql = "SELECT iID, iName, iEmail, iRegDate FROM sam ORDER BY iRegDate";
    $stmt = $conn->query($sql);

    // Initialize TCPDF
    $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

    // Set document information
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('Your Name');
    $pdf->SetTitle('Active User and Credential Report');
    $pdf->SetSubject('Active User and Credential Report');
    $pdf->SetKeywords('TCPDF, PDF, report');

    // Remove default header/footer
    $pdf->setPrintHeader(false);
    $pdf->setPrintFooter(false);

    // Add a page
    $pdf->AddPage();

    // Set font
    $pdf->SetFont('helvetica', '', 10);

    // Include Bootstrap CSS
    $html = '<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">';

    // Header
    $html .= '<h1 class="text-center mb-4">Active User and Credential Report</h1>';
    $html .= '<p class="text-center">Report generated on ' . date('Y-m-d') . '</p>';

    // Initialize variable to track current month
    $currentMonth = '';

    // Table
    $html .= '<table class="table table-bordered">';
    $html .= '<thead class="thead-dark">';
    $html .= '<tr><th>Record ID</th><th>Customer Name</th><th>Email Address</th><th>Registration Date</th></tr>';
    $html .= '</thead>';
    $html .= '<tbody>';

    // Loop through the results
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        // Extract month and year from the registration date
        $month = date('F Y', strtotime($row['iRegDate']));

        // Check if a new month is encountered
        if ($currentMonth != $month) {
            // Add a new row for the month label
            $html .= '<tr><td colspan="4"><strong>' . $month . '</strong></td></tr>';
            $currentMonth = $month;
        }

        // Add data for each record
        $html .= '<tr>';
        $html .= '<td>' . $row['iID'] . '</td>';
        $html .= '<td>' . $row['iName'] . '</td>';
        $html .= '<td>' . $row['iEmail'] . '</td>';
        $html .= '<td>' . $row['iRegDate'] . '</td>';
        $html .= '</tr>';
    }

    $html .= '</tbody>';
    $html .= '</table>';

    // Output the HTML content
    $pdf->writeHTML($html, true, false, true, false, '');

    // Close and output PDF document
    $pdf->Output('active_user_report.pdf', 'D');

?>
