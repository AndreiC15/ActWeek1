<!DOCTYPE html>
<html lang="en">
<?php
require_once 'accountProcess/connect.php';

class UserProfile {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function getUserProfile($id) {
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
    <title>Dashboard</title>
    <link rel="stylesheet" href="pagesCSS/HomepageStyle.css">

</head>

<body>
    <p>dashboard</p>
</body>

</html>
