<?php
session_start();

// Predefined admin credentials (you can replace these with values from a database)
$adminUsername = "admin";
$adminPassword = "password123"; // For simplicity; use hashed passwords in production

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Validate credentials
    if ($username === $adminUsername && $password === $adminPassword) {
        // Set session variable to indicate that the admin is logged in
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_username'] = $username; // Store the username in session

        // Redirect to the admin dashboard or any page
        header('Location: /admin/index.html');
        exit();
    } else {
        // Invalid credentials
        echo "Invalid username or password.";
    }
}
?>
