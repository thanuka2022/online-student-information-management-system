<?php
session_start();
include_once("config.php");

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve the submitted username and password
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Begin the transaction
    $conn->begin_transaction();

    try {
        // Check if the username and password exist in the database
        $checkQuery = "SELECT * FROM users WHERE username = '$username' AND password = '$password'";
        $result = $conn->query($checkQuery);

        if ($result && $result->num_rows > 0) {
            // Valid username and password, set the user session and commit the transaction
            $_SESSION['username'] = $username;
            $conn->commit();
            header('Location: dashboard.php');
            exit();
        } else {
            // Invalid username or password, rollback the transaction and show an error message
            $conn->rollback();
            $error = "Invalid username or password.";
        }
    } catch (Exception $e) {
        // Error occurred, rollback the transaction and handle the error
        $conn->rollback();
        $error = "An error occurred during the login process.";
        // You can also log the error to a log file or perform additional error handling here
    }

    // Close the database connection
    $conn->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login Page</title>
    <link rel="stylesheet" href="css/style1.css">
    <style>
    .error {
        color: red;
        margin-bottom: 10px;
    }
    </style>
</head>
<body>
    <div class="login-box">
        <center><img src="images/logo.png" class="logo"></center>
        <h2>Welcome</h2>
        <form action="" method="post">
            <?php if (isset($error)) { ?>
                <div class="error"><?php echo $error; ?></div>
            <?php } ?>
            <div class="user-box">
                <input type="text" name="username" required="">
                <label>Username</label>
            </div>
            <div class="user-box">
                <input type="password" name="password" required="">
                <label>Password</label>
            </div>
            <div class="row">
                <div class="col-8">
                    <div class="icheck-primary">
                        <input type="checkbox" id="remember" <?php if(isset($_COOKIE["user_login"])) { ?> checked <?php } ?>>
                        <label for="remember">
                            Remember Me
                        </label>
                    </div>
                </div>
            </div>
            <input type="submit" class="submit-button" value="Login">
        </form>
        <p class="forgot-password-link">
            <a href="forgotpassword.php">I forgot my password</a>
        </p>
    </div>
</body>
</html>