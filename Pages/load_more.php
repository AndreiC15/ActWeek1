<?php
require_once 'accountProcess/connect.php';

$offset = $_GET['offset'];

// Use prepared statement to avoid SQL injection
$sql = "SELECT WallpaperID, Title, WallpaperLocation FROM wallpaper LIMIT ?, 6";
$stmt = $databaseConnection->getConnection()->prepare($sql);
$stmt->bind_param('i', $offset);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows >= 1) {
    while ($row = $result->fetch_assoc()) {
        $imagePath = 'accountProcess/' . $row['WallpaperLocation'];

        if (file_exists($imagePath)) {
            echo '<li class="image-item">';
            echo '<div class="image-container">';
            echo '<img style="width:400px;height:230px; " src="' . $imagePath . '" alt="' . htmlspecialchars($row['Title']) . '">';
            echo '<p style="color: white;text-transform: capitalize;">' . $row['Title'] . '</p>';
            echo '</div>';
            echo '<div class="dl_Btn">';
            echo '<a href="' . $imagePath . '" download="' . htmlspecialchars($row['Title']) . '">Download</a>';
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
}

// Close the prepared statement
$stmt->close();
?>
