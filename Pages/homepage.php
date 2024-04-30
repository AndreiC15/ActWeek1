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

// Check if the tag parameter is set in the URL

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <link rel="stylesheet" href="pagesCSS/HomepageStyle.css">
    <link rel="stylesheet" href="pagesCSS/HomepageStyle2.css">
    <script src="pagesJS/homepage.js"></script>
    <style>
        .pagination {
            margin-top: 20px;
            text-align: center;
        }

        .pagination a {
            color: white;
            background-color: red;
            padding: 8px 16px;
            text-decoration: none;
            margin: 0 5px;
            border-radius: 5px;
        }

        .pagination a.active {
            background-color: blue;
            color: white;
        }

        .pagination a:hover:not(.active) {
            background-color: red;
            color: white;
        }
    </style>
</head>

<body>
    <div class="scroll-buttons-container">
        <button onclick="scrollToTop()" id="scrollToTopBtn" title="Scroll to top">&#9650;</button>
        <button onclick="scrollToBottom()" id="scrollToBottomBtn" title="Scroll to bottom">&#9660;</button>
    </div>
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
                <form id="searchForm" method="GET" action="homepage.php" style="background-color: #f0f0f0; padding: 8px; border-radius: 5px;">
                    <input type="text" name="search" placeholder="Search by title" value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>" style="border: none; outline: none; background-color: #f0f0f0; font-size: 14px;">
                    <button type="submit" style="background-color: #4CAF50; color: white; border: none; outline: none; padding: 6px 12px; border-radius: 5px; cursor: pointer;">Search</button>
                </form>


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
                    <!-- Include the search parameter in the form -->
                    <input type="hidden" name="search" value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                </form>

            </div>



            <?php

            // Pagination logic
            $itemsPerPage = 9;
            $currentPage = isset($_GET['page']) ? intval($_GET['page']) : 1;
            $offset = ($currentPage - 1) * $itemsPerPage;

            // Sort options
            $sortOptions = [
                'latest' => 'WallpaperID DESC',
                'oldest' => 'WallpaperID ASC',
                'title' => 'Title ASC',
                'title_desc' => 'Title DESC',
                'downloads' => 'DownloadCount DESC',
                'least_downloaded' => 'DownloadCount ASC'
            ];

            // Get the sorting parameter from the URL or use 'latest' as default
            $sort = isset($_GET['sort']) && isset($sortOptions[$_GET['sort']]) ? $_GET['sort'] : 'latest';
            $orderBy = $sortOptions[$sort];

            // Handle search query
            $searchQuery = isset($_GET['search']) ? trim($_GET['search']) : '';
            $searchCondition = !empty($searchQuery) ? "WHERE Title LIKE '%$searchQuery%'" : '';

            // Count total number of images
            $sqlCount = "SELECT COUNT(*) AS total FROM wallpaper $searchCondition";
            $resultCount = $databaseConnection->getConnection()->query($sqlCount);
            $totalCount = $resultCount->fetch_assoc()['total'];

            // Calculate total number of pages
            $totalPages = ceil($totalCount / $itemsPerPage);

            // SQL query with pagination, search condition, and sorting
            $sql = "SELECT WallpaperID, Uploader, Title, WallpaperLocation, DownloadCount, Tags1, Tags2, Tags3, Tags4, Tags5 
                FROM wallpaper 
                $searchCondition 
                ORDER BY $orderBy 
                LIMIT $itemsPerPage OFFSET $offset";

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
                        echo '<img style="width:400px;height:230px;object-fit:cover" src="' . $imagePath . '" alt="' . htmlspecialchars($row['Title']) . '" onclick="openModal(\'' . addslashes($imagePath) . '\', \'' . addslashes($row['Title']) . '\')">';
                        echo '</div>';
                        echo '<div style="max-width: 400px;">'; // Adjust max-width to match the width of the image
                        echo '<p style="color: white;font-weight:bold;margin-top:5%;text-align:center; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">' . $row['Title'] . '</p>';
                        echo '</div>';
                        $Uploader = explode('@', $row['Uploader'])[0];
                        echo '<p style="color: white;font-size:12px;margin-top:-3%">Uploaded by: ' . $Uploader . '</p>';
                        echo '<p style="color: white;font-size:12px;margin-top:-2%" id="downloadCount_' . $row['WallpaperID'] . '">Downloaded: ' . (int)$row['DownloadCount'] . ' times</p>';
                        echo '<div style="color: white; font-size: 12px; margin-top: -2%; max-width: 500px; overflow-x: auto; display: flex; flex-wrap: wrap; align-items: center; justify-content: center;">';
                        echo 'Tags:&nbsp;';

                        $tags = [];

                        $tagCount = 0;
                        for ($i = 1; $i <= 5; $i++) {
                            $tag = $row['Tags' . $i];
                            if (!empty($tag)) {
                                // Display each tag as a clickable link with background color
                                echo '<span style="margin-right: 5px;">';
                                echo '<a href="tag_search.php?tag=' . urlencode($tag) . '" style="text-decoration: none; background-color: #4CAF50; padding: 3px 6px; border-radius: 3px; color: white; display: inline-block; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">' . $tag . '</a>';
                                echo '</span>';
                                $tagCount++;
                            }
                        }

                        if ($tagCount == 0) {
                            echo 'No tags available';
                        }

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
            } else if (!empty($searchQuery)) {
                // Display message for no matching wallpapers found
                echo '<div style="text-align: center; padding: 5px; background-color: #f0f0f0; border: 1px solid #ccc; width:50%; margin-top: 10vh;">';
                echo '<p style="font-size: 18px; color: #333;margin-left:-1%">No matching wallpapers found &#128531</p>';
                echo '</div>';
            } else {
                echo '<div style="text-align: center; padding: 5px; background-color: #f0f0f0; border: 1px solid #ccc; width:50%; margin-top: 10vh;">';
                echo '<p style="font-size: 18px; color: #333;margin-left:-1%">No uploaded wallpapers&#128531</p>';
                echo '</div>';
            }

            // Display pagination links
            // Display pagination links
            // Display pagination links
            echo '<div class="pagination" style="margin-top: 20px; text-align: center;">';
            if ($totalPages > 1) {
                for ($i = 1; $i <= $totalPages; $i++) {
                    // Include both search and sort parameters in the pagination links
                    $paginationLink = isset($_GET['search']) ? 'homepage.php?search=' . urlencode($_GET['search']) . '&sort=' . $sort . '&page=' . $i : 'homepage.php?sort=' . $sort . '&page=' . $i;

                    if ($i === $currentPage) {
                        echo "<a href='$paginationLink' class='active'>$i</a>";
                    } else {
                        echo "<a href='$paginationLink'>$i</a>";
                    }
                }
            }
            echo '</div>';




            ?>
        </fieldset>
        <nav class="main-menu">
            <ul>
                <li>
                    <i class="fa fa-info fa-2x" onmousedown="startRedirectTimer()" onmouseup="cancelRedirectTimer()"><img class="navSideIconLogo" src="testImages/icon.png"></i>
                    <span class="nav-text" onmousedown="startRedirectTimer()" onmouseup="cancelRedirectTimer()">WallpaperStation</span>
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
    </center>
</body>

</html>