<?php
$files = glob("json/*.json");
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Website Settings</title>

    <style>
    body {
        font-family: Arial;
        background: #f4f6f8;
        padding: 40px;
    }

    .container {
        max-width: 650px;
        margin: auto;
        background: white;
        padding: 30px;
        border-radius: 10px;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
    }

    .form-group {
        margin-bottom: 15px
    }

    label {
        font-weight: bold;
        display: block;
        margin-bottom: 5px
    }

    input,
    textarea,
    select {
        width: 100%;
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 6px
    }

    .buttons {
        margin-top: 20px;
        display: flex;
        gap: 10px
    }

    button {
        padding: 10px 20px;
        border: none;
        border-radius: 6px;
        cursor: pointer
    }

    .save {
        background: #27ae60;
        color: white
    }

    .download {
        background: #3498db;
        color: white
    }
    </style>
</head>

<body>

    <div class="container">

        <h2>⚙ Website Settings</h2>

        <div class="form-group">
            <label>Pilih File JSON</label>
            <select id="file_select">
                <?php foreach($files as $file): ?>
                <option value="<?= basename($file) ?>">
                    <?= basename($file) ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label>App Name</label>
            <input id="app_name">
        </div>

        <div class="form-group">
            <label>Version</label>
            <input id="app_version">
        </div>

        <div class="form-group">
            <label>Description</label>
            <textarea id="app_description"></textarea>
        </div>

        <div class="form-group">
            <label>Author</label>
            <input id="author">
        </div>

        <div class="form-group">
            <label>Base URL</label>
            <input id="base_url">
        </div>

        <div class="form-group">
            <label>Timeout</label>
            <input id="default_timeout" type="number">
        </div>

        <div class="form-group">
            <label>Theme</label>
            <select id="theme">
                <option value="light">Light</option>
                <option value="dark">Dark</option>
            </select>
        </div>

        <div class="buttons">
            <button class="save" onclick="saveFile()">Save</button>
            <button class="download" onclick="downloadJSON()">Download JSON</button>
        </div>

    </div>

    <script>
    let currentFile = null

    document.getElementById("file_select").addEventListener("change", loadJSON)

    async function loadJSON() {

        const file = document.getElementById("file_select").value
        currentFile = file

        const res = await fetch("json/" + file)
        const data = await res.json()

        setFormData(data)

    }

    function setFormData(data) {

        app_name.value = data.app_name || ''
        app_version.value = data.app_version || ''
        app_description.value = data.app_description || ''
        author.value = data.author || ''
        base_url.value = data.base_url || ''
        default_timeout.value = data.default_timeout || ''
        theme.value = data.theme || 'light'

    }

    function getFormData() {

        return {
            file: currentFile,
            app_name: app_name.value,
            app_version: app_version.value,
            app_description: app_description.value,
            author: author.value,
            base_url: base_url.value,
            default_timeout: parseInt(default_timeout.value),
            theme: theme.value
        }

    }

    async function saveFile() {

        const data = getFormData()

        const res = await fetch("save_json.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify(data)
        })

        const result = await res.json()

        alert(result.message)

    }

    function downloadJSON() {

        const data = getFormData()

        delete data.file

        const blob = new Blob(
            [JSON.stringify(data, null, 4)], {
                type: "application/json"
            }
        )

        const a = document.createElement("a")
        a.href = URL.createObjectURL(blob)
        a.download = currentFile
        a.click()

    }

    loadJSON()
    </script>

</body>

</html>