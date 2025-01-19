<?php
include 'db.php';
session_start();

// Check if the user is logged in as admin
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin_login.php");
    exit;
}

// Check if student_id is provided
if (isset($_GET['student_id'])) {
    $studentId = $_GET['student_id'];

    // Start a transaction
    $conn->begin_transaction();

    try {
        // Delete from registered_user table
        $sql = "DELETE FROM registered_user WHERE user_id = ?";
        $stmt = $conn->prepare($sql);

        if ($stmt) {
            $stmt->bind_param('i', $studentId);
            $stmt->execute();
            $stmt->close();
        } else {
            throw new Exception("Database error: " . $conn->error);
        }

        // Delete from students table
        $sql = "DELETE FROM students WHERE student_id = ?";
        $stmt = $conn->prepare($sql);

        if ($stmt) {
            $stmt->bind_param('i', $studentId);
            $stmt->execute();
            $stmt->close();
        } else {
            throw new Exception("Database error: " . $conn->error);
        }

        // Commit the transaction
        $conn->commit();
        echo "Student and user deleted successfully.";
    } catch (Exception $e) {
        // Rollback the transaction if any error occurs
        $conn->rollback();
        echo $e->getMessage();
    }
} else {
    echo "Invalid request.";
}

$conn->close();

// Redirect back to the admin dashboard
header("Location: admin_dashboard.php");
exit;
?>