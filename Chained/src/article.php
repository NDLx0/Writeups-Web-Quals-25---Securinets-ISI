<?php
session_start();
$conn = new mysqli("db", "ctf_user_ndlx0!", "ctf_password_ndlx0!", "newshub");

if ($conn->connect_error) {
    die("Connection failed, arr! The ship’s sunk: " . $conn->connect_error);
}

$ip = $_SERVER['REMOTE_ADDR'];
$key = "comment_count_$ip";
$ban_key = "comment_ban_$ip";

if (isset($_SESSION[$ban_key]) && (time() - $_SESSION[$ban_key]) < 300) {
    die("Too many scribbles, ye scurvy dog! Banned fer 5 minutes!");
}

if (!isset($_SESSION[$key])) {
    $_SESSION[$key] = 0;
}

$pirate_insults = [
    "Ye salty sea rat",
    "Ye bilge-suckin’ barnacle",
    "Ye grog-guzzlin’ landlubber",
    "Ye scurvy-ridden swab",
    "Ye peg-legged poltroon",
    "Ye plank-walkin’ pollywog",
    "Ye rum-soaked rapscallion",
    "Ye keelhaulin’ knave",
    "Ye barnacle-bottomed buccaneer",
    "Ye cannon-fodder cur",
    "Ye swabbin’ scoundrel",
    "Ye cutlass-clumsy codfish",
    "Ye treasure-grubbin’ git",
    "Ye parrot-perchin’ pest",
    "Ye sea-sickened scalawag",
    "Ye grotty galley grub",
    "Ye mutinous mongrel",
    "Ye wind-whistlin’ wastrel",
    "Ye shark-baited shirker",
    "Ye deck-scrubbin’ dastard",
    "Ye brig-bound blaggard",
    "Ye rum-runnin’ rogue",
    "Ye crow’s-nest clod",
    "Ye bilge-breathed blackguard",
    "Ye hornswogglin’ harpy",
    "Ye tar-stained twit",
    "Ye anchor-draggin’ addle-brain",
    "Ye wave-washed wretch",
    "Ye scabby sea dog",
    "Ye jelly-spined jackanapes",
    "Ye storm-tossed simpleton",
    "Ye plank-prancin’ prat",
    "Ye sea-weed wrapped wally",
    "Ye grog-addled gadabout",
    "Ye cannon-cockin’ cretin",
    "Ye kraken-kissed kook",
    "Ye reef-rash ruffian",
    "Ye tide-tangled tosspot",
    "Ye barnacle-brained buffoon",
    "Ye helm-happy halfwit",
    "Ye sea-salted sluggard",
    "Ye rum-rotted rascal",
    "Ye mast-missin’ muppet",
    "Ye gull-gnawed goon",
    "Ye fish-flogged fool",
    "Ye wave-whipped whippersnapper",
    "Ye scurvy-soaked sop",
    "Ye brigantine-bumblin’ boob",
    "Ye coral-crusted clodpoll",
    "Ye squid-squirmin’ squab",
    "Ye anchorless arse",
    "Ye tide-tossed turncoat",
    "Ye windlass-wreckin’ wench",
    "Ye sea-foam fleabag",
    "Ye grog-garglin’ gizzard",
    "Ye hull-huggin’ hooligan",
    "Ye plank-pilin’ pillock",
    "Ye rum-reekin’ reprobate",
    "Ye sail-shreddin’ schlub",
    "Ye shark-shunned shyster",
    "Ye deck-dancin’ dimwit",
    "Ye kelp-knot knucklehead",
    "Ye cannonball-clankin’ clod",
    "Ye sea-spray-soaked souse",
    "Ye bilge-boilin’ berk",
    "Ye mast-manglin’ mook",
    "Ye tide-turnin’ twerp",
    "Ye grog-gluggin’ gremlin",
    "Ye barnacle-bitten blunderbuss",
    "Ye reef-runnin’ rube",
    "Ye scupper-suckin’ scamp",
    "Ye helm-hoggin’ hussy",
    "Ye sea-slime-slick slacker",
    "Ye rum-ravaged roustabout",
    "Ye plank-poundin’ pipsqueak",
    "Ye squid-skewered skunk",
    "Ye wave-wreckin’ wastrel",
    "Ye keel-kickin’ klutz",
    "Ye grog-gobblin’ guppy",
    "Ye sail-snaggin’ sniveler",
    "Ye barnacle-bunglin’ boor",
    "Ye sea-soggy snipe",
    "Ye cannon-cracked caitiff",
    "Ye tide-trapped trollop",
    "Ye rum-reeked ruffler",
    "Ye plank-ploddin’ plonker",
    "Ye shark-snarfed sneak",
    "Ye deck-droolin’ dunderhead",
    "Ye kelp-kinked kipper",
    "Ye bilge-blasted blighter",
    "Ye mast-muddled marauder",
    "Ye sea-stink swindler",
    "Ye grog-grimed grump",
    "Ye reef-rotted rotter",
    "Ye scuttle-sunk scallywag",
    "Ye helm-hazard hoolock",
    "Ye wave-worn windbag"
];

?>
<!DOCTYPE html>
<html>
<head>
    <title>Pirate Logbook</title>
    <link href="https://fonts.googleapis.com/css2?family=Pirata+One&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <style>
        .comment-box {
            background: rgba(139, 69, 19, 0.2);
            border: 2px dashed #FFD700;
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
            position: relative;
        }
        .pirate-time {
            font-size: 0.8em;
            color: #00CED1;
            position: absolute;
            bottom: 5px;
            right: 5px;
        }
        .insult {
            color: #FF4500;
            font-style: italic;
        }
    </style>
</head>
<body>
    <div class="container">
        <?php
        if (isset($_GET['id'])) {
            $id = intval($_GET['id']);
            $sql = "SELECT * FROM articles WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($row = $result->fetch_assoc()) {
                echo "<h1>" . htmlspecialchars($row['title']) . "</h1>";
                echo "<p>" . htmlspecialchars($row['content']) . "</p>";

                echo "<h2>Scallywag Scribbles</h2>";
                $comment_sql = "SELECT content, created_at FROM comments WHERE article_id = ? ORDER BY created_at DESC";
                $comment_stmt = $conn->prepare($comment_sql);
                $comment_stmt->bind_param("i", $id);
                $comment_stmt->execute();
                $comment_result = $comment_stmt->get_result();

                if ($comment_result->num_rows > 0) {
                    while ($comment = $comment_result->fetch_assoc()) {
                        $pirate_time = pirate_time($comment['created_at'] ? strtotime($comment['created_at']) : time());
                        $insult = $pirate_insults[array_rand($pirate_insults)];
                        echo "<div class='comment-box'>";
                        echo "<p><span class='insult'>$insult</span> scrawls: " . htmlspecialchars($comment['content']) . "</p>";
                        echo "<span class='pirate-time'>$pirate_time</span>";
                        echo "</div>";
                    }
                } else {
                    echo "<p>No scribbles yet, arr! Be the first to mark the log!</p>";
                }

                echo "<h3>Scrawl Yer Mark</h3>";
                echo "<form method='POST'>";
                echo "<textarea name='comment' placeholder='Yer pirate wisdom here, arr!' rows='4' cols='50'></textarea><br>";
                echo "<input type='submit' value='Scrawl Yer Words'>";
                echo "</form>";

                if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['comment'])) {
                    $_SESSION[$key]++;
                    if ($_SESSION[$key] > 20) {
                        $_SESSION[$ban_key] = time();
                        $_SESSION[$key] = 0;
                        die("Too many scribbles, ye scurvy dog! Banned fer 5 minutes!");
                    }
                    $comment = trim($_POST['comment']);
                    if (!empty($comment)) {
                        $insert_sql = "INSERT INTO comments (article_id, content, created_at) VALUES (?, ?, NOW())";
                        $insert_stmt = $conn->prepare($insert_sql);
                        $insert_stmt->bind_param("is", $id, $comment);
                        if ($insert_stmt->execute()) {
                            header("Location: article.php?id=$id");
                            exit;
                        } else {
                            echo "<p>Scrawlin’ failed, arr! " . $conn->error . "</p>";
                        }
                    } else {
                        echo "<p>Ye can’t scrawl nothin’, ye empty-headed swab!</p>";
                    }
                    $insert_stmt->close();
                }
            } else {
                echo "<p>No tale found with that mark, arr!</p>";
            }
            $stmt->close();
            $comment_stmt->close();
        } else {
            echo "<p>No article mark given, arr! Head back to <a href='index.php'>the ship</a>.</p>";
        }
        $conn->close();

        function pirate_time($timestamp) {
            $minutes_ago = (time() - $timestamp) / 60;
            if ($minutes_ago < 1) return "Just now, fresh as grog!";
            if ($minutes_ago < 60) return round($minutes_ago) . " bells ago";
            $hours_ago = $minutes_ago / 60;
            if ($hours_ago < 24) return round($hours_ago) . " watches past";
            $days_ago = $hours_ago / 24;
            return round($days_ago) . " days sailin’ back";
        }
        ?>
    </div>
</body>
</html>