<?php
session_start();
include_once("config.php");

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve the submitted data
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];

    // Perform basic validation
    $errors = array();

    if (empty($name)) {
        $errors[] = "Name is required.";
    }

    if (empty($email)) {
        $errors[] = "Email is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }

    if (empty($password)) {
        $errors[] = "Password is required.";
    } elseif ($password !== $confirmPassword) {
        $errors[] = "Passwords do not match.";
    }

    // Proceed with registration if there are no validation errors
    if (empty($errors)) {
        // Check if the email already exists in the database
        $checkQuery = "SELECT * FROM teachers WHERE email = '$email'";
        $result = $conn->query($checkQuery);

        if ($result && $result->num_rows > 0) {
            $errors[] = "Email already exists.";
        } else {
            // Insert the new teacher record into the database
            $insertQuery = "INSERT INTO teachers (name, email, password) VALUES ('$name', '$email', '$password')";
            if ($conn->query($insertQuery)) {
                $_SESSION['username'] = $email;
                header('Location: dashboard.php');
                exit();
            } else {
                $errors[] = "Error occurred during registration.";
            }
        }
    }

    // Close the database connection
    $conn->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Registration Page</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style1.css">
    <style>
        .error {
            color: red;
            margin-bottom: 10px;
        }

        .registration-box {
            max-width: 400px;
            margin: 0 auto;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            background-color: #f8f8f8;
        }

        .registration-box h2 {
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
            .registration-box {
                max-width: 320px;
                margin: 0 auto;
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center align-items-center vh-100">
            <div class="col-lg-4 col-md-6 col-sm-8 col-10">
                <div class="registration-box">
                    <h2>Teacher's Registration</h2>
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
                                <input type="text" name="name" class="form-control" required="">
                                <label>Username</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="user-box">
                                <input type="email" name="email" class="form-control" required="">
                                <label>Email</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="user-box">
                                <input type="password" name="password" class="form-control" required="">
                                <label>Password</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="user-box">
                                <input type="password" name="confirm_password" class="form-control" required="">
                                <label>Confirm Password</label>
                            </div>
                        </div>
                        <input type="submit" class="btn btn-primary submit-button" value="Register">
                    </form>
                    <p class="login-link">
                        Already have an account? <a href="index.php">Login here</a>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
