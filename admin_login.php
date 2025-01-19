<?php
include_once "db.php";
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    $stmt = $conn->prepare("SELECT * FROM admin_users WHERE admin_username = ?");
    if ($stmt) {
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $admin = $result->fetch_assoc();

        if ($admin && password_verify($password, $admin['admin_password'])) {
            // Secure session handling
            session_regenerate_id(true);
            $_SESSION['admin_logged_in'] = true; // Ensure this matches the dashboard check
            $_SESSION['username'] = $username;
        
            header("Location: loadtodashB.php");
            exit();
        } else {
            // Redirect to login page with an error message
            echo "<script>alert('Invalid credentials. You are not an admin.'); window.location.href = 'admin_login.php';</script>";
            exit();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link rel="stylesheet" href="css/admin_login.css">
</head>
<body>
    <form class="login" method="POST" action="">
        <a href="index.php" class="back-button">Back</a>
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Login</button>
    </form>
    <?php if (isset($error)) { echo "<p class='error'>$error</p>"; } ?>
</body>     
    <script>    
        window.history.forward();
    </script>
</html>
