<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>API Client</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link
        href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@300;400;500;600&family=Inter:wght@300;400;500;600&display=swap"
        rel="stylesheet">
    <style>
    :root {
        --bg-primary: #1a1a2e;
        --bg-secondary: #16213e;
        --bg-panel: #1e1e2e;
        --bg-input: #13131f;
        --bg-hover: #252540;
        --bg-active: #2a2a4a;
        --accent: #e94560;
        --accent-dim: rgba(233, 69, 96, 0.15);
        --accent-hover: #ff6b6b;
        --green: #00d4aa;
        --yellow: #ffd166;
        --blue: #4cc9f0;
        --purple: #a855f7;
        --orange: #fb923c;
        --text-primary: #e2e8f0;
        --text-secondary: #94a3b8;
        --text-muted: #475569;
        --border: #2a2a4a;
        --border-light: #333355;
        --radius: 6px;
        --font-mono: 'JetBrains Mono', monospace;
        --font-ui: 'Inter', sans-serif;
        --tab-h: 36px;
    }

    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: var(--font-ui);
        background: var(--bg-primary);
        color: var(--text-primary);
        height: 100vh;
        display: flex;
        flex-direction: column;
        overflow: hidden;
        font-size: 13px;
    }

    .topbar {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 0 16px;
        height: 44px;
        background: var(--bg-secondary);
        border-bottom: 1px solid var(--border);
        flex-shrink: 0;
    }

    .logo {
        font-family: var(--font-mono);
        font-weight: 600;
        font-size: 14px;
        color: var(--accent);
        letter-spacing: 2px;
    }

    .logo span {
        color: var(--text-secondary);
        font-weight: 300;
    }

    .topbar-actions {
        margin-left: auto;
        display: flex;
        gap: 6px;
        align-items: center;
    }

    .btn {
        padding: 4px 12px;
        border-radius: var(--radius);
        border: 1px solid var(--border-light);
        background: transparent;
        color: var(--text-secondary);
        cursor: pointer;
        font-size: 11px;
        font-family: var(--font-ui);
        transition: all .2s;
        display: flex;
        align-items: center;
        gap: 5px;
        height: 28px;
    }

    .btn:hover {
        background: var(--bg-hover);
        color: var(--text-primary);
        border-color: var(--accent);
    }

    .btn-accent {
        background: var(--accent);
        color: #fff;
        border-color: var(--accent);
    }

    .btn-accent:hover {
        background: var(--accent-hover);
        color: #fff;
    }

    .save-hint {
        font-size: 10px;
        color: var(--text-muted);
        background: var(--bg-input);
        padding: 2px 8px;
        border-radius: var(--radius);
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .save-hint kbd {
        background: var(--bg-hover);
        border: 1px solid var(--border-light);
        border-radius: 3px;
        padding: 1px 5px;
        font-size: 9px;
        font-family: var(--font-mono);
        color: var(--text-secondary);
    }

    .tabs-bar {
        display: flex;
        align-items: stretch;
        background: #0f0f1a;
        border-bottom: 1px solid var(--border);
        height: var(--tab-h);
        overflow-x: auto;
        overflow-y: hidden;
        flex-shrink: 0;
        scrollbar-width: none;
    }

    .tabs-bar::-webkit-scrollbar {
        display: none;
    }

    .vtab {
        display: flex;
        align-items: center;
        gap: 7px;
        padding: 0 14px 0 12px;
        height: var(--tab-h);
        cursor: pointer;
        border-right: 1px solid var(--border);
        color: var(--text-muted);
        font-size: 12px;
        white-space: nowrap;
        flex-shrink: 0;
        min-width: 120px;
        max-width: 200px;
        transition: background .15s, color .15s;
        user-select: none;
    }

    .vtab:hover {
        background: var(--bg-hover);
        color: var(--text-secondary);
    }

    .vtab.active {
        background: var(--bg-primary);
        color: var(--text-primary);
        border-top: 2px solid var(--accent);
    }

    .vtab-name {
        flex: 1;
        overflow: hidden;
        text-overflow: ellipsis;
        min-width: 0;
    }

    .vtab.dirty .vtab-name::after {
        content: '●';
        font-size: 8px;
        color: var(--orange);
        margin-left: 5px;
        vertical-align: middle;
    }

    .vtab-method {
        font-family: var(--font-mono);
        font-size: 8px;
        font-weight: 700;
        padding: 1px 4px;
        border-radius: 3px;
        flex-shrink: 0;
    }

    .vtab-close {
        width: 16px;
        height: 16px;
        border-radius: 3px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 11px;
        color: var(--text-muted);
        flex-shrink: 0;
        opacity: 0;
        transition: opacity .15s, background .15s;
    }

    .vtab:hover .vtab-close,
    .vtab.dirty .vtab-close {
        opacity: 1;
    }

    .vtab-close:hover {
        background: rgba(233, 69, 96, 0.3);
        color: var(--accent);
    }

    .vtab-new {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 36px;
        height: var(--tab-h);
        cursor: pointer;
        color: var(--text-muted);
        font-size: 18px;
        flex-shrink: 0;
        transition: color .15s, background .15s;
    }

    .vtab-new:hover {
        color: var(--text-primary);
        background: var(--bg-hover);
    }

    .app-body {
        display: flex;
        flex: 1;
        overflow: hidden;
    }

    .sidebar {
        width: 270px;
        min-width: 200px;
        background: var(--bg-secondary);
        border-right: 1px solid var(--border);
        display: flex;
        flex-direction: column;
        overflow: hidden;
        flex-shrink: 0;
    }

    .sidebar-header {
        padding: 10px;
        border-bottom: 1px solid var(--border);
        display: flex;
        flex-direction: column;
        gap: 7px;
    }

    .sidebar-tabs {
        display: flex;
        gap: 3px;
    }

    .sidebar-tab {
        flex: 1;
        padding: 5px;
        text-align: center;
        border-radius: var(--radius);
        cursor: pointer;
        color: var(--text-muted);
        font-size: 11px;
        font-weight: 500;
        transition: all .2s;
    }

    .sidebar-tab.active {
        background: var(--bg-active);
        color: var(--text-primary);
    }

    .sidebar-actions {
        display: flex;
        gap: 4px;
    }

    .search-box {
        position: relative;
        flex: 1;
    }

    .search-box input {
        width: 100%;
        padding: 5px 8px 5px 26px;
        background: var(--bg-input);
        border: 1px solid var(--border);
        border-radius: var(--radius);
        color: var(--text-primary);
        font-size: 12px;
        font-family: var(--font-ui);
        outline: none;
        transition: border-color .2s;
    }

    .search-box input:focus {
        border-color: var(--accent);
    }

    .search-box::before {
        content: '⌕';
        position: absolute;
        left: 7px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--text-muted);
        font-size: 13px;
    }

    .sidebar-content {
        flex: 1;
        overflow-y: auto;
        padding: 6px;
    }

    .sidebar-content::-webkit-scrollbar {
        width: 3px;
    }

    .sidebar-content::-webkit-scrollbar-thumb {
        background: var(--border-light);
        border-radius: 2px;
    }

    .collection-group {
        margin-bottom: 3px;
    }

    .collection-header {
        display: flex;
        align-items: center;
        gap: 5px;
        padding: 6px 7px;
        border-radius: var(--radius);
        cursor: pointer;
        color: var(--text-secondary);
        font-weight: 500;
        transition: background .15s;
        font-size: 12px;
    }

    .collection-header:hover {
        background: var(--bg-hover);
        color: var(--text-primary);
    }

    .col-arrow {
        font-size: 9px;
        transition: transform .2s;
        flex-shrink: 0;
    }

    .col-arrow.open {
        transform: rotate(90deg);
    }

    .collection-name-text {
        flex: 1;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .collection-requests {
        padding-left: 6px;
        display: none;
    }

    .collection-requests.open {
        display: block;
    }

    .folder-group {
        margin-bottom: 1px;
    }

    .folder-header {
        display: flex;
        align-items: center;
        gap: 5px;
        padding: 4px 7px;
        border-radius: var(--radius);
        cursor: pointer;
        color: var(--text-muted);
        font-size: 11px;
        transition: background .15s;
    }

    .folder-header:hover {
        background: var(--bg-hover);
        color: var(--text-secondary);
    }

    .fol-arrow {
        font-size: 8px;
        transition: transform .2s;
        flex-shrink: 0;
    }

    .fol-arrow.open {
        transform: rotate(90deg);
    }

    .folder-name-text {
        flex: 1;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .folder-requests {
        padding-left: 8px;
        display: none;
    }

    .folder-requests.open {
        display: block;
    }

    .request-item {
        display: flex;
        align-items: center;
        gap: 7px;
        padding: 4px 7px;
        border-radius: var(--radius);
        cursor: pointer;
        color: var(--text-muted);
        font-size: 11px;
        transition: background .15s;
    }

    .request-item:hover {
        background: var(--bg-hover);
        color: var(--text-primary);
    }

    .request-item.active {
        background: var(--accent-dim);
        color: var(--text-primary);
        border-left: 2px solid var(--accent);
    }

    .item-name {
        flex: 1;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .item-menu-btn {
        opacity: 0;
        background: none;
        border: none;
        color: var(--text-muted);
        cursor: pointer;
        padding: 2px 5px;
        border-radius: 3px;
        font-size: 11px;
        flex-shrink: 0;
        line-height: 1;
        letter-spacing: 1px;
    }

    .collection-header:hover .item-menu-btn,
    .folder-header:hover .item-menu-btn,
    .request-item:hover .item-menu-btn {
        opacity: 1;
    }

    .item-menu-btn:hover {
        color: var(--text-primary);
        background: var(--bg-active);
    }

    .method-badge {
        font-family: var(--font-mono);
        font-size: 8px;
        font-weight: 700;
        padding: 1px 4px;
        border-radius: 3px;
        min-width: 34px;
        text-align: center;
        flex-shrink: 0;
    }

    .method-GET {
        background: rgba(0, 212, 170, 0.2);
        color: var(--green);
    }

    .method-POST {
        background: rgba(255, 209, 102, 0.2);
        color: var(--yellow);
    }

    .method-PUT {
        background: rgba(76, 201, 240, 0.2);
        color: var(--blue);
    }

    .method-PATCH {
        background: rgba(168, 85, 247, 0.2);
        color: var(--purple);
    }

    .method-DELETE {
        background: rgba(233, 69, 96, 0.2);
        color: var(--accent);
    }

    /* CONTEXT MENU */
    .ctx-menu {
        position: fixed;
        background: var(--bg-secondary);
        border: 1px solid var(--border-light);
        border-radius: var(--radius);
        box-shadow: 0 8px 32px rgba(0, 0, 0, .6);
        z-index: 9999;
        min-width: 190px;
        padding: 4px;
        display: none;
    }

    .ctx-menu.open {
        display: block;
    }

    .ctx-item {
        display: flex;
        align-items: center;
        gap: 9px;
        padding: 7px 11px;
        border-radius: 4px;
        cursor: pointer;
        font-size: 12px;
        color: var(--text-secondary);
        transition: background .12s, color .12s;
        white-space: nowrap;
    }

    .ctx-item:hover {
        background: var(--bg-hover);
        color: var(--text-primary);
    }

    .ctx-item.danger {
        color: #f87171;
    }

    .ctx-item.danger:hover {
        background: rgba(233, 69, 96, 0.15);
        color: #fca5a5;
    }

    .ctx-sep {
        height: 1px;
        background: var(--border);
        margin: 4px 0;
    }

    .empty-state {
        text-align: center;
        padding: 32px 12px;
        color: var(--text-muted);
    }

    .empty-state .icon {
        font-size: 28px;
        margin-bottom: 7px;
    }

    .empty-state p {
        font-size: 11px;
        line-height: 1.6;
    }

    .history-item {
        display: flex;
        align-items: center;
        gap: 7px;
        padding: 5px 7px;
        border-radius: var(--radius);
        cursor: pointer;
        transition: background .15s;
        border-bottom: 1px solid var(--border);
    }

    .history-item:hover {
        background: var(--bg-hover);
    }

    .history-info {
        flex: 1;
        min-width: 0;
    }

    .history-url {
        font-size: 10px;
        color: var(--text-secondary);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .history-time {
        font-size: 9px;
        color: var(--text-muted);
    }

    .main {
        flex: 1;
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }

    .request-bar {
        padding: 10px 14px;
        background: var(--bg-panel);
        border-bottom: 1px solid var(--border);
        display: flex;
        gap: 7px;
        align-items: center;
        flex-shrink: 0;
    }

    .method-select {
        background: var(--bg-input);
        border: 1px solid var(--border);
        border-radius: var(--radius);
        font-family: var(--font-mono);
        font-size: 12px;
        font-weight: 700;
        padding: 6px 9px;
        cursor: pointer;
        outline: none;
        min-width: 88px;
        color: var(--green);
    }

    .method-select:focus {
        border-color: var(--accent);
    }

    .method-select option {
        background: var(--bg-secondary);
        color: var(--text-primary);
    }

    .url-input {
        flex: 1;
        background: var(--bg-input);
        border: 1px solid var(--border);
        border-radius: var(--radius);
        color: var(--text-primary);
        font-family: var(--font-mono);
        font-size: 12px;
        padding: 6px 10px;
        outline: none;
        transition: border-color .2s;
    }

    .url-input:focus {
        border-color: var(--accent);
    }

    .send-btn {
        padding: 6px 18px;
        background: var(--accent);
        color: #fff;
        border: none;
        border-radius: var(--radius);
        font-weight: 600;
        font-size: 12px;
        cursor: pointer;
        transition: all .2s;
        white-space: nowrap;
    }

    .send-btn:hover {
        background: var(--accent-hover);
        transform: translateY(-1px);
    }

    .send-btn.loading {
        opacity: .7;
        pointer-events: none;
    }

    .request-response {
        flex: 1;
        display: flex;
        overflow: hidden;
    }

    .request-panel {
        width: 45%;
        min-width: 280px;
        border-right: 1px solid var(--border);
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }

    .response-panel {
        flex: 1;
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }

    .inner-tabs {
        display: flex;
        background: var(--bg-secondary);
        border-bottom: 1px solid var(--border);
        padding: 0 10px;
        flex-shrink: 0;
    }

    .tab {
        padding: 8px 12px;
        cursor: pointer;
        color: var(--text-muted);
        font-size: 11px;
        border-bottom: 2px solid transparent;
        transition: all .2s;
        white-space: nowrap;
    }

    .tab:hover {
        color: var(--text-secondary);
    }

    .tab.active {
        color: var(--accent);
        border-bottom-color: var(--accent);
    }

    .tab-content {
        display: none;
        flex: 1;
        overflow-y: auto;
        padding: 10px;
    }

    .tab-content.active {
        display: flex;
        flex-direction: column;
        gap: 7px;
    }

    .tab-content::-webkit-scrollbar {
        width: 3px;
    }

    .tab-content::-webkit-scrollbar-thumb {
        background: var(--border-light);
        border-radius: 2px;
    }

    .kv-row {
        display: flex;
        gap: 5px;
        align-items: center;
    }

    .kv-input {
        flex: 1;
        background: var(--bg-input);
        border: 1px solid var(--border);
        border-radius: var(--radius);
        color: var(--text-primary);
        font-family: var(--font-mono);
        font-size: 11px;
        padding: 5px 8px;
        outline: none;
        transition: border-color .2s;
    }

    .kv-input:focus {
        border-color: var(--accent);
    }

    .kv-input::placeholder {
        color: var(--text-muted);
    }

    .kv-delete {
        background: none;
        border: none;
        color: var(--text-muted);
        cursor: pointer;
        padding: 3px 5px;
        border-radius: 3px;
        font-size: 12px;
        transition: color .15s;
    }

    .kv-delete:hover {
        color: var(--accent);
    }

    .add-row-btn {
        padding: 4px 10px;
        background: transparent;
        border: 1px dashed var(--border-light);
        border-radius: var(--radius);
        color: var(--text-muted);
        cursor: pointer;
        font-size: 10px;
        transition: all .2s;
        align-self: flex-start;
    }

    .add-row-btn:hover {
        border-color: var(--accent);
        color: var(--accent);
    }

    .body-mode-bar {
        display: flex;
        gap: 4px;
        margin-bottom: 7px;
        flex-shrink: 0;
    }

    .body-mode-btn {
        padding: 3px 9px;
        background: transparent;
        border: 1px solid var(--border);
        border-radius: var(--radius);
        color: var(--text-muted);
        cursor: pointer;
        font-size: 10px;
        transition: all .2s;
    }

    .body-mode-btn.active {
        background: var(--accent-dim);
        border-color: var(--accent);
        color: var(--accent);
    }

    .code-textarea {
        flex: 1;
        min-height: 180px;
        background: var(--bg-input);
        border: 1px solid var(--border);
        border-radius: var(--radius);
        color: var(--text-primary);
        font-family: var(--font-mono);
        font-size: 11px;
        padding: 9px;
        outline: none;
        resize: vertical;
        line-height: 1.6;
        transition: border-color .2s;
    }

    .code-textarea:focus {
        border-color: var(--accent);
    }

    .auth-type-select {
        background: var(--bg-input);
        border: 1px solid var(--border);
        border-radius: var(--radius);
        color: var(--text-primary);
        font-size: 11px;
        padding: 6px 8px;
        outline: none;
        width: 100%;
        margin-bottom: 9px;
    }

    .form-label {
        font-size: 10px;
        color: var(--text-muted);
        margin-bottom: 3px;
        display: block;
    }

    .response-meta {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 7px 14px;
        background: var(--bg-secondary);
        border-bottom: 1px solid var(--border);
        flex-shrink: 0;
    }

    .status-badge {
        font-family: var(--font-mono);
        font-size: 11px;
        font-weight: 700;
        padding: 2px 9px;
        border-radius: var(--radius);
        background: var(--bg-input);
        color: var(--text-muted);
    }

    .status-2xx {
        background: rgba(0, 212, 170, 0.2);
        color: var(--green);
    }

    .status-3xx {
        background: rgba(76, 201, 240, 0.2);
        color: var(--blue);
    }

    .status-4xx {
        background: rgba(255, 209, 102, 0.2);
        color: var(--yellow);
    }

    .status-5xx {
        background: rgba(233, 69, 96, 0.2);
        color: var(--accent);
    }

    .meta-pill {
        font-size: 10px;
        color: var(--text-muted);
        background: var(--bg-input);
        padding: 2px 7px;
        border-radius: var(--radius);
    }

    .response-body-wrapper {
        flex: 1;
        overflow: hidden;
        display: flex;
        flex-direction: column;
    }

    .response-body {
        flex: 1;
        overflow-y: auto;
        padding: 10px 14px;
        font-family: var(--font-mono);
        font-size: 11px;
        line-height: 1.7;
        white-space: pre-wrap;
        word-break: break-all;
        color: var(--text-secondary);
        background: var(--bg-primary);
    }

    .response-body::-webkit-scrollbar {
        width: 5px;
    }

    .response-body::-webkit-scrollbar-thumb {
        background: var(--border-light);
        border-radius: 3px;
    }

    .json-string {
        color: #a8ff78;
    }

    .json-number {
        color: var(--blue);
    }

    .json-boolean {
        color: var(--purple);
    }

    .json-null {
        color: var(--text-muted);
    }

    .json-key {
        color: #ff9de2;
    }

    .response-placeholder {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        height: 100%;
        color: var(--text-muted);
        gap: 10px;
    }

    .response-placeholder .icon {
        font-size: 42px;
        opacity: .3;
    }

    .spinner {
        width: 18px;
        height: 18px;
        border: 2px solid var(--border-light);
        border-top-color: var(--accent);
        border-radius: 50%;
        animation: spin .7s linear infinite;
        display: none;
    }

    @keyframes spin {
        to {
            transform: rotate(360deg);
        }
    }

    /* MODALS */
    .modal-overlay {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, .7);
        z-index: 1000;
        align-items: center;
        justify-content: center;
    }

    .modal-overlay.open {
        display: flex;
    }

    .modal {
        background: var(--bg-secondary);
        border: 1px solid var(--border-light);
        border-radius: 10px;
        width: 500px;
        max-width: 90vw;
        max-height: 80vh;
        display: flex;
        flex-direction: column;
        box-shadow: 0 20px 60px rgba(0, 0, 0, .5);
    }

    .modal-header {
        display: flex;
        align-items: center;
        padding: 14px 18px;
        border-bottom: 1px solid var(--border);
        gap: 9px;
    }

    .modal-header h2 {
        font-size: 14px;
        font-weight: 600;
        flex: 1;
    }

    .modal-close {
        background: none;
        border: none;
        color: var(--text-muted);
        font-size: 17px;
        cursor: pointer;
        padding: 2px 5px;
        border-radius: 4px;
    }

    .modal-close:hover {
        color: var(--accent);
    }

    .modal-body {
        padding: 16px 18px;
        flex: 1;
        overflow-y: auto;
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .modal-footer {
        padding: 10px 18px;
        border-top: 1px solid var(--border);
        display: flex;
        gap: 7px;
        justify-content: flex-end;
    }

    .form-group {
        display: flex;
        flex-direction: column;
        gap: 5px;
    }

    .form-group label {
        font-size: 11px;
        color: var(--text-secondary);
        font-weight: 500;
    }

    .form-group input,
    .form-group select,
    .form-group textarea {
        background: var(--bg-input);
        border: 1px solid var(--border);
        border-radius: var(--radius);
        color: var(--text-primary);
        font-size: 12px;
        padding: 7px 10px;
        outline: none;
        font-family: var(--font-ui);
        transition: border-color .2s;
        width: 100%;
    }

    .form-group input:focus,
    .form-group select:focus,
    .form-group textarea:focus {
        border-color: var(--accent);
    }

    .form-group textarea {
        resize: vertical;
        min-height: 70px;
        font-family: var(--font-mono);
        font-size: 11px;
    }

    .form-group select option {
        background: var(--bg-secondary);
    }

    .toast {
        position: fixed;
        bottom: 18px;
        right: 18px;
        background: var(--bg-secondary);
        border: 1px solid var(--border-light);
        border-radius: var(--radius);
        padding: 9px 14px;
        font-size: 12px;
        color: var(--text-primary);
        z-index: 2000;
        transform: translateY(60px);
        opacity: 0;
        transition: all .3s;
        display: flex;
        align-items: center;
        gap: 7px;
    }

    .toast.show {
        transform: translateY(0);
        opacity: 1;
    }

    .toast.success {
        border-left: 3px solid var(--green);
    }

    .toast.error {
        border-left: 3px solid var(--accent);
    }

    ::-webkit-scrollbar {
        width: 5px;
        height: 5px;
    }

    ::-webkit-scrollbar-track {
        background: transparent;
    }

    ::-webkit-scrollbar-thumb {
        background: var(--border-light);
        border-radius: 3px;
    }

    .no-select {
        user-select: none;
    }
    </style>
</head>

<body>

    <div class="topbar">
        <div class="logo" id="appLogo"></div>
        <div class="topbar-actions">
            <div class="save-hint"><kbd>Ctrl</kbd>+<kbd>S</kbd> save</div>
            <button class="btn" onclick="openEnvModal()">🌐 Env</button>
            <button class="btn" onclick="exportAllCollections()">⬆ Export All</button>
            <label class="btn" style="cursor:pointer">📂 Import JSON<input type="file" id="importFile" accept=".json"
                    style="display:none" onchange="importPostman(event)"></label>
            <button class="btn btn-accent" onclick="openNewCollectionModal()">＋ New Collection</button>
        </div>
    </div>

    <div class="tabs-bar" id="tabsBar"></div>

    <div class="app-body">
        <div class="sidebar">
            <div class="sidebar-header">
                <div class="sidebar-tabs">
                    <div class="sidebar-tab active no-select" onclick="switchSidebarTab('collections',this)">Collections
                    </div>
                    <div class="sidebar-tab no-select" onclick="switchSidebarTab('history',this)">History</div>
                </div>
                <div class="sidebar-actions">
                    <div class="search-box"><input type="text" placeholder="Search requests..."
                            oninput="searchRequests(this.value)"></div>
                </div>
            </div>
            <div class="sidebar-content" id="sidebarContent"></div>
        </div>

        <div class="main">
            <div class="request-bar">
                <select class="method-select" id="methodSelect" onchange="updateMethodColor();markDirty()">
                    <option>GET</option>
                    <option>POST</option>
                    <option>PUT</option>
                    <option>PATCH</option>
                    <option>DELETE</option>
                </select>
                <input class="url-input" id="urlInput" type="text" placeholder="https://api.example.com/endpoint"
                    oninput="markDirty()">
                <div class="spinner" id="spinner"></div>
                <button class="send-btn" id="sendBtn" onclick="sendRequest()">Send ▶</button>
            </div>
            <div class="request-response">
                <div class="request-panel">
                    <div class="inner-tabs" id="reqTabs">
                        <div class="tab active no-select" onclick="switchTab('params',this,'reqTabs','reqContent')">
                            Params</div>
                        <div class="tab no-select" onclick="switchTab('auth',this,'reqTabs','reqContent')">Auth</div>
                        <div class="tab no-select" onclick="switchTab('headers',this,'reqTabs','reqContent')">Headers
                        </div>
                        <div class="tab no-select" onclick="switchTab('body',this,'reqTabs','reqContent')">Body</div>
                    </div>
                    <div id="reqContent" style="flex:1;overflow:hidden;display:flex;flex-direction:column;">
                        <div class="tab-content active" id="tab-params">
                            <div id="paramRows"></div><button class="add-row-btn"
                                onclick="addRow('paramRows','param');markDirty()">+ Add Param</button>
                        </div>
                        <div class="tab-content" id="tab-auth">
                            <select class="auth-type-select" id="authType" onchange="toggleAuthFields();markDirty()">
                                <option value="none">No Auth</option>
                                <option value="bearer">Bearer Token</option>
                                <option value="basic">Basic Auth</option>
                                <option value="apikey">API Key</option>
                            </select>
                            <div id="authFields"></div>
                        </div>
                        <div class="tab-content" id="tab-headers">
                            <div id="headerRows"></div><button class="add-row-btn"
                                onclick="addRow('headerRows','header');markDirty()">+ Add Header</button>
                        </div>
                        <div class="tab-content" id="tab-body">
                            <div class="body-mode-bar">
                                <button class="body-mode-btn active" onclick="setBodyMode('none',this)">none</button>
                                <button class="body-mode-btn" onclick="setBodyMode('json',this)">JSON</button>
                                <button class="body-mode-btn" onclick="setBodyMode('formdata',this)">form-data</button>
                                <button class="body-mode-btn"
                                    onclick="setBodyMode('urlencoded',this)">urlencoded</button>
                            </div>
                            <div id="bodyContent"></div>
                        </div>
                    </div>
                </div>
                <div class="response-panel">
                    <div class="inner-tabs" id="resTabs">
                        <div class="tab active no-select" onclick="switchTab('res-body',this,'resTabs','resContent')">
                            Body</div>
                        <div class="tab no-select" onclick="switchTab('res-headers',this,'resTabs','resContent')">
                            Headers</div>
                    </div>
                    <div class="response-meta">
                        <span class="status-badge" id="statusBadge">—</span>
                        <span class="meta-pill" id="timePill">— ms</span>
                        <span class="meta-pill" id="sizePill">— B</span>
                    </div>
                    <div id="resContent" style="flex:1;overflow:hidden;display:flex;flex-direction:column;">
                        <div class="tab-content active response-body-wrapper" id="tab-res-body">
                            <div class="response-body" id="responseBody">
                                <div class="response-placeholder">
                                    <div class="icon">⚡</div>
                                    <p>Send a request to see the response</p>
                                </div>
                            </div>
                        </div>
                        <div class="tab-content response-body-wrapper" id="tab-res-headers">
                            <div class="response-body" id="responseHeaders">
                                <div class="response-placeholder">
                                    <div class="icon">📋</div>
                                    <p>No response yet</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ENV MODAL -->
    <div class="modal-overlay" id="envModal">
        <div class="modal">
            <div class="modal-header"><span>🌐</span>
                <h2>Environment Variables</h2><button class="modal-close" onclick="closeModal('envModal')">✕</button>
            </div>
            <div class="modal-body" id="envRows"></div>
            <div class="modal-footer"><button class="btn" onclick="addEnvRow()">+ Add Variable</button><button
                    class="btn btn-accent" onclick="saveEnv()">Save</button></div>
        </div>
    </div>

    <!-- COLLECTION MODAL -->
    <div class="modal-overlay" id="collectionModal">
        <div class="modal">
            <div class="modal-header"><span id="colModalIcon">📦</span>
                <h2 id="colModalTitle">New Collection</h2><button class="modal-close"
                    onclick="closeModal('collectionModal')">✕</button>
            </div>
            <div class="modal-body">
                <div class="form-group"><label>Collection Name *</label><input id="colName"
                        placeholder="My API Collection" onkeydown="if(event.key==='Enter')saveCollectionModal()"></div>
                <div class="form-group"><label>Description</label><textarea id="colDesc"
                        placeholder="What is this collection for?"></textarea></div>
            </div>
            <div class="modal-footer"><button class="btn" onclick="closeModal('collectionModal')">Cancel</button><button
                    class="btn btn-accent" id="colModalSaveBtn" onclick="saveCollectionModal()">Create</button></div>
        </div>
    </div>

    <!-- FOLDER MODAL -->
    <div class="modal-overlay" id="folderModal">
        <div class="modal">
            <div class="modal-header"><span>📂</span>
                <h2 id="folderModalTitle">New Folder</h2><button class="modal-close"
                    onclick="closeModal('folderModal')">✕</button>
            </div>
            <div class="modal-body">
                <div class="form-group"><label>Folder Name *</label><input id="folderName" placeholder="Auth Endpoints"
                        onkeydown="if(event.key==='Enter')saveFolderModal()"></div>
            </div>
            <div class="modal-footer"><button class="btn" onclick="closeModal('folderModal')">Cancel</button><button
                    class="btn btn-accent" id="folderModalSaveBtn" onclick="saveFolderModal()">Create</button></div>
        </div>
    </div>

    <!-- SAVE REQUEST MODAL -->
    <div class="modal-overlay" id="requestModal">
        <div class="modal">
            <div class="modal-header"><span>💾</span>
                <h2>Save Request</h2><button class="modal-close" onclick="closeModal('requestModal')">✕</button>
            </div>
            <div class="modal-body">
                <div class="form-group"><label>Request Name *</label><input id="reqModalName"
                        placeholder="Get User Profile" onkeydown="if(event.key==='Enter')saveRequestModal()"></div>
                <div class="form-group"><label>Collection</label><select id="reqModalCollection"
                        onchange="populateReqModalFolders()"></select></div>
                <div class="form-group"><label>Folder (optional)</label><select id="reqModalFolder">
                        <option value="">— Root of Collection —</option>
                    </select></div>
            </div>
            <div class="modal-footer"><button class="btn" onclick="closeModal('requestModal')">Cancel</button><button
                    class="btn btn-accent" onclick="saveRequestModal()">Save</button></div>
        </div>
    </div>

    <!-- RENAME MODAL -->
    <div class="modal-overlay" id="renameModal">
        <div class="modal">
            <div class="modal-header"><span>✏️</span>
                <h2>Rename</h2><button class="modal-close" onclick="closeModal('renameModal')">✕</button>
            </div>
            <div class="modal-body">
                <div class="form-group"><label>New Name *</label><input id="renameName" placeholder="New name"
                        onkeydown="if(event.key==='Enter')confirmRename()"></div>
            </div>
            <div class="modal-footer"><button class="btn" onclick="closeModal('renameModal')">Cancel</button><button
                    class="btn btn-accent" onclick="confirmRename()">Rename</button></div>
        </div>
    </div>

    <!-- CONTEXT MENU -->
    <div class="ctx-menu" id="ctxMenu"></div>
    <div class="toast" id="toast"></div>

    <script>
    // ── STATE ──
    let collections = JSON.parse(localStorage.getItem('api_collections') || '[]');
    let historyList = JSON.parse(localStorage.getItem('api_history') || '[]');
    let envVars = JSON.parse(localStorage.getItem('api_env') || '{}');
    let tabs = JSON.parse(localStorage.getItem('api_tabs') || '[]');
    let activeTabId = localStorage.getItem('api_active_tab') || null;
    let tabCounter = parseInt(localStorage.getItem('api_tab_ctr') || '0');
    let sidebarTab = 'collections';
    let bodyMode = 'none';
    let requestMap = {};

    // Modal state
    let _colModalMode = 'new',
        _colModalIdx = -1;
    let _folderModalMode = 'new',
        _folderModalColIdx = -1,
        _folderModalPath = [];
    let _renameTarget = null;
    let _ctxOpen = false;

    let settingWebsite = {
        app_name: 'API Client'
    };

    let appName = settingWebsite.app_name;

    fetch('json/data-settingWebsite.json')
        .then(response => response.json())
        .then(data => {
            settingWebsite = data;
            appName = settingWebsite?.app_name || 'API Client';
            renderLogo();
        })
        .catch(() => {
            console.log('Using default app name');
            renderLogo();
        });

    function renderLogo() {
        const parts = appName.split(' ');
        const first = parts[0] || '';
        const second = parts.slice(1).join('') || '';

        document.getElementById('appLogo').innerHTML =
            first + '<span>' + second + '</span>';
    }
    // ── INIT ──
    renderEnvRows();
    renderSidebar();
    if (!tabs.length) newTab();
    else {
        renderTabsBar();
        switchToTab((tabs.find(t => t.id === activeTabId) || tabs[0]).id, false);
    }

    document.addEventListener('click', e => {
        if (_ctxOpen && !document.getElementById('ctxMenu').contains(e.target)) closeCtxMenu();
    });
    document.addEventListener('keydown', e => {
        // Ctrl+S = save current tab
        if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 's' && !e.shiftKey) {
            e.preventDefault();
            saveTab();
        }
        // Ctrl+Shift+S = save to collection
        if ((e.ctrlKey || e.metaKey) && e.shiftKey && (e.key === 'S' || e.key === 's')) {
            e.preventDefault();
            saveCurrentTabState();
            openSaveRequestModal();
        }
        if (e.key === 'Escape') closeCtxMenu();
    });

    // ── MODAL HELPERS ──
    function openModal(id) {
        document.getElementById(id).classList.add('open');
    }

    function closeModal(id) {
        document.getElementById(id).classList.remove('open');
    }

    // ── COLLECTION CRUD ──
    function openNewCollectionModal() {
        _colModalMode = 'new';
        document.getElementById('colModalIcon').textContent = '📦';
        document.getElementById('colModalTitle').textContent = 'New Collection';
        document.getElementById('colModalSaveBtn').textContent = 'Create';
        document.getElementById('colName').value = '';
        document.getElementById('colDesc').value = '';
        openModal('collectionModal');
        setTimeout(() => document.getElementById('colName').focus(), 100);
    }

    function openEditCollectionModal(ci) {
        _colModalMode = 'edit';
        _colModalIdx = ci;
        const col = collections[ci];
        document.getElementById('colModalIcon').textContent = '✏️';
        document.getElementById('colModalTitle').textContent = 'Edit Collection';
        document.getElementById('colModalSaveBtn').textContent = 'Save';
        document.getElementById('colName').value = col.name;
        document.getElementById('colDesc').value = col.description || '';
        openModal('collectionModal');
        setTimeout(() => document.getElementById('colName').focus(), 100);
    }

    function saveCollectionModal() {
        const name = document.getElementById('colName').value.trim();
        if (!name) {
            showToast('Name required', 'error');
            return;
        }
        if (_colModalMode === 'new') collections.push({
            id: Date.now(),
            name,
            description: document.getElementById('colDesc').value.trim(),
            items: []
        });
        else {
            collections[_colModalIdx].name = name;
            collections[_colModalIdx].description = document.getElementById('colDesc').value.trim();
        }
        saveCollections();
        renderSidebar();
        closeModal('collectionModal');
        showToast(_colModalMode === 'new' ? '✅ Collection created!' : '✅ Collection updated!', 'success');
    }

    function deleteCollection(ci) {
        if (!confirm(`Delete collection "${collections[ci].name}"? This cannot be undone.`)) return;
        collections.splice(ci, 1);
        saveCollections();
        renderSidebar();
        showToast('Collection deleted', 'success');
    }

    function exportCollection(ci) {
        // ambil waktu sekarang
        const now = new Date();

        const date =
            now.getFullYear() +
            (now.getMonth() + 1).toString().padStart(2, '0') +
            now.getDate().toString().padStart(2, '0');

        const time =
            now.getHours().toString().padStart(2, '0') +
            now.getMinutes().toString().padStart(2, '0') +
            now.getSeconds().toString().padStart(2, '0');

        // format: collection.appname-yyyymmdd-hhmmss.collection.json
        const fileName =
            collections[ci].name.replace(/\s+/g, '_') +
            '.' +
            appName.replace(/\s+/g, '') +
            '-' +
            date +
            '-' +
            time +
            '.collection.json';

        downloadJSON(
            collectionToPostman(collections[ci]),
            fileName
        );

        showToast('✅ Collection exported!', 'success');
    }

    function exportAllCollections() {
        if (!collections.length) {
            showToast('No collections to export', 'error');
            return;
        }
        downloadJSON({
            info: {
                name: 'All Collections',
                schema: 'https://schema.getpostman.com/json/collection/v2.1.0/collection.json'
            },
            item: collections.map(collectionToPostman)
        }, 'api_collections_export.json');
        showToast(`✅ Exported ${collections.length} collection(s)`, 'success');
    }

    // ── FOLDER CRUD ──
    function openNewFolderModal(ci, path) {
        _folderModalMode = 'new';
        _folderModalColIdx = ci;
        _folderModalPath = path || [];
        document.getElementById('folderModalTitle').textContent = 'New Folder';
        document.getElementById('folderModalSaveBtn').textContent = 'Create';
        document.getElementById('folderName').value = '';
        openModal('folderModal');
        setTimeout(() => document.getElementById('folderName').focus(), 100);
    }

    function saveFolderModal() {
        const name = document.getElementById('folderName').value.trim();
        if (!name) {
            showToast('Name required', 'error');
            return;
        }
        const items = collections[_folderModalColIdx].items;
        const target = _folderModalPath.length ? getContainerAtPath(items, _folderModalPath) : items;
        if (_folderModalMode === 'new') target.push({
            type: 'folder',
            name,
            items: []
        });
        saveCollections();
        renderSidebar();
        closeModal('folderModal');
        showToast('✅ Folder created!', 'success');
    }

    function deleteFolder(ci, path) {
        const parent = path.length === 1 ? collections[ci].items : getItemByPath(collections[ci].items, path.slice(0, -
            1)).items;
        if (!confirm(`Delete folder "${parent[path[path.length-1]].name}" and all its requests?`)) return;
        parent.splice(path[path.length - 1], 1);
        saveCollections();
        renderSidebar();
        showToast('Folder deleted', 'success');
    }

    // ── REQUEST CRUD ──
    function openSaveRequestModal() {
        saveCurrentTabState();
        if (!collections.length) {
            showToast('Create a collection first', 'error');
            return;
        }
        const tab = tabs.find(t => t.id === activeTabId);
        if (!tab) return;
        document.getElementById('reqModalName').value = tab.name === 'New Request' ? '' : tab.name;
        const colSel = document.getElementById('reqModalCollection');
        colSel.innerHTML = collections.map((c, i) => `<option value="${i}">${esc(c.name)}</option>`).join('');
        populateReqModalFolders();
        openModal('requestModal');
        setTimeout(() => document.getElementById('reqModalName').focus(), 100);
    }

    function populateReqModalFolders() {
        const ci = parseInt(document.getElementById('reqModalCollection').value);
        const sel = document.getElementById('reqModalFolder');
        sel.innerHTML = '<option value="">— Root of Collection —</option>';
        if (isNaN(ci) || !collections[ci]) return;
        const addFolders = (items, prefix, path) => items.forEach((item, i) => {
            if (item.type === 'folder') {
                const p = [...path, i];
                sel.add(new Option(prefix + item.name, JSON.stringify(p)));
                addFolders(item.items, prefix + '  ', p);
            }
        });
        addFolders(collections[ci].items, '📂 ', []);
    }

    function saveRequestModal() {
        const name = document.getElementById('reqModalName').value.trim();
        if (!name) {
            showToast('Request name required', 'error');
            return;
        }
        const ci = parseInt(document.getElementById('reqModalCollection').value);
        const folVal = document.getElementById('reqModalFolder').value;
        const path = folVal ? JSON.parse(folVal) : null;
        const tab = tabs.find(t => t.id === activeTabId);
        if (!tab) return;
        const reqData = {
            type: 'request',
            name,
            method: tab.method,
            url: tab.url,
            params: tab.params || {},
            headers: tab.headers || {},
            auth: tab.auth,
            body: tab.body,
            bodyMode: tab.bodyMode || 'none'
        };
        const container = path ? getItemByPath(collections[ci].items, path).items : collections[ci].items;
        container.push(reqData);
        tab.name = name;
        tab.dirty = false;
        saveTabs();
        saveCollections();
        renderSidebar();
        renderTabsBar();
        closeModal('requestModal');
        showToast('✅ Saved to collection!', 'success');
    }

    function duplicateRequest(ci, path, reqIdx) {
        const container = path ? getItemByPath(collections[ci].items, path).items : collections[ci].items;
        const copy = JSON.parse(JSON.stringify(container[reqIdx]));
        copy.name += ' (Copy)';
        container.splice(reqIdx + 1, 0, copy);
        saveCollections();
        renderSidebar();
        showToast('✅ Duplicated!', 'success');
    }

    function deleteRequest(ci, path, reqIdx) {
        const container = path ? getItemByPath(collections[ci].items, path).items : collections[ci].items;
        if (!confirm(`Delete "${container[reqIdx].name}"?`)) return;
        container.splice(reqIdx, 1);
        saveCollections();
        renderSidebar();
        showToast('Request deleted', 'success');
    }

    // ── RENAME ──
    function openRenameModal(target, currentName) {
        _renameTarget = target;
        document.getElementById('renameName').value = currentName;
        openModal('renameModal');
        setTimeout(() => {
            const i = document.getElementById('renameName');
            i.focus();
            i.select();
        }, 100);
    }

    function confirmRename() {
        const name = document.getElementById('renameName').value.trim();
        if (!name) {
            showToast('Name required', 'error');
            return;
        }
        const {
            type,
            ci,
            path,
            reqIdx
        } = _renameTarget;
        if (type === 'collection') collections[ci].name = name;
        else if (type === 'folder') getItemByPath(collections[ci].items, path).name = name;
        else if (type === 'request') {
            const container = path ? getItemByPath(collections[ci].items, path).items : collections[ci].items;
            container[reqIdx].name = name;
        }
        saveCollections();
        renderSidebar();
        closeModal('renameModal');
        showToast('✅ Renamed!', 'success');
    }

    // ── PATH HELPERS ──
    function getContainerAtPath(items, path) {
        let cur = items;
        for (const i of path) cur = cur[i].items;
        return cur;
    }

    function getItemByPath(items, path) {
        let cur = items;
        for (let i = 0; i < path.length - 1; i++) cur = cur[i].items;
        return cur[path[path.length - 1]];
    }

    // ── EXPORT HELPERS ──
    function collectionToPostman(col) {
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

    function downloadJSON(data, filename) {
        const a = document.createElement('a');
        a.href = URL.createObjectURL(new Blob([JSON.stringify(data, null, 2)], {
            type: 'application/json'
        }));
        a.download = filename;
        a.click();
    }

    // ── CONTEXT MENU ──
    function showCtxMenu(e, menuItems) {
        e.preventDefault();
        e.stopPropagation();
        const menu = document.getElementById('ctxMenu');
        menu.innerHTML = menuItems.map(it => {
            if (it === '-') return '<div class="ctx-sep"></div>';
            return `<div class="ctx-item${it.d?' danger':''}" onclick="(${it.fn})();closeCtxMenu()">${it.ic||''} ${it.lb}</div>`;
        }).join('');
        const x = Math.min(e.clientX, window.innerWidth - 210),
            y = Math.min(e.clientY, window.innerHeight - 320);
        menu.style.left = x + 'px';
        menu.style.top = y + 'px';
        menu.classList.add('open');
        _ctxOpen = true;
    }

    function closeCtxMenu() {
        document.getElementById('ctxMenu').classList.remove('open');
        _ctxOpen = false;
    }

    function ctxCollection(e, ci) {
        showCtxMenu(e, [{
                ic: '✏️',
                lb: 'Edit',
                fn: `function(){openEditCollectionModal(${ci})}`
            },
            {
                ic: '📂',
                lb: 'Add Folder',
                fn: `function(){openNewFolderModal(${ci},[])}`
            },
            {
                ic: '⬆',
                lb: 'Export as JSON',
                fn: `function(){exportCollection(${ci})}`
            },
            '-',
            {
                ic: '🗑',
                lb: 'Delete Collection',
                d: true,
                fn: `function(){deleteCollection(${ci})}`
            }
        ]);
    }

    function ctxFolder(e, ci, pathJson) {
        const path = JSON.parse(pathJson);
        const folder = getItemByPath(collections[ci].items, path);
        showCtxMenu(e, [{
                ic: '✏️',
                lb: 'Rename',
                fn: `function(){openRenameModal({type:'folder',ci:${ci},path:${pathJson}},'${folder.name.replace(/'/g,"\\'")}')}`
            },
            {
                ic: '📂',
                lb: 'Add Sub-folder',
                fn: `function(){openNewFolderModal(${ci},${pathJson})}`
            },
            '-',
            {
                ic: '🗑',
                lb: 'Delete Folder',
                d: true,
                fn: `function(){deleteFolder(${ci},${pathJson})}`
            }
        ]);
    }

    function ctxRequest(e, ci, pathJson, reqIdx) {
        const path = pathJson === 'null' ? null : JSON.parse(pathJson);
        const container = path ? getItemByPath(collections[ci].items, path).items : collections[ci].items;
        const name = container[reqIdx].name;
        showCtxMenu(e, [{
                ic: '↗',
                lb: 'Open in Tab',
                fn: `function(){loadRequestByPos(${ci},${pathJson},${reqIdx})}`
            },
            {
                ic: '📋',
                lb: 'Duplicate',
                fn: `function(){duplicateRequest(${ci},${pathJson==='null'?null:pathJson},${reqIdx})}`
            },
            {
                ic: '✏️',
                lb: 'Rename',
                fn: `function(){openRenameModal({type:'request',ci:${ci},path:${pathJson==='null'?null:pathJson},reqIdx:${reqIdx}},'${name.replace(/'/g,"\\'")}')}`
            },
            '-',
            {
                ic: '🗑',
                lb: 'Delete',
                d: true,
                fn: `function(){deleteRequest(${ci},${pathJson==='null'?null:pathJson},${reqIdx})}`
            }
        ]);
    }

    // ── RENDER SIDEBAR ──
    function renderSidebar() {
        requestMap = {};
        const c = document.getElementById('sidebarContent');
        if (sidebarTab === 'history') {
            if (!historyList.length) {
                c.innerHTML = '<div class="empty-state"><div class="icon">🕐</div><p>No history yet</p></div>';
                return;
            }
            c.innerHTML = historyList.slice().reverse().map((h, i) => `
      <div class="history-item" onclick="loadHistory(${historyList.length-1-i})">
        <span class="method-badge method-${h.method}">${h.method}</span>
        <div class="history-info"><div class="history-url">${esc(h.url)}</div><div class="history-time">${h.status?h.status+' · ':''}${h.time}ms · ${h.date}</div></div>
      </div>`).join('');
            return;
        }
        if (!collections.length) {
            c.innerHTML =
                '<div class="empty-state"><div class="icon">📁</div><p>No collections yet.<br>Import or click <b>+ New Collection</b>.</p></div>';
            return;
        }
        c.innerHTML = collections.map((col, ci) => `
    <div class="collection-group">
      <div class="collection-header" onclick="toggleCol('colReqs${ci}','colArrow${ci}')">
        <span class="col-arrow" id="colArrow${ci}">▶</span>
        <span>📦</span>
        <span class="collection-name-text" title="${esc(col.name)}">${esc(col.name)}</span>
        <button class="item-menu-btn" onclick="event.stopPropagation();ctxCollection(event,${ci})">···</button>
      </div>
      <div class="collection-requests" id="colReqs${ci}">${renderItems(col.items,'c'+ci,ci,[])}</div>
    </div>`).join('');
    }

    function renderItems(items, prefix, ci, parentPath) {
        return (items || []).map((item, i) => {
            const id = `${prefix}-${i}`,
                curPath = [...parentPath, i];
            const pathJson = JSON.stringify(curPath);
            if (item.type === 'folder') {
                return `<div class="folder-group">
        <div class="folder-header" onclick="toggleCol('fr${id}','fa${id}')">
          <span class="fol-arrow" id="fa${id}">▶</span><span>📂</span>
          <span class="folder-name-text" title="${esc(item.name)}">${esc(item.name)}</span>
          <button class="item-menu-btn" onclick="event.stopPropagation();ctxFolder(event,${ci},'${pathJson}')">···</button>
        </div>
        <div class="folder-requests" id="fr${id}">${renderItems(item.items,id,ci,curPath)}</div>
      </div>`;
            }
            const parentJson = parentPath.length ? JSON.stringify(parentPath) : 'null';
            requestMap[id] = {
                item,
                ci,
                parentPath
            };
            return `<div class="request-item" data-key="${id}" onclick="loadRequest('${id}',this)">
      <span class="method-badge method-${item.method}">${item.method}</span>
      <span class="item-name" title="${esc(item.name)}">${esc(item.name)}</span>
      <button class="item-menu-btn" onclick="event.stopPropagation();ctxRequest(event,${ci},'${parentJson}',${i})">···</button>
    </div>`;
        }).join('');
    }

    function toggleCol(targetId, arrowId) {
        const el = document.getElementById(targetId),
            ar = document.getElementById(arrowId);
        el?.classList.toggle('open');
        ar?.classList.toggle('open');
    }

    function loadRequest(key, el) {
        const {
            item
        } = requestMap[key] || {};
        if (!item) return;
        saveCurrentTabState();
        const existing = tabs.find(t => t.name === item.name && t.method === item.method);
        if (existing) switchToTab(existing.id, false);
        else newTab(item);
        document.querySelectorAll('.request-item').forEach(r => r.classList.remove('active'));
        el.classList.add('active');
    }

    function loadRequestByPos(ci, pathJson, reqIdx) {
        const path = (pathJson === null || pathJson === 'null') ? null : (typeof pathJson === 'string' ? JSON.parse(
            pathJson) : pathJson);
        const container = path ? getItemByPath(collections[ci].items, path).items : collections[ci].items;
        const item = container[reqIdx];
        if (!item) return;
        saveCurrentTabState();
        const existing = tabs.find(t => t.name === item.name && t.method === item.method);
        if (existing) switchToTab(existing.id, false);
        else newTab(item);
    }

    // ── TABS ──
    function newTab(item) {
        tabCounter++;
        const id = 'tab_' + tabCounter;
        tabs.push({
            id,
            name: item ? item.name : 'New Request',
            method: item ? item.method : 'GET',
            url: item ? item.url : '',
            params: item ? (item.params || {}) : {},
            headers: item ? (item.headers || {}) : {},
            auth: item ? item.auth : null,
            body: item ? item.body : null,
            bodyMode: item ? item.bodyMode : 'none',
            dirty: false,
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

    function switchToTab(id, saveFirst) {
        if (saveFirst !== false && activeTabId) saveCurrentTabState();
        activeTabId = id;
        localStorage.setItem('api_active_tab', id);
        renderTabsBar();
        const tab = tabs.find(t => t.id === id);
        if (!tab) return;
        document.getElementById('methodSelect').value = tab.method || 'GET';
        document.getElementById('urlInput').value = tab.url || '';
        updateMethodColor();
        document.getElementById('paramRows').innerHTML = '';
        Object.entries(tab.params || {}).forEach(([k, v]) => addRow('paramRows', 'param', k, v));
        addRow('paramRows', 'param');
        document.getElementById('headerRows').innerHTML = '';
        Object.entries(tab.headers || {}).forEach(([k, v]) => addRow('headerRows', 'header', k, v));
        addRow('headerRows', 'header');
        document.getElementById('authType').value = tab.auth?.type || 'none';
        toggleAuthFields();
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
        bodyMode = tab.bodyMode || 'none';
        document.querySelectorAll('.body-mode-btn').forEach(b => {
            const m = b.getAttribute('onclick').match(/setBodyMode\('([^']+)'/);
            if (m) b.classList.toggle('active', m[1] === bodyMode);
        });
        const bc = document.getElementById('bodyContent');
        bc.innerHTML = '';
        if (bodyMode === 'json') {
            bc.innerHTML = `<textarea class="code-textarea" id="bodyJson" oninput="markDirty()"></textarea>`;
            setTimeout(() => {
                const ta = document.getElementById('bodyJson');
                if (ta && tab.body) ta.value = typeof tab.body === 'string' ? tab.body : JSON.stringify(tab
                    .body, null, 2);
            }, 10);
        } else if (bodyMode === 'formdata' || bodyMode === 'urlencoded') {
            bc.innerHTML =
                `<div id="formRows"></div><button class="add-row-btn" onclick="addRow('formRows','form');markDirty()">+ Add Field</button>`;
            if (tab.body && typeof tab.body === 'object') Object.entries(tab.body).forEach(([k, v]) => addRow(
                'formRows', 'form', k, v));
            else addRow('formRows', 'form');
        }
        if (tab.response) {
            const r = tab.response;
            showResponse(r.status, r.body, r.headers, r.time, r.size, r.bodyType, r.contentType);
        } else {
            document.getElementById('responseBody').innerHTML =
                '<div class="response-placeholder"><div class="icon">⚡</div><p>Send a request to see the response</p></div>';
            document.getElementById('responseHeaders').innerHTML =
                '<div class="response-placeholder"><div class="icon">📋</div><p>No response yet</p></div>';
            document.getElementById('statusBadge').textContent = '—';
            document.getElementById('statusBadge').className = 'status-badge';
            document.getElementById('timePill').textContent = '— ms';
            document.getElementById('sizePill').textContent = '— B';
        }
    }

    function saveCurrentTabState() {
        const tab = tabs.find(t => t.id === activeTabId);
        if (!tab) return;
        tab.method = document.getElementById('methodSelect').value;
        tab.url = document.getElementById('urlInput').value;
        tab.params = getRows('paramRows');
        tab.headers = getRows('headerRows');
        tab.bodyMode = bodyMode;
        tab.auth = getAuthState();
        if (bodyMode === 'json') {
            const ta = document.getElementById('bodyJson');
            tab.body = ta ? ta.value : null;
        } else if (bodyMode === 'formdata' || bodyMode === 'urlencoded') tab.body = getRows('formRows');
        else tab.body = null;
        saveTabs();
    }

    function saveTab() {
        saveCurrentTabState();
        const tab = tabs.find(t => t.id === activeTabId);
        if (tab) {
            tab.dirty = false;
            saveTabs();
            renderTabsBar();
            showToast('✅ Tab saved!', 'success');
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
            GET: '#00d4aa',
            POST: '#ffd166',
            PUT: '#4cc9f0',
            PATCH: '#a855f7',
            DELETE: '#e94560'
        };
        bar.innerHTML = '';
        tabs.forEach(tab => {
            const el = document.createElement('div');
            el.className = 'vtab' + (tab.id === activeTabId ? ' active' : '') + (tab.dirty ? ' dirty' : '');
            el.onclick = () => {
                saveCurrentTabState();
                switchToTab(tab.id, false);
            };
            el.innerHTML = `<span class="vtab-method" style="background:${(MC[tab.method]||'#888')+'22'};color:${MC[tab.method]||'#e2e8f0'}">${tab.method}</span>
      <span class="vtab-name">${esc(tab.name)}</span>
      <span class="vtab-close" onclick="closeTab('${tab.id}',event)">✕</span>`;
            bar.appendChild(el);
        });
        const save = document.createElement('div');
        save.className = 'vtab-new';
        save.title = 'Save to Collection (💾)';
        save.style.fontSize = '14px';
        save.textContent = '💾';
        save.onclick = () => {
            saveCurrentTabState();
            openSaveRequestModal();
        };
        bar.appendChild(save);
        const plus = document.createElement('div');
        plus.className = 'vtab-new';
        plus.title = 'New Tab';
        plus.textContent = '+';
        plus.onclick = () => {
            saveCurrentTabState();
            newTab();
        };
        bar.appendChild(plus);
    }

    // ── TABS SWITCH ──
    function switchTab(name, el, tabsId, contentId) {
        document.querySelectorAll(`#${tabsId} .tab`).forEach(t => t.classList.remove('active'));
        document.querySelectorAll(`#${contentId} .tab-content`).forEach(c => c.classList.remove('active'));
        el.classList.add('active');
        document.getElementById(`tab-${name}`)?.classList.add('active');
    }

    function switchSidebarTab(tab, el) {
        sidebarTab = tab;
        document.querySelectorAll('.sidebar-tab').forEach(t => t.classList.remove('active'));
        el.classList.add('active');
        renderSidebar();
    }

    // ── KV ROWS ──
    function addRow(cId, type, key, value) {
        key = key || '';
        value = value || '';
        const c = document.getElementById(cId);
        if (!c) return;
        const row = document.createElement('div');
        row.className = 'kv-row';
        row.innerHTML = `<input class="kv-input" placeholder="${type==='param'?'key':'Header name'}" value="${esc(key)}" oninput="markDirty()">
    <input class="kv-input" placeholder="value" value="${esc(value)}" oninput="markDirty()">
    <button class="kv-delete" onclick="this.parentElement.remove();markDirty()">✕</button>`;
        c.appendChild(row);
    }

    function getRows(cId) {
        const rows = {};
        document.querySelectorAll(`#${cId} .kv-row`).forEach(row => {
            const i = row.querySelectorAll('input');
            const k = i[0]?.value.trim(),
                v = i[1]?.value.trim();
            if (k) rows[k] = v || '';
        });
        return rows;
    }

    // ── BODY MODE ──
    function setBodyMode(mode, btn) {
        bodyMode = mode;
        document.querySelectorAll('.body-mode-btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        const bc = document.getElementById('bodyContent');
        bc.innerHTML = '';
        if (mode === 'json') bc.innerHTML =
            `<textarea class="code-textarea" id="bodyJson" oninput="markDirty()" placeholder='{\n    "key": "value"\n}'></textarea>`;
        else if (mode === 'formdata' || mode === 'urlencoded') {
            bc.innerHTML =
                `<div id="formRows"></div><button class="add-row-btn" onclick="addRow('formRows','form');markDirty()">+ Add Field</button>`;
            addRow('formRows', 'form');
        }
        markDirty();
    }

    // ── AUTH ──
    function toggleAuthFields() {
        const type = document.getElementById('authType').value;
        const c = document.getElementById('authFields');
        c.innerHTML = '';
        if (type === 'bearer') c.innerHTML =
            `<label class="form-label">Token</label><input class="kv-input" id="bearerToken" placeholder="eyJhbGci..." style="width:100%" oninput="markDirty()">`;
        else if (type === 'basic') c.innerHTML =
            `<label class="form-label">Username</label><input class="kv-input" id="basicUser" placeholder="username" style="width:100%;margin-bottom:7px" oninput="markDirty()">
    <label class="form-label">Password</label><input class="kv-input" id="basicPass" placeholder="password" type="password" style="width:100%" oninput="markDirty()">`;
        else if (type === 'apikey') c.innerHTML =
            `<label class="form-label">Key Name</label><input class="kv-input" id="apiKeyName" placeholder="x-api-key" style="width:100%;margin-bottom:7px" oninput="markDirty()">
    <label class="form-label">Key Value</label><input class="kv-input" id="apiKeyValue" placeholder="your-api-key" style="width:100%" oninput="markDirty()">`;
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
            const t = applyEnvVars(document.getElementById('bearerToken')?.value.trim() || '');
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
                v = applyEnvVars(document.getElementById('apiKeyValue')?.value.trim() || '');
            return k ? {
                [k]: v
            } : {};
        }
        return {};
    }

    // ── SEND ──
    async function sendRequest() {
        let rawUrl = document.getElementById('urlInput').value.trim();
        if (!rawUrl) {
            showToast('Enter a URL first!', 'error');
            return;
        }
        let url = applyEnvVars(rawUrl);
        const method = document.getElementById('methodSelect').value;
        const rawH = {
            ...getRows('headerRows'),
            ...getAuthHeader()
        };
        const headers = {};
        for (const [k, v] of Object.entries(rawH)) headers[k] = applyEnvVars(v);
        const params = {};
        for (const [k, v] of Object.entries(getRows('paramRows'))) params[k] = applyEnvVars(v);
        const qp = new URLSearchParams(params).toString();
        if (qp) url += (url.includes('?') ? '&' : '?') + qp;
        let body = null;
        const bMode = bodyMode;
        if (['POST', 'PUT', 'PATCH', 'DELETE'].includes(method) && bMode !== 'none') {
            if (bMode === 'json') {
                const raw = applyEnvVars(document.getElementById('bodyJson')?.value.trim() || '');
                try {
                    body = JSON.parse(raw);
                } catch {
                    body = raw;
                }
                headers['Content-Type'] = 'application/json';
            } else {
                body = {};
                for (const [k, v] of Object.entries(getRows('formRows'))) body[k] = applyEnvVars(v);
            }
        }
        const btn = document.getElementById('sendBtn'),
            sp = document.getElementById('spinner');
        btn.classList.add('loading');
        btn.textContent = 'Sending...';
        sp.style.display = 'block';
        try {
            const res = await fetch('/proxy.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    url,
                    method,
                    headers,
                    body,
                    bodyMode: bMode
                })
            });
            const data = await res.json();
            if (data.error && !data.status) showResponse(null, data.error, {}, 0, 0);
            else {
                showResponse(data.status, data.body, data.headers, data.time, data.size, data.bodyType, data
                    .contentType);
                const tab = tabs.find(t => t.id === activeTabId);
                if (tab) {
                    tab.response = {
                        status: data.status,
                        body: data.body,
                        headers: data.headers,
                        time: data.time,
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
                    time: data.time
                });
            }
        } catch (err) {
            showResponse(null, `❌ Error: ${err.message}`, {}, 0, 0);
        } finally {
            btn.classList.remove('loading');
            btn.textContent = 'Send ▶';
            sp.style.display = 'none';
        }
    }

    function showResponse(status, body, headers, time, size, bodyType, contentType) {
        const badge = document.getElementById('statusBadge');
        badge.textContent = status || 'Error';
        badge.className = 'status-badge';
        if (status >= 200 && status < 300) badge.classList.add('status-2xx');
        else if (status >= 300 && status < 400) badge.classList.add('status-3xx');
        else if (status >= 400 && status < 500) badge.classList.add('status-4xx');
        else if (status >= 500) badge.classList.add('status-5xx');
        document.getElementById('timePill').textContent = `${time} ms`;
        document.getElementById('sizePill').textContent = formatSize(size);
        const bodyEl = document.getElementById('responseBody');
        if (bodyType === 'base64' && contentType && contentType.includes('pdf')) {
            const blob = base64ToBlob(body, 'application/pdf');
            const bUrl = URL.createObjectURL(blob);
            bodyEl.innerHTML = `<div style="display:flex;flex-direction:column;height:100%;gap:8px">
      <div style="display:flex;gap:8px;padding:4px 0;flex-shrink:0">
        <a href="${bUrl}" download="response.pdf" style="padding:4px 12px;background:var(--accent);color:#fff;border-radius:var(--radius);text-decoration:none;font-size:11px">⬇ Download PDF</a>
        <a href="${bUrl}" target="_blank" style="padding:4px 12px;background:var(--bg-hover);color:var(--text-primary);border-radius:var(--radius);text-decoration:none;font-size:11px;border:1px solid var(--border-light)">🔗 Open in Tab</a>
      </div>
      <iframe src="${bUrl}" style="flex:1;border:none;border-radius:var(--radius);background:#fff;min-height:400px"></iframe>
    </div>`;
        } else if (bodyType === 'base64') {
            const blob = base64ToBlob(body, contentType || 'application/octet-stream');
            const bUrl = URL.createObjectURL(blob);
            bodyEl.innerHTML =
                `<div style="padding:20px;color:var(--text-secondary)">Binary response (${contentType})<br><br><a href="${bUrl}" download="response.bin" style="color:var(--accent)">⬇ Download file</a></div>`;
        } else {
            try {
                bodyEl.innerHTML = syntaxHighlight(JSON.stringify(JSON.parse(body), null, 2));
            } catch {
                bodyEl.textContent = body;
            }
        }
        const hEl = document.getElementById('responseHeaders');
        hEl.innerHTML = Object.keys(headers || {}).length ? syntaxHighlight(JSON.stringify(headers, null, 2)) :
            'No headers';
    }

    function base64ToBlob(b64, mime) {
        const bytes = atob(b64);
        const arr = new Uint8Array(bytes.length);
        for (let i = 0; i < bytes.length; i++) arr[i] = bytes.charCodeAt(i);
        return new Blob([arr], {
            type: mime
        });
    }

    function syntaxHighlight(json) {
        json = json.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
        return json.replace(
            /("(\\u[a-zA-Z0-9]{4}|\\[^u]|[^\\"])*"(\s*:)?|\b(true|false|null)\b|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?)/g,
            m => {
                let cls = 'json-number';
                if (/^"/.test(m)) cls = /:$/.test(m) ? 'json-key' : 'json-string';
                else if (/true|false/.test(m)) cls = 'json-boolean';
                else if (/null/.test(m)) cls = 'json-null';
                return `<span class="${cls}">${m}</span>`;
            });
    }

    function formatSize(b) {
        if (!b) return '0 B';
        if (b < 1024) return b + ' B';
        return (b / 1024).toFixed(1) + ' KB';
    }

    // ── IMPORT ──
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
                    showToast('Unrecognized format', 'error');
                    return;
                }
                collections.push(...imported);
                saveCollections();
                renderSidebar();
                showToast(`✅ Imported ${imported.length} collection(s)!`, 'success');
            } catch (err) {
                showToast('Parse error: ' + err.message, 'error');
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
            if (!Object.keys(params).length) {
                try {
                    const clean = rawUrl.replace(/\{\{[^}]+\}\}/g, 'X');
                    new URL(clean).searchParams.forEach((v, k) => {
                        params[k] = v;
                    });
                } catch {}
            }
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

    // ── HISTORY ──
    function saveHistory(entry) {
        entry.date = new Date().toLocaleTimeString('id-ID');
        historyList.push(entry);
        if (historyList.length > 100) historyList.shift();
        localStorage.setItem('api_history', JSON.stringify(historyList));
    }

    function loadHistory(i) {
        const h = historyList[i];
        saveCurrentTabState();
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

    // ── ENV ──
    function openEnvModal() {
        renderEnvRows();
        openModal('envModal');
    }

    function renderEnvRows() {
        const c = document.getElementById('envRows');
        c.innerHTML = '';
        Object.entries(envVars).length ? Object.entries(envVars).forEach(([k, v]) => addEnvRow(k, v)) : addEnvRow();
    }

    function addEnvRow(key, value) {
        key = key || '';
        value = value || '';
        const c = document.getElementById('envRows');
        const row = document.createElement('div');
        row.className = 'kv-row';
        row.innerHTML = `<input class="kv-input" placeholder="VARIABLE_NAME" value="${esc(key)}" style="font-family:var(--font-mono)">
    <input class="kv-input" placeholder="value" value="${esc(value)}">
    <button class="kv-delete" onclick="this.parentElement.remove()">✕</button>`;
        c.appendChild(row);
    }

    function saveEnv() {
        envVars = {};
        document.querySelectorAll('#envRows .kv-row').forEach(row => {
            const i = row.querySelectorAll('input');
            const k = i[0].value.trim(),
                v = i[1].value.trim();
            if (k) envVars[k] = v;
        });
        localStorage.setItem('api_env', JSON.stringify(envVars));
        closeModal('envModal');
        showToast(`✅ Saved ${Object.keys(envVars).length} variables`, 'success');
    }

    function applyEnvVars(str) {
        if (!str) return str;
        return str.replace(/\{\{\s*(\w+)\s*\}\}/g, (m, k) => envVars.hasOwnProperty(k) ? envVars[k] : m);
    }

    function saveCollections() {
        localStorage.setItem('api_collections', JSON.stringify(collections));
    }

    function searchRequests(q) {
        q = q.toLowerCase();
        document.querySelectorAll('.request-item').forEach(el => {
            el.style.display = el.textContent.toLowerCase().includes(q) ? '' : 'none';
        });
    }

    function updateMethodColor() {
        const sel = document.getElementById('methodSelect');
        const C = {
            GET: '#00d4aa',
            POST: '#ffd166',
            PUT: '#4cc9f0',
            PATCH: '#a855f7',
            DELETE: '#e94560'
        };
        sel.style.color = C[sel.value] || '#e2e8f0';
        const tab = tabs.find(t => t.id === activeTabId);
        if (tab) {
            tab.method = sel.value;
            renderTabsBar();
        }
    }

    function showToast(msg, type) {
        type = type || 'success';
        const t = document.getElementById('toast');
        t.textContent = msg;
        t.className = `toast ${type} show`;
        setTimeout(() => t.classList.remove('show'), 3000);
    }

    function esc(str) {
        str = str || '';
        return String(str).replace(/&/g, '&amp;').replace(/"/g, '&quot;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
    }

    updateMethodColor();
    </script>
</body>

</html>