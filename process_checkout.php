// process_checkout.php

<?php
session_start();
require('conx_user.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $deliveryOption = $_POST['deliveryOption'];
    $deliveryDate = $_POST['deliveryDate'];
    $paymentMethod = $_POST['paymentMethod'];
    $gcashNumber = $_POST['gcashNumber'] ?? null;
    $total_price = 0;
    $shipping_cost = ($deliveryOption === 'delivery') ? 70 : 0;

    // Calculate total price
    foreach ($_SESSION['cart'] as $item) {
        $total_price += $item['price'] * $item['quantity'];
    }
    $total_price += $shipping_cost;

    // If PayMongo is selected, initiate the API checkout session
    if ($paymentMethod === 'gcash') {
        $apiUrl = 'https://api.paymongo.com/v1/checkout_sessions';
        $apiKey = 'sk_test_nYXD5qAN3dGZWQtEbS4GFnC7';

        $line_items = [];
    foreach ($order_items as $item) {
       $line_items[] = [
           'name' => $item['name'], // Assuming 'name' is the name of the item
           'amount' => intval($item['price'] * 100), // Convert to centavos
           'currency' => 'PHP',
           'quantity' => $item['quantity'],
        ];
    }

        $checkoutData = [
            'data' => [
                'attributes' => [
                    'cancel_url' => 'http://localhost/cart.php?payment=cancel',
                    'success_url' => 'http://localhost/bakedplan/landing.php?order_id=' . $order_id,
                    'line_items' => [
                        [
                            'name' => 'Order Total',
                            'amount' => intval($total_price * 100), // Amount in centavos
                            'currency' => 'PHP',
                            'quantity' => 1,
                        ]
                    ],
                    'payment_method_types' => ['gcash'],
                ]
            ]
        ];

        $checkoutDataJson = json_encode($checkoutData);

        $ch = curl_init($apiUrl);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Basic ' . base64_encode($apiKey . ':')
        ]);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $checkoutDataJson);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $responseArray = json_decode($response, true);

        if ($httpCode == 201 || $httpCode == 200) {
            $checkoutUrl = $responseArray['data']['attributes']['checkout_url'];
            $_SESSION['checkout_session_id'] = $responseArray['data']['id'];
            header("Location: $checkoutUrl");
            exit();
        } else {
            $errorMessage = $responseArray['errors'][0]['detail'] ?? 'Unknown error';
            echo "Payment failed: " . htmlspecialchars($errorMessage);
            exit();
        }
    } else {
        // Handle other payment methods (e.g., GCash and Cash on Delivery)
        // Insert order details into the database as in the original code
        // ...
        header("Location: localhost/bakedplan/confirmation.php?order_id=$order_id");
        exit();
    }
}
?>
