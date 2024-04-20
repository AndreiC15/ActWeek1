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
            $stmt->bind_param('i', $id);
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
    <title>Dashboard</title>
    <link rel="stylesheet" href="pagesCSS/dashboard.css">

    <style>
        .image-list {
            list-style: none;
            padding: 5;
            margin: 5;
            display: flex;
            flex-wrap: wrap;
            justify-content: space-around;
            margin-top: 20px;
            /* Adjust the margin as needed */
        }

        .image-item {
            flex: 0 0 30%;
            /* Adjust the width of each item as needed */
            margin-bottom: 20px;
            /* Adjust the margin for space between images */
        }

        .image-container {
            width: 100%;
            margin-left: -15.5%;
        }

        .image-container img {
            width: 100%;
            height: auto;
        }

        .deleteBtn {
            margin-left: -15.5%;
        }

        .editBtn {
            width: 100%;
            height: 10px;
            color: white;
            padding: 2px 30px 2px 30px;
            background-color: blue;
            cursor: pointer;
            font-size: 14px;
            font-family: arial;
            border-style: solid;
            border-width: 1.5px;
            border-color: gray;
        }

        input {
            cursor: pointer;
        }

        table {
            margin-left: -20%;
        }
    </style>
</head>

<body>

    <div class="navBarTop">
        <h1>Dashboard</h1>
    </div>
    <center>
    <div class="area"></div>
    <nav class="main-menu">
            <ul>
                <li>
                    <i class="fa fa-info fa-2x"><img class="navSideIconLogo" src="testImages/icon.png"></i>
                    <span class="nav-text">WallpaperStation</span>
                </li>
                <!-- Add your other menu items here -->
                <li>
                    <a href="homepage.php">
                        <i class="fa fa-info fa-2x"><img class="navSideIcon" src="testImages/home.png"></i>
                        <span class="nav-text">Home</span>
                    </a>
                </li>
                <li>
                    <a href="dashboard.php">
                        <i class="fa fa-info fa-2x"><img class="navSideIcon" src="testImages/dashboard.png"></i>
                        <span class="nav-text">Dashboard</span>
                    </a>
                </li>
                <li>
                    <a href="settings.php">
                        <i class="fa fa-info fa-2x"><img class="navSideIcon" src="testImages/setting.png"></i>
                        <span class="nav-text">Account Settings</span>
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
    <center>
        <a href="uploadWallpaper.php">
            <input style="font-size:15px" class="uploadContainer" type="button" value="Upload Wallpaper">
        </a>
        <fieldset>
            <h2 style="margin-left:-2.5%;margin-top:-1%">My Uploaded Wallpapers</h2>
            <?php
            $sql = "SELECT WallpaperID, Title, WallpaperLocation FROM wallpaper ORDER BY WallpaperID DESC";

            $result = $databaseConnection->getConnection()->query($sql);

            if ($result->num_rows >= 1) {
                echo '<ul class="image-list">';
                while ($row = $result->fetch_assoc()) {
                    $imagePath = 'accountProcess/' . $row['WallpaperLocation'];

                    if (file_exists($imagePath)) {
                        echo '<li class="image-item">';
                        echo '<div class="image-container">';
                        echo '<img style="width:400px;height:230px;object-fit:cover " src="' . $imagePath . '" alt="' . htmlspecialchars($row['Title']) . '">';

                        // Updated form to include an anchor tag for "Edit" functionality
                        echo '<form method="post" action="./accountProcess/process.php">';
                        echo '<input type="hidden" name="WallpaperID" value="' . $row['WallpaperID'] . '">';
                        echo '<p style="color: white;">' . $row['Title'] . '</p>';
                        echo '<table>';
                        echo '<tr>';
                        echo '<td><a class="editBtn" href="editWallpaper.php?WallpaperID=' . $row['WallpaperID'] . '">Edit</a><td>';
                        echo '<td><input style="background-color:red;color:white;width:150%" type="submit" name="delete_wallpaper" value="Delete"></td>';
                        echo '</tr>';
                        echo '</table>';
                        echo '</form>';

                        echo '</div>';
                        echo '</li>';
                    } else {
                        echo '<li class="image-item">';
                        echo '<div class="image-container">';
                        echo '<p style="color: red;">Image not found: ' . $row['Title'] . '</p>';
                        echo '</div>';
                        echo '</li>';
                    }
                }
                echo '</ul>';
            } else {
                echo '<div style="text-align: center; padding: 5px; background-color: #f0f0f0; border: 1px solid #ccc; width:50%">';
                echo '<p style="font-size: 18px; color: #333;margin-left:-1%">You haven\'t uploaded any wallpaper yet.</p>';
                echo '</div>';
            }
            ?>
        </fieldset>
    </center>
</body>

</html>