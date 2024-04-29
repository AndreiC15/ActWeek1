<?php
require_once './accountProcess/connect.php';

// Check for an existing login session
if (isset($_SESSION['login']) && $_SESSION['login'] === true) {
    echo "<script>alert('You are already logged in, redirecting you to homepage now.'); window.location = 'homepage.php';</script>";
    exit();
}

$imageUrls = array(); // Initialize an empty array
$sql = "SELECT WallpaperLocation FROM wallpaper"; // Adjust the SQL query according to your database schema
$result = $databaseConnection->getConnection()->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $imageUrls[] = 'accountProcess/' . $row['WallpaperLocation'];
    }
}

// Shuffle the array to randomize the images
shuffle($imageUrls);
?>

<html>
<title>Reset Password</title>

<head>
    <link rel="stylesheet" href="pagesCSS/reset.css">
    <link rel="stylesheet" href="pagesCSS/reset2.css">
    <script>
        window.onload = function() {
            alert("Please enter the email you want to reset the password for. ");
        };
    </script>
</head>

<body>
    <div id="slideshow">
        <?php foreach ($imageUrls as $index => $imageUrl) : ?>
            <img src="<?php echo $imageUrl; ?>" alt="Slideshow Image" class="<?php echo $index === 0 ? 'active' : ''; ?>">
        <?php endforeach; ?>
    </div>
    <div class="Angle1"></div>
    <div class="Angle2"></div>
    <center>
        <div class="LogForm">
            <form method="POST" action="./accountProcess/process.php" onsubmit="showProcessingAlert()">
                <div class="ResBG">
                    <h1 class="ResText">Reset Password</h1>
                </div>
                <div class="resetInfo">
                    <input class="LogInText" type="email" id="email" name="email" placeholder="Email" required>
                </div>
                <input class="SubmitButton" type="submit" id="send_code_reset" name="send_code_reset" value="Reset Password" required>
            </form>
        </div>
    </center>
    <script>
        function togglePasswordVisibility() {
            var password = document.getElementById("password");
            var confirmPassword = document.getElementById("confirmPassword");

            // Toggle visibility for the "Password" field
            if (password.type === "password") {
                password.type = "text";
            } else {
                password.type = "password";
            }

            // Toggle visibility for the "Confirm Password" field
            if (confirmPassword.type === "password") {
                confirmPassword.type = "text";
            } else {
                confirmPassword.type = "password";
            }
        }
        var slideshowIndex = 0;
        var slideshowImages = <?php echo json_encode($imageUrls); ?>;
        var images = document.querySelectorAll('#slideshow img');

        function showSlides() {
            images[slideshowIndex].classList.remove('active');
            slideshowIndex = (slideshowIndex + 1) % images.length;
            images[slideshowIndex].classList.add('active');
            setTimeout(showSlides, 5000); // Change image every 5 seconds
        }

        // Start the slideshow when the page loads
        showSlides();

    // Display an alert when the page loads
    function showProcessingAlert () {
        alert("Please wait while we send the verification code to your email to verify your reset password request");
    };
    </script>
</body>

</html>