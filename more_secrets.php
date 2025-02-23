<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SerialKiller Pro Max</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Roboto+Mono:wght@300;400;500&display=swap');

        body {
            background: #0d1117;
            color: #00ff00;
            font-family: 'Roboto Mono', monospace;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            overflow: hidden;
            margin: 0;
            flex-direction: column;
        }

        form {
            border: 1px solid #00ff00;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 20px #00ff00;
            text-align: center;
            width: 500px;
        }

        label {
            display: block;
            font-size: 24px;
            margin-bottom: 15px;
        }

        input[type="submit"] {
            background: transparent;
            color: #00ff00;
            border: 1px solid #00ff00;
            padding: 10px 20px;
            font-size: 18px;
            cursor: pointer;
            border-radius: 5px;
            transition: background 0.3s, color 0.3s;
        }

        input[type="submit"]:hover {
            background: #00ff00;
            color: #0d1117;
        }

        .terminal {
            font-size: 18px;
            color: #00ff00;
            margin-top: 20px;
            overflow: hidden;
            white-space: nowrap;
            border-right: 2px solid #00ff00;
            width: 0;
            animation: typing 4s steps(40, end) 1 forwards, blink 0.7s;
            width: auto; /* Ensure width doesn't collapse */


        }
        .terminal2 {
            font-size: 60px;
            color: #00ff00;
            white-space: nowrap;
            border-right: 2px solid #00ff00;
            width: 0;
            margin-right: 720px;
            
        }

        @keyframes typing {
            from { width: 0; }
            to { width: 20%; }
        }
    </style>
</head>
<body>
    <h1 class="terminal2">More Cookies for you ❤️</h1>
    <form action="" method="post">
        <label for="">Set/Reset Cookie</label>
        <input type="submit" name="set" value="Reset">
    </form>
    <br> <br>
    <?php
    class Secret {
        public $username;
        public $token;
        public function __construct($username, $token) {
            $this->username = $username;
            $this->token = $token;
        }
    }

    function setSecretCookie() {
        $someguy = new Secret('test', 'weaktoken');
        setcookie('secret', base64_encode(serialize($someguy)), time() + 3600, "/");
    }

    if (isset($_POST['set']) && $_POST['set'] == 'Reset') {
        setSecretCookie();
    }

    if (isset($_COOKIE['secret'])) {
        $user = unserialize(base64_decode($_COOKIE['secret']));
        if ($user->username==='admin' && $user->token=='REDACTED') {
            echo '<div class="terminal">Securinets{fake_flag}</div>';
        } else {
            echo '<div class="terminal">You again ?? U still a punk !</div>';
        }
    }
    ?>

</body>
</html>
