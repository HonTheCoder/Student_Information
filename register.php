<?php
session_start(); // Start the session
include 'db.php'; // Include the database connection

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['register'])) {
        // Registration logic
        $username = $_POST['username'];
        $email = $_POST['email'];
        $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

        // Check if the email already exists
        $checkEmailSql = "SELECT * FROM registered_user WHERE email = ?";
        $checkEmailStmt = $conn->prepare($checkEmailSql);
        $checkEmailStmt->bind_param("s", $email);
        $checkEmailStmt->execute();
        $checkEmailStmt->store_result();

        if ($checkEmailStmt->num_rows > 0) {
            // Email already exists
            echo '<script>alert("Error. Please use another email."); window.history.back();</script>';
        } else {
            // Email is unique, proceed with registration
            $sql = "INSERT INTO registered_user (username, email, password) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sss", $username, $email, $password);

            if ($stmt->execute()) {
                echo '<script>alert("You have successfully registered!"); window.location.href = "register.php";</script>';
            } else {
                echo "Error: " . $stmt->error;
            }
        }
        $checkEmailStmt->close();
    } elseif (isset($_POST['login'])) {
        // Login logic
        $username = $_POST['username'];
        $password = $_POST['password'];

        $sql = "SELECT password FROM registered_user WHERE username = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $stmt->bind_result($hashed_password);
            $stmt->fetch();

            if (password_verify($password, $hashed_password)) {
                $_SESSION['username'] = $username; // Set session variable
                header("Location: loadtostudent.php");
                exit();
            } else {
                echo '<script>alert("Wrong password Or Username!"); window.location.href = "register.php";</script>';
            }
        } else {
            echo '<script>alert("User not registered!"); window.location.href = "register.php";</script>';
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
	<title>Register and Login</title>
	<link rel="stylesheet" type="text/css" href="css/register.css">
	<link href="https://fonts.googleapis.com/css2?family=Jost:wght@500&display=swap" rel="stylesheet">
</head>
<body>
	<div class="main">
        <div class="back-button button-borders">
            <a href="index.php" class="label primary-button">Back</a>
        </div>
        <input type="checkbox" id="chk" aria-hidden="true">

			<div class="signup">
				<form method="POST" action="">
					<label for="chk" aria-hidden="true">Sign up</label>
					<input type="text" name="username" placeholder="User name" required minlength="4" maxlength="10">
					<input type="email" name="email" placeholder="Email" required pattern=".+@gmail\.com" title="Please enter a valid Gmail address">
					<input type="password" name="password" placeholder="Password" required minlength="8" maxlength="13">
					<button type="submit" name="register">Sign up</button>
				</form>
			</div>

			<div class="login">
				<form method="POST" action="">
					<label for="chk" aria-hidden="true">Login</label>
					<input type="text" name="username" placeholder="Username" required="">
					<input type="password" name="password" placeholder="Password" required="">
					<button type="submit" name="login">Login</button>
					<a href="password_reset_request.php" class="forgot-password">Forgot Password?</a>
				</form>
			</div>
	</div>
</body>
    <script>
        window.history.forward();
        window.addEventListener('load', () => {
          document.body.style.opacity = '1';
        });
    </script>
</html>