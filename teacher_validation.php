<?php

function validateForm($formData)
{
    $errors = [];

    // Validate name
    if (empty($formData['name'])) {
        $errors['name'] = "Name is required";
    }

    // Validate subject
    if (empty($formData['subject'])) {
        $errors['subject'] = "Subject is required";
    }

    // Validate experience
    if (empty($formData['experience'])) {
        $errors['experience'] = "Experience is required";
    } elseif (!is_numeric($formData['experience'])) {
        $errors['experience'] = "Experience must be a number";
    }

    // Validate telephone
    if (empty($formData['telephone'])) {
        $errors['telephone'] = "Telephone is required";
    } elseif (strlen($formData['telephone']) > 12) {
        $errors['telephone'] = "Telephone number cannot exceed 12 digits";
    }

    // Validate email
    if (empty($formData['email'])) {
        $errors['email'] = "Email is required";
    } elseif (!filter_var($formData['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Invalid email format";
    }

    // Validate username
    if (empty($formData['username'])) {
        $errors['username'] = "Username is required";
    }

    // Validate password
    if (empty($formData['password'])) {
        $errors['password'] = "Password is required";
    }

    // Return the validation errors, if any
    return $errors;
}

?>
