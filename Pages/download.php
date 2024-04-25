<?php
require_once 'accountProcess/connect.php';

if(isset($_GET['WallpaperID'])) {
    $wallpaperID = $_GET['WallpaperID'];

    // Query to increment download count
    $incrementQuery = "UPDATE wallpaper SET DownloadCount = DownloadCount + 1 WHERE WallpaperID = ?";
    $stmt = $databaseConnection->getConnection()->prepare($incrementQuery);
    $stmt->bind_param('i', $wallpaperID);
    $stmt->execute();

    // Check if the increment was successful
    if($stmt->affected_rows > 0) {
        // Fetch the image path from the database
        $selectQuery = "SELECT WallpaperLocation, Title FROM wallpaper WHERE WallpaperID = ?";
        $stmt = $databaseConnection->getConnection()->prepare($selectQuery);
        $stmt->bind_param('i', $wallpaperID);
        $stmt->execute();
        $result = $stmt->get_result();

        // If image found, initiate download
        if($result->num_rows == 1) {
            $row = $result->fetch_assoc();
            $imagePath = 'accountProcess/' . $row['WallpaperLocation'];
            $title = $row['Title'];

            // Check if the file exists
            if(file_exists($imagePath)) {
                // Set headers to initiate download
                header('Content-Description: File Transfer');
                header('Content-Type: application/octet-stream');
                header('Content-Disposition: attachment; filename="'.sanitizeFilename($title).'.'.pathinfo($imagePath, PATHINFO_EXTENSION).'"');
                header('Expires: 0');
                header('Cache-Control: must-revalidate');
                header('Pragma: public');
                header('Content-Length: ' . filesize($imagePath));
                readfile($imagePath);
                exit;
            }
        }
    }
}

// Redirect to homepage if download fails
header("Location: homepage.php");
exit;

// Function to sanitize the filename
function sanitizeFilename($filename) {
    // Remove any non-alphabetic characters
    $filename = preg_replace("/[^a-zA-Z]/", '', $filename);
    return $filename;
}
?>
