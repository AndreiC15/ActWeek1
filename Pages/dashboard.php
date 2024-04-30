<?php
// Include database connection file
require_once 'accountProcess/connect.php';

// Class for user profile
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

            // Set the 'Email' key in the session
            $_SESSION['Email'] = $userData['Email'];

            return $userData;
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage();
            die();
        }
    }

    public function getTotalUploadedWallpapers($email)
    {
        try {
            $stmt = $this->db->prepare("SELECT COUNT(*) AS total FROM wallpaper WHERE Uploader = ?");
            $stmt->bind_param('s', $email);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            return $row['total'];
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage();
            return false;
        }
    }
}

// Check if user is logged in
if (!empty($_SESSION['id'])) {
    // Reuse database connection
    $userProfile = new UserProfile($databaseConnection);
    $id = $_SESSION['id'];
    $userData = $userProfile->getUserProfile($id);

    // Get total uploaded wallpapers count
    $totalUploadedWallpapers = $userProfile->getTotalUploadedWallpapers($userData['Email']);
} else {
    echo "<script>alert('Logout successfully'); window.location = 'index.php';</script>";
    exit();
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="pagesCSS/dashboard.css">
    <link rel="stylesheet" href="pagesCSS/dashboard2.css">
    <script src="pagesJS/dashboard.js"></script>
</head>

<body>
    <div class="scroll-buttons-container">
        <button onclick="scrollToTop()" id="scrollToTopBtn" title="Scroll to top">&#9650;</button>
        <button onclick="scrollToBottom()" id="scrollToBottomBtn" title="Scroll to bottom">&#9660;</button>
    </div>
    <div class="navBarTop">
        <h1 class="userName"><?php echo $userData['FirstName']; ?>'s</h1>
        <h1>&nbsp;&nbsp;Dashboard</h1>
    </div>
    <center>
        <fieldset>
            <h2 style="margin-top:2%;">My Uploaded Wallpapers: <?php echo $totalUploadedWallpapers; ?> </h2>
            <div style="display: flex; align-items: center; justify-content: center; width: 100%;margin-bottom:2%">
                <a href="uploadWallpaper.php" style="margin-right: 10px;">
                    <input style="font-size: 15px;" class="uploadContainer" type="button" value="Upload Wallpaper">
                </a>
                <form action="accountProcess/process.php" method="post" onsubmit="return confirm('Are you sure you want to delete all your wallpapers?');">
                    <input type="hidden" name="Email" value="<?php echo $userData['Email']; ?>">
                    <button type="submit" class="deleteContainer" name="delete_all_wallpaper" class="btn btn-danger" style="margin-right: 10px;">Delete All Wallpaper</button>
                </form>
            </div>
            <div style="display: flex; justify-content: space-between; align-items: center; width: 100%; margin-top: 2%;cursor:text">
                <form id="searchForm" method="GET" action="" style="background-color: #f0f0f0; padding: 8px; border-radius: 5px;">
                    <input style="border: none; outline: none; background-color: #f0f0f0; font-size: 14px;cursor:text" type="text" name="search" placeholder="Search by title">
                    <button type="submit" style="background-color: #4CAF50; color: white; border: none; outline: none; padding: 6px 12px; border-radius: 5px; ;">Search</button>
                </form>

                <form id="sortForm" method="GET" action="dashboard.php" style="background-color: #f0f0f0; padding: 8px; border-radius: 5px;cursor:default">
                    <select name="sort" onchange="document.getElementById('sortForm').submit()" style="border: none; outline: none; background-color: #f0f0f0; font-size: 14px;">
                        <option value="latest" <?php if (isset($_GET['sort']) && $_GET['sort'] === 'latest') echo 'selected'; ?>>Latest</option>
                        <option value="oldest" <?php if (isset($_GET['sort']) && $_GET['sort'] === 'oldest') echo 'selected'; ?>>Oldest</option>
                        <option value="title" <?php if (isset($_GET['sort']) && $_GET['sort'] === 'title') echo 'selected'; ?>>Title (A-Z)</option>
                        <option value="title_desc" <?php if (isset($_GET['sort']) && $_GET['sort'] === 'title_desc') echo 'selected'; ?>>Title (Z-A)</option>
                        <option value="downloads" <?php if (isset($_GET['sort']) && $_GET['sort'] === 'downloads') echo 'selected'; ?>>Most Downloaded</option>
                        <option value="least_downloaded" <?php if (isset($_GET['sort']) && $_GET['sort'] === 'least_downloaded') echo 'selected'; ?>>Least Downloaded</option>
                    </select>
                    <!-- Include the search parameter in the form -->
                    <input style="cursor:default" type="hidden" name="search" value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                </form>
            </div>
            <?php
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

            $searchQuery = isset($_GET['search']) ? trim($_GET['search']) : '';
            $searchCondition = !empty($searchQuery) ? "WHERE Title LIKE '%$searchQuery%'" : '';

            $sql = "SELECT WallpaperID, Title, WallpaperLocation, DownloadCount, Tags1, Tags2, Tags3, Tags4, Tags5 FROM wallpaper WHERE Uploader = ? ORDER BY $orderBy";

            $query = $databaseConnection->getConnection()->prepare($sql);
            $query->bind_param('s', $userData['Email']);

            // Check if search query is set
            if (isset($_GET['search']) && !empty($_GET['search'])) {
                $search = '%' . $_GET['search'] . '%';
                $sql = "SELECT WallpaperID, Title, WallpaperLocation, DownloadCount, Tags1, Tags2, Tags3, Tags4, Tags5 
            FROM wallpaper 
            WHERE Uploader = ? AND Title LIKE ? 
            ORDER BY $orderBy";
                $query = $databaseConnection->getConnection()->prepare($sql);
                $query->bind_param('ss', $userData['Email'], $search);
            } else {
                $sql = "SELECT WallpaperID, Title, WallpaperLocation, DownloadCount, Tags1, Tags2, Tags3, Tags4, Tags5 
            FROM wallpaper 
            WHERE Uploader = ? 
            ORDER BY $orderBy";
                $query = $databaseConnection->getConnection()->prepare($sql);
                $query->bind_param('s', $userData['Email']);
            }

            $query->execute();
            $result = $query->get_result();


            if ($result->num_rows >= 1) {
                echo '<ul class="image-list">';
                while ($row = $result->fetch_assoc()) {
                    $imagePath = 'accountProcess/' . $row['WallpaperLocation'];

                    if (file_exists($imagePath)) {
                        echo '<li class="image-item">';
                        echo '<div class="image-container">';
                        echo '<img style="width:400px;height:230px;object-fit:cover " src="' . $imagePath . '" alt="' . htmlspecialchars($row['Title']) . '" onclick="openModal(\'' . $imagePath . '\', \'' . htmlspecialchars($row['Title']) . '\')">';
                        // Updated form to include an anchor tag for "Edit" functionality
                        echo '<form method="post" action="./accountProcess/process.php">';
                        echo '<input type="hidden" name="WallpaperID" value="' . $row['WallpaperID'] . '">';
                        echo '<div style="max-width: 400px;">'; // Adjust max-width to match the width of the image
                        echo '<p style="color: white;font-weight:bold;margin-top:5%;text-align:center; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">' . $row['Title'] . '</p>';
                        echo '</div>';
                        echo '<p style="color: white;font-size:12px;margin-top:-3%" id="downloadCount_' . $row['WallpaperID'] . '">Downloaded: ' . (int)$row['DownloadCount'] . ' times</p>';

                        echo '<div style="color: white; font-size: 12px; margin-top: -2%; max-width: 500px; overflow-x: auto; display: flex; flex-wrap: wrap; align-items: center; justify-content: center;">';
                        echo 'Tags:&nbsp;';

                        $tags = [];

                        $tagCount = 0;
                        for ($i = 1; $i <= 5; $i++) {
                            $tag = $row['Tags' . $i];
                            if (!empty($tag)) {
                                // Display each tag as a clickable link with background color
                                echo '<span style="margin-right: 5px;">';
                                echo '<a href="tag_search_dashboard.php?tag=' . urlencode($tag) . '" style="text-decoration: none; background-color: #4CAF50; padding: 3px 6px; border-radius: 3px; color: white; display: inline-block; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">' . $tag . '</a>';
                                echo '</span>';
                                $tagCount++;
                            }
                        }
                        if ($tagCount == 0) {
                            echo 'No tags available';
                        }
                        echo '</div>';
                        echo '<table style="margin-left:0.5%;margin-top:5%;">';
                        echo '<tr>';
                        echo '<td><a  href="editWallpaper.php?WallpaperID=' . $row['WallpaperID'] . '"><img class="editBtn" src="testImages/edit.png" title="Edit Wallpaper"></a><td>';
                        echo '<td>
                    <form action="process.php" method="post">
                        <button name="delete_wallpaper" title="Delete Wallpaper" style="background: none; border: none; padding: 0; margin: 0; cursor: pointer;">
                            <img class="editBtn" src="testImages/delete.png" alt="Delete Wallpaper">
                        </button>
                    </form>
                </td>';
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
            } else if (!empty($searchQuery)) {
                // Display message for no matching wallpapers found
                echo '<div style="text-align: center; padding: 5px; background-color: #f0f0f0; border: 1px solid #ccc; width:50%; margin-top: 10vh;">';
                echo '<p style="font-size: 18px; color: #333;margin-left:-1%">No matching wallpapers found &#128531</p>';
                echo '</div>';
            }else {
                echo '<div style="text-align: center; padding: 5px; background-color: #f0f0f0; border: 1px solid #ccc; width:50%; margin-top: 10vh;">';
                echo '<p style="font-size: 18px; color: #333;margin-left:-1%">You haven\'t uploaded any wallpaper yet.</p>';
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
    </center>
    <div id="myModal" class="modal">
        <div class="modal-content">
            <span class="close-btn" onclick="closeModal()">&times;</span>
            <img id="modalImg" class="modal-img" src="" alt="Full Image">
        </div>
        <p id="modalTitle" style="color: white; text-align: center; margin-top: -3%;font-size:20px"></p>
    </div>
</body>

</html>