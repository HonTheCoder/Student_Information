<?php
include 'db.php';
session_start();

// Fetch and sanitize the search input
$search = isset($_GET['search']) ? htmlspecialchars(trim($_GET['search'])) : '';

// Fetch student data with search functionality
$sql = "SELECT student_id, fullname, course, year_level, gender FROM students WHERE fullname LIKE ? OR course LIKE ? OR year_level LIKE ? OR gender LIKE ?";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("Database error: " . $conn->error);
}

$searchParam = '%' . $search . '%';
$stmt->bind_param('ssss', $searchParam, $searchParam, $searchParam, $searchParam);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student List</title>
    <link rel="stylesheet" href="css/studenttostudentlist.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <section class="dashboard">
        <div class="top">
            <i class="uil uil-bars sidebar-toggle"></i>
            <div class="search-box">
                <form method="GET" action="">
                    <input type="text" id="searchInput" placeholder="Search...">
                    <i class="fas fa-search"></i>
                </form>
            </div>
        </div>
        <div class="student-list">
            <h2>STUDENT LIST</h2>
            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Course</th>
                        <th>Year Level</th>
                        <th>Gender</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="studentTable">
                    <?php
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            $studentId = htmlspecialchars($row["student_id"]);  // Ensure ID is safe to output
                            echo "<tr data-name='" . htmlspecialchars(strtolower($row["fullname"])) . "' data-course='" . htmlspecialchars(strtolower($row["course"])) . "' data-year='" . htmlspecialchars(strtolower($row["year_level"])) . "' data-gender='" . htmlspecialchars(strtolower($row["gender"])) . "'>";
                            echo "<td>" . htmlspecialchars($row["fullname"]) . "</td>";
                            echo "<td>" . htmlspecialchars($row["course"]) . "</td>";
                            echo "<td>" . htmlspecialchars($row["year_level"]) . "</td>";
                            echo "<td>" . htmlspecialchars($row["gender"]) . "</td>";
                            echo "<td>
                                    <button class='view-btn' onclick=\"window.location.href='studentprofile.php?student_id=" . urlencode($studentId) . "'\">View</button>
                                  </td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='5'>No students found</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </section>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const searchInput = document.getElementById("searchInput"),
                studentTable = document.getElementById("studentTable").querySelectorAll("tr");

            // Disable Enter key
            searchInput.addEventListener("keydown", function (event) {
                if (event.key === "Enter") {
                    event.preventDefault();
                }
            });

            // Live search
            searchInput.addEventListener("input", function () {
                const query = searchInput.value.toLowerCase();
                studentTable.forEach((row) => {
                    const name = row.getAttribute("data-name");
                    const course = row.getAttribute("data-course");
                    const year = row.getAttribute("data-year");
                    const gender = row.getAttribute("data-gender");
                    row.style.display = name.includes(query) || course.includes(query) || year.includes(query) || gender.includes(query) ? "" : "none";
                });
            });
        });
    </script>
</body>
</html>