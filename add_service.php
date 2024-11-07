<?php
require('conx_user.php');
session_start();

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Initialize an array to store potential errors
    $errors = [];

    // Validate and sanitize input data
    $prod_id = isset($_POST['prod_id']) ? intval($_POST['prod_id']) : null;
    $serv_type = isset($_POST['serv_type']) ? htmlspecialchars($_POST['serv_type']) : null;
    $speci = isset($_POST['speci']) ? htmlspecialchars($_POST['speci']) : null;
    $serv_amount = isset($_POST['serv_amount']) ? floatval($_POST['serv_amount']) : null;

    // Validate product ID
    if (!$prod_id) {
        $errors[] = "Product ID is required.";
    }

    // Validate service type
    if (!$serv_type) {
        $errors[] = "Service Type is required.";
    }

    // Validate service amount
    if (!$serv_amount || $serv_amount <= 0) {
        $errors[] = "Service Amount must be a positive number.";
    }

    // If there are no errors, proceed with inserting the service into the database
    if (empty($errors)) {
        // Prepare the SQL statement
        $sqlInsertService = "INSERT INTO `service` (`prod_ID`, `serv_Type`, `speci`, `serv_amount`) VALUES (?, ?, ?, ?)";

        // Initialize and prepare the statement
        $stmt = mysqli_prepare($conn, $sqlInsertService);
        if (!$stmt) {
            die('Prepare failed: ' . htmlspecialchars(mysqli_error($conn)));
        }

        // Bind the parameters
        $bind = mysqli_stmt_bind_param($stmt, 'isss', $prod_id, $serv_type, $speci, $serv_amount);
        if (!$bind) {
            die('Bind failed: ' . htmlspecialchars(mysqli_stmt_error($stmt)));
        }

        // Execute the statement
        $exec = mysqli_stmt_execute($stmt);
        if (!$exec) {
            die('Execute failed: ' . htmlspecialchars(mysqli_stmt_error($stmt)));
        }

        // Close the statement
        mysqli_stmt_close($stmt);

        // Redirect to a success page
        header('Location: success_page.php');
        exit();
    } else {
        // Display validation errors
        foreach ($errors as $error) {
            echo "<p>Error: $error</p>";
        }
    }
}

// Close the connection
mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Service</title>
</head>
<body>
    <form action="add_service.php" method="post">
        <label for="prod_id">Product ID:</label>
        <input type="number" id="prod_id" name="prod_id" required><br><br>

        <label for="serv_type">Service Type:</label>
        <input type="text" id="serv_type" name="serv_type" required><br><br>

        <label for="speci">Specifications:</label>
        <input type="text" id="speci" name="speci"><br><br>

        <label for="serv_amount">Service Amount:</label>
        <input type="number" id="serv_amount" name="serv_amount" required><br><br>

        <button type="submit">Add Service</button>
    </form>
</body>
</html>
