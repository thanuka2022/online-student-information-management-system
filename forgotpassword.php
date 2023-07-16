<?php

include 'config.php';
session_start();
$errors = [];

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

//Load Composer's autoloader
require('PHPmailer/Exception.php');
require('PHPmailer/SMTP.php');
require('PHPmailer/PHPMailer.php');


function sendEmail($email, $resetToken)
{
    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'mtkdesilva@gmail.com'; // SMTP username
        $mail->Password = 'xxwaxiujarxnvfrj'; // SMTP password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        // $mail->SMTPDebug = SMTP::DEBUG_SERVER;
        $mail->Port = 465;
        
        // Set email content
        $mail->setFrom('mtkdesilva@gmail.com', 'OSIMS');
        $mail->addAddress($email);                                // Add a recipient
        $mail->isHTML(true);                                      // Set email format to HTML
        $mail->Subject = 'Reset Password Link from OSIMS';
        $mail->Body = "Click the link to reset your password: <a href='http://localhost/OSIMS/reset_password.php?email=$email&token=$resetToken'>Reset Password</a><br><br>Your reset token is: $resetToken<br><br>Enter that token to reset your password.";

        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];

    $checkQuery = "SELECT * FROM `users` WHERE email = '$email'";
    $result = $conn->query($checkQuery);

    if ($result) {
        if ($result->num_rows == 1) {
            $resetToken = bin2hex(random_bytes(10));

            $updateQuery = "UPDATE users SET `reset_token`='$resetToken' WHERE email = '$email'";

            if ($conn->query($updateQuery) && sendEmail($email, $resetToken)) {
                echo "<script>alert('Reset Password Link sent to your email')</script>";
            } else {
                echo "<script>alert('Server error 002')</script>";
            }
        } else {
            echo "<script>alert('User not registered')</script>";
        }
    } else {
        echo "<script>alert('Server error')</script>";
    }
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>Forgot Password</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style1.css">
    <style>
        .error-message {
            color: red;
            margin-bottom: 10px;
        }

        .login-box {
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            background-color: #f8f8f8;
        }

        .login-box h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        .user-box {
            position: relative;
            margin-bottom: 20px;
        }

        .user-box input {
            width: 100%;
            padding: 10px 0;
            font-size: 16px;
        }

        .user-box label {
            position: absolute;
            top: 0;
            left: 0;
            padding: 10px 0;
            font-size: 16px;
            color: #888;
            pointer-events: none;
            transition: 0.5s;
        }

        .user-box input:focus ~ label,
        .user-box input:valid ~ label {
            top: -20px;
            left: 0;
            color: #333;
            font-size: 12px;
        }

        .submit-button {
            display: block;
            width: 100%;
            padding: 10px;
            margin-top: 30px;
            border: none;
            background-color: #4CAF50;
            color: #fff;
            font-size: 16px;
            cursor: pointer;
        }

        .login-link {
            text-align: center;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6 col-md-8">
                <div class="login-box">
                    <center><img src="images/logo.png" class="logo"></center>
                    <h2>Forgot Password</h2>
                    <?php if (!empty($errors)) { ?>
                        <div class="error-message">
                            <ul>
                                <?php foreach ($errors as $error) { ?>
                                    <li><?php echo $error; ?></li>
                                <?php } ?>
                            </ul>
                        </div>
                    <?php } ?>
                    <form action="" method="post">
                        <div class="user-box">
                            <input type="email" name="email" required="">
                            <label>Email</label>
                        </div>
                        <input type="submit" name="register" class="btn btn-primary submit-button" value="Reset Password">
                    </form>
                    <p class="login-link">
                        <a href="index.php">Back to Login</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
