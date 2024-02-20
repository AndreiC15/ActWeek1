<!DOCTYPE html>
<html lang="en">
<?php
require_once 'accountProcess/connect.php';

class UserProfile {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function getUserProfile($id) {
        try {
            $stmt = $this->db->getConnection()->prepare("SELECT * FROM user_acct WHERE id = ?");
            $stmt->bind_param('i', $id); // 'i' indicates integer type
            $stmt->execute();
            $result = $stmt->get_result();

            $userData = $result->fetch_assoc();


            if (!$userData) {
                echo "<script>alert('No login session'); window.location = 'index.php';</script>";
                exit();
            }

            return $userData;
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage();
            die();
        }
    }
}

// Check if the database connection is established
if ($databaseConnection->getConnection()) {
    $userProfile = new UserProfile($databaseConnection);

    if (!empty($_SESSION['id'])) {
        $id = $_SESSION['id'];
        $userData = $userProfile->getUserProfile($id);
    } else {
        echo "<script>alert('Logout successfully'); window.location = 'index.php';</script>";
        exit();
    }
} else {
    echo "Error: Database connection not established.";
}
?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Settings</title>
    <link rel="stylesheet" href="pagesCSS/Settings.css">
    <link rel="stylesheet" href="pagesCSS/UserProfileSettings.css">
</head>

<body>
    <center>  
    <div class="area"></div><nav class="main-menu">
    <ul>
                <li>
                       <i class="fa fa-info fa-2x"><img class="navSideIconLogo" src="testImages/icon.png"></i>
                        <span class="nav-text">
                            WallpaperStation
                        </span>
                    </a>
                </li>
            </ul>
            <ul>
                <li>
                    <a href="homepage.php">
                       <i class="fa fa-info fa-2x"><img class="navSideIcon" src="testImages/home.png"></i>
                        <span class="nav-text">
                            Home
                        </span>
                    </a>
                </li>
            </ul>
            <ul>
                <li>
                    <a href="dashboard.php">
                       <i class="fa fa-info fa-2x"><img class="navSideIcon" src="testImages/dashboard.png"></i>
                        <span class="nav-text">
                            Dashboard
                        </span>
                    </a>
                </li>
            </ul>
            <ul>
                <li>
                    <a href="settings.php">
                       <i class="fa fa-info fa-2x"><img class="navSideIcon" src="testImages/setting.png"></i>
                        <span class="nav-text">
                            Account Settings
                        </span>
                    </a>
                </li>
            </ul>

            <ul class="logout">
                <li>
                   <a href="#">
                   <i class="fa fa-info fa-2x"><img class="navSideIcon" src="testImages/shutdown.png"></i>
                   <span class="nav-text">
                        <div class="LogoutButton">
                        <form method="POST" action="./accountProcess/process.php">
                <input style="width: 100%; max-width: 100px; height: 30px; background-color: red; border-radius: 50px; color: white;cursor: pointer;" type="submit" id="logout" name="logout" value="Logout">
            </form>  
        </div>
                   </span>
                    </a>
                </li>  
            </ul>
        </nav>
        </center>

    <div class="navBarTop">
        <h1>Account Settings</h1>   
    </div>

    
    <div class="profileContainer">
    <h2>User Profile</h2>
        <div class="profileContainerInside">
            <div class="profilePic">
                <form enctype="multipart/form-data" method="post" action="./accountProcess/process.php">
                <?php
function displayProfilePicture($userId, $db) {
    // Prepare and execute the SQL query to get the user's profile picture path
    $getProfilePicPathSql = "SELECT ProfilePic FROM user_acct WHERE ID = ?";
    $getProfilePicPathStmt = $db->prepare($getProfilePicPathSql);
    $getProfilePicPathStmt->bind_param('i', $userId);
    $getProfilePicPathStmt->execute();
    $profilePicResult = $getProfilePicPathStmt->get_result();

    // Check if the query returned a result
    if ($profilePicResult->num_rows === 1) {
        $row = $profilePicResult->fetch_assoc();
        $profilePicPath = 'accountProcess/' . $row['ProfilePic'];

        // Check if the user has a profile picture and the file exists
        if (!empty($row['ProfilePic']) && file_exists($profilePicPath)) {
            echo '<div class="profilePic">';
            echo '<img class="userImage" src="' . $profilePicPath . '" alt="user profile">';
            echo '</div>';
        } else {
            // No profile picture found or invalid path, display default image
            echo '<div class="profilePic">';
            echo '<img style="position:relative;width:120px;height:120px;margin-left:-25%" src="./testImages/user.png" alt="user profile">';
            echo '</div>';
        }
    } else {
        // User not found, display default image
        echo '<div class="profilePic">';
        echo '<img style="position:relative;width:120px;height:120px;margin-left:-25%" src="./testImages/user.png" alt="user profile">';
        echo '</div>';
    }
}

// Usage
displayProfilePicture($_SESSION['id'], $databaseConnection->getConnection());
?>


            </br>
            <input type="file" id="profile_pic" name="profile_pic">
            <input style="position:absolute;margin-top:2.5%;margin-left:-18%" name="remove_pic" id="remove_pic" type=submit value="Remove picture">
            </div>
            <div class="dividerTop"></div>
            <div class="dividerProfile"></div>
        <table class="userProfile">
            <tr>
                <td>First Name:</td>
                <td><input type="text" id="first_name" name="first_name" placeholder="<?php echo $userData['FirstName']; ?>"></td>
                <td>Middle Name:</td>
                <td><input type="text" id="middle_name" name="middle_name" placeholder="<?php echo $userData['MiddleName']; ?>"></td>
                <td>Last Name:</td>
                <td><input type="text" id="last_name" name="last_name" placeholder="<?php echo $userData['LastName']; ?>"></td>
            </tr>
                <tr>
                <td>Email:</td>
                <td><input type="text" id="email" name="email" placeholder="<?php echo $userData['Email']; ?>"></td>
                <td>Password:</td>
                <td><input type="password" id="password" name="password"></td>
                <td>Phone Number:</td>
                <td><input type="number" id="phone_number" name="phone_number" placeholder="<?php echo $userData['PhoneNumber']; ?>"></td> 
            </tr>
            <tr>  
            <td>Country:</td>
                <td><input type="text" id="country" name="country" placeholder="<?php echo $userData['Country']; ?>"></td>
                <td>Province:</td>
                <td><input type="text" id="province" name="province" placeholder="<?php echo $userData['Province']; ?>"></td>
                <td>City/Municipality:</td>
                <td><input type="text" id="citycity" name="citycity" placeholder="<?php echo $userData['CityCity']; ?>"></td>
            </tr>
            <tr>
            <td>District:</td>
                <td><input type="text" id="district" name="district" placeholder="<?php echo $userData['District']; ?>"></td>
                <td>House No. & Street</td>
                <td><input type="text" id="house_no_street" name="house_no_street" placeholder="<?php echo $userData['HouseNoStreet']; ?>">  
                <td>Zip Code:</td>
                <td><input type="text" id="zipcode" name="zipcode" placeholder="<?php echo $userData['ZipCode']; ?>" >
            </tr>
            <input class="editBtn" type="submit" id="update_profile" name="update_profile" value="Save Changes">
            </form>
        </table>
        </div>
    </div>
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