<?php
require_once 'connect.php';


class UserAuth {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function login($Email, $password) {
        $con = $this->db->getConnection();
    
        if (isset($_SESSION['login']) && $_SESSION['login'] === true) {
            echo "<script>alert('You are already logged in.'); window.location = '../homepage.php';</script>";
            exit;
        }
    
        $result = mysqli_query($con, "SELECT * FROM user_acct WHERE email = '$Email'");
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


    public function register($FirstName, $MiddleName, $LastName, $Email, $Password, $PhoneNumber, $Country, $Province, $CityCity, $District, $HouseNoStreet, $ZipCode) {
        $con = $this->db->getConnection();
    
        // Check if email already exists
        $checkEmailDuplicate = "SELECT * FROM user_acct WHERE Email = ?";
        $stmtCheckEmail = $this->db->prepare($checkEmailDuplicate);
        $stmtCheckEmail->bind_param("s", $Email);
        $stmtCheckEmail->execute();
        $checkEmailResult = $stmtCheckEmail->get_result();
    
        if ($checkEmailResult->num_rows > 0) {
            echo "<script>alert('Email Already Exists'); window.location = '../register.php';</script>";
            exit;
        }

        $sql = "INSERT INTO user_acct (FirstName, MiddleName, LastName, Email, Password, PhoneNumber, Country, Province, CityCity, District, HouseNoStreet, ZipCode) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $query = $this->db->prepare($sql);
    
        $insertParams = [&$FirstName, &$MiddleName, &$LastName, &$Email, &$Password, &$PhoneNumber, &$Country, &$Province, &$CityCity, &$District, &$HouseNoStreet, &$ZipCode];
    
        // Bind parameters using foreach loop
        $paramTypes = str_repeat('s', count($insertParams)); // 's' for string
        $query->bind_param($paramTypes, ...$insertParams);

        $result = $query->execute();

        if ($result) {
            echo "<script>alert('Registered Successfully, please proceed to login page'); window.location = '../index.php';</script>";
        } else {
            echo "<script>alert('Error in registration'); window.location = '../register.php';</script>";
        }
        $query->close();
    }

    // ORIG ==========================
     public function editInformation($id, $FirstName, $MiddleName, $LastName, $Email, $Password, $PhoneNumber, $Country, $Province, $CityCity, $District, $HouseNoStreet, $ZipCode) {
        $db = $this->db->getConnection();
        $fields = ['FirstName', 'MiddleName', 'LastName', 'Email', 'Password', 'PhoneNumber', 'Country', 'Province', 'CityCity', 'District', 'HouseNoStreet', 'ZipCode'];
        $id = $db->real_escape_string($id);
    
        foreach ($fields as $field) {
            $$field = $db->real_escape_string($$field);
        }
    
        $sql = "UPDATE user_acct SET ";
        $updateFields = [];
        $params = [];
        
        foreach ($fields as $field) {
            if (!empty($$field)) {
                $updateFields[] = "$field = ?";
                $params[] = $$field;
            }
        }
    
        $sql .= implode(", ", $updateFields);
        $sql .= " WHERE ID = ?";
        
        // Add the ID to the parameters
        $params[] = $id;
    
        $stmt = $db->prepare($sql);
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
    }  

    
    
    // public function editInformation($id, $FirstName, $MiddleName, $LastName, $Email, $Password, $PhoneNumber, $Country, $Province, $CityCity, $District, $HouseNoStreet, $ZipCode) {
    //     // Retrieve the mysqli object from the DatabaseConnection
    //     $db = $this->db->getConnection();
    
    //     // Sanitize user inputs to prevent SQL injection
    //     $id = $db->real_escape_string($id);
    //     $FirstName = $db->real_escape_string($FirstName);
    //     $MiddleName = $db->real_escape_string($MiddleName);
    //     $LastName = $db->real_escape_string($LastName);
    //     $Email = $db->real_escape_string($Email);
    //     $Password = $db->real_escape_string($Password);
    //     $PhoneNumber = $db->real_escape_string($PhoneNumber);
    //     $Country = $db->real_escape_string($Country);
    //     $Province = $db->real_escape_string($Province);
    //     $CityCity = $db->real_escape_string($CityCity);
    //     $District = $db->real_escape_string($District);
    //     $HouseNoStreet = $db->real_escape_string($HouseNoStreet);
    //     $ZipCode = $db->real_escape_string($ZipCode);
    
    //     // Construct the SQL query
    //     $sql = "UPDATE user_acct SET ";
    //     $updateFields = [];
    
    //     // Check each field if it is provided and not empty
    //     if (!empty($FirstName)) $updateFields[] = "FirstName = '$FirstName'";
    //     if (!empty($MiddleName)) $updateFields[] = "MiddleName = '$MiddleName'";
    //     if (!empty($LastName)) $updateFields[] = "LastName = '$LastName'";
    //     if (!empty($Email)) $updateFields[] = "Email = '$Email'";
    //     if (!empty($Password)) $updateFields[] = "Password = '$Password'";
    //     if (!empty($PhoneNumber)) $updateFields[] = "PhoneNumber = '$PhoneNumber'";
    //     if (!empty($Country)) $updateFields[] = "Country = '$Country'";
    //     if (!empty($Province)) $updateFields[] = "Province = '$Province'";
    //     if (!empty($CityCity)) $updateFields[] = "CityCity = '$CityCity'";
    //     if (!empty($District)) $updateFields[] = "District = '$District'";
    //     if (!empty($HouseNoStreet)) $updateFields[] = "HouseNoStreet = '$HouseNoStreet'";
    //     if (!empty($ZipCode)) $updateFields[] = "ZipCode = '$ZipCode'";
    
    //     // Join the update fields into the SQL query
    //     $sql .= implode(", ", $updateFields);
    
    //     // Add the WHERE clause
    //     $sql .= " WHERE ID = '$id'";
    
    //     $result = $db->query($sql);
    
    //     if ($result) {
    //         echo "<script>alert('User info updated!'); window.location = '../settings.php';</script>";
    //         exit;
    //     } else {
    //         echo "<script>alert('Failed to update user information'); window.location = '../settings.php';</script>";
    //     }
    // }
    
    

    public function logout() {
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
        $userAuth->register($_POST['first_name'], $_POST['middle_name'], $_POST['last_name'], $_POST['email'], $_POST['password'], $_POST['phone_number'], $_POST['country'], $_POST['province'], $_POST['citycity'], $_POST['district'], $_POST['house_no_street'], $_POST['zip_code']);
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
            $_POST['profile_pic']
        );
    }

} else {
    echo "Error: Database connection not established.";
}
?>
