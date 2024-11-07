<?php
require 'conx_user.php';
session_start();

function recordAuditTrail($conn, $userId, $action, $details = null) {
    $stmt = $conn->prepare("INSERT INTO `audit_trail` (`user_id`, `action`, `details`) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $userId, $action, $details);
    $stmt->execute();
    $stmt->close();
}

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['cusEmail'];
    $pass = $_POST['cusPass'];

    // Fetch user data including login attempts
    $stmt = $conn->prepare("SELECT * FROM `user_acc` WHERE `cusEmail` = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();

    if ($user) {
        $current_time = new DateTime();
        $locked_until = $user['locked_until'] ? new DateTime($user['locked_until']) : null;

        // Check if account is locked
        if ($locked_until && $current_time < $locked_until) {
            $error = "Your account is locked. Please try again later.";
        } else {
            // Verify password
            if (password_verify($pass, $user['cusPass'])) {
                // Successful login
                $userId = $user['cusID'];
                $action = 'Login';
                $details = 'User logged in: ' . $email;
                recordAuditTrail($conn, $userId, $action, $details);

                // Reset login attempts
                $stmt = $conn->prepare("UPDATE `user_acc` SET `login_attempts` = 0, `locked_until` = NULL WHERE `cusEmail` = ?");
                $stmt->bind_param("s", $email);
                $stmt->execute();
                $stmt->close();

                // Set session and redirect
                $_SESSION['cusID'] = $user['cusID'];
                $_SESSION['cusNam'] = $user['cusNam'];
                $_SESSION['cusType'] = $user['cusType'];

                if ($user['cusType'] == 1) {
                    header("Location: landing.php");
                    exit();
                } else if ($user['cusType'] == 2) {
                    header("Location: admin.php");
                    exit();
                }
            } else {
                // Failed login attempt
                $login_attempts = $user['login_attempts'] + 1;

                // Determine lock duration
                $lock_duration = 0;
                if ($login_attempts >= 5) {
                    $lock_duration = 5; // Lock for 5 minutes
                } elseif ($login_attempts >= 3) {
                    $lock_duration = 1; // Lock for 1 minute
                }

                if ($lock_duration > 0) {
                    $locked_until = $current_time->modify("+{$lock_duration} minutes")->format('Y-m-d H:i:s');
                } else {
                    $locked_until = null;
                }

                // Update login attempts and lock status
                $stmt = $conn->prepare("UPDATE `user_acc` SET `login_attempts` = ?, `locked_until` = ? WHERE `cusEmail` = ?");
                $stmt->bind_param("iss", $login_attempts, $locked_until, $email);
                $stmt->execute();
                $stmt->close();

                $error = "Credentials do not match!";
            }
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
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Poppins', sans-serif; }
        html, body { display: grid; height: 100%; width: 100%; place-items: center; background-image: url('logo1.png'); background-color: #193925; background-repeat: no-repeat; background-position: top; background-size: 325px; }
        .wrapper { width: 380px; background: #fff; border-radius: 15px; box-shadow: 0px 15px 20px rgba(0, 0, 0, 0.5); }
        .wrapper .title { font-size: 35px; font-weight: 600; text-align: center; line-height: 100px; color: #fff; user-select: none; border-radius: 15px 15px 0 0; background: linear-gradient(25deg, #193925, #4b5320); }
        .wrapper form { padding: 10px 30px 50px 30px; }
        .wrapper form .field { height: 50px; width: 100%; margin-top: 20px; position: relative; }
        .wrapper form .field input { height: 100%; width: 100%; outline: none; font-size: 17px; padding-left: 20px; border: 1px solid lightgrey; border-radius: 25px; transition: all 0.3s ease; }
        .wrapper form .field input:focus, form .field input:valid { border-color: #4158d0; }
        .wrapper form .field label { position: absolute; top: 50%; left: 20px; color: #999999; font-weight: 400; font-size: 17px; pointer-events: none; transform: translateY(-50%); transition: all 0.3s ease; }
        form .field input:focus~label, form .field input:valid~label { top: 0%; font-size: 16px; color: #4158d0; background: #fff; transform: translateY(-50%); }
        form .content { display: flex; width: 100%; height: 50px; font-size: 16px; align-items: center; justify-content: space-around; }
        form .content .checkbox { display: flex; align-items: center; justify-content: center; }
        form .content input { width: 15px; height: 15px; }
        form .content label { color: #262626; user-select: none; padding-left: 5px; }
        form .field input[type="submit"] { color: #fff; border: none; padding-left: 0; margin-top: -10px; font-size: 20px; font-weight: 500; cursor: pointer; background: linear-gradient(25deg, #193925, #4b5320); transition: all 0.3s ease; }
        form .field input[type="submit"]:active { transform: scale(0.95); }
        form .signup-link { color: #262626; margin-top: 20px; text-align: center; }
        form .signup-link a { color: #4158d0; text-decoration: none; }
        form .signup-link a:hover { text-decoration: underline; }
        .error-message { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; padding: 10px; margin-top: 10px; border-radius: 5px; text-align: center; }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="title">
            Login Form
        </div>
        <form action="login.php" method="post">
            <?php if (!empty($error)) { echo '<div class="error-message">' . $error . '</div>'; } ?>
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
                Not a member? <a href="signup.php">Signup now</a>
            </div>
        </form>
    </div>
</body>
</html>
