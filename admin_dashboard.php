<?php
include 'db.php';
session_start();

// Secure session handling
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin_login.php");
    exit;
}

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
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="css/admin_dashboard.css" />
    <link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.0/css/line.css" />
    <title>Admin Dashboard Panel</title>
</head>
<body>
    <audio id="loginMusic" src="msc/admin.mp3" type="audio/mpeg" autoplay volume="2.0"></audio>
    <nav>
        <div class="logo-name">
            <div class="logo-image">
                <img src="img/admin.png" alt="" />
            </div>
            <span class="logo_name">Admin</span>
        </div>
        <div class="menu-items">
            <ul class="nav-links">
                <li>
                    <a href="contacts.php">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24">
                            <path d="M6.62 10.79a15.053 15.053 0 0 0 6.59 6.59l2.2-2.2a1.003 1.003 0 0 1 1.11-.27c1.12.37 2.33.57 3.58.57.55 0 1 .45 1 1v3.5c0 .55-.45 1-1 1C10.85 22 2 13.15 2 3.5 2 2.95 2.45 2.5 3 2.5H6.5c.55 0 1 .45 1 1 0 1.25.2 2.46.57 3.58.11.36.03.76-.27 1.11l-2.2 2.2z" fill="#7D8590"></path>
                        </svg>
                        <span class="link-name">Contacts</span>
                    </a>
                </li>
            </ul>
            <ul class="logout-mode">
                <li>
                    <a href="loadtoindex.php">
                        <i class="uil uil-signout"></i>
                        <span class="link-name">Logout</span>
                    </a>
                </li>
                <li class="mode">
                    <a href="#">
                        <i class="uil uil-moon"></i>
                        <span class="link-name">Dark Mode</span>
                    </a>
                    <div class="mode-toggle">
                        <span class="switch"></span>
                    </div>
                </li>
            </ul>
        </div>
    </nav>
    <section class="dashboard">
        <div class="top">
            <i class="uil uil-bars sidebar-toggle"></i>
            <div class="search-box">
                <form method="GET" action="">
                    <i class="uil uil-search"></i>
                    <input type="text" id="searchInput" name="search" placeholder="Search here..." value="<?php echo htmlspecialchars($search); ?>" />
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
                        echo "<tr data-name='" . strtolower(htmlspecialchars($row["fullname"])) . "' data-course='" . strtolower(htmlspecialchars($row["course"])) . "' data-year='" . strtolower(htmlspecialchars($row["year_level"])) . "' data-gender='" . strtolower(htmlspecialchars($row["gender"])) . "'>";
                        echo "<td>" . htmlspecialchars($row["fullname"]) . "</td>";
                        echo "<td>" . htmlspecialchars($row["course"]) . "</td>";
                        echo "<td>" . htmlspecialchars($row["year_level"]) . "</td>";
                        echo "<td>" . htmlspecialchars($row["gender"]) . "</td>";
                        echo "<td>
                                <button class='view-btn' onclick=\"window.location.href='studentprofile.php?student_id=" . urlencode($studentId) . "'\">View</button>
                                <button class='edit-btn' onclick=\"window.location.href='edit_student.php?student_id=" . urlencode($studentId) . "'\">Edit</button>
                                <button class='delete-btn' onclick=\"confirmDelete('" . urlencode($studentId) . "')\">Delete</button>
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
            const body = document.querySelector("body"),
                modeToggle = body.querySelector(".mode-toggle"),
                sidebar = body.querySelector("nav"),
                sidebarToggle = body.querySelector(".sidebar-toggle"),
                music = document.getElementById("loginMusic"),
                searchInput = document.getElementById("searchInput"),
                studentTable = document.getElementById("studentTable").querySelectorAll("tr");

            // Dark mode
            let getMode = localStorage.getItem("mode");
            if (getMode && getMode === "dark") {
                body.classList.toggle("dark");
            }
            modeToggle.addEventListener("click", () => {
                body.classList.toggle("dark");
                localStorage.setItem("mode", body.classList.contains("dark") ? "dark" : "light");
            });

            // Sidebar toggle
            let getStatus = localStorage.getItem("status");
            if (getStatus && getStatus === "close") {
                sidebar.classList.toggle("close");
            }
            sidebarToggle.addEventListener("click", () => {
                sidebar.classList.toggle("close");
                localStorage.setItem("status", sidebar.classList.contains("close") ? "close" : "open");
            });

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

            // Audio playback
            const searchQuery = new URLSearchParams(window.location.search).get("search");
            if (!sessionStorage.getItem("musicPlayed")) {
                if (!searchQuery) {
                    music.play().catch((error) => {
                        console.error("Autoplay was prevented:", error);
                    });
                    sessionStorage.setItem("musicPlayed", "true");
                }
            }
        });

        function confirmDelete(studentId) {
            if (confirm("Are you sure you want to delete this student?")) {
                window.location.href = 'delete_student.php?student_id=' + studentId;
            }
        }
    </script>
</body>
</html>
