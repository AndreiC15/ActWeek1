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
?>


<head>
    <title> Verify Sign In </title>
</head>

<body class="SignUpBG">
    <div class="LogIn">
        <center>
        <form method="POST" action="Verify.php">
        <!-- Pre-fill the email input field if the email parameter is present in the URL -->
        <input type="hidden" id="email" name="email" value="<?php echo isset($_GET['Email']) ? htmlspecialchars($_GET['Email']) : ''; ?>" required>
        <fieldset class="LogInForm" style="width:350px;">
            <p class="fn" style="color: #000;">Input the code that we sent to your email</p>
            
            <table>
                <tr>
                    <td>
                        <p style="color: #000;" class="fn">Verification Code: </p>
                    </td>
                    <td><input type="number" id="VerificationCode" name="VerificationCode" required></td>
                </tr>
            </table><br>
            <input class="fn" type="submit" id="verify_email" name="verify_email" value="Verify">
        </fieldset>
    </form>

            </br></br>
            <center>
                </fieldset>
    </div>
</body>
