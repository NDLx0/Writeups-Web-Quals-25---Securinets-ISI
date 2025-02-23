<?php
$sourceCode = htmlspecialchars(file_get_contents("index.php"));
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Source Code</title>
  <style>
    body { background: #0e0b16; color: #00ff00; font-family: 'Courier New', Courier, monospace; padding: 20px; }
    pre { white-space: pre-wrap; word-wrap: break-word; font-size: 14px; }
    .container { background: rgba(0,0,0,0.7); padding: 20px; border-radius: 10px; }
  </style>
</head>
<body>
  <h1>Source Code</h1>
  <div class="container">
    <pre id="sourceCode"></pre>
  </div>
  <script>
    const sourceText = <?php echo json_encode($sourceCode); ?>;
    let i = 0;
    function typeWriter() {
      if (i < sourceText.length) {
        document.getElementById("sourceCode").innerHTML += sourceText.charAt(i);
        i++;
        setTimeout(typeWriter, 1);
      }
    }
    typeWriter();
  </script>
</body>
</html>

