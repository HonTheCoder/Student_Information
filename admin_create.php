<?php
include_once "db.php"; // Ensure this file contains your database connection setup

// Define the admin credentials
$username = 'admin1'; // Replace with your desired username
$password = 'admin12345'; // Replace with your desired password

// Hash the password
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

try {
    // Prepare the SQL statement
    $stmt = $conn->prepare("INSERT INTO admin_users (admin_username, admin_password) VALUES (?, ?)");

    // Bind parameters
    $stmt->bind_param("ss", $username, $hashedPassword);

    // Execute the statement
    if ($stmt->execute()) {
        echo "Admin user created successfully.";
    } else {
        echo "Error: " . $stmt->error;
    }

    // Close the statement
    $stmt->close();
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}

// Close the connection
$conn->close();
?>