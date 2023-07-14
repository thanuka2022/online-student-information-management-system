<?php
include("config.php");

// Begin the transaction
$conn->begin_transaction();

try {
    // Handle form submissions for updating student details, guardian details, and photo
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Update Student Details
        foreach ($_POST as $key => $value) {
            if (strpos($key, 'updateStudentName') !== false) {
                $studentId = substr($key, strlen('updateStudentName'));
                $name = $_POST['updateStudentName' . $studentId];
                $dob = $_POST['updateStudentDOB' . $studentId];
                $gender = $_POST['updateStudentGender' . $studentId];
                $address = $_POST['updateStudentAddress' . $studentId];
                $registeredDate = $_POST['updateStudentRegisteredDate' . $studentId];

                // Update the student details in the database using prepared statement
                $updateQuery = "UPDATE students SET name_with_initial = ?, date_of_birth = ?, gender = ?, permanent_address = ?, registered_date = ? WHERE student_id = ?";
                $stmt = $conn->prepare($updateQuery);
                $stmt->bind_param("sssssi", $name, $dob, $gender, $address, $registeredDate, $studentId);
                $stmt->execute();

                // Check if the update was successful
                if ($stmt->affected_rows < 1) {
                    throw new Exception("Failed to update student details for ID: $studentId");
                }
            }
        }

        // Update Guardian Details
        foreach ($_POST as $key => $value) {
            if (strpos($key, 'updateGuardianName') !== false) {
                $studentId = substr($key, strlen('updateGuardianName'));
                $guardianName = $_POST['updateGuardianName' . $studentId];
                $guardianPhone = $_POST['updateGuardianPhone' . $studentId];
                $guardianAddress = $_POST['updateGuardianAddress' . $studentId];
                $guardianRelation = $_POST['updateGuardianRelation' . $studentId];

                // Update the guardian details in the database using prepared statement
                $updateQuery = "UPDATE guardians SET guardian_name = ?, contact_no = ?, guardian_address = ?, relation = ? WHERE student_id = ?";
                $stmt = $conn->prepare($updateQuery);
                $stmt->bind_param("ssssi", $guardianName, $guardianPhone, $guardianAddress, $guardianRelation, $studentId);
                $stmt->execute();

                // Check if the update was successful
                if ($stmt->affected_rows < 1) {
                    throw new Exception("Failed to update guardian details for ID: $studentId");
                }
            }
        }

        // Update Student Photo
        foreach ($_FILES as $key => $value) {
            if (strpos($key, 'updateImage') !== false) {
                $studentId = substr($key, strlen('updateImage'));
                $photo = $_FILES['updateImage' . $studentId]['name'];

                // Update the student photo in the database using prepared statement
                $updateQuery = "UPDATE students SET photo = ? WHERE student_id = ?";
                $stmt = $conn->prepare($updateQuery);
                $stmt->bind_param("si", $photo, $studentId);
                $stmt->execute();

                // Check if the update was successful
                if ($stmt->affected_rows < 1) {
                    throw new Exception("Failed to update student photo for ID: $studentId");
                }

                // Move the uploaded photo to a desired directory
                $target_dir = "uploads/";
                $target_file = $target_dir . basename($_FILES['updateImage' . $studentId]['name']);
                move_uploaded_file($_FILES['updateImage' . $studentId]['tmp_name'], $target_file);
            }
        }

        // Commit the transaction
        $conn->commit();

        // Set a flag indicating successful updates
        $updatesApplied = true;
    }
} catch (Exception $e) {
    // Rollback the transaction
    $conn->rollback();

    // Handle the error
    echo "Error: " . $e->getMessage();
}

// Retrieve student details from the database
$query = "SELECT students.*, guardians.*
          FROM students
          JOIN guardians ON students.student_id = guardians.student_id";

// Retrieve all students by default
$result = $conn->query($query);

if (!$result) {
    echo "Error retrieving student details: " . $conn->error;
}

// Process the retrieved data and populate the table
$students = $result->fetch_all(MYSQLI_ASSOC);

// Function to calculate age based on date of birth
function calculateAge($dateOfBirth) {
    $today = new DateTime();
    $birthDate = DateTime::createFromFormat('Y-m-d', $dateOfBirth);
    $age = $today->diff($birthDate)->y;
    return $age;
}

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Student List</title>
    <!-- Include CSS and Bootstrap -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/student_list.css">
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
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="add_student.php">Add Student</a>
                </li>
                <li class="nav-item active">
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
    <h2>Student List</h2>
    <div class="text-right mb-2">
        <a href="add_student.php" class="btn btn-primary">+ Add Student</a>
    </div>
    <div class="row">
        <div class="col-md-12 text-right">
            <!-- Add form element and search button -->
            <form class="form-inline" method="POST" id="searchForm">
                <div class="row">
                    <div class="col-md-3 mb-2">
                        <label for="searchStudentNo" class="form-label">Student No:</label>
                        <input type="text" class="form-control" name="searchStudentNo" placeholder="Enter student number">
                    </div>
                    <div class="col-md-3 mb-2">
                        <label for="searchGender" class="form-label ml-4">Gender:</label>
                        <select class="form-control" name="searchGender">
                            <option value="">All</option>
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div class="col-md-3 mb-2">
                        <label for="searchRegisteredDateFrom" class="form-label">Registered Date From:</label>
                        <input type="date" class="form-control" name="searchRegisteredDateFrom">
                    </div>
                    <div class="col-md-3 mb-2">
                        <label for="searchRegisteredDateTo" class="form-label">Registered Date To:</label>
                        <input type="date" class="form-control" name="searchRegisteredDateTo">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 mt-3 text-right">
                        <button type="submit" class="btn btn-primary ml-3" name="searchBtn">Search</button>
                        <button type="button" class="btn btn-secondary" onclick="resetForm()">Reset</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="table-frame" id="studentList">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Student No</th>
                    <th>Name</th>
                    <th>Gender</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($students as $student) : ?>
                    <tr>
                        <td><?php echo $student['student_id']; ?></td>
                        <td><?php echo $student['name_with_initial']; ?></td>
                        <td><?php echo $student['gender']; ?></td>
                        <td>
                            <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#viewModal<?php echo $student['student_id']; ?>">View</button>
                            <button type="button" class="btn btn-warning btn-sm" data-toggle="modal" data-target="#updateModal<?php echo $student['student_id']; ?>">Update</button>
                            <a href="#" class="btn btn-danger btn-sm" onclick="confirmDelete(<?php echo $student['student_id']; ?>)">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php if (isset($updatesApplied) && $updatesApplied && !isset($_POST['searchBtn']) && !isset($_POST['resetBtn'])) : ?>
        <script>
            if (window.history.replaceState) {
                window.history.replaceState(null, null, window.location.href);
            }
            alert('Updates applied successfully.');
        </script>
    <?php endif; ?>

<!-- View Modal -->
<?php foreach ($students as $student) : ?>
<div class="modal fade" id="viewModal<?php echo $student['student_id']; ?>" tabindex="-1" role="dialog" aria-labelledby="viewModalLabel<?php echo $student['student_id']; ?>" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewModalLabel1">View Student Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-4 text-center">
                            <img src="uploads/<?php echo $student['photo']; ?>" alt="Student Image" class="img-fluid mb-3">
                        </div>
                        <div class="col-md-8">
                            <div class="row mb-2">
                                <div class="col-md-4 text-right"><strong>Student No:</strong></div>
                                <div class="col-md-8"><?php echo $student['student_id']; ?></div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-md-4 text-right"><strong>Name:</strong></div>
                                <div class="col-md-8"><?php echo $student['name_with_initial']; ?></div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-md-4 text-right"><strong>Date Of Birth :</strong></div>
                                <div class="col-md-8"><?php echo $student['date_of_birth']; ?></div>
                                <!-- <div class="col-md-8"><?php echo calculateAge($student['date_of_birth']); ?></div> -->
                                
                            </div>
                            <div class="row mb-2">
                                <div class="col-md-4 text-right"><strong>Gender:</strong></div>
                                <div class="col-md-8"><?php echo $student['gender']; ?></div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-md-4 text-right"><strong>Guardian's Name:</strong></div>
                                <div class="col-md-8"><?php echo $student['guardian_name']; ?></div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-md-4 text-right"><strong>Guardian's Address:</strong></div>
                                <div class="col-md-8"><?php echo $student['contact_no']; ?></div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-md-4 text-right"><strong>Relationship:</strong></div>
                                <div class="col-md-8"><?php echo $student['relation']; ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<?php endforeach; ?>

<!-- Update Modal -->
<?php foreach ($students as $student) : ?>
<div class="modal fade" id="updateModal<?php echo $student['student_id']; ?>" tabindex="-1" role="dialog" aria-labelledby="updateModalLabel<?php echo $student['student_id']; ?>" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="updateModalLabel<?php echo $student['student_id']; ?>">Update Student Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <ul class="nav nav-tabs" id="myTab<?php echo $student['student_id']; ?>" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="student-tab<?php echo $student['student_id']; ?>" data-toggle="tab" href="#student<?php echo $student['student_id']; ?>" role="tab" aria-controls="student<?php echo $student['student_id']; ?>" aria-selected="true">Student Details</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="guardian-tab<?php echo $student['student_id']; ?>" data-toggle="tab" href="#guardian<?php echo $student['student_id']; ?>" role="tab" aria-controls="guardian<?php echo $student['student_id']; ?>" aria-selected="false">Guardian Details</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="image-tab<?php echo $student['student_id']; ?>" data-toggle="tab" href="#image<?php echo $student['student_id']; ?>" role="tab" aria-controls="image<?php echo $student['student_id']; ?>" aria-selected="false">Update Image</a>
                    </li>
                </ul>
                <div class="tab-content mt-2" id="myTabContent<?php echo $student['student_id']; ?>">

                    <!-- Student Details Tab -->
                    <div class="tab-pane fade show active" id="student<?php echo $student['student_id']; ?>" role="tabpanel" aria-labelledby="student-tab<?php echo $student['student_id']; ?>">
                        <form method="POST" enctype="multipart/form-data" id="updateForm<?php echo $student['student_id']; ?>">
                            <div class="form-group">
                                <label for="updateName<?php echo $student['student_id']; ?>">Name</label>
                                <input type="text" class="form-control" name="updateStudentName<?php echo $student['student_id']; ?>" id="updateName<?php echo $student['student_id']; ?>" placeholder="Enter name">
                            </div>
                            <div class="form-group">
                                <label for="updateDOB<?php echo $student['student_id']; ?>">Date of Birth</label>
                                <input type="date" class="form-control" name="updateStudentDOB<?php echo $student['student_id']; ?>" id="updateDOB<?php echo $student['student_id']; ?>">
                            </div>
                            <div class="form-group">
                                <label for="updateGender<?php echo $student['student_id']; ?>">Gender</label>
                                <select class="form-control" name="updateStudentGender<?php echo $student['student_id']; ?>" id="updateGender<?php echo $student['student_id']; ?>">
                                    <option value="">Select Gender</option>
                                    <option value="male">Male</option>
                                    <option value="female">Female</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="updateAddress<?php echo $student['student_id']; ?>">Permanent Address</label>
                                <textarea class="form-control" name="updateStudentAddress<?php echo $student['student_id']; ?>" id="updateAddress<?php echo $student['student_id']; ?>" placeholder="Enter permanent address"></textarea>
                            </div>
                            <div class="form-group">
                                <label for="updateRegisteredDate<?php echo $student['student_id']; ?>">Registered Date</label>
                                <input type="date" class="form-control" name="updateStudentRegisteredDate<?php echo $student['student_id']; ?>" id="updateRegisteredDate<?php echo $student['student_id']; ?>">
                            </div>
                            <!-- Add more student details fields as needed -->
                            <div class="text-right">
                            <button type="submit" name="submitStudent<?php echo $student['student_id']; ?>" class="btn btn-primary">Update</button>
                            </div>
                        </form>
                    </div>
                    <!-- Guardian Details Tab -->
                    <div class="tab-pane fade" id="guardian<?php echo $student['student_id']; ?>" role="tabpanel" aria-labelledby="guardian-tab<?php echo $student['student_id']; ?>">
                        <form method="POST" enctype="multipart/form-data" id="updateGuardianForm<?php echo $student['student_id']; ?>">
                            <div class="form-group">
                                <label for="updateGuardianName<?php echo $student['student_id']; ?>">Guardian Name</label>
                                <input type="text" class="form-control" name="updateGuardianName<?php echo $student['student_id']; ?>" id="updateGuardianName<?php echo $student['student_id']; ?>" placeholder="Enter guardian name">
                            </div>
                            <div class="form-group">
                                <label for="updateGuardianPhone<?php echo $student['student_id']; ?>">Guardian Phone</label>
                                <input type="text" class="form-control" name="updateGuardianPhone<?php echo $student['student_id']; ?>" id="updateGuardianPhone<?php echo $student['student_id']; ?>" placeholder="Enter guardian phone">
                            </div>
                            <div class="form-group">
                                <label for="updateGuardianAddress<?php echo $student['student_id']; ?>">Guardian Address</label>
                                <textarea class="form-control" name="updateGuardianAddress<?php echo $student['student_id']; ?>" id="updateGuardianAddress<?php echo $student['student_id']; ?>" placeholder="Enter guardian address"></textarea>
                            </div>
                            <div class="form-group">
                                <label for="updateGuardianRelation<?php echo $student['student_id']; ?>">Relation</label>
                                <input type="text" class="form-control"  name="updateGuardianRelation<?php echo $student['student_id']; ?>" id="updateGuardianRelation<?php echo $student['student_id']; ?>" placeholder="Enter guardian relation">
                            </div>
                            <!-- Add more guardian details fields as needed -->
                            <div class="text-right">
                            <button type="submit" name="submitGuardian<?php echo $student['student_id']; ?>" class="btn btn-primary">Update</button>
                            </div>
                        </form>
                    </div>
                    <!-- Update Image Tab -->
                    <div class="tab-pane fade" id="image<?php echo $student['student_id']; ?>" role="tabpanel" aria-labelledby="image-tab<?php echo $student['student_id']; ?>">
                        <form method="POST" enctype="multipart/form-data" id="updateImageForm<?php echo $student['student_id']; ?>">
                            <div class="form-group">
                                <label for="updateImage<?php echo $student['student_id']; ?>">Update Image</label>
                                <input type="file" name="updateImage<?php echo $student['student_id']; ?>" class="form-control-file" id="updateImage<?php echo $student['student_id']; ?>">
                            </div>
                            <div class="text-right">
                                <button type="submit" name="submitImage<?php echo $student['student_id']; ?>" class="btn btn-primary">Update</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<?php endforeach; ?>

    <div class="main-footer">
        <footer>
            <strong>&copy; <script>document.write(new Date().getFullYear());</script></strong> All rights reserved.
        </footer>
    </div>

    <script>
            function confirmDelete(studentId) {
                if (confirm("Are you sure you want to delete this student?")) {
                    // Redirect to delete_student.php with the student ID as a parameter
                    window.location.href = "student_list.php?studentId=" + studentId;
                }
            }
    </script>

<!-- Include jQuery library -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
$(document).ready(function() {
    $('#searchForm').submit(function(e) {
        e.preventDefault();

        // Get the form data
        var formData = $(this).serialize();

        // Send the AJAX request
        $.ajax({
            type: 'POST',
            url: 'search.php',
            data: formData,
            success: function(response) {
                // Update the content of the studentList div with the retrieved search results
                $('#studentList').html(response);
            },
            error: function(xhr, status, error) {
                // Handle the error if the AJAX request fails
                console.error(error);
            }
        });
    });

    
});

function resetForm() {
    // Reset the form fields
    document.getElementById("searchForm").reset();

    // Refresh the page
    window.location.reload();
}

</script>

<!-- Include Bootstrap JS and jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script>

</body>
</html>


