<!DOCTYPE html>
<html>
<head>
    <title>ZType – Load Your Own Text</title>
    <link rel="shortcut icon" href="media/favicon.png" type="image/png">
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no"/>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .container {
            background: white;
            border-radius: 12px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            max-width: 900px;
            width: 100%;
            padding: 40px;
            max-height: 90vh;
            overflow-y: auto;
        }
        
        h1 {
            color: #333;
            margin-bottom: 10px;
            font-size: 32px;
        }
        
        .subtitle {
            color: #666;
            margin-bottom: 30px;
            font-size: 16px;
        }
        
        .tabs {
            display: flex;
            gap: 10px;
            margin-bottom: 30px;
            border-bottom: 2px solid #e0e0e0;
        }
        
        .tab {
            padding: 12px 24px;
            cursor: pointer;
            border: none;
            background: none;
            color: #666;
            font-size: 16px;
            font-weight: 600;
            border-bottom: 3px solid transparent;
            transition: all 0.3s;
        }
        
        .tab:hover {
            color: #667eea;
        }
        
        .tab.active {
            color: #667eea;
            border-bottom-color: #667eea;
        }
        
        .tab-content {
            display: none;
        }
        
        .tab-content.active {
            display: block;
        }
        
        .input-group {
            margin-bottom: 20px;
        }
        
        label {
            display: block;
            color: #555;
            font-weight: 600;
            margin-bottom: 8px;
            font-size: 14px;
        }
        
        input[type="url"],
        input[type="text"],
        textarea {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 15px;
            font-family: inherit;
            transition: border-color 0.3s;
        }
        
        input[type="url"]:focus,
        input[type="text"]:focus,
        textarea:focus {
            outline: none;
            border-color: #667eea;
        }
        
        textarea {
            min-height: 200px;
            resize: vertical;
            line-height: 1.6;
        }
        
        .options {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }
        
        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        input[type="checkbox"] {
            width: 18px;
            height: 18px;
            cursor: pointer;
        }
        
        .checkbox-group label {
            margin: 0;
            cursor: pointer;
            font-weight: normal;
        }
        
        .button-group {
            display: flex;
            gap: 12px;
            margin-top: 30px;
        }
        
        button {
            padding: 14px 24px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            flex: 1;
        }
        
        .btn-primary:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(102, 126, 234, 0.4);
        }
        
        .btn-primary:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
        
        .btn-secondary {
            background: #f5f5f5;
            color: #666;
            flex: 1;
        }
        
        .btn-secondary:hover {
            background: #e0e0e0;
        }
        
        .btn-small {
            padding: 8px 16px;
            font-size: 14px;
            flex: none;
        }
        
        .btn-danger {
            background: #e74c3c;
            color: white;
        }
        
        .btn-danger:hover {
            background: #c0392b;
        }
        
        .info {
            background: #f0f7ff;
            border-left: 4px solid #667eea;
            padding: 16px;
            margin-top: 20px;
            border-radius: 4px;
            font-size: 14px;
            color: #555;
        }
        
        .success {
            background: #f0fff4;
            border-left: 4px solid #48bb78;
            padding: 16px;
            margin-top: 20px;
            border-radius: 4px;
            font-size: 14px;
            color: #2f855a;
            display: none;
        }
        
        .error {
            background: #fff0f0;
            border-left: 4px solid #e74c3c;
            padding: 16px;
            margin-top: 20px;
            border-radius: 4px;
            font-size: 14px;
            color: #c0392b;
            display: none;
        }
        
        .wordset-list {
            margin-top: 20px;
        }
        
        .wordset-item {
            background: #f8f9fa;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            padding: 16px;
            margin-bottom: 12px;
            transition: all 0.3s;
        }
        
        .wordset-item:hover {
            border-color: #667eea;
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.1);
        }
        
        .wordset-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 8px;
        }
        
        .wordset-name {
            font-weight: 600;
            font-size: 16px;
            color: #333;
        }
        
        .wordset-meta {
            font-size: 13px;
            color: #888;
            margin-bottom: 8px;
        }
        
        .wordset-actions {
            display: flex;
            gap: 8px;
        }
        
        .empty-state {
            text-align: center;
            padding: 40px;
            color: #999;
        }
        
        .loading {
            display: none;
            text-align: center;
            padding: 20px;
        }
        
        .spinner {
            border: 3px solid #f3f3f3;
            border-top: 3px solid #667eea;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
            margin: 0 auto 10px;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        .save-section {
            background: #f8f9fa;
            padding: 16px;
            border-radius: 8px;
            margin-top: 20px;
        }
        
        @media (max-width: 600px) {
            .container {
                padding: 24px;
            }
            
            h1 {
                font-size: 24px;
            }
            
            .button-group {
                flex-direction: column;
            }
            
            .wordset-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🎮 Load Your Own Text</h1>
        <p class="subtitle">Practice typing with any text you want!</p>
        
        <!-- Tabs -->
        <div class="tabs">
            <button class="tab active" onclick="switchTab('new')">New Text</button>
            <button class="tab" onclick="switchTab('saved')">Saved Sets (<span id="savedCount">0</span>)</button>
        </div>
        
        <!-- New Text Tab -->
        <div id="tab-new" class="tab-content active">
            <form id="loadForm">
                <div class="input-group">
                    <label for="url">Load from URL (Wikipedia, news articles, etc.)</label>
                    <input type="url" id="url" name="url" placeholder="https://en.wikipedia.org/wiki/Typing">
                </div>
                
                <div style="text-align: center; margin: 20px 0; color: #999;">— OR —</div>
                
                <div class="input-group">
                    <label for="text">Paste your own text (one sentence per line)</label>
                    <textarea id="text" name="text" placeholder="Type or paste any text here..."></textarea>
                </div>
                
                <div class="options">
                    <div class="checkbox-group">
                        <input type="checkbox" id="caseSensitive" name="caseSensitive">
                        <label for="caseSensitive">Case sensitive</label>
                    </div>
                    <div class="checkbox-group">
                        <input type="checkbox" id="punctuation" name="punctuation">
                        <label for="punctuation">Include punctuation</label>
                    </div>
                    <div class="checkbox-group">
                        <input type="checkbox" id="numbers" name="numbers">
                        <label for="numbers">Include numbers</label>
                    </div>
                </div>
                
                <!-- Save Section -->
                <div class="save-section">
                    <div class="checkbox-group" style="margin-bottom: 12px;">
                        <input type="checkbox" id="saveWordset">
                        <label for="saveWordset" style="font-weight: 600;">💾 Save this wordset for later</label>
                    </div>
                    <div id="saveNameGroup" class="input-group" style="display: none; margin-bottom: 0;">
                        <label for="wordsetName">Wordset Name</label>
                        <input type="text" id="wordsetName" placeholder="e.g., Programming Terms, Shakespeare Quotes">
                    </div>
                </div>
                
                <div class="button-group">
                    <button type="submit" class="btn-primary">Start Game</button>
                    <button type="button" class="btn-secondary" onclick="window.location.href='/'">Cancel</button>
                </div>
            </form>
            
            <div class="info">
                <strong>💡 Tips:</strong>
                <ul style="margin: 8px 0 0 20px; line-height: 1.8;">
                    <li>Load Wikipedia articles for educational content</li>
                    <li>Paste your favorite book excerpts or articles</li>
                    <li>Minimum 50 characters required</li>
                    <li>Save wordsets to replay them anytime!</li>
                </ul>
            </div>
        </div>
        
        <!-- Saved Sets Tab -->
        <div id="tab-saved" class="tab-content">
            <div id="savedWordsets" class="wordset-list">
                <div class="loading">
                    <div class="spinner"></div>
                    <p>Loading saved wordsets...</p>
                </div>
            </div>
        </div>
        
        <div class="loading" id="loading">
            <div class="spinner"></div>
            <p>Loading text...</p>
        </div>
        
        <div class="success" id="success"></div>
        <div class="error" id="error"></div>
    </div>
    
    <script>
        let savedWordsets = [];
        
        // Tab switching
        function switchTab(tabName) {
            document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
            document.querySelectorAll('.tab-content').forEach(t => t.classList.remove('active'));
            
            event.target.classList.add('active');
            document.getElementById('tab-' + tabName).classList.add('active');
            
            if (tabName === 'saved') {
                loadSavedWordsets();
            }
        }
        
        // Load saved wordsets
        async function loadSavedWordsets() {
            const container = document.getElementById('savedWordsets');
            container.innerHTML = '<div class="loading" style="display: block;"><div class="spinner"></div><p>Loading saved wordsets...</p></div>';
            
            try {
                const response = await fetch('/api/wordsets.php?action=list');
                const data = await response.json();
                
                if (data.success && data.wordsets) {
                    savedWordsets = data.wordsets;
                    document.getElementById('savedCount').textContent = data.wordsets.length;
                    
                    if (data.wordsets.length === 0) {
                        container.innerHTML = '<div class="empty-state">📝 No saved wordsets yet.<br>Create one in the "New Text" tab!</div>';
                    } else {
                        container.innerHTML = data.wordsets.map(ws => `
                            <div class="wordset-item">
                                <div class="wordset-header">
                                    <div class="wordset-name">${escapeHtml(ws.name)}</div>
                                    <div class="wordset-actions">
                                        <button class="btn-primary btn-small" onclick="playWordset(${ws.id})">Play</button>
                                        <button class="btn-danger btn-small" onclick="deleteWordset(${ws.id})">Delete</button>
                                    </div>
                                </div>
                                <div class="wordset-meta">
                                    ${ws.text_length} characters
                                    ${ws.source_url ? '• From URL' : '• Custom text'}
                                    ${ws.play_count > 0 ? `• Played ${ws.play_count} time${ws.play_count > 1 ? 's' : ''}` : ''}
                                    • Created ${formatDate(ws.created)}
                                </div>
                            </div>
                        `).join('');
                    }
                } else {
                    container.innerHTML = '<div class="error" style="display: block;">Failed to load wordsets</div>';
                }
            } catch (err) {
                container.innerHTML = '<div class="error" style="display: block;">Error loading wordsets</div>';
            }
        }
        
        // Play a saved wordset
        async function playWordset(id) {
            try {
                const response = await fetch(`/api/wordsets.php?action=load&id=${id}`);
                const data = await response.json();
                
                if (data.success && data.wordset) {
                    // Update play count
                    fetch('/api/wordsets.php', {
                        method: 'POST',
                        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                        body: `action=update_play_count&id=${id}`
                    });
                    
                    // Redirect to game with wordset
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = '/custom-text.php';
                    
                    const textField = document.createElement('input');
                    textField.type = 'hidden';
                    textField.name = 'text';
                    textField.value = data.wordset.text_content;
                    
                    const flagsField = document.createElement('input');
                    flagsField.type = 'hidden';
                    flagsField.name = 'flags';
                    flagsField.value = data.wordset.flags;
                    
                    form.appendChild(textField);
                    form.appendChild(flagsField);
                    document.body.appendChild(form);
                    form.submit();
                } else {
                    showError('Failed to load wordset');
                }
            } catch (err) {
                showError('Error loading wordset');
            }
        }
        
        // Delete a wordset
        async function deleteWordset(id) {
            if (!confirm('Are you sure you want to delete this wordset?')) {
                return;
            }
            
            // Prompt for password
            const password = prompt('Enter password to delete:');
            if (!password) {
                return; // User cancelled
            }
            
            try {
                const response = await fetch('/api/wordsets.php', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                    body: `action=delete&id=${id}&password=${encodeURIComponent(password)}`
                });
                
                const data = await response.json();
                
                if (data.success) {
                    showSuccess('Wordset deleted successfully');
                    loadSavedWordsets();
                } else {
                    showError(data.error || 'Failed to delete wordset');
                }
            } catch (err) {
                showError('Error deleting wordset');
            }
        }
        
        // Form submission
        const form = document.getElementById('loadForm');
        const urlInput = document.getElementById('url');
        const textInput = document.getElementById('text');
        const saveCheckbox = document.getElementById('saveWordset');
        const saveNameGroup = document.getElementById('saveNameGroup');
        const wordsetNameInput = document.getElementById('wordsetName');
        
        // Show/hide save name input
        saveCheckbox.addEventListener('change', () => {
            saveNameGroup.style.display = saveCheckbox.checked ? 'block' : 'none';
        });
        
        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            hideMessages();
            
            const url = urlInput.value.trim();
            const text = textInput.value.trim();
            
            if (!url && !text) {
                showError('Please provide either a URL or paste some text.');
                return;
            }
            
            if (text && text.length < 50) {
                showError('Please provide at least 50 characters of text.');
                return;
            }
            
            // Build flags
            let flags = 0;
            if (document.getElementById('caseSensitive').checked) flags |= 1;
            if (document.getElementById('punctuation').checked) flags |= 2;
            if (document.getElementById('numbers').checked) flags |= 4;
            
            // Check if we need to save
            if (saveCheckbox.checked) {
                const wordsetName = wordsetNameInput.value.trim();
                if (!wordsetName) {
                    showError('Please enter a name for the wordset');
                    return;
                }
                
                // Save the wordset first
                const textToSave = text || await fetchUrlText(url);
                if (!textToSave) {
                    showError('Failed to fetch text from URL');
                    return;
                }
                
                const saved = await saveWordsetToServer(wordsetName, textToSave, url, flags);
                if (!saved) {
                    return; // Error already shown
                }
                
                showSuccess('Wordset saved! Starting game...');
                await new Promise(resolve => setTimeout(resolve, 1000));
            }
            
            // Start the game
            if (url) {
                window.location.href = `/custom.php?url=${encodeURIComponent(url)}&flags=${flags}`;
            } else {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '/custom-text.php';
                
                const textField = document.createElement('input');
                textField.type = 'hidden';
                textField.name = 'text';
                textField.value = text;
                
                const flagsField = document.createElement('input');
                flagsField.type = 'hidden';
                flagsField.name = 'flags';
                flagsField.value = flags;
                
                form.appendChild(textField);
                form.appendChild(flagsField);
                document.body.appendChild(form);
                form.submit();
            }
        });
        
        // Fetch text from URL
        async function fetchUrlText(url) {
            try {
                const response = await fetch(`/api/fetch-url.php?url=${encodeURIComponent(url)}`);
                const data = await response.json();
                return data.text || null;
            } catch (err) {
                return null;
            }
        }
        
        // Save wordset to server
        async function saveWordsetToServer(name, text, url, flags) {
            try {
                const formData = new URLSearchParams();
                formData.append('action', 'save');
                formData.append('name', name);
                formData.append('text', text);
                formData.append('url', url);
                formData.append('flags', flags);
                
                const response = await fetch('/api/wordsets.php', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                    body: formData.toString()
                });
                
                const data = await response.json();
                
                if (data.success) {
                    return true;
                } else {
                    showError(data.error || 'Failed to save wordset');
                    return false;
                }
            } catch (err) {
                showError('Error saving wordset');
                return false;
            }
        }
        
        // Utility functions
        function showError(message) {
            const errorDiv = document.getElementById('error');
            errorDiv.textContent = message;
            errorDiv.style.display = 'block';
            setTimeout(() => errorDiv.style.display = 'none', 5000);
        }
        
        function showSuccess(message) {
            const successDiv = document.getElementById('success');
            successDiv.textContent = message;
            successDiv.style.display = 'block';
            setTimeout(() => successDiv.style.display = 'none', 3000);
        }
        
        function hideMessages() {
            document.getElementById('error').style.display = 'none';
            document.getElementById('success').style.display = 'none';
        }
        
        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }
        
        function formatDate(dateStr) {
            const date = new Date(dateStr);
            const now = new Date();
            const diffMs = now - date;
            const diffDays = Math.floor(diffMs / (1000 * 60 * 60 * 24));
            
            if (diffDays === 0) return 'today';
            if (diffDays === 1) return 'yesterday';
            if (diffDays < 7) return `${diffDays} days ago`;
            if (diffDays < 30) return `${Math.floor(diffDays / 7)} weeks ago`;
            
            return date.toLocaleDateString();
        }
        
        // Clear one field when the other is used
        urlInput.addEventListener('input', () => {
            if (urlInput.value) textInput.value = '';
        });
        
        textInput.addEventListener('input', () => {
            if (textInput.value) urlInput.value = '';
        });
        
        // Load saved count on page load
        loadSavedWordsets();
    </script>
</body>
</html>
