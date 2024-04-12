<?php
require_once 'accountProcess/connect.php';
// Fetch image URLs from the database
$imageUrls = array(); // Initialize an empty array
$sql = "SELECT WallpaperLocation FROM wallpaper"; // Adjust the SQL query according to your database schema
$result = $databaseConnection->getConnection()->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // $imageUrls[] = $row['imageUrl'];
        $imageUrls[] = 'accountProcess/' . $row['WallpaperLocation'];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="pagesCSS/StartupScreen.css">
    <style>
        body, html {
            margin: 0;
            padding: 0;
            height: 100%;
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }

        #slideshow {
            flex: 1;
            height: 100%;
        }

        #slideshow img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .LeftBG {
            flex-grow: 1;
        }

        .footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #333;
            color: white;
            padding: 10px;
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
        }

        .footer p {
            margin: 0;
        }
    </style>
</head>
<body>
    <!-- Your HTML body content here -->
    <div id="slideshow">
        <?php foreach ($imageUrls as $imageUrl): ?>
            <img src="<?php echo $imageUrl; ?>" alt="Slideshow Image">
        <?php endforeach; ?>
    </div>

    <center>
        <div class="LeftBG">
            <center>
                <img class="LogoFigma" src="testImages/LogoFigma.png">
                <p class="quote">Personalize your device with our vast collection of wallpapers</p>
            </center>
            <form method="POST" action="./accountProcess/process.php">
                <input class="LogInText" type="email" id="email" name="email" placeholder="Email" required>
                <input class="PasswordText" type="password" id="password" name="password" placeholder="Password" minlength="8" required></br></br>
                <input class="ShowPass" type="checkbox" onclick="myFunction()">Show Password
                <br>
                <input class="SubmitButton" type="submit" id="login" name="login" value="Log In" required>
            </form>
            <p>Forgot password? <a href="reset.php">Click here</a></br></br>Not registered yet? Sign up below</p>
            <a href="register.php">
                <div class="SignUpButton">
                    <p>Sign Up</p>
                </div>
            </a>
        </div>
    </center>

    <div class="footer">
        <p>For project purposes only, no copyright infringement</p>
        <p style="margin-right:1%"><?php echo date("F j, Y"); ?></p>
    </div>

    <script>
        // JavaScript code for slideshow functionality
        var slideshowIndex = 0;
        var slideshowImages = <?php echo json_encode($imageUrls); ?>;

        function showSlides() {
            var slideshow = document.getElementById("slideshow");
            if (slideshowImages.length === 0) return;

            slideshow.innerHTML = ""; // Clear existing images

            var img = document.createElement("img");
            img.src = slideshowImages[slideshowIndex];
            img.alt = "Slideshow Image";
            slideshow.appendChild(img);

            slideshowIndex++;
            if (slideshowIndex >= slideshowImages.length) {
                slideshowIndex = 0; // Restart slideshow from the beginning
            }

            setTimeout(showSlides, 5000); // Change image every 3 seconds
        }

        // Start the slideshow when the page loads
        showSlides();
    </script>
</body>
</html>
