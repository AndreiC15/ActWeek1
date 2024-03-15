<!DOCTYPE html>
<html lang="en">
<!DOCTYPE html>
<html lang="en">
<?php
require_once 'accountProcess/connect.php';

class UserProfile
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function getUserProfile($id)
    {
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
    <title>Upload</title>
    <link rel="stylesheet" href="pagesCSS/uploadWallpaper.css">
</head>

<body>

    <div class="navBarTop">
        <h1>UPLOAD</h1>
    </div>

    <div class="area"></div>
    <nav class="main-menu">
        <center>
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
        </center>
        <ul class="logout">
            <li>
                <a href="#">
                    <i class="fa fa-info fa-2x"><img class="navSideIcon" src="testImages/shutdown.png"></i>
                    <span class="nav-text">
                        <center>
                            <div class="LogoutButton">
                                <form method="POST" action="./accountProcess/process.php">
                                    <input style="width: 100%; max-width: 100px; height: 30px; background-color: red; border-radius: 50px; color: white;cursor: pointer;" type="submit" id="logout" name="logout" value="Logout">
                        </center>
                        </form>
                        </div>
                    </span>
                </a>
            </li>
        </ul>
    </nav>
    </br></br>
    <fieldset>
        <center>
            <div class="divider">
                <h2 style="margin-left:-2.5%;color:black;">Upload Wallpaper</h2>
            </div>
            <table>
                <form method="POST" action="./accountProcess/process.php" enctype="multipart/form-data">
                    <center>
                        <tr>
                            </br>
                            <td>Title:</td>
                            <td><input class="titleText" type="text" name="title" id="title" required></td>
                        </tr>
                        <tr>
                            <td>Upload image:</td>
                            <td><input class="titleText" id="new_wallpaper" type="file" name="new_wallpaper" accept=".jpg, .jpeg, .png, .gif" onchange="PreviewImage();" required /></td>
                        </tr>
                    </center>
            </table>

            <div id="imagePreviewContainer" style="display: none;">
                Image Preview:</br></br>
                <img id="uploadPreview" style="width: 550px;"></br></br>
            </div>

            <input type="submit" name="add_wallpaper" id="add_wallpaper">
        </center>
        </form>
    </fieldset>


    <script type="text/javascript">
        function PreviewImage() {
            var oFReader = new FileReader();
            var previewContainer = document.getElementById("imagePreviewContainer");

            oFReader.readAsDataURL(document.getElementById("new_wallpaper").files[0]);

            oFReader.onload = function(oFREvent) {
                document.getElementById("uploadPreview").src = oFREvent.target.result;
                previewContainer.style.display = "block"; // Show the preview container
            };
        };
    </script>


</body>

</html>
