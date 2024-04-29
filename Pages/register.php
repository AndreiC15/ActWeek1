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
<title>Registration</title>

<head>
    <link rel="stylesheet" href="pagesCSS/register.css">
    <link rel="stylesheet" href="pagesCSS/removeArrowinput.css">
    <link rel="stylesheet" href="pagesCSS/register2.css">
    <script  src="pagesJS/register.js"></script>
</head>

<body>

    <div id="slideshow">
        <?php foreach ($imageUrls as $index => $imageUrl) : ?>
            <img src="<?php echo $imageUrl; ?>" alt="Slideshow Image" class="<?php echo $index === 0 ? 'active' : ''; ?>">
        <?php endforeach; ?>
    </div>

    <center>
        <div class="RegForm">
            <div class="RegBG">
                <h1 class="RegText">Registration</h1>
            </div>

            <table class="userInfoo">
            <form method="POST" action="./accountProcess/process.php" onsubmit="showProcessingAlert()">
                    <tr>
                        <input class="LogInText" type="text" id="first_name" name="first_name" placeholder="First Name" oninput="sanitizeInput(this); applySentenceCase(this);" required>
                    </tr>
                    <tr>
                        <input class="LogInText" type="text" id="middle_name" name="middle_name" placeholder="Middle Name" oninput="sanitizeInput(this); applySentenceCase(this);">
                    </tr>
                    <tr>
                        <input class="LogInText" type="text" id="last_name" name="last_name" placeholder="Last Name" oninput="sanitizeInput(this); applySentenceCase(this);" required>
                    </tr>
                    <tr>
                        <input class="LogInText" type="email" id="email" name="email" placeholder="Email" required>
                    </tr>
                    <tr>
                        <input class="PasswordText" type="password" id="password" name="password" placeholder="Password" minlength="8" required>
                    </tr>
                    <tr>
                        <input class="PasswordText" type="password" id="confirmPassword" name="confirmPassword" placeholder="Retype password" minlength="8" required>
                    </tr>
                    <tr>
                        <input class="LogInText" type="text" id="phone_number" name="phone_number" placeholder="Phone Number" oninput="sanitizeNumericInput(event);" maxlength="11" required>
                    </tr>
                    </br></br>
                    <tr>
                        <center>
                            <label class="checkbox-label">
                                <input class="ShowPass" type="checkbox" onclick="togglePasswordVisibility()">
                                Show Password
                        </center>
                        </label>
                    </tr>
            </table>

            <div class="AddBG">
                <h1 class="AddText">Address</h1>
            </div>
            <div class="userInfoAddress">
                <table class="userInfo">
                    <tr>
                        <td>
                            <input class="LogInText" type="text" id="country" name="country" placeholder="Country" oninput="sanitizeInput(this); applySentenceCase(this);" required>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <input class="LogInText" type="text" id="province" name="province" placeholder="Province" oninput="sanitizeInput(this); applySentenceCase(this);" required>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <input class="LogInText" type="text" id="citycity" name="citycity" placeholder="City/Municipality" oninput="sanitizeInput(this); applySentenceCase(this);" required>
                        </td>
                    </tr>
                </table>

                <table class="userInfo">
                    <tr>
                        <td>
                            <input class="LogInText" type="text" id="district" name="district" placeholder="District" oninput="sanitizeInput(this); applySentenceCase(this);" required>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <input class="LogInText" type="text" id="house_no_street" name="house_no_street" placeholder="House Number & Street" oninput="applySentenceCase(this);" required>
                        </td>
                    </tr>
                    <tr>
                        <td><input class="LogInText" type="text" id="zipcode" name="zipcode" placeholder="Zip Code" oninput="convertToUppercase(this);" required></td>
                    </tr>
                </table>
            </div>
            <input class="SubmitButton" type="submit" id="register" name="register" value="Register">
            <p>Already registered? <a href="index.php">Log in Here</a></p>
            </form>
        </div>
    </center>
    <div class="Angle1"></div>
    <div class="Angle2"></div>
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
    </script>
</body>

</html>