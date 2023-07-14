<?php
include_once("config.php");

// Begin the transaction
$conn->begin_transaction();

try {

    // Get the count of all students
    $totalCountQuery = "SELECT COUNT(*) as total_count FROM students";
    $totalCountResult = $conn->query($totalCountQuery);

    // Check if the query was successful
    if ($totalCountResult) {
        $totalRow = $totalCountResult->fetch_assoc();
        $totalCount = $totalRow['total_count'];

        // Output the total student count
        // echo "Total Students: " . $totalCount;
    } else {
        throw new Exception($conn->error);
    }

    // Get the count of male students
    $maleCountQuery = "SELECT COUNT(*) as male_count FROM students WHERE gender = 'Male'";
    $maleResult = $conn->query($maleCountQuery);

    // Check if the query was successful
    if ($maleResult) {
        $maleRow = $maleResult->fetch_assoc();
        $maleCount = $maleRow['male_count'];

        // Output the male student count
        // echo "Total Male Students: " . $maleCount;
    } else {
        throw new Exception($conn->error);
    }

    // Get the count of female students
    $femaleCountQuery = "SELECT COUNT(*) as female_count FROM students WHERE gender = 'Female'";
    $femaleResult = $conn->query($femaleCountQuery);

    // Check if the query was successful
    if ($femaleResult) {
        $femaleRow = $femaleResult->fetch_assoc();
        $femaleCount = $femaleRow['female_count'];

        // Output the female student count
        // echo "Total Female Students: " . $femaleCount;
    } else {
        throw new Exception($conn->error);
    }

    // Get the teacher name
    $teacherNameQuery = "SELECT name FROM users WHERE user_id = '1'";
    $teacherResult = $conn->query($teacherNameQuery);

    // Check if the query was successful
    if ($teacherResult) {
        $teacherRow = $teacherResult->fetch_assoc();
        $teacherName = $teacherRow['name'];

        // Output the teacher name
        // echo "Teacher Name: " . $teacherName;
    } else {
        throw new Exception($conn->error);
    }

    // Commit the transaction
    $conn->commit();
} catch (Exception $e) {
    // Rollback the transaction
    $conn->rollback();

    // Handle the error
    echo "Error: " . $e->getMessage();
}

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Home</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Include CSS and Bootstrap -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.css">
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

        .card-img-top {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }

        #calendar {
            margin-top: 20px;
            max-width: 500px; /* Adjust the max-width as needed */
            margin-left: auto;
            margin-right: auto;
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
                <li class="nav-item active">
                    <a class="nav-link" href="dashboard.php">Home</a>
                </li>
                <li class="nav-item">
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
        <div class="row">
            <div class="col-md-6">
                <h2>Welcome to OSIMS</h2>
            </div>
            <div class="col-md-6 text-right">
                <h4>Hi <?php echo $teacherName; ?> !</h4>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <div class="card">
                    <img src="images/total.png" class="card-img-top" alt="Card Image">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo $totalCount; ?></h5>
                        <p class="card-text">Total Students</p>
                        <a href="student_list.php" class="btn btn-primary">View More</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <img src="images/boy.png" class="card-img-top" alt="Card Image">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo $maleCount; ?></h5>
                        <p class="card-text">Total Male students</p>
                        <a href="student_list.php" class="btn btn-primary">View More</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <img src="images/girl.png" class="card-img-top" alt="Card Image">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo $femaleCount ?></h5>
                        <p class="card-text">Total female students</p>
                        <a href="student_list.php" class="btn btn-primary">View More</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div id="calendar"></div>
    <div class="main-footer">
        <footer>
            <strong>&copy; <script>document.write(new Date().getFullYear());</script></strong> All rights reserved.
        </footer>
    </div>
    <!-- Include Bootstrap JS and FullCalendar library -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#calendar').fullCalendar({
                // Add your calendar settings here
                // For example, you can set the events as an array of objects
                events: [
                    {
                        title: 'Event 1',
                        start: '2023-07-01',
                        end: '2023-07-03'
                    },
                    {
                        title: 'Event 2',
                        start: '2023-07-10',
                        end: '2023-07-12'
                    },
                    {
                        title: 'Event 3',
                        start: '2023-07-15',
                        end: '2023-07-16'
                    }
                    // Add more events as needed
                ]
            });
        });
    </script>
</body>
</html>
