<?php
session_start();
require('conx_user.php');

$response = ['success' => false, 'error' => ''];

if (!isset($_SESSION['cusID'])) {
    $response['error'] = 'User not logged in.';
    echo json_encode($response);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $order_id = $_POST['order_id'];

    // Fetch order to verify if it's the user's order
    $stmt = $conn->prepare("SELECT * FROM orders WHERE order_id = ? AND cusID = ?");
    $stmt->bind_param("ii", $order_id, $_SESSION['cusID']);
    $stmt->execute();
    $order_result = $stmt->get_result();
    $order = $order_result->fetch_assoc();
    $stmt->close();

    if ($order) {
        // Check if the delivery method is pickup and the status is approved
        if ($order['delivery_method'] == 'pickup' && $order['status'] == 'approved') {
            // Update the order status to 'ready for pickup'
            $update_stmt = $conn->prepare("UPDATE orders SET status = 'ready_for_pickup' WHERE order_id = ?");
            $update_stmt->bind_param("i", $order_id);
            if ($update_stmt->execute()) {
                $response['success'] = true;
            } else {
                $response['error'] = 'Failed to update order status.';
            }
            $update_stmt->close();
        } else {
            $response['error'] = 'Order is not eligible to be marked as ready for pickup.';
        }
    } else {
        $response['error'] = 'Order not found or you do not have permission to update this order.';
    }
} else {
    $response['error'] = 'Invalid request method.';
}

echo json_encode($response);
?>
