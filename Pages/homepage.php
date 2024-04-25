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

    public function incrementDownloadCount($wallpaperID)
    {
        try {
            $stmt = $this->db->getConnection()->prepare("UPDATE wallpaper SET DownloadCount = DownloadCount + 1 WHERE WallpaperID = ?");
            $stmt->bind_param('i', $wallpaperID);
            $stmt->execute();
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
            position: absolute;
            bottom: 2%;
            right: 2%;
            width: fit-content;
            height: fit-content;
            padding: 5px;
            background-color: white;
            border-radius: 5px;
            border-style: solid;
            border-width: 2px;
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

        .modal {
            display: none;
            position: fixed;
            z-index: 9999;
            /* Ensure modal appears above other content */
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.8);
            /* Semi-transparent black background */
        }

        .modal-content {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100%;
        }

        .modal-img {
            max-width: 85%;
            max-height: 85%;
            object-fit: contain;
            margin-top:-2%;
            /* Ensure the image fits within the modal */
        }

        .close-btn {
            position: absolute;
            top: 1px;
            right: 20px;
            color: #fff;
            font-size: 64px;
            cursor: pointer;
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
            <div style="display: flex; justify-content: space-between; align-items: center; width: 100%; margin-top: 2%;">

                <!-- Search form -->
                <form id="searchForm" method="GET" action="" style="background-color: #f0f0f0; padding: 8px; border-radius: 5px;">
                    <input type="text" name="search" placeholder="Search by title" value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>" style="border: none; outline: none; background-color: #f0f0f0; font-size: 14px;">
                    <button type="submit" style="background-color: #4CAF50; color: white; border: none; outline: none; padding: 6px 12px; border-radius: 5px; cursor: pointer;">Search</button>
                </form>

                <!-- Sort options -->
                <!-- Sort options -->
                <form id="sortForm" method="GET" action="homepage.php" style="background-color: #f0f0f0; padding: 8px; border-radius: 5px;">
                    <select name="sort" onchange="document.getElementById('sortForm').submit()" style="border: none; outline: none; background-color: #f0f0f0; font-size: 14px;">
                        <option value="latest" <?php if (isset($_GET['sort']) && $_GET['sort'] === 'latest') echo 'selected'; ?>>Latest</option>
                        <option value="oldest" <?php if (isset($_GET['sort']) && $_GET['sort'] === 'oldest') echo 'selected'; ?>>Oldest</option>
                        <option value="title" <?php if (isset($_GET['sort']) && $_GET['sort'] === 'title') echo 'selected'; ?>>Title (A-Z)</option>
                        <option value="title_desc" <?php if (isset($_GET['sort']) && $_GET['sort'] === 'title_desc') echo 'selected'; ?>>Title (Z-A)</option>
                        <option value="downloads" <?php if (isset($_GET['sort']) && $_GET['sort'] === 'downloads') echo 'selected'; ?>>Most Downloaded</option>
                        <option value="least_downloaded" <?php if (isset($_GET['sort']) && $_GET['sort'] === 'least_downloaded') echo 'selected'; ?>>Least Downloaded</option>
                    </select>
                </form>

            </div>



            <?php
            $limit = 9; // Number of wallpapers to display per page

            // Calculate the offset based on the current page
            $currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $offset = ($currentPage - 1) * $limit;

            // Query to count total wallpapers
            $countQuery = "SELECT COUNT(*) as total FROM wallpaper";
            $totalResult = $databaseConnection->getConnection()->query($countQuery);
            $totalWallpapers = $totalResult->fetch_assoc()['total'];

            $sortOptions = [
                'latest' => 'WallpaperID DESC',
                'oldest' => 'WallpaperID ASC',
                'title' => 'Title ASC',
                'title_desc' => 'Title DESC',
                'downloads' => 'DownloadCount DESC',
                'least_downloaded' => 'DownloadCount ASC'
            ];

            $sort = isset($_GET['sort']) && isset($sortOptions[$_GET['sort']]) ? $_GET['sort'] : 'latest';
            $orderBy = $sortOptions[$sort];

            // Search query
            $searchQuery = isset($_GET['search']) ? trim($_GET['search']) : '';
            $searchCondition = !empty($searchQuery) ? "WHERE Title LIKE '%$searchQuery%'" : '';

            // SQL query with search condition
            // SQL query with search condition
            $sql = "SELECT WallpaperID, Uploader, Title, WallpaperLocation, DownloadCount FROM wallpaper $searchCondition ORDER BY $orderBy LIMIT $offset, $limit";
            $result = $databaseConnection->getConnection()->query($sql);

            // Check if there are no wallpapers
            if ($result->num_rows >= 1) {
                echo '<ul class="image-list" id="wallpaperList">';
                while ($row = $result->fetch_assoc()) {
                    $imagePath = 'accountProcess/' . $row['WallpaperLocation'];

                    if (file_exists($imagePath)) {
                        echo '<li class="image-item">';
                        echo '<div class="image-container">';
                        echo '<div class="dl_Btn">';
                        echo '<a style="display:flex;padding-left:5px;padding-right:5px; font-family:arial;" href="download.php?WallpaperID=' . $row['WallpaperID'] . '" >';
                        echo '<img style="width:20px; height:20px" src="testImages/download.png" ></a>';
                        echo '</div>';
                        // Pass the image path and title to the openModal function
                        echo '<img style="width:400px;height:230px;object-fit:cover " src="' . $imagePath . '" alt="' . htmlspecialchars($row['Title']) . '" onclick="openModal(\'' . $imagePath . '\', \'' . htmlspecialchars($row['Title']) . '\')">';
                        echo '</div>';
                        echo '<div style="max-width: 400px;">'; // Adjust max-width to match the width of the image
                        echo '<p style="color: white;font-weight:bold;margin-top:5%;text-align:center; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">' . $row['Title'] . '</p>';
                        echo '</div>';
                        $Uploader = explode('@', $row['Uploader'])[0];
                        echo '<p style="color: white;font-size:12px;margin-top:-3%">Uploaded by: ' . $Uploader . '</p>';
                        echo '<p style="color: white;font-size:12px;margin-top:-2%" id="downloadCount_' . $row['WallpaperID'] . '">Downloaded: ' . (int)$row['DownloadCount'] . ' times</p>';
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
                echo '<div style="text-align: center; padding: 5px; background-color: #f0f0f0; border: 1px solid #ccc; width:50%; margin-top: 10vh;">';
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
        <div id="myModal" class="modal">
            <div class="modal-content">
                <span class="close-btn" onclick="closeModal()">&times;</span>
                <img id="modalImg" class="modal-img" src="" alt="Full Image">
            </div>
            <p id="modalTitle" style="color: white; text-align: center; margin-top: -3%;font-size:20px"></p>
        </div>

        <script>
            function openModal(imagePath, title) {
                var modal = document.getElementById('myModal');
                var modalImg = document.getElementById('modalImg');
                var modalTitle = document.getElementById('modalTitle');

                modal.style.display = "block";
                modalImg.src = imagePath;
                modalTitle.textContent = title;
            }


            // Function to close the modal
            function closeModal() {
                var modal = document.getElementById('myModal');
                modal.style.display = "none";
            }

            // Close modal when user clicks outside the modal content
            window.onclick = function(event) {
                var modal = document.getElementById('myModal');
                if (event.target == modal) {
                    closeModal();
                }
            }
        </script>
</body>

</html>