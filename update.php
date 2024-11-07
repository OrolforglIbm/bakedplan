<?php 
require('conx_user.php');
session_start();

error_reporting(E_ALL);
ini_set('display_errors', 1);

function recordAuditTrail($conn, $userId, $action, $details = null) {
    $stmt = $conn->prepare("INSERT INTO `audit_trail` (`user_id`, `action`, `details`) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $userId, $action, $details);
    $stmt->execute();
    $stmt->close();
}

// Define the target directory for file uploads
$target_dir = "images/profile_pictures/";

// Fetch user data
$cusID = $_SESSION['cusID'];
$sqlFetch = "SELECT * FROM `user_acc` WHERE `cusID` = '$cusID'";
$result = mysqli_query($conn, $sqlFetch);
$userData = mysqli_fetch_assoc($result);

if (!$userData) {
    echo "User not found.";
    exit();
}

// Check if the form was submitted with any changes
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nameChanged = $_POST['lname'] !== $userData['cusNam'];
    $fileUploaded = isset($_FILES["profile_pic"]["name"]) && !empty($_FILES["profile_pic"]["name"]);

    // Initialize a message variable to store the feedback
    $message = "No changes were made.";

    // Variables to track what changes occurred
    $changes = [];

    // Handle profile picture upload if a new file is provided
    if ($fileUploaded) {
        $imageFileType = strtolower(pathinfo($_FILES["profile_pic"]["name"], PATHINFO_EXTENSION));
        $timestamp = date('Y-m-d-H-i-s');
        $target_file = $target_dir . "_" . $cusID . "_profile_pic_" . $timestamp . "." . $imageFileType;

        // Check if image file is a valid image
        $check = getimagesize($_FILES["profile_pic"]["tmp_name"]);
        if ($check === false) {
            echo "File is not an image.";
            exit();
        }

        // Check file size
        if ($_FILES["profile_pic"]["size"] > 5000000) {
            echo "Sorry, your file is too large.";
            exit();
        }

        // Allow certain file formats
        if (!in_array($imageFileType, ["jpg", "png", "jpeg", "gif", "webp"])) {
            echo "Sorry, only JPG, JPEG, PNG, WEBP, & GIF files are allowed.";
            exit();
        }

        // Move uploaded file to target directory
        if (!move_uploaded_file($_FILES["profile_pic"]["tmp_name"], $target_file)) {
            echo "Sorry, there was an error uploading your file.";
            exit();
        } else {
            // Add to changes array
            $changes[] = "Profile picture updated.";
        }
    } else {
        $target_file = $userData['cusPic']; // Keep the old picture if no new picture is uploaded
    }

    if ($nameChanged) {
        $changes[] = "Name updated.";
    }

    if ($nameChanged || $fileUploaded) {
        // Update user's profile picture and name in the database
        $sqlUpdateProfile = "UPDATE `user_acc` SET `cusNam` = ?, `cusPic` = ? WHERE `cusID` = ?";
        $stmt = mysqli_prepare($conn, $sqlUpdateProfile);
        mysqli_stmt_bind_param($stmt, "ssi", $_POST['lname'], $target_file, $cusID);
        mysqli_stmt_execute($stmt);

        // Update session variable with new profile picture and name
        $_SESSION['user']['cusPic'] = $target_file;
        $_SESSION['user']['cusNam'] = $_POST['lname'];

        // Record the audit trail
        $userId = $cusID;
        $action = 'Profile Update';
        $details = 'User updated profile. ' . implode(' ', $changes);
        recordAuditTrail($conn, $userId, $action, $details);

        // Prepare the success message
        $message = implode(' ', $changes) . ' Profile updated successfully.';
    }

    // Redirect to profile page with the appropriate message
    header("Location: profile.php?message=" . urlencode($message));
    exit();
} else {
    echo "Invalid request.";
    exit();
}
