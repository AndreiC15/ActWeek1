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
                <form method="POST" action="./accountProcess/process.php">
                    <tr>
                        <input class="LogInText" type="text" id="first_name" name="first_name" placeholder="First Name" oninput="sanitizeInput(this); applySentenceCase(this);" required>
                    </tr>
                    <tr>
                        <input class="LogInText" type="text" id="middle_name" name="middle_name" placeholder="Middle Name" oninput="sanitizeInput(this); applySentenceCase(this);" required>
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
                        <input class="LogInText" type="text" id="phone_number" name="phone_number" placeholder="Phone Number" pattern="[0-9]{11}" title="Please enter a valid 11-digit phone number" oninput="this.value = this.value.replace(/[^0-9]/g, '').substring(0, 11);" required>
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
                            <input class="LogInText" type="text" id="house_no_street" name="house_no_street" placeholder="House Number & Street:" oninput="sanitizeInput(this); applySentenceCase(this);" required>
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

        function capitalizeEachWord(str) {
            return str.replace(/\b\w/g, function(char) {
                return char.toUpperCase();
            });
        }

        // Function to convert input value to sentence case for specific fields
        function applySentenceCase(inputElement) {
            var inputValue = inputElement.value;
            var sentenceCaseValue = inputValue.charAt(0).toUpperCase() + inputValue.slice(1);
            if (inputElement.id === "house_no_street") {
                sentenceCaseValue = capitalizeEachWord(sentenceCaseValue);
            }
            inputElement.value = sentenceCaseValue;
        }

        function sanitizeInput(inputElement) {
            // Remove special characters
            inputElement.value = inputElement.value.replace(/[^A-Za-z\s]/g, '');
        }

        function applySentenceCase(inputElement) {
            var inputValue = inputElement.value;
            var sentenceCaseValue = inputValue.charAt(0).toUpperCase() + inputValue.slice(1);
            inputElement.value = sentenceCaseValue;
        }
    </script>
</body>

</html>