<?php

declare(strict_types=1);

/**
 * URL Shortener - Entry Point
 *
 * Modern PHP URL shortener with Analytics
 * Senior-level portfolio project
 */

require_once __DIR__ . '/../vendor/autoload.php';

use App\Services\UrlShortener;
use App\Services\Analytics;

// Error handling
error_reporting(E_ALL);
ini_set('display_errors', '0');
ini_set('log_errors', '1');

// Start session
session_start();

// Get request info
$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$requestMethod = $_SERVER['REQUEST_METHOD'];
$baseDir = '/url-shortener/public'; // XAMPP için

// Remove base directory from URI
$requestUri = str_replace($baseDir, '', $requestUri);
$requestUri = $requestUri ?: '/';

// Simple Router
switch ($requestUri) {
    case '/':
    case '/dashboard':
        // Show dashboard
        require __DIR__ . '/../views/dashboard.php';
        break;

    case '/api/shorten':
        // API: Shorten URL
        if ($requestMethod === 'POST') {
            header('Content-Type: application/json');

            $data = json_decode(file_get_contents('php://input'), true);
            $url = $data['url'] ?? '';

            if (empty($url)) {
                http_response_code(400);
                echo json_encode(['error' => 'URL is required']);
                exit;
            }

            $shortener = UrlShortener::create();
            $shortCode = $shortener->shorten($url);

            if ($shortCode) {
                $baseUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http")
                    . "://" . $_SERVER['HTTP_HOST'] . $baseDir;

                echo json_encode([
                    'success' => true,
                    'short_code' => $shortCode,
                    'short_url' => $baseUrl . '/' . $shortCode,
                    'original_url' => $url,
                ]);
            } else {
                http_response_code(400);
                echo json_encode(['error' => 'Failed to shorten URL. Please check if it\'s valid.']);
            }
        }
        break;

    case '/api/urls':
        // API: Get all URLs
        if ($requestMethod === 'GET') {
            header('Content-Type: application/json');

            $shortener = UrlShortener::create();
            $urls = $shortener->getAllUrls();

            echo json_encode(['urls' => $urls]);
        }
        break;

    case '/api/stats':
        // API: Get overall stats
        if ($requestMethod === 'GET') {
            header('Content-Type: application/json');

            $analytics = new Analytics();
            $stats = $analytics->getOverallStats();

            echo json_encode($stats);
        }
        break;

    default:
        // Try to resolve as short code
        $code = ltrim($requestUri, '/');

        if (preg_match('/^[a-zA-Z0-9]{6,10}$/', $code)) {
            $shortener = UrlShortener::create();
            $urlData = $shortener->resolve($code);

            if ($urlData) {
                // Track the click
                $analytics = new Analytics();
                $analytics->trackClick(
                    (int) $urlData['id'],
                    [
                        'ip' => $_SERVER['REMOTE_ADDR'] ?? null,
                        'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? null,
                        'referer' => $_SERVER['HTTP_REFERER'] ?? null,
                    ]
                );

                // Redirect to original URL
                header('Location: ' . $urlData['original_url'], true, 301);
                exit;
            }
        }

        // 404 Not Found
        http_response_code(404);
        require __DIR__ . '/../views/errors/404.php';
        break;
}
