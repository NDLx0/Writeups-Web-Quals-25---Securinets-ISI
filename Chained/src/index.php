<?php
session_start();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Pirate NewsHub</title>
    <link href="https://fonts.googleapis.com/css2?family=Pirata+One&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>Cap'n's NewsHub</h1>
        <p>Welcome aboard, matey! Send a message in a bottle: <a href="contact.php">Contact</a></p>
        <p>Check the logs fer secrets: <a href="logs.php?path=/var/www/html/logs/admin.log">Cap'n's Log</a></p>
        <p>Want to express yourself? Try here: <a href="article.php?id=1">Express yourself</a></p>

    </div>
</body>
</html>
