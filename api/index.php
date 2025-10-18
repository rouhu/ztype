<?php
/**
 * ZType Game Backend API
 * Handles game statistics, leaderboards, and screenshot sharing
 */

// Include shared configuration
require_once __DIR__ . '/config.php';

// CORS headers for cross-origin requests
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Submit game statistics
function submitStats($data) {
    $pdo = getDB();
    $sessionId = getSessionID();
    $ip = getClientIP();
    
    // Validate input
    $score = filter_var($data['score'] ?? 0, FILTER_VALIDATE_INT);
    $wave = filter_var($data['wave'] ?? 0, FILTER_VALIDATE_INT);
    $streak = filter_var($data['streak'] ?? 0, FILTER_VALIDATE_INT);
    $accuracy = filter_var($data['accuracy'] ?? 0, FILTER_VALIDATE_FLOAT);
    
    if ($score === false || $wave === false || $streak === false || $accuracy === false) {
        return ['error' => 'Invalid input data'];
    }
    
    // Validate ranges
    if ($score < 0 || $score > 999999 || $wave < 0 || $wave > 999 || 
        $streak < 0 || $streak > 9999 || $accuracy < 0 || $accuracy > 100) {
        return ['error' => 'Data out of valid range'];
    }
    
    try {
        $stmt = $pdo->prepare("
            INSERT INTO game_stats (session_id, ip_address, score, wave, streak, accuracy, created)
            VALUES (:session_id, :ip, :score, :wave, :streak, :accuracy, NOW())
        ");
        
        $stmt->execute([
            ':session_id' => $sessionId,
            ':ip' => $ip,
            ':score' => $score,
            ':wave' => $wave,
            ':streak' => $streak,
            ':accuracy' => $accuracy
        ]);
        
        // Clean up old stats for this session (keep only last 30)
        $stmt = $pdo->prepare("
            DELETE FROM game_stats 
            WHERE session_id = :session_id 
            AND id NOT IN (
                SELECT id FROM (
                    SELECT id FROM game_stats 
                    WHERE session_id = :session_id 
                    ORDER BY created DESC 
                    LIMIT :limit
                ) tmp
            )
        ");
        
        $stmt->execute([
            ':session_id' => $sessionId,
            ':limit' => MAX_STATS_PER_USER
        ]);
        
        return loadStats();
        
    } catch (PDOException $e) {
        error_log("Error submitting stats: " . $e->getMessage());
        return ['error' => 'Failed to save statistics'];
    }
}

// Load user statistics
function loadStats() {
    $pdo = getDB();
    $sessionId = getSessionID();
    
    try {
        // Get user's recent games
        $stmt = $pdo->prepare("
            SELECT score, wave, streak, accuracy, UNIX_TIMESTAMP(created) as created
            FROM game_stats
            WHERE session_id = :session_id
            ORDER BY created DESC
            LIMIT :limit
        ");
        
        $stmt->execute([
            ':session_id' => $sessionId,
            ':limit' => MAX_STATS_PER_USER
        ]);
        
        $games = $stmt->fetchAll();
        
        // Reverse to show oldest first
        $games = array_reverse($games);
        
        // Get total game count for this session
        $stmt = $pdo->prepare("
            SELECT COUNT(*) as total FROM game_stats WHERE session_id = :session_id
        ");
        $stmt->execute([':session_id' => $sessionId]);
        $totalGames = $stmt->fetch()['total'];
        
        return [
            'games' => $games,
            'isExcerpt' => $totalGames > MAX_STATS_PER_USER,
            'totalGames' => (int)$totalGames
        ];
        
    } catch (PDOException $e) {
        error_log("Error loading stats: " . $e->getMessage());
        return ['error' => 'Failed to load statistics'];
    }
}

// Load all statistics (for detailed stats view)
function loadAllStats() {
    $pdo = getDB();
    $sessionId = getSessionID();
    
    try {
        $stmt = $pdo->prepare("
            SELECT score, wave, streak, accuracy, UNIX_TIMESTAMP(created) as created
            FROM game_stats
            WHERE session_id = :session_id
            ORDER BY created ASC
        ");
        
        $stmt->execute([':session_id' => $sessionId]);
        $games = $stmt->fetchAll();
        
        return ['games' => $games];
        
    } catch (PDOException $e) {
        error_log("Error loading all stats: " . $e->getMessage());
        return ['error' => 'Failed to load statistics'];
    }
}

// Save screenshot for social sharing
function saveScreenshot($data) {
    // Validate base64 data
    if (empty($data['data']) || !preg_match('/^data:image\/png;base64,/', $data['data'])) {
        return ['error' => 'Invalid screenshot data'];
    }
    
    // Extract base64 data
    $imageData = preg_replace('/^data:image\/png;base64,/', '', $data['data']);
    $imageData = base64_decode($imageData);
    
    if ($imageData === false) {
        return ['error' => 'Failed to decode image data'];
    }
    
    // Validate image size (max 5MB)
    if (strlen($imageData) > 5 * 1024 * 1024) {
        return ['error' => 'Image too large'];
    }
    
    // Create screenshots directory if it doesn't exist
    if (!is_dir(SCREENSHOT_DIR)) {
        mkdir(SCREENSHOT_DIR, 0755, true);
    }
    
    // Generate unique filename
    $filename = 'ztype-scores-' . date('Y-m-d-His') . '-' . bin2hex(random_bytes(4)) . '.png';
    $filepath = SCREENSHOT_DIR . $filename;
    
    // Save file
    if (file_put_contents($filepath, $imageData) === false) {
        return ['error' => 'Failed to save screenshot'];
    }
    
    // Clean up old screenshots (keep only last 100)
    cleanupOldScreenshots();
    
    return ['file' => 'screenshots/' . $filename];
}

// Clean up old screenshots
function cleanupOldScreenshots() {
    $files = glob(SCREENSHOT_DIR . '*.png');
    
    if (count($files) > 100) {
        // Sort by modification time
        usort($files, function($a, $b) {
            return filemtime($a) - filemtime($b);
        });
        
        // Delete oldest files
        $filesToDelete = array_slice($files, 0, count($files) - 100);
        foreach ($filesToDelete as $file) {
            @unlink($file);
        }
    }
}

// Get leaderboard
function getLeaderboard($limit = 100) {
    $pdo = getDB();
    
    try {
        $stmt = $pdo->prepare("
            SELECT 
                session_id,
                MAX(score) as best_score,
                MAX(wave) as best_wave,
                MAX(streak) as best_streak,
                MAX(accuracy) as best_accuracy,
                COUNT(*) as total_games
            FROM game_stats
            GROUP BY session_id
            ORDER BY best_score DESC
            LIMIT :limit
        ");
        
        $stmt->execute([':limit' => $limit]);
        return ['leaderboard' => $stmt->fetchAll()];
        
    } catch (PDOException $e) {
        error_log("Error loading leaderboard: " . $e->getMessage());
        return ['error' => 'Failed to load leaderboard'];
    }
}

// Main request handler
function handleRequest() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        return ['error' => 'Only POST requests are allowed'];
    }
    
    // Parse POST data
    $postData = [];
    parse_str(file_get_contents('php://input'), $postData);
    
    // Route based on action
    if (isset($postData['load']) && $postData['load']) {
        return loadStats();
    }
    
    if (isset($postData['loadAll']) && $postData['loadAll']) {
        return loadAllStats();
    }
    
    if (isset($postData['saveScreenshot']) && $postData['saveScreenshot']) {
        return saveScreenshot($postData);
    }
    
    if (isset($postData['leaderboard']) && $postData['leaderboard']) {
        return getLeaderboard();
    }
    
    // Default: submit stats
    if (isset($postData['score'])) {
        return submitStats($postData);
    }
    
    return ['error' => 'Invalid request'];
}

// Execute and return response
try {
    $response = handleRequest();
    echo json_encode($response);
} catch (Exception $e) {
    error_log("Unhandled exception: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Internal server error']);
}
