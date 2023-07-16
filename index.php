<?php
session_start();
include_once("config.php");

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve the submitted username and password
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    // Perform basic validation
    $errors = array();

    if (empty($username)) {
        $errors[] = "Username is required.";
    }

    if (empty($password)) {
        $errors[] = "Password is required.";
    }

    // Proceed with login if there are no validation errors
    if (empty($errors)) {
        // Begin the transaction
        $conn->begin_transaction();

        try {
            // Check if the username and password exist in the database
            $checkQuery = "SELECT * FROM users WHERE username = '$username'";
            $result = $conn->query($checkQuery);

            if ($result && $result->num_rows > 0) {
                $user = $result->fetch_assoc();
                $hashedPassword = $user['password'];

                if (password_verify($_POST['password'], $hashedPassword)) {
                    // Valid username and password, set the user session and commit the transaction
                    $_SESSION['username'] = $username;
                    $_SESSION['userid'] = $user['user_id'];
                    $conn->commit();
                    header('Location: dashboard.php');
                    exit();
                } else {
                    // Invalid username or password, rollback the transaction and show an error message
                    $conn->rollback();
                    $errors[] = "Invalid username or password.";
                }
            } else {
                // Invalid username or password, rollback the transaction and show an error message
                $conn->rollback();
                $errors[] = "Invalid username or password.";
            }

        } catch (Exception $e) {
            // Error occurred, rollback the transaction and handle the error
            $conn->rollback();
            $errors[] = "An error occurred during the login process.";
            // You can also log the error to a log file or perform additional error handling here
        }
    }

    // Close the database connection
    $conn->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login Page</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style1.css">
    <style>
    .error {
        color: red;
        margin-bottom: 10px;

    }

    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center align-items-center vh-100">
            <div class="col-lg-4 col-md-6 col-sm-8 col-11">
                <div class="login-box">
                    <center><img src="images/logo.png" class="logo"></center>
                    <h2 style="color: #403e3e;">Welcome</h2>
                    <form action="" method="post">
                        <?php if (!empty($errors)) { ?>
                            <div class="error">
                                <ul>
                                    <?php foreach ($errors as $error) { ?>
                                        <li><?php echo $error; ?></li>
                                    <?php } ?>
                                </ul>
                            </div>
                        <?php } ?>
                        <div class="form-group">
                            <div class="user-box">
                                <input type="text" name="username" class="form-control" required="">
                                <label>Username</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="user-box">
                                <input type="password" name="password" class="form-control" required="">
                                <label>Password</label>
                            </div>
                        </div>
                        <div class="form-group">
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
                        </div>
                        <input type="submit" class="btn btn-primary submit-button" value="Login">
                    </form>
                    <div class="row">
                        <p class="text-center text-muted mt-3 mb-0">Not Registered?
                            <a href="registration.php" style="color: #403e3e;"><u>Create an account</u></a>
                            <a id="btn" class="text-danger " href="forgotpassword.php" style="font-size: 15px;"><u>Forgot my password</u></a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
