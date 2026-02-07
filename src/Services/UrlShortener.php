<?php

declare(strict_types=1);

namespace App\Services;

use App\Database\Connection;
use PDO;

/**
 * URL Shortener Service - Factory Pattern
 *
 * Handles URL shortening logic with unique code generation.
 * Uses prepared statements for SQL injection protection.
 *
 * @package App\Services
 */
class UrlShortener
{
    private PDO $db;
    private const CODE_LENGTH = 7;
    private const ALLOWED_CHARS = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';

    /**
     * Private constructor for Factory pattern
     */
    private function __construct()
    {
        $this->db = Connection::getInstance()->getConnection();
    }

    /**
     * Factory method to create instance
     *
     * @return self
     */
    public static function create(): self
    {
        return new self();
    }

    /**
     * Shorten a URL
     *
     * @param string $url The original URL to shorten
     * @return string|null The short code or null on failure
     */
    public function shorten(string $url): ?string
    {
        // Validate URL
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            return null;
        }

        // Sanitize URL
        $url = filter_var($url, FILTER_SANITIZE_URL);

        // Check if URL already exists
        $existing = $this->findByUrl($url);
        if ($existing) {
            return $existing['short_code'];
        }

        // Generate unique short code
        $shortCode = $this->generateUniqueCode();

        // Insert into database (SQL Injection protected)
        try {
            $stmt = $this->db->prepare(
                "INSERT INTO urls (original_url, short_code) VALUES (?, ?)"
            );
            $stmt->execute([$url, $shortCode]);

            return $shortCode;
        } catch (\PDOException $e) {
            error_log("URL Shortening Error: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Resolve a short code to original URL
     *
     * @param string $code Short code
     * @return array|null URL data or null if not found
     */
    public function resolve(string $code): ?array
    {
        // Sanitize input
        $code = preg_replace('/[^a-zA-Z0-9]/', '', $code);

        $stmt = $this->db->prepare(
            "SELECT * FROM urls WHERE short_code = ? AND is_active = 1"
        );
        $stmt->execute([$code]);

        $result = $stmt->fetch();
        return $result ?: null;
    }

    /**
     * Get all URLs with statistics
     *
     * @return array
     */
    public function getAllUrls(): array
    {
        $stmt = $this->db->query(
            "SELECT u.*, COUNT(c.id) as click_count 
             FROM urls u 
             LEFT JOIN clicks c ON u.id = c.url_id 
             WHERE u.is_active = 1
             GROUP BY u.id 
             ORDER BY u.created_at DESC
             LIMIT 100"
        );

        return $stmt->fetchAll();
    }

    /**
     * Delete a URL
     *
     * @param int $id URL ID
     * @return bool Success status
     */
    public function deleteUrl(int $id): bool
    {
        try {
            $stmt = $this->db->prepare(
                "UPDATE urls SET is_active = 0 WHERE id = ?"
            );
            return $stmt->execute([$id]);
        } catch (\PDOException $e) {
            error_log("URL Deletion Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Generate a unique short code
     *
     * @return string Unique short code
     */
    private function generateUniqueCode(): string
    {
        $maxAttempts = 10;
        $attempts = 0;

        do {
            $code = $this->randomString(self::CODE_LENGTH);
            $stmt = $this->db->prepare("SELECT id FROM urls WHERE short_code = ?");
            $stmt->execute([$code]);
            $attempts++;
        } while ($stmt->fetch() && $attempts < $maxAttempts);

        if ($attempts >= $maxAttempts) {
            // Increase code length if too many collisions
            return $this->randomString(self::CODE_LENGTH + 1);
        }

        return $code;
    }

    /**
     * Generate random string
     *
     * @param int $length String length
     * @return string Random string
     */
    private function randomString(int $length): string
    {
        $chars = self::ALLOWED_CHARS;
        $charsLength = strlen($chars);
        $randomString = '';

        for ($i = 0; $i < $length; $i++) {
            $randomString .= $chars[random_int(0, $charsLength - 1)];
        }

        return $randomString;
    }

    /**
     * Find URL by original URL
     *
     * @param string $url Original URL
     * @return array|null
     */
    private function findByUrl(string $url): ?array
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM urls WHERE original_url = ? AND is_active = 1 
             ORDER BY created_at DESC LIMIT 1"
        );
        $stmt->execute([$url]);

        $result = $stmt->fetch();
        return $result ?: null;
    }
}
