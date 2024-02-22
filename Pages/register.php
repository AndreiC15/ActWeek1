<?php
require_once './accountProcess/connect.php';

// Check for an existing login session
if (isset($_SESSION['login']) && $_SESSION['login'] === true) {
    echo "<script>alert('You are already logged in, redirecting you to homepage now.'); window.location = 'homepage.php';</script>";
    exit();
}
?>
<html>
    <title>Registration</title>
    <head>
    <link rel="stylesheet" href="pagesCSS/IndexStyle.css">
    <link rel="stylesheet" href="pagesCSS/removeArrowinput.css">
    </head>
    <body>
       <center>
       <div class="ForgotwebIcon">
        <p class="webtitle">Wallpaper</p>
        <div class="hub">
            <p class="webtitle" style="padding: 0 10px 0 10px;">Station</p>
        </div>
    </div>
        <div class="RegForm">
                <h1>Registration</h1>
                    <div class="divider"></div>
                            <table class="userInfo">
                                <form  method="POST" action="./accountProcess/process.php">
                                <tr>
                                    <input class="LogInText" type="text" id="first_name" name="first_name" placeholder="First Name" required>
                                </tr>
                                <tr>
                                    <input class="LogInText" type="text" id="middle_name" name="middle_name" placeholder="Middle Name" required>
                                </tr>
                                <tr>
                                    <input class="LogInText" type="text" id="last_name" name="last_name" placeholder="Last Name" required>
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
                                    <input class="LogInText" type="number" id="phone_number" name="phone_number" placeholder="Phone Number" maxlength="11" required>
                                </tr>

                                    <br></br>
                                    <center>
                                    <input class="ShowPass" type="checkbox" onclick="togglePasswordVisibility()">Show Password

                                </center>
                            </table>
                            
                            <div class="dividerTop"></div>
                            <h1>Address</h1>
                        <div class="userInfoAddress">
                            <table class="userInfo">
                                <tr>
                                    <td>
                                    <input class="LogInText" type="text" id="country" name="country" placeholder="Country" required>
                                    </td>                                
                                </tr>
                                <tr>
                                    <td>
                                    <input class="LogInText" type="text" id="province" name="province" placeholder="Province" required>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                    <input class="LogInText" type="text" id="citycity" name="citycity" placeholder="City/Municipality" required>
                                    </td>
                                </tr>
                                
                            </table>

                            <table class="userInfo">
                            <tr>
                                    <td>
                                    <input class="LogInText" type="text" id="district" name="district" placeholder="District" required>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                    <input class="LogInText" type="text" id="house_no_street" name="house_no_street" placeholder="House Number & Street:" required>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                    <input class="LogInText" type="number" id="zip_code" name="zip_code" placeholder="Zip Code" required>
                                    </td>
                                </tr>
                            </table>
                        </div>
                            </br>
                            <input class="SubmitButton" type="submit" id="register" name="register" value="Register">
                            <p>Already registered? <a href="index.php">Log in Here</a></p>
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