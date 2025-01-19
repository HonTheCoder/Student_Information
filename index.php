<!DOCTYPE html>
<html>
  <head>
    <title>Student Information System</title>
    <link rel="stylesheet" href="css/styleindex.css" />
  </head>
  <body>
    <div id="snow-container"></div>
    <h1>Welcome to the Student Information System</h1>
    <div class="button-container">
      <div class="container" onclick="window.location.href='loadtoregister.php'">
        <img src="img/student.png" alt="Student" />
        <div class="title">Student</div>
      </div>
      <div class="container" onclick="window.location.href='loadtoadmin.php'">
        <img src="img/admin.png" alt="Admin" />
        <div class="title">Admin</div>
      </div>
    </div>

    <footer>
      <p>&copy; 2024 Hon Ezekiel Bognalbal & Jake Mariscotes. All rights reserved.</p>
    </footer>
    <script>
      document.addEventListener("DOMContentLoaded", function() {
        const snowContainer = document.getElementById('snow-container');
        const totalSnowflakes = 200;

        for (let i = 0; i < totalSnowflakes; i++) {
          const snowflake = document.createElement('div');
          snowflake.className = 'snowflake';
          snowflake.style.width = `${Math.random() * 10 + 5}px`;
          snowflake.style.height = snowflake.style.width;
          snowflake.style.left = `${Math.random() * 100}vw`;
          snowflake.style.animationDuration = `${Math.random() * 10 + 10}s`;
          snowflake.style.animationDelay = `${Math.random() * -10}s`;
          snowflake.style.opacity = Math.random() * 0.5 + 0.5;

          snowContainer.appendChild(snowflake);
        }

        // Fade in the page once it's fully loaded
        window.addEventListener('load', () => {
          document.body.style.opacity = '1';
        });
      });
    </script>
  </body>
</html>
