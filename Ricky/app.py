from flask import Flask, request, render_template, redirect
import pickle
import os
import re

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

        banned_tokens = [
            "rm", "mv", "cat", "chmod", "chown", "wget", "curl", "python", "bash", "sh",
            "nc", "netcat", ";", "|", "&&", "`", "$(", "sudo", "dd", "<", "||", "exec",
            "open", "system", "ls", "find", "tar", "zip", "unzip", "rmdir", "unlink",
            "unlinkat", "rename", "renameat", "remove", "removeat", ".."
        ]
        for token in banned_tokens:
            if token in command:
                return render_template("result.html", message=f"Invalid command! Banned token found: {token}")

        if command.startswith("cp ") and not (command.endswith("/tmp") or command.endswith("/tmp/")):
            return render_template("result.html", message="You can't copy there!")
        os.system(command)
        return render_template("result.html", message="Bingo! The bomb has been sent to Davy Jones' locker!")
    
    except Exception as e:
        return render_template("result.html", message=f"Shiver me timbers! There be an error: {e}")


@app.route('/gallery', methods=['GET'])
def gallery():
    filename = request.args.get('file')

    if filename and (re.search(r'^(?!/tmp/flag\.txt$).*flag\.txt', filename)or filename is '/app/app.py'):
        return render_template("result.html", message="You can't view that file, I banned those directories!")
    
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
