<?php
// Include database connection
require 'conx_user.php';

function recordAuditTrail($conn, $userId, $action, $details = null) {
    $stmt = $conn->prepare("INSERT INTO `audit_trail` (`user_id`, `action`, `details`) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $userId, $action, $details);
    $stmt->execute();
    $stmt->close();
}

// Start the session
session_start();

// Check if the user is logged in
if (isset($_SESSION['cusID'])) {
    // Get the user ID
    $cusID = $_SESSION['cusID'];
    $email = $_SESSION['cusEmail'];
    
    // Check if the logout form was submitted
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['logout'])) {
        // Record logout event in audit trail
        $details = 'User logged out: ' . $email; // Optionally, include the user's email in the details
        recordAuditTrail($conn, $cusID, 'Logout');
        
        // Destroy session
        session_destroy();
        
        // Redirect to login page
        header("Location: login.php");
        exit();
    }
} else {
    // Redirect to login page if not logged in
    header("Location: login.php");
    exit();
}
?>
