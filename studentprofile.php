<?php
// Database connection
include 'db.php';

// Get student_id from URL
$student_id = isset($_GET['student_id']) ? intval($_GET['student_id']) : null;

if ($student_id === null) {
    die("No student_id provided in the URL.");
}

// Query to fetch student data
$sql = "SELECT student_picture, fullname, course, year_level, age, dateofbirth, gender, bu_id, email, phone, address, parent_name, parent_contact, emergency_contact_name, emergency_contact_phone, nationality, religion, academic_year, sayings, dateinput FROM students WHERE student_id = ?";
$stmt = $conn->prepare($sql);

if ($stmt === false) {
    die('Prepare failed: ' . htmlspecialchars($conn->error));
}

$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
} else {
    die("No data found for student_id: " . htmlspecialchars($student_id));
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Profile</title>
    <link rel="stylesheet" href="css/studentprofile.css"> <!-- Link to your CSS file -->
</head>
<body>
    <div class="resume">
        <!-- Left-side -->
        <div class="left-side">
            <section class="profile">
                <div class='profile-img'>
                    <?php if (!empty($row['student_picture'])): ?>
                        <img src="data:image/jpeg;base64,<?= base64_encode($row['student_picture']) ?>" alt="Profile Photo">
                    <?php else: ?>
                        <img src="path/to/placeholder.jpg" alt="Profile Photo">
                    <?php endif; ?>
                </div>
                <h1 class='name'><?= htmlspecialchars($row['fullname']) ?></h1>
                <p class='title'>Course: <?= htmlspecialchars($row['course']) ?> - Year Level: <?= htmlspecialchars($row['year_level']) ?></p>
            </section>
        </div>

        <!-- Right-side -->
        <div class="right-side">

            <section class="student-info padding-top-bg">
                <h1 class="heading-primary-black">Student Information</h1>
                <div class='student-details'>
                    <!-- Student details table -->
                    <div class='column'>
                        <table>
                            <tr><th>Full Name:</th><td><?= htmlspecialchars($row['fullname']) ?></td></tr>
                            <tr><th>Age:</th><td><?= htmlspecialchars($row['age']) ?></td></tr>
                            <tr><th>Date of Birth:</th><td><?= htmlspecialchars($row['dateofbirth']) ?></td></tr>
                            <tr><th>Gender:</th><td><?= htmlspecialchars($row['gender']) ?></td></tr>
                            <tr><th>BU ID:</th><td><?= htmlspecialchars($row['bu_id']) ?></td></tr>
                            <tr><th>Course:</th><td><?= htmlspecialchars($row['course']) ?></td></tr>
                            <tr><th>Year Level:</th><td><?= htmlspecialchars($row['year_level']) ?></td></tr>
                            <tr><th>Email:</th><td><?= htmlspecialchars($row['email']) ?></td></tr>
                            <tr><th>Phone:</th><td><?= htmlspecialchars($row['phone']) ?></td></tr>
                            <tr><th>Address:</th><td><?= htmlspecialchars($row['address']) ?></td></tr>
                        </table>
                    </div>

                    <div class='column'>
                        <table>
                            <tr><th>Parent Name:</th><td><?= htmlspecialchars($row['parent_name']) ?></td></tr>
                            <tr><th>Parent Contact:</th><td><?= htmlspecialchars($row['parent_contact']) ?></td></tr>
                            <tr><th>Emergency Contact Name:</th><td><?= htmlspecialchars($row['emergency_contact_name']) ?></td></tr>
                            <tr><th>Emergency Contact Phone:</th><td><?= htmlspecialchars($row['emergency_contact_phone']) ?></td></tr>
                            <tr><th>Nationality:</th><td><?= htmlspecialchars($row['nationality']) ?></td></tr>
                            <tr><th>Religion:</th><td><?= htmlspecialchars($row['religion']) ?></td></tr>
                            <tr><th>Academic Year:</th><td><?= htmlspecialchars($row['academic_year']) ?></td></tr>
                            <tr><th>Sayings:</th><td><?= htmlspecialchars($row['sayings']) ?></td></tr>
                        </table>
                    </div>
                </div>
            </section>
                
            
            </form>
        </div>
    </div>
</body>
</html>
