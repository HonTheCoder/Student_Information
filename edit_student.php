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

    // Fetch student data
    $sql = "SELECT fullname, age, dateofbirth, gender, bu_id, course, year_level, email, phone, address, parent_name, parent_contact, emergency_contact_name, emergency_contact_phone, nationality, religion, academic_year, sayings FROM students WHERE student_id = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param('s', $studentId);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $student = $result->fetch_assoc();
        } else {
            echo "No student found with the given ID.";
            exit;
        }

        $stmt->close();
    } else {
        echo "Database error: " . $conn->error;
        exit;
    }
} else {
    echo "Invalid request.";
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullname = $_POST['fullname'];
    $age = $_POST['age'];
    $gender = $_POST['gender'];
    $bu_id = $_POST['bu_id'];
    $course = $_POST['course'];
    $year_level = $_POST['year_level'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $parent_name = $_POST['parent_name'];
    $parent_contact = $_POST['parent_contact'];
    $emergency_contact_name = $_POST['emergency_contact_name'];
    $emergency_contact_phone = $_POST['emergency_contact_phone'];
    $nationality = $_POST['nationality'];
    $religion = $_POST['religion'];
    $academic_year = $_POST['academic_year'];
    $sayings = $_POST['sayings'];

    // Update student data
    $sql = "UPDATE students SET fullname = ?, age = ?, gender = ?, bu_id = ?, course = ?, year_level = ?, email = ?, phone = ?, address = ?, parent_name = ?, parent_contact = ?, emergency_contact_name = ?, emergency_contact_phone = ?, nationality = ?, religion = ?, academic_year = ?, sayings = ? WHERE student_id = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param('sissssssssssssssss', $fullname, $age, $gender, $bu_id, $course, $year_level, $email, $phone, $address, $parent_name, $parent_contact, $emergency_contact_name, $emergency_contact_phone, $nationality, $religion, $academic_year, $sayings, $studentId);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            echo "Student updated successfully.";
        } else {
            echo "No changes made.";
        }

        $stmt->close();
    } else {
        echo "Database error: " . $conn->error;
    }

    // Redirect back to the admin dashboard
    header("Location: admin_dashboard.php");
    exit;
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Student</title>
    <link rel="stylesheet" href="css/admin_dashboard.css">
    <link rel="stylesheet" href="css/edit.css">
</head>
<body>
    <h2>Edit Student</h2>
    <form method="POST" action="">
        <label for="fullname">Full Name:</label>
        <input type="text" id="fullname" name="fullname" value="<?php echo htmlspecialchars($student['fullname']); ?>" required><br>

        <label for="age">Age:</label>
        <input type="number" id="age" name="age" value="<?php echo htmlspecialchars($student['age']); ?>" required><br>

        <label for="gender">Gender:</label>
        <input type="text" id="gender" name="gender" value="<?php echo htmlspecialchars($student['gender']); ?>" required><br>

        <label for="bu_id">BU ID:</label>
        <input type="number" id="bu_id" name="bu_id" value="<?php echo htmlspecialchars($student['bu_id']); ?>" required><br>

        <label for="course">Course:</label>
        <input type="text" id="course" name="course" value="<?php echo htmlspecialchars($student['course']); ?>" required><br>

        <label for="year_level">Year Level:</label>
        <input type="text" id="year_level" name="year_level" value="<?php echo htmlspecialchars($student['year_level']); ?>" required><br>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($student['email']); ?>" required><br>

        <label for="phone">Phone:</label>
        <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($student['phone']); ?>" required><br>

        <label for="address">Address:</label>
        <textarea id="address" name="address" required><?php echo htmlspecialchars($student['address']); ?></textarea><br>

        <label for="parent_name">Parent Name:</label>
        <input type="text" id="parent_name" name="parent_name" value="<?php echo htmlspecialchars($student['parent_name']); ?>" required><br>

        <label for="parent_contact">Parent Contact:</label>
        <input type="text" id="parent_contact" name="parent_contact" value="<?php echo htmlspecialchars($student['parent_contact']); ?>" required><br>

        <label for="emergency_contact_name">Emergency Contact Name:</label>
        <input type="text" id="emergency_contact_name" name="emergency_contact_name" value="<?php echo htmlspecialchars($student['emergency_contact_name']); ?>" required><br>

        <label for="emergency_contact_phone">Emergency Contact Phone:</label>
        <input type="text" id="emergency_contact_phone" name="emergency_contact_phone" value="<?php echo htmlspecialchars($student['emergency_contact_phone']); ?>" required><br>

        <label for="nationality">Nationality:</label>
        <input type="text" id="nationality" name="nationality" value="<?php echo htmlspecialchars($student['nationality']); ?>" required><br>

        <label for="religion">Religion:</label>
        <input type="text" id="religion" name="religion" value="<?php echo htmlspecialchars($student['religion']); ?>" required><br>

        <label for="academic_year">Academic Year:</label>
        <input type="text" id="academic_year" name="academic_year" value="<?php echo htmlspecialchars($student['academic_year']); ?>" required><br>

        <label for="sayings">Sayings:</label>
        <input type="text" id="sayings" name="sayings" value="<?php echo htmlspecialchars($student['sayings']); ?>" required><br>

        <button type="submit">Update</button>
    </form>
</body>
</html>