<?php
session_start();
require('conx_user.php');

$response = ['success' => false, 'error' => ''];

if (!isset($_SESSION['cusID'])) {
    $response['error'] = 'Unauthorized';
    echo json_encode($response);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $orderId = $_POST['order_id'];
    $cusID = $_SESSION['cusID'];

    // Verify that the order belongs to the logged-in customer and is currently "delivering" or "ready_for_pickup"
    $verify_sql = "SELECT * FROM orders WHERE order_id = ? AND cusID = ? AND (status = 'delivering' OR status = 'ready_for_pickup')";
    $stmt = $conn->prepare($verify_sql);
    $stmt->bind_param("ii", $orderId, $cusID);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $update_sql = "UPDATE orders SET status = 'received' WHERE order_id = ?";
        $stmt = $conn->prepare($update_sql);
        $stmt->bind_param("i", $orderId);
        if ($stmt->execute()) {
            $response['success'] = true;
        } else {
            $response['error'] = 'Failed to update order status';
        }
    } else {
        $response['error'] = 'Order not found or status not valid for update';
    }
    $stmt->close();
} else {
    $response['error'] = 'Invalid request';
}

echo json_encode($response);
?>
