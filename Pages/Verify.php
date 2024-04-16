<?php

require_once './accountProcess/connect.php';

$con = mysqli_connect("localhost", "root", "", "logintest");
	if (!$con) {
		die("Connection failed: " . mysqli_connect_error());
	}

if (isset($_POST["verify_email"])) {
	
	$Email = isset($_GET['Email']) ? urldecode($_GET['Email']) : '';

	$VerificationCode = $_POST["VerificationCode"];

	

	// mark email as verified
	$sql = "UPDATE user_acct SET email_verified_at = NOW() WHERE Email = '$Email' AND VerificationCode = '$VerificationCode'";

	$result = mysqli_query($con, $sql);

	if ($result && mysqli_affected_rows($con) > 0) {
		echo "<p>You can login now.</p>";
		echo '<a href="login.php">Login now</a>';
	} else {
		echo "Verification code failed.";
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
				<b>
					<fieldset class="LogInForm" style="width:350px;">
						<legend>
							<p class="leglog" style="color:#000" ;>Verification: </p>
						</legend>
						<p class="fn" style="color: #000;">Input the code that we sent to your email</p>
						<table>
							<tr>
								<td>
									<p style="color: #000;" class="fn">Verification Code: </p>
								</td>
								<td><input type="number" id="verification_code" name="VerificationCode" required></br></td>
						</table></br>
						<input class="fn" type="submit" id="verify_email" name="verify_email" value="Verify">
			</form>
			</br></br>
			<center>
				</fieldset>
	</div>
</body>