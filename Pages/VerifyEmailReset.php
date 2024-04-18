<?php
require_once './accountProcess/connect.php';

if (isset($_POST["verify_reset_code"])) {
    $Email = $_POST["email"];
    $VerificationCode = $_POST["VerificationCode"];

    $con = mysqli_connect("localhost", "root", "", "logintest");
    if (!$con) {
        die("Connection failed: " . mysqli_connect_error());
    }

    // Mark email as verified and set AccountStatus to "active"
    $sql = "SELECT * FROM user_acct  WHERE Email = '$Email' AND VerificationCode = '$VerificationCode'";
    $result = mysqli_query($con, $sql);

    if ($result && mysqli_affected_rows($con) > 0) {
        echo "<script>alert('Account confirmation success, please proceed to the reset password page');  window.location = 'ResetPassword.php?Email=" . urlencode($Email) . "';</script>";
    } else {
        // Redirect to the verification page with the email parameter in the URL
        echo "<script>alert('Account verification failed, please input the correct code'); window.location = 'VerifyEmailReset.php?Email=" . urlencode($Email) . "';</script>";
    }
}

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


<head>
    <title> Verify Sign In </title>
    <link rel="stylesheet" href="pagesCSS/verify.css">
    <style>
        body,
        html {
            margin: 0;
            padding: 0;
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        #slideshow {
            position: relative;
            flex: 1;
            height: 100vh;
            /* Cover entire viewport height */
            z-index: 1;
            /* Ensure slideshow is behind other elements */
        }

        #slideshow img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            position: absolute;
            top: 0;
            left: 0;
            opacity: 0;
            transition: opacity 2s ease-in-out;
        }

        #slideshow img.active {
            opacity: 1;
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
            z-index: 1;
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
            z-index: 1;
        }
    </style>
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
            <form method="POST" action="">
            <center>
                <input type="hidden" id="email" name="email" value="<?php echo isset($_GET['Email']) ? htmlspecialchars($_GET['Email']) : ''; ?>" required>
                <p >Input the code that we sent to your email</p>
                <input class="verificationInput" type="text" id="VerificationCode" name="VerificationCode" placeholder="Verification Code" oninput="sanitizeNumericInput(event);" maxlength="6" required></td></br></br>
                <input class="verificationSubmit" type="submit" id="verify_reset_code" name="verify_reset_code" value="Verify">
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