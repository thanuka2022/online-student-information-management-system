<?php
include_once("config.php");
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $full_name = $_POST['full_name'];
    $name_initial = $_POST['name_initial'];
    $date_of_birth = $_POST['date_of_birth'];
    $permanent_address = $_POST['permanent_address'];
    $gender = $_POST['gender'];
    $photo = $_FILES['photo']['name'];
    $registered_date = $_POST['registered_date'];
    $guardian_name = $_POST['guardian_name'];
    $contact_no = $_POST['contact_no'];
    $guardian_address = $_POST['guardian_address'];
    $relation = $_POST['relation'];
    $user_id = $_SESSION['userid'];
    // Begin the transaction
    $conn->begin_transaction();

    try {
        // Prepare the INSERT statement for student details
        $studentQuery = "INSERT INTO students (full_name, name_with_initial, permanent_address, date_of_birth, gender, photo, registered_date ,user_id)
                         VALUES (?, ?, ?, ?, ?, ?, ? , ?)";

        // Prepare the statement
        $studentStmt = $conn->prepare($studentQuery);
        if (!$studentStmt) {
            throw new Exception($conn->error);
        }
        $studentStmt->bind_param("ssssssss", $full_name, $name_initial, $permanent_address, $date_of_birth, $gender, $photo, $registered_date, $user_id);

        // Execute the statement
        $studentStmt->execute();

        // Retrieve the auto-generated student ID
        $student_id = $studentStmt->insert_id;

        // Prepare the INSERT statement for guardian details
        $guardianQuery = "INSERT INTO guardians (student_id, guardian_name, contact_no, guardian_address, relation)
                          VALUES (?, ?, ?, ?, ?)";

        // Prepare the statement
        $guardianStmt = $conn->prepare($guardianQuery);
        if (!$guardianStmt) {
            throw new Exception($conn->error);
        }
        $guardianStmt->bind_param("issss", $student_id, $guardian_name, $contact_no, $guardian_address, $relation);

        // Execute the statement
        $guardianStmt->execute();

        // Move the uploaded photo to a desired directory
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES['photo']['name']);
        move_uploaded_file($_FILES['photo']['tmp_name'], $target_file);

        // Commit the transaction
        $conn->commit();

        // Redirect to a success page or display a success message
        echo "<script>alert('Student added successfully.');</script>";
        // You can redirect to a success page using the following code
        header("Location: student_list.php");
        // exit();
    } catch (Exception $e) {
        // Rollback the transaction
        $conn->rollback();

        // Handle the error
        echo "Error: " . $e->getMessage();
    }
}

// Close the database connection
$conn->close();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Add Student</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Include CSS and Bootstrap -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        /* Custom CSS styles */

        .form-frame {
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            background-color: #f8f9fa;
            margin: 20px;
        }

        .student-details-heading {
            text-align: center;
            margin-bottom: 20px;
        }

        .logo {
            width: 40px;
            height: 40px;
        }

        .form-control-navbar {
            height: 20px;
            width: 200px;
        }

        .main-footer {
            background-color: #f8f9fa;
            padding: 20px;
            text-align: center;
        }
    </style>
</head>
<body>
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
            </li>
            <li class="nav-item active">
                <a class="nav-link" href="add_student.php">Add Student</a>
            </li> 
            <li class="nav-item">
                <a class="nav-link" href="student_list.php">Student List</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="teachers_profile.php">Teacher's Profile</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="logout.php">Logout</a>
            </li>
        </ul>
    </div>
</nav>

<div class="container mt-4">
    <h2 class="text-center">Add Student</h2>
    <div class="form-frame">
        <form action="add_student.php" method="POST" enctype="multipart/form-data">
            <div class="row">
                <div class="col-md-4">
                    <h4 class="student-details-heading">Student Details</h4>
                    <div class="form-group">
                        <label for="full_name" class="text-primary">Student Full Name:</label>
                        <input type="text" class="form-control" id="full_name" name="full_name" required>
                    </div>
                    <div class="form-group">
                        <label for="name_initial" class="text-primary">Name with Initial:</label>
                        <input type="text" class="form-control" id="name_initial" name="name_initial" required>
                    </div>
                    <div class="form-group">
                        <label for="date_of_birth" class="text-primary">Date of Birth:</label>
                        <input type="date" class="form-control" id="date_of_birth" name="date_of_birth" required>
                    </div>
                    <div class="form-group">
                        <label for="photo" class="text-primary">Photo:</label>
                        <input type="file" class="form-control" id="photo" name="photo" accept="image/*" required>
                    </div>
                </div>
                <div class="col-md-4 mt-5">
                    <div class="form-group">
                        <label for="permanent_address" class="text-primary">Permanent Address:</label>
                        <textarea class="form-control" id="permanent_address" name="permanent_address" rows="3" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="gender" class="text-primary">Gender:</label>
                        <select class="form-control" id="gender" name="gender" required>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="registered_date" class="text-primary">Registered Date:</label>
                        <input type="date" class="form-control" id="registered_date" name="registered_date" required>
                    </div>
                </div>
                <div class="col-md-4">
                    <h4 class="student-details-heading">Guardian Details</h4>
                    <div class="form-group">
                        <label for="guardian_name" class="text-primary">Guardian Name:</label>
                        <input type="text" class="form-control" id="guardian_name" name="guardian_name" required>
                    </div>
                    <div class="form-group">
                        <label for="contact_no" class="text-primary">Contact No:</label>
                        <input type="text" class="form-control" id="contact_no" name="contact_no" required>
                    </div>
                    <div class="form-group">
                        <label for="guardian_address" class="text-primary">Guardian Address:</label>
                        <textarea class="form-control" id="guardian_address" name="guardian_address" rows="3" required></textarea>
                    </div>

                    <div class="form-group">
                        <label for="relation" class="text-primary">Relation:</label>
                        <select class="form-control" id="relation" name="relation" required>
                            <option value="">Select Relation</option>
                            <option value="Father">Father</option>
                            <option value="Mother">Mother</option>
                            <option value="Uncle">Uncle</option>
                            <option value="Aunt">Aunt</option>
                            <option value="Grand">Grand</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="text-right">
                <button type="submit" class="btn btn-primary">Submit</button>
            </div>
        </form>
    </div>
</div>

<div class="main-footer">
    <p>&copy; <?php echo date("Y"); ?> All rights reserved.</p>
</div>

<!-- Include Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

