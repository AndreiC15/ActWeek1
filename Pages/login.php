<?php
require_once './accountProcess/connect.php';

// Check for an existing login session
if (isset($_SESSION['login']) && $_SESSION['login'] === true) {
    echo "<script>alert('You are already logged in, redirecting you to homepage now.'); window.location = 'homepage.php';</script>";
    exit();
}
?>

<html>
<title>Log in</title>

<head>
    <link rel="stylesheet" href="pagesCSS/IndexStyle.css">
</head>

<body>
    <center>
        <div class="webIcon">
            <p class="webtitle">Wallpaper</p>
            <div class="hub">
                <p class="webtitle" style="padding: 0 10px 0 10px;">Station</p>
            </div>
        </div>
        <div class="LogForm">
            <form method="POST" action="./accountProcess/process.php">
                <h1>Login</h1>
                <div class="divider"></div>
                <input class="LogInText" type="email" id="email" name="email" placeholder="Email" required>
                <input class="PasswordText" type="password" id="password" name="password" placeholder="Password" minlength="8" required></br></br>
                <input class="ShowPass" type="checkbox" onclick="myFunction()">Show Password
                <br>
                <input class="SubmitButton" type="submit" id="login" name="login" value="Log In" required>
            </form>
            <p>Forgot password? <a href="reset.php">Click here</a></p>
            <p>Not registered yet? <a href="register.php">Register here</a></p>
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