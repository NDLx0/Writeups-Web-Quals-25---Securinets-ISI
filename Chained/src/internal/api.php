<?php
session_start();
header("Content-Type: text/plain");
if (!isset($_SERVER['HTTP_X_API_KEY']) || $_SERVER['HTTP_X_API_KEY'] !== 'PIRATE_KEY_789') {
    echo "Wrong key, ye landlubber!";
    exit;
}
if (!isset($_SESSION['upload_complete']) || $_SESSION['upload_complete'] !== true) {
    echo "Stow a message in a bottle first, arr!";
    exit;
}
if (isset($_GET['action']) && $_GET['action'] === 'include' && isset($_GET['file'])) {
    $file = $_GET['file'];
    if (strpos($file, '/uploads/') !== 0 || strpos($file, '../') !== false) {
        echo "Stay in the hold, arr! Only uploads be allowed!";
        exit;
    }
    $full_path = '/var/www/html' . $file;
    $real_path = realpath($full_path);
    if (!$real_path || strpos($real_path, '/var/www/html/uploads/') !== 0) {
        echo "Invalid scroll path, arr!";
        exit;
    }
    if (file_exists($real_path)) {
        include($real_path);
    } else {
        echo "Scroll not found, arr!";
    }
}
?>