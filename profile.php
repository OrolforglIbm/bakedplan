<?php
require('conx_user.php');
session_start();
$user = $_SESSION['cusID'];

$cusID = $_SESSION['cusID'];
$sqlFetch = "SELECT * FROM `user_acc` WHERE `cusID` = '$cusID'";
$result = mysqli_query($conn, $sqlFetch);
$userData = mysqli_fetch_assoc($result);

if (!$userData) {
    echo "User not found.";
    exit();
}

// Handle form submission (update profile)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Handle profile update logic here
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Rubik:ital,wght@0,300..900;1,300..900&display=swap');

        * {
            margin: 0;
            padding: 0;
            font-family: Rubik;
            background-color: #F8F4E1;
        }

        body {
            margin: 0;
            padding: 0;
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
            background-color: #193925;
            text-align: center;
            padding: 14px 20px;
            text-decoration: none;
        }

        .navbar a:hover {
            background-color: #ddd;
            color: black;
        }

        .div {
            padding: 20px;
            max-width: 600px;
            margin: 20px auto; /* Center and add top margin */
            background-color: #F8F4E1;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .message {
            color: green;
            text-align: center;
            margin-bottom: 20px; /* Spacing below the message */
            font-weight: bold; /* Bold text for message */
        }

        h1 {
            margin-bottom: 20px;
            font-weight: 600;
            color: #212529;
        }

        label {
            font-size: 16px;
            margin-bottom: 8px;
            color: #495057;
        }

        input[type="text"], input[type="password"], input[type="email"], input[type="file"] {
            font-size: 14px;
            padding: 10px;
            width: 100%;
            border: 1px solid #ced4da;
            border-radius: 5px;
            margin-bottom: 15px;
        }

        input[type="submit"], a {
            padding: 10px 20px;
            color: #fff;
            background-color: #193925;
            border: none;
            font-size: 16px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            transition: background-color 0.3s;
        }

        input[type="submit"]:hover, a:hover {
            background-color: #ddd;
            color: black;
        }

        .imgdiv {
            width: 200px;
            height: 200px;
            border-radius: 100%;
            padding: 5px;
            background-image: url('<?php echo htmlspecialchars($userData["cusPic"]); ?>');
            border: 2px solid #193925;
            background-position: center;
            background-repeat: no-repeat;
            background-size: cover;
            margin: 0 auto;
            margin-bottom: 20px;
        }

        #preview {
            max-width: 200px;
            margin: 10px auto;
            display: block;
        }
    </style>
    <script>
        function confirmLogout(event) {
            event.preventDefault(); // Prevent form from submitting

            if (confirm('Are you sure you want to log out?')) {
                // User confirmed logout, show alert and submit form
                alert('User Logged Out!');
                document.getElementById('logoutForm').submit();
            }
        }

        function previewFile() {
            const preview = document.getElementById('preview');
            const file = document.querySelector('input[type=file]').files[0];
            const reader = new FileReader();

            reader.onloadend = function() {
                preview.src = reader.result;
            }

            if (file) {
                reader.readAsDataURL(file);
            } else {
                preview.src = "";
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

    <div class="div">
        <h1>User Profile</h1>

        <?php
        // Display the message inside the user profile div
        if (isset($_GET['message'])) {
            echo '<div class="message">' . htmlspecialchars($_GET['message']) . '<br></div>';
        }
        ?>

        <form action="update.php" method="post" enctype="multipart/form-data">
            <div class="imgdiv"></div>

            <label for="email">Email</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($userData['cusEmail']); ?>" disabled>

            <label for="lname">Name</label>
            <input type="text" id="lname" name="lname" value="<?php echo htmlspecialchars($userData['cusNam']); ?>" required max="50" pattern="[a-zA-Z\s]{1,50}$" title="Name must contain only letters and be maximum 50 characters long.">

            <label for="profile_pic">Profile Picture</label>
            <input class="file" type="file" name="profile_pic" id="profile_pic" onchange="previewFile()">
            <img id="preview" src="" alt="">

            <div>
                <input type="submit" value="Update">
                <a href="display.php">Profile</a>
            </div>
        </form>
    </div>
</body>
</html>
