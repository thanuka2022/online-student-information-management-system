<?php
include("config.php");

// Get the search criteria from the form submission
$searchStudentNo = $_POST['searchStudentNo'] ?? '';
$searchGender = $_POST['searchGender'] ?? '';
$searchRegisteredDateFrom = $_POST['searchRegisteredDateFrom'] ?? '';
$searchRegisteredDateTo = $_POST['searchRegisteredDateTo'] ?? '';

// Prepare the search query
$searchQuery = "SELECT students.*, guardians.*
                FROM students
                JOIN guardians ON students.student_id = guardians.student_id
                WHERE 1=1";

// Build the search query dynamically based on the provided search criteria
if (!empty($searchStudentNo)) {
    $searchQuery .= " AND students.student_id = '$searchStudentNo'";
}

if (!empty($searchGender)) {
    $searchQuery .= " AND students.gender = '$searchGender'";
}

if (!empty($searchRegisteredDateFrom) && !empty($searchRegisteredDateTo)) {
    $searchQuery .= " AND students.registered_date BETWEEN '$searchRegisteredDateFrom' AND '$searchRegisteredDateTo'";
}

// Execute the search query
$searchResult = $conn->query($searchQuery);

if ($searchResult) {
    $students = $searchResult->fetch_all(MYSQLI_ASSOC);

    if (!empty($students)) {
        echo '<table class="table table-striped">
                <thead>
                    <tr>
                        <th>Student No</th>
                        <th>Name</th>
                        <th>Gender</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>';

        foreach ($students as $student) {
            echo '<tr>
                    <td>' . $student['student_id'] . '</td>
                    <td>' . $student['name_with_initial'] . '</td>
                    <td>' . $student['gender'] . '</td>
                    <td>
                        <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#viewModal' . $student['student_id'] . '">View</button>
                        <button type="button" class="btn btn-warning btn-sm" data-toggle="modal" data-target="#updateModal' . $student['student_id'] . '">Update</button>
                        <a href="#" class="btn btn-danger btn-sm" onclick="confirmDelete(' . $student['student_id'] . ')">Delete</a>
                    </td>
                </tr>';
        }

        echo '</tbody>
            </table>';
    } else {
        echo '<p>No results found.</p>';
    }
} else {
    echo 'Error executing search query: ' . $conn->error;
}

// Close the database connection
$conn->close();
?>
