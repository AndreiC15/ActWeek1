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
        <form  method="POST" action="./accountProcess/process.php">
            <h1>Reset Password</h1>
                <div class="divider"></div>
                    <input class="LogInText" type="email" id="email" name="email" placeholder="Email" required>
                    <input class="PasswordText" type="password" id="password" name="password" placeholder="Password" minlength="8" required>
                    <input class="PasswordText" type="password" id="confirmPassword" name="confirmPassword" placeholder="Retype password" minlength="8" required></br></br>
                    <input class="ShowPass" type="checkbox" onclick="togglePasswordVisibility()">Show Password
                        <br>
                        <input class="SubmitButton" type="submit" id="reset_password" name="reset_password" value="Reset Password" required>
                        
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
    
        </script>   
    </body>
</html>
