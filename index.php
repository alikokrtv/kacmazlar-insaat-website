<?php
/**
 * Railway Production Entry Point
 */

// Error reporting for production
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

// Check if it's a static file request
$requestUri = $_SERVER['REQUEST_URI'];
$pathInfo = pathinfo($requestUri);

// Serve static files directly
if (isset($pathInfo['extension'])) {
    $filePath = __DIR__ . $requestUri;
    if (file_exists($filePath) && is_file($filePath)) {
        $mimeType = mime_content_type($filePath);
        header('Content-Type: ' . $mimeType);
        readfile($filePath);
        exit;
    }
}

// Simple health check endpoint
if ($requestUri === '/health') {
    http_response_code(200);
    echo json_encode(['status' => 'ok', 'timestamp' => date('Y-m-d H:i:s')]);
    exit;
}

// Route requests
switch ($requestUri) {
    case '/':
    case '/index.html':
        include 'index.html';
        break;
        
    case '/admin':
    case '/admin.php':
        include 'admin.php';
        break;
        
    case '/login':
    case '/login.php':
        include 'login.php';
        break;
        
    case '/logout':
    case '/logout.php':
        include 'logout.php';
        break;
        
    case '/katalog':
    case '/katalog.html':
        include 'katalog.html';
        break;
        
    default:
        // Check if it's a project page
        if (preg_match('/^\/proje-(\d+)\.html$/', $requestUri, $matches)) {
            $projectFile = 'proje-' . $matches[1] . '.html';
            if (file_exists($projectFile)) {
                include $projectFile;
                break;
            }
        }
        
        // Check if it's an API endpoint
        if (strpos($requestUri, '/api/') === 0) {
            include 'api.php';
            break;
        }
        
        // 404 Not Found
        http_response_code(404);
        echo '<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Sayfa Bulunamadı</title>
    <style>
        body { font-family: Arial, sans-serif; text-align: center; padding: 50px; }
        h1 { color: #004E89; }
        a { color: #FF6B35; text-decoration: none; }
    </style>
</head>
<body>
    <h1>404 - Sayfa Bulunamadı</h1>
    <p>Aradığınız sayfa mevcut değil.</p>
    <a href="/">Ana Sayfaya Dön</a>
</body>
</html>';
        break;
}
?>
