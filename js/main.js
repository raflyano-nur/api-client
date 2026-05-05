    // ═══════════════════════════════════════════════════
    //  CONFIG (dari JSON) - defaults sebelum fetch
    // ═══════════════════════════════════════════════════
    let cfg = {
        app_name: 'API Client',
        app_version: '1.0.0',
        proxy_url: '/proxy.php',
        history_max_items: 100,
        default_method: 'GET',
        default_body_mode: 'none',
        default_auth_type: 'none',
        available_methods: ['GET', 'POST', 'PUT', 'PATCH', 'DELETE'],
        sidebar_width: 260,
        accent_color: '#ff6c37',
        show_shortcut_hints: true
    };

    // ═══════════════════════════════════════════════════
    //  STATE
    // ═══════════════════════════════════════════════════
    let collections = JSON.parse(localStorage.getItem('api_collections') || '[]');
    let historyList = JSON.parse(localStorage.getItem('api_history') || '[]');
    let envVars = JSON.parse(localStorage.getItem('api_env') || '{}');
    let tabs = JSON.parse(localStorage.getItem('api_tabs') || '[]');
    let activeTabId = localStorage.getItem('api_active_tab') || null;
    let tabCounter = parseInt(localStorage.getItem('api_tab_ctr') || '0');
    let sidebarMode = 'collections';
    let bodyMode = 'none';
    let requestMap = {};

    let _colMode = 'new',
        _colIdx = -1;
    let _folMode = 'new',
        _folColIdx = -1,
        _folPath = [];
    let _renameTarget = null;
    let _ctxOpen = false;

    // ─── LOAD CONFIG dari JSON lalu init ───
    fetch('json/data-settingWebsite.json')
        .then(r => r.json())
        .then(d => {
            // Merge ke cfg
            if (d.app_name) cfg.app_name = d.app_name;
            if (d.app_version) cfg.app_version = d.app_version;
            if (d.proxy_url) cfg.proxy_url = d.proxy_url;
            if (d.history_max_items) cfg.history_max_items = parseInt(d.history_max_items) || 100;
            if (d.default_method) cfg.default_method = d.default_method;
            if (d.default_body_mode) cfg.default_body_mode = d.default_body_mode;
            if (d.default_auth_type) cfg.default_auth_type = d.default_auth_type;
            if (Array.isArray(d.available_methods) && d.available_methods.length)
                cfg.available_methods = d.available_methods;
            if (d.sidebar_width) cfg.sidebar_width = parseInt(d.sidebar_width) || 260;
            if (d.accent_color) cfg.accent_color = d.accent_color;
            if (d.show_shortcut_hints !== undefined)
                cfg.show_shortcut_hints = d.show_shortcut_hints !== false;

            applyConfig();
        })
        .catch(() => applyConfig()); // fallback ke defaults

    function applyConfig() {
        // 1. App name
        document.getElementById('appName').textContent = cfg.app_name;
        document.title = cfg.app_name;

        // 2. Accent color — update semua CSS vars terkait orange
        if (cfg.accent_color) {
            const r = document.documentElement.style;
            r.setProperty('--pm-orange', cfg.accent_color);
            // Buat versi gelap: 10% lebih gelap (approx)
            r.setProperty('--pm-orange2', darkenHex(cfg.accent_color, 20));
        }

        // 3. Sidebar width awal
        document.getElementById('sidebar').style.width = cfg.sidebar_width + 'px';

        // 4. Shortcut hints
        document.getElementById('shortcutHint').style.display =
            cfg.show_shortcut_hints ? '' : 'none';

        // 5. Method dropdown — isi hanya metode yang diizinkan
        const sel = document.getElementById('methodSel');
        const methodColors = {
            GET: '#6bbd5b',
            POST: '#f5a623',
            PUT: '#4a9eff',
            PATCH: '#c97bff',
            DELETE: '#f85149'
        };
        sel.innerHTML = cfg.available_methods.map(m =>
            `<option value="${m}">${m}</option>`
        ).join('');
        // Set default method jika ada di list
        if (cfg.available_methods.includes(cfg.default_method)) {
            sel.value = cfg.default_method;
        }

        // 6. Default body mode — set active button
        bodyMode = cfg.default_body_mode;
        document.querySelectorAll('.body-mode-btn').forEach(b => {
            const m = b.getAttribute('onclick').match(/setBodyMode\('([^']+)'/);
            if (m) b.classList.toggle('active', m[1] === bodyMode);
        });

        // 7. Init setelah config teraplikasi
        renderEnvRows();
        renderSidebar();
        if (!tabs.length) newTab();
        else {
            renderTabsBar();
            switchToTab((tabs.find(t => t.id === activeTabId) || tabs[0]).id, false);
        }
        onMethodChange();
    }

    // Helper: darken hex color
    function darkenHex(hex, amount) {
        hex = hex.replace('#', '');
        if (hex.length === 3) hex = hex.split('').map(c => c + c).join('');
        let r = Math.max(0, parseInt(hex.slice(0, 2), 16) - amount);
        let g = Math.max(0, parseInt(hex.slice(2, 4), 16) - amount);
        let b = Math.max(0, parseInt(hex.slice(4, 6), 16) - amount);
        return '#' + [r, g, b].map(v => v.toString(16).padStart(2, '0')).join('');
    }

    // ─── EVENT LISTENERS ───
    document.addEventListener('click', e => {
        if (_ctxOpen && !document.getElementById('ctxMenu').contains(e.target)) closeCtx();
    });
    document.addEventListener('keydown', e => {
        if ((e.ctrlKey || e.metaKey) && !e.shiftKey && e.key.toLowerCase() === 's') {
            e.preventDefault();
            saveTab();
        }
        if ((e.ctrlKey || e.metaKey) && e.shiftKey && e.key.toLowerCase() === 's') {
            e.preventDefault();
            saveCurrentState();
            openSaveReqModal();
        }
        if (e.key === 'Escape') closeCtx();
    });

    // ─── RESIZERS ───
    initSidebarResizer();
    initPaneDivider();

    function initSidebarResizer() {
        const sidebar = document.getElementById('sidebar');
        const rsz = document.getElementById('sidebarResizer');
        let dragging = false,
            startX, startW;
        rsz.addEventListener('mousedown', e => {
            dragging = true;
            startX = e.clientX;
            startW = sidebar.offsetWidth;
            document.body.style.userSelect = 'none';
            document.body.style.cursor = 'col-resize';
        });
        document.addEventListener('mousemove', e => {
            if (!dragging) return;
            sidebar.style.width = Math.max(160, Math.min(500, startW + e.clientX - startX)) + 'px';
        });
        document.addEventListener('mouseup', () => {
            dragging = false;
            document.body.style.userSelect = '';
            document.body.style.cursor = '';
        });
    }

    function initPaneDivider() {
        const div = document.getElementById('paneDivider');
        const pane = document.getElementById('paneRequest');
        let dragging = false,
            startX, startW;
        div.addEventListener('mousedown', e => {
            dragging = true;
            startX = e.clientX;
            startW = pane.offsetWidth;
            div.classList.add('dragging');
            document.body.style.userSelect = 'none';
            document.body.style.cursor = 'col-resize';
        });
        document.addEventListener('mousemove', e => {
            if (!dragging) return;
            const w = Math.max(200, Math.min(window.innerWidth * .75, startW + e.clientX - startX));
            pane.style.width = w + 'px';
            pane.style.flex = 'none';
        });
        document.addEventListener('mouseup', () => {
            dragging = false;
            div.classList.remove('dragging');
            document.body.style.userSelect = '';
            document.body.style.cursor = '';
        });
    }

    // ═══════════════════════════════════════════════════
    //  MODALS
    // ═══════════════════════════════════════════════════
    function openModal(id) {
        document.getElementById(id).classList.add('open');
    }

    function closeModal(id) {
        document.getElementById(id).classList.remove('open');
    }

    // ─── COLLECTIONS ───
    function openNewCollectionModal() {
        _colMode = 'new';
        document.getElementById('colModalIcon').textContent = '📦';
        document.getElementById('colModalTitle').textContent = 'New Collection';
        document.getElementById('colSaveBtn').textContent = 'Create';
        document.getElementById('colName').value = '';
        document.getElementById('colDesc').value = '';
        openModal('colModal');
        setTimeout(() => document.getElementById('colName').focus(), 80);
    }

    function openEditColModal(ci) {
        _colMode = 'edit';
        _colIdx = ci;
        const c = collections[ci];
        document.getElementById('colModalIcon').textContent = '✏️';
        document.getElementById('colModalTitle').textContent = 'Edit Collection';
        document.getElementById('colSaveBtn').textContent = 'Save';
        document.getElementById('colName').value = c.name;
        document.getElementById('colDesc').value = c.description || '';
        openModal('colModal');
        setTimeout(() => document.getElementById('colName').focus(), 80);
    }

    function saveColModal() {
        const name = document.getElementById('colName').value.trim();
        if (!name) {
            toast('Name required', 'error');
            return;
        }
        if (_colMode === 'new') collections.push({
            id: Date.now(),
            name,
            description: document.getElementById('colDesc').value.trim(),
            items: []
        });
        else {
            collections[_colIdx].name = name;
            collections[_colIdx].description = document.getElementById('colDesc').value.trim();
        }
        saveCols();
        renderSidebar();
        closeModal('colModal');
        toast(_colMode === 'new' ? '✓ Collection created' : '✓ Collection updated', 'success');
    }

    function deleteCol(ci) {
        if (!confirm(`Delete collection "${collections[ci].name}"?`)) return;
        collections.splice(ci, 1);
        saveCols();
        renderSidebar();
        toast('Collection deleted', 'success');
    }

    // ─── FOLDERS ───
    function openFolderModal(ci, path) {
        _folColIdx = ci;
        _folPath = path || [];
        document.getElementById('folderName').value = '';
        openModal('folderModal');
        setTimeout(() => document.getElementById('folderName').focus(), 80);
    }

    function saveFolderModal() {
        const name = document.getElementById('folderName').value.trim();
        if (!name) {
            toast('Name required', 'error');
            return;
        }
        const arr = collections[_folColIdx].items;
        const target = _folPath.length ? getContainer(arr, _folPath) : arr;
        target.push({
            type: 'folder',
            name,
            items: []
        });
        saveCols();
        renderSidebar();
        closeModal('folderModal');
        toast('✓ Folder created', 'success');
    }

    function deleteFolder(ci, path) {
        const parent = path.length === 1 ? collections[ci].items : getByPath(collections[ci].items, path.slice(0, -1))
            .items;
        if (!confirm(`Delete folder "${parent[path[path.length-1]].name}"?`)) return;
        parent.splice(path[path.length - 1], 1);
        saveCols();
        renderSidebar();
        toast('Folder deleted', 'success');
    }

    // ─── REQUESTS ───
    function openSaveReqModal() {
        saveCurrentState();
        if (!collections.length) {
            toast('Create a collection first', 'error');
            return;
        }
        const tab = tabs.find(t => t.id === activeTabId);
        if (!tab) return;
        document.getElementById('saveReqName').value = tab.name === 'New Request' ? '' : tab.name;
        const sel = document.getElementById('saveReqCol');
        sel.innerHTML = collections.map((c, i) => `<option value="${i}">${esc(c.name)}</option>`).join('');
        populateSaveReqFolders();
        openModal('saveReqModal');
        setTimeout(() => document.getElementById('saveReqName').focus(), 80);
    }

    function populateSaveReqFolders() {
        const ci = parseInt(document.getElementById('saveReqCol').value);
        const sel = document.getElementById('saveReqFolder');
        sel.innerHTML = '<option value="">— Root —</option>';
        if (isNaN(ci) || !collections[ci]) return;
        const walk = (items, prefix, path) => items.forEach((it, i) => {
            if (it.type === 'folder') {
                const p = [...path, i];
                sel.add(new Option(prefix + it.name, JSON.stringify(p)));
                walk(it.items, prefix + '  ', p);
            }
        });
        walk(collections[ci].items, '📂 ', []);
    }

    function doSaveRequest() {
        const name = document.getElementById('saveReqName').value.trim();
        if (!name) {
            toast('Request name required', 'error');
            return;
        }
        const ci = parseInt(document.getElementById('saveReqCol').value);
        const folVal = document.getElementById('saveReqFolder').value;
        const path = folVal ? JSON.parse(folVal) : null;
        const tab = tabs.find(t => t.id === activeTabId);
        if (!tab) return;
        const req = {
            type: 'request',
            name,
            method: tab.method,
            url: tab.url,
            params: tab.params || {},
            paramEnabled: tab.paramEnabled || {},
            headers: tab.headers || {},
            headerEnabled: tab.headerEnabled || {},
            auth: tab.auth,
            body: tab.body,
            bodyMode: tab.bodyMode || 'none'
        };
        const container = path ? getByPath(collections[ci].items, path).items : collections[ci].items;
        container.push(req);
        tab.name = name;
        tab.dirty = false;
        saveTabs();
        saveCols();
        renderSidebar();
        renderTabsBar();
        closeModal('saveReqModal');
        toast('✓ Saved to collection!', 'success');
    }

    function duplicateReq(ci, path, idx) {
        const container = path ? getByPath(collections[ci].items, path).items : collections[ci].items;
        const copy = JSON.parse(JSON.stringify(container[idx]));
        copy.name += ' (Copy)';
        container.splice(idx + 1, 0, copy);
        saveCols();
        renderSidebar();
        toast('✓ Duplicated!', 'success');
    }

    function deleteReq(ci, path, idx) {
        const container = path ? getByPath(collections[ci].items, path).items : collections[ci].items;
        if (!confirm(`Delete "${container[idx].name}"?`)) return;
        container.splice(idx, 1);
        saveCols();
        renderSidebar();
        toast('Request deleted', 'success');
    }

    // ─── RENAME ───
    function openRenameModal(target, current) {
        _renameTarget = target;
        document.getElementById('renameName').value = current;
        openModal('renameModal');
        setTimeout(() => {
            const i = document.getElementById('renameName');
            i.focus();
            i.select();
        }, 80);
    }

    function doRename() {
        const name = document.getElementById('renameName').value.trim();
        if (!name) {
            toast('Name required', 'error');
            return;
        }
        const {
            type,
            ci,
            path,
            idx
        } = _renameTarget;
        if (type === 'col') collections[ci].name = name;
        else if (type === 'folder') getByPath(collections[ci].items, path).name = name;
        else {
            const c = path ? getByPath(collections[ci].items, path).items : collections[ci].items;
            c[idx].name = name;
        }
        saveCols();
        renderSidebar();
        closeModal('renameModal');
        toast('✓ Renamed!', 'success');
    }

    // ─── PATH HELPERS ───
    function getContainer(items, path) {
        let cur = items;
        for (const i of path) cur = cur[i].items;
        return cur;
    }

    function getByPath(items, path) {
        let cur = items;
        for (let i = 0; i < path.length - 1; i++) cur = cur[path[i]].items;
        return cur[path[path.length - 1]];
    }

    // ═══════════════════════════════════════════════════
    //  CONTEXT MENU
    // ═══════════════════════════════════════════════════
    function showCtx(e, items) {
        e.preventDefault();
        e.stopPropagation();
        const menu = document.getElementById('ctxMenu');
        menu.innerHTML = items.map(it => {
            if (it === '-') return '<div class="ctx-sep"></div>';
            return `<div class="ctx-item${it.d?' danger':''}" onclick="(${it.fn})();closeCtx()">
                <span class="ctx-item-icon">${it.ic||''}</span>${it.lb}</div>`;
        }).join('');
        const x = Math.min(e.clientX, window.innerWidth - 200);
        const y = Math.min(e.clientY, window.innerHeight - 300);
        menu.style.left = x + 'px';
        menu.style.top = y + 'px';
        menu.classList.add('open');
        _ctxOpen = true;
    }

    function closeCtx() {
        document.getElementById('ctxMenu').classList.remove('open');
        _ctxOpen = false;
    }

    function ctxCol(e, ci) {
        showCtx(e, [{
                ic: '✏️',
                lb: 'Edit',
                fn: `function(){openEditColModal(${ci})}`
            },
            {
                ic: '📂',
                lb: 'Add Folder',
                fn: `function(){openFolderModal(${ci},[])}`
            },
            {
                ic: '⬇',
                lb: 'Export',
                fn: `function(){exportCol(${ci})}`
            },
            '-',
            {
                ic: '🗑',
                lb: 'Delete',
                d: true,
                fn: `function(){deleteCol(${ci})}`
            }
        ]);
    }

    function ctxFolder(e, ci, pj) {
        const path = JSON.parse(pj);
        const folder = getByPath(collections[ci].items, path);
        showCtx(e, [{
                ic: '✏️',
                lb: 'Rename',
                fn: `function(){openRenameModal({type:'folder',ci:${ci},path:${pj}},'${folder.name.replace(/'/g,"\\'")}')}`
            },
            {
                ic: '📂',
                lb: 'Add Sub-folder',
                fn: `function(){openFolderModal(${ci},${pj})}`
            },
            '-',
            {
                ic: '🗑',
                lb: 'Delete Folder',
                d: true,
                fn: `function(){deleteFolder(${ci},${pj})}`
            }
        ]);
    }

    function ctxReq(e, ci, pj, idx) {
        const path = pj === 'null' ? null : JSON.parse(pj);
        const c = path ? getByPath(collections[ci].items, path).items : collections[ci].items;
        const name = c[idx].name;
        showCtx(e, [{
                ic: '↗',
                lb: 'Open in Tab',
                fn: `function(){loadReqByPos(${ci},${pj},${idx})}`
            },
            {
                ic: '📋',
                lb: 'Duplicate',
                fn: `function(){duplicateReq(${ci},${pj==='null'?null:pj},${idx})}`
            },
            {
                ic: '✏️',
                lb: 'Rename',
                fn: `function(){openRenameModal({type:'req',ci:${ci},path:${pj==='null'?null:pj},idx:${idx}},'${name.replace(/'/g,"\\'")}')}`
            },
            '-',
            {
                ic: '🗑',
                lb: 'Delete',
                d: true,
                fn: `function(){deleteReq(${ci},${pj==='null'?null:pj},${idx})}`
            }
        ]);
    }

    // ═══════════════════════════════════════════════════
    //  SIDEBAR
    // ═══════════════════════════════════════════════════
    function renderSidebar() {
        requestMap = {};
        const c = document.getElementById('sidebarContent');
        if (sidebarMode === 'history') {
            renderHistory(c);
            return;
        }
        if (!collections.length) {
            c.innerHTML = `<div class="empty">
                <svg viewBox="0 0 48 48" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="8" y="8" width="32" height="32" rx="2"/><path d="M16 24h16M16 32h10M16 16h16"/></svg>
                <p>No collections yet.<br>Click <b>New Collection</b> to start.</p></div>`;
            return;
        }
        c.innerHTML = collections.map((col, ci) => `
            <div class="col-group">
              <div class="col-header" onclick="toggleNode('colBody${ci}','colArr${ci}')">
                <span class="tree-arrow" id="colArr${ci}"><svg viewBox="0 0 8 8" fill="currentColor"><path d="M2 1l4 3-4 3z"/></svg></span>
                <span class="col-icon">📦</span>
                <span class="col-name" title="${esc(col.name)}">${esc(col.name)}</span>
                <button class="col-menu" onclick="event.stopPropagation();ctxCol(event,${ci})">···</button>
              </div>
              <div class="col-body" id="colBody${ci}">${renderItems(col.items,'c'+ci,ci,[])}</div>
            </div>`).join('');
    }

    function renderItems(items, prefix, ci, parentPath) {
        return (items || []).map((item, i) => {
            const id = `${prefix}-${i}`,
                curPath = [...parentPath, i],
                pj = JSON.stringify(curPath);
            if (item.type === 'folder') {
                return `<div class="folder-group">
                    <div class="folder-header" onclick="toggleNode('fb${id}','fa${id}')">
                      <span class="tree-arrow" id="fa${id}"><svg viewBox="0 0 8 8" fill="currentColor"><path d="M2 1l4 3-4 3z"/></svg></span>
                      <span style="font-size:11px">📂</span>
                      <span class="col-name" title="${esc(item.name)}">${esc(item.name)}</span>
                      <button class="col-menu" onclick="event.stopPropagation();ctxFolder(event,${ci},'${pj}')">···</button>
                    </div>
                    <div class="folder-body" id="fb${id}">${renderItems(item.items,id,ci,curPath)}</div>
                  </div>`;
            }
            const pjParent = parentPath.length ? JSON.stringify(parentPath) : 'null';
            requestMap[id] = {
                item,
                ci,
                parentPath
            };
            return `<div class="req-item" data-key="${id}" onclick="loadRequest('${id}',this)">
                <span class="method-badge m-${item.method}">${item.method}</span>
                <span class="req-name" title="${esc(item.name)}">${esc(item.name)}</span>
                <button class="col-menu" onclick="event.stopPropagation();ctxReq(event,${ci},'${pjParent}',${i})">···</button>
              </div>`;
        }).join('');
    }

    function renderHistory(c) {
        if (!historyList.length) {
            c.innerHTML = `<div class="empty">
                <svg viewBox="0 0 48 48" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="24" cy="24" r="16"/><path d="M24 14v10l6 4"/></svg>
                <p>No history yet</p></div>`;
            return;
        }
        c.innerHTML = historyList.slice().reverse().map((h, i) => `
            <div class="hist-item" onclick="loadHistory(${historyList.length-1-i})">
              <span class="method-badge m-${h.method}">${h.method}</span>
              <div class="hist-info">
                <div class="hist-url">${esc(h.url)}</div>
                <div class="hist-meta">${h.status?h.status+' · ':''}${h.time}ms · ${h.date}</div>
              </div>
            </div>`).join('');
    }

    function toggleNode(bodyId, arrId) {
        document.getElementById(bodyId)?.classList.toggle('open');
        document.getElementById(arrId)?.classList.toggle('open');
    }

    function switchSidebar(mode, el) {
        sidebarMode = mode;
        document.querySelectorAll('.sidebar-tab').forEach(t => t.classList.remove('active'));
        el.classList.add('active');
        renderSidebar();
    }

    function searchRequests(q) {
        q = q.toLowerCase();
        document.querySelectorAll('.req-item').forEach(el => {
            el.style.display = el.textContent.toLowerCase().includes(q) ? '' : 'none';
        });
    }

    function loadRequest(key, el) {
        const {
            item
        } = requestMap[key] || {};
        if (!item) return;
        saveCurrentState();
        const existing = tabs.find(t => t.name === item.name && t.method === item.method);
        if (existing) switchToTab(existing.id, false);
        else newTab(item);
        document.querySelectorAll('.req-item').forEach(r => r.classList.remove('active'));
        el.classList.add('active');
    }

    function loadReqByPos(ci, pj, idx) {
        const path = (pj === null || pj === 'null') ? null : (typeof pj === 'string' ? JSON.parse(pj) : pj);
        const c = path ? getByPath(collections[ci].items, path).items : collections[ci].items;
        const item = c[idx];
        if (!item) return;
        saveCurrentState();
        const existing = tabs.find(t => t.name === item.name && t.method === item.method);
        if (existing) switchToTab(existing.id, false);
        else newTab(item);
    }

    // ═══════════════════════════════════════════════════
    //  TABS
    // ═══════════════════════════════════════════════════
    function newTab(item) {
        tabCounter++;
        const id = 'tab_' + tabCounter;
        tabs.push({
            id,
            dirty: false,
            name: item ? item.name : 'New Request',
            method: item ? item.method : cfg.default_method,
            url: item ? item.url : '',
            params: item ? (item.params || {}) : {},
            paramEnabled: item ? (item.paramEnabled || {}) : {},
            headers: item ? (item.headers || {}) : {},
            headerEnabled: item ? (item.headerEnabled || {}) : {},
            auth: item ? item.auth : (cfg.default_auth_type !== 'none' ? {
                type: cfg.default_auth_type
            } : null),
            body: item ? item.body : null,
            bodyMode: item ? item.bodyMode : cfg.default_body_mode,
            response: null
        });
        saveTabs();
        renderTabsBar();
        switchToTab(id, false);
        return id;
    }

    function closeTab(id, e) {
        if (e) e.stopPropagation();
        const idx = tabs.findIndex(t => t.id === id);
        if (idx === -1) return;
        if (tabs[idx].dirty && !confirm(`"${tabs[idx].name}" has unsaved changes. Close?`)) return;
        tabs.splice(idx, 1);
        if (!tabs.length) {
            newTab();
            return;
        }
        const next = tabs[Math.min(idx, tabs.length - 1)].id;
        saveTabs();
        renderTabsBar();
        switchToTab(next, false);
    }

    function switchToTab(id, doSave) {
        if (doSave !== false && activeTabId) saveCurrentState();
        activeTabId = id;
        localStorage.setItem('api_active_tab', id);
        renderTabsBar();
        const tab = tabs.find(t => t.id === id);
        if (!tab) return;
        document.getElementById('methodSel').value = tab.method || cfg.default_method;
        document.getElementById('urlInput').value = tab.url || '';
        onMethodChange();

        // Params
        document.getElementById('paramRows').innerHTML = '';
        Object.entries(tab.params || {}).forEach(([k, v]) =>
            addKvRow('paramRows', 'param', k, v, tab.paramEnabled ? tab.paramEnabled[k] !== false : true));
        addKvRow('paramRows', 'param', '', '', true);

        // Headers
        document.getElementById('headerRows').innerHTML = '';
        Object.entries(tab.headers || {}).forEach(([k, v]) =>
            addKvRow('headerRows', 'header', k, v, tab.headerEnabled ? tab.headerEnabled[k] !== false : true));
        addKvRow('headerRows', 'header', '', '', true);

        updateCounters();

        // Auth
        document.getElementById('authType').value = tab.auth?.type || cfg.default_auth_type;
        toggleAuth();
        if (tab.auth?.type === 'bearer') {
            const el = document.getElementById('bearerToken');
            if (el) el.value = tab.auth.token || '';
        } else if (tab.auth?.type === 'basic') {
            const u = document.getElementById('basicUser'),
                p = document.getElementById('basicPass');
            if (u) u.value = tab.auth.username || '';
            if (p) p.value = tab.auth.password || '';
        } else if (tab.auth?.type === 'apikey') {
            const k = document.getElementById('apiKeyName'),
                v = document.getElementById('apiKeyValue');
            if (k) k.value = tab.auth.keyName || '';
            if (v) v.value = tab.auth.keyValue || '';
        }

        // Body
        bodyMode = tab.bodyMode || cfg.default_body_mode;
        document.querySelectorAll('.body-mode-btn').forEach(b => {
            const m = b.getAttribute('onclick').match(/setBodyMode\('([^']+)'/);
            if (m) b.classList.toggle('active', m[1] === bodyMode);
        });
        renderBodyContent(tab);

        // Pre-req
        const preEl = document.getElementById('preReqScript');
        if (preEl) preEl.value = tab.preReq || '';

        // Response
        if (tab.response) {
            const r = tab.response;
            showResponse(r.status, r.body, r.headers, r.time, r.size, r.bodyType, r.contentType);
        } else {
            resetResponse();
        }
    }

    function renderBodyContent(tab) {
        const bc = document.getElementById('bodyContent');
        bc.innerHTML = '';
        if (bodyMode === 'json' || bodyMode === 'raw') {
            const ta = document.createElement('textarea');
            ta.className = 'code-editor';
            ta.id = 'bodyJson';
            ta.placeholder = bodyMode === 'json' ? '{\n  "key": "value"\n}' : 'Enter raw body…';
            ta.oninput = markDirty;
            ta.style.flex = '1';
            bc.appendChild(ta);
            if (tab?.body) setTimeout(() => {
                // Dulu: JSON.stringify(JSON.parse(tab.body)) → merusak komentar yang sudah distrip
                ta.value = typeof tab.body === 'string' ? tab.body : JSON.stringify(tab.body, null, 2);
            }, 10);
        } else if (bodyMode === 'formdata' || bodyMode === 'urlencoded') {
            const wrap = document.createElement('div');
            wrap.style.cssText = 'display:flex;flex-direction:column;flex:1;overflow:hidden;';
            wrap.innerHTML = `<table class="kv-table"><thead><tr><th style="width:22px"></th><th>Key</th><th>Value</th><th style="width:28px"></th></tr></thead><tbody id="formRows"></tbody></table>
                <button class="kv-add-row" onclick="addKvRow('formRows','form');markDirty()">
                  <svg viewBox="0 0 16 16" fill="currentColor" width="12" height="12"><path d="M8 3a1 1 0 011 1v3h3a1 1 0 110 2H9v3a1 1 0 11-2 0V9H4a1 1 0 110-2h3V4a1 1 0 011-1z"/></svg>
                  Add Field</button>`;
            bc.appendChild(wrap);
            if (tab?.body && typeof tab.body === 'object') {
                Object.entries(tab.body).forEach(([k, v]) => addKvRow('formRows', 'form', k, v, true));
            }
            addKvRow('formRows', 'form', '', '', true);
        }
    }

    function saveCurrentState() {
        const tab = tabs.find(t => t.id === activeTabId);
        if (!tab) return;
        tab.method = document.getElementById('methodSel').value;
        tab.url = document.getElementById('urlInput').value;

        const {
            keys: pk,
            vals: pv,
            enabled: pe
        } = getKvData('paramRows');
        tab.params = {};
        tab.paramEnabled = {};
        pk.forEach((k, i) => {
            if (k) {
                tab.params[k] = pv[i];
                tab.paramEnabled[k] = pe[i];
            }
        });

        const {
            keys: hk,
            vals: hv,
            enabled: he
        } = getKvData('headerRows');
        tab.headers = {};
        tab.headerEnabled = {};
        hk.forEach((k, i) => {
            if (k) {
                tab.headers[k] = hv[i];
                tab.headerEnabled[k] = he[i];
            }
        });

        tab.bodyMode = bodyMode;
        tab.auth = getAuthState();
        tab.preReq = document.getElementById('preReqScript')?.value || '';

        if (bodyMode === 'json' || bodyMode === 'raw') {
            const ta = document.getElementById('bodyJson');
            tab.body = ta ? ta.value : null;
        } else if (bodyMode === 'formdata' || bodyMode === 'urlencoded') {
            const {
                keys: fk,
                vals: fv
            } = getKvData('formRows');
            tab.body = {};
            fk.forEach((k, i) => {
                if (k) tab.body[k] = fv[i];
            });
        } else {
            tab.body = null;
        }
        saveTabs();
    }

    function saveTab() {
        saveCurrentState();
        const tab = tabs.find(t => t.id === activeTabId);
        if (tab) {
            tab.dirty = false;
            saveTabs();
            renderTabsBar();
            toast('✓ Tab saved!', 'success');
        }
    }

    function markDirty() {
        const tab = tabs.find(t => t.id === activeTabId);
        if (tab && !tab.dirty) {
            tab.dirty = true;
            renderTabsBar();
        }
    }

    function saveTabs() {
        localStorage.setItem('api_tabs', JSON.stringify(tabs));
        localStorage.setItem('api_tab_ctr', String(tabCounter));
    }

    function renderTabsBar() {
        const bar = document.getElementById('tabsBar');
        const MC = {
            GET: '#6bbd5b',
            POST: '#f5a623',
            PUT: '#4a9eff',
            PATCH: '#c97bff',
            DELETE: '#f85149'
        };
        bar.innerHTML = '';
        tabs.forEach(tab => {
            const el = document.createElement('div');
            el.className = 'vtab' + (tab.id === activeTabId ? ' active' : '') + (tab.dirty ? ' dirty' : '');
            el.onclick = () => {
                saveCurrentState();
                switchToTab(tab.id, false);
            };
            el.innerHTML = `<span class="vtab-method" style="background:${(MC[tab.method]||'#888')+'22'};color:${MC[tab.method]||'#e8e8e8'}">${tab.method}</span>
                <span class="vtab-name">${esc(tab.name)}</span>
                <span class="vtab-close" onclick="closeTab('${tab.id}',event)">✕</span>`;
            bar.appendChild(el);
        });
        // Save icon
        const save = document.createElement('div');
        save.className = 'vtab-action';
        save.title = 'Save to Collection';
        save.innerHTML =
            '<svg width="14" height="14" viewBox="0 0 16 16" fill="currentColor"><path d="M13 1H3a1 1 0 00-1 1v12l6-3 6 3V2a1 1 0 00-1-1z"/></svg>';
        save.onclick = () => {
            saveCurrentState();
            openSaveReqModal();
        };
        bar.appendChild(save);
        // New tab
        const plus = document.createElement('div');
        plus.className = 'vtab-action';
        plus.title = 'New Tab';
        plus.textContent = '+';
        plus.onclick = () => {
            saveCurrentState();
            newTab();
        };
        bar.appendChild(plus);
    }

    // ═══════════════════════════════════════════════════
    //  INNER TABS
    // ═══════════════════════════════════════════════════
    function switchInner(name, el, tabsId, panesId) {
        document.querySelectorAll(`#${tabsId} .itab`).forEach(t => t.classList.remove('active'));
        document.querySelectorAll(`#${panesId} .tab-pane`).forEach(p => p.classList.remove('active'));
        el.classList.add('active');
        document.getElementById(`pane-${name}`)?.classList.add('active');
    }

    // ═══════════════════════════════════════════════════
    //  KV ROWS
    // ═══════════════════════════════════════════════════
    function addKvRow(tbodyId, type, key, value, enabled) {
        key = key || '';
        value = value || '';
        if (enabled === undefined) enabled = true;
        const tbody = document.getElementById(tbodyId);
        if (!tbody) return;
        const tr = document.createElement('tr');
        const ph = type === 'param' ? 'parameter' : 'Header name';
        tr.innerHTML =
            `
            <td style="text-align:center"><input type="checkbox" class="kv-check" ${enabled?'checked':''} onchange="updateCounters();markDirty()"></td>
            <td><input class="kv-field" placeholder="${ph}" value="${esc(key)}" oninput="updateCounters();markDirty()"></td>
            <td><input class="kv-field" placeholder="Value" value="${esc(value)}" oninput="updateCounters();markDirty()"></td>
            <td style="text-align:center"><button class="kv-del" onclick="this.closest('tr').remove();updateCounters();markDirty()" title="Delete">✕</button></td>`;
        tbody.appendChild(tr);
    }

    function getKvData(tbodyId) {
        const keys = [],
            vals = [],
            enabled = [];
        document.querySelectorAll(`#${tbodyId} tr`).forEach(tr => {
            const inputs = tr.querySelectorAll('input.kv-field');
            const cb = tr.querySelector('input[type=checkbox]');
            if (inputs.length >= 2) {
                keys.push(inputs[0].value.trim());
                vals.push(inputs[1].value.trim());
                enabled.push(cb ? cb.checked : true);
            }
        });
        return {
            keys,
            vals,
            enabled
        };
    }

    function getKvObj(tbodyId) {
        const r = {},
            {
                keys,
                vals,
                enabled
            } = getKvData(tbodyId);
        keys.forEach((k, i) => {
            if (k && enabled[i]) r[k] = vals[i];
        });
        return r;
    }

    function updateCounters() {
        let pn = 0,
            hn = 0;
        document.querySelectorAll('#paramRows tr input.kv-field:first-of-type').forEach(i => {
            if (i.value.trim()) pn++;
        });
        document.querySelectorAll('#headerRows tr input.kv-field:first-of-type').forEach(i => {
            if (i.value.trim()) hn++;
        });
        document.getElementById('cnt-params').textContent = pn;
        document.getElementById('cnt-headers').textContent = hn;
    }

    // ═══════════════════════════════════════════════════
    //  BODY MODE
    // ═══════════════════════════════════════════════════
    function setBodyMode(mode, btn) {
        bodyMode = mode;
        document.querySelectorAll('.body-mode-btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        renderBodyContent(null);
        markDirty();
    }

    // ═══════════════════════════════════════════════════
    //  AUTH
    // ═══════════════════════════════════════════════════
    function toggleAuth() {
        const type = document.getElementById('authType').value;
        const c = document.getElementById('authFields');
        c.innerHTML = '';
        if (type === 'bearer') {
            c.innerHTML =
                `<div class="form-field"><label>Token</label><input id="bearerToken" placeholder="eyJhbGci..." oninput="markDirty()"></div>`;
        } else if (type === 'basic') {
            c.innerHTML =
                `<div class="form-field"><label>Username</label><input id="basicUser" placeholder="username" oninput="markDirty()"></div>
                <div class="form-field"><label>Password</label><input id="basicPass" type="password" placeholder="password" oninput="markDirty()"></div>`;
        } else if (type === 'apikey') {
            c.innerHTML =
                `<div class="form-field"><label>Key Name</label><input id="apiKeyName" placeholder="x-api-key" oninput="markDirty()"></div>
                <div class="form-field"><label>Key Value</label><input id="apiKeyValue" placeholder="your-api-key" oninput="markDirty()"></div>`;
        }
    }

    function getAuthState() {
        const type = document.getElementById('authType').value;
        if (type === 'bearer') return {
            type,
            token: document.getElementById('bearerToken')?.value || ''
        };
        if (type === 'basic') return {
            type,
            username: document.getElementById('basicUser')?.value || '',
            password: document.getElementById('basicPass')?.value || ''
        };
        if (type === 'apikey') return {
            type,
            keyName: document.getElementById('apiKeyName')?.value || '',
            keyValue: document.getElementById('apiKeyValue')?.value || ''
        };
        return null;
    }

    function getAuthHeader() {
        const type = document.getElementById('authType').value;
        if (type === 'bearer') {
            const t = applyEnv(document.getElementById('bearerToken')?.value.trim() || '');
            return t ? {
                Authorization: `Bearer ${t}`
            } : {};
        }
        if (type === 'basic') {
            const u = document.getElementById('basicUser')?.value.trim(),
                p = document.getElementById('basicPass')?.value.trim();
            return u ? {
                Authorization: `Basic ${btoa(u+':'+p)}`
            } : {};
        }
        if (type === 'apikey') {
            const k = document.getElementById('apiKeyName')?.value.trim(),
                v = applyEnv(document.getElementById('apiKeyValue')?.value.trim() || '');
            return k ? {
                [k]: v
            } : {};
        }
        return {};
    }

    // ═══════════════════════════════════════════════════
    //  SEND  — menggunakan cfg.proxy_url & cfg.default_timeout
    // ═══════════════════════════════════════════════════
    async function sendRequest() {
        let rawUrl = document.getElementById('urlInput').value.trim();
        if (!rawUrl) {
            toast('Enter a URL first', 'error');
            return;
        }
        let url = applyEnv(rawUrl);
        const method = document.getElementById('methodSel').value;

        const rawHeaders = {
            ...getKvObj('headerRows'),
            ...getAuthHeader()
        };
        const headers = {};
        for (const [k, v] of Object.entries(rawHeaders)) headers[k] = applyEnv(v);

        const params = {};
        for (const [k, v] of Object.entries(getKvObj('paramRows'))) params[k] = applyEnv(v);
        const qp = new URLSearchParams(params).toString();
        if (qp) url += (url.includes('?') ? '&' : '?') + qp;

        let body = null;
        if (['POST', 'PUT', 'PATCH', 'DELETE'].includes(method) && bodyMode !== 'none') {
            if (bodyMode === 'json' || bodyMode === 'raw') {
                const raw = applyEnv(document.getElementById('bodyJson')?.value.trim() || '');
                
                if (bodyMode === 'json') {
                    // Strip komentar // dan /* */ sebelum validasi & kirim
                    const stripped = raw
                        .replace(/\/\/[^\n\r]*/g, '')       // hapus komentar //
                        .replace(/\/\*[\s\S]*?\*\//g, '')    // hapus komentar /* */
                        .replace(/,\s*([}\]])/g, '$1')       // hapus trailing comma
                        .trim();
                    
                    try {
                        JSON.parse(stripped); // validasi saja
                    } catch {
                        toast('⚠ Body bukan JSON valid (cek sintaks)', 'error');
                        return;
                    }
                    headers['Content-Type'] = 'application/json';
                    body = stripped; // kirim yang sudah bersih
                } else {
                    body = raw;
                }
            } else {
                body = {};
                for (const [k, v] of Object.entries(getKvObj('formRows'))) body[k] = applyEnv(v);
            }
        }

        const btn = document.getElementById('sendBtn'),
            sp = document.getElementById('spinner');
        btn.classList.add('loading');
        btn.innerHTML = '<span>Sending…</span>';
        sp.style.display = 'block';

        // Timeout dari config (cfg.default_timeout dalam ms)
        const controller = new AbortController();
        const timeoutId = cfg.default_timeout ?
            setTimeout(() => controller.abort(), cfg.default_timeout) :
            null;

        const t0 = Date.now();
        try {
            const res = await fetch(cfg.proxy_url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    url,
                    method,
                    headers,
                    body,
                    bodyMode
                }),
                signal: controller.signal
            });
            if (timeoutId) clearTimeout(timeoutId);
            const data = await res.json();
            const elapsed = Date.now() - t0;
            if (data.error && !data.status) showResponse(null, data.error, {}, elapsed, 0);
            else {
                showResponse(data.status, data.body, data.headers, data.time || elapsed, data.size, data.bodyType,
                    data.contentType);
                const tab = tabs.find(t => t.id === activeTabId);
                if (tab) {
                    tab.response = {
                        status: data.status,
                        body: data.body,
                        headers: data.headers,
                        time: data.time || elapsed,
                        size: data.size,
                        bodyType: data.bodyType,
                        contentType: data.contentType
                    };
                    saveTabs();
                }
                saveHistory({
                    method,
                    url: rawUrl,
                    status: data.status,
                    time: data.time || elapsed
                });
            }
        } catch (err) {
            if (timeoutId) clearTimeout(timeoutId);
            const msg = err.name === 'AbortError' ?
                `⏱ Timeout after ${cfg.default_timeout}ms` :
                `❌ Error: ${err.message}`;
            showResponse(null, msg, {}, Date.now() - t0, 0);
        } finally {
            btn.classList.remove('loading');
            btn.innerHTML =
                'Send <svg viewBox="0 0 16 16" fill="currentColor" width="12" height="12"><path d="M2 2l12 6-12 6V9.5l8-1.5-8-1.5V2z"/></svg>';
            sp.style.display = 'none';
        }
    }

    // ─── RESPONSE DISPLAY ───
    function showResponse(status, body, headers, time, size, bodyType, contentType) {
        const pill = document.getElementById('statusPill');
        pill.textContent = status ? `${status} ${statusText(status)}` : 'Error';
        pill.className = 'status-pill';
        if (status >= 200 && status < 300) pill.classList.add('s-ok');
        else if (status >= 300 && status < 400) pill.classList.add('s-redir');
        else if (status >= 400 && status < 500) pill.classList.add('s-cli');
        else if (status >= 500) pill.classList.add('s-srv');
        else pill.classList.add('s-none');

        document.getElementById('timeVal').textContent = time || 0;
        document.getElementById('sizeVal').textContent = fmtSize(size);

        const bodyEl = document.getElementById('resBody');
        if (bodyType === 'base64' && contentType?.includes('pdf')) {
            const blob = b64toBlob(body, 'application/pdf'),
                bUrl = URL.createObjectURL(blob);
            bodyEl.innerHTML = `<div style="display:flex;flex-direction:column;height:100%;gap:8px;padding:8px">
                <div style="display:flex;gap:8px">
                  <a href="${bUrl}" download="response.pdf" style="padding:4px 12px;background:var(--pm-orange);color:#fff;border-radius:4px;text-decoration:none;font-size:11px">⬇ Download PDF</a>
                  <a href="${bUrl}" target="_blank" style="padding:4px 12px;background:var(--pm-surface2);color:var(--pm-text);border-radius:4px;text-decoration:none;font-size:11px;border:1px solid var(--pm-border)">🔗 Open in Tab</a>
                </div>
                <iframe src="${bUrl}" style="flex:1;border:none;border-radius:4px;background:#fff"></iframe></div>`;
        } else if (bodyType === 'base64') {
            const blob = b64toBlob(body, contentType || 'application/octet-stream'),
                bUrl = URL.createObjectURL(blob);
            bodyEl.innerHTML = `<div style="padding:20px;color:var(--pm-text2)">Binary response (${contentType})<br><br>
                <a href="${bUrl}" download="response.bin" style="color:var(--pm-orange)">⬇ Download file</a></div>`;
        } else {
            try {
                bodyEl.innerHTML = syntaxHL(JSON.stringify(JSON.parse(body), null, 2));
            } catch {
                bodyEl.textContent = body;
            }
        }

        const hEl = document.getElementById('resHeaders');
        hEl.innerHTML = Object.keys(headers || {}).length ?
            syntaxHL(JSON.stringify(headers, null, 2)) :
            '<span style="color:var(--pm-text3)">No headers</span>';
    }

    function resetResponse() {
        document.getElementById('statusPill').textContent = '—';
        document.getElementById('statusPill').className = 'status-pill s-none';
        document.getElementById('timeVal').textContent = '—';
        document.getElementById('sizeVal').textContent = '—';
        document.getElementById('resBody').innerHTML = `<div class="placeholder-state">
            <svg viewBox="0 0 48 48" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M8 8h32v32H8zM16 20h16M16 28h10"/></svg>
            <p>Hit <b>Send</b> to get a response</p></div>`;
        document.getElementById('resHeaders').innerHTML = `<div class="placeholder-state">
            <svg viewBox="0 0 48 48" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M8 8h32v32H8zM16 20h16M16 28h10"/></svg>
            <p>No response yet</p></div>`;
    }

    function clearResponse() {
        const tab = tabs.find(t => t.id === activeTabId);
        if (tab) {
            tab.response = null;
            saveTabs();
        }
        resetResponse();
        toast('Response cleared', 'success');
    }

    function copyResponse() {
        const text = document.getElementById('resBody').textContent;
        if (!text.trim()) {
            toast('Nothing to copy', 'error');
            return;
        }
        navigator.clipboard.writeText(text)
            .then(() => toast('✓ Copied to clipboard', 'success'))
            .catch(() => toast('Copy failed', 'error'));
    }

    // ─── METHOD COLOR ───
    function onMethodChange() {
        const sel = document.getElementById('methodSel');
        const C = {
            GET: '#6bbd5b',
            POST: '#f5a623',
            PUT: '#4a9eff',
            PATCH: '#c97bff',
            DELETE: '#f85149'
        };
        sel.style.color = C[sel.value] || '#e8e8e8';
        const tab = tabs.find(t => t.id === activeTabId);
        if (tab) {
            tab.method = sel.value;
            renderTabsBar();
        }
    }

    // ═══════════════════════════════════════════════════
    //  IMPORT / EXPORT
    // ═══════════════════════════════════════════════════
    function importPostman(event) {
        const file = event.target.files[0];
        if (!file) return;
        const reader = new FileReader();
        reader.onload = e => {
            try {
                const data = JSON.parse(e.target.result);
                const imported = [];
                if (data.item) imported.push({
                    id: Date.now(),
                    name: data.info?.name || 'Imported',
                    description: data.info?.description || '',
                    items: parseItems(data.item)
                });
                else if (Array.isArray(data)) data.forEach((col, i) => imported.push({
                    id: Date.now() + i,
                    name: col.info?.name || col.name || 'Imported ' + i,
                    description: '',
                    items: parseItems(col.item || [])
                }));
                else {
                    toast('Unrecognized format', 'error');
                    return;
                }
                collections.push(...imported);
                saveCols();
                renderSidebar();
                toast(`✓ Imported ${imported.length} collection(s)!`, 'success');
            } catch (err) {
                toast('Parse error: ' + err.message, 'error');
            }
        };
        reader.readAsText(file);
        event.target.value = '';
    }

    function parseItems(items) {
        return items.map(item => {
            if (item.item) return {
                type: 'folder',
                name: item.name,
                items: parseItems(item.item)
            };
            const req = item.request || {};
            const rawUrl = req.url?.raw || (typeof req.url === 'string' ? req.url : '') || '';
            const headers = {};
            (req.header || []).forEach(h => {
                if (h.key && !h.disabled) headers[h.key] = h.value || '';
            });
            const params = {};
            if (Array.isArray(req.url?.query)) req.url.query.forEach(q => {
                if (q.key && !q.disabled) params[q.key] = q.value || '';
            });
            let cleanUrl = rawUrl;
            if (Object.keys(params).length && rawUrl.includes('?')) cleanUrl = rawUrl.split('?')[0];
            let auth = null;
            const ra = req.auth;
            if (ra?.type === 'bearer') {
                const t = (ra.bearer || []).find(b => b.key === 'token');
                auth = {
                    type: 'bearer',
                    token: t?.value || ''
                };
            } else if (ra?.type === 'basic') {
                const u = (ra.basic || []).find(b => b.key === 'username'),
                    p = (ra.basic || []).find(b => b.key === 'password');
                auth = {
                    type: 'basic',
                    username: u?.value || '',
                    password: p?.value || ''
                };
            } else if (ra?.type === 'apikey') {
                const k = (ra.apikey || []).find(b => b.key === 'key'),
                    v = (ra.apikey || []).find(b => b.key === 'value');
                auth = {
                    type: 'apikey',
                    keyName: k?.value || '',
                    keyValue: v?.value || ''
                };
            }
            let body = null,
                bMode = 'none';
            const rb = req.body;
            if (rb) {
                bMode = rb.mode || 'none';
                if (bMode === 'raw') {
                    bMode = 'json';
                    try {
                        body = JSON.parse(rb.raw);
                    } catch {
                        body = rb.raw;
                    }
                } else if (bMode === 'formdata') {
                    body = {};
                    (rb.formdata || []).forEach(f => {
                        if (!f.disabled) body[f.key] = f.value || '';
                    });
                } else if (bMode === 'urlencoded') {
                    body = {};
                    (rb.urlencoded || []).forEach(f => {
                        if (!f.disabled) body[f.key] = f.value || '';
                    });
                }
            }
            return {
                type: 'request',
                name: item.name,
                method: (req.method || 'GET').toUpperCase().trim(),
                url: cleanUrl,
                params,
                headers,
                auth,
                body,
                bodyMode: bMode
            };
        });
    }

    function exportCol(ci) {
        const now = new Date();
        const stamp = now.getFullYear() + ('0' + (now.getMonth() + 1)).slice(-2) + ('0' + now.getDate()).slice(-2) +
            '-' + ('0' + now.getHours()).slice(-2) + ('0' + now.getMinutes()).slice(-2) + ('0' + now.getSeconds())
            .slice(-2);
        const fname = collections[ci].name.replace(/\s+/g, '_') + '.' + cfg.app_name.replace(/\s+/g, '') + '-' + stamp +
            '.collection.json';
        dlJSON(colToPostman(collections[ci]), fname);
        toast('✓ Collection exported!', 'success');
    }

    function exportAllCollections() {
        if (!collections.length) {
            toast('No collections to export', 'error');
            return;
        }
        dlJSON({
            info: {
                name: 'All Collections',
                schema: 'https://schema.getpostman.com/json/collection/v2.1.0/collection.json'
            },
            item: collections.map(colToPostman)
        }, 'api_collections_all.json');
        toast(`✓ Exported ${collections.length} collection(s)`, 'success');
    }

    function colToPostman(col) {
        return {
            info: {
                name: col.name,
                description: col.description || '',
                schema: 'https://schema.getpostman.com/json/collection/v2.1.0/collection.json'
            },
            item: itemsToPostman(col.items)
        };
    }

    function itemsToPostman(items) {
        return (items || []).map(item => {
            if (item.type === 'folder') return {
                name: item.name,
                item: itemsToPostman(item.items)
            };
            const qp = Object.entries(item.params || {}).map(([k, v]) => ({
                key: k,
                value: v,
                disabled: false
            }));
            const headers = Object.entries(item.headers || {}).map(([k, v]) => ({
                key: k,
                value: v
            }));
            let auth;
            if (item.auth?.type === 'bearer') auth = {
                type: 'bearer',
                bearer: [{
                    key: 'token',
                    value: item.auth.token,
                    type: 'string'
                }]
            };
            else if (item.auth?.type === 'basic') auth = {
                type: 'basic',
                basic: [{
                    key: 'username',
                    value: item.auth.username,
                    type: 'string'
                }, {
                    key: 'password',
                    value: item.auth.password,
                    type: 'string'
                }]
            };
            else if (item.auth?.type === 'apikey') auth = {
                type: 'apikey',
                apikey: [{
                    key: 'key',
                    value: item.auth.keyName,
                    type: 'string'
                }, {
                    key: 'value',
                    value: item.auth.keyValue,
                    type: 'string'
                }]
            };
            let body;
            if (item.bodyMode === 'json') body = {
                mode: 'raw',
                raw: typeof item.body === 'string' ? item.body : JSON.stringify(item.body, null, 2),
                options: {
                    raw: {
                        language: 'json'
                    }
                }
            };
            else if (item.bodyMode === 'formdata') body = {
                mode: 'formdata',
                formdata: Object.entries(item.body || {}).map(([k, v]) => ({
                    key: k,
                    value: v
                }))
            };
            else if (item.bodyMode === 'urlencoded') body = {
                mode: 'urlencoded',
                urlencoded: Object.entries(item.body || {}).map(([k, v]) => ({
                    key: k,
                    value: v
                }))
            };
            return {
                name: item.name,
                request: {
                    method: item.method,
                    header: headers,
                    url: {
                        raw: item.url,
                        query: qp
                    },
                    auth,
                    body
                }
            };
        });
    }

    function dlJSON(data, filename) {
        const a = document.createElement('a');
        a.href = URL.createObjectURL(new Blob([JSON.stringify(data, null, 2)], {
            type: 'application/json'
        }));
        a.download = filename;
        a.click();
    }

    // ═══════════════════════════════════════════════════
    //  HISTORY  — menggunakan cfg.history_max_items
    // ═══════════════════════════════════════════════════
    function saveHistory(entry) {
        entry.date = new Date().toLocaleTimeString('id-ID');
        historyList.push(entry);
        if (historyList.length > cfg.history_max_items) historyList.shift();
        localStorage.setItem('api_history', JSON.stringify(historyList));
    }

    function loadHistory(i) {
        const h = historyList[i];
        saveCurrentState();
        newTab({
            name: h.url.split('/').pop() || h.method,
            method: h.method,
            url: h.url,
            params: {},
            headers: {},
            auth: null,
            body: null,
            bodyMode: 'none'
        });
    }

    // ═══════════════════════════════════════════════════
    //  ENV
    // ═══════════════════════════════════════════════════
    function openEnvModal() {
        renderEnvRows();
        openModal('envModal');
    }

    function renderEnvRows() {
        const c = document.getElementById('envRows');
        c.innerHTML = '';
        if (Object.keys(envVars).length) Object.entries(envVars).forEach(([k, v]) => addEnvRow(k, v));
        else addEnvRow();
    }

    function addEnvRow(key, value) {
        const c = document.getElementById('envRows');
        const row = document.createElement('div');
        row.style.cssText = 'display:flex;gap:6px;align-items:center;margin-bottom:6px';
        row.innerHTML =
            `
            <input class="kv-field" placeholder="VARIABLE_NAME" value="${esc(key||'')}" style="background:var(--pm-bg);border:1px solid var(--pm-border);border-radius:4px;padding:6px 8px;font-family:var(--pm-mono);font-size:11px;color:var(--pm-text);outline:none;flex:1">
            <input class="kv-field" placeholder="value"         value="${esc(value||'')}" style="background:var(--pm-bg);border:1px solid var(--pm-border);border-radius:4px;padding:6px 8px;font-size:11px;color:var(--pm-text);outline:none;flex:1">
            <button onclick="this.parentElement.remove()" style="background:none;border:none;color:var(--pm-text3);cursor:pointer;padding:4px;font-size:13px">✕</button>`;
        c.appendChild(row);
    }

    function saveEnv() {
        envVars = {};
        document.querySelectorAll('#envRows div').forEach(row => {
            const inputs = row.querySelectorAll('input');
            const k = inputs[0]?.value.trim(),
                v = inputs[1]?.value.trim();
            if (k) envVars[k] = v || '';
        });
        localStorage.setItem('api_env', JSON.stringify(envVars));
        closeModal('envModal');
        toast(`✓ Saved ${Object.keys(envVars).length} variables`, 'success');
    }

    function applyEnv(str) {
        if (!str) return str;
        return str.replace(/\{\{\s*(\w+)\s*\}\}/g, (m, k) => envVars.hasOwnProperty(k) ? envVars[k] : m);
    }

    // ═══════════════════════════════════════════════════
    //  UTILS
    // ═══════════════════════════════════════════════════
    function saveCols() {
        localStorage.setItem('api_collections', JSON.stringify(collections));
    }

    function toast(msg, type) {
        const t = document.getElementById('toast');
        t.textContent = msg;
        t.className = `toast ${type||'success'} show`;
        setTimeout(() => t.classList.remove('show'), 3000);
    }

    function esc(s) {
        return String(s || '').replace(/&/g, '&amp;').replace(/"/g, '&quot;').replace(/</g, '&lt;').replace(/>/g,
            '&gt;');
    }

    function statusText(s) {
        const map = {
            200: 'OK',
            201: 'Created',
            204: 'No Content',
            301: 'Moved',
            302: 'Found',
            400: 'Bad Request',
            401: 'Unauthorized',
            403: 'Forbidden',
            404: 'Not Found',
            405: 'Method Not Allowed',
            422: 'Unprocessable',
            429: 'Too Many Requests',
            500: 'Server Error',
            502: 'Bad Gateway',
            503: 'Unavailable'
        };
        return map[s] || '';
    }

    function b64toBlob(b64, mime) {
        const bytes = atob(b64),
            arr = new Uint8Array(bytes.length);
        for (let i = 0; i < bytes.length; i++) arr[i] = bytes.charCodeAt(i);
        return new Blob([arr], {
            type: mime
        });
    }

    function syntaxHL(json) {
        json = json.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
        return json.replace(
            /("(\\u[a-zA-Z0-9]{4}|\\[^u]|[^\\"])*"(\s*:)?|\b(true|false|null)\b|-?\d+(?:\.\d*)?(?:[eE][+-]?\d+)?)/g,
            m => {
                let cls = 'jn';
                if (/^"/.test(m)) cls = /:$/.test(m) ? 'jk' : 'js';
                else if (/true|false/.test(m)) cls = 'jb';
                else if (/null/.test(m)) cls = 'jnull';
                return `<span class="${cls}">${m}</span>`;
            });
    }

    function fmtSize(b) {
        if (!b) return '0 B';
        if (b < 1024) return b + ' B';
        if (b < 1048576) return (b / 1024).toFixed(1) + ' KB';
        return (b / 1048576).toFixed(1) + ' MB';
    }
