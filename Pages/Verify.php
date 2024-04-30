<?php
require_once './accountProcess/connect.php';

if (isset($_POST["verify_email"])) {
    $Email = $_POST["email"];
    $VerificationCode = $_POST["VerificationCode"];

    $con = mysqli_connect("localhost", "root", "", "logintest");
    if (!$con) {
        die("Connection failed: " . mysqli_connect_error());
    }

    // Mark email as verified and set AccountStatus to "active"
    $sql = "UPDATE user_acct SET email_verified_at = NOW(), AccountStatus = 'active' WHERE Email = '$Email' AND VerificationCode = '$VerificationCode'";
    $result = mysqli_query($con, $sql);

    if ($result && mysqli_affected_rows($con) > 0) {
        echo "<script>alert('Account verification successful, please proceed to the login page'); window.location = 'index.php';</script>";
    } else {
        // Redirect to the verification page with the email parameter in the URL
        echo "<script>alert('Account verification failed, please input the correct code'); window.location = 'Verify.php?Email=" . urlencode($Email) . "';</script>";
    }
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


<head>
    <title> Verify Sign In </title>
    <link rel="stylesheet" href="pagesCSS/verify.css">
    <link rel="stylesheet" href="pagesCSS/verify2.css">
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
        <div class="verifyContainer">
            <form method="POST" action="Verify.php">
                <center>
                    <input type="hidden" id="email" name="email" value="<?php echo isset($_GET['Email']) ? htmlspecialchars($_GET['Email']) : ''; ?>" required>
                    <p>Please input the verification code that we've sent to your email to verify your account</p>
                    <input class="verificationInput" type="text" id="VerificationCode" name="VerificationCode" placeholder="Verification Code" oninput="sanitizeNumericInput(event);" maxlength="6" required></td></br></br>
                    <input class="verificationSubmit" type="submit" id="verify_email" name="verify_email" value="Verify">
                    <center>
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

        function sanitizeNumericInput(event) {
            var inputValue = event.target.value;
            // Replace any non-numeric characters with an empty string
            var numericValue = inputValue.replace(/[^0-9]/g, '');

            // Truncate to a maximum length of 11 characters
            event.target.value = numericValue.substring(0, 6);
        }
    </script>
</body>