<?php
/**
 * ZType Custom Text Page
 * This page loads custom text for the typing game
 */

// Get custom text from session storage or URL parameters
$customText = '';
$flags = isset($_GET['flags']) ? intval($_GET['flags']) : 0;
$url = isset($_GET['url']) ? $_GET['url'] : '';

// If URL is provided, fetch the content
if ($url) {
    // Validate URL
    if (filter_var($url, FILTER_VALIDATE_URL)) {
        // Fetch content from URL
        $context = stream_context_create([
            'http' => [
                'timeout' => 10,
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'
            ]
        ]);
        
        $content = @file_get_contents($url, false, $context);
        
        if ($content !== false) {
            // Strip HTML tags and get text content
            $customText = strip_tags($content);
        }
    }
} else {
    // Fallback to posted data if no URL
    $customText = isset($_POST['text']) ? $_POST['text'] : '';
}

// If no text loaded, show error
if (empty($customText)) {
    header('Location: /load.php?error=failed');
    exit;
}

// Ensure minimum length
if (strlen($customText) < 100) {
    header('Location: /load.php?error=tooshort');
    exit;
}

// Process sentences to be treated as single words
$wordsByLength = [];

if ($url) {
    // For URL-based text, split into words
    $words = preg_split('/[\s,]+/', $customText);
    foreach ($words as $word) {
        $word = trim($word);
        if (!empty($word)) {
            $len = strlen($word);
            if (!isset($wordsByLength[$len])) {
                $wordsByLength[$len] = [];
            }
            $wordsByLength[$len][] = $word;
        }
    }
} else {
    // For pasted text, split by newlines
    $lines = explode("\n", $customText);
    foreach ($lines as $line) {
        $trimmedLine = trim($line);
        if (!empty($trimmedLine)) {
            $len = strlen($trimmedLine);
            if (!isset($wordsByLength[$len])) {
                $wordsByLength[$len] = [];
            }
            $wordsByLength[$len][] = $trimmedLine;
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>ZType – Custom Text Mode</title>
    <link rel="shortcut icon" href="media/favicon.png" type="image/png">
    <link rel="icon" href="media/favicon.png" type="image/png">
    
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no"/>
    
    <style type="text/css">
        html,body {
            background-color: #000;
            background-image: url(media/background/page.png);
            color: #555;
            font-family: helvetica, arial, sans-serif;
            margin: 0;
            padding: 0;
            font-size: 10pt;
        }
        
        #ztype-game-canvas {
            border: 0;
            z-index: 1000002;
            box-shadow: 0 0 30px #000;
        }
        
        .ztype-game {
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        
        /* Hide the custom text content */
        #custom-text-content {
            position: absolute;
            left: -9999px;
            visibility: hidden;
        }
        
        #ztype-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.9);
            z-index: 1000000;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: opacity 0.3s;
        }
        
        #ztype-overlay.ztype-playing {
            opacity: 0;
            pointer-events: none;
        }
        
        #ztype-scan-progress {
            width: 0%;
            height: 4px;
            background: #4dfed2;
            transition: width 0.3s;
        }
        
        .ztype-current-text-fragment {
            background-color: rgba(77, 254, 210, 0.2);
            padding: 2px 0;
        }
    </style>
    
    <script type="text/javascript">
        // Enable document mode for custom text
        window.ZTypeDocumentMode = true;
        window.ZTYPE_FLAGS = <?php echo $flags; ?>;
    </script>
    
    <script type="text/javascript" src="ztype.min.js?v22" charset="UTF-8"></script>

    <script type="text/javascript">
        // Override the WORDS object with our custom sentences
        window.WORDS = <?php echo json_encode($wordsByLength); ?>;
    </script>
</head>
<body>
    <!-- Overlay for scanning animation -->
    <div id="ztype-overlay" class="ztype-scanning">
        <div style="text-align: center; color: #4dfed2;">
            <div style="font-size: 24px; margin-bottom: 20px;">SCANNING TEXT...</div>
            <div style="width: 300px; background: rgba(255,255,255,0.1); height: 4px; border-radius: 2px;">
                <div id="ztype-scan-progress"></div>
            </div>
        </div>
    </div>
    
    <!-- Custom text content (hidden but scanned by game) -->
    <div id="custom-text-content">
        <?php echo htmlspecialchars($customText, ENT_QUOTES, 'UTF-8'); ?>
    </div>
    
    <!-- Game canvas -->
    <div class="ztype-game">
        <canvas id="ztype-game-canvas"></canvas>
    </div>
</body>
</html>
