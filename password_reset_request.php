<?php
include_once "db.php"; // Connect to the database

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];

    // Check if the email exists in the database
    $stmt = $conn->prepare("SELECT * FROM registered_user WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Email exists, redirect to a new password form
        echo "<script type='text/javascript'>
                window.location.href = 'new_password_form.php?email=" . urlencode($email) . "';
              </script>";
    } else {
        echo "<script type='text/javascript'>document.getElementById('message').innerText = 'No account found with that email address.';</script>";
    }

    $stmt->close();
    $conn->close();
}
?>
<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Font Awesome Icons  -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css"
        integrity="sha512-+4zCK9k+qNFUR5X+cKL9EIR+ZOhtIloNl9GIKS57V1MyNsYpYcUrUeQc9vNfzsWfV28IaLL3i96P9sdNyeRssA=="
        crossorigin="anonymous" />

    <!-- Google Fonts  -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/psr.css">

    <title>Forgot Password</title>
</head>

<body>
    <div id="message"></div>
    <div class="card">
        <div class="back-button button-borders">
            <a href="register.php" class="label primary-button">Back</a>
        </div>
        <p class="lock-icon"><i class="fas fa-lock"></i></p>
        <h2>Forgot Password?</h2>
        <p>You can reset your Password here</p>
        <form action="password_reset_request.php" method="post">
            <input type="email" name="email" class="passInput" placeholder="Email address" required>
            <button type="submit" class="password-reset">Send My Password</button>
        </form>
    </div>
    <script>
        // Function to show the message
        function showMessage(text) {
            var messageDiv = document.getElementById('message');
            messageDiv.innerText = text;
            messageDiv.style.display = 'block';
        }
    </script>
</body>

</html>
