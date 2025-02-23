<!DOCTYPE html>
<html>
<head>
    <title>Cap'n's Log</title>
    <link href="https://fonts.googleapis.com/css2?family=Pirata+One&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>Cap'n's Secret Log</h1>
        <?php
        if (isset($_GET['path'])) {
            $path = $_GET['path'];
            $log_dir = '/var/www/html/logs/';
            if (strpos($path, '../') !== false || strpos(realpath($path), $log_dir) !== 0) {
                echo "<p>No sneakin' outta the log hold, matey! Only logs be allowed!</p>";
            } else {
                if (file_exists($path)) {
                    echo "<p>" . file_get_contents($path) . "</p>";
                } else {
                    echo "<p>Log not found, arr!</p>";
                }
            }
        } else {
            echo "<p>No log specified, arr!</p>";
        }
        ?>
    </div>
</body>
</html>
