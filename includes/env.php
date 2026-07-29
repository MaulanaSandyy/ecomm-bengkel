<?php
// Load environment variables from .env file
$env_file = __DIR__ . '/../.env';

if (file_exists($env_file)) {
    $lines = file($env_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        // Skip comments
        if (strpos(trim($line), '#') === 0) {
            continue;
        }
        
        // Parse KEY=VALUE
        $parts = explode('=', $line, 2);
        if (count($parts) === 2) {
            $key = trim($parts[0]);
            $value = trim($parts[1]);
            
            // Remove quotes if present
            if (preg_match('/^["\'](.*)["\']\s*$/', $value, $matches)) {
                $value = $matches[1];
            }
            
            // Set as environment variable
            $_ENV[$key] = $value;
            putenv("$key=$value");
        }
    }
}

// Database Configuration
define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
define('DB_USER', getenv('DB_USER') ?: 'root');
define('DB_PASS', getenv('DB_PASS') ?: '');
define('DB_NAME', getenv('DB_NAME') ?: 'bengkel_mobil');

// Xendit Configuration
define('XENDIT_API_KEY', getenv('XENDIT_API_KEY') ?: '');
define('XENDIT_CALLBACK_TOKEN', getenv('XENDIT_CALLBACK_TOKEN') ?: '');

// Application Configuration
define('APP_ENV', getenv('APP_ENV') ?: 'development');
define('APP_DEBUG', getenv('APP_DEBUG') === 'true');

// Auto-detect BASE_URL berdasarkan struktur direktori
// Cocok untuk both lokal (XAMPP) dan hosting (InfinityFree)
$scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_FILENAME']));
$docRoot = str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT']);
$relativeToRoot = str_replace($docRoot, '', $scriptDir);

$subDirs = ['admin', 'owner', 'customer', 'pegawai', 'auth', 'callback'];
$projectPath = $relativeToRoot;
foreach ($subDirs as $dir) {
    $dirWithSlash = '/' . $dir;
    if (substr($relativeToRoot, -strlen($dirWithSlash)) === $dirWithSlash) {
        $projectPath = substr($relativeToRoot, 0, -strlen($dirWithSlash));
        break;
    }
}

$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
define('BASE_URL', $protocol . '://' . $_SERVER['HTTP_HOST'] . rtrim($projectPath, '/'));
?>