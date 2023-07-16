<?php
include 'config.php';
session_start();
$resetToken = $_GET['token'];
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];
    $reset_token = $_POST['reset_token'];

    if (empty($email)) {
        $errors[] = "Email is required.";
    }
    if (empty($password)) {
        $errors[] = "Password is required.";
    }
    if ($password !== $confirmPassword) {
        $errors[] = "Passwords do not match.";
    }
    if ($resetToken !== $reset_token) {
        $errors[] = "Reset token does not match.";
    }

    if (empty($errors)) {
        $checkQuery = "SELECT * FROM users WHERE email = '$email'";
        $result = $conn->query($checkQuery);

        if ($result && $result->num_rows == 1) {
            $password = password_hash($password, PASSWORD_BCRYPT);
            $updateQuery = "UPDATE users SET password = '$password', reset_token = '$resetToken' WHERE email = '$email'";

            if ($conn->query($updateQuery)) {
                // Password reset successful
                echo "<script>alert('Password reset successful')</script>";
            } else {
                $errors[] = "Failed to reset password.";
            }
        } else {
            $errors[] = "Invalid email address.";
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Reset Password</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style1.css">
    <style>
        .error {
            color: red;
            margin-bottom: 10px;
        }

        .login-box {
            max-width: 400px;
            margin: 0 auto;
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

        /* Media Query for Mobile Large Responsive View */
        @media (min-width: 576px) and (max-width: 767.98px) {
            .login-box {
                max-width: 320px;
                margin: 0 auto;
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="login-box">
                    <center><img src="images/logo.png" class="logo"></center>
                    <h2>Reset Password</h2>
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
                        <div class="form-group">
                            <div class="user-box">
                                <input type="email" name="email" class="form-control" required="">
                                <label>Email</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="user-box">
                                <input type="password" name="password" class="form-control" required="">
                                <label>New Password</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="user-box">
                                <input type="password" name="confirm_password" class="form-control" required="">
                                <label>Confirm Password</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="user-box">
                                <input type="text" name="reset_token" class="form-control" required="">
                                <label>Reset Token</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <input type="submit" name="register" class="btn btn-primary submit-button" value="Reset Password">
                        </div>
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
