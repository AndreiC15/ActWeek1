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
    <link rel="stylesheet" href="pagesCSS/webcam.css">
    <style>
        #container {
            margin: 0px auto;
            width: 500px;
            height: 375px;
            margin-top:5%;
            border: 10px #333 solid;
            transform: scaleX(-1);
        }

        #videoElement {
            width: 500px;
            height: 375px;
            background-color: #666;
            
        }

        #buttonContainer {
            text-align: center;
        }

        #captureBtn,
        #downloadLink {
            display: block;
            margin: 10px auto;
            padding: 10px;
            background-color: #3498db;
            color: #fff;
            border: none;
            cursor: pointer;
        }
    </style>
</head>

<body>

    <div class="navBarTop">
        <h1>Webcam</h1>
    </div>

    <div class="area"></div>
    <nav class="main-menu">
        <ul>
            <center>
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
            </center>
        </ul>
        <ul class="logout">
            <li>
                <center>
                    <i class="fa fa-info fa-2x"><img class="navSideIcon" src="testImages/shutdown.png"></i>
                    <span class="nav-text">
                        <div class="LogoutButton">
                            <form method="POST" action="./accountProcess/process.php">
                                <input style="width: 100%; max-width: 100px; height: 30px; background-color: red; border-radius: 50px; color: white;cursor: pointer;" type="submit" id="logout" name="logout" value="Logout">
                            </form>
                        </div>
                    </span>
                </center>
                </a>
            </li>
        </ul>
    </nav>
    <div id="container">
        <video autoplay="true" id="videoElement"></video>
    </div>
    <div id="buttonContainer">
        <button id="captureBtn" onclick="captureImage()">Capture Image</button>
        <canvas id="canvas" style="display:none;"></canvas>
        <img id="capturedImage" style="display:none;">
        <a id="downloadLink" style="display:none;" download="captured_image.png">Download Image</a>
    </div>

    <script>
        var video = document.querySelector("#videoElement");
        var canvas = document.querySelector("#canvas");
        var capturedImage = document.querySelector("#capturedImage");
        var downloadLink = document.querySelector("#downloadLink");

        if (navigator.mediaDevices.getUserMedia) {
            navigator.mediaDevices.getUserMedia({
                    video: true
                })
                .then(function(stream) {
                    video.srcObject = stream;
                })
                .catch(function(error) {
                    console.log("Something went wrong!", error);
                });
        }

        function captureImage() {
    canvas.width = video.videoWidth;
    canvas.height = video.videoHeight;
    canvas.getContext('2d').drawImage(video, 0, 0, video.videoWidth, video.videoHeight);

    // Flip the captured image horizontally
    canvas.getContext('2d').scale(-1, 1);
    canvas.getContext('2d').drawImage(video, -video.videoWidth, 0, video.videoWidth, video.videoHeight);

    capturedImage.src = canvas.toDataURL('image/png');
    capturedImage.style.display = 'block';

    downloadLink.href = capturedImage.src;
    downloadLink.style.display = 'block';
}
    </script>
</body>

</html>