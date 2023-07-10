<?php
include_once("config.php");

$errors = []; // Array to store validation errors

// Retrieve existing values from the database
$query = "SELECT name, subject, experience, telephone_no, photo FROM users WHERE user_id = 1"; // Assuming the teacher's ID is 1
$result = $conn->query($query);

if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $existingName = $row['name'];
    $existingSubject = $row['subject'];
    $existingExperience = $row['experience'];
    $existingTelephone = $row['telephone_no'];
    $existingPhoto = $row['photo'];
} else {
    // Handle the case when no teacher record is found
    echo "Teacher record not found.";
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $name = $_POST['name'];
    $subject = $_POST['subject'];
    $experience = $_POST['experience'];
    $telephone = $_POST['telephone'];
    $email = $_POST['email'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $photo = $_FILES['photo']['name'];

    // Validate form data
    if (empty($name)) {
        $errors['name'] = "Name is required";
    }

    if (empty($subject)) {
        $errors['subject'] = "Subject is required";
    }

    if (!is_numeric($experience)) {
        $errors['experience'] = "Experience must be a number";
    }

    if (strlen($telephone) > 12) {
        $errors['telephone'] = "Telephone number exceeds maximum length";
    }

    // ... validate other fields ...

    // Proceed if there are no validation errors
    if (empty($errors)) {
        // Prepare the UPDATE statement
        $updateQuery = "UPDATE users SET username = ?, password = ?, email = ?, name = ?, subject = ?, experience = ?, telephone_no = ?, photo = ? WHERE user_id = 1"; // Assuming the teacher's ID is 1

        // Begin the transaction
        $conn->begin_transaction();

        try {
            // Prepare the statement
            $stmt = $conn->prepare($updateQuery);
            if (!$stmt) {
                throw new Exception($conn->error);
            }

            // Bind the parameters
            $stmt->bind_param("ssssssss", $username, $password, $email, $name, $subject, $experience, $telephone, $photo);

            // Execute the statement
            $stmt->execute();

            // Move the uploaded photo to a desired directory
            $target_dir = "uploads/";
            $target_file = $target_dir . basename($_FILES['photo']['name']);
            move_uploaded_file($_FILES['photo']['tmp_name'], $target_file);

            // Commit the transaction
            $conn->commit();

            // Redirect to a success page or display a success message
            echo "<script>alert('Profile updated successfully.');
                window.location.href = window.location.href;</script>";
            // You can redirect to a success page using the following code
            // header("Location: success.php");
            // exit();
        } catch (Exception $e) {
            // Rollback the transaction
            $conn->rollback();

            // Handle the error
            echo "Error: " . $e->getMessage();
        }
    }
}

// Close the database connection
$conn->close();
?>


<!DOCTYPE html>
<html>
<head>
    <title>Teacher's Profile</title>
    <!-- Include CSS and Bootstrap -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .logo {
            width: 40px;
            height: 40px;
        }

        .navbar-brand {
            display: flex;
            align-items: center;
        }

        .form-control-navbar {
            height: 20px;
            width: 200px;
        }

        .frame-with-margin {
            margin: 20px;
        }

        .form-frame {
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .text-right {
            text-align: right;
        }

        .main-footer {
            background-color: #f8f9fa;
            padding: 20px;
            text-align: center;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <a class="navbar-brand" href="#">
            <img src="images/logo.png" class="logo">
            <span class="ml-2">OSIMS</span>
        </a>
        <form class="form-inline ml-auto">
            <div class="input-group input-group-sm">
                <input class="form-control form-control-navbar" type="search" placeholder="Search" aria-label="Search">
                <div class="input-group-append">
                    <button class="btn btn-navbar" type="submit">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>
        </form>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a class="nav-link" href="dashboard.php">Home</a>
                <li class="nav-item">
                    <a class="nav-link" href="add_student.php">Add Student</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="student_list.php">Student List</a>
                </li>
                <li class="nav-item active">
                    <a class="nav-link" href="teachers_profile.php">Teacher's Profile</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="logout.php">Logout</a>
                </li>
            </ul>
        </div>
    </nav>

    <div class="container mt-4">
        <h2>Teacher's Profile</h2>
        <div class="row">
            <div class="col-md-4">
                <div class="card">
                    <?php
                    $imagePath = "uploads/" . $existingPhoto;
                    ?>
                    <img src="<?php echo $imagePath; ?>" class="card-img-top" alt="Teacher's Photo" style="width: 100%; height: auto;">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo $existingName; ?></h5>
                        <p class="card-text">Subject: <?php echo $existingSubject; ?></p>
                        <p class="card-text">Experience: <?php echo $existingExperience; ?> years</p>
                    </div>
                </div>
            </div>

            <div class="col-md-8">
                <div class="frame-with-margin">
                    <div class="form-frame">
                        <h4>Update Profile</h4>
                        <form method="POST" enctype="multipart/form-data">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="name">Name</label>
                                        <input type="text" class="form-control" id="name" name="name" placeholder="Enter your name" >
                                    </div>
                                    <div class="form-group">
                                        <label for="subject">Subject</label>
                                        <input type="text" class="form-control" id="subject" name="subject" placeholder="Enter your subject" >
                                    </div>
                                    <div class="form-group">
                                        <label for="experience">Experience</label>
                                        <input type="text" class="form-control" id="experience" name="experience" placeholder="Enter your experience" >
                                    </div>
                                    <div class="form-group">
                                        <label for="telephone">Telephone</label>
                                        <input type="tel" class="form-control" id="telephone" name="telephone" placeholder="Enter your telephone number" >
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="email">Email</label>
                                        <input type="email" class="form-control" id="email" name="email" placeholder="Enter your email address">
                                    </div>
                                    <div class="form-group">
                                        <label for="username">Username</label>
                                        <input type="text" class="form-control" id="username" name="username" placeholder="Enter your username">
                                    </div>
                                    <div class="form-group">
                                        <label for="password">Password</label>
                                        <input type="password" class="form-control" id="password" name="password" placeholder="Enter your password">
                                    </div>
                                    <div class="form-group">
                                        <label for="photo">Photo</label>
                                        <input type="file" class="form-control" id="photo" name="photo" accept="image/*">
                                    </div>
                                </div>
                            </div>
                            <div class="text-right">
                                <button type="submit" class="btn btn-primary">Update</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="main-footer">
        <footer>
            <strong>&copy; <script>document.write(new Date().getFullYear());</script></strong> All rights reserved.
        </footer>
    </div>


    <!-- Include Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
