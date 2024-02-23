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

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <link rel="stylesheet" href="pagesCSS/HomepageStyle.css">
    <style>
        .image-list {
            list-style: none;
            padding: 5;
            margin: 5;
            display: flex;
            flex-wrap: wrap;
            justify-content: space-around;
            margin-top: 20px;
            margin-left: -3%;
        }

        .image-item {
            flex: 0 0 30%;
            margin-bottom: 10px;
        }

        .image-container {
            width: 100%;
        }

        .image-container img {
            width: 100%;
            height: auto;
        }

        .dl_Btn {
            width: fit-content;
            height: 20px;
            padding: 5px;
            background-color: red;
            border-radius: 50px;
            color: white;
            cursor: pointer;
            text-decoration: none;
        }

        a {
            color: white;
            text-decoration: none;
        }

        .pagination {
            display: inline-block;
            padding: 8px 16px;
            margin: 4px;
            border-radius: 5px;
            text-decoration: none;
            color: black;
            background-color: #f2f2f2;
        }

        .pagination.active {
            background-color: #4CAF50;
            color: white;
        }
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
        <p style="font-family: rockwell;color:white;">Hello <b style="font-size:20px;"><u><?php echo $userData['FirstName']; ?></u></b>&nbsp;!, this is my sample website. Contact me in my <a style="color:white;" href="https://www.facebook.com/atc11502" target="_blank">Facebook account</a> if you have any inquiries</p>
        <h2>Popular HD Wallpaper</h2>
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
        <fieldset>
            <?php
            $limit = 6; // Number of wallpapers to display per page

            // Calculate the offset based on the current page
            $currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $offset = ($currentPage - 1) * $limit;

            // Query to count total wallpapers
            $countQuery = "SELECT COUNT(*) as total FROM wallpaper";
            $totalResult = $databaseConnection->getConnection()->query($countQuery);
            $totalWallpapers = $totalResult->fetch_assoc()['total'];

            $sql = "SELECT WallpaperID, Title, WallpaperLocation FROM wallpaper LIMIT $offset, $limit";
            $result = $databaseConnection->getConnection()->query($sql);

            // Check if there are no wallpapers
            if ($result->num_rows >= 1) {
                echo '<ul class="image-list" id="wallpaperList">';
                while ($row = $result->fetch_assoc()) {
                    $imagePath = 'accountProcess/' . $row['WallpaperLocation'];

                    if (file_exists($imagePath)) {
                        echo '<li class="image-item">';
                        echo '<div class="image-container">';
                        echo '<img style="width:400px;height:230px; " src="' . $imagePath . '" alt="' . htmlspecialchars($row['Title']) . '">';
                        echo '<p style="color: white;text-transform: capitalize;">' . $row['Title'] . '</p>';
                        echo '</div>';
                        echo '<div class="dl_Btn">';
                        echo '<a style="display:flex;padding-left:5px;padding-right:5px; font-family:arial;" href="' . $imagePath . '" download="' . htmlspecialchars($row['Title']) . '">
                        <img style="filter: invert(100%);padding-right:5px" src="testImages/download.png" width="20" height="20"> Download</a>';
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

                // Pagination links
                echo '<center>';
                $totalPages = ceil($totalWallpapers / $limit);

                for ($page = 1; $page <= $totalPages; $page++) {
                    $isActive = ($page == $currentPage) ? 'active' : '';
                    echo '<a href="?page=' . $page . '" class="pagination ' . $isActive . '">' . $page . '</a>';
                }

                echo '</center>';
            } else {
                echo '<div style="text-align: center; padding: 5px; background-color: #f0f0f0; border: 1px solid #ccc; width:50%">';
                echo '<p style="font-size: 18px; color: #333;margin-left:-1%">No uploaded wallpapers&#128531</p>';
                echo '</div>';
            }
            ?>
        </fieldset>

        <script>
            var offset = <?php echo $currentPage * $limit; ?>;

            function showMore() {
                var xhr = new XMLHttpRequest();
                var spinner = document.getElementById("loadingSpinner");

                xhr.onreadystatechange = function() {
                    if (xhr.readyState == 4 && xhr.status == 200) {
                        document.getElementById("wallpaperList").innerHTML += xhr.responseText;
                        offset += <?php echo $limit; ?>;
                        spinner.style.display = "none";
                    }
                };

                spinner.style.display = "block";
                xhr.open("GET", "load_more.php?offset=" + offset, true);
                xhr.send();
            }
        </script>
</body>

</html>