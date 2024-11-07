<?php
require 'conx_user.php';
session_start();

$error = "Credentials does not match!";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['cusEmail'];
    $pass = $_POST['cusPass'];

    $sqlFetch = "SELECT * FROM `user_acc` WHERE `cusEmail` = '$email'";
    $result = mysqli_query($conn, $sqlFetch);

    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $hashed_pass = $row['cusPass'];

        if (password_verify($pass, $hashed_pass)) {
            $_SESSION['cusID'] = $row['cusID'];
            $_SESSION['cusNam'] = $row['cusNam'];
            $_SESSION['cusType'] = $row['cusType'];

            if ($row['cusType'] == 1) {
                header("Location: landing.php");
                exit();
            } else if ($row['cusType'] == 2) {
                header("Location: admin.php");
                exit();
            }
        } else {
            $error = "Credentials do not match!";
        }
    } else {
        $error = "Credentials do not match!";
    }
}
?>

<!DOCTYPE html>
<html lang="en" dir="ltr">

<head>
    <meta charset="utf-8">
    <title>Login Form</title>
    <style>
        @import url('https://fonts.googleapis.com/css?family=Poppins:400,500,600,700&display=swap');

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        html,
        body {
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

        ::selection {
            background: #4158d0;
            color: #fff;
        }

        .wrapper {
            width: 380px;
            background: #fff;
            border-radius: 15px;
            box-shadow: 0px 15px 20px rgba(0, 0, 0, 0.5);
        }

        .wrapper .title {
            font-size: 35px;
            font-weight: 600;
            text-align: center;
            line-height: 100px;
            color: #fff;
            user-select: none;
            border-radius: 15px 15px 0 0;
            background: linear-gradient( 25deg, #193925, #4b5320);
        }

        .wrapper form {
            padding: 10px 30px 50px 30px;
        }

        .wrapper form .field {
            height: 50px;
            width: 100%;
            margin-top: 20px;
            position: relative;
        }

        .wrapper form .field input {
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
        form .field input:valid {
            border-color: #4158d0;
        }

        .wrapper form .field label {
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

        form .field input:focus~label,
        form .field input:valid~label {
            top: 0%;
            font-size: 16px;
            color: #4158d0;
            background: #fff;
            transform: translateY(-50%);
        }

        form .content {
            display: flex;
            width: 100%;
            height: 50px;
            font-size: 16px;
            align-items: center;
            justify-content: space-around;
        }

        form .content .checkbox {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        form .content input {
            width: 15px;
            height: 15px;
            background: red;
        }

        form .content label {
            color: #262626;
            user-select: none;
            padding-left: 5px;
        }

        form .content .pass-link {
            color: "";
        }

        form .field input[type="submit"] {
            color: #fff;
            border: none;
            padding-left: 0;
            margin-top: -10px;
            font-size: 20px;
            font-weight: 500;
            cursor: pointer;
            background: linear-gradient(25deg, #193925, #4b5320);
            transition: all 0.3s ease;
        }

        form .field input[type="submit"]:active {
            transform: scale(0.95);
        }

        form .signup-link {
            color: #262626;
            margin-top: 20px;
            text-align: center;
        }

        form .pass-link a,
        form .signup-link a {
            color: #4158d0;
            text-decoration: none;
        }

        form .pass-link a:hover,
        form .signup-link a:hover {
            text-decoration: underline;
        }

        /* Error message styling */
        .error-message {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
            padding: 10px;
            margin-top: 10px;
            border-radius: 5px;
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="wrapper">
        <div class="title">
            Login Form
        </div>
        <form action="login.php" method="post">
            <?php
            if (!empty($error)) {
                echo '<div class="error-message">' . $error . '</div>';
            }
            ?>
            <div class="field">
                <input type="text" name="cusEmail" required>
                <label>Email Address</label>
            </div>
            <div class="field">
                <input type="password" name="cusPass" required>
                <label>Password</label>
            </div>
            <div class="field">
                <input type="submit" value="Login">
            </div>
            <div class="signup-link">
                Do not have an account? <a href="signup.php">Signup</a>
            </div>
        </form>
    </div>
</body>

</html>
