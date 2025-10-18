-- ZType Game Database Schema
-- This file contains the database structure for the ZType game backend

-- Create database
--CREATE DATABASE IF NOT EXISTS ztype_game CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

--USE ztype_game;

-- Game statistics table
CREATE TABLE IF NOT EXISTS game_stats (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    session_id VARCHAR(64) NOT NULL,
    ip_address VARCHAR(45) NOT NULL,
    score INT UNSIGNED NOT NULL DEFAULT 0,
    wave INT UNSIGNED NOT NULL DEFAULT 0,
    streak INT UNSIGNED NOT NULL DEFAULT 0,
    accuracy DECIMAL(5,2) NOT NULL DEFAULT 0.00,
    created TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_session (session_id),
    INDEX idx_score (score DESC),
    INDEX idx_created (created DESC),
    INDEX idx_session_created (session_id, created DESC)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Leaderboard view for quick access to top scores
CREATE OR REPLACE VIEW leaderboard_view AS
SELECT 
    session_id,
    MAX(score) as best_score,
    MAX(wave) as best_wave,
    MAX(streak) as best_streak,
    MAX(accuracy) as best_accuracy,
    COUNT(*) as total_games,
    MAX(created) as last_played
FROM game_stats
GROUP BY session_id
ORDER BY best_score DESC
LIMIT 100;

-- Optional: User profiles table for registered users
CREATE TABLE IF NOT EXISTS user_profiles (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    session_id VARCHAR(64) UNIQUE NOT NULL,
    username VARCHAR(50) UNIQUE,
    email VARCHAR(255),
    created TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    last_active TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_username (username),
    INDEX idx_session (session_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Screenshot metadata table (optional - for tracking shared screenshots)
CREATE TABLE IF NOT EXISTS screenshots (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    session_id VARCHAR(64) NOT NULL,
    filename VARCHAR(255) NOT NULL,
    score INT UNSIGNED NOT NULL,
    wave INT UNSIGNED NOT NULL,
    created TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_session (session_id),
    INDEX idx_created (created DESC)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Saved word sets table
CREATE TABLE IF NOT EXISTS saved_wordsets (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    session_id VARCHAR(64) NOT NULL,
    name VARCHAR(100) NOT NULL,
    text_content TEXT NOT NULL,
    source_url VARCHAR(500),
    flags TINYINT UNSIGNED NOT NULL DEFAULT 0,
    play_count INT UNSIGNED NOT NULL DEFAULT 0,
    created TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    last_played TIMESTAMP NULL,
    
    INDEX idx_session (session_id),
    INDEX idx_created (created DESC),
    INDEX idx_session_name (session_id, name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Sample data for testing (optional)
-- INSERT INTO game_stats (session_id, ip_address, score, wave, streak, accuracy) VALUES
-- ('test_session_1', '127.0.0.1', 15000, 25, 150, 95.5),
-- ('test_session_1', '127.0.0.1', 12000, 20, 120, 92.3),
-- ('test_session_2', '127.0.0.1', 18000, 30, 180, 97.8);
