<?php
/**
 * ZType Custom Text Page (for pasted text)
 * This page loads custom pasted text for the typing game
 */

// Get custom text from POST
$customText = isset($_POST['text']) ? trim($_POST['text']) : '';
$flags = isset($_POST['flags']) ? intval($_POST['flags']) : 0;

// If no text provided, redirect back
if (empty($customText)) {
    header('Location: /load.php?error=notext');
    exit;
}

// Ensure minimum length
if (strlen($customText) < 50) {
    header('Location: /load.php?error=tooshort');
    exit;
}

// Process sentences to be treated as single words
$lines = explode("\n", $customText);
$processedLines = [];
foreach ($lines as $line) {
    $trimmedLine = trim($line);
    if (!empty($trimmedLine)) {
        // Replace spaces with non-breaking spaces
        $processedLines[] = str_replace(' ', "\xC2\xA0", $trimmedLine);
    }
}
$customText = implode(' ', $processedLines);
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
        <p><?php echo htmlspecialchars($customText, ENT_QUOTES, 'UTF-8'); ?></p>
    </div>
    
    <!-- Game canvas -->
    <div class="ztype-game">
        <canvas id="ztype-game-canvas"></canvas>
    </div>
</body>
</html>
