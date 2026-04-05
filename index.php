<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>API Client</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=JetBrains+Mono:wght@300;400;500;600&display=swap"
        rel="stylesheet">
    <link href="css/root.css" rel="stylesheet">
    <link href="css/scrollBar.css" rel="stylesheet">
    <link href="css/topBar.css" rel="stylesheet">
    <link href="css/tabsBar.css" rel="stylesheet">
    <link href="css/layout.css" rel="stylesheet">
    <link href="css/sideBar.css" rel="stylesheet">
    <link href="css/collectionTree.css" rel="stylesheet">
    <link href="css/history.css" rel="stylesheet">
    <link href="css/requestBar.css" rel="stylesheet">
    <link href="css/splitPane.css" rel="stylesheet">
    <link href="css/innerTabs.css" rel="stylesheet">
    <link href="css/kvTable.css" rel="stylesheet">
    <link href="css/bodyEditor.css" rel="stylesheet">
    <link href="css/auth.css" rel="stylesheet">
    <link href="css/response.css" rel="stylesheet">
    <link href="css/jsonSyntax.css" rel="stylesheet">
    <link href="css/contextMenu.css" rel="stylesheet">
    <link href="css/toast.css" rel="stylesheet">
    <link href="css/empty.css" rel="stylesheet">
    <link href="css/resizer.css" rel="stylesheet">
    <link href="css/misc.css" rel="stylesheet">
    <style>
    /* ─── MAIN AREA ─── */
    .main {
        flex: 1;
        display: flex;
        flex-direction: column;
        overflow: hidden;
        background: var(--pm-bg);
    }
    </style>
</head>

<body>

    <!-- ─── TOPBAR ─── -->
    <div class="topbar">
        <div class="logo-pm">
            <div class="logo-icon">
                <svg viewBox="0 0 16 16">
                    <circle cx="8" cy="8" r="6" />
                    <path d="M5 8h6M8 5v6" stroke="#fff" stroke-width="1.5" fill="none" stroke-linecap="round" />
                </svg>
            </div>
            <span class="logo-name" id="appName">API Client</span>
        </div>
        <div class="topbar-sep"></div>
        <div class="shortcut-hint" id="shortcutHint">
            <kbd>Ctrl</kbd>+<kbd>S</kbd> save · <kbd>Ctrl</kbd>+<kbd>Shift</kbd>+<kbd>S</kbd> save to collection
        </div>
        <div class="topbar-actions">
            <a href="/settingWeb" class="topbar-btn">
                <svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5">
                    <circle cx="8" cy="8" r="6" />
                    <path d="M2 8h12M8 2a10 10 0 010 12M8 2a10 10 0 000 12" />
                </svg>
                Settings
            </a>
            <button class="topbar-btn" onclick="openEnvModal()">
                <svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5">
                    <circle cx="8" cy="8" r="6" />
                    <path d="M2 8h12M8 2a10 10 0 010 12M8 2a10 10 0 000 12" />
                </svg>
                Environments
            </button>
            <button class="topbar-btn" onclick="exportAllCollections()">
                <svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5">
                    <path d="M8 2v8M4 7l4 4 4-4M2 12v2h12v-2" />
                </svg>
                Export
            </button>
            <label class="topbar-btn" style="cursor:pointer">
                <svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5">
                    <path d="M2 12v2h12v-2M8 10V2M4 5l4-4 4 4" />
                </svg>
                Import
                <input type="file" id="importFile" accept=".json" style="display:none" onchange="importPostman(event)">
            </label>
            <button class="topbar-btn accent" onclick="openNewCollectionModal()">
                <svg viewBox="0 0 16 16" fill="currentColor">
                    <path d="M8 3a1 1 0 011 1v3h3a1 1 0 110 2H9v3a1 1 0 11-2 0V9H4a1 1 0 110-2h3V4a1 1 0 011-1z" />
                </svg>
                New Collection
            </button>
        </div>
    </div>

    <!-- ─── TABS BAR ─── -->
    <div class="tabs-bar" id="tabsBar"></div>

    <!-- ─── BODY ─── -->
    <div class="app-body">

        <!-- SIDEBAR -->
        <div class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <div class="sidebar-tabs">
                    <div class="sidebar-tab active no-select" onclick="switchSidebar('collections',this)">Collections
                    </div>
                    <div class="sidebar-tab no-select" onclick="switchSidebar('history',this)">History</div>
                </div>
                <div class="sidebar-search">
                    <span class="sidebar-search-icon">
                        <svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5">
                            <circle cx="6.5" cy="6.5" r="4.5" />
                            <path d="M11 11l3 3" />
                        </svg>
                    </span>
                    <input type="text" placeholder="Search requests…" oninput="searchRequests(this.value)">
                </div>
                <button class="sidebar-new-btn" onclick="openNewCollectionModal()">
                    <svg viewBox="0 0 16 16" fill="currentColor" width="12" height="12">
                        <path d="M8 3a1 1 0 011 1v3h3a1 1 0 110 2H9v3a1 1 0 11-2 0V9H4a1 1 0 110-2h3V4a1 1 0 011-1z" />
                    </svg>
                    New Collection
                </button>
            </div>
            <div class="sidebar-content" id="sidebarContent"></div>
            <div class="sidebar-resizer" id="sidebarResizer"></div>
        </div>

        <!-- MAIN -->
        <div class="main">

            <!-- REQUEST BAR -->
            <div class="req-bar">
                <select class="method-sel" id="methodSel" onchange="onMethodChange();markDirty()">
                    <option>GET</option>
                    <option>POST</option>
                    <option>PUT</option>
                    <option>PATCH</option>
                    <option>DELETE</option>
                </select>
                <div class="url-wrap">
                    <input class="url-input" id="urlInput" type="text" placeholder="Enter request URL or paste cURL"
                        oninput="markDirty()">
                </div>
                <div class="spinner" id="spinner"></div>
                <button class="send-btn" id="sendBtn" onclick="sendRequest()">
                    Send
                    <svg viewBox="0 0 16 16" fill="currentColor">
                        <path d="M2 2l12 6-12 6V9.5l8-1.5-8-1.5V2z" />
                    </svg>
                </button>
            </div>

            <!-- SPLIT PANE -->
            <div class="split-pane" id="splitPane">

                <!-- REQUEST PANEL -->
                <div class="pane-request" id="paneRequest">
                    <div class="inner-tabs" id="reqTabs">
                        <div class="itab active no-select" onclick="switchInner('params',this,'reqTabs','reqPanes')">
                            Params <span class="itab-count" id="cnt-params">0</span>
                        </div>
                        <div class="itab no-select" onclick="switchInner('auth',this,'reqTabs','reqPanes')">Auth</div>
                        <div class="itab no-select" onclick="switchInner('headers',this,'reqTabs','reqPanes')">
                            Headers <span class="itab-count" id="cnt-headers">0</span>
                        </div>
                        <div class="itab no-select" onclick="switchInner('body',this,'reqTabs','reqPanes')">Body</div>
                        <div class="itab no-select" onclick="switchInner('pre-req',this,'reqTabs','reqPanes')">Pre-req
                        </div>
                    </div>

                    <div id="reqPanes" style="flex:1;overflow:hidden;display:flex;flex-direction:column;">

                        <!-- PARAMS -->
                        <div class="tab-pane active" id="pane-params" style="flex-direction:column;">
                            <table class="kv-table" id="paramTable">
                                <thead>
                                    <tr>
                                        <th style="width:22px"></th>
                                        <th>Key</th>
                                        <th>Value</th>
                                        <th style="width:28px"></th>
                                    </tr>
                                </thead>
                                <tbody id="paramRows"></tbody>
                            </table>
                            <button class="kv-add-row"
                                onclick="addKvRow('paramRows','param');updateCounters();markDirty()">
                                <svg viewBox="0 0 16 16" fill="currentColor">
                                    <path
                                        d="M8 3a1 1 0 011 1v3h3a1 1 0 110 2H9v3a1 1 0 11-2 0V9H4a1 1 0 110-2h3V4a1 1 0 011-1z" />
                                </svg>
                                Add Param
                            </button>
                        </div>

                        <!-- AUTH -->
                        <div class="tab-pane" id="pane-auth">
                            <div class="auth-wrap">
                                <div class="mform-group">
                                    <label>Auth Type</label>
                                    <select class="auth-type-sel" id="authType" onchange="toggleAuth();markDirty()">
                                        <option value="none">No Auth</option>
                                        <option value="bearer">Bearer Token</option>
                                        <option value="basic">Basic Auth</option>
                                        <option value="apikey">API Key</option>
                                    </select>
                                </div>
                                <div id="authFields"></div>
                            </div>
                        </div>

                        <!-- HEADERS -->
                        <div class="tab-pane" id="pane-headers" style="flex-direction:column;">
                            <table class="kv-table" id="headerTable">
                                <thead>
                                    <tr>
                                        <th style="width:22px"></th>
                                        <th>Key</th>
                                        <th>Value</th>
                                        <th style="width:28px"></th>
                                    </tr>
                                </thead>
                                <tbody id="headerRows"></tbody>
                            </table>
                            <button class="kv-add-row"
                                onclick="addKvRow('headerRows','header');updateCounters();markDirty()">
                                <svg viewBox="0 0 16 16" fill="currentColor">
                                    <path
                                        d="M8 3a1 1 0 011 1v3h3a1 1 0 110 2H9v3a1 1 0 11-2 0V9H4a1 1 0 110-2h3V4a1 1 0 011-1z" />
                                </svg>
                                Add Header
                            </button>
                        </div>

                        <!-- BODY -->
                        <div class="tab-pane" id="pane-body" style="flex-direction:column;">
                            <div class="body-modes" id="bodyModes">
                                <button class="body-mode-btn active" onclick="setBodyMode('none',this)">none</button>
                                <button class="body-mode-btn" onclick="setBodyMode('json',this)">JSON</button>
                                <button class="body-mode-btn" onclick="setBodyMode('formdata',this)">form-data</button>
                                <button class="body-mode-btn"
                                    onclick="setBodyMode('urlencoded',this)">urlencoded</button>
                                <button class="body-mode-btn" onclick="setBodyMode('raw',this)">raw</button>
                            </div>
                            <div id="bodyContent" style="flex:1;display:flex;flex-direction:column;overflow:hidden;">
                            </div>
                        </div>

                        <!-- PRE-REQ -->
                        <div class="tab-pane" id="pane-pre-req" style="flex-direction:column;">
                            <textarea class="code-editor"
                                placeholder="// Pre-request script (JavaScript)&#10;// pm.environment.set('variable', 'value');"
                                style="flex:1;" oninput="markDirty()" id="preReqScript"></textarea>
                        </div>

                    </div>
                </div>

                <!-- DIVIDER -->
                <div class="pane-divider" id="paneDivider"></div>

                <!-- RESPONSE PANEL -->
                <div class="pane-response">
                    <div class="inner-tabs" id="resTabs">
                        <div class="itab active no-select" onclick="switchInner('res-body',this,'resTabs','resPanes')">
                            Body</div>
                        <div class="itab no-select" onclick="switchInner('res-headers',this,'resTabs','resPanes')">
                            Headers</div>
                    </div>
                    <div class="res-meta">
                        <span class="status-pill s-none" id="statusPill">—</span>
                        <span class="meta-chip"><b id="timeVal">—</b> ms</span>
                        <span class="meta-chip"><b id="sizeVal">—</b></span>
                        <div class="res-meta-actions">
                            <button class="res-action-btn" onclick="copyResponse()">
                                <svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5">
                                    <rect x="5" y="5" width="9" height="9" rx="1" />
                                    <path d="M2 10V2h8" />
                                </svg>
                                Copy
                            </button>
                            <button class="res-action-btn" onclick="clearResponse()">
                                <svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5">
                                    <path d="M3 3l10 10M13 3L3 13" />
                                </svg>
                                Clear
                            </button>
                        </div>
                    </div>
                    <div id="resPanes" style="flex:1;overflow:hidden;display:flex;flex-direction:column;">
                        <div class="tab-pane active" id="pane-res-body" style="flex-direction:column;">
                            <div class="res-body" id="resBody">
                                <div class="placeholder-state">
                                    <svg viewBox="0 0 48 48" fill="none" stroke="currentColor" stroke-width="1.5">
                                        <path d="M8 8h32v32H8zM16 20h16M16 28h10" />
                                    </svg>
                                    <p>Hit <b>Send</b> to get a response</p>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane" id="pane-res-headers" style="flex-direction:column;">
                            <div class="res-body" id="resHeaders">
                                <div class="placeholder-state">
                                    <svg viewBox="0 0 48 48" fill="none" stroke="currentColor" stroke-width="1.5">
                                        <path d="M8 8h32v32H8zM16 20h16M16 28h10" />
                                    </svg>
                                    <p>No response yet</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div><!-- /split-pane -->
        </div><!-- /main -->
    </div><!-- /app-body -->

    <!-- ─── MODALS ─── -->

    <!-- ENV -->
    <div class="modal-overlay" id="envModal">
        <div class="modal-box" style="width:560px">
            <div class="modal-head">
                <svg width="16" height="16" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5">
                    <circle cx="8" cy="8" r="6" />
                    <path d="M2 8h12M8 2a10 10 0 010 12M8 2a10 10 0 000 12" />
                </svg>
                <h2>Environment Variables</h2>
                <button class="modal-close" onclick="closeModal('envModal')">✕</button>
            </div>
            <div class="modal-body" id="envRows"></div>
            <div class="modal-foot">
                <button class="topbar-btn" onclick="addEnvRow()">+ Add Variable</button>
                <button class="topbar-btn accent" onclick="saveEnv()">Save</button>
            </div>
        </div>
    </div>

    <!-- COLLECTION -->
    <div class="modal-overlay" id="colModal">
        <div class="modal-box">
            <div class="modal-head">
                <span id="colModalIcon">📦</span>
                <h2 id="colModalTitle">New Collection</h2>
                <button class="modal-close" onclick="closeModal('colModal')">✕</button>
            </div>
            <div class="modal-body">
                <div class="mform-group"><label>Name *</label><input id="colName" placeholder="My API Collection"
                        onkeydown="if(event.key==='Enter')saveColModal()"></div>
                <div class="mform-group"><label>Description</label><textarea id="colDesc"
                        placeholder="What's this collection for?"></textarea></div>
            </div>
            <div class="modal-foot">
                <button class="topbar-btn" onclick="closeModal('colModal')">Cancel</button>
                <button class="topbar-btn accent" id="colSaveBtn" onclick="saveColModal()">Create</button>
            </div>
        </div>
    </div>

    <!-- FOLDER -->
    <div class="modal-overlay" id="folderModal">
        <div class="modal-box" style="width:400px">
            <div class="modal-head">
                <span>📂</span>
                <h2 id="folderModalTitle">New Folder</h2>
                <button class="modal-close" onclick="closeModal('folderModal')">✕</button>
            </div>
            <div class="modal-body">
                <div class="mform-group"><label>Folder Name *</label><input id="folderName" placeholder="Auth Endpoints"
                        onkeydown="if(event.key==='Enter')saveFolderModal()"></div>
            </div>
            <div class="modal-foot">
                <button class="topbar-btn" onclick="closeModal('folderModal')">Cancel</button>
                <button class="topbar-btn accent" onclick="saveFolderModal()">Create</button>
            </div>
        </div>
    </div>

    <!-- SAVE REQUEST -->
    <div class="modal-overlay" id="saveReqModal">
        <div class="modal-box">
            <div class="modal-head">
                <span>💾</span>
                <h2>Save Request</h2>
                <button class="modal-close" onclick="closeModal('saveReqModal')">✕</button>
            </div>
            <div class="modal-body">
                <div class="mform-group"><label>Request Name *</label><input id="saveReqName"
                        placeholder="Get User Profile" onkeydown="if(event.key==='Enter')doSaveRequest()"></div>
                <div class="mform-group"><label>Collection</label><select id="saveReqCol"
                        onchange="populateSaveReqFolders()"></select></div>
                <div class="mform-group"><label>Folder (optional)</label><select id="saveReqFolder">
                        <option value="">— Root —</option>
                    </select></div>
            </div>
            <div class="modal-foot">
                <button class="topbar-btn" onclick="closeModal('saveReqModal')">Cancel</button>
                <button class="topbar-btn accent" onclick="doSaveRequest()">Save</button>
            </div>
        </div>
    </div>

    <!-- RENAME -->
    <div class="modal-overlay" id="renameModal">
        <div class="modal-box" style="width:380px">
            <div class="modal-head">
                <span>✏️</span>
                <h2>Rename</h2>
                <button class="modal-close" onclick="closeModal('renameModal')">✕</button>
            </div>
            <div class="modal-body">
                <div class="mform-group"><label>New Name *</label><input id="renameName"
                        onkeydown="if(event.key==='Enter')doRename()"></div>
            </div>
            <div class="modal-foot">
                <button class="topbar-btn" onclick="closeModal('renameModal')">Cancel</button>
                <button class="topbar-btn accent" onclick="doRename()">Rename</button>
            </div>
        </div>
    </div>

    <!-- CONTEXT MENU -->
    <div class="ctx-menu" id="ctxMenu"></div>
    <div class="toast" id="toast"></div>

    <script src="js/main.js"></script>

</body>

</html>