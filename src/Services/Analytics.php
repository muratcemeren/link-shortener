<?php

declare(strict_types=1);

namespace App\Services;

use App\Database\Connection;
use PDO;

/**
 * Analytics Service
 *
 * Handles click tracking and analytics for shortened URLs.
 * Provides browser/platform detection and statistics.
 *
 * @package App\Services
 */
class Analytics
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Connection::getInstance()->getConnection();
    }

    /**
     * Track a click event
     *
     * @param int $urlId URL ID
     * @param array $data Click data
     * @return bool Success status
     */
    public function trackClick(int $urlId, array $data = []): bool
    {
        try {
            $browserInfo = $this->parseUserAgent($data['user_agent'] ?? '');

            $stmt = $this->db->prepare(
                "INSERT INTO clicks 
                (url_id, ip_address, user_agent, browser, platform, device, referer) 
                VALUES (?, ?, ?, ?, ?, ?, ?)"
            );

            return $stmt->execute([
                $urlId,
                $data['ip'] ?? null,
                $data['user_agent'] ?? null,
                $browserInfo['browser'] ?? 'Unknown',
                $browserInfo['platform'] ?? 'Unknown',
                $browserInfo['device'] ?? 'Desktop',
                $data['referer'] ?? null,
            ]);
        } catch (\PDOException $e) {
            error_log("Click Tracking Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get statistics for a specific URL
     *
     * @param int $urlId URL ID
     * @return array Statistics
     */
    public function getUrlStats(int $urlId): array
    {
        // Total clicks
        $stmt = $this->db->prepare(
            "SELECT COUNT(*) as total FROM clicks WHERE url_id = ?"
        );
        $stmt->execute([$urlId]);
        $total = $stmt->fetch()['total'] ?? 0;

        // Browser breakdown
        $stmt = $this->db->prepare(
            "SELECT browser, COUNT(*) as count 
             FROM clicks 
             WHERE url_id = ? 
             GROUP BY browser 
             ORDER BY count DESC"
        );
        $stmt->execute([$urlId]);
        $browsers = $stmt->fetchAll();

        // Platform breakdown
        $stmt = $this->db->prepare(
            "SELECT platform, COUNT(*) as count 
             FROM clicks 
             WHERE url_id = ? 
             GROUP BY platform 
             ORDER BY count DESC"
        );
        $stmt->execute([$urlId]);
        $platforms = $stmt->fetchAll();

        // Device breakdown
        $stmt = $this->db->prepare(
            "SELECT device, COUNT(*) as count 
             FROM clicks 
             WHERE url_id = ? 
             GROUP BY device 
             ORDER BY count DESC"
        );
        $stmt->execute([$urlId]);
        $devices = $stmt->fetchAll();

        // Recent clicks (last 10)
        $stmt = $this->db->prepare(
            "SELECT browser, platform, device, clicked_at 
             FROM clicks 
             WHERE url_id = ? 
             ORDER BY clicked_at DESC 
             LIMIT 10"
        );
        $stmt->execute([$urlId]);
        $recentClicks = $stmt->fetchAll();

        return [
            'total_clicks' => $total,
            'browsers' => $browsers,
            'platforms' => $platforms,
            'devices' => $devices,
            'recent_clicks' => $recentClicks,
        ];
    }

    /**
     * Get overall statistics
     *
     * @return array
     */
    public function getOverallStats(): array
    {
        // Total URLs
        $stmt = $this->db->query(
            "SELECT COUNT(*) as total FROM urls WHERE is_active = 1"
        );
        $totalUrls = $stmt->fetch()['total'] ?? 0;

        // Total clicks
        $stmt = $this->db->query(
            "SELECT COUNT(*) as total FROM clicks"
        );
        $totalClicks = $stmt->fetch()['total'] ?? 0;

        // Clicks today
        $stmt = $this->db->query(
            "SELECT COUNT(*) as total 
             FROM clicks 
             WHERE DATE(clicked_at) = CURDATE()"
        );
        $clicksToday = $stmt->fetch()['total'] ?? 0;

        return [
            'total_urls' => $totalUrls,
            'total_clicks' => $totalClicks,
            'clicks_today' => $clicksToday,
        ];
    }

    /**
     * Parse user agent string
     *
     * @param string $userAgent User agent string
     * @return array Browser info
     */
    private function parseUserAgent(string $userAgent): array
    {
        $browser = 'Unknown';
        $platform = 'Unknown';
        $device = 'Desktop';

        // Detect browser
        if (str_contains($userAgent, 'Chrome') && !str_contains($userAgent, 'Edg')) {
            $browser = 'Chrome';
        } elseif (str_contains($userAgent, 'Firefox')) {
            $browser = 'Firefox';
        } elseif (str_contains($userAgent, 'Safari') && !str_contains($userAgent, 'Chrome')) {
            $browser = 'Safari';
        } elseif (str_contains($userAgent, 'Edg')) {
            $browser = 'Edge';
        } elseif (str_contains($userAgent, 'Opera') || str_contains($userAgent, 'OPR')) {
            $browser = 'Opera';
        }

        // Detect platform
        if (str_contains($userAgent, 'Windows')) {
            $platform = 'Windows';
        } elseif (str_contains($userAgent, 'Mac')) {
            $platform = 'MacOS';
        } elseif (str_contains($userAgent, 'Linux')) {
            $platform = 'Linux';
        } elseif (str_contains($userAgent, 'Android')) {
            $platform = 'Android';
            $device = 'Mobile';
        } elseif (str_contains($userAgent, 'iPhone') || str_contains($userAgent, 'iPad')) {
            $platform = 'iOS';
            $device = str_contains($userAgent, 'iPad') ? 'Tablet' : 'Mobile';
        }

        // Detect device type (additional check)
        if (str_contains($userAgent, 'Mobile') && $device === 'Desktop') {
            $device = 'Mobile';
        }

        return [
            'browser' => $browser,
            'platform' => $platform,
            'device' => $device,
        ];
    }
}
