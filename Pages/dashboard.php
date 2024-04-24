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

    // Use the user ID to filter images
    $limit = 9; // Number of wallpapers to display per page
    $currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $offset = ($currentPage - 1) * $limit;

    // Query to count total wallpapers uploaded by the user
    $countQuery = "SELECT COUNT(*) as total FROM wallpaper WHERE Uploader = ?";
    $countStmt = $databaseConnection->prepare($countQuery);
    $countStmt->bind_param('s', $userData['Email']);
    $countStmt->execute();
    $totalResult = $countStmt->get_result();
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
    $searchCondition = !empty($searchQuery) ? "AND Title LIKE ?" : '';

    // SQL query with search condition and pagination
    $sql = "SELECT WallpaperID, Title, WallpaperLocation, DownloadCount FROM wallpaper WHERE Uploader = ? $searchCondition ORDER BY $orderBy LIMIT ?, ?";
    $query = $databaseConnection->prepare($sql);
    $query->bind_param('sii', $userData['Email'], $offset, $limit);

    if (!empty($searchQuery)) {
        $searchParam = "%$searchQuery%";
        $query->bind_param('s', $searchParam);
    }

    $query->execute();
    $result = $query->get_result();
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

        .image-container {
            width: 100%;
            height: auto;
        }

        .deleteBtn {
            margin-left: -15.5%;
        }

        .editBtn {
            width: 30px;
            height: 30px;
            background-color: rgba(255, 255, 255);
            /* White color with 75% opacity */
            padding: 8px;
            border-radius: 10px;
        }


        input {
            cursor: pointer;
        }

        table {
            margin-left: -20%;
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
                    <button type="submit" class="uploadContainer" name="delete_all_wallpaper" class="btn btn-danger" style="margin-right: 10px;">Delete All Wallpaper</button>
                </form>
            </div>
            <div style="display: flex; justify-content: space-between; align-items: center; width: 95%; margin-top: 2%;">

                <!-- Search form -->
                <form id="searchForm" method="GET" action="" style="background-color: #f0f0f0; padding: 8px; border-radius: 5px;">
                    <input type="text" name="search" placeholder="Search by title" value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>" style="border: none; outline: none; background-color: #f0f0f0; font-size: 14px;">
                    <button type="submit" style="background-color: #4CAF50; color: white; border: none; outline: none; padding: 6px 12px; border-radius: 5px; cursor: pointer;">Search</button>
                </form>


                <!-- Sort options -->
                <form id="sortForm" method="GET" action="dashboard.php" style="background-color: #f0f0f0; padding: 8px; border-radius: 5px;">
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
                        echo '<p style="color: white;font-size:12px;margin-top:-1%" id="downloadCount_' . $row['WallpaperID'] . '">Downloaded: ' . (int)$row['DownloadCount'] . ' times</p>';
                        echo '<table style="margin-left:3%;">';
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

                // Show pagination only if there are more than 6 wallpapers
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
</body>

</html>