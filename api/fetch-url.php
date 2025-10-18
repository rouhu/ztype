<?php
/**
 * ZType URL Fetcher
 * Fetches and extracts text content from URLs
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

$url = $_GET['url'] ?? '';

if (empty($url)) {
    echo json_encode(['error' => 'URL is required']);
    exit;
}

// Validate URL
if (!filter_var($url, FILTER_VALIDATE_URL)) {
    echo json_encode(['error' => 'Invalid URL']);
    exit;
}

// Fetch content
$context = stream_context_create([
    'http' => [
        'timeout' => 10,
        'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
        'follow_location' => true,
        'max_redirects' => 3
    ],
    'ssl' => [
        'verify_peer' => false,
        'verify_peer_name' => false
    ]
]);

$content = @file_get_contents($url, false, $context);

if ($content === false) {
    echo json_encode(['error' => 'Failed to fetch URL content']);
    exit;
}

// Strip HTML tags and extract text
$text = strip_tags($content);

// Clean up whitespace, preserving line breaks as separators
$text = preg_replace('/[ \t\r]+/', ' ', $text);
$text = trim($text);

// Check minimum length
if (strlen($text) < 100) {
    echo json_encode(['error' => 'URL content too short (minimum 100 characters)']);
    exit;
}

echo json_encode([
    'success' => true,
    'text' => $text,
    'length' => strlen($text)
]);
