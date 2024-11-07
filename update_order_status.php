<?php
session_start();
require('conx_admin.php');

if (!isset($_SESSION['cusID']) || $_SESSION['cusType'] != 2) {
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $orderId = $_POST['order_id'];
    $status = $_POST['status'];

    $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE order_id = ?");
    $stmt->bind_param('si', $status, $orderId);

    if ($stmt->execute()) {
        echo json_encode(['success' => 'Order status updated']);
    } else {
        echo json_encode(['error' => 'Failed to update order status']);
    }

    $stmt->close();

    // Record audit trail
    recordAuditTrail($conn, $_SESSION['cusID'], 'Update Order Status', "Order ID: $orderId, Status: $status");
} else {
    echo json_encode(['error' => 'Invalid request']);
}

function recordAuditTrail($conn, $userId, $action, $details = null) {
    $stmt = $conn->prepare("INSERT INTO audit_trail (user_id, action, details) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $userId, $action, $details);
    $stmt->execute();
    $stmt->close();
}
?>
