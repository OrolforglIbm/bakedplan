<?php
require('conx_admin.php');
session_start();

function recordAuditTrail($conn, $userId, $action, $details = null) {
    $stmt = $conn->prepare("INSERT INTO `audit_trail` (`user_id`, `action`, `details`) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $userId, $action, $details);
    $stmt->execute();
    $stmt->close();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION['cusID'])) {
    $order_id = $_POST['order_id'];
    $userId = $_SESSION['cusID'];
    $action = 'Order Confirmed';
    $details = 'User Order ID: ' . $order_id;

    recordAuditTrail($conn, $userId, $action, $details);

    echo "Audit trail recorded.";
}
?>
