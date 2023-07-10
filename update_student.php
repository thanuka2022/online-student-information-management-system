<?php
include("config.php");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $studentId = $_POST["student_id"];
    $name = $_POST["updateName" . $studentId];
    $dob = $_POST["updateDOB" . $studentId];
    $gender = $_POST["updateGender" . $studentId];
    $address = $_POST["updateAddress" . $studentId];
    $registeredDate = $_POST["updateRegisteredDate" . $studentId];

    // Update student data in the database
    $sql = "UPDATE students SET name = '$name', dob = '$dob', gender = '$gender', address = '$address', registered_date = '$registeredDate' WHERE student_id = $studentId";
    $result = mysqli_query($conn, $sql);

    if ($result) {
        // Redirect the user back to the student list page or display a success message
        header("Location: student_list.php");
        exit();
    } else {
        // Handle the error, redirect to an error page, or display an error message
        echo "Error updating data.";
    }
} else {
    // Invalid request
    echo "Invalid request.";
}

// Close the database connection
mysqli_close($conn);
?>
