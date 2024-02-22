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
                        <input class="LogInText" type="text" id="phone_number" name="phone_number" placeholder="Phone Number" oninput="sanitizeNumericInput(event);" maxlength="11" required>
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
                            <input class="LogInText" type="text" id="house_no_street" name="house_no_street" placeholder="House Number & Street:" oninput="applySentenceCase(this);" required>
                        </td>
                    </tr>
                    <tr>
                        <td><input class="LogInText" type="text" id="zipcode" name="zipcode" placeholder="Zip Code" oninput="convertToUppercase(this);"></td>
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
            toggleInputType(password);

            // Toggle visibility for the "Confirm Password" field
            toggleInputType(confirmPassword);
        }

        function toggleInputType(inputElement) {
            inputElement.type = (inputElement.type === "password") ? "text" : "password";
        }

        function capitalizeEachWord(str) {
            return str.replace(/\b\w/g, function(char) {
                return char.toUpperCase();
            });
        }

        function applySentenceCase(inputElement) {
            var inputValue = inputElement.value;

            // Special case for "House No. & Street"
            if (inputElement.id === "house_no_street") {
                inputValue = capitalizeEachWord(inputValue);
            } else {
                // Regular sentence case for other fields
                var words = inputValue.split(/\s+/); // Split by whitespace
                words = words.map(function(word) {
                    return word.charAt(0).toUpperCase() + word.slice(1).toLowerCase();
                });
                inputValue = words.join(' ');
            }
            inputElement.value = inputValue;
        }

        function convertToUppercase(inputElement) {
            inputElement.value = inputElement.value.toUpperCase();
        }

        function sanitizeNumericInput(event) {
            var inputValue = event.target.value;
            // Replace any non-numeric characters with an empty string
            var numericValue = inputValue.replace(/[^0-9]/g, '');

            // Truncate to a maximum length of 11 characters
            event.target.value = numericValue.substring(0, 11);
        }
    </script>
</body>

</html>