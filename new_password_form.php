<?php
include_once "db.php"; // Connect to the database

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_GET['email'];
    $new_password = $_POST['new_password'];

    // Hash the new password for security
    $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);

    // Update the password in the database
    $stmt = $conn->prepare("UPDATE registered_user SET password = ? WHERE email = ?");
    $stmt->bind_param("ss", $hashed_password, $email);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        echo "<script type='text/javascript'>alert('Password updated successfully.'); window.location.href = 'register.php';</script>";
    } else {
        echo "<script type='text/javascript'>alert('Failed to update password. Please try again.');window.location.href = 'register.php';</script>";
    }

    $stmt->close();
    $conn->close();
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="css/psr.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <title>Reset Password</title>
</head>
<body>
    <div class="card">
        <a href="index.php" class="home-button">
            <i class="fas fa-home"></i> Home
        </a>
        <h2>Reset Your Password</h2>
        <form action="new_password_form.php?email=<?php echo urlencode($_GET['email']); ?>" method="post">
            <input type="password" name="new_password" class="passInput" placeholder="New Password" required minlength="8" maxlength="13">
            <button type="submit" class="password-reset">Update Password</button>
        </form>
    </div>
</body>
</html>