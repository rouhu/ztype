# ZType Game Backend - PHP Implementation

This is a complete PHP backend implementation for the ZType typing game. It handles game statistics, leaderboards, and screenshot sharing functionality.

## 📋 Features

- **Game Statistics Tracking**: Records score, wave, streak, and accuracy for each game
- **User Session Management**: Tracks players using secure session cookies
- **Leaderboard System**: Maintains top scores and player rankings
- **Screenshot Sharing**: Saves and serves game score screenshots for social media
- **Data Validation**: Comprehensive input validation and sanitization
- **Security**: SQL injection protection, XSS prevention, and rate limiting ready
- **CORS Support**: Cross-origin resource sharing for API access

## 🎮 How the Game Backend Works

### Game Flow Analysis

From the JavaScript code analysis, the game:

1. **Submits Stats**: When a game ends, sends POST request with:
   - `score`: Player's final score
   - `wave`: Wave number reached
   - `streak`: Longest typing streak
   - `accuracy`: Typing accuracy percentage

2. **Loads Stats**: Retrieves player's game history for display

3. **Saves Screenshots**: Generates and saves score graphs as PNG images

4. **Displays Trends**: Shows performance over multiple games

## 🚀 Installation

### Prerequisites

- PHP 7.4 or higher
- MySQL 5.7+ or MariaDB 10.2+
- Apache or Nginx web server
- mod_rewrite enabled (for Apache)

### Step 1: Database Setup

1. Create the database and tables:

```bash
mysql -u root -p < database.sql
```

Or manually execute the SQL commands in `database.sql`

2. Create a database user with appropriate permissions:

```sql
CREATE USER 'ztype_user'@'localhost' IDENTIFIED BY 'your_secure_password';
GRANT SELECT, INSERT, UPDATE, DELETE ON ztype_game.* TO 'ztype_user'@'localhost';
FLUSH PRIVILEGES;
```

### Step 2: Configure the Backend

1. Edit `index.php` and update the database credentials:

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'ztype_game');
define('DB_USER', 'ztype_user');
define('DB_PASS', 'your_secure_password');
```

2. Create the screenshots directory:

```bash
mkdir -p ../screenshots
chmod 755 ../screenshots
```

### Step 3: Web Server Configuration

#### Apache (.htaccess)

Create `.htaccess` in the `api` directory:

```apache
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php [QSA,L]

# Security headers
Header set X-Content-Type-Options "nosniff"
Header set X-Frame-Options "SAMEORIGIN"
Header set X-XSS-Protection "1; mode=block"

# Enable CORS (adjust origin as needed)
Header set Access-Control-Allow-Origin "*"
Header set Access-Control-Allow-Methods "POST, GET, OPTIONS"
Header set Access-Control-Allow-Headers "Content-Type"
```

#### Nginx

Add to your server block:

```nginx
location /api/ {
    try_files $uri $uri/ /api/index.php?$query_string;
    
    # CORS headers
    add_header Access-Control-Allow-Origin *;
    add_header Access-Control-Allow-Methods "POST, GET, OPTIONS";
    add_header Access-Control-Allow-Headers "Content-Type";
    
    # Security headers
    add_header X-Content-Type-Options "nosniff";
    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-XSS-Protection "1; mode=block";
}
```

### Step 4: Test the Installation

Test the API endpoint:

```bash
curl -X POST http://your-domain.com/api/ \
  -H "Content-Type: application/x-www-form-urlencoded" \
  -d "score=1000&wave=5&streak=50&accuracy=95.5"
```

Expected response:
```json
{
  "games": [...],
  "isExcerpt": false,
  "totalGames": 1
}
```

## 📡 API Endpoints

### Submit Game Stats

**Endpoint**: `POST /api/`

**Parameters**:
- `score` (int): Game score (0-999999)
- `wave` (int): Wave reached (0-999)
- `streak` (int): Longest streak (0-9999)
- `accuracy` (float): Accuracy percentage (0-100)

**Response**:
```json
{
  "games": [
    {
      "score": 15000,
      "wave": 25,
      "streak": 150,
      "accuracy": "95.50",
      "created": 1697654321
    }
  ],
  "isExcerpt": false,
  "totalGames": 1
}
```

### Load User Statistics

**Endpoint**: `POST /api/`

**Parameters**:
- `load=true`

**Response**: Same as submit stats

### Load All Statistics

**Endpoint**: `POST /api/`

**Parameters**:
- `loadAll=true`

**Response**:
```json
{
  "games": [...]
}
```

### Save Screenshot

**Endpoint**: `POST /api/`

**Parameters**:
- `saveScreenshot=true`
- `data` (string): Base64-encoded PNG image

**Response**:
```json
{
  "file": "screenshots/ztype-scores-2024-01-15-123456-abc123.png"
}
```

### Get Leaderboard

**Endpoint**: `POST /api/`

**Parameters**:
- `leaderboard=true`

**Response**:
```json
{
  "leaderboard": [
    {
      "session_id": "abc123...",
      "best_score": 25000,
      "best_wave": 40,
      "best_streak": 200,
      "best_accuracy": "98.50",
      "total_games": 15
    }
  ]
}
```

## 🔒 Security Considerations

### Implemented Security Features

1. **SQL Injection Protection**: Using PDO prepared statements
2. **Input Validation**: All inputs are validated and sanitized
3. **XSS Prevention**: JSON encoding prevents script injection
4. **CSRF Protection**: Session-based tracking
5. **Rate Limiting Ready**: Structure supports rate limiting implementation

### Recommended Additional Security

1. **Rate Limiting**: Implement rate limiting to prevent abuse:

```php
// Add to index.php
function checkRateLimit($sessionId) {
    $pdo = getDB();
    $stmt = $pdo->prepare("
        SELECT COUNT(*) as count 
        FROM game_stats 
        WHERE session_id = :session_id 
        AND created > DATE_SUB(NOW(), INTERVAL 1 MINUTE)
    ");
    $stmt->execute([':session_id' => $sessionId]);
    $count = $stmt->fetch()['count'];
    
    if ($count > 10) { // Max 10 submissions per minute
        http_response_code(429);
        echo json_encode(['error' => 'Rate limit exceeded']);
        exit();
    }
}
```

2. **HTTPS Only**: Force HTTPS in production:

```php
if (!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] !== 'on') {
    header('Location: https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
    exit();
}
```

3. **Content Security Policy**: Add CSP headers

4. **Database Backups**: Regular automated backups

## 🛠️ Maintenance

### Database Cleanup

Old data cleanup (run periodically):

```sql
-- Delete stats older than 1 year
DELETE FROM game_stats WHERE created < DATE_SUB(NOW(), INTERVAL 1 YEAR);

-- Optimize tables
OPTIMIZE TABLE game_stats;
```

### Screenshot Cleanup

The system automatically keeps only the last 100 screenshots. Manual cleanup:

```bash
find ../screenshots -name "*.png" -mtime +30 -delete
```

### Monitoring

Monitor these metrics:
- Database size growth
- API response times
- Error logs
- Screenshot directory size

## 📊 Database Schema

### game_stats Table

| Column | Type | Description |
|--------|------|-------------|
| id | INT UNSIGNED | Primary key |
| session_id | VARCHAR(64) | User session identifier |
| ip_address | VARCHAR(45) | Client IP address |
| score | INT UNSIGNED | Game score |
| wave | INT UNSIGNED | Wave reached |
| streak | INT UNSIGNED | Longest streak |
| accuracy | DECIMAL(5,2) | Typing accuracy |
| created | TIMESTAMP | Record creation time |

## 🐛 Troubleshooting

### Common Issues

1. **Database Connection Failed**
   - Check credentials in `index.php`
   - Verify MySQL service is running
   - Check user permissions

2. **CORS Errors**
   - Verify CORS headers in `.htaccess` or Nginx config
   - Check browser console for specific errors

3. **Screenshot Upload Fails**
   - Check directory permissions: `chmod 755 screenshots`
   - Verify PHP upload limits in `php.ini`

4. **Session Not Persisting**
   - Check cookie settings
   - Verify domain/path configuration

## 📝 License

This backend implementation is provided as-is for the ZType game.

## 🤝 Contributing

To improve this backend:

1. Add rate limiting
2. Implement user authentication
3. Add caching layer (Redis/Memcached)
4. Create admin dashboard
5. Add analytics and reporting

## 📞 Support

For issues or questions:
- Check error logs: `/var/log/apache2/error.log` or `/var/log/nginx/error.log`
- Enable PHP error reporting for debugging
- Review database query logs
