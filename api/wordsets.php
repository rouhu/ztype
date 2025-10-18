<?php
/**
 * ZType Wordsets API
 * Handles saving and loading custom word sets
 */

// Include shared configuration
require_once __DIR__ . '/config.php';

// CORS headers
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Get action from request
$action = $_POST['action'] ?? $_GET['action'] ?? '';

switch ($action) {
    case 'save':
        echo json_encode(saveWordset());
        break;
    
    case 'load':
        echo json_encode(loadWordset());
        break;
    
    case 'list':
        echo json_encode(listWordsets());
        break;
    
    case 'delete':
        echo json_encode(deleteWordset());
        break;
    
    case 'update_play_count':
        echo json_encode(updatePlayCount());
        break;
    
    default:
        http_response_code(400);
        echo json_encode(['error' => 'Invalid action']);
}

/**
 * Save a new wordset
 */
function saveWordset() {
    $pdo = getDB();
    $sessionId = getSessionID();
    
    // Get parameters
    $name = trim($_POST['name'] ?? '');
    $text = trim($_POST['text'] ?? '');
    $url = trim($_POST['url'] ?? '');
    $flags = intval($_POST['flags'] ?? 0);
    
    // Validate
    if (empty($name)) {
        return ['error' => 'Name is required'];
    }
    
    if (empty($text)) {
        return ['error' => 'Text content is required'];
    }
    
    if (strlen($name) > 100) {
        return ['error' => 'Name too long (max 100 characters)'];
    }
    
    if (strlen($text) < 50) {
        return ['error' => 'Text too short (minimum 50 characters)'];
    }
    
    try {
        // Check if name already exists for this session
        $stmt = $pdo->prepare("
            SELECT id FROM saved_wordsets 
            WHERE session_id = :session_id AND name = :name
        ");
        $stmt->execute([
            ':session_id' => $sessionId,
            ':name' => $name
        ]);
        
        if ($stmt->fetch()) {
            return ['error' => 'A wordset with this name already exists'];
        }
        
        // Insert new wordset
        $stmt = $pdo->prepare("
            INSERT INTO saved_wordsets (session_id, name, text_content, source_url, flags, created)
            VALUES (:session_id, :name, :text, :url, :flags, NOW())
        ");
        
        $stmt->execute([
            ':session_id' => $sessionId,
            ':name' => $name,
            ':text' => $text,
            ':url' => $url,
            ':flags' => $flags
        ]);
        
        return [
            'success' => true,
            'id' => $pdo->lastInsertId(),
            'message' => 'Wordset saved successfully'
        ];
        
    } catch (PDOException $e) {
        error_log("Error saving wordset: " . $e->getMessage());
        return ['error' => 'Failed to save wordset'];
    }
}

/**
 * Load a specific wordset (accessible to all users)
 */
function loadWordset() {
    $pdo = getDB();
    
    $id = intval($_GET['id'] ?? 0);
    
    if (!$id) {
        return ['error' => 'Wordset ID is required'];
    }
    
    try {
        $stmt = $pdo->prepare("
            SELECT id, name, text_content, source_url, flags, play_count, created
            FROM saved_wordsets
            WHERE id = :id
        ");
        
        $stmt->execute([':id' => $id]);
        
        $wordset = $stmt->fetch();
        
        if (!$wordset) {
            return ['error' => 'Wordset not found'];
        }
        
        return [
            'success' => true,
            'wordset' => $wordset
        ];
        
    } catch (PDOException $e) {
        error_log("Error loading wordset: " . $e->getMessage());
        return ['error' => 'Failed to load wordset'];
    }
}

/**
 * List all wordsets (visible to all users)
 */
function listWordsets() {
    $pdo = getDB();
    
    try {
        $stmt = $pdo->prepare("
            SELECT id, name, source_url, flags, play_count, created, last_played,
                   CHAR_LENGTH(text_content) as text_length
            FROM saved_wordsets
            ORDER BY last_played DESC, created DESC
        ");
        
        $stmt->execute();
        $wordsets = $stmt->fetchAll();
        
        return [
            'success' => true,
            'wordsets' => $wordsets
        ];
        
    } catch (PDOException $e) {
        error_log("Error listing wordsets: " . $e->getMessage());
        return ['error' => 'Failed to list wordsets'];
    }
}

/**
 * Delete a wordset (password protected, accessible to all users)
 */
function deleteWordset() {
    $pdo = getDB();
    
    $id = intval($_POST['id'] ?? 0);
    $password = $_POST['password'] ?? '';
    
    if (!$id) {
        return ['error' => 'Wordset ID is required'];
    }
    
    // Verify password
    if ($password !== DELETE_PASSWORD) {
        return ['error' => 'Invalid password'];
    }
    
    try {
        $stmt = $pdo->prepare("
            DELETE FROM saved_wordsets
            WHERE id = :id
        ");
        
        $stmt->execute([':id' => $id]);
        
        if ($stmt->rowCount() > 0) {
            return [
                'success' => true,
                'message' => 'Wordset deleted successfully'
            ];
        } else {
            return ['error' => 'Wordset not found'];
        }
        
    } catch (PDOException $e) {
        error_log("Error deleting wordset: " . $e->getMessage());
        return ['error' => 'Failed to delete wordset'];
    }
}

/**
 * Update play count for a wordset (accessible to all users)
 */
function updatePlayCount() {
    $pdo = getDB();
    
    $id = intval($_POST['id'] ?? 0);
    
    if (!$id) {
        return ['error' => 'Wordset ID is required'];
    }
    
    try {
        $stmt = $pdo->prepare("
            UPDATE saved_wordsets
            SET play_count = play_count + 1,
                last_played = NOW()
            WHERE id = :id
        ");
        
        $stmt->execute([':id' => $id]);
        
        return ['success' => true];
        
    } catch (PDOException $e) {
        error_log("Error updating play count: " . $e->getMessage());
        return ['error' => 'Failed to update play count'];
    }
}
