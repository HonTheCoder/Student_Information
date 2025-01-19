<?php
include_once "db.php";

session_start();

// Check if the user is logged in, otherwise redirect to login page
if (!isset($_SESSION['username'])) {
    header("Location: register.php");
    exit();
}

// Handle logout request
if (isset($_GET['action']) && $_GET['action'] == 'logout') {
    session_unset();
    session_destroy();
    header("Location: index.php");
    exit();
}

$username = $_SESSION['username'];

// Check if the user has already submitted their information
$sql = "SELECT info_submitted FROM registered_user WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$stmt->bind_result($info_submitted);
$stmt->fetch();
$stmt->close();

$formDisabled = $info_submitted ? 'disabled' : '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Collect form data
    $fullname = $_POST['fullname'];
    $age = $_POST['age'];
    $dateofbirth = $_POST['dateofbirth'];
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
    $sayings = $_POST['sayings'];
    $academic_year = $_POST['academic_year'];

    // Handle the file upload (student picture)
    if ($_FILES['student_picture']['error'] == 0) {
        $student_picture = file_get_contents($_FILES['student_picture']['tmp_name']);
    } else {
        // If no file is uploaded, set the picture to NULL
        $student_picture = NULL;
    }

    // Prepare the SQL query to insert student data
    $sql = "INSERT INTO students (fullname, age, dateofbirth, gender, bu_id, course, year_level, email, phone, address, parent_name, parent_contact, emergency_contact_name, emergency_contact_phone, nationality, religion, academic_year, student_picture, sayings)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);

    // Include 'b' for the BLOB data type
    $stmt->bind_param("sisssssssssssssssbs", $fullname, $age, $dateofbirth, $gender, $bu_id, $course, $year_level, $email, $phone, $address, $parent_name, $parent_contact, $emergency_contact_name, $emergency_contact_phone, $nationality, $religion, $academic_year, $student_picture, $sayings);

    // Send the BLOB data separately
    $stmt->send_long_data(17, $student_picture);

    if ($stmt->execute()) {
        // After successful submission
        $sql = "UPDATE registered_user SET info_submitted = TRUE WHERE username = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->close();

        echo '<script>alert("Information submitted successfully!"); window.location.href = "student.php";</script>';
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/student.css">
    <title>Student Information</title>
</head>
<body>
    <label class="switch">
        <input type="checkbox" id="theme-toggle">
        <span class="slider"></span>
    </label>
    <h1>Student Information</h1>
    <?php if ($info_submitted): ?>
        <p>You have already submitted your information. You can view your information in the Student List page.</p>
    <?php endif; ?>
    <form id="studentForm" method="POST" enctype="multipart/form-data">
        <div class="form-page active" id="page1">
            <label for="fullname">Full Name:</label>
            <input type="text" name="fullname" class="input" required <?= $formDisabled ?>>

            <label for="age">Age:</label>
            <input type="number" name="age" class="input" required <?= $formDisabled ?>>

            <label for="dateofbirth">Date of Birth:</label>
            <input type="date" name="dateofbirth" class="input" required max="2024-12-31" <?= $formDisabled ?>>

            <label for="gender">Gender:</label>
            <select name="gender" class="input" required <?= $formDisabled ?>>
                <option value="Male">Male</option>
                <option value="Female">Female</option>
            </select>

            <label for="bu_id">Student ID:</label>
            <input type="text" name="bu_id" class="input" required <?= $formDisabled ?>>

            <label for="course">Course:</label>
            <input type="text" name="course" class="input" required <?= $formDisabled ?>>

            <label for="year_level">Year Level & Section:</label>
            <input type="text" name="year_level" class="input" required <?= $formDisabled ?>>

            <div class="btn-conteiner">
                <a class="btn-content nextbutton" href="javascript:void(0);" onclick="showNextPage(2)">
                    <span class="btn-title">NEXT</span>
                    <span class="icon-arrow">
                        <svg width="66px" height="43px" viewBox="0 0 66 43" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                            <g id="arrow" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                <path id="arrow-icon-one" d="M40.1543933,3.89485454 L43.9763149,0.139296592 C44.1708311,-0.0518420739 44.4826329,-0.0518571125 44.6771675,0.139262789 L65.6916134,20.7848311 C66.0855801,21.1718824 66.0911863,21.8050225 65.704135,22.1989893 C65.7000188,22.2031791 65.6958657,22.2073326 65.6916762,22.2114492 L44.677098,42.8607841 C44.4825957,43.0519059 44.1708242,43.0519358 43.9762853,42.8608513 L40.1545186,39.1069479 C39.9575152,38.9134427 39.9546793,38.5968729 40.1481845,38.3998695 C40.1502893,38.3977268 40.1524132,38.395603 40.1545562,38.3934985 L56.9937789,21.8567812 C57.1908028,21.6632968 57.193672,21.3467273 57.0001876,21.1497035 C56.9980647,21.1475418 56.9959223,21.1453995 56.9937605,21.1432767 L40.1545208,4.60825197 C39.9574869,4.41477773 39.9546013,4.09820839 40.1480756,3.90117456 C40.1501626,3.89904911 40.1522686,3.89694235 40.1543933,3.89485454 Z" fill="#FFFFFF"></path>
                                <path id="arrow-icon-two" d="M20.1543933,3.89485454 L23.9763149,0.139296592 C24.1708311,-0.0518420739 24.4826329,-0.0518571125 24.6771675,0.139262789 L45.6916134,20.7848311 C46.0855801,21.1718824 46.0911863,21.8050225 45.704135,22.1989893 C45.7000188,22.2031791 45.6958657,22.2073326 45.6916762,22.2114492 L24.677098,42.8607841 C24.4825957,43.0519059 24.1708242,43.0519358 23.9762853,42.8608513 L20.1545186,39.1069479 C19.9575152,38.9134427 19.9546793,38.5968729 20.1481845,38.3998695 C20.1502893,38.3977268 20.1524132,38.395603 20.1545562,38.3934985 L36.9937789,21.8567812 C37.1908028,21.6632968 37.193672,21.3467273 37.0001876,21.1497035 C36.9980647,21.1475418 36.9959223,21.1453995 36.9937605,21.1432767 L20.1545208,4.60825197 C19.9574869,4.41477773 19.9546013,4.09820839 20.1480756,3.90117456 C20.1501626,3.89904911 20.1522686,3.89694235 20.1543933,3.89485454 Z" fill="#FFFFFF"></path>
                                <path id="arrow-icon-three" d="M0.154393339,3.89485454 L3.97631488,0.139296592 C4.17083111,-0.0518420739 4.48263286,-0.0518571125 4.67716753,0.139262789 L25.6916134,20.7848311 C26.0855801,21.1718824 26.0911863,21.8050225 25.704135,22.1989893 C25.7000188,22.2031791 25.6958657,22.2073326 25.6916762,22.2114492 L4.67709797,42.8607841 C4.48259567,43.0519059 4.17082418,43.0519358 3.97628526,42.8608513 L0.154518591,39.1069479 C-0.0424848215,38.9134427 -0.0453206733,38.5968729 0.148184538,38.3998695 C0.150289256,38.3977268 0.152413239,38.395603 0.154556228,38.3934985 L16.9937789,21.8567812 C17.1908028,21.6632968 17.193672,21.3467273 17.0001876,21.1497035 C16.9980647,21.1475418 16.9959223,21.1453995 16.9937605,21.1432767 L0.15452076,4.60825197 C-0.0425130651,4.41477773 -0.0453986756,4.09820839 0.148075568,3.90117456 C0.150162624,3.89904911 0.152268631,3.89694235 0.154393339,3.89485454 Z" fill="#FFFFFF"></path>
                            </g>
                        </svg>
                    </span> 
                </a>
            </div>
        </div>
        <div class="form-page" id="page2">
            <button type="button" class="backbutton" onclick="showPreviousPage(1)">
                <svg height="16" width="16" xmlns="http://www.w3.org/2000/svg" version="1.1" viewBox="0 0 1024 1024">
                    <path d="M874.690416 495.52477c0 11.2973-9.168824 20.466124-20.466124 20.466124l-604.773963 0 188.083679 188.083679c7.992021 7.992021 7.992021 20.947078 0 28.939099-4.001127 3.990894-9.240455 5.996574-14.46955 5.996574-5.239328 0-10.478655-1.995447-14.479783-5.996574l-223.00912-223.00912c-3.837398-3.837398-5.996574-9.046027-5.996574-14.46955 0-5.433756 2.159176-10.632151 5.996574-14.46955l223.019353-223.029586c7.992021-7.992021 20.957311-7.992021 28.949332 0 7.992021 8.002254 7.992021 20.957311 0 28.949332l-188.073446 188.073446 604.753497 0C865.521592 475.058646 874.690416 484.217237 874.690416 495.52477z"></path>
                </svg>
                <span>Back</span>
            </button>
            <label for="email">Email Address:</label>
            <input type="email" name="email" class="input" required <?= $formDisabled ?>>

            <label for="phone">Phone Number:</label>
            <input type="text" name="phone" class="input" required <?= $formDisabled ?>>

            <label for="address">Address:</label>
            <input type="text" name="address" class="input" required <?= $formDisabled ?>>

            <label for="parent_name">Parent/Guardian Name:</label>
            <input type="text" name="parent_name" class="input" required <?= $formDisabled ?>>

            <label for="parent_contact">Parent/Guardian Contact:</label>
            <input type="text" name="parent_contact" class="input" required <?= $formDisabled ?>>

            <label for="emergency_contact_name">Emergency Contact Name:</label>
            <input type="text" name="emergency_contact_name" class="input" required <?= $formDisabled ?>>

            <div class="btn-conteiner">
                <a class="btn-content nextbutton" href="javascript:void(0);" onclick="showNextPage(3)">
                    <span class="btn-title">NEXT</span>
                    <span class="icon-arrow">
                        <svg width="66px" height="43px" viewBox="0 0 66 43" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                            <g id="arrow" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                <path id="arrow-icon-one" d="M40.1543933,3.89485454 L43.9763149,0.139296592 C44.1708311,-0.0518420739 44.4826329,-0.0518571125 44.6771675,0.139262789 L65.6916134,20.7848311 C66.0855801,21.1718824 66.0911863,21.8050225 65.704135,22.1989893 C65.7000188,22.2031791 65.6958657,22.2073326 65.6916762,22.2114492 L44.677098,42.8607841 C44.4825957,43.0519059 44.1708242,43.0519358 43.9762853,42.8608513 L40.1545186,39.1069479 C39.9575152,38.9134427 39.9546793,38.5968729 40.1481845,38.3998695 C40.1502893,38.3977268 40.1524132,38.395603 40.1545562,38.3934985 L56.9937789,21.8567812 C57.1908028,21.6632968 57.193672,21.3467273 57.0001876,21.1497035 C56.9980647,21.1475418 56.9959223,21.1453995 56.9937605,21.1432767 L40.1545208,4.60825197 C39.9574869,4.41477773 39.9546013,4.09820839 40.1480756,3.90117456 C40.1501626,3.89904911 40.1522686,3.89694235 40.1543933,3.89485454 Z" fill="#FFFFFF"></path>
                                <path id="arrow-icon-two" d="M20.1543933,3.89485454 L23.9763149,0.139296592 C24.1708311,-0.0518420739 24.4826329,-0.0518571125 24.6771675,0.139262789 L45.6916134,20.7848311 C46.0855801,21.1718824 46.0911863,21.8050225 45.704135,22.1989893 C45.7000188,22.2031791 45.6958657,22.2073326 45.6916762,22.2114492 L24.677098,42.8607841 C24.4825957,43.0519059 24.1708242,43.0519358 23.9762853,42.8608513 L20.1545186,39.1069479 C19.9575152,38.9134427 19.9546793,38.5968729 20.1481845,38.3998695 C20.1502893,38.3977268 20.1524132,38.395603 20.1545562,38.3934985 L36.9937789,21.8567812 C37.1908028,21.6632968 37.193672,21.3467273 37.0001876,21.1497035 C36.9980647,21.1475418 36.9959223,21.1453995 36.9937605,21.1432767 L20.1545208,4.60825197 C19.9574869,4.41477773 19.9546013,4.09820839 20.1480756,3.90117456 C20.1501626,3.89904911 20.1522686,3.89694235 20.1543933,3.89485454 Z" fill="#FFFFFF"></path>
                                <path id="arrow-icon-three" d="M0.154393339,3.89485454 L3.97631488,0.139296592 C4.17083111,-0.0518420739 4.48263286,-0.0518571125 4.67716753,0.139262789 L25.6916134,20.7848311 C26.0855801,21.1718824 26.0911863,21.8050225 25.704135,22.1989893 C25.7000188,22.2031791 25.6958657,22.2073326 25.6916762,22.2114492 L4.67709797,42.8607841 C4.48259567,43.0519059 4.17082418,43.0519358 3.97628526,42.8608513 L0.154518591,39.1069479 C-0.0424848215,38.9134427 -0.0453206733,38.5968729 0.148184538,38.3998695 C0.150289256,38.3977268 0.152413239,38.395603 0.154556228,38.3934985 L16.9937789,21.8567812 C17.1908028,21.6632968 17.193672,21.3467273 17.0001876,21.1497035 C16.9980647,21.1475418 16.9959223,21.1453995 16.9937605,21.1432767 L0.15452076,4.60825197 C-0.0425130651,4.41477773 -0.0453986756,4.09820839 0.148075568,3.90117456 C0.150162624,3.89904911 0.152268631,3.89694235 0.154393339,3.89485454 Z" fill="#FFFFFF"></path>
                            </g>
                        </svg>
                    </span> 
                </a>
            </div>
        </div>
        <div class="form-page" id="page3">
            <button type="button" class="backbutton" onclick="showPreviousPage(2)">
                <svg height="16" width="16" xmlns="http://www.w3.org/2000/svg" version="1.1" viewBox="0 0 1024 1024">
                    <path d="M874.690416 495.52477c0 11.2973-9.168824 20.466124-20.466124 20.466124l-604.773963 0 188.083679 188.083679c7.992021 7.992021 7.992021 20.947078 0 28.939099-4.001127 3.990894-9.240455 5.996574-14.46955 5.996574-5.239328 0-10.478655-1.995447-14.479783-5.996574l-223.00912-223.00912c-3.837398-3.837398-5.996574-9.046027-5.996574-14.46955 0-5.433756 2.159176-10.632151 5.996574-14.46955l223.019353-223.029586c7.992021-7.992021 20.957311-7.992021 28.949332 0 7.992021 8.002254 7.992021 20.957311 0 28.949332l-188.073446 188.073446 604.753497 0C865.521592 475.058646 874.690416 484.217237 874.690416 495.52477z"></path>
                </svg>
                <span>Back</span>
            </button>
            <label for="emergency_contact_phone">Emergency Contact Phone:</label>
            <input type="text" name="emergency_contact_phone" class="input" required <?= $formDisabled ?>>

            <label for="nationality">Nationality:</label>
            <input type="text" name="nationality" class="input" required <?= $formDisabled ?>>

            <label for="religion">Religion:</label>
            <input type="text" name="religion" class="input" <?= $formDisabled ?>>

            <label for="academic_year">Academic Year:</label>
            <input type="text" name="academic_year" class="input" required <?= $formDisabled ?>>

            <label for="student_picture">Upload Student Picture:</label>
            <input type="file" name="student_picture" class="input" accept="image/*" required <?= $formDisabled ?>>

            <label for="sayings">Sayings:</label>
            <input type="text" name="sayings" class="input" <?= $formDisabled ?>>

            <button type="submit" class="submit-button" <?= $formDisabled ?>>
                <span class="button_top">Submit</span>
            </button>
        </div>
    </form>
    <label class="hamburger">
        <input type="checkbox" id="menu-toggle">
        <svg viewBox="0 0 32 32">
            <path class="line line-top-bottom" d="M27 10 13 10C10.8 10 9 8.2 9 6 9 3.5 10.8 2 13 2 15.2 2 17 3.8 17 6L17 26C17 28.2 18.8 30 21 30 23.2 30 25 28.2 25 26 25 23.8 23.2 22 21 22L7 22"></path>
            <path class="line" d="M7 16 27 16"></path>
        </svg>
    </label>

    <div class="menu-items" id="menu-items">
        <button class="value" onclick="window.location.href='studenttostudentlist.php'">
            <svg data-name="Layer 2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16">
                <path d="m1.5 13v1a.5.5 0 0 0 .3379.4731 18.9718 18.9718 0 0 0 6.1621 1.0269 18.9629 18.9629 0 0 0 6.1621-1.0269.5.5 0 0 0 .3379-.4731v-1a6.5083 6.5083 0 0 0 -4.461-6.1676 3.5 3.5 0 1 0 -4.078 0 6.5083 6.5083 0 0 0 -4.461 6.1676zm4-9a2.5 2.5 0 1 1 2.5 2.5 2.5026 2.5026 0 0 1 -2.5-2.5zm2.5 3.5a5.5066 5.5066 0 0 1 5.5 5.5v.6392a18.08 18.08 0 0 1 -11 0v-.6392a5.5066 5.5066 0 0 1 5.5-5.5z" fill="#7D8590"></path>
            </svg>
            Student List
        </button>
        <button class="value" onclick="window.location.href='contacts.php'">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
            <path d="M6.62 10.79a15.053 15.053 0 0 0 6.59 6.59l2.2-2.2a1.003 1.003 0 0 1 1.11-.27c1.12.37 2.33.57 3.58.57.55 0 1 .45 1 1v3.5c0 .55-.45 1-1 1C10.85 22 2 13.15 2 3.5 2 2.95 2.45 2.5 3 2.5H6.5c.55 0 1 .45 1 1 0 1.25.2 2.46.57 3.58.11.36.03.76-.27 1.11l-2.2 2.2z" fill="#7D8590"></path>
            </svg>
            Contacts
        </button>
        <button class="value" onclick="window.location.href='loadtoindex.php?action=logout'">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                <path d="M10 2v2H4v16h6v2H4c-1.1 0-2-.9-2-2V4c0-1.1.9-2 2-2h6zm9.59 11L16 15.59V13h-5v-2h5V8.41L19.59 11z" fill="#7D8590"></path>
            </svg>
            Logout
        </button>
    </div>
    <audio id="loginMusic" src="msc/student.mp3" type="audio/mpeg"></audio>
</body>
<script>
    // Fade in the page content
    window.addEventListener('load', () => {
          document.body.style.opacity = '1';
        });

    function showNextPage(pageNumber) {
        document.querySelectorAll('.form-page').forEach(page => page.classList.remove('active'));
        document.getElementById('page' + pageNumber).classList.add('active');
    }

    function showPreviousPage(pageNumber) {
        document.querySelectorAll('.form-page').forEach(page => page.classList.remove('active'));
        document.getElementById('page' + pageNumber).classList.add('active');
    }

    const toggleSwitch = document.getElementById('theme-toggle');
    const body = document.body;

    // Function to set the background image based on the theme
    function setBackgroundImage() {
        if (body.classList.contains('dark-mode')) {
            body.style.backgroundImage = "url('img/night2.jpg')";
        } else {
            body.style.backgroundImage = "url('img/day2.jpg')";
        }
    }

    // Set initial background image
    setBackgroundImage();

    toggleSwitch.addEventListener('change', function() {
        body.classList.toggle('dark-mode');
        setBackgroundImage();
    });

    const menuToggle = document.getElementById('menu-toggle');
    const menuItems = document.getElementById('menu-items');

    menuToggle.addEventListener('change', function() {
        if (menuToggle.checked) {   
            menuItems.style.display = 'flex';
        } else {
            menuItems.style.display = 'none';
        }
    });
    // Play music when the page loads
    window.addEventListener('load', function() {
        // Get the audio element
        const music = document.getElementById('loginMusic');
        
        // Play the music
        music.play().catch(error => {
            console.error("Music playback failed:", error);
        });
    });
</script>
</html>
