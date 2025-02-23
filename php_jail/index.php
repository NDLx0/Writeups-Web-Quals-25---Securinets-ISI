<?php
error_reporting(0);
$output = "";
$message = "";
$blacklist = ['system', 'exec', 'shell_exec', 'passthru', 'flag'];

if (isset($_GET['parse'])) {
    $a = $_GET['parse'] ?? '';

    foreach ($blacklist as $badword) {
        if (stripos($a, $badword) !== false) {
            $message = "That's not allowed. I thought you already knew that.";
            goto end;
        }
    }

    function _X($b) {
        $c = str_replace(["<?", "?>"], "", $b);
        $c = preg_replace('/\b(system|exec|shell_exec|passthru|flag)\b/i', "", $c);
        $c = preg_replace('/\btrue\b/i', "", $c);
        return $c;
    }

    $d = _X($a);

    if (preg_match('/\{if:\s*(.*?)\)\s*(.*?)\s*;\/\/\}(.*?)\{end if\}/is', $d, $e)) {
        $f = $e[1];
        $g = $e[2];
        $h = $e[3];
        $i = false;

        try {
            @eval('$i = ('.$f.');');
        } catch (Throwable $e) {
            $message = "WTF you doing?";
            goto end;
        }

        if ($i) {
            try {
                @eval($g.';');
            } catch (Throwable $e) {
                $message = "WTF you doing?";
                goto end;
            }
        }

        $output = htmlspecialchars($h);
    } else {
        $message = "WTF you doing?";
    }
}

end:
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Cyber Pirates' Den</title>
  <style>
    body { font-family: 'Courier New', Courier, monospace; color: #e0e0e0; background: #0e0b16; text-align: center; }
    h1 { font-size: 3em; }
    p { font-size: 1.2em; }
    .form-container {
      background: rgba(255,255,255,0.1);
      padding: 1.5em;
      border-radius: 10px;
      display: inline-block;
    }
    input, button {
      padding: 10px;
      font-size: 1em;
      border: none;
      border-radius: 5px;
    }
    button {
      background: #ffcc00;
      cursor: pointer;
    }
    .error {
      color: red;
      font-weight: bold;
      font-size: 1.5em;
      margin-top: 10px;
    }
  </style>
</head>
<body>
  <h1>Cyber Pirates' Den</h1>
  <p>Can you escape the digital jail?</p>
  <div class="form-container">
    <form action="" method="GET">
      <label><strong>Your Payload:</strong></label><br>
      <input type="text" name="parse" placeholder="{if: ... }"><br><br>
      <input type="submit" value="Execute">
    </form>
  </div>

  <?php if($output){ ?>
    <div><pre><?php echo $output; ?></pre></div>
  <?php } ?>

  <?php if($message){ ?>
    <div class="error"><?php echo $message; ?></div>
  <?php } ?>

  <br><br>
  <button onclick="window.open('source.php', '_blank', 'width=800,height=600')">View Source</button>
</body>
</html>

