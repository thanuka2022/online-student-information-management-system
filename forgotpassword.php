<?php
// Include the database connection and necessary functions
require_once "config.php";

// Include PHPMailer library
require 'path/to/PHPMailer/PHPMailer.php';
require 'path/to/PHPMailer/SMTP.php';
require 'path/to/PHPMailer/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Validate the email address
    $email = $_POST["email"];

    // Perform necessary validation checks on the email address
    // ...

    // If the email is valid, send the password reset email
    if (isValidEmail($email)) {
        try {
            // Begin the transaction
            $conn->begin_transaction();

            // Generate a unique password reset token
            $token = generateResetToken();

            // Store the token in the database along with the user's email and expiration time
            storeResetToken($conn, $email, $token);

            // Commit the transaction if all operations are successful
            $conn->commit();

            // Send the password reset email
            sendResetEmail($email, $token);

            // Redirect the user to a confirmation page
            header("Location: resetpassword.php?success=true");
            exit();
        } catch (Exception $e) {
            // Rollback the transaction if any error occurs
            $conn->rollback();

            // Handle the error
            $error = "An error occurred. Please try again later.";
        }
    } else {
        $error = "Invalid email address. Please try again.";
    }
}

// Close the database connection
$conn->close();

// Function to store the reset token in the database
// Function to store the reset token and email in the "users" table
function storeResetToken($conn, $email, $token)
{
    $query = "UPDATE users SET token = '$token', email = '$email' WHERE email = '$email'";
    $conn->query($query);
}


// Example function to check if the email is valid
function isValidEmail($email)
{
    // Perform necessary validation checks on the email address
    // ...

    return true;
}

// Example function to generate a unique reset token
function generateResetToken()
{
    $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    $token = '';

    for ($i = 0; $i < 10; $i++) {
        $randomIndex = mt_rand(0, strlen($characters) - 1);
        $token .= $characters[$randomIndex];
    }

    return $token;
}


// Function to send the password reset email
function sendResetEmail($email, $token)
{
    // Instantiate PHPMailer
    $mail = new PHPMailer(true);

    try {
        // SMTP configuration
        $mail->isSMTP();
        $mail->Host = 'smtp.example.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'your-email@example.com';
        $mail->Password = 'your-email-password';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Sender and recipient
        $mail->setFrom('your-email@example.com', 'Your Name');
        $mail->addAddress($email);

        // Email content
        $mail->isHTML(true);
        $mail->Subject = 'Password Reset';
        $mail->Body = 'Click the following link to reset your password: <a href="https://example.com/reset-password.php?token=' . $token . '">Reset Password</a>';

        // Send the email
        $mail->send();

        echo 'Password reset email sent successfully';
    } catch (Exception $e) {
        echo 'Error sending password reset email: ' . $mail->ErrorInfo;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Forgot Password</title>
    <link rel="stylesheet" href="css/style1.css">
</head>
<body>
    <div class="login-box">
        <center><img src="images/logo.png" class="logo"></center>
        <h2>Forgot Password</h2>
        <?php if (isset($error)) { ?>
            <p class="error-message"><?php echo $error; ?></p>
        <?php } ?>
        <form action="forgotpassword.php" method="post">
            <div class="user-box">
                <input type="email" name="email" required="">
                <label>Email</label>
            </div>
            <input type="submit" class="submit-button" value="Reset Password">
        </form>
        <p class="login-link">
            <a href="index.php">Back to Login</a>
        </p>
    </div>
</body>
</html>
