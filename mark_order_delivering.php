<?php
session_start();
require('conx_user.php');

if (!isset($_SESSION['cusID'])) {
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $orderId = $_POST['order_id'];
    $cusID = $_SESSION['cusID'];

    // Verify that the order belongs to the logged-in customer and is currently "approved"
    $verify_sql = "SELECT * FROM orders WHERE order_id = ? AND cusID = ? AND status = 'approved'";
    $stmt = $conn->prepare($verify_sql);
    $stmt->bind_param("ii", $orderId, $cusID);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $update_sql = "UPDATE orders SET status = 'delivering' WHERE order_id = ?";
        $stmt = $conn->prepare($update_sql);
        $stmt->bind_param("i", $orderId);
        if ($stmt->execute()) {
            echo json_encode(['success' => 'Order status updated to delivering']);
        } else {
            echo json_encode(['error' => 'Failed to update order status']);
        }
    } else {
        echo json_encode(['error' => 'Order not found or status not valid for update']);
    }
    $stmt->close();
} else {
    echo json_encode(['error' => 'Invalid request']);
}
?>
