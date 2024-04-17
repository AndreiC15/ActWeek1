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
            background-color: white;
            border-radius: 50px;
            color: black;
            cursor: pointer;
            text-decoration: none;
        }

        a {
            color: black;
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

        <fieldset>
            <h2>Popular HD Wallpaper</h2>

            <!-- Dropdown menu for sorting -->
            <form id="sortForm" method="GET" action="homepage.php">
                <select name="sort" onchange="document.getElementById('sortForm').submit()">
                    <option value="latest" <?php if (isset($_GET['sort']) && $_GET['sort'] === 'latest') echo 'selected'; ?>>Sort by Latest</option>
                    <option value="oldest" <?php if (isset($_GET['sort']) && $_GET['sort'] === 'oldest') echo 'selected'; ?>>Sort by Oldest</option>
                    <option value="title" <?php if (isset($_GET['sort']) && $_GET['sort'] === 'title') echo 'selected'; ?>>Sort by Title</option>
                </select>
            </form>

            <!-- Search form -->
            <form id="searchForm" method="GET" action="homepage.php">
                <input type="text" name="search" placeholder="Search by title" value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                <button type="submit">Search</button>
            </form>

            <?php
            $limit = 6; // Number of wallpapers to display per page

            // Calculate the offset based on the current page
            $currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $offset = ($currentPage - 1) * $limit;

            // Query to count total wallpapers
            $countQuery = "SELECT COUNT(*) as total FROM wallpaper";
            $totalResult = $databaseConnection->getConnection()->query($countQuery);
            $totalWallpapers = $totalResult->fetch_assoc()['total'];

            $sort = isset($_GET['sort']) ? $_GET['sort'] : 'latest';
            $order = ($sort === 'oldest') ? 'ASC' : 'DESC';

            // Adjust order for sorting by ID
            if ($sort === 'latest') {
                $orderBy = 'WallpaperID DESC'; // Biggest ID number first
            } elseif ($sort === 'oldest') {
                $orderBy = 'WallpaperID ASC'; // Smallest ID number first
            } elseif ($sort === 'title') {
                $orderBy = 'Title ASC'; // Sort titles alphabetically from A to Z
            }

            // Search query
            $searchQuery = isset($_GET['search']) ? trim($_GET['search']) : '';
            $searchCondition = !empty($searchQuery) ? "WHERE Title LIKE '%$searchQuery%'" : '';

            // SQL query with search condition
            $sql = "SELECT WallpaperID, Title, WallpaperLocation FROM wallpaper $searchCondition ORDER BY $orderBy LIMIT $offset, $limit";
            $result = $databaseConnection->getConnection()->query($sql);

            // Check if there are no wallpapers
            if ($result->num_rows >= 1) {
                echo '<ul class="image-list" id="wallpaperList">';
                while ($row = $result->fetch_assoc()) {
                    $imagePath = 'accountProcess/' . $row['WallpaperLocation'];

                    if (file_exists($imagePath)) {
                        echo '<li class="image-item">';
                        echo '<div class="image-container">';
                        echo '<img style="width:400px;height:230px;object-fit:cover " src="' . $imagePath . '" alt="' . htmlspecialchars($row['Title']) . '">';
                        echo '<p style="color: white;text-transform: capitalize;">' . $row['Title'] . '</p>';
                        echo '</div>';
                        echo '<div class="dl_Btn">';
                        echo '<a style="display:flex;padding-left:5px;padding-right:5px; font-family:arial;" href="' . $imagePath . '" download="' . htmlspecialchars($row['Title']) . '">
    <img style="padding-right:5px" src="testImages/download.png" width="20" height="20"> Download</a>';
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

                // Show pagination only if there are more than 6 search results
                if ($totalWallpapers > $limit) {
                    echo '<center>';
                    $totalPages = ceil($totalWallpapers / $limit);

                    for ($page = 1; $page <= $totalPages; $page++) {
                        $isActive = ($page == $currentPage) ? 'active' : '';
                        $paginationLink = "?page=$page&sort=$sort&search=$searchQuery"; // Include sort and search parameters in pagination link
                        echo "<a href=\"$paginationLink\" class=\"pagination $isActive\">$page</a>";
                    }

                    echo '</center>';
                }
            } else {
                echo '<div style="text-align: center; padding: 5px; background-color: #f0f0f0; border: 1px solid #ccc; width:50%">';
                echo '<p style="font-size: 18px; color: #333;margin-left:-1%">No uploaded wallpapers&#128531</p>';
                echo '</div>';
            }

            ?>
        </fieldset>




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
                <li>
                    <a href="videocall.php">
                        <i class="fa fa-info fa-2x"><img class="navSideIcon" src="testImages/webcamera.png"></i>
                        <span class="nav-text">Video Call</span>
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
                xhr.open("GET", "load_more.php?page=<?php echo $currentPage + 1; ?>", true);
                xhr.send();
            }
        </script>
</body>

</html>