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
        .image-container,
        .image-container1 {
            position: relative;
            display: flex;
            justify-content: center;
            flex-wrap: wrap; /* Ensure images wrap to the next line on smaller screens */
        }

        .zoom {
            transition: transform .2s; /* Animation */
            margin: 0 10px; /* Adjust spacing between images */
            position: relative;
            z-index: 1; /* Default z-index */
            flex: 1; /* Equal width for all images within the container */
        }

        .zoom:hover {
            -ms-transform: scale(1.2); /* IE 9 */
            -webkit-transform: scale(1.2); /* Safari 3-8 */
            transform: scale(1.2);
            z-index: 2; /* Higher z-index on hover */
        }

        /* Adjust fieldset width for smaller screens */
        fieldset {
            background-color: black;
            border-style: solid;
            border-radius: 25px;
            width: 90%;
            margin: 0 auto; /* Center the fieldset */
            box-sizing: border-box;
        }

        /* Adjust image width for smaller screens */
        .zoom a img {
            border-style: solid;
            border-color: white;
            width: 100%;
            max-width: 450px; /* Set maximum width for larger screens */
            height: auto;
        }

        /* Adjust LogoutButton style for smaller screens */
        .LogoutButton {
            
            vertical-align: middle;
        }

        .fa-2x {
        font-size: 2em;
        }
        .fa {
        position: relative;
        display: table-cell;
        width: 60px;
        height: 36px;
        text-align: center;
        vertical-align: middle;
        font-size:20px;
        }


        .main-menu:hover,nav.main-menu.expanded {
        width:250px;
        overflow:visible;
        }

        .main-menu {
        background:#212121;
        border-right:1px solid #e5e5e5;
        position:absolute;
        top:0;
        bottom:0;
        height:100%;
        left:0;
        width:60px;
        overflow:hidden;
        -webkit-transition:width .05s linear;
        transition:width .05s linear;
        -webkit-transform:translateZ(0) scale(1,1);
        z-index:1000;
        }

        .main-menu>ul {
        margin:7px 0;
        }

        .main-menu li {
        position:relative;
        display:block;
        width:250px;
        }

        .main-menu li>a {
        position:relative;
        display:table;
        border-collapse:collapse;
        border-spacing:0;
        color:#999;
        font-family: arial;
        font-size: 14px;
        text-decoration:none;
        -webkit-transform:translateZ(0) scale(1,1);
        -webkit-transition:all .1s linear;
        transition:all .1s linear;
        
        }

        .main-menu .nav-icon {
        position:relative;
        display:table-cell;
        width:60px;
        height:36px;
        text-align:center;
        vertical-align:middle;
        font-size:18px;
        }

        .main-menu .nav-text {
        position:relative;
        display:table-cell;
        vertical-align:middle;
        width:180px;

        }

        .main-menu>ul.logout {
        position:absolute;
        left:0;
        bottom:0;
        }

        .no-touch .scrollable.hover {
        overflow-y:hidden;
        }

        .no-touch .scrollable.hover:hover {
        overflow-y:auto;
        overflow:visible;
        }

        a:hover,a:focus {
        text-decoration:none;
        }

        nav {
        -webkit-user-select:none;
        -moz-user-select:none;
        -ms-user-select:none;
        -o-user-select:none;
        user-select:none;
        }

        nav ul,nav li {
        outline:0;
        margin:0;
        padding:0;
        }
        .main-menu li:hover>a,nav.main-menu li.active>a,.dropdown-menu>li>a:hover,.dropdown-menu>li>a:focus,.dropdown-menu>.active>a,.dropdown-menu>.active>a:hover,.dropdown-menu>.active>a:focus,.no-touch .dashboard-page nav.dashboard-menu ul li:hover a,.dashboard-page nav.dashboard-menu ul li.active a {
        color:#fff;
        background-color:#5fa2db;
        }
        .area {
        float: left;
        background: #e2e2e2;
        width: 100%;
        height: 100%;
        }
        .navSideIcon{
        filter: invert(1);
        width:20px;
        height: 20px;
        }
        .webIcon {
            background-color: blue;
            width: 400px;
            height: 80px;
            border-radius: 10px;
            display: flex;
            align-items: center; /* Align items vertically in the center */
            justify-content: center; /* Center content horizontally */
        }

        .webIcon p {
            color: white;
            font-size: 50px;
            margin: 0; /* Remove default margin */
        }

        .webIcon .hub {
            background-color: red;
            border-radius: 10px;
            color: white;
            margin-left: 5px; /* Add some space between "Wallpaper" and "Hub" */
        }

    </style>
</head>

<body>
    <center>
    <div class="webIcon">
        <p>Wallpaper</p>
        <div class="hub">
            <p style="padding: 0 10px 0 10px;">Station</p>
        </div>
    </div>
        <p style="font-family: rockwell;">Hello <b><?php echo $userData['FirstName']; ?></b>!, this is my sample website. Contact me in my <a href="https://www.facebook.com/atc11502" target="_blank">Facebook account</a> if you have any inquiries</p>

        <div class="area"></div><nav class="main-menu">
            
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
                            Settings
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
            <div class="image-container">
                <div class="zoom">
                    <a href="https://wallpaperswide.com/windows_10_sea-wallpapers.html" target="_blank">
                        <img src="testImages/wp3.jpg">
                    </a>
                </div>

                <div class="zoom">
                    <a href="https://www.wallpaperflare.com/landscape-4k-bliss-windows-xp-stock-wallpaper-uswgo" target="_blank">
                        <img src="testImages/wp4.jpg">
                    </a>
                </div>

                <div class="zoom">
                    <a href="https://wallpapers.com/wallpapers/valorant-player-roles-305kescxw5dpup7y.html" target="_blank">
                        <img src="testImages/wp6.jpg">
                    </a>
                </div>
            </div>

        </fieldset>

        
    </center>
</body>

</html>
