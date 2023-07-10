<?php
session_start();
include_once("config.php");
include_once("PHPMailerAutoload.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the user's email from the form
    $email = $_POST["email"];

    // Generate a random token
    $token = bin2hex(random_bytes(16));

    // Update the user's token in the database
    $updateTokenQuery = "UPDATE users SET token = '$token' WHERE email = '$email'";
    mysqli_query($conn, $updateTokenQuery);

    // Send password reset email
    $mail = new PHPMailer(true);
    try {
        // Configure SMTP settings
        $mail->isSMTP();
        $mail->Host = 'smtp.example.com';  // Replace with your SMTP host
        $mail->SMTPAuth = true;
        $mail->Username = 'your_email@example.com';  // Replace with your email address
        $mail->Password = 'your_password';  // Replace with your email password
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        // Set the From and To addresses
        $mail->setFrom('from@example.com', 'Your Name');
        $mail->addAddress($email);

        // Set email subject and body
        $mail->Subject = 'Reset Password';
        $mail->Body = 'Click the following link to reset your password: ' . "http://example.com/resetpassword.php?email=$email&token=$token";

        // Send the email
        $mail->send();

        // Redirect to a success page
        header("Location: resetpassword_success.php");
        exit();
    } catch (Exception $e) {
        // Handle the error
        echo 'Email could not be sent. Error: ', $mail->ErrorInfo;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Reset Password</title>
</head>
<body>
    <h2>Reset Password</h2>
    <form method="post" action="<?php echo $_SERVER["PHP_SELF"]; ?>">
        <label>Email:</label>
        <input type="email" name="email" required>
        <br>
        <input type="submit" value="Reset Password">
    </form>
</body>
</html>
