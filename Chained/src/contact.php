<?php
session_start();
$ip = $_SERVER['REMOTE_ADDR'];
$key = "upload_count_$ip";
$ban_key = "upload_ban_$ip";

if (isset($_SESSION[$ban_key]) && (time() - $_SESSION[$ban_key]) < 300) {
    die("Too many bottles tossed, arr! Banned fer 5 minutes!");
}

if (!isset($_SESSION[$key])) {
    $_SESSION[$key] = 0;
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>Message in a Bottle</title>
    <link href="https://fonts.googleapis.com/css2?family=Pirata+One&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>Send a Message in a Bottle</h1>
        <?php
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['file'])) {
            $_SESSION[$key]++;
            if ($_SESSION[$key] > 20) {
                $_SESSION[$ban_key] = time();
                $_SESSION[$key] = 0;
                die("Too many bottles tossed, arr! Banned fer 5 minutes!");
            }
            $file = $_FILES['file'];
            $filename = basename($file['name']);
            $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
            $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
            if (!in_array($file['type'], $allowed_types) || !in_array($ext, ['jpg', 'png', 'gif'])) {
                die("Invalid scroll type or markin', arr!");
            }
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime_type = finfo_file($finfo, $file['tmp_name']);
            if (!in_array($mime_type, $allowed_types)) {
                die("Scroll contents be fishy, arr!");
            }
            $upload_dir = 'uploads/';
            $target_path = $upload_dir . $filename;
            if (move_uploaded_file($file['tmp_name'], $target_path)) {
                $_SESSION['upload_complete'] = true;
                $_SESSION['uploaded_file'] = $filename;
                echo "Message stowed successfully: <a href='/uploads/$filename'>$filename</a>";
            } else {
                echo "Message lost at sea, arr!";
            }
        } else {
            echo "<form method='POST' enctype='multipart/form-data'>";
            echo "<input type='file' name='file' required><br>";
            echo "<input type='submit' value='Toss Bottle'>";
            echo "</form>";
        }
        ?>
    </div>
</body>
</html>
