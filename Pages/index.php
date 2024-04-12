<?php
require_once './accountProcess/connect.php';

// Check for an existing login session
if (isset($_SESSION['login']) && $_SESSION['login'] === true) {
    echo "<script>alert('You are already logged in, redirecting you to homepage now.'); window.location = 'homepage.php';</script>";
    exit();
}
?>

<html>
<title>WallpaperStation</title>

<head>
    <link rel="stylesheet" href="pagesCSS/StartupScreen.css">
</head>

<body>
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
    <script>
        function myFunction() {
            var x = document.getElementById("password");
            if (x.type === "password") {
                x.type = "text";
            } else {
                x.type = "password";
            }
        }
    </script>
</body>

</html>