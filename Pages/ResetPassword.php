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
    <link rel="stylesheet" href="pagesCSS/ResetPassword.css">
    <link rel="stylesheet" href="pagesCSS/ResetPassword2.css">
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
            <form method="POST" action="./accountProcess/process.php">
                <div class="ResBG">
                    <h1 class="ResText">Reset Password</h1>
                </div>
                <input type="hidden" id="email" name="email" value="<?php echo isset($_GET['Email']) ? htmlspecialchars($_GET['Email']) : ''; ?>" required>
                <input class="PasswordText" type="password" id="password" name="password" placeholder="New Password" minlength="8" required>
                <input class="PasswordText" type="password" id="confirmPassword" name="confirmPassword" placeholder="Retype password" minlength="8" required>
                <table class="checkbox">
                    <tr>
                        <td>
                            <label class="checkbox-label">
                                <input class="ShowPass" type="checkbox" onclick="togglePasswordVisibility()">
                                Show Password
                            </label>
                        </td>
                    </tr>
                </table>
                <input class="SubmitButton" type="submit" id="reset_password" name="reset_password" value="Reset Password" required>
            </form>
        </div>
    </center>
    <script>
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
    </script>
</body>

</html>