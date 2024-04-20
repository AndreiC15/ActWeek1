<?php
require_once 'accountProcess/connect.php';

if (isset($_SESSION['login']) && $_SESSION['login'] === true) {
    echo "<script>alert('You are already logged in, redirecting you to homepage now.'); window.location = 'homepage.php';</script>";
    exit();
}

// Fetch image URLs from the database
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

<!DOCTYPE html>
<html lang="en">

<head>
    <link rel="stylesheet" href="pagesCSS/StartupScreen.css">
    <style>
        body,
        html {
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
            position: relative;
        }

        #slideshow img {
            width: 100%;
            height: 100%;
            opacity: 0;
            /* Set initial opacity to 0 */
            position: absolute;
            object-fit: cover;
            /* Ensure proper sizing without stretching */
            top: 0;
            left: 0;
            transform: scale(1);
            /* Set initial scale */
            transition: transform 2s ease-in-out, opacity 2s ease-in-out;
            /* Apply ease-in-out transition for transform and opacity */
        }

        #slideshow img.active {
            opacity: 1;
            /* Set opacity to 1 for active image */
            transform: scale(1.2);
            /* Increase scale for active image */
        }

        .LeftBG {
            flex-grow: 1;
        }

        .footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: rgba(0, 0, 0, 0.75);
            /* Black color with 50% opacity */
            color: white;
            padding: 5px;
            position: fixed;
            bottom: 0;
            font-size: 12px;
            left: 0;
            width: 100%;
        }

        .footer p {
            margin: 0;
        }

        .Angle1 {
            width: 0;
            height: 0;
            border-top: calc(60vh - 100px) solid transparent;
            /* Adjust the height as needed */
            border-left: calc(60vw - 100px) solid white;
            /* Adjust the color and width as needed */
            opacity: 0.85;
            position: fixed;
            bottom: 0;
            left: 0;
        }

        .Angle2 {
            width: 0;
            height: 0;
            border-bottom: calc(60vh - 100px) solid transparent;
            /* Adjust the height as needed */
            border-right: calc(60vw - 100px) solid white;
            /* Adjust the color and width as needed */
            opacity: 0.85;
            position: fixed;
            top: 0;
            right: 0;
        }
    </style>
</head>

<body>
    <!-- Your HTML body content here -->
    <div id="slideshow">
        <?php foreach ($imageUrls as $index => $imageUrl) : ?>
            <img src="<?php echo $imageUrl; ?>" alt="Slideshow Image" class="<?php echo $index === 0 ? 'active' : ''; ?>">
        <?php endforeach; ?>
    </div>


    <center>
        <div class="LeftBG">
            <center>
                <img class="LogoFigma" src="testImages/LogoFigma.png">
                <p class="quote">Personalize your device with our vast collection of HD wallpapers</p>
            </center>
            <form method="POST" action="./accountProcess/process.php">
                <table>
                    <tr><input class="LogInText" type="email" id="email" name="email" placeholder="Email" required></tr></br>
                    <tr><input class="PasswordText" type="password" id="password" name="password" placeholder="Password" minlength="8" required></tr>
                </table>
                <table class="checkbox">
                    <tr>
                        <td>
                            <label class="checkbox-label">
                                <input class="ShowPass" type="checkbox" onclick="myFunction()">
                                Show Password
                            </label>
                        </td>
                    </tr>
                </table>

                <br>
                <input class="SubmitButton" type="submit" id="login" name="login" value="Log In" required>
            </form>
            <p class="ForgReg">Forgot password? <a href="reset.php">Click here</a></br></br>Not registered yet? Sign up below</p>
            <a href="register.php">
                <div class="SignUpButton">
                    <p>Sign Up</p>
                </div>
            </a>
        </div>
    </center>

    <div class="Angle1"></div>
    <div class="Angle2"></div>

    <div class="footer">
        <p class="footerText">The images used in this website are for project purposes only, no copyright infringement to its rightful owners</p>
        <p class="footerText" style="margin-right:1%"><?php echo date("F j, Y"); ?></p>
    </div>

    <script>
        function myFunction() {
            var x = document.getElementById("password");
            if (x.type === "password") {
                x.type = "text";
            } else {
                x.type = "password";
            }
        }

        // JavaScript code for slideshow functionality
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
    </script>
</body>

</html>