<?php
require_once 'connect.php';

class UserAuth {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function login($email, $password) {
    $con = $this->db->getConnection();

    // Check if user is already logged in
    if (isset($_SESSION['login']) && $_SESSION['login'] === true) {
        echo "<script>alert('You are already logged in.'); window.location = '../homepage.php';</script>";
        exit;
    }

    $result = mysqli_query($con, "SELECT * FROM user_acct WHERE email = '$email'");
    $row = mysqli_fetch_assoc($result);

    if (mysqli_num_rows($result) > 0) {
        if ($password == $row['Password']) {
            $_SESSION['login'] = true;
            $_SESSION['id'] = $row['ID'];
            echo "<script>alert('Log In Successfully'); window.location = '../homepage.php';</script>";
            exit;
        } else {
            echo "<script>alert('Wrong Email or Password'); window.location = '../index.php';</script>";
            exit;
        }
    } else {
        echo "<script>alert('User not found'); window.location = '../index.php';</script>";
        exit;
    }
}


    public function register($FirstName, $MiddleName, $LastName, $Email, $Password,$Country, $Province, $CityCity, $District, $HouseNoStreet, $ZipCode,$PhoneNumber) {
        $con = $this->db->getConnection();

        $checkEmailDuplicate = "SELECT * FROM user_acct WHERE Email = '$Email'";
        $checkEmailResult = mysqli_query($con, $checkEmailDuplicate);

        if (mysqli_num_rows($checkEmailResult) > 0) {
            echo "<script>alert('Email Already Exists'); window.location = '../register.php';</script>";
            exit;
        } else {
            $sql = "INSERT INTO user_acct (ID, FirstName, MiddleName, LastName, Email, Password, Country, Province, CityCity, District, HouseNoStreet, ZipCode, PhoneNumber) VALUES ('', '$FirstName', '$MiddleName', '$LastName', '$Email', '$Password', '$Country', '$Province', '$CityCity', '$District', '$HouseNoStreet', '$ZipCode', '$PhoneNumber')";

            if (mysqli_query($con, $sql)) {
                echo "<script>alert('Registered Successfully, please proceed to login page'); window.location = '../index.php';</script>";
            } else {
                echo "Error: " . $sql . "<br>" . mysqli_error($con);
            }
        }
    }

    public function logout() {
        $_SESSION = [];
        echo "<script>alert('Logout Successful'); window.location = '../index.php';</script>";
        session_unset();
        session_destroy();
    }

    public function editInformation($id, $FirstName, $MiddleName, $LastName, $Email, $Password, $Country, $Province, $CityCity, $District, $HouseNoStreet, $ZipCode, $PhoneNumber) {
        $query = $this->db->prepare("UPDATE user_acct SET FirstName = ?, MiddleName = ?, LastName = ?, Email = ?, Password = ?, Country = ?, Province = ?, CityCity = ?, District = ?, HouseNoStreet = ?, ZipCode = ?, PhoneNumber = ? WHERE ID = ?");
        
        $query->bind_param('sssssssssssi', $FirstName, $MiddleName, $LastName, $Email, $Password, $Country, $Province, $CityCity, $District, $HouseNoStreet, $ZipCode, $PhoneNumber, $id);
        $query->execute();
    
        if ($query->affected_rows > 0) {
            // Redirect after successful update
            header("Location: ../homepage.php");
            exit;
        } else {
            // Handle update failure
            echo "<script>alert('Failed to update user information');</script>";
        }
    }
    
    
}

if ($databaseConnection->getConnection()) {
    $userAuth = new UserAuth($databaseConnection);

    if (!empty($_SESSION['id'])) {
        header("Location: ../homepage.php");
    }

    if (isset($_POST['login'])) {
        $userAuth->login($_POST['email'], $_POST['password']);
    }

    if (isset($_POST['register'])) {
        $userAuth->register($_POST['first_name'], $_POST['middle_name'], $_POST['last_name'], $_POST['email'], $_POST['password'], $_POST['country'], $_POST['province'], $_POST['citycity'], $_POST['district'], $_POST['house_no_street'], $_POST['zip_code'], $_POST['phone_number']);
    }

    if (isset($_POST['logout'])) {
        $userAuth->logout();
    }

if (isset($_POST['update_profile'])) {// Assuming you have a session for user ID
        $success = $userAuth->editInformation(
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
            $_POST['zip_code']
        );

        if ($success) {
            echo "<script>alert('User information updated successfully');</script>";
        } else {
            echo "<script>alert('Failed to update user information');</script>";
        }
    }

} else {
    echo "Error: Database connection not established.";
}
?>
