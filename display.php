<?php 
    require ('conx_user.php');
    session_start();
    
    // Ensure $_SESSION['cusID'] is set and contains the user data
    if(isset($_SESSION['cusID'])) {
        $cusID = $_SESSION['cusID'];
        $sqlFetch = "SELECT * FROM `user_acc` WHERE `cusID` = '$cusID'";
        $result = mysqli_query($conn, $sqlFetch);
        
        // Check if user data is fetched successfully
        if($result) {
            $userData = mysqli_fetch_assoc($result);
        } else {
            echo "Error fetching user data: " . mysqli_error($conn);
            exit; // Exit if there's an error fetching user data
        }
    } else {
        echo "User session data not found.";
        exit; // Exit if session data is not found
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
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Rubik', sans-serif;
        }

        body {
            background-color: #F8F4E1;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 20px;
        }

        .maindiv {
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            max-width: 400px;
            width: 100%;
            text-align: center;
            padding-top: 150px; /* Add padding to the top */
            position: relative; /* Position relative for child absolute positioning */
        }

        .profilediv {
            padding: 20px;
        }

        .imgdiv {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            background-image: url('<?php echo htmlspecialchars($userData["cusPic"]); ?>');
            background-position: center;
            background-repeat: no-repeat;
            background-size: cover;
            border: 5px solid #193925;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            position: absolute; /* Position absolute for proper layout */
            top: 35px; /* Adjust position to place the image above the main div */
            left: 50%;
            transform: translateX(-50%);
        }


        h2 {
            margin-top: 50px;
            font-size: 24px;
            color: #212529;
        }

        #email {
            margin-top: 10px;
            margin-bottom: 20px;
            color: #666;
        }

        .profile_logout {
            background-color: #f1f1f1;
            padding: 20px;
            border-top: 1px solid #e8e8e8;
        }

        a {
            padding: 10px 20px;
            color: white;
            background-color: #d33242;
            border-radius: 5px;
            border: none;
            font-size: 15px;
            text-decoration: none;
            transition: background-color 0.3s ease;
        }

        a:hover {
            background-color: #b12a36;
        }

        #gb {
            background-color: #193925;
        }

        #gb:hover {
            background-color: #ddd;
            color: black;
        }

        input[type="submit"], #gb {
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="maindiv">
        <div class="imgdiv"></div>
        <div class="profilediv">
            <h2><?php echo htmlspecialchars($userData['cusNam']); ?></h2>
            <p id="email"><?php echo htmlspecialchars($userData['cusEmail']); ?></p>
        </div>
        <div class="profile_logout">
            <a id="gb" href="profile.php">Go Back</a>
        </div>
    </div>
</body>
</html>
