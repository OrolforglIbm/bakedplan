<?php

session_start();

// Check if the user is logged in, if not then redirect to login page
if (!isset($_SESSION['cusID'])) {
    header("Location: login.php");
    exit();
}

// Include database connection
require 'conx_user.php';

// Fetch user data
$cusID = $_SESSION['cusID'];
$sqlFetch = "SELECT * FROM `user_acc` WHERE `cusID` = '$cusID'";
$result = mysqli_query($conn, $sqlFetch);

// Check if the query was successful and user data was fetched
if ($result && mysqli_num_rows($result) > 0) {
    $user = mysqli_fetch_assoc($result);
} else {
    // Handle the case where user data is not found or query fails
    echo "Error: Unable to fetch user data.";
    exit();
}

// Handle logout
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['logout'])) {
    // Destroy session and redirect to login page
    session_destroy();
    header("Location: login.php");
    exit();
}

// Close database connection
mysqli_close($conn);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Landing Page</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Rubik:ital,wght@0,300..900;1,300..900&display=swap');

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Rubik', sans-serif;
        }

        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            background-color: #F8F4E1;
            color: #212529;
        }

        .navbar {
            background-color: #193925;
            overflow: hidden;
        }

        .navbar a {
            float: left;
            display: block;
            color: #f2f2f2;
            text-align: center;
            padding: 14px 20px;
            text-decoration: none;
            transition: background-color 0.3s ease;
        }

        .navbar a:hover {
            background-color: #ddd;
            color: black;
        }

        .logout-btn {
            background: none;
            border: none;
            color: white;
            cursor: pointer;
            font-size: 16px;
            padding: 14px 20px;
        }

        .logout-btn:hover {
            background-color: #EE4E4E;
        }

        .container {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 60px 20px 20px 20px;
            text-align: center;
            background-image: url('background.jpg'); /* Add your background image here */
            background-size: cover;
            background-position: center;
        }

        .content-box {
            background-color: rgba(255, 255, 255, 0.9);
            padding: 50px 40px;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            max-width: 600px;
        }

        .content-box h1 {
            font-size: 36px;
            margin-bottom: 20px;
            color: #212529;
        }

        .content-box p {
            font-size: 18px;
            color: #212529;
        }

        .order-btn {
            margin-top: 20px;
            padding: 10px 20px;
            color: white;
            background-color: #193925;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }

        .order-btn:hover {
            background-color: #145f2e;
        }
    </style>
    <script>
        function confirmLogout(event) {
            event.preventDefault();
            if (confirm('Are you sure you want to log out?')) {
                alert('User Logged Out!');
                document.getElementById('logoutForm').submit();
            }
        }
    </script>
</head>
<body>
    <div class="navbar">
    <a href="landing.php">Home</a>
    <a href="products.php">Product List</a>
    <a href="cart.php">Cart</a>
    <a href="profile.php">Profile</a>
    <!-- <a href="user_orders.php">My Orders</a> -->
        <div style="float: right;">
            <form id="logoutForm" action="logout.php" method="post" style="margin: 0;">
                <input type="hidden" name="logout" value="1">
                <input type="submit" value="Logout" class="logout-btn" onclick="confirmLogout(event)">
            </form>
        </div>
    </div>

    <div class="container">
        <div class="content-box">
            <h1>Welcome, <?php echo htmlspecialchars($user['cusNam']); ?>!</h1>
            <!-- <p>You are logged in as a <?php echo $user['cusType'] == 1 ? 'Customer' : 'Admin'; ?>.</p> -->
            <a href="products.php" class="order-btn">Order Now</a>
            <!-- <a href="user_orders.php" class="order-btn">View Orders</a> -->
        </div>
    </div>
</body>
</html>













