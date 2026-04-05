<?php
// $jsonDirectory = isset($_GET['dir']) ? $_GET['dir'] : 'json';
// if (!is_dir($jsonDirectory)) {
//     $jsonDirectory = 'json';
// }
// $files = glob($jsonDirectory . "/*.json");
// $currentDir = $jsonDirectory;
$baseDir = __DIR__;

$jsonDirectory = isset($_GET['dir']) ? $_GET['dir'] : 'json';

// sanitize: hapus leading slash biar gak jadi absolute
$jsonDirectory = ltrim($jsonDirectory, '/\\');

// full path
$fullPath = realpath($baseDir . DIRECTORY_SEPARATOR . $jsonDirectory);

// validasi: harus di dalam project
if ($fullPath === false || !str_starts_with($fullPath, realpath($baseDir))) {
    $fullPath = realpath($baseDir . DIRECTORY_SEPARATOR . 'json');
}

$files = glob($fullPath . "/*.json");
$currentDir = $jsonDirectory;
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ConfigForge · Dark Edition</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;14..32,400;14..32,500;14..32,600;14..32,700&display=swap"
        rel="stylesheet">
    <style>
    * {
        font-family: 'Inter', system-ui, -apple-system, sans-serif;
    }

    body {
        background: #0a0c10;
        background-image: radial-gradient(circle at 25% 0%, rgba(30, 35, 48, 0.9) 0%, #0a0c10 90%);
        min-height: 100vh;
    }

    ::-webkit-scrollbar {
        width: 8px;
        height: 8px;
    }

    ::-webkit-scrollbar-track {
        background: #1a1d24;
        border-radius: 10px;
    }

    ::-webkit-scrollbar-thumb {
        background: #3b3f4a;
        border-radius: 10px;
    }

    ::-webkit-scrollbar-thumb:hover {
        background: #4f5563;
    }

    .glass-dark {
        background: rgba(18, 22, 28, 0.85);
        backdrop-filter: blur(12px);
        border: 1px solid rgba(55, 65, 81, 0.4);
        box-shadow: 0 25px 45px -12px rgba(0, 0, 0, 0.5);
    }

    .input-dark {
        background: #0f1117;
        border: 1px solid #2a2f3a;
        transition: all 0.2s ease;
        color: #e2e8f0;
    }

    .input-dark:focus {
        border-color: #6366f1;
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.15);
        outline: none;
    }

    .input-dark:hover {
        border-color: #4a4f5e;
    }

    select.input-dark {
        background-color: #0f1117;
        cursor: pointer;
    }

    .btn-gradient-save {
        background: linear-gradient(135deg, #0d9488 0%, #14b8a6 100%);
        transition: all 0.25s;
    }

    .btn-gradient-save:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px -8px #0d948880;
        filter: brightness(1.05);
    }

    .btn-gradient-download {
        background: linear-gradient(135deg, #4f46e5 0%, #6366f1 100%);
        transition: all 0.25s;
    }

    .btn-gradient-download:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px -8px #4f46e580;
        filter: brightness(1.05);
    }

    .card-hover {
        transition: transform 0.2s, box-shadow 0.2s;
    }

    .card-hover:hover {
        transform: translateY(-3px);
        box-shadow: 0 20px 35px -12px rgba(0, 0, 0, 0.4);
    }

    .section-divider {
        border: none;
        border-top: 1px solid rgba(55, 65, 81, 0.4);
        margin: 1.5rem 0;
    }

    .section-label {
        font-size: 10px;
        font-weight: 600;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        color: #6366f1;
        display: flex;
        align-items: center;
        gap: 6px;
        margin-bottom: 12px;
    }

    .section-label::after {
        content: '';
        flex: 1;
        height: 1px;
        background: rgba(99, 102, 241, 0.2);
    }

    .tag-checkbox {
        display: none;
    }

    .tag-label {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 4px 12px;
        border-radius: 9999px;
        border: 1px solid #2a2f3a;
        background: #0f1117;
        color: #9ca3af;
        font-size: 12px;
        font-family: 'JetBrains Mono', monospace;
        cursor: pointer;
        transition: all 0.15s;
        user-select: none;
    }

    .tag-checkbox:checked+.tag-label {
        background: rgba(99, 102, 241, 0.15);
        border-color: #6366f1;
        color: #a5b4fc;
    }

    .tag-label:hover {
        border-color: #4a4f5e;
        color: #d1d5db;
    }

    .color-preview {
        width: 14px;
        height: 14px;
        border-radius: 50%;
        display: inline-block;
        border: 1px solid rgba(255, 255, 255, 0.1);
    }
    </style>
</head>

<body class="antialiased p-4 md:p-6 lg:p-8">
    <div class="max-w-5xl mx-auto">

        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between flex-wrap gap-4">
                <div>
                    <div class="flex items-center gap-2 mb-2">
                        <div
                            class="w-8 h-8 rounded-lg bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center shadow-lg">
                            <i class="fas fa-cog text-white text-sm"></i>
                        </div>
                        <span
                            class="text-xs font-mono text-indigo-400 bg-indigo-950/50 px-3 py-1 rounded-full border border-indigo-800/50">
                            <i class="fas fa-terminal mr-1"></i> config forge · dark studio
                        </span>
                    </div>
                    <h1 class="text-3xl md:text-4xl font-bold text-white tracking-tight">
                        Website <span
                            class="text-transparent bg-clip-text bg-gradient-to-r from-indigo-400 to-teal-400">Settings</span>
                    </h1>
                    <p class="text-gray-400 mt-2 text-sm">manage application metadata, versioning & behavior profiles
                    </p>
                </div>
                <div class="flex items-center gap-2 text-xs">
                    <div class="flex items-center gap-1.5 bg-black/30 px-3 py-1.5 rounded-full border border-gray-800">
                        <i class="fas fa-moon text-indigo-300 text-[11px]"></i>
                        <span class="text-gray-400">dark mode</span>
                        <div class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse ml-1"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Card -->
        <div class="glass-dark rounded-2xl overflow-hidden card-hover transition-all duration-300">
            <!-- Card header -->
            <div
                class="px-6 py-4 border-b border-gray-800/80 flex items-center justify-between flex-wrap gap-3 bg-black/20">
                <div class="flex items-center gap-2">
                    <i class="fas fa-sliders-h text-indigo-400 text-sm"></i>
                    <span class="text-gray-300 font-medium">configuration editor</span>
                    <span class="text-[10px] bg-gray-800 text-gray-400 px-2 py-0.5 rounded-full">live sync</span>
                </div>
                <div class="flex gap-1.5">
                    <div class="w-2.5 h-2.5 rounded-full bg-red-500/60"></div>
                    <div class="w-2.5 h-2.5 rounded-full bg-yellow-500/60"></div>
                    <div class="w-2.5 h-2.5 rounded-full bg-green-500/60"></div>
                </div>
            </div>

            <div class="p-6 md:p-7">

                <!-- Directory Configuration -->
                <div class="mb-7 bg-black/30 rounded-xl p-4 border border-gray-800/60">
                    <div class="flex flex-col md:flex-row md:items-center gap-4">
                        <div class="flex items-center gap-2 flex-1">
                            <i class="fas fa-folder-tree text-indigo-400 text-sm"></i>
                            <span class="text-gray-300 text-sm font-medium">JSON Directory:</span>
                            <code id="currentDirDisplay"
                                class="bg-gray-900 px-2 py-1 rounded text-indigo-300 text-xs font-mono"><?= htmlspecialchars($currentDir) ?></code>
                        </div>
                        <div class="flex gap-2">
                            <input type="text" id="dirInput" placeholder="custom path (e.g., config/data)"
                                class="flex-1 px-3 py-2 bg-gray-900 border border-gray-700 rounded-lg text-gray-300 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none">
                            <button id="changeDirBtn"
                                class="px-4 py-2 bg-indigo-950/60 hover:bg-indigo-900/60 border border-indigo-800/50 rounded-lg text-indigo-300 text-sm transition flex items-center gap-2">
                                <i class="fas fa-sync-alt text-xs"></i> Set
                            </button>
                        </div>
                    </div>
                    <p class="text-xs text-gray-500 mt-3 flex items-center gap-1">
                        <i class="fas fa-info-circle text-[10px]"></i>
                        Path relatif dari root atau absolut (contoh: ../config, ./data/json, json)
                    </p>
                </div>

                <!-- File Selection -->
                <div class="mb-7 bg-gray-900/40 rounded-xl p-4 border border-gray-800">
                    <label class="block text-sm font-medium text-gray-300 mb-2 flex items-center gap-2">
                        <i class="fas fa-database text-indigo-400"></i>
                        Configuration File
                        <span id="fileCountBadge"
                            class="text-[10px] bg-gray-800 text-gray-400 px-2 py-0.5 rounded-full"></span>
                    </label>
                    <div class="relative">
                        <select id="file_select"
                            class="input-dark w-full px-4 py-2.5 rounded-xl appearance-none cursor-pointer">
                            <?php foreach($files as $file): ?>
                            <option value="<?= htmlspecialchars(basename($file)) ?>">📄
                                <?= htmlspecialchars(basename($file)) ?></option>
                            <?php endforeach; ?>
                            <?php if(empty($files)): ?>
                            <option disabled>⚠️ no JSON files found in "<?= htmlspecialchars($currentDir) ?>"</option>
                            <?php endif; ?>
                        </select>
                        <i
                            class="fas fa-chevron-down absolute right-3 top-3.5 text-gray-500 text-xs pointer-events-none"></i>
                    </div>
                </div>

                <!-- ═══ SECTION: App Identity ═══ -->
                <div class="section-label"><i class="fas fa-id-card"></i> App Identity</div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1.5 flex items-center gap-1.5">
                            <i class="fas fa-trademark text-indigo-400 text-xs"></i> App Name
                        </label>
                        <input id="app_name" type="text" placeholder="Nexus Dashboard"
                            class="input-dark w-full px-4 py-2.5 rounded-lg">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1.5 flex items-center gap-1.5">
                            <i class="fas fa-tag text-indigo-400 text-xs"></i> Version
                        </label>
                        <input id="app_version" type="text" placeholder="1.0.0"
                            class="input-dark w-full px-4 py-2.5 rounded-lg">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1.5 flex items-center gap-1.5">
                            <i class="fas fa-user-astronaut text-indigo-400 text-xs"></i> Author
                        </label>
                        <input id="author" type="text" placeholder="Studio / Creator"
                            class="input-dark w-full px-4 py-2.5 rounded-lg">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1.5 flex items-center gap-1.5">
                            <i class="fas fa-palette text-indigo-400 text-xs"></i> Theme
                        </label>
                        <select id="theme"
                            class="input-dark w-full px-4 py-2.5 rounded-lg appearance-none cursor-pointer">
                            <option value="light">☀️ Light</option>
                            <option value="dark">🌙 Dark (Default)</option>
                        </select>
                    </div>
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-300 mb-1.5 flex items-center gap-1.5">
                        <i class="fas fa-align-left text-indigo-400 text-xs"></i> Description
                    </label>
                    <textarea id="app_description" rows="2" placeholder="Describe your application..."
                        class="input-dark w-full px-4 py-2.5 rounded-lg resize-y"></textarea>
                </div>

                <hr class="section-divider">

                <!-- ═══ SECTION: Request Defaults ═══ -->
                <div class="section-label"><i class="fas fa-paper-plane"></i> Request Defaults</div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1.5 flex items-center gap-1.5">
                            <i class="fas fa-project-diagram text-indigo-400 text-xs"></i> Default Method
                        </label>
                        <select id="default_method"
                            class="input-dark w-full px-4 py-2.5 rounded-lg appearance-none cursor-pointer">
                            <option value="GET">GET</option>
                            <option value="POST">POST</option>
                            <option value="PUT">PUT</option>
                            <option value="PATCH">PATCH</option>
                            <option value="DELETE">DELETE</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1.5 flex items-center gap-1.5">
                            <i class="fas fa-code text-indigo-400 text-xs"></i> Default Body Mode
                        </label>
                        <select id="default_body_mode"
                            class="input-dark w-full px-4 py-2.5 rounded-lg appearance-none cursor-pointer">
                            <option value="none">none</option>
                            <option value="json">JSON</option>
                            <option value="formdata">form-data</option>
                            <option value="urlencoded">urlencoded</option>
                            <option value="raw">raw</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1.5 flex items-center gap-1.5">
                            <i class="fas fa-lock text-indigo-400 text-xs"></i> Default Auth Type
                        </label>
                        <select id="default_auth_type"
                            class="input-dark w-full px-4 py-2.5 rounded-lg appearance-none cursor-pointer">
                            <option value="none">No Auth</option>
                            <option value="bearer">Bearer Token</option>
                            <option value="basic">Basic Auth</option>
                            <option value="apikey">API Key</option>
                        </select>
                    </div>
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-300 mb-3 flex items-center gap-1.5">
                        <i class="fas fa-list-ul text-indigo-400 text-xs"></i> Available HTTP Methods
                        <span class="text-[10px] text-gray-500 font-normal">(shown in method dropdown)</span>
                    </label>
                    <div class="flex flex-wrap gap-2" id="methodsCheckboxes">
                        <?php
                    $allMethods = ['GET','POST','PUT','PATCH','DELETE'];
                    $methodColors = ['GET'=>'#6bbd5b','POST'=>'#f5a623','PUT'=>'#4a9eff','PATCH'=>'#c97bff','DELETE'=>'#f85149'];
                    foreach($allMethods as $m): ?>
                        <div>
                            <input type="checkbox" class="tag-checkbox method-check" id="method_<?= $m ?>"
                                value="<?= $m ?>" checked>
                            <label for="method_<?= $m ?>" class="tag-label" style="">
                                <span class="color-preview" style="background:<?= $methodColors[$m] ?>"></span>
                                <?= $m ?>
                            </label>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <hr class="section-divider">

                <!-- ═══ SECTION: Network & Proxy ═══ -->
                <div class="section-label"><i class="fas fa-network-wired"></i> Network & Proxy</div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1.5 flex items-center gap-1.5">
                            <i class="fas fa-server text-indigo-400 text-xs"></i> Proxy URL
                            <span class="text-[10px] text-gray-500">(used for all requests)</span>
                        </label>
                        <input id="proxy_url" type="text" placeholder="/proxy.php"
                            class="input-dark w-full px-4 py-2.5 rounded-lg font-mono text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1.5 flex items-center gap-1.5">
                            <i class="fas fa-hourglass-half text-indigo-400 text-xs"></i> Default Timeout (ms)
                        </label>
                        <input id="default_timeout" type="number" placeholder="5000"
                            class="input-dark w-full px-4 py-2.5 rounded-lg">
                    </div>
                </div>

                <hr class="section-divider">

                <!-- ═══ SECTION: UI & Behavior ═══ -->
                <div class="section-label"><i class="fas fa-desktop"></i> UI & Behavior</div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1.5 flex items-center gap-1.5">
                            <i class="fas fa-history text-indigo-400 text-xs"></i> History Max Items
                        </label>
                        <input id="history_max_items" type="number" placeholder="100" min="10" max="1000"
                            class="input-dark w-full px-4 py-2.5 rounded-lg">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1.5 flex items-center gap-1.5">
                            <i class="fas fa-arrows-alt-h text-indigo-400 text-xs"></i> Sidebar Width (px)
                        </label>
                        <input id="sidebar_width" type="number" placeholder="260" min="160" max="500"
                            class="input-dark w-full px-4 py-2.5 rounded-lg">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1.5 flex items-center gap-1.5">
                            <i class="fas fa-circle text-indigo-400 text-xs"></i> Accent Color
                            <span id="accentPreview" class="color-preview ml-1" style="background:#ff6c37"></span>
                        </label>
                        <input id="accent_color" type="text" placeholder="#ff6c37"
                            class="input-dark w-full px-4 py-2.5 rounded-lg font-mono text-sm"
                            oninput="updateAccentPreview(this.value)">
                    </div>
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-300 mb-3 flex items-center gap-1.5">
                        <i class="fas fa-toggle-on text-indigo-400 text-xs"></i> Feature Toggles
                    </label>
                    <div class="flex flex-wrap gap-3">
                        <div class="flex items-center gap-2 bg-black/30 rounded-lg px-4 py-2.5 border border-gray-800 cursor-pointer"
                            onclick="toggleCheckbox('show_shortcut_hints')">
                            <input type="checkbox" id="show_shortcut_hints" class="accent-indigo-500 cursor-pointer"
                                checked>
                            <label for="show_shortcut_hints"
                                class="text-sm text-gray-300 cursor-pointer flex items-center gap-1.5">
                                <i class="fas fa-keyboard text-indigo-400 text-xs"></i> Show Shortcut Hints
                            </label>
                        </div>
                    </div>
                </div>

                <hr class="section-divider">

                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row gap-4 pt-2">
                    <button id="saveBtn"
                        class="btn-gradient-save flex-1 py-3 rounded-xl text-white font-semibold flex items-center justify-center gap-2 transition-all">
                        <i class="fas fa-save"></i> Save to Server
                    </button>
                    <button id="downloadBtn"
                        class="btn-gradient-download flex-1 py-3 rounded-xl text-white font-semibold flex items-center justify-center gap-2 transition-all">
                        <i class="fas fa-download"></i> Download JSON
                    </button>
                </div>
                <p class="text-center text-[11px] text-gray-500 mt-5 flex items-center justify-center gap-2">
                    <i class="fas fa-server"></i> Save → overwrites original file · Download → exports local copy
                </p>
            </div>
        </div>

        <!-- Footer -->
        <div class="mt-6 text-center text-xs text-gray-600 flex justify-center gap-4">
            <span><i class="fas fa-dragon"></i> config forge v2</span>
            <span>•</span>
            <span><i class="fas fa-moon"></i> dark interface</span>
        </div>
    </div>

    <!-- Toast -->
    <div id="toast"
        class="fixed bottom-6 left-1/2 transform -translate-x-1/2 z-50 opacity-0 pointer-events-none transition-all duration-300">
        <div
            class="bg-gray-900/95 backdrop-filter backdrop-blur-md border border-gray-700 rounded-xl shadow-2xl px-5 py-3 flex items-center gap-3 min-w-[240px]">
            <i id="toastIcon" class="fas fa-check-circle text-emerald-400"></i>
            <span id="toastMsg" class="text-sm text-gray-200">Action completed</span>
        </div>
    </div>

    <script>
    const fileSelect = document.getElementById('file_select');
    const saveBtn = document.getElementById('saveBtn');
    const downloadBtn = document.getElementById('downloadBtn');
    const changeDirBtn = document.getElementById('changeDirBtn');
    const dirInput = document.getElementById('dirInput');
    const currentDirDisplay = document.getElementById('currentDirDisplay');

    let currentFile = null;
    let currentDirectory = '<?= addslashes($currentDir) ?>';

    // ─── Toast ───
    function showToast(message, isError = false) {
        const toast = document.getElementById('toast');
        const toastIcon = document.getElementById('toastIcon');
        const toastMsg = document.getElementById('toastMsg');
        toastMsg.innerText = message;
        toastIcon.className = isError ?
            "fas fa-exclamation-triangle text-amber-400" :
            "fas fa-check-circle text-emerald-400";
        toast.classList.remove('opacity-0', 'pointer-events-none');
        toast.classList.add('opacity-100', 'pointer-events-auto');
        setTimeout(() => {
            toast.classList.remove('opacity-100', 'pointer-events-auto');
            toast.classList.add('opacity-0', 'pointer-events-none');
        }, 3000);
    }

    // ─── Accent preview ───
    function updateAccentPreview(val) {
        if (/^#[0-9a-fA-F]{3,6}$/.test(val)) {
            document.getElementById('accentPreview').style.background = val;
        }
    }

    // ─── Checkbox toggle helper ───
    function toggleCheckbox(id) {
        const el = document.getElementById(id);
        el.checked = !el.checked;
    }

    // ─── Get available_methods from checkboxes ───
    function getSelectedMethods() {
        return [...document.querySelectorAll('.method-check:checked')].map(c => c.value);
    }

    function setSelectedMethods(methods) {
        document.querySelectorAll('.method-check').forEach(c => {
            c.checked = !methods || methods.length === 0 || methods.includes(c.value);
        });
    }

    // ─── Load JSON ───
    async function loadJSON() {
        if (!fileSelect.value || fileSelect.options[0]?.disabled) return;
        currentFile = fileSelect.value;
        try {
            const url =
                `fetch_json.php?dir=${encodeURIComponent(currentDirectory)}&file=${encodeURIComponent(currentFile)}`;
            const res = await fetch(url);
            if (!res.ok) throw new Error(`HTTP ${res.status}`);
            const data = await res.json();
            setFormData(data);
        } catch (err) {
            showToast(`Failed to load: ${err.message}`, true);
            setFormData({});
        }
    }

    function setFormData(data) {
        // Identity
        document.getElementById('app_name').value = data.app_name || '';
        document.getElementById('app_version').value = data.app_version || '';
        document.getElementById('app_description').value = data.app_description || '';
        document.getElementById('author').value = data.author || '';
        document.getElementById('theme').value = ['dark', 'light'].includes(data.theme) ? data.theme : 'dark';

        // Network
        document.getElementById('proxy_url').value = data.proxy_url || '/proxy.php';
        document.getElementById('default_timeout').value = data.default_timeout != null ? data.default_timeout : 5000;

        // Request defaults
        document.getElementById('default_method').value = data.default_method || 'GET';
        document.getElementById('default_body_mode').value = data.default_body_mode || 'none';
        document.getElementById('default_auth_type').value = data.default_auth_type || 'none';

        // Available methods
        setSelectedMethods(data.available_methods || []);

        // UI
        document.getElementById('history_max_items').value = data.history_max_items != null ? data.history_max_items :
            100;
        document.getElementById('sidebar_width').value = data.sidebar_width != null ? data.sidebar_width : 260;

        const accentVal = data.accent_color || '#ff6c37';
        document.getElementById('accent_color').value = accentVal;
        updateAccentPreview(accentVal);

        document.getElementById('show_shortcut_hints').checked = data.show_shortcut_hints !== false;
    }

    function getFormData() {
        const timeoutRaw = document.getElementById('default_timeout').value;
        const historyRaw = document.getElementById('history_max_items').value;
        const sidebarRaw = document.getElementById('sidebar_width').value;

        return {
            file: currentFile,
            directory: currentDirectory,
            app_name: document.getElementById('app_name').value,
            app_version: document.getElementById('app_version').value,
            app_description: document.getElementById('app_description').value,
            author: document.getElementById('author').value,
            theme: document.getElementById('theme').value,
            proxy_url: document.getElementById('proxy_url').value,
            default_timeout: timeoutRaw !== '' ? parseInt(timeoutRaw) : null,
            default_method: document.getElementById('default_method').value,
            default_body_mode: document.getElementById('default_body_mode').value,
            default_auth_type: document.getElementById('default_auth_type').value,
            available_methods: getSelectedMethods(),
            history_max_items: historyRaw !== '' ? parseInt(historyRaw) : 100,
            sidebar_width: sidebarRaw !== '' ? parseInt(sidebarRaw) : 260,
            accent_color: document.getElementById('accent_color').value,
            show_shortcut_hints: document.getElementById('show_shortcut_hints').checked,
        };
    }

    // ─── Save to server ───
    async function saveToServer() {
        if (!currentFile) {
            showToast("Select a JSON file first", true);
            return;
        }
        const data = getFormData();
        try {
            const res = await fetch("save_json.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json"
                },
                body: JSON.stringify(data)
            });
            const result = await res.json();
            if (res.ok && result.status !== 'error') showToast(result.message || "Saved ✨");
            else showToast(result.message || "Error saving", true);
        } catch (err) {
            showToast("Network error", true);
        }
    }

    // ─── Download JSON ───
    function downloadJSON() {
        if (!currentFile) {
            showToast("No file selected", true);
            return;
        }
        const {
            file,
            directory,
            ...clean
        } = getFormData();
        const blob = new Blob([JSON.stringify(clean, null, 2)], {
            type: "application/json"
        });
        const a = document.createElement('a');
        a.href = URL.createObjectURL(blob);
        a.download = currentFile;
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        URL.revokeObjectURL(a.href);
        showToast(`📁 Downloaded ${currentFile}`);
    }

    // ─── Change directory ───
    async function changeDirectory() {
        const newDir = dirInput.value.trim();
        if (!newDir) {
            showToast("Enter a directory path", true);
            return;
        }
        try {
            const res = await fetch(`list_files.php?dir=${encodeURIComponent(newDir)}`);
            const result = await res.json();
            if (result.status === 'success') {
                currentDirectory = newDir;
                currentDirDisplay.innerText = currentDirectory;
                fileSelect.innerHTML = '';
                if (result.files?.length > 0) {
                    result.files.forEach(f => {
                        const o = document.createElement('option');
                        o.value = f;
                        o.textContent = `📄 ${f}`;
                        fileSelect.appendChild(o);
                    });
                    currentFile = result.files[0];
                    loadJSON();
                    showToast(`Directory set to "${currentDirectory}" · ${result.files.length} file(s)`);
                } else {
                    fileSelect.innerHTML = '<option disabled>⚠️ no JSON files found</option>';
                    currentFile = null;
                    setFormData({});
                    showToast(`No JSON files in "${currentDirectory}"`, true);
                }
                const badge = document.getElementById('fileCountBadge');
                if (badge) badge.innerText = `${result.files?.length || 0} file(s)`;
            } else {
                showToast(result.message || "Invalid directory", true);
            }
        } catch (err) {
            showToast("Failed to access directory", true);
        }
    }

    // ─── Events ───
    fileSelect.addEventListener('change', loadJSON);
    saveBtn.addEventListener('click', saveToServer);
    downloadBtn.addEventListener('click', downloadJSON);
    changeDirBtn.addEventListener('click', changeDirectory);
    dirInput.addEventListener('keypress', e => {
        if (e.key === 'Enter') changeDirectory();
    });

    // ─── Init ───
    if (fileSelect.options.length > 0 && fileSelect.value && !fileSelect.options[0]?.disabled) {
        loadJSON();
        document.getElementById('fileCountBadge').innerText = `${fileSelect.options.length} file(s)`;
    } else {
        saveBtn.disabled = downloadBtn.disabled = true;
        saveBtn.style.opacity = downloadBtn.style.opacity = '0.5';
        saveBtn.style.cursor = downloadBtn.style.cursor = 'not-allowed';
    }
    </script>
</body>

</html>