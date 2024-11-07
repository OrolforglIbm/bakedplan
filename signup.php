<?php
require 'conx_user.php';
session_start();

function recordAuditTrail($conn, $userId, $action, $details = null) {
    $stmt = $conn->prepare("INSERT INTO `audit_trail` (`user_id`, `action`, `details`) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $userId, $action, $details);
    $stmt->execute();
    $stmt->close();
}

$errors = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Trim and sanitize inputs
    $name = trim($_POST['cusNam']);
    $email = trim($_POST['cusEmail']);
    $pass = $_POST['cusPass'];
    $pnumber = trim($_POST['cusCNum']);
    $address = trim($_POST['cusAdd']);
    $type = 1; 

    // Validate name (letters and spaces only)
    if (!preg_match("/^[a-zA-Z ]+$/", $name)) {
        $errors[] = "<b>Invalid name format.</b>";
    }

    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "<b>Invalid email format.</b>";
    }

    // Validate contact number (digits only)
    if (!preg_match("/^[0-9]+$/", $pnumber)) {
        $errors[] = "<b>Invalid contact number format.</b>";
    }

    // Validate password (at least 8 characters, one uppercase letter, one number, and one special character)
    if (!preg_match('/^(?=.*[a-zA-Z])(?=.*\d)(?=.*[\W_]).{8,}$/', $pass)) {
        $errors[] = "<b>Invalid password format.</b>";
    }

    // Validate address (alphanumeric and spaces)
    if (!preg_match("/^[a-zA-Z0-9 .,]+$/", $address)) {
        $errors[] = "<b>Invalid address format.</b>";
    }

    if (empty($errors)) {
        $checkEmailQuery = "SELECT * FROM `user_acc` WHERE `cusEmail` = '$email'";
        $result = mysqli_query($conn, $checkEmailQuery);

        if ($result) {
            if (mysqli_num_rows($result) > 0) {
                $errors[] = "Email already exists!";
            } else {
                $hashed_pass = password_hash($pass, PASSWORD_DEFAULT);

                $sql = "INSERT INTO `user_acc` (`cusNam`, `cusEmail`, `cusPass`, `cusCNum`, `cusAdd`, `cusType`) 
                        VALUES ('$name', '$email', '$hashed_pass', '$pnumber', '$address', '$type')";
                $result = mysqli_query($conn, $sql);

                if ($result) {
                    $userId = mysqli_insert_id($conn); // Get the ID of the newly signed up user
                    $action = 'Signup';
                    $details = 'New user signed up: ' . $name; // Optionally, include the user's name in the details
                    recordAuditTrail($conn, $userId, $action, $details);
                    
                    echo '<script>alert("Account successfully registered!");';
                    echo 'window.location.href = "login.php";</script>'; // Redirect to login.php after displaying the alert
                    exit();
                } else {
                    $errors[] = "Error: " . mysqli_error($conn);
                }

            }
        } else {
            $errors[] = "Error: " . mysqli_error($conn);
        }
    }

    mysqli_close($conn);
}
?>

<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="utf-8">
    <title>Sign Up Form</title>
    <style>
        @import url('https://fonts.googleapis.com/css?family=Poppins:400,500,600,700&display=swap');
        *{
          margin: 0;
          padding: 0;
          box-sizing: border-box;
          font-family: 'Poppins', sans-serif;
        }
        html,body{
          display: grid;
            height: 100%;
            width: 100%;
            place-items: center;
            background-image: url('logo1.png');
            background-color: #193925;
            background-repeat: no-repeat;
            background-position: top;
            background-size: 325px;
        }
        ::selection{
          background: #4158d0;
          color: #fff;
        }
        .wrapper{
          width: 550px;
          background: #fff;
          border-radius: 15px;
          box-shadow: 0px 15px 20px rgba(0,0,0,0.1);
        }
        .wrapper .title{
          font-size: 35px;
          font-weight: 600;
          text-align: center;
          line-height: 100px;
          color: #fff;
          user-select: none;
          border-radius: 15px 15px 0 0;
          background: linear-gradient( 25deg, #193925, #4b5320);
        }
        .wrapper form{
          padding: 10px 30px 50px 30px;
        }
        .wrapper form .field{
          height: 50px;
          width: 100%;
          margin-top: 20px;
          position: relative;
          margin-bottom: 30px;
        }
        .wrapper form .field.address-field{
          height: 50px;
          width: 100%;
          margin-top: 20px;
          position: relative;
          margin-bottom: 50px;
        }
        .wrapper form .field input{
          height: 100%;
          width: 100%;
          outline: none;
          font-size: 17px;
          padding-left: 20px;
          border: 1px solid lightgrey;
          border-radius: 25px;
          transition: all 0.3s ease;
        }
        .wrapper form .field input:focus,
        form .field input:valid{
          border-color: #4158d0;
        }
        .wrapper form .field label{
          position: absolute;
          top: 50%;
          left: 20px;
          color: #999999;
          font-weight: 400;
          font-size: 17px;
          pointer-events: none;
          transform: translateY(-50%);
          transition: all 0.3s ease;
        }
        form .field input:focus ~ label,
        form .field input:valid ~ label{
          top: 0%;
          font-size: 16px;
          color: #4158d0;
          background: #fff;
          transform: translateY(-50%);
        }
        form .content{
          display: flex;
          flex-direction: column;
          width: 100%;
          font-size: 16px;
          align-items: flex-start;
          justify-content: space-around;
        }
        form .content .field{
          width: 100%;
        }
        form .content .checkbox{
          display: flex;
          align-items: center;
          justify-content: center;
        }
        form .content input{
          width: 15px;
          height: 15px;
          background: red;
        }
        form .content label{
          color: #262626;
          user-select: none;
          padding-left: 5px;
        }
        form .content .pass-link{
          color: "";
        }
        form .field input[type="submit"]{
          color: #fff;
          border: none;
          padding-left: 0;
          margin-top: 20px;
          font-size: 20px;
          font-weight: 500;
          cursor: pointer;
          background: linear-gradient( 25deg, #193925, #4b5320);
          transition: all 0.3s ease;
        }
        form .field input[type="submit"]:active{
          transform: scale(0.95);
        }
        form .signup-link{
          color: #262626;
          margin-top: 20px;
          text-align: center;
        }
        form .pass-link a,
        form .signup-link a{
          color: #4158d0;
          text-decoration: none;
        }
        form .pass-link a:hover,
        form .signup-link a:hover{
          text-decoration: underline;
        }
        /* Error message styling */
        .error-messages {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 5px;
        }

        .error-messages ul {
            list-style-type: none;
            padding: 0;
        }

        .error-messages li {
            margin-bottom: 5px;
        }

        .field p {
            font-size: 14px;
            color: #888; /* Adjust the color as needed */
            margin-top: 5px;
            margin-left: 15px;
            margin-bottom: 5px; /* Add some space between the input field and the paragraph */
        }

    </style>
</head>
<body>
    <div class="wrapper">
        <div class="title">
            Sign Up Form
        </div>
        <form action="signup.php" method="post">
            <?php
            if (!empty($errors)) {
                echo '<div class="error-messages"><ul>';
                foreach ($errors as $error) {
                    echo '<li>' . $error . '</li>';
                }
                echo '</ul></div>';
            }
            ?>
            <div class="field">
                <input type="text" name="cusNam" required>
                <label>Name</label>
                <p>Must Contain letters and spaces only (e.g., Juan Santos).</p>
            </div>
            <div class="field">
                <input type="text" name="cusEmail" required>
                <label>Email Address</label>
                <p>Must contain a valid email format (e.g., example@example.com).</p>

            </div>
            <div class="field">
                <input type="text" name="cusCNum" required>
                <label>Contact Number</label>
                <p>Must contain digits only (e.g., 09*********).</p>
            </div>
            <div class="field address-field">
                <input type="text" name="cusAdd" required>
                <label>Address</label>
                <p><p>Must contain alphanumeric characters, spaces, commas, and periods only (e.g., 123 Main St , Barangay, City, Province).</p>
            </div>
            <div class="field">
                <input type="password" name="cusPass" required>
                <label>Password</label>
                <p>Must contain at least 8 characters long and contain at least one uppercase letter, one number, and one special character.</p>
            </div>
            <div class="field">
                <input type="submit" value="Sign Up">
            </div>
            <div class="signup-link">
                Already have an account? <a href="login.php">Login</a>
            </div>
        </form>
    </div>
</body>
</html>
