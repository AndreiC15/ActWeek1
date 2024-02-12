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
    <title>Home</title>
    <link rel="stylesheet" href="pagesCSS/HomepageStyle.css">
    <style>
        
        

    </style>
</head>
<body>
    <center>
    <div class="webIcon">
        <p class="webtitle">Wallpaper</p>
        <div class="hub">
            <p class="webtitle" style="padding: 0 10px 0 10px;">Station</p>
        </div>
    </div>
        <p style="font-family: rockwell;color:white;">Hello <b style="font-size:20px;"><u><?php echo $userData['FirstName']; ?></u></b>&nbsp;!, this is my sample website. Contact me in my <a style="color:white;"href="https://www.facebook.com/atc11502" target="_blank">Facebook account</a> if you have any inquiries</p>
        <h2>Popular HD Wallpaper</h2>
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
        <fieldset>
            <!-- <div class="image-container">
                <div class="zoom">
                    <a href="#" target="_blank">
                        <img src="testImages/wp3.jpg">
                    </a>
                    <p class="title">Valorant</p>
                </div>

                <div class="zoom">
                    <a href="#" target="_blank">
                        <img src="testImages/wp4.jpg">
                    </a>
                    <p class="title">Grand Theft Auto VI</p>
                </div>

                <div class="zoom">
                    <a href="#" target="_blank">
                        <img src="testImages/wp6.jpg">
                    </a>
                    <p class="title">Resident Evil 2</p>
                </div> 
                </div>  
            </div> -->

            <?php
// Assuming you have an array of image sources and titles
$imageSources = [
    "testImages/wp3.jpg",
    "testImages/wp4.jpg",
    "testImages/wp6.jpg",
    // Add more image sources as needed
];

$titles = [
    "Valorant",
    "Grand Theft Auto VI",
    "Resident Evil 2",
    // Add more titles as needed
];

// Loop through the array and generate the HTML
for ($k = 0; $k < 3; $k++) { // Loop vertically 3 times
    echo '<div class="image-container">';
    
    // Loop through the images and titles
    for ($i = 0; $i < count($imageSources); $i++) {
        echo '<div class="zoom">';
        echo '<a href="#" target="_blank">';
        echo '<img src="' . $imageSources[$i] . '">';
        echo '</a>';
        echo '<p class="title">' . $titles[$i] . '</p>';
        echo '</div>';
    }
    
    echo '</div>';
}
?>

        </fieldset>   
    </center>
</body>
</html>
