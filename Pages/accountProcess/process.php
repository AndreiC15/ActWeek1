<?php
require_once 'connect.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require 'C:\xampp\htdocs\PHPMailer\PHPMailer\src\Exception.php';
require 'C:\xampp\htdocs\PHPMailer\PHPMailer\src\PHPMailer.php';
require 'C:\xampp\htdocs\PHPMailer\PHPMailer\src\SMTP.php';

class UserAuth
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    // EMAIL DUPLICATE CHECK
    public function checkDuplicateEmail($email)
    {
        $checkEmailDuplicate = "SELECT * FROM user_acct WHERE Email = ?";
        $stmtCheckEmail = $this->db->prepare($checkEmailDuplicate);
        $stmtCheckEmail->bind_param("s", $email);
        $stmtCheckEmail->execute();
        $checkEmailResult = $stmtCheckEmail->get_result();

        return $checkEmailResult->num_rows > 0;
    }

    // CONTACT NUMBER DUPLICATE CHECK
    public function checkDuplicatePhoneNumber($phoneNumber)
    {
        $checkPhoneNumberDuplicate = "SELECT * FROM user_acct WHERE PhoneNumber = ?";
        $stmtCheckPhoneNumber = $this->db->prepare($checkPhoneNumberDuplicate);
        $stmtCheckPhoneNumber->bind_param("s", $phoneNumber);
        $stmtCheckPhoneNumber->execute();
        $checkPhoneNumberResult = $stmtCheckPhoneNumber->get_result();

        return $checkPhoneNumberResult->num_rows > 0;
    }

    public function login($Email, $password)
    {
        $con = $this->db->getConnection();

        if (isset($_SESSION['login']) && $_SESSION['login'] === true) {
            echo "<script>alert('You are already logged in.'); window.location = '../homepage.php';</script>";
            exit;
        }

        // Check if user account exists
        $result = mysqli_query($con, "SELECT * FROM user_acct WHERE email = '$Email'");
        $row = mysqli_fetch_assoc($result);

        if (mysqli_num_rows($result) > 0) {
            // Check if account is active
            if ($row['AccountStatus'] === 'active') {
                // Verify password
                if ($password == $row['Password']) {
                    // Set session variables
                    $_SESSION['login'] = true;
                    $_SESSION['id'] = $row['ID'];
                    echo "<script>alert('Log In Successfully'); window.location = '../homepage.php';</script>";
                    exit;
                } else {
                    // Incorrect password
                    echo "<script>alert('Wrong Email or Password'); window.location = '../index.php';</script>";
                    exit;
                }
            } else {
                // Account is inactive
                echo "<script>alert('Your account is not verified. You will be redirected now to verify your account.'); window.location = '../Verify.php?Email=" . urlencode($Email) . "';</script>";

                $mail = new PHPMailer(true);
                //Server settings
                try {
                    $mail->SMTPDebug = SMTP::DEBUG_SERVER;
                    $mail->isSMTP();
                    $mail->Host       = 'smtp.gmail.com';
                    $mail->SMTPAuth   = true;
                    $mail->Username   = 'calderon.optical.clinic@gmail.com';
                    $mail->Password   = 'avuoeowvxfwgnjix';
                    $mail->SMTPSecure = 'ssl';
                    $mail->Port       = 465;
                    //Recipients
                    $mail->setFrom('calderon.optical.clinic@gmail.com', 'WallpaperStation');
                    $mail->addAddress($Email);
                    //Content
                    $mail->isHTML(true);
                    $VerificationCode = substr(number_format(time() * rand(), 0, '', ''), 0, 6);
                    $mail->Subject = 'Verify your email.';
                    $mail->Body = '<p style="font-size: 20px;">Good day! Your verification code is: <b style="font-size: 30px;">&nbsp;' . $VerificationCode . '&nbsp;</b> Thank you!</p>';
                    $mail->send();

                    $sql = "UPDATE user_acct SET VerificationCode = ? WHERE Email = ?";
                    $query = $this->db->prepare($sql);

                    $insertParams = [&$VerificationCode, &$Email];
                    $paramTypes = str_repeat('s', count($insertParams)); // 's' for string
                    $query->bind_param($paramTypes, ...$insertParams);

                    $result = $query->execute();
                    exit;
                } catch (Exception $e) {
                    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
                }
            }
        } else {
            // User not found
            echo "<script>alert('User not found'); window.location = '../index.php';</script>";
            exit;
        }
    }


    public function register(
        $FirstName,
        $MiddleName,
        $LastName,
        $Email,
        $Password,
        $PhoneNumber,
        $Country,
        $Province,
        $CityCity,
        $District,
        $HouseNoStreet,
        $ZipCode,
        $VerificationCode,
        $email_verified_at
    ) {

        $con = $this->db->getConnection();

        if (strlen($PhoneNumber) < 11) {
            echo "<script>alert('Phone number must be at least 11 characters long.'); window.location = '../register.php';</script>";
            exit;
        }

        if ($this->checkDuplicateEmail($Email)) {
            echo "<script>alert('Email Already Exists'); window.location = '../register.php';</script>";
            exit;
        }

        if ($this->checkDuplicatePhoneNumber($PhoneNumber)) {
            echo "<script>alert('Contact Number Already Exists'); window.location = '../register.php';</script>";
            exit;
        }

        if ($_POST['password'] !== $_POST['confirmPassword']) {
            echo "<script>alert('Passwords do not match'); window.location = '../register.php';</script>";
            exit;
        }

        $mail = new PHPMailer(true);
        //Server settings
        try {
            $mail->SMTPDebug = SMTP::DEBUG_SERVER;
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'calderon.optical.clinic@gmail.com';
            $mail->Password   = 'avuoeowvxfwgnjix';
            $mail->SMTPSecure = 'ssl';
            $mail->Port       = 465;
            //Recipients
            $mail->setFrom('calderon.optical.clinic@gmail.com', 'WallpaperStation');
            $mail->addAddress($Email, $FirstName);
            //Content
            $mail->isHTML(true);
            $VerificationCode = substr(number_format(time() * rand(), 0, '', ''), 0, 6);
            $mail->Subject = 'Verify your email.';
            $mail->Body = '<p style="font-size: 20px;">Good day! Your verification code is: <b style="font-size: 30px;">&nbsp;' . $VerificationCode . '&nbsp;</b> Thank you!</p>';
            $mail->send();

            $sql = "INSERT INTO user_acct (FirstName, MiddleName, LastName, Email, Password, PhoneNumber, Country, Province, CityCity, District, HouseNoStreet, ZipCode, VerificationCode, email_verified_at, AccountStatus) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $query = $this->db->prepare($sql);

            $AccountStatus = "inactive"; // Set the AccountStatus to "inactive"
            $insertParams = [&$FirstName, &$MiddleName, &$LastName, &$Email, &$Password, &$PhoneNumber, &$Country, &$Province, &$CityCity, &$District, &$HouseNoStreet, &$ZipCode, &$VerificationCode, &$email_verified_at, &$AccountStatus];
            $paramTypes = str_repeat('s', count($insertParams)); // 's' for string
            $query->bind_param($paramTypes, ...$insertParams);

            $result = $query->execute();


            // After successfully inserting the user's data into the database
            if ($result) {
                echo "<script>alert('Registered Successfully, please proceed to the verification page');</script>";
                $email = $_POST['email'];
                header("Location: ../Verify.php?Email=" . $email);
                exit();
            } else {
                echo "<script>alert('Error in registration'); window.location = '../register.php';</script>";
            }
        } catch (Exception $e) {
            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    }

    public function resetSelectEmail($Email)
    {
        $con = $this->db->getConnection();
        // You may want to perform additional validation and sanitation for $password
        $result = mysqli_query($con, "SELECT * FROM user_acct WHERE email = '$Email'");
        $row = mysqli_fetch_assoc($result);

        if (mysqli_num_rows($result) > 0) {
            $mail = new PHPMailer(true);
            //Server settings
            try {
                $mail->SMTPDebug = SMTP::DEBUG_SERVER;
                $mail->isSMTP();
                $mail->Host       = 'smtp.gmail.com';
                $mail->SMTPAuth   = true;
                $mail->Username   = 'calderon.optical.clinic@gmail.com';
                $mail->Password   = 'avuoeowvxfwgnjix';
                $mail->SMTPSecure = 'ssl';
                $mail->Port       = 465;
                //Recipients
                $mail->setFrom('calderon.optical.clinic@gmail.com', 'WallpaperStation');
                $mail->addAddress($Email);
                //Content
                $mail->isHTML(true);
                $VerificationCode = substr(number_format(time() * rand(), 0, '', ''), 0, 6);
                $mail->Subject = 'Verify your email.';
                $mail->Body = '<p style="font-size: 20px;">Good day! Your verification code is: <b style="font-size: 30px;">&nbsp;' . $VerificationCode . '&nbsp;</b> Thank you!</p>';
                $mail->send();

                $sql = "UPDATE user_acct SET VerificationCode = ? WHERE Email = ?";
                $query = $this->db->prepare($sql);

                $insertParams = [&$VerificationCode, &$Email];
                $paramTypes = str_repeat('s', count($insertParams)); // 's' for string
                $query->bind_param($paramTypes, ...$insertParams);

                $result = $query->execute();



                // After successfully inserting the user's data into the database
                if ($result) {
                    echo "<script>alert('Account Confirmation success, please proceed to the verify email page');</script>";
                    $email = $_POST['email'];
                    header("Location: ../VerifyEmailReset.php?Email=" . $email);
                    exit();
                } else {
                    echo "<script>alert('Error in registration'); window.location = '../register.php';</script>";
                }
            } catch (Exception $e) {
                echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
            }
        } else {
            echo "<script>alert('Email not found'); window.location = '../reset.php';</script>";
            exit;
        }
    }

    public function resetPassword($Email, $password)
    {
        $con = $this->db->getConnection();
        // You may want to perform additional validation and sanitation for $password
        $result = mysqli_query($con, "SELECT * FROM user_acct WHERE email = '$Email'");
        $row = mysqli_fetch_assoc($result);

        if (mysqli_num_rows($result) > 0) {
            $sql = "UPDATE user_acct SET Password = ? WHERE Email = ?";
            $stmt = $this->db->getConnection()->prepare($sql);
            $stmt->bind_param('ss', $password, $Email);

            if ($_POST['password'] !== $_POST['confirmPassword']) {
                echo "<script>alert('Passwords do not match'); window.location = '../ResetPassword.php?Email=" . urlencode($Email) . "';</script>";
                exit;
            }

            if ($stmt->execute()) {
                echo "<script>alert('Password reset successful.'); window.location = '../index.php';</script>";
                exit();
            } else {
                echo "<script>alert('Password reset failed.'); window.location = '../index.php';</script>";
                exit();
            }
        } else {
            echo "<script>alert('User not found'); window.location = '../index.php';</script>";
            exit;
        }
    }

    public function editInformation($id, $FirstName, $MiddleName, $LastName, $Email, $Password, $PhoneNumber, $Country, $Province, $CityCity, $District, $HouseNoStreet, $ZipCode, $ProfilePic)
    {
        $db = $this->db->getConnection();
        $fields = ['FirstName', 'MiddleName', 'LastName', 'Email', 'Password', 'PhoneNumber', 'Country', 'Province', 'CityCity', 'District', 'HouseNoStreet', 'ZipCode', 'ProfilePic'];
        $id = $db->real_escape_string($id);

        // Check if any changes were made
        $changesMade = false;
        foreach ($fields as $field) {
            if (!empty($$field)) {
                $changesMade = true;
                break;
            }
        }

        if (!$changesMade) {
            echo "<script>alert('No changes made.'); window.location = '../settings.php';</script>";
            exit;
        }

        if ($this->checkDuplicateEmail($Email)) {
            echo "<script>alert('Email Already Exists'); window.location = '../settings.php';</script>";
            exit;
        }

        if ($this->checkDuplicatePhoneNumber($PhoneNumber)) {
            echo "<script>alert('Contact Number Already Exists'); window.location = '../settings.php';</script>";
            exit;
        }

        // Handle profile picture upload
        $profilePicLocation = null;
        if (!empty($_FILES['profile_pic']['name'])) {
            $profilePic = $_FILES['profile_pic'];
            $profilePic_temp = $profilePic['tmp_name'];
            $profilePicLocation = "profilePic/" . $profilePic['name'];
            move_uploaded_file($profilePic_temp, $profilePicLocation);
        }

        $sql = "UPDATE user_acct SET ";
        $updateFields = [];
        $params = [];

        foreach ($fields as $field) {
            if (!empty($$field)) {
                if ($field === 'ProfilePic') {
                    if ($profilePicLocation !== null) {
                        $updateFields[] = "$field = ?";
                        $params[] = $profilePicLocation;
                    } // If no new picture is uploaded, the existing picture remains unchanged
                } elseif ($field === 'ZipCode') {
                    // Treat ZipCode as a string to preserve leading zeros
                    $updateFields[] = "$field = ?";
                    $params[] = (string) $$field;
                } else {
                    $updateFields[] = "$field = ?";
                    $params[] = $$field;
                }
            }
        }

        $sql .= implode(", ", $updateFields);

        // Check if any fields are updated
        if (!empty($updateFields)) {
            $sql .= " WHERE ID = ?";

            // Add the ID to the parameters
            $params[] = $id;

            $stmt = $db->prepare($sql);

            if (!$stmt) {
                // Check for errors in the preparation of the statement
                echo "<script>alert('Error in SQL query preparation'); window.location = '../settings.php';</script>";
                exit;
            }

            $paramTypes = str_repeat('s', count($params));
            $stmt->bind_param($paramTypes, ...$params);

            $result = $stmt->execute();

            if ($result) {
                echo "<script>alert('User info updated!'); window.location = '../settings.php';</script>";
                exit;
            } else {
                echo "<script>alert('Failed to update user information'); window.location = '../settings.php';</script>";
            }

            $stmt->close();
        } else {
            echo "<script>alert('No changes made.'); window.location = '../settings.php';</script>";
        }
    }

    // Inside the UserAuth class in process.php

    public function removeProfilePicture($id)
    {
        $id = filter_var($id, FILTER_VALIDATE_INT);

        if ($id === false || $id <= 0) {
            echo "Invalid user ID";
            exit;
        }

        $db = $this->db->getConnection();

        // Fetch the current profile picture path
        $getProfilePicPathSql = "SELECT ProfilePic FROM user_acct WHERE ID = ?";
        $getProfilePicPathStmt = $db->prepare($getProfilePicPathSql);
        $getProfilePicPathStmt->bind_param('i', $id);
        $getProfilePicPathStmt->execute();
        $profilePicResult = $getProfilePicPathStmt->get_result();

        if ($profilePicResult->num_rows === 1) {
            $row = $profilePicResult->fetch_assoc();

            // Use __DIR__ to get the absolute path of the script directory
            $profilePicPath = '' . $row['ProfilePic'];

            // Check if the user has a profile picture
            if (file_exists($profilePicPath)) {
                // Remove the existing profile picture file
                if (unlink($profilePicPath)) {
                    // Update the database to set ProfilePic to NULL
                    $updateProfilePicSql = "UPDATE user_acct SET ProfilePic = NULL WHERE ID = ?";
                    $updateProfilePicStmt = $db->prepare($updateProfilePicSql);
                    $updateProfilePicStmt->bind_param('i', $id);
                    $updateProfilePicStmt->execute();

                    if ($updateProfilePicStmt->affected_rows >= 1) {
                        echo "<script>alert('Profile picture removed successfully!'); window.location = '../settings.php';</script>";
                    } else {
                        echo "Failed to update profile picture record in the database";
                    }
                } else {
                    echo "Failed to remove the profile picture file";
                }
            } else {
                echo "<script>alert('No existing profile picture!'); window.location = '../settings.php';</script>";
            }
        } else {
            echo "User not found";
        }
    }

    // Inside the addWallpaper method in the UserAuth class

    public function addWallpaper($WallpaperID, $Uploader, $Title, $WallpaperLocation)
    {
        $allowedFileTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];

        $con = $this->db->getConnection();

        $wallpaper = $_FILES['new_wallpaper'];
        $wallpaper_temp = $wallpaper['tmp_name'];

        // Check if the uploaded file is of an allowed type
        if (!in_array($wallpaper['type'], $allowedFileTypes)) {
            echo "<script>alert('Invalid file type. Only JPG, PNG, and GIF files are allowed.'); window.location = '../dashboard.php';</script>";
            exit;
        }

        $WallpaperLocation = "upload/" . $wallpaper['name'];
        move_uploaded_file($wallpaper_temp, $WallpaperLocation);

        $sql = "INSERT INTO wallpaper (WallpaperID, Uploader, Title, WallpaperLocation) VALUES ('',?, ?, ?)";
        $query = $this->db->prepare($sql);

        $insertParams = [&$Uploader, &$Title, &$WallpaperLocation]; // Include UploaderEmail as the first parameter

        // Bind parameters using foreach loop
        $paramTypes = str_repeat('s', count($insertParams)); // 's' for string
        $query->bind_param($paramTypes, ...$insertParams);

        $result = $query->execute();

        if ($result) {
            echo "<script>alert('Wallpaper Added!'); window.location = '../dashboard.php';</script>";
        } else {
            echo "<script>alert('Upload Error'); window.location = '../register.php';</script>";
        }
        $query->close();
    }

    public function updateWallpaper($WallpaperID, $Title, $NewWallpaper)
    {
        $allowedFileTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];

        $con = $this->db->getConnection();

        // Check if a new wallpaper is being uploaded
        if (!empty($NewWallpaper['name'])) {
            $newWallpaper_temp = $NewWallpaper['tmp_name'];

            // Check if the uploaded file is of an allowed type
            if (!in_array($NewWallpaper['type'], $allowedFileTypes)) {
                echo "<script>alert('Invalid file type. Only JPG, PNG, and GIF files are allowed.'); window.location = '../dashboard.php';</script>";
                exit;
            }

            $WallpaperLocation = "upload/" . $NewWallpaper['name'];
            move_uploaded_file($newWallpaper_temp, $WallpaperLocation);

            // Use UPDATE query to modify existing wallpaper information
            $sql = "UPDATE wallpaper SET Title = ?, WallpaperLocation = ? WHERE WallpaperID = ?";
            $query = $this->db->prepare($sql);

            $insertParams = [&$Title, &$WallpaperLocation, &$WallpaperID];
            $paramTypes = "ssi"; // 's' for string, 'i' for integer
            $query->bind_param($paramTypes, ...$insertParams);

            $result = $query->execute();

            if ($result) {
                echo "<script>alert('Wallpaper Updated!'); window.location = '../dashboard.php';</script>";
            } else {
                echo "<script>alert('Update Error'); window.location = '../register.php';</script>";
            }
        } else {
            // If no new image is uploaded, only update the title
            $sql = "UPDATE wallpaper SET Title = ? WHERE WallpaperID = ?";
            $query = $this->db->prepare($sql);

            $insertParams = [&$Title, &$WallpaperID];
            $paramTypes = "si"; // 's' for string, 'i' for integer
            $query->bind_param($paramTypes, ...$insertParams);

            $result = $query->execute();

            if ($result) {
                // Check if any changes were made
                $changesMade = $query->affected_rows > 0;

                if ($changesMade) {
                    echo "<script>alert('Wallpaper Title Updated!'); window.location = '../dashboard.php';</script>";
                } else {
                    echo "<script>alert('No changes made.'); window.location = '../editWallpaper.php?WallpaperID=$WallpaperID';</script>";
                }
            } else {
                echo "<script>alert('Update Error'); window.location = '../register.php';</script>";
            }
        }
        $query->close();
    }



    public function deleteWallpaper($WallpaperId)
    {
        $WallpaperId = filter_var($WallpaperId, FILTER_VALIDATE_INT);

        if ($WallpaperId === false || $WallpaperId <= 0) {
            echo "invalid wallpaper";
            exit;
        }

        $deleteSql = "DELETE FROM wallpaper WHERE WallpaperID = ?";
        $deleteStmt = $this->db->getConnection()->prepare($deleteSql);
        $deleteStmt->bind_param("i", $WallpaperId);
        $deleteResult = $deleteStmt->execute();
        $deleteStmt->close();

        if ($deleteResult) {
            $imagePath = './accountProcess/upload/' . $WallpaperId;
            if (file_exists($imagePath)) {
                unlink($imagePath); // Delete the file
                echo "deleted";
            } else {
                echo "<script>alert('Wallpaper deleted successfully'); window.location = '../dashboard.php';</script>";
                exit;
            }
        }
    }

    public function logout()
    {
        echo "<script>alert('Logout Successful'); window.location = '../index.php';</script>";
        session_unset();
        session_destroy();
    }
}

if ($databaseConnection->getConnection()) {
    $userAuth = new UserAuth($databaseConnection);


    if (isset($_POST['login'])) {
        $userAuth->login($_POST['email'], $_POST['password']);
    }

    if (isset($_POST['register'])) {
        $userAuth->register(
            $_POST['first_name'],
            $_POST['middle_name'],
            $_POST['last_name'],
            $_POST['email'],
            $_POST['password'],
            $_POST['phone_number'],
            $_POST['country'],
            $_POST['province'],
            $_POST['citycity'],
            $_POST['district'],
            $_POST['house_no_street'],
            $_POST['zipcode'],
            NULL,
            NULL // Or provide the correct key for the last parameter if applicable
        );
    }

    if (isset($_POST['send_code_reset'])) {
        $userAuth->resetSelectEmail(
            $_POST['email']
        );
    }

    if (isset($_POST['reset_password'])) {
        $userAuth->resetPassword(
            $_POST['email'],
            $_POST['password'],
            $_POST['confirmPassword'],
        );
    }

    if (isset($_POST['logout'])) {
        $userAuth->logout();
    }

    if (isset($_POST['update_profile'])) {
        $id = $_SESSION['id'];
        $userAuth->editInformation(
            $id,
            $_POST['first_name'],
            $_POST['middle_name'],
            $_POST['last_name'],
            $_POST['email'],
            $_POST['password'],
            $_POST['phone_number'],
            $_POST['country'],
            $_POST['province'],
            $_POST['citycity'],
            $_POST['district'],
            $_POST['house_no_street'],
            $_POST['zipcode'],
            $_FILES['profile_pic']
        );
    }
    if (isset($_POST['remove_pic'])) {
        $id = $_SESSION['id'];
        $userAuth->removeProfilePicture($id);
    }

    

// Check if the add_wallpaper form was submitted
if (isset($_POST['add_wallpaper'])) {
    $email = $_POST['email'];
    $id = $_SESSION['id'];
    $userAuth->addWallpaper(
        $id,
        $email,
        $_POST['title'],
        $_FILES['new_wallpaper'],
         // Pass the email to the addWallpaper method
    );
}

    if (isset($_POST['edit_wallpaper'])) {
        $id = $_SESSION['id'];
        $userAuth->updateWallpaper(
            $_POST['WallpaperID'],  // Assuming WallpaperID is available in the form
            $_POST['title'],
            $_FILES['new_wallpaper']
        );
    }


    if (isset($_POST['delete_wallpaper'])) {
        $WallpaperIdToDelete = $_POST['WallpaperID'];
        $userAuth->deleteWallpaper($WallpaperIdToDelete);
    }
} else {
    echo "Error: Database connection not established.";
}
